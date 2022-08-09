<?php

namespace App\Http\Controllers\Voyager;

use App\Concept;
use App\CustomData;
use App\IabCountry;
use App\IabRegion;
use App\Languages;
use App\IabCity;
use App\Pixel;
use App\Pmp;
use App\Ssp;
use App\Sitelist;
use App\Publisherlist;
use App\Iplist;
use App\Ziplist;
use App\StrategiesIsp;
use App\Keywordslist;
use App\StrategiesKeywordslist;
use App\StrategiesInventoryType;
use App\StrategiesLocationsCity;
use App\StrategiesLang;
use App\StrategiesGeofencing;
use App\StrategiesLocationsCountry;
use App\StrategiesLocationsRegion;
use App\StrategiesZiplist;
use App\StrategiesSitelist;
use App\StrategiesPublisherlist;
use App\StrategiesIplist;
use App\StrategiesPmp;
use App\StrategiesSsp;
use App\StrategiesSegment;
use App\StrategiesContextuals;
use App\StrategiesCustomData;
use App\StrategiesTechnologiesBrowser;
use App\StrategiesTechnologiesDevice;
use App\StrategiesTechnologiesIsp;
use App\StrategiesDataPixel;
use App\StrategiesTechnologiesOs;
use App\Strategy;
use App\Advertiser;
use App\StrategyConcept;
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
use App\User;
use App\Campaign;
use Illuminate\Support\Carbon;
use App\Http\Helpers\CloneHelper;
use App\Http\Helpers\CsvHelper;
use Illuminate\Support\Facades\Log;
use App\Creative;
use App\Organization;

