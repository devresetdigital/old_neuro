<?php

namespace App\Http\Controllers\Voyager;

use App\Campaign;
use App\CampaignsBudgetFlight;
use App\User;
use App\Advertiser;
use App\Strategy;
use App\Vwi;
use App\VwiLocation;
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
use Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Helpers\CloneHelper;
use App\Http\Helpers\CsvHelper;


class CampaignController extends VoyagerBaseController
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

            $advertiser_selected = 0;

            if($request->get('advertiser') != 0){
                $query->where('advertiser_id' , $request->get('advertiser'));
                $advertiser_selected = $request->get('advertiser');
            }
            

            if ($search->value) {
                $search_value =$search->value;
                $query->where(function($query_search) use ($search_value) {
                    $query_search->where('name', 'LIKE', '%'.$search_value.'%')
                        ->orWhere('id', 'LIKE', '%'.$search_value.'%');
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
            //return $logged_user;

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
        

        foreach ($dataTypeContent as $key => $value) {
           $campaign = Campaign::find($value->id);
           $value->firstFlight= $campaign->getFirstFlight();
           $value->lastFlight= $campaign->getLastFlight();
        }

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
            'advertisers',
            'advertiser_selected',
            'isServerSide'
        ));
    }

    
    /**
     * clones a campaign and redirect to the list
     */
    public function clone($id){
        try {
            DB::beginTransaction();
            $campaign =  Campaign::find(intval($id));

            $clone = CloneHelper::cloneCampaign($campaign);
            if($clone['status']){
                DB::commit();
                return redirect('/admin/campaigns');
            }

            DB::rollBack();
            Log::info($clone['message']);
            return "there was an error trying to clone the campaign, please try again";
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return "there was an error trying to clone the campaign, please try again";
        }
       

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

        //GET Flights
        $campaignFlights = CampaignsBudgetFlight::where('campaign_id',$id)->get();

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        //Campaign VWIs
        $selected_vwis=array();
        $vwis_list = Vwi::all();

        //Selected VWI Location
        $selected_vwi_locations=explode(",",$dataTypeContent->vwis_location);
        $vwi_locations = VwiLocation::all();

        //USER
        $logged_user = User::where('id', '=', Auth::user()->id)->get();

        //GET ORG Advertisers
        $organization_advertisers = Advertiser::where('organization_id','=',$logged_user[0]->organization_id)->get();

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','campaignFlights','selected_vwis','vwis_list','selected_vwi_locations','vwi_locations','logged_user','organization_advertisers'));
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
            //dd($request);
           /* $nflights = count($request->get("flight_sdate"));
            for($i=0;$i<$nflights;$i++){
                echo $request->get("flight_sdate")[$i]."<br>";
                echo $request->get("flight_edate")[$i]."<br>";
                echo $request->get("flight_monetary")[$i]."<br>";
                echo $request->get("flight_impression")[$i]."<br><br>";
            }*/

            //
            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
            //Update VWI Locations
            if(is_array($request->vwi_locations)==false){
                $request->vwi_locations = array();
            }
            if(count($request->vwi_locations)>0) {
                $locations="";
                foreach ($request->vwi_locations as $location){
                    $locations.=$location.",";
                }
                $campaign = Campaign::find($id);
                $campaign->vwis_location = rtrim($locations,",");
                $campaign->save();
            }
            //DELETE OLD FLIGHTS
            CampaignsBudgetFlight::where('campaign_id',$id)->delete();
            //ADD NEW FLIGHTS
            $nflights = count($request->get("flight_sdate"));
            if($request->get("flight_sdate")[0]!=""){
                for($i=0;$i<$nflights;$i++) {
                    if($request->get("flight_sdate")[$i]!="" && $request->get("flight_edate")[$i]!="" ) {
              
                        $end_date = substr($request->get("flight_edate")[$i],0,10).' 23:59:59';
                        CampaignsBudgetFlight::create([
                            "campaign_id" => $id,
                            "date_start" => $request->get("flight_sdate")[$i],
                            "date_end" => $end_date,
                            "budget" => $request->get("flight_monetary")[$i],
                            "impression" => $request->get("flight_impression")[$i]
                        ]);
                    }
            }

               // echo $request->get("flight_sdate")[$i]."<br>";
               // echo $request->get("flight_edate")[$i]."<br>";
              //  echo $request->get("flight_monetary")[$i]."<br>";
              //  echo $request->get("flight_impression")[$i]."<br><br>";
            }

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

        //Campaign VWIs
        $selected_vwis=array();
        $vwis_list = Vwi::all();

        //Selected VWI Location
        $selected_vwi_locations=array();
        $vwi_locations = VwiLocation::all();

        //USER
        $logged_user = User::where('id', '=', Auth::user()->id)->get();

        //GET ORG Advertisers
        $organization_advertisers = Advertiser::where('organization_id','=',$logged_user[0]->organization_id)->get();

       // print_r($logged_user);
       // die();

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','vwis_list','selected_vwis','selected_vwi_locations','vwi_locations','logged_user','organization_advertisers'));
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
            $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
            $cid = $data->id;

            //ADD NEW FLIGHTS
            $nflights = count($request->get("flight_sdate"));
            if($request->get("flight_sdate")[0]!="") {
                for ($i = 0; $i < $nflights; $i++) {
                    
                    $end_date = substr($request->get("flight_edate")[$i],0,10).'23:59:59';
                    CampaignsBudgetFlight::create([
                        "campaign_id" => $cid,
                        "date_start" => $request->get("flight_sdate")[$i],
                        "date_end" => $end_date,
                        "budget" => $request->get("flight_monetary")[$i],
                        "impression" => $request->get("flight_impression")[$i]
                    ]);
                }
            }
            event(new BreadDataAdded($dataType, $data));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $data]);
            }

            return redirect()
                ->route("voyager.strategies.create",["campaign_id" => $cid])
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
     * export to csv
     */
    public function export($id){

        $exportData[] = array('id','campaign','name','date_start','date_end','budget','goal_type','goal_amount','goal_bid_for','goal_min_bid','goal_max_bid','m_type','m_amount','m_stype','i_type','i_amount','i_stype','f_type','f_amount','f_stype','selected_concepts','country_inc_exc','country','region_inc_exc','region','city_inc_exc','city','lang_inc_exc','language','geofencing_inc_exc','geofencingjson','sitelists_inc_exc','sitelists','iplists_inc_exc','iplists','pmps','open_market','ssps_inc_exc','ssps','ziplists_inc_exc','ziplists','keywordslist_inc_exc','keywordslists','devices_inc_exc','device','inventories_inc_exc','inventory_type','isp_inc_exc','isps','os_inc_exc','os','browser_inc_exc','browser','pixels_inc_exc','pixels','custom_datas_inc_exc','custom_datas','segments_inc_exc','segments');

        $strategies = Strategy::where('campaign_id', $id)->get();

        foreach ( $strategies as $strategy) {
            $strategy_data = Strategy::getDataToExport($strategy->id);
            $exportData = array_merge($exportData, $strategy_data);
        }

        $filename ="Campaign [".$id."]";
 
        return CsvHelper::getCsv($exportData , $filename);

    }



    public function reachFrequency(){

        if ($_GET["campaign_id"]) {

            $url = "http://51.161.86.82:9080/getcampaigns?campaignid=".$_GET["campaign_id"];
            $request = file_get_contents($url);
            $data  = json_decode($request, true);

            $url_ip = "http://51.161.86.82:9080/getcampaigns?type=ip&campaignid=".$_GET["campaign_id"];
            $request_ip = file_get_contents($url_ip);
            $data_ip  = json_decode($request_ip, true);

            $results =[
                'totals'=> [],
                'totals_ip'=> [],
                'all'=> []
            ];

            $campaign_id = intval($_GET["campaign_id"]);

            if($campaign_id > 1000000){
                $campaign_id = $campaign_id - ($_ENV['WL_PREFIX']*1000000);
            }

            $campaign = Campaign::where('id', $campaign_id)->first();
     
            $start_date = $campaign->getFirstFlight();
            $start_date = Campaign::formatDates($start_date);

            $end_date = $campaign->getLastFlight();
            $end_date = Campaign::formatDates($end_date);

            if ($data != []) {
                $results['totals'] = $data['totals'];
                $results['totals_ip'] = $data_ip['totals'];
                foreach ($data['all'] as $key => $value) {
                    $formated_key =  Campaign::formatDates($key);
                    $results['all'][$formated_key] = [
                        'reach' => $value['reach'],
                        'freq' => $value['freq'],
                        'reach_ip' => $data_ip['all'][$key]['reach'],
                        'freq_ip' => $data_ip['all'][$key]['freq']
                    ];
                }
            }

            $data = $results;

            if(array_key_exists('format', $_GET) && $_GET['format'] == 'json'){
                return $data;
            }

            return Voyager::view('voyager::campaigns.reachfrequency', compact('data', 'start_date','end_date'));

        } else {
            return '';
        }

    }

    public function specialReports(){
        if($_GET["campaign_id"]){
            return Voyager::view('voyager::campaigns.specialreports');
        }else{
            return '';
        }
    }

}
