<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\User;
use App\Advertiser;
use App\Campaign;
use App\Pixel;
use Auth;

class ConversionPixelsController extends VoyagerBaseController
{
    use BreadRelationshipParser;
    
     // POST BR(E)AD
     public function update(Request $request, $id)
     {
  
         $slug = $this->getSlug($request);
 
         $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
 
         // Compatibility with Model binding.
         $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;
 
         $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
 
         // Check permission
         $this->authorize('edit', $data);
 
         // Validate fields with ajax
         $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id);
 
         if ($val->fails()) {
             return response()->json(['errors' => $val->messages()]);
         }

         if (!$request->ajax()) {
             $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
 
             event(new BreadDataUpdated($dataType, $data));
 
             return redirect()
                 ->route("voyager.{$dataType->slug}.index")
                 ->with([
                     'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
                     'alert-type' => 'success',
                 ]);
         }
     }

      //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';
        $orderBy = $request->get('order_by');
        $sortOrder = $request->get('sort_order', null);

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $relationships = $this->getRelationships($dataType);

            $model = app($dataType->model_name);
            $query = $model::select('*')->with($relationships);

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value) {
                $search_value = $search->value;
                $query->where(function($query_search) use ($search_value) {
                    $query_search->where('name', 'LIKE', '%'.$search_value.'%');
                });
                $query->orWhereHas('Organization', function($q)  use ($search_value) {
                    $q->where('name', 'LIKE', '%'.$search_value.'%');
                });
            }

            if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
                $logged_user = User::where('id', '=', Auth::user()->id)->get();
               // $query->where("organization_id","=",$logged_user[0]->organization_id);
            }


            if(Auth::user()->role_id != 1){
                $pixels = Pixel::where('organization_id', '=', Auth::user()->organization_id)->get()->pluck('id')->toArray();
                $query->whereIn("smart_pixel_id",$pixels);
            }
        

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'DESC';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable($model))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'sortOrder',
            'searchable',
            'isServerSide'
        ));
    }


     //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;

        foreach ($dataType->addRows as $key => $row) {
            $details = json_decode($row->details);
            $dataType->addRows[$key]['col_width'] = isset($details->width) ? $details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        if(Auth::user()->role_id == 1){
            $campaigns = Campaign::all();
            $pixels = Pixel::all();
        }else{
            $advertisers = Advertiser::where('organization_id',Auth::user()->organization_id)->get()->pluck('id')->toArray();
            $campaigns = Campaign::whereIn('advertiser_id',$advertisers)->get();
            $pixels = Pixel::where('organization_id',Auth::user()->organization_id)->get();
        }
    
        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType','organizations','campaigns', 'pixels','dataTypeContent', 'isModelTranslatable'));
    }
 

    
    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $relationships = $this->getRelationships($dataType);

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? app($dataType->model_name)->with($relationships)->findOrFail($id)
            : DB::table($dataType->name)->where('id', $id)->first(); // If Model doest exist, get data from table name

        foreach ($dataType->editRows as $key => $row) {
            $details = json_decode($row->details);
            $dataType->editRows[$key]['col_width'] = isset($details->width) ? $details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        if(Auth::user()->role_id == 1){
            $campaigns = Campaign::all();
            $pixels = Pixel::all();
        }else{
            $advertisers = Advertiser::where('organization_id',Auth::user()->organization_id)->get()->pluck('id')->toArray();
            $campaigns = Campaign::whereIn('advertiser_id',$advertisers)->get();
            $pixels = Pixel::where('organization_id',Auth::user()->organization_id)->get();
        }
    
        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','campaigns' , 'pixels'));
    }

}