class StrategyController extends VoyagerBaseController
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

            if ($search->value) {
                $search_value = $search->value;
                $query->where(function($query_search) use ($search_value) {
                    $query_search->where('name', 'LIKE', '%'.$search_value.'%')
                        ->orWhere('id', 'LIKE', '%'.$search_value.'%');
                });
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

        // dd($dataTypeContent);

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
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Shows strategies for a certain campaign
    //
    //****************************************

    public function strategiesByCampaign(Request $request, $campaignId)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = 'strategies';

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
                $search_value = $search->value;
                $query->where(function($query_search) use ($search_value) {
                    $query_search->where('name', 'LIKE', '%'.$search_value.'%')
                        ->orWhere('id', 'LIKE', '%'.$search_value.'%');
                });
            }

            //ADD WHERE STRATEGY CAMPAIGN ID
            $query->where('strategies.campaign_id',"=",$campaignId);

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

  
        $view = "voyager::$slug.browse_campaign";

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search', 
            'orderBy',
            'sortOrder',
            'searchable',
            'isServerSide',
            'campaignId'
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
        $logged_user = Auth::user();

        $user = $logged_user;
        
        $organization = Organization::where('id', $user->organization_id)->first();

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

        if($user->role_id == 1 ) {
            $advertisers =  Advertiser::select('id', 'name')->get();
        } else {
            //Get Advertisers From Organization
            $advertisers = Advertiser::select('id','name')->where('organization_id', '=', $user->organization_id)->get();
        }

        //GET SELECTED CONCEPTS
        $selected_concepts = StrategyConcept::where('strategy_id','=',$id)->with('concept.creatives')->get();

        //GET ALL Sitelists
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
            $advertiser_sitelists = Sitelist::where("organization_id","=",$logged_user->organization_id)->get();
            $advertiser_iplists = Iplist::where("organization_id","=",$logged_user->organization_id)->get();
            $advertiser_publisherlists = Publisherlist::where("organization_id","=",$logged_user->organization_id)->get();
        } else {
            $advertiser_sitelists = Sitelist::all();
            $advertiser_iplists = Iplist::all();
            $advertiser_publisherlists = Publisherlist::all();
        }

        //GET SELECTED Sitelists
        $selected_sitelists = StrategiesSitelist::where('strategy_id','=',$id)->with('sitelist')->get();
        $selected_iplists = StrategiesIplist::where('strategy_id','=',$id)->with('iplist')->get();
        $selected_publisherlists = StrategiesPublisherlist::where('strategy_id','=',$id)->with('publisherlist')->get();

        //GET ALL ORGANIZATION PMPs
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
            $organization_pmps = Pmp::where("organization_id","=",$logged_user->organization_id)->get();
        } else {
            $organization_pmps = Pmp::all();
        }

        //GET SELECTED PMPs

        $strategy_pmps = StrategiesPmp::where('strategy_id','=',$id)->get();
        $strategy_pmps->count() > 0 ? $selected_pmps = explode(",",$strategy_pmps[0]["pmp_id"]) : $selected_pmps=array("");
        if($strategy_pmps->count() > 0) {
            $pmps_open_market = $strategy_pmps[0]["open_market"];
        } else {
            $pmps_open_market = array();
        }


        //GET SELECTED SEGMENTS
        
        $segments = StrategiesSegment::where('strategy_id','=',$id)->first();
        $selecteds_segments = $segments != null ? explode(",",$segments["segment_id"]) : array();
        $segments_cpm =  $segments != null ? floatval($segments["data_cpm"]) : 0;

        $segments_inc_exc = $segments["inc_exc"];

        $retriveData = str_replace('-ANDROID','', $segments["segment_id"]); 
        $retriveData = str_replace('-IOS','', $retriveData); 
        $retriveData = str_replace('-IP','', $retriveData); 
        $retriveData = str_replace('-COOKIE','', $retriveData); 
  

        $url = 'http://104.131.2.141:9000/idlist';
        $data = array('ids' => $retriveData);


        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response,true);

        $selecteds_segments_data = [];
  
        foreach ($selecteds_segments as $segment) {
            
            if (strpos($segment, 'ANDROID') != false){
                $key = str_replace('-ANDROID','', $segment); 
                $selecteds_segments_data[]=[
                    "id"=> $segment,
                    "type"=> 'ANDROID',
                    "name"=> $response[$key]['name'],
                    "reach"=> array_key_exists('ANDROID', $response[$key])? $response[$key]['ANDROID']: 0,
                    "price"=> $response[$key]['price']
                ];
            }
            if (strpos($segment, 'IOS') != false){
                $key = str_replace('-IOS','', $segment); 
                $selecteds_segments_data[]=[
                    "id"=> $segment,
                    "type"=> 'IOS',
                    "name"=> $response[$key]['name'],
                    "reach"=> array_key_exists('IOS', $response[$key])? $response[$key]['IOS']: 0,
                    "price"=> $response[$key]['price']
                ];
            }
            if (strpos($segment, 'IP') != false){
                $key = str_replace('-IP','', $segment); 
                $selecteds_segments_data[]=[
                    "id"=> $segment,
                    "type"=> 'IP',
                    "name"=> $response[$key]['name'],
                    "reach"=> array_key_exists('IP', $response[$key])? $response[$key]['IP']: 0,
                    "price"=> $response[$key]['price']
                ];
            }
            if (strpos($segment, 'COOKIE') != false){
                $key = str_replace('-COOKIE','', $segment); 
                $selecteds_segments_data[]=[
                    "id"=> $segment,
                    "type"=> 'COOKIE',
                    "name"=> $response[$key]['name'],
                    "reach"=> array_key_exists('COOKIE', $response[$key])? $response[$key]['COOKIE']: 0,
                    "price"=> $response[$key]['price']
                ];
            }
        }


        //CONTEXTUAL
        $contextual = StrategiesContextuals::where('strategy_id','=',$id)->first();
        $selecteds_contextual = $contextual != null ? explode(",",$contextual["contextual_id"]) : array();
        $contextual_inc_exc = $contextual["inc_exc"];

        $retriveData = $contextual["contextual_id"];
       
        $url = 'http://157.245.219.212:9090/idlist';
        $data = array('ids' => $retriveData);


        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response,true);

        $contextual_selected_data = [];
        $contextual_selected = [];

        foreach ($selecteds_contextual as $contextual) {
            if (array_key_exists($contextual, $response)){
                $contextual_selected []=$contextual;
                $contextual_selected_data[]=[
                    "id"=> $contextual,
                    "type"=> $response[$contextual]['channel'],
                    "name"=> $response[$contextual]['name'],
                    "items"=> $response[$contextual]['items']
                ];
            }
        }
   
        //GET ALL ZipLists
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
            $advertiser_ziplists = Ziplist::where("organization_id","=",$logged_user->organization_id)->get();
        } else {
            $advertiser_ziplists = Ziplist::all();
        }
        //GET SELECTED Ziplists
        $selected_ziplists = StrategiesZiplist::where('strategy_id','=',$id)->with('ziplist')->get();


        //GET ALL ZipLists
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
            $advertiser_keywordslist = Keywordslist::where("organization_id","=",$logged_user->organization_id)->get();
        } else {
            $advertiser_keywordslist = Keywordslist::all();
        }
        
        //GET SELECTED Ziplists
        $selected_keywordslists = StrategiesKeywordslist::where('strategy_id','=',$id)->with('keywordslist')->get();

        //GET Countries
        $iab_countries = IabCountry::all();
        //SELECTED Countries
        $strategy_countries = StrategiesLocationsCountry::where('strategy_id','=',$id)->get();
        //dd($strategy_countries);
        $strategy_countries->count() > 0 ? $selected_countries = explode(",",$strategy_countries[0]["country"]) : $selected_countries=array();
        if($strategy_countries->count() > 0) {
            $country_inc_exc = $strategy_countries[0]["inc_exc"];
        } else {
            $country_inc_exc = array();
        }

        //GET Regions
        $iab_regions = IabRegion::all();
        $strategy_regions = StrategiesLocationsRegion::where('strategy_id','=',$id)->get();
        $strategy_regions->count() > 0 ? $selected_regions = explode(",",$strategy_regions[0]["region"]) : $selected_regions=array();
        
        $region_index= $iab_regions->toArray();
        foreach ($selected_regions as $key => &$reg) {
            $key = array_search($reg, array_column($region_index, 'region'));
            if($key != false){
                $reg = $region_index[$key]['region'] . " (".$region_index[$key]['pid']  . ")";
            }
        }
        unset($region_index);

        if($strategy_regions->count() > 0) {
            $region_inc_exc = $strategy_regions[0]["inc_exc"];
        } else {
            $region_inc_exc = array();
        }

        //GET Selected Cities
        $strategy_cities = StrategiesLocationsCity::where('strategy_id',$id)->get();
        $strategy_cities->count() > 0 ? $selected_cities = explode(",",$strategy_cities[0]["city"]) : $selected_cities=array();

        $cities_labels = [];
      
        foreach($selected_cities as $city){
            if(strpos($city,'-')){
                $aux = explode("-",$city);
                $label = IabCity::where('country',$aux[0])->where('code',$aux[1])->first();
                $cities_labels[$city] = $label->city . ' ('.$label->pid.') '.' ('.$label->country.') ' ;
            }
        }

        if($strategy_cities->count() > 0) {
            $city_inc_exc = $strategy_cities[0]["inc_exc"];
        } else {
            $city_inc_exc = array();
        }

        //GET Langs
        $langs = Languages::all();

        //GET Selected Langs
        $strategy_langs = StrategiesLang::where('strategy_id',$id)->get();
        $strategy_langs->count() > 0 ? $selected_langs = explode(",",$strategy_langs[0]["lang"]) : $selected_langs=array();

        if($strategy_langs->count() > 0) {
            $lang_inc_exc = $strategy_langs[0]["inc_exc"];
        } else {
            $lang_inc_exc = array();
        }

        //GET Selected Geofencing
        $strategy_geofencing = StrategiesGeofencing::where('strategy_id',$id)->get();
        $strategy_geofencing->count() > 0 ? $selected_geofencing = $strategy_geofencing[0]["geolocation"] : $selected_geofencing="";
        $strategy_geofencing->count() > 0 ? $selected_geofencing_inc_exc = $strategy_geofencing[0]["inc_exc"] : $selected_geofencing_inc_exc="";

        //SSPS
        $organization_ssps  = explode("," , $organization->ssps);

        $ssps = Ssp::whereIn('id',$organization_ssps)->get();

        $selected_ssps = array("");
        $strategy_ssps = StrategiesSsp::where('strategy_id','=',$id)->get();
        $strategy_ssps->count() > 0 ? $selected_ssps = explode(",",$strategy_ssps[0]["ssp_id"]) : $selected_ssps=array("");


        //GET Selected Devices
        $strategy_devices = StrategiesTechnologiesDevice::where('strategy_id',$id)->get();
        $strategy_devices->count() > 0 ? $selected_devices = explode(",",$strategy_devices[0]["device_id"]) : $selected_devices=array();

        //GET Selected Inventory Types
        $strategy_itypes = StrategiesInventoryType::where('strategy_id',$id)->get();
        $strategy_itypes->count() > 0 ? $selected_itypes = explode(",",$strategy_itypes[0]["inventory_type"]) : $selected_itypes=array();

        //GET Selected ISPs
        $selected_isps = StrategiesIsp::where('strategy_id',$id)->first();

        //GET Selected Browsers
        $strategy_browsers = StrategiesTechnologiesBrowser::where('strategy_id',$id)->get();
        $strategy_browsers->count() > 0 ? $selected_browsers = explode(",",$strategy_browsers[0]["browser_id"]) : $selected_browsers=array();

        //GET Selected Browsers
        $strategy_oss = StrategiesTechnologiesOs::where('strategy_id',$id)->get();
        $strategy_oss->count() > 0 ? $selected_oss = explode(",",$strategy_oss[0]["os"]) : $selected_oss=array();

        //Pixels
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3) {
            $pixels_list = Pixel::where("organization_id","=",$logged_user->organization_id)->get();
        } else {
            $pixels_list = Pixel::all();
        }

        //GET SELECTED PIXELS
        $pixels_inc_exc =3;
        $selected_pixels = [];

        $strategy_pixels = StrategiesDataPixel::where('strategy_id',$id)->first();
        if ($strategy_pixels) {
            $pixels_inc_exc =$strategy_pixels->inc_exc;
            $selected_pixels = explode(",",$strategy_pixels->pixels);
        }
      
        
        //Custom Datas

        $custom_datas = CustomData::all();

        $strategy_custom_datas = StrategiesCustomData::where('strategy_id',$id)->get();
        $strategy_custom_datas->count() > 0 ? $selected_custom_datas = explode(",",$strategy_custom_datas[0]["custom_datas"]) : $selected_custom_datas=array();

        if($strategy_custom_datas->count() > 0) {
            $custom_datas_inc_exc = $strategy_custom_datas[0]["inc_exc"];
        } else {
            $custom_datas_inc_exc = 3;
        }

        $dmp =[];

        if($_SERVER['HTTP_HOST']=="dsp-panel.inspire.com") {
            $dmp = [
                "180byTWO" => '180x2',
                "OnSpot" => 'onspot',
                "Neuro-Programmatic" => 'neuro',
                "Inspire" => 'inspire',
                "Semcasting" => 'semcasting',
            ];
        }


        if(strpos($organization->dmps, '1') !== false){
            $dmp["180byTWO"] ='180x2';
        }
        if(strpos($organization->dmps, '2') !== false){
            $dmp["Inspire"] ='inspire';
        }
        if(strpos($organization->dmps, '3') !== false){
            $dmp["OnSpot"] ='onspot';
        }
        if(strpos($organization->dmps, '4') !== false){
            $dmp["Neuro-Programmatic"] ='neuro';
        }
        if(strpos($organization->dmps, '5') !== false){
            $dmp["Semcasting"] ='semcasting';
        }

        $contextualTypes = [
            "Neuro-Programmatic" => 'neuro',
            "Keywords" => 'keyword',
            "Categories" => 'category',
        ];

         
        $dataTypeContent->load('Campaign');

        $logged_user_role = Auth::user()->role_id;

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable',
        'selected_concepts','advertisers','advertiser_sitelists','advertiser_iplists','advertiser_publisherlists',
        'selected_sitelists','selected_iplists','selected_publisherlists','advertiser_ziplists','advertiser_keywordslist', 'selected_keywordslists','selected_ziplists',
        'iab_countries','selected_countries','iab_regions','selected_concepts','selected_regions',
        'selected_cities','selected_devices','selected_itypes','selected_isps','selected_browsers',
        'pixels_list','selected_oss','cities_labels','country_inc_exc','region_inc_exc','city_inc_exc','selected_pixels','pixels_inc_exc',
        'organization_pmps','selected_pmps','ssps','selected_ssps','selecteds_segments','segments_cpm','selecteds_segments_data','custom_datas',
        'selected_custom_datas','selected_geofencing','selected_geofencing_inc_exc','selected_langs',
        'langs','lang_inc_exc','custom_datas_inc_exc','pmps_open_market','dmp','segments_inc_exc',
        'segments_target','contextual_selected','contextual_selected_data','contextualTypes','contextual_inc_exc','logged_user_role'));
    }


     /**
     * export to csv
     */
    public function export($id){

        $exportData[] = array('id','campaign','name','date_start','date_end','budget','goal_type','goal_amount','goal_bid_for','goal_min_bid','goal_max_bid','m_type','m_amount','m_stype','i_type','i_amount','i_stype','f_type','f_amount','f_stype','selected_concepts','country_inc_exc','country','region_inc_exc','region','city_inc_exc','city','lang_inc_exc','language','geofencing_inc_exc','geofencingjson','sitelists_inc_exc','sitelists','iplists_inc_exc','iplists','publisherlists_inc_exc','publisherlists','pmps','open_market','ssps_inc_exc','ssps','ziplists_inc_exc','ziplists','keywordslist_inc_exc','keywordslists','devices_inc_exc','device','inventories_inc_exc','inventory_type','isp_inc_exc','isps','os_inc_exc','os','browser_inc_exc','browser','pixels_inc_exc','pixels','custom_datas_inc_exc','custom_datas','segments_inc_exc','segments');

        $creative = Strategy::getDataToExport($id);

        $exportData = array_merge($exportData, $creative);
 
        $filename ="Strategy [".$id."]";
 
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
            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

            //DELETE OLD CONCEPTS
            StrategyConcept::where('strategy_id',$id)->delete();

            //INSERT NEW CONCEPTS
            $selected_concepts = explode(",",$request->get("selected_concepts"));
            foreach ($selected_concepts as $concept){
                if($concept!="") {
                    StrategyConcept::create([
                        "strategy_id" => $id,
                        "concept_id" => $concept
                    ]);
                }
            }

            //INSERT SITELIST

            StrategiesSitelist::where('strategy_id',$id)->delete();
            if($request->get("sitelists")) {
                foreach ($request->get("sitelists") as $sitelist) {

                    $sitelists_inc_exc = $request->get("sitelists_inc_exc")!="" ? $request->get("sitelists_inc_exc") : 3;

                    if(intval($sitelist)){
                        StrategiesSitelist::create([
                            "strategy_id" => $id,
                            "sitelist_id" => $sitelist,
                            "inc_exc" => $sitelists_inc_exc
                        ]);
                    }
                }
            }
            //INSERT IPLIST
            StrategiesIplist::where('strategy_id',$id)->delete();
            if($request->get("iplists")) {
                foreach ($request->get("iplists") as $iplist) {

                    $iplists_inc_exc = $request->get("iplists_inc_exc")!="" ? $request->get("iplists_inc_exc") : 3;

                    if(intval($iplist)){
                        StrategiesIplist::create([
                            "strategy_id" => $id,
                            "iplist_id" => $iplist,
                            "inc_exc" => $iplists_inc_exc
                        ]);
                    }
                }
            }
            //INSERT Sitelists
            StrategiesPublisherlist::where('strategy_id',$id)->delete();
            if($request->get("publisherlists")) {
                foreach ($request->get("publisherlists") as $publisherlist) {

                    $publisherlists_inc_exc = $request->get("publisherlists_inc_exc")!="" ? $request->get("publisherlists_inc_exc") : 3;

                    if(intval($publisherlist)){
                        StrategiesPublisherlist::create([
                            "strategy_id" => $id,
                            "publisherlist_id" => $publisherlist,
                            "inc_exc" => $publisherlists_inc_exc
                        ]);
                    }
                }
            }

            //INSERT ZIPLIST
            StrategiesZiplist::where('strategy_id',$id)->delete();
            if($request->get("ziplists")) {
                foreach ($request->get("ziplists") as $ziplist) {

                    $ziplists_inc_exc = $request->get("ziplists_inc_exc")!="" ? $request->get("ziplists_inc_exc") : 3;

                    StrategiesZiplist::create([
                        "strategy_id" => $id,
                        "ziplist_id" => $ziplist,
                        "inc_exc" => $ziplists_inc_exc
                    ]);
                }
            }
            //INSERT StrategiesKeywords
            StrategiesKeywordslist::where('strategy_id',$id)->delete();
            if($request->get("keywordslists")) {
                foreach ($request->get("keywordslists") as $ziplist) {
                    $keywordslist_inc_exc = $request->get("keywordslist_inc_exc")!="" ? $request->get("keywordslist_inc_exc") : 3;
                    StrategiesKeywordslist::create([
                        "strategy_id" => $id,
                        "keywordslist_id" => $ziplist,
                        "inc_exc" => $keywordslist_inc_exc
                    ]);
                }
            }
      
            //INSERT StrategiesKeywords
            StrategiesIsp::where('strategy_id',$id)->delete();
            if($request->get("isps")) {
                $inc_exc = $request->get("isp_inc_exc")!="" ? $request->get("isp_inc_exc") : 3;
                StrategiesIsp::create([
                    "strategy_id" => $id,
                    "isps" => $request->get("isps"),
                    "inc_exc" => $inc_exc
                ]);
            }

            //INSERT PMPs
            $pmps="";
            $cpmps=0;
            StrategiesPmp::where('strategy_id',$id)->delete();
            if($request->get("pmps")) {
                foreach ($request->get('pmps') as $pmp) {
                    $pmps .= $pmp . ",";
                }
                $cpmps=count($request->get("pmps"));
            } else {
                $cpmps =0;
            }
            //if($countries!="") {
            $open_market = $request->get("open_market");
            if($open_market==""){ $open_market=0; }
            if($open_market=="" && $cpmps==0){ $open_market=1; }

            StrategiesPmp::create([
                "strategy_id" => $id,
                "pmp_id" => rtrim($pmps,","),
                "inc_exc" => $cpmps>0 ? "1" : "3",
                "open_market" => $open_market
            ]);
            //}

            //INSERT Segments
            $segments=$request->get("audiences_selection");
            $audiences_cpm=$request->get("audiences_cpm");

            StrategiesSegment::where('strategy_id',$id)->delete();

            $segments_target=[];

            if($request->has('segments_target_1')){
                $segments_target[]='ANDROID';
            }
            if($request->has('segments_target_2')){
                $segments_target[]='IOS';
            }
            if($request->has('segments_target_3')){
                $segments_target[]='IP';
            }
            if($request->has('segments_target_4')){
                $segments_target[]='COOKIE';
            } 


            if($request->get("segments_inc_exc") == 3){
                $segments='';
            }
            StrategiesSegment::create([
                "strategy_id" => $id,
                "segment_id" => rtrim($segments),
                "inc_exc" => $request->get("segments_inc_exc"),
                "segment_targets" => implode(',' , $segments_target),
                "data_cpm" => $audiences_cpm
            ]);

            //INSERT Contextuals
            $contextual=$request->get("contextual_selection");
            StrategiesContextuals::where('strategy_id',$id)->delete();

            if($request->get("contextual_inc_exc") == 3){
                $contextual='';
            }
           
            StrategiesContextuals::create([
                "strategy_id" => $id,
                "contextual_id" => rtrim($contextual),
                "inc_exc" => $request->get("contextual_inc_exc"),
            ]);

            //INSERT COUNTRY
            $countries="";
            StrategiesLocationsCountry::where('strategy_id',$id)->delete();
            if($request->get("country")) {
                foreach ($request->get('country') as $country) {
                    $countries .= $country . ",";
                }
            }
            //if($countries!="") {
                StrategiesLocationsCountry::create([
                    "strategy_id" => $id,
                    "country" => rtrim($countries,","),
                    "inc_exc" => $request->get('country_inc_exc')
                ]);
            //}


            //INSERT REGION
            $regions="";
            StrategiesLocationsRegion::where('strategy_id',$id)->delete();
            if($request->get("region")) {
                foreach ($request->get('region') as $region) {
                    $regions .= $region . ",";
                }
            } else {

            }
           // if($regions!="") {
                StrategiesLocationsRegion::create([
                    "strategy_id" => $id,
                    "region" => rtrim($regions,","),
                    "inc_exc" => $request->get('region_inc_exc')
                ]);
           // }
            //INSERT CITY
            $cities="";
            StrategiesLocationsCity::where('strategy_id',$id)->delete();
            if($request->get("city")) {
                foreach ($request->get('city') as $city) {
                    $cities .= $city . ",";
                }
            }
          //  if($cities!="") {
                StrategiesLocationsCity::create([
                    "strategy_id" => $id,
                    "city" => rtrim($cities,","),
                    "inc_exc" => $request->get('city_inc_exc')
                ]);
         //   }
            //INSERT Language
            $langs="";
            StrategiesLang::where('strategy_id',$id)->delete();
            if($request->get("language")) {
                foreach ($request->get('language') as $lang) {
                    $langs .= $lang . ",";
                }
            }

            StrategiesLang::create([
                "strategy_id" => $id,
                "lang" => rtrim($langs,","),
                "inc_exc" => $request->get('lang_inc_exc')
            ]);



            //INSERT GEOFENCING
            $geofencing="";
            StrategiesGeofencing::where('strategy_id',$id)->delete();
            if($request->get("geofencing_inc_exc")!=3) {
                //  if($cities!="") {
                StrategiesGeofencing::create([
                    "strategy_id" => $id,
                    "geolocation" => $request->get("geofencingjson"),
                    "inc_exc" => $request->get("geofencing_inc_exc")
                ]);
            } else {
                StrategiesGeofencing::create([
                    "strategy_id" => $id,
                    "geolocation" => "",
                    "inc_exc" => $request->get("geofencing_inc_exc")
                ]);
            }

            //   }
            //INSERT OSs
            $oss="";
            StrategiesTechnologiesOs::where('strategy_id',$id)->delete();
            if($request->get("os")) {
                foreach ($request->get('os') as $os) {
                    $oss.= $os.",";
                }
            }
            if($oss!="") {
                StrategiesTechnologiesOs::create([
                    "strategy_id" => $id,
                    "os" => rtrim($oss,","),
                    "inc_exc" => "1"
                ]);
            }
            //INSERT DEVICE
            $devices="";
            StrategiesTechnologiesDevice::where('strategy_id',$id)->delete();
            if($request->get("device")) {
                foreach ($request->get('device') as $device) {
                    $devices .= $device . ",";
                }
            }
            if($devices!="") {
                StrategiesTechnologiesDevice::create([
                    "strategy_id" => $id,
                    "device_id" => rtrim($devices,",")
                ]);
            }
            //INSERT INVENTORY TYPE
            $itypes="";
            StrategiesInventoryType::where('strategy_id',$id)->delete();
            if($request->get("inventory_type")) {
                foreach ($request->get('inventory_type') as $itype) {
                    $itypes .= $itype . ",";
                }
            }
            if($itypes!="") {
                StrategiesInventoryType::create([
                    "strategy_id" => $id,
                    "inventory_type" => rtrim($itypes,","),
                    "inc_exc" => "1"
                ]);
            }
           
            //INSERT BROWSER
            $browsers="";
            StrategiesTechnologiesBrowser::where('strategy_id',$id)->delete();
            if($request->get("browser")) {
                foreach ($request->get('browser') as $browser) {
                    $browsers .= $browser . ",";
                }
            }
            if($browsers!="") {
                StrategiesTechnologiesBrowser::create([
                    "strategy_id" => $id,
                    "browser_id" => rtrim($browsers,",")
                ]);
            }

            //INSERT PIXELS
            $pixels="";
            StrategiesDataPixel::where('strategy_id',$id)->delete();
            if($request->get("pixels")) {
                foreach ($request->get('pixels') as $pixel) {
                    $pixels .= $pixel . ",";
                }
            }
            if($pixels!="") {
                StrategiesDataPixel::create([
                    "strategy_id" => $id,
                    "pixels" => rtrim($pixels,","),
                    "inc_exc" =>$request->get("pixels_inc_exc") 
                ]);
            }

            //INSERT CUSTOM DATA
            $custom_datas="";
            StrategiesCustomData::where('strategy_id',$id)->delete();
            if($request->get("custom_datas")) {
                foreach ($request->get('custom_datas') as $custom_data) {
                    $custom_datas .= $custom_data . ",";
                }
            }
            if($custom_datas!="") {
                StrategiesCustomData::create([
                    "strategy_id" => $id,
                    "custom_datas" => rtrim($custom_datas,","),
                    "inc_exc" => $request->get("custom_datas_inc_exc")
                ]);
            }

            //INSERT SEGMENTS

            //INSERT SSP
            $ssps="";
            $cssps=0;
            StrategiesSsp::where('strategy_id',$id)->delete();
            if($request->get("ssps")) {
                foreach ($request->get('ssps') as $ssp) {
                    $ssps .= $ssp . ",";
                }
                $cssps=count($request->get("ssps"));
            }
            //if($countries!="") {
            StrategiesSsp::create([
                "strategy_id" => $id,
                "ssp_id" => rtrim($ssps,",")
            ]);
            //}

            //UPDATE CAMPAIGN UPDATED_AT
            $strategy = Strategy::find($id);
            $strategy->date_end = $request->date_end . ' 23:59:59';
            $strategy->save();

            $campaign = Campaign::find($strategy->campaign_id);
            $campaign->updated_at = Carbon::now();
            $campaign->save();


            event(new BreadDataUpdated($dataType, $data));

            

            return redirect()
                ->route("voyager.strategies_campaign.index",$data["campaign_id"])
                ->with([
                    'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
                    'alert-type' => 'success'
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
        
        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
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
        //dd($request);
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
            $data->campaign_id=$request->input('campaign_id');
            $data->date_end = $request->date_end . ' 23:59:59';
            $data->save();
            $sid = $data->id;
            event(new BreadDataAdded($dataType, $data));


            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $data]);
            }

            return redirect()
                ->route("voyager.{$dataType->slug}.edit",$sid)
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
        $campaign = Strategy::where('id', $id)->first();
        
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
        
        return redirect('admin/strategies_campaign/'.$campaign->campaign_id);
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
     * clones a strategy and redirect to the list
     */
    public function clone($id)
    {
        try {
            DB::beginTransaction();
            $strategy =  Strategy::find(intval($id));

            $clone = CloneHelper::cloneStrategy($strategy);
            if($clone['status']){
                DB::commit();
                return redirect('/admin/strategies_campaign/'.$strategy->campaign_id);
            }

            DB::rollBack();
            Log::info($clone['message']);
            return "there was an error trying to clone the strategy, please try again";
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return "there was an error trying to clone the strategy, please try again";
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
        return Voyager::view("voyager::strategies.bulk", compact('advertisers'));
    }


    public function reports($id){
        //return Auth::user();

        //GET REPORT CONTENT BY DATE
        /* $report_json = file_get_contents("http://e-us-east01.resetdigital.co:8080/0_impressions?groupby=date&from=18100100&until=18100723&format=json");

         $reports = "[";
         foreach(json_decode($report_json,true) as $key => $val){
             //formaat date
             $date_year = substr($key,0,2);
             $date_month = substr($key,2,2);
             $date_day = substr($key,4,2);

             //By Date Report
             $date = $date_month."-".$date_day."-".$date_year;
             $impressions = $val[1];
             $clics = $val[4];
             $spent = $val[3]!=0 ? round($val[3]/1000,2) : 0;
             $ecpm = ($val[1]!=0 && $val[1] !=0) ? round($val[1]/$val[3],2) : 0;
             $cpc = ($val[3]!=0 && $val[4]!=0) ? round(($val[3]/1000)/$val[4],2) : 0;
             $ctr = ($val[1] != 0 && $val[4]!= 0)? round(($clics*100)/$impressions,2) : 0;
             $conversions = $val[9];
             $cpa = ($val[3]!=0 && $val[9]!=0) ? round(($val[3]/1000)/$val[9],2) : 0;

             $reports.= "['$date',"; //DATE
             $reports.= $impressions.","; // IMPRESSIONS
             $reports.= $clics.","; // CLICKS
             $reports.= $spent.","; // SPENT
             $reports.= $ecpm.",";  // ECPM
             $reports.= $ctr.","; //CTR
             $reports.= $cpc.","; //CPC
             $reports.= $conversions.","; //CONVERSIONS
             $reports.= $cpa."],"; //CPA
         }
         $reports.="]";*/

        $from = Carbon::now()->subDays(6)->format("ymdH");
        $until = Carbon::now()->format("ymdH");
        //Organization
        $userorganization="";
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3 || Auth::user()->role_id == 5) {
            $logged_user = User::where('id', '=', Auth::user()->id)->get();
            $userorganization = $logged_user[0]->organization_id;
        }

        $user_id = Auth::user()->id;
        $user_role = Auth::user()->role_id;

        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3 || Auth::user()->role_id == 5){

            //Campaigns
            $logged_user = User::where('id', '=', Auth::user()->id)->get();
            //Get Advertisers From Organization
            $advertisers = Advertiser::where('organization_id', '=', $logged_user[0]->organization_id)->get();

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

            //$campaigns = Campaign::where-("","","")->get();

            //Concepts
            //$concepts = Concept::all();

            //Creatives
            //$creatives = Creative::all();

        } else {

            //Campaigns
            $campaigns = Campaign::all();

            //Concepts
            $concepts = Concept::all();

            //Creatives
            $creatives = Creative::all();
        }


        //Domains

        //Countries
        $countries = IabCountry::all();

        //Regions
        $regions = IabRegion::all();



        return Voyager::view('voyager::strategies.reports', compact('reports','from','until','campaigns','concepts','creatives','countries','regions','userorganization','user_id','user_role','id'));
        //return Voyager::view('voyager::strategies.reports', compact('data', 'id'));
    }



    public function reachFrequency($id){
        $strategy_id = $id - $_ENV['WL_PREFIX']*1000000;

        $campaign_id = Strategy::find(intval($strategy_id))->campaign_id;

        $campaign_id = $campaign_id + $_ENV['WL_PREFIX']*1000000;

        $url = "http://51.161.86.82:9080/getcampaigns?campaignid=".$campaign_id."&strategyid=".$id;
        $request = file_get_contents($url);
        $data  = json_decode($request, true);

        
        $url_ip = "http://51.161.86.82:9080/getcampaigns?type=ip&campaignid=".$campaign_id."&strategyid=".$id;
        $request_ip = file_get_contents($url_ip);
        $data_ip  = json_decode($request_ip, true);


        $campaign_id = intval($campaign_id);
     
        if($campaign_id > 1000000){
            $campaign_id = $campaign_id - ($_ENV['WL_PREFIX']*1000000);
        }

        $campaign = Campaign::where('id', $campaign_id)->first();
 
        $start_date = $campaign->getFirstFlight();
        $start_date = Campaign::formatDates($start_date);

        $end_date = $campaign->getLastFlight();
        $end_date = Campaign::formatDates($end_date);

        $results =[
            'totals'=> [],
            'totals_ip'=> [],
            'all'=> []
        ];

        if($data != []){
            $results['totals'] = $data['totals'];
            $results['totals_ip'] = $data_ip['totals'];
            foreach ($data['all'] as $key => $value) {

                $date = substr($key, 4, 2) ."-". substr($key, 6, 2)  ."-". substr($key, 2, 2) ;
              
                $results['all'][$date] = [
                    'reach' => $value['reach'],
                    'freq' => $value['freq'],
                    'reach_ip' => $data_ip['all'][$key]['reach'],
                    'freq_ip' => $data_ip['all'][$key]['freq']
                ];
            }
        }

        $data=$results;
        
        if(array_key_exists('format', $_GET) && $_GET['format'] == 'json'){
            return $data;
        }
        
        return Voyager::view('voyager::strategies.reachfrequency', compact('data','id','end_date','start_date'));
    }

    public function specialReports($id){
        $strategy_id = $id - $_ENV['WL_PREFIX']*1000000;
        $campaign_id = Strategy::find(intval($strategy_id))->campaign_id;
        $campaign_id = $campaign_id + $_ENV['WL_PREFIX']*1000000;

        return Voyager::view('voyager::strategies.specialreports', compact('campaign_id','id'));
    }

}
