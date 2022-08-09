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
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;

use App\User;
use App\Campaign;
use App\Concept;
use App\IabCountry;
use App\IabRegion;
use App\Creative;
use App\Advertiser;

use App\Ssp;
use Auth;
use  App\Organization;
use Carbon\Carbon;

class OrganizationController extends VoyagerBaseController
{
    use BreadRelationshipParser;
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

            $details = $dataType->details;
            if ($details['order_column']) {
                $query->orderBy($details['order_column'], 'DESC');
            }

            if ($search->value) {
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where(function($query_search) use ($search_value) {
                    $query_search->where('name', 'LIKE', '%'.$search_value.'%')
                        ->orWhere('id', 'LIKE', '%'.$search_value.'%')
                        ->orWhere('contact_name', 'LIKE', '%'.$search_value.'%')
                        ->orWhere('email', 'LIKE', '%'.$search_value.'%');
                });
            }

            if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
                return redirect('admin');
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

        $users = User::where('organization_id',$id)->with('role')->get()->toArray();
      
        $ssps = Ssp::orderBy('name', 'asc')->get();

        //SELECTED SSPS AND DMPS
        //die($dataTypeContent["name"]);
        $selected_ssps = explode(",",$dataTypeContent["ssps"]);
        $selected_dmps = explode(",",$dataTypeContent["dmps"]);

        //FROM UNTIL

        $from = Carbon::now()->firstOfMonth()->format("ymdH");
        $until = Carbon::now()->format("ymdH");


        return Voyager::view($view, compact('dataType','users', 'dataTypeContent', 'isModelTranslatable','ssps','selected_dmps','selected_ssps','from','until'));
    }
    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        //print_r($request->ssps);
        //die();
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

            if(!$request->ssps){
                $request->ssps=[];
            }

            if(!$request->dmps){
                $request->dmps=[];
            }

            //INSERT SSPS and DMPS
            $ossps ="";
            $odmps="";
            foreach ($request->ssps as $ssp) {
                $ossps.=$ssp.",";
            }
            foreach ($request->dmps as $dmp) {
                $odmps.=$dmp.",";
            }


            Organization::where('id',$id)->update(['ssps'=>rtrim($ossps,","), 'dmps'=>rtrim($odmps,",")]);


            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }

    public function reports($id){

        $from = Carbon::now()->subDays(6)->format("ymdH");
        $until = Carbon::now()->format("ymdH");
        //Organization
    
        $user_id = Auth::user()->id;
        $user_role = Auth::user()->role_id;

        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3 || Auth::user()->role_id == 5){

            //Campaigns
            $logged_user = User::where('id', '=', Auth::user()->id)->get();
            //Get Advertisers From Organization
            $advertisers = Advertiser::where('organization_id', '=', $id)->get();

            //return $advertisers;

            //return $advertisers[1]->id;
            $advids = array();
            foreach ($advertisers as $adv){
                $advids[]=$adv->id;
            }
            //die($user_role);
            if($user_role == 5){
                $user_advertiser = UsersAdvertiser::where('user_id','=',$user_id)->get();
                $advids = array();
                $advids = array($user_advertiser[0]->advertiser_id);
            }
            $campaigns = Campaign::whereIn('advertiser_id',$advids)->get();
            $concepts = Concept::whereIn('advertiser_id',$advids)->get();
            $creatives = Creative::whereIn('advertiser_id',$advids)->get();


        } else {

            //Campaigns
            $campaigns = Campaign::all();

            //Concepts
            $concepts = Concept::all();

            //Creatives
            $creatives = Creative::all();
        }
       

        $advids = Advertiser::where('organization_id', $id )->get()->pluck('id')->toArray();

        $campaigns_list = Campaign::whereIn('advertiser_id', $advids )->get()->pluck('id')->toArray();

   
        foreach ($campaigns_list as $key => &$campaign_aux){
            $campaigns_list[$key] = intval(intval($campaign_aux) + ( intval(env('WL_PREFIX')) * 1000000));
        }
        $campaigns_list = str_replace(' ', '', implode(', ', $campaigns_list));

        $campaigns_list =  $campaigns_list =='' ? 'nocapaings' :  $campaigns_list;
        //Domains

        //Countries
        $countries = IabCountry::all();

        //Regions
        $regions = IabRegion::all();

        $organization_id = $id;

        return Voyager::view('voyager::organizations.reports', compact('organization_id','campaigns_list','reports','from','until','campaigns','concepts','creatives','countries','regions','userorganization','user_id','user_role','id'));
      
    }

}
