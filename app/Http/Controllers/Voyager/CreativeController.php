<?php

namespace App\Http\Controllers\Voyager;

use App\Creative;
use App\Campaign;
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
use App\CreativesAttribute;
use App\CreativeDisplay;
use App\CreativeVideo;
use App\CreativeAudio;
use App\TrustScan;
use Auth;
use App\User;
use App\Advertiser;
use App\Concept;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Helpers\CloneHelper;
use App\Http\Helpers\CsvHelper;
use App\Http\Helpers\TrustHelper;
use App\Http\Helpers\MailerHelper;
use Illuminate\Support\Carbon;

class CreativeController extends VoyagerBaseController
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

            $advertiser_selected = 0;

            if($request->get('advertiser') != 0){
                $query->where('advertiser_id' , $request->get('advertiser'));
                $advertiser_selected = $request->get('advertiser');
            }

            if ($search->value) {
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where(function($query_search) use ($search_value) {
                    $query_search->where('name', 'LIKE', '%'.$search_value.'%')
                        ->orWhere('id', 'LIKE', '%'.$search_value.'%');
                });

                $query->orWhereHas('Concept', function($q)  use ($search_value) {
                    $q->where('name', 'LIKE', '%'.$search_value.'%');
                });
            }

            if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
                $logged_user = User::where('id', '=', Auth::user()->id)->get();
                //Get Advertisers From Organization
                $advertisers = Advertiser::where('organization_id', '=', $logged_user[0]->organization_id)->get();

                //return $advertisers[1]->id;
                $advids = array();
                foreach ($advertisers as $adv){
                    $advids[]=$adv->id;
                }
                $query->whereIn('advertiser_id',$advids);
            }

            //ADD Where Concept Id
            if(isset($_GET["concept_id"])){
                $query->where('concept_id',$_GET["concept_id"]);
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
        $dataTypeContent->load('Concept');
        $dataTypeContent->load('TrustScan');

        $user = Auth::user();
        if($user->role_id != 1){
            $advertisers = Advertiser::where('organization_id',$user->organization_id)->get();
        }else{
            $advertisers = Advertiser::all();
        }

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'sortOrder',
            'searchable',
            'isServerSide',
            'advertisers',
            'advertiser_selected'
        ));
    }

    //***************************************
    //  INDEX ONLY WITH INCIDENTS
    //****************************************

    public function incidents(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = 'creatives';

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


            $advertiser_selected = 0;

            if($request->get('advertiser') != 0){
                $query->where('advertiser_id' , $request->get('advertiser'));
                $advertiser_selected = $request->get('advertiser');
            }
            $query->whereHas('TrustScan', function($q){
                $q->where('status', 'INCIDENT');
            });
            if ($search->value) {
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where(function($query_search) use ($search_value) {
                    $query_search->where('name', 'LIKE', '%'.$search_value.'%')
                        ->orWhere('id', 'LIKE', '%'.$search_value.'%');
                });

                $query->orWhereHas('Concept', function($q)  use ($search_value) {
                    $q->where('name', 'LIKE', '%'.$search_value.'%');
                });
            }

            if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
                $logged_user = User::where('id', '=', Auth::user()->id)->get();
                //Get Advertisers From Organization
                $advertisers = Advertiser::where('organization_id', '=', $logged_user[0]->organization_id)->get();

                //return $advertisers[1]->id;
                $advids = array();
                foreach ($advertisers as $adv){
                    $advids[]=$adv->id;
                }
                $query->whereIn('advertiser_id',$advids);
            }

            //ADD Where Concept Id
            if(isset($_GET["concept_id"])){
                $query->where('concept_id',$_GET["concept_id"]);
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


        $view = "voyager::$slug.browse_incidents";

        $dataTypeContent->load('Concept');
        $dataTypeContent->load('TrustScan');

        $user = Auth::user();
        if($user->role_id != 1){
            $advertisers = Advertiser::where('organization_id',$user->organization_id)->get();
        }else{
            $advertisers = Advertiser::all();
        }


        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'sortOrder',
            'searchable',
            'isServerSide',
            'advertisers',
            'advertiser_selected'
        ));
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |__) |
    //               |  _  /
    //               | | \ \
    //               |_|  \_\
    //
    //  Read an item of our Data Type B(R)EAD
    //
    //****************************************

    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $relationships = $this->getRelationships($dataType);
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $dataTypeContent = call_user_func([$model->with($relationships), 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
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

        $creative = Creative::find($id);

        $concept= array(
            'id' => null,
            'name' => null,
        );

        if($creative->Concept != null){
            $concept= array(
                'id' => $creative->Concept->id,
                'name' => $creative->Concept->name,
            );
        }


        $user = Auth::user();

        if($user->role_id == 1 ) {
            $advertisers =  Advertiser::select('id', 'name')->get();
        } else {
            //Get Advertisers From Organization
            $advertisers = Advertiser::select('id','name')->where('organization_id', '=', $user->organization_id)->get();
        }

        $thisCreativeAttributes = $creative->CreativeAttributes()->get();
        //dd($thisCreativeAttributes);
        $creativeDisplay = CreativeDisplay::where("creative_id",$id)->first();
        $creativeVideo = CreativeVideo::where("creative_id",$id)->first();
        $dataTypeContent->load('Concept');

        $dataTypeContent->load('TrustScan');

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','thisCreativeAttributes','creativeDisplay','creativeVideo','concept','advertisers'));
    }
    /**
     * export to csv
     */
    public function export($id){

        $exportData[] = array("id","creative_type_id","name","click_url","3pas_tag_id","landing_page","start_date","end_date","concept","ad_height","ad_width","tag_code","3rd_tracking","creative_attributes","vast_code","skippable");

        $creative = Creative::getDataToExport($id);

        $exportData = array_merge($exportData, $creative);

        $filename ="Creative [".$id."]";

        return CsvHelper::getCsv($exportData , $filename);
    }





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

            $creative =$this->insertUpdateData($request, $slug, $dataType->editRows, $data);

            event(new BreadDataUpdated($dataType, $data));

            //Delete all Creative Attributes
            CreativesAttribute::where("creative_id", $id)->delete();

            //Insert Creative Attributes
            if($request->creative_type_id == 1){
                if($request->creative_attributes) {
                    foreach ($request->creative_attributes as $attribute) {
                        CreativesAttribute::create([
                            'creative_id' => $id,
                            'attribute_id' => $attribute
                        ]);
                    }
                }
            }

            //If is Display Insert/Update Display parameter
            if($request->creative_type_id == 1){
                //DELETE creativeDisplay
                CreativeDisplay::where("creative_id",$id)->delete();
                //INSERT updated creativeDisplay
                CreativeDisplay::create([
                    'creative_id' => $id,
                    'mime_type' => $request->mime_type,
                    'mraid_required' => isset($request->mraid_required) && $request->mraid_required==1 ? $request->mraid_required : 0,
                    'tag_type' => $request->tag_type,
                    'ad_format' => $request->ad_format,
                    'ad_width' => $request->ad_width,
                    'ad_height' => $request->ad_height,
                    'tag_code' => $request->tag_code,
                    '3rd_tracking' => $request->{'3rd_tracking'}

                ]);
            }
            //If Creative is Video/VAST
            if($request->creative_type_id == 2){
                //DELETE creativeVideo
                CreativeVideo::where("creative_id",$id)->delete();

                $vast_code = $request->vast_code;

                if ($request->vast_type === "form") {
                    $vast_code = $request->form_vast_code;
                }

                //INSERT updated craiveVideo
                CreativeVideo::create([
                    'creative_id' => $id,
                    'vast_code' => $vast_code,
                    'skippable' => $request->skippable,
                    'duration' => $request->duration,
                    'bitrate' => $request->bitrate,
                    'vast_type' => $request->vast_type
                ]);
            }

            if($request->creative_type_id == 3){


                if(isset($request->audio_file)) {
                    $audio_name = $id."-audio.mp3";

                    //UPLOAD AUDIO
                    //DELETE creativeAudio
                    CreativeAudio::where("creative_id",$id)->delete();

                    $request->audio_file->storeAs('public/audios', $audio_name);

                    CreativeAudio::create([
                        'creative_id' => $id,
                        'audio_file' => $audio_name
                    ]);
                }
            }

            if($request->input('date_unlimited') == 1){
                $creative->start_date=null;
                $creative->end_date=null;
            }else{
                $creative->end_date = $request->end_date . ' 23:59:59';
            }
            $creative->save();

            TrustScan::where('creative_id', $id)->delete();
            if($creative->status == 0){
                $saveScan = new TrustScan();
                $saveScan->provider = 'TMT';
                $saveScan->status = 'PAUSSING';
                $saveScan->last_scan = Carbon::now();
                $saveScan->creative_id = $id;
                $saveScan->save();

            }else{
                $saveScan = new TrustScan();
                $saveScan->provider = 'TMT';
                $saveScan->status = 'PENDING';
                $saveScan->last_scan = Carbon::now();
                $saveScan->creative_id = $id;
                $saveScan->save();

            }

            Creative::updateTimestamp($id);

            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
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

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $user = Auth::user();

        if($user->role_id == 1 ) {

            $advertisers =  Advertiser::select('id', 'name')->get();
        } else {
            //Get Advertisers From Organization
            $advertisers = Advertiser::select('id','name')->where('organization_id', '=', $user->organization_id)->get();
        }

        foreach ($advertisers as $adv){
            $advs_array[] = $adv->id;
        }

        //Concepts
        if($user->role_id == 1 ) {
            $concepts = Concept::select('id', 'name')->get();
        } else {
            $concepts = Concept::whereIn('advertiser_id',$advs_array)->get();
        }

        $creativeDisplay  = collect([]);
        $thisCreativeAttributes = collect([]);

        return Voyager::view($view, compact('dataType', 'advertisers', 'dataTypeContent', 'isModelTranslatable','creativeDisplay','thisCreativeAttributes','concepts'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->has('_validate')) {

            //dd($request->all());

            $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
            $creative = $data;
            $cid = $data->id;
            event(new BreadDataAdded($dataType, $data));

            if($request->input('date_unlimited') == 1){
                $creative->start_date=null;
                $creative->end_date=null;
            }else{
                $creative->end_date = $request->end_date . ' 23:59:59';
            }
            $creative->save();

            CreativesAttribute::where('creative_id', $cid )->delete();
            //Insert Creative Attributes
            if($request->creative_attributes){
                foreach ($request->creative_attributes as $attribute) {
                    CreativesAttribute::create([
                        'creative_id' => $cid,
                        'attribute_id' => $attribute
                    ]);
                }
            }

            //If is Display Insert/Update Display parameter
            if($request->creative_type_id == 1){
                CreativeDisplay::create([
                    'creative_id' => $cid,
                    'mime_type' => $request->mime_type,
                    'mraid_required' => isset($request->mraid_required) ? $request->mraid_required : 0,
                    'tag_type' => $request->tag_type,
                    'ad_format' => $request->ad_format,
                    'ad_width' => $request->ad_width,
                    'ad_height' => $request->ad_height,
                    'tag_code' => $request->tag_code,
                    '3rd_tracking' => $request->{'3rd_tracking'}

                ]);
            }
            if($request->creative_type_id == 2){

                $vast_code = $request->vast_code;

                if ($request->vast_type === "form") {
                    $vast_code = $request->form_vast_code;
                }

                CreativeVideo::create([
                    'creative_id' => $cid,
                    'vast_code' => $vast_code,
                    'skippable' => $request->skippable,
                    'duration' => $request->duration,
                    'bitrate' => $request->bitrate,
                    'vast_type' => $request->vast_type
                ]);
            }
            if($request->creative_type_id == 3){

                //UPLOAD AUDIO
                $audio_name = $cid."-audio.mp3";
                $request->audio_file->storeAs('public/audios', $audio_name);

                CreativeAudio::create([
                    'creative_id' => $cid,
                    'audio_file' => $audio_name

                ]);
            }


            TrustScan::where('creative_id', $cid)->delete();

            $saveScan = new TrustScan();
            $saveScan->provider = 'TMT';
            $saveScan->status = 'PENDING';
            $saveScan->last_scan = Carbon::now();
            $saveScan->creative_id = $cid;
            $saveScan->save();


            $updated_at = $creative->updated_at;

            $concept = Concept::find($creative->concept_id);

            $concept->updated_at = $updated_at;
            $concept->save();

            foreach($concept->Strategies as $strategy){
                $strategy->updated_at = $updated_at;
                $strategy->save();

                $campaign = Campaign::find(intval($strategy->campaign_id));
                if($campaign){
                    $campaign->updated_at = $updated_at;
                    $campaign->save();
                }
            }



            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $data]);
            }

            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message'    => __('voyager::generic.successfully_added_new')." {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $ad_ID = $id + ($_ENV['WL_PREFIX'] * 1000000);

        TrustHelper::deleteTag($ad_ID);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('delete', app($dataType->model_name));

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
            $this->cleanup($dataType, $data);
        }

        $displayName = count($ids) > 1 ? $dataType->display_name_plural : $dataType->display_name_singular;

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    /**
     * Remove translations, images and files related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $dataType
     * @param \Illuminate\Database\Eloquent\Model $data
     *
     * @return void
     */
    protected function cleanup($dataType, $data)
    {
        // Delete Translations, if present
        if (is_bread_translatable($data)) {
            $data->deleteAttributeTranslations($data->getTranslatableAttributes());
        }

        // Delete Images
        $this->deleteBreadImages($data, $dataType->deleteRows->where('type', 'image'));

        // Delete Files
        foreach ($dataType->deleteRows->where('type', 'file') as $row) {
            foreach (json_decode($data->{$row->field}) as $file) {
                $this->deleteFileIfExists($file->download_link);
            }
        }
    }

    /**
     * Delete all images related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Database\Eloquent\Model $rows
     *
     * @return void
     */
    public function deleteBreadImages($data, $rows)
    {
        foreach ($rows as $row) {
            if ($data->{$row->field} != config('voyager.user.default_avatar')) {
                $this->deleteFileIfExists($data->{$row->field});
            }

            $options = json_decode($row->details);

            if (isset($options->thumbnails)) {
                foreach ($options->thumbnails as $thumbnail) {
                    $ext = explode('.', $data->{$row->field});
                    $extension = '.'.$ext[count($ext) - 1];

                    $path = str_replace($extension, '', $data->{$row->field});

                    $thumb_name = $thumbnail->name;

                    $this->deleteFileIfExists($path.'-'.$thumb_name.$extension);
                }
            }
        }

        if ($rows->count() > 0) {
            event(new BreadImagesDeleted($data, $rows));
        }
    }

    public function manualScan($id){

        TrustHelper::SendForScaning($id);

        return redirect()
            ->route("voyager.creatives.index");
    }


    /**
     * Order BREAD items.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        if (!isset($dataType->order_column) || !isset($dataType->order_display_column)) {
            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message'    => __('voyager::bread.ordering_not_set'),
                    'alert-type' => 'error',
                ]);
        }

        $model = app($dataType->model_name);
        $results = $model->orderBy($dataType->order_column)->get();

        $display_column = $dataType->order_display_column;

        $view = 'voyager::bread.order';

        if (view()->exists("voyager::$slug.order")) {
            $view = "voyager::$slug.order";
        }

        return Voyager::view($view, compact(
            'dataType',
            'display_column',
            'results'
        ));
    }

    public function update_order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        $model = app($dataType->model_name);

        $order = json_decode($request->input('order'));
        $column = $dataType->order_column;
        foreach ($order as $key => $item) {
            $i = $model->findOrFail($item->id);
            $i->$column = ($key + 1);
            $i->save();
        }
    }
    /**
     * clones a creative and redirect to the list
     */
    public function clone($id)
    {
        try {
            DB::beginTransaction();
            $creative =  Creative::find(intval($id));

            $clone = CloneHelper::cloneCreative($creative);

            TrustScan::where('creative_id', $clone['data']->id)->delete();

            if($clone['status']){
                DB::commit();
                return redirect('/admin/creatives');
            }

            DB::rollBack();
            Log::info($clone['message']);
            return "there was an error trying to clone the creative, please try again";

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return "there was an error trying to clone the creative, please try again";
        }


    }

    /**
     * load the bulk view
     */
    public function bulk()
    {
        $user = Auth::user();
        if($user->role_id==1){
            $advertisers = Advertiser::get();
        }else{
            $advertisers = Advertiser::where('organization_id',$user->organization_id)->get();
        }
        return Voyager::view("voyager::creatives.bulk", compact('advertisers'));
    }


    public function testScan(){

        MailerHelper::creativeBlocked(3209);
        die();

    }

}