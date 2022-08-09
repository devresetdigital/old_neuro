<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Strategy;
use App\Campaign;
use App\Advertiser;
use App\Concept;
use App\Http\Resources\Strategy as StrategyResource;
use App\Http\Helpers\CloneHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\StrategiesKeywordslist;
use App\StrategiesInventoryType;
use App\StrategiesLocationsCity;
use App\StrategiesLang;
use App\StrategiesGeofencing;
use App\StrategiesLocationsCountry;
use App\StrategiesLocationsRegion;
use App\StrategiesZiplist;
use App\StrategiesSitelist;
use App\StrategiesIplist;
use App\StrategiesPmp;
use App\StrategiesSsp;
use App\StrategiesSegment;
use App\StrategiesCustomData;
use App\StrategiesTechnologiesBrowser;
use App\StrategiesTechnologiesDevice;
use App\StrategiesTechnologiesIsp;
use App\StrategiesDataPixel;
use App\StrategiesTechnologiesOs;
use App\StrategiesIsp;
use App\StrategyConcept;
use App\IabCountry;
use App\IabCity;
use App\IabRegion;
use App\Languages;
use App\Sitelist;
use App\Iplist;
use App\Ziplist;
use App\Keywordslist;
use App\CustomData;
use App\Pmp;
use App\StrategiesPublisherlist;
use Illuminate\Support\Facades\Cache;

class StrategyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $organization = isset($_GET['organization']) ? $_GET['organization'] : false;
            $schema = Schema::getColumnListing('strategies'); 
            if(isset($_GET['hide'])){
                $fields = array_diff($schema, explode(',',$_GET['hide']));
            }else{
                $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : [];
            }
          
            $strategies = new Strategy;
            if ($organization!= false){
                $organization = explode(',',$organization);
                $campaigns  = Campaign::whereHas('Advertiser', function ($query) use ($organization){
                    $query->whereIn('organization_id', $organization);
                })->get(['id'])->pluck('id')->toArray();
                
                if(empty($campaigns)){
                    return response()->json([]);
                }
                $strategies = $strategies->whereIn('campaign_id', $campaigns);   
            }

            if(empty($fields)){
                $strategies_cache = Cache::get($_ENV["WL_PREFIX"]."_api_strategies_index");
                if( $strategies_cache != null){
                    return $strategies_cache;
                }
                $strategies = $strategies->get();
                $fieldsToReturn = $schema;
            }else{

                $fieldsToReturn = $fields;
                if(!in_array('id',$fields)) {
                    $fields[] = 'id';
                }
                $strategies = $strategies->get($fields);
            }
          
            $response = collect($strategies)->keyBy('id')->map(function ($item) use ($fieldsToReturn) {
                $response =  $item->only($fieldsToReturn);
                if(isset($response['updated_at'])) {
                    $response['updated_at'] = $item->updated_at->getTimestamp();
                }
                if(isset($response['created_at'])) {
                    $response['created_at'] = $item->created_at->getTimestamp();
                }
                if(isset($response['deleted_at'])) {
                    $response['deleted_at'] = $item->deleted_at->getTimestamp();
                }
                if(isset($response['date_start'])) {
                    $response['date_start'] =  strtotime($item->date_start);
                }
                if(isset($response['date_end'])) {
                    $response['date_end'] = strtotime($item->date_end);
                }
                return $response;
            });

            Cache::put($_ENV["WL_PREFIX"].'_api_strategies_index', $response, 0.1);

            return $response;

        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['message' => 'There was an error trying to return the data, please check the fields']);

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    
    /**
     * 
     */
    public function dmpinfo(Request $request){
        $url = "http://104.131.2.141:9000/dmpinfo?" . $_SERVER['QUERY_STRING'];
        $ch = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // set TIMEOUT
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//time in seconds
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);

        return response()->json(['data'=> json_decode($result,true)]);

    }
      /**
     * 
     */
    public function contextualinfo(Request $request){
        $url = "http://segments.resetdigital.co:9090/contextualinfo?" . $_SERVER['QUERY_STRING'];
        $ch = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // set TIMEOUT
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//time in seconds
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);
        
        return response()->json(['data'=> json_decode($result,true)]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //PREFIX
        if($id>1000000){
            $float_wlprefix = $_ENV['WL_PREFIX'].".0";
            $wlprefix = (float) $float_wlprefix*1000000;
            $old_id = $id;

            $id = $id-$wlprefix;
            $prefix = $old_id - $id;

            //die($id);

            if($id>=1000000){
                header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
                die();
            }
        }


        
        $prefixed_id = intval($_ENV["WL_PREFIX"]) + intval($id);
        $cache_data = Cache::get($prefixed_id."_api_strategy_show");
        if( $cache_data != null){
            return $cache_data;
        }


        //Get Campaign by id
        $strategy = Strategy::with("StrategiesLocationsCity","StrategiesLocationsRegion","StrategiesLocationsCountry","StrategiesPmp","StrategiesSitelist.Sitelist","StrategiesIplist.Iplist","StrategiesSsp","StrategiesTechnologiesBrowser","StrategiesTechnologiesDevice","StrategiesTechnologiesIsp","StrategiesTechnologiesOs","StrategiesInventoryType","StrategiesGeofencing")
            ->where('id','=',$id)
            ->first();

        $schema = Schema::getColumnListing('strategies'); 
        $fields = $schema;
       
        if(isset($_GET['hide'])){
            $fields = array_diff($schema, explode(',',$_GET['hide']));
        }else{
            $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : [];
        }
     
        $strategy->fields = $fields;

        //Return Single Campaign as a Resource
        $response =  new StrategyResource($strategy);

        Cache::put($prefixed_id.'_api_strategy_show', $response, 0.1);

        return $response;
    }

    public function geofencing($id)
    {
        //Get Campaign by id
        $strategy = Strategy::with("StrategiesLocationsCity","StrategiesLocationsRegion","StrategiesLocationsCountry","StrategiesPmp","StrategiesSitelist.Sitelist","StrategiesIplist.Iplist","StrategiesSsp","StrategiesTechnologiesBrowser","StrategiesTechnologiesDevice","StrategiesTechnologiesIsp","StrategiesTechnologiesOs","StrategiesInventoryType","StrategiesGeofencing")
            ->where('id','=',$id)
            ->first();

        //Geofencing
        $geofencing = array();
        $geofencing_inc_exc = "";
        if(isset($strategy->StrategiesGeofencing)) {
            $geofencing = $strategy->StrategiesGeofencing["geolocation"];
            $geofencing = json_decode($geofencing);
            $geofencing_inc_exc = $strategy->StrategiesGeofencing["inc_exc"];
        }
        return [
            "geofencing" => ["inc_exc"=> (is_null($geofencing_inc_exc)=== true || $geofencing_inc_exc=="")? 3 : $geofencing_inc_exc,"data"=> (is_null($geofencing)=== true) ? [] : $geofencing]
        ];
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkStore(Request $request)
    {
      
        $advertiser = Advertiser::find(intval($request->input('advertiser')));
        $advertisers = Advertiser::where('organization_id',$advertiser->organization_id)->get()->pluck('id')->toArray();
        
        try {
            DB::beginTransaction();
          
            foreach ($request->input('strategies') as $key => $strategy) {
                if(intval($strategy['campaign']) > 0 ){
                    $campaing = Campaign::where('id', intval($strategy['campaign']))->first();
                }else{
                    $campaing = Campaign::whereIn('advertiser_id', $advertisers)->where('name',$strategy['campaign'])->first();
                }
                if($campaing == null){
                    $campaing = new Campaign();
                    $campaing->advertiser_id = intval($request->input('advertiser'));
                    $campaing->name = $strategy['campaign'];
                    $campaing->user_id = 1;
                    $campaing->status = 0;
                    $campaing->save();
                }

                if (intval($strategy['id']) > 0 ) {

                    $new_strategy = Strategy::where('id',intval($strategy['id']))->first();
           
                    if(isset($strategy['name']) && $strategy['name'] != '***'){
                        $new_strategy->name = $strategy['name'];
                    }
                    if(isset($strategy['date_start']) && $strategy['date_start'] != '***'){
                        $aux = explode("-", $strategy['date_start']);
                        $aux =$aux[2] ."-". $aux[0] ."-". $aux[1];
                        $new_strategy->date_start =  $aux;
                    }
                    if(isset($strategy['date_end']) && $strategy['date_end'] != '***'){
                        $aux = explode("-", $strategy['date_end']);
                        $aux =$aux[2] ."-". $aux[0] ."-". $aux[1];
                        $new_strategy->date_end = $aux;
                    }
                    if(isset($strategy['budget']) && $strategy['budget'] != '***'){
                        $new_strategy->budget =$strategy['budget'];
                    }
                    if(isset($strategy['goal_type']) && $strategy['goal_type'] != '***'){
                        $new_strategy->goal_type  =$strategy['goal_type'];
                    }
                    if($strategy['goal_amount'] != '***' && $strategy['goal_bid_for'] != '***' && $strategy['goal_min_bid'] != '***' && $strategy['goal_max_bid'] != '***'){
                        $new_strategy->goal_values =$strategy['goal_amount'].','.$strategy['goal_bid_for'].','.$strategy['goal_min_bid'].','.$strategy['goal_max_bid'] ;
                    }
                    if($strategy['m_type'] != '***' && $strategy['m_amount'] != '***' && $strategy['m_stype'] != '***'){
                        $new_strategy->pacing_monetary =$strategy['m_type'].','.$strategy['m_amount'].','.$strategy['m_stype'];
                    }
                    if($strategy['i_type'] != '***' && $strategy['i_amount'] != '***' && $strategy['i_stype'] != '***'){
                        $new_strategy->pacing_impression =$strategy['i_type'].','.$strategy['i_amount'].','.$strategy['i_stype'];
                    }
                    if($strategy['f_type'] != '***' && $strategy['f_amount'] != '***' && $strategy['f_stype'] != '***'){
                        $new_strategy->frequency_cap = $strategy['f_type'].','.$strategy['f_amount'].','.$strategy['f_stype'];
                    }
             
                } else {
                    $new_strategy = new Strategy();
                    
                    $new_strategy->name = $strategy['name'];

                    $aux = explode("-", $strategy['date_start']);
                    $aux =$aux[2] ."-". $aux[0] ."-". $aux[1];
                    $new_strategy->date_start = $aux;

                    $aux = explode("-", $strategy['date_end']);
                    $aux =$aux[2] ."-". $aux[0] ."-". $aux[1];
                    $new_strategy->date_end = $aux;

                    $new_strategy->budget =$strategy['budget'];
                    $new_strategy->goal_type  =$strategy['goal_type'];
                    $new_strategy->goal_values =$strategy['goal_amount'].','.$strategy['goal_bid_for'].','.$strategy['goal_min_bid'].','.$strategy['goal_max_bid'] ;
                    $new_strategy->pacing_monetary =$strategy['m_type'].','.$strategy['m_amount'].','.$strategy['m_stype'];
                    $new_strategy->pacing_impression =$strategy['i_type'].','.$strategy['i_amount'].','.$strategy['i_stype'];
                    $new_strategy->frequency_cap = $strategy['f_type'].','.$strategy['f_amount'].','.$strategy['f_stype'];
                }
            
                $new_strategy->campaign_id = $campaing->id;
                $new_strategy->channel = null;//???????
                $new_strategy->status = 0;
                $new_strategy->checked  = 1;
                //$new_strategy->secondbb =0;//???????
                $new_strategy->save();

                $id = $new_strategy->id;

                    
                if ($strategy["selected_concepts"]!='***'){
                    //DELETE OLD CONCEPTS
                    StrategyConcept::where('strategy_id',$id)->delete();
                    //INSERT NEW CONCEPTS
                    $selected_concepts = explode(",",$strategy["selected_concepts"]);

                    foreach ($selected_concepts as $concept){
                        if($concept!="") {
                            if(!intval($concept) > 0){
                                $concept = Concept::where('name',$concept)->first()->id;
                            }
                            StrategyConcept::create([
                                "strategy_id" => $id,
                                "concept_id" => $concept
                            ]);
                        }
                    }
                }


                if ($strategy["sitelists"]!='***'){
                    StrategiesSitelist::where('strategy_id',$id)->delete();

                    if( $strategy["sitelists"]) {
                        $sitelists = explode(',', $strategy["sitelists"]);
                        foreach ($sitelists as $sitelist) {
                            $sitelists_inc_exc =  $strategy["sitelists_inc_exc"]!="" ?  $strategy["sitelists_inc_exc"] : 3;
                            if(!intval($sitelist) > 0){
                                $sitelist = Sitelist::where('name',$sitelist)->first()->id;
                            }
                            if(intval($sitelist)){
                                StrategiesSitelist::create([
                                    "strategy_id" => $id,
                                    "sitelist_id" => $sitelist,
                                    "inc_exc" => $sitelists_inc_exc
                                ]);
                            }
                        }
                    }
                }
                
    
                if ($strategy["iplists"]!='***'){
                    //INSERT IPLIST
                    StrategiesIplist::where('strategy_id',$id)->delete();
                    if( $strategy["iplists"]) {
                        $iplists = explode(',', $strategy["iplists"]);

                        foreach ($iplists as $iplist) {
                            $iplists_inc_exc =  $strategy["iplists_inc_exc"]!="" ?  $strategy["iplists_inc_exc"] : 3;
                            if(!intval($iplist) > 0){
                                $iplist = Iplist::where('name',$iplist)->first()->id;
                            }
                            if(intval($iplist)){
                                StrategiesIplist::create([
                                    "strategy_id" => $id,
                                    "iplist_id" => $iplist,
                                    "inc_exc" => $iplists_inc_exc
                                ]);
                            }
                        }
                    }
                }

                if ($strategy["ziplists"]!='***'){
                    //INSERT ZIPLIST
                    StrategiesZiplist::where('strategy_id',$id)->delete();
                    if( $strategy["ziplists"]) {
                        $ziplists = explode(',', $strategy["ziplists"]);
                        foreach ($ziplists as $ziplist) {

                            $ziplists_inc_exc =  $strategy["ziplists_inc_exc"]!="" ?  $strategy["ziplists_inc_exc"] : 3;
                            if(!intval($ziplist) > 0){
                                $ziplist = Ziplist::where('name',$ziplist)->first()->id;
                            }
                            StrategiesZiplist::create([
                                "strategy_id" => $id,
                                "ziplist_id" => $ziplist,
                                "inc_exc" => $ziplists_inc_exc
                            ]);
                        }
                    }
                }

                if ($strategy["keywordslists"]!='***'){
                    //INSERT StrategiesKeywords
                    StrategiesKeywordslist::where('strategy_id',$id)->delete();
                    if( $strategy["keywordslists"]) {
                        $keywordslists = explode(',', $strategy["keywordslists"]);
                        foreach ($keywordslists as $keyword) {
                            $keywordslist_inc_exc =  $strategy["keywordslist_inc_exc"]!="" ?  $strategy["keywordslist_inc_exc"] : 3;
                            if(!intval($keyword) > 0){
                                $keyword = Keywordslist::where('name',$keyword)->first()->id;
                            }
                            StrategiesKeywordslist::create([
                                "strategy_id" => $id,
                                "keywordslist_id" => $keyword,
                                "inc_exc" => $keywordslist_inc_exc
                            ]);
                        }
                    }
                }

                if ($strategy["isps"]!='***'){
                    //INSERT isps
                    StrategiesIsp::where('strategy_id',$id)->delete();
                    if( $strategy["isps"]) {
                        $inc_exc =  $strategy["isp_inc_exc"]!="" ?  $strategy["isp_inc_exc"] : 3;
                        StrategiesIsp::create([
                            "strategy_id" => $id,
                            "isps" =>  $strategy["isps"],
                            "inc_exc" => $inc_exc
                        ]);
                    }
                }

                if ($strategy['pmps'] != '' && $strategy["pmps"]!='***'){
                    //INSERT PMPs
                    $pmps = explode(',',  $strategy['pmps']);
                    $cpmps=count(explode(',',  $strategy['pmps']));
                    StrategiesPmp::where('strategy_id',$id)->delete();

                    $pmp_save=[];
   
                    foreach ($pmps as $key => $pmp) {
                        if(intval($pmp) > 0){
                            $pmp_save[]= Pmp::find(intval($pmp))->deal_id;
                        } else{
                            $pmp_save[]= Pmp::where('name', $pmp)->first()->deal_id;
                        }   
                    }

                    $open_market =  $strategy["open_market"];
                    
                    if($open_market==""){ $open_market=0; }
                    if($open_market=="" && $cpmps==0){ $open_market=1; }

                    StrategiesPmp::create([
                        "strategy_id" => $id,
                        "pmp_id" => implode(",",$pmp_save),
                        "inc_exc" => $cpmps>0 ? "1" : "3",
                        "open_market" => $open_market
                    ]);
                }
                

                if ($strategy["segments"]!='***'){
                    //INSERT Segments
                    $segments= $strategy["segments"];
                    $segments_inc_exc =   $strategy["segments_inc_exc"];
                    StrategiesSegment::where('strategy_id',$id)->delete();

                    $segments_target = [];

                    StrategiesSegment::create([
                        "strategy_id" => $id,
                        "segment_id" => rtrim($segments),
                        "inc_exc" => intval($segments_inc_exc) > 0 ? intval($segments_inc_exc) : 3 ,
                        "segment_targets" => implode(',' , $segments_target)
                    ]);
                }
            
                if ($strategy["country"]!='***'){
                    //INSERT COUNTRY
                    $countries="";
                    StrategiesLocationsCountry::where('strategy_id',$id)->delete();
                    if($strategy["country"]) {
                        foreach ( explode(',',$strategy['country']) as $country) {
                            $exists = IabCountry::whereRaw("UPPER(`country`) LIKE '%". strtoupper($country)."%'")->first(); 
                            $countries .= $exists->code . ",";
                        }
                    }
            
                    StrategiesLocationsCountry::create([
                        "strategy_id" => $id,
                        "country" => rtrim($countries,","),
                        "inc_exc" =>  intval($strategy['country_inc_exc']) != 0 ? intval($strategy['country_inc_exc']): 3
                    ]);
                }
                
                if ($strategy["region"]!='***'){
                    //INSERT REGION
                    $regions="";
                    StrategiesLocationsRegion::where('strategy_id',$id)->delete();
                    if( $strategy["region"]) {
                        foreach ( explode(',',$strategy['region']) as $region) {
                            $exists = IabRegion::whereRaw("UPPER(`region`) LIKE '%". strtoupper($region)."%'")->count(); 
                            $regions .= $exists->code . ",";
                        }
                    } 
        
                    StrategiesLocationsRegion::create([
                        "strategy_id" => $id,
                        "region" => rtrim($regions,","),
                        "inc_exc" =>  $strategy['region_inc_exc']
                    ]);
                }

                if ($strategy["city"]!='***'){
                    //INSERT CITY
                    $cities="";
                    StrategiesLocationsCity::where('strategy_id',$id)->delete();
                    if( $strategy["city"]) {
                        foreach ( explode(',',$strategy['city'])  as $city) {
                            $exists = IabCity::whereRaw("UPPER(`city`) LIKE '%". strtoupper($city)."%'")->count(); 
                            $cities .= $exists->code . ",";
                        }
                    }
        
                    StrategiesLocationsCity::create([
                        "strategy_id" => $id,
                        "city" => rtrim($cities,","),
                        "inc_exc" =>  $strategy['city_inc_exc']
                    ]);

                }

                if ($strategy["language"]!='***'){
                    //INSERT Language
                    $langs="";
                    StrategiesLang::where('strategy_id',$id)->delete();
                    if( $strategy["language"]) {
                        foreach (explode(',',$strategy['language']) as $lang) {
                            $exists = Languages::whereRaw("UPPER(`language`) LIKE '%". strtoupper($lang)."%'")->count(); 
                            $langs .=$exists->code . ",";
                        }
                    }

                    StrategiesLang::create([
                        "strategy_id" => $id,
                        "lang" => rtrim($langs,","),
                        "inc_exc" =>  $strategy['lang_inc_exc']
                    ]);

                }

                if ($strategy["geofencingjson"]!='***'){
                    //INSERT GEOFENCING
                    $geofencing="";
                    StrategiesGeofencing::where('strategy_id',$id)->delete();
                    if( $strategy["geofencing_inc_exc"]!=3) {
                        StrategiesGeofencing::create([
                            "strategy_id" => $id,
                            "geolocation" =>  $strategy["geofencingjson"],
                            "inc_exc" =>  $strategy["geofencing_inc_exc"]
                        ]);
                    } else {
                        StrategiesGeofencing::create([
                            "strategy_id" => $id,
                            "geolocation" => "",
                            "inc_exc" =>  $strategy["geofencing_inc_exc"]
                        ]);
                    }
                }
         
                if ($strategy["os"]!='***'){
                    //INSERT OSs
                    StrategiesTechnologiesOs::where('strategy_id',$id)->delete();
                    if($strategy["os"]!="") {
                        $oss = explode(',' , $strategy["os"]);
                        $oss_save = [];
                        $os_values = array(
                            null,
                            'Windows 7',
                            'Windows 8',
                            'Windows 10',
                            'Mac OS',
                            'Linux',
                            'ANDROID',
                            'IOS',
                            'Other',
                            'Roku OS',
                            'Tizen',
                        );

                        foreach ($oss as $key => $os) {
                        if(intval($os) > 0){
                                $oss_save[] = $os;
                        }else{
                            $oss_save[] = array_search($os, $os_values);
                        }
                        }

                        StrategiesTechnologiesOs::create([
                            "strategy_id" => $id,
                            "os" =>rtrim($strategy["os"],','),
                            "inc_exc" => "1"
                        ]);
                    }
                }

                

                if ($strategy["device"]!='***'){
                    StrategiesTechnologiesDevice::where('strategy_id',$id)->delete();
           
                    $devices = $strategy['device'] != null ? explode(",",$strategy['device']) : '';
            
                    if($devices!="") {
                        $devices_save = [];
                        $devices_values = array(
                            null,
                            'Windows Computer',
                            'Apple Computer',
                            'Ipad',
                            'Iphone',
                            'Ipod',
                            'Apple Device',
                            'Android Phone',
                            'Android Tablet',
                            'Other'
                        );
                        foreach ($devices as $key => $device) {
                        if(intval($device) > 0){
                                $devices_save[] = $device;
                        }else{
                            $devices_save[] = array_search($device, $devices_values);
                        }
                        }

                        StrategiesTechnologiesDevice::create([
                            "strategy_id" => $id,
                            "device_id" => implode(",",$devices_save)
                        ]);
                    }
                }
                
         
                if ($strategy["inventory_type"]!='***'){
                    //INSERT INVENTORY TYPE
                    StrategiesInventoryType::where('strategy_id',$id)->delete();
                    if($strategy["inventory_type"] != "") {
                        $inventories = explode("," , $strategy["inventory_type"] );
                        $inventory_save = [];
                        $inventory_values = array(
                            null,
                            'Desktop & Mobile Web',
                            'Mobile In-App',
                            'Mobile Optimized Web'
                        );
                        foreach ($inventories as $key => $inventory) {
                        if(intval($inventory) > 0){
                                $inventory_save[] = $inventory;
                        }else{
                            $inventory_save[] = array_search($inventory, $inventory_values);
                        }
                        }
                        StrategiesInventoryType::create([
                            "strategy_id" => $id,
                            "inventory_type" => implode(",",$inventory_save),
                            "inc_exc" => "1"
                        ]);
                    }
                }
                

                if ($strategy["browser"]!='***'){
                    //INSERT BROWSER
                    StrategiesTechnologiesBrowser::where('strategy_id',$id)->delete();
                    if($strategy["browser"]!="") {
                        $browsers = explode("," , $strategy["browser"] );
                        $browser_save = [];
                        $browser_values = array(
                            null,
                            'Chrome',
                            'Firefox',
                            'Windows 10',
                            'MSIE',
                            'Opera',
                            'Safari',
                            'Other'
                        );
                        foreach ($browsers as $key => $browser) {
                        if(intval($browser) > 0){
                                $browser_save[] = $browser;
                        }else{
                            $browser_save[] = array_search($browser, $browser_values);
                        }
                        }
                        StrategiesTechnologiesBrowser::create([
                            "strategy_id" => $id,
                            "browser_id" => implode(",",$browser_save)
                        ]);
                    }
                }
                


                

                /*StrategiesDataPixel::where('strategy_id',$id)->delete();
                //INSERT PIXELS
                if($strategy["pixels"]!="") {
                    StrategiesDataPixel::create([
                        "strategy_id" => $id,
                        "pixels" => rtrim($strategy["pixels"],",")
                    ]);
                }
                */

                if ($strategy["custom_datas"]!='***'){
                    //INSERT CUSTOM DATA
                    StrategiesCustomData::where('strategy_id',$id)->delete();
                    if($strategy['custom_datas']!="") {
                        $custom_datas_save=[];
                        $custom_datas= explode(",", $strategy['custom_datas']);

                        foreach ($custom_datas as $key => $custom_data) {
                            if(intval($custom_data) > 0){
                                $custom_datas_save[]= $custom_data;
                            } else {
                                $custom_datas_save[]= CustomData::where('name', $custom_data)->first()->id;
                            }   
                        }   

                        StrategiesCustomData::create([
                            "strategy_id" => $id,
                            "custom_datas" => implode(",",$custom_datas_save),
                            "inc_exc" =>  $strategy["custom_datas_inc_exc"]
                        ]);
                    }
                }

                if ($strategy["ssps"]!='***'){
                    //INSERT SSP
                    $ssps=$strategy["ssps"];
                    StrategiesSsp::where('strategy_id',$id)->delete();
                    if($ssps!="") {
                        StrategiesSsp::create([
                            "strategy_id" => $id,
                            "ssp_id" => rtrim($ssps,",")
                        ]);
                    }
                }
                
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTrace());
            Log::info($e->getMessage());
            return [
                'error' => true,
                'message' =>'there was an error trying to proccess bulk strategies, please try again'
            ];
        }  
        DB::commit();
        return [
            'error' => false,
            'message' =>'bulk upload process completed successfully'
        ];
    }

    public function strategiesLists(Request $request){
        if(!array_key_exists('list_type', $_GET)){
            return [
                'error' => true,
                'message' =>'missing argument list_type'
            ];
        }
        if(!array_key_exists('strategy_active', $_GET)){
            return [
                'error' => true,
                'message' =>'missing argument strategy_active'
            ];
        }
        if(!array_key_exists('campaign_active', $_GET)){
            return [
                'error' => true,
                'message' =>'missing argument campaign_active'
            ];
        }
        if(!array_key_exists('list_id', $_GET)){
            return [
                'error' => true,
                'message' =>'missing argument list_id'
            ];
        }

        $all = 0;
        if(array_key_exists('all', $_GET) && $_GET['all'] != 0){
            $all = 1;
        }
         
         
        
        switch ($_GET['list_type']) {
            case 'sitelist':
                $list_ids = StrategiesSitelist::where('sitelist_id','=',$_GET['list_id'])->pluck('strategy_id')->toArray();
                $query =  Strategy::whereIn('id', $list_ids);
                break;
            case 'publisherlists':
                $list_ids = StrategiesPublisherlist::where('publisherlist_id','=',$_GET['list_id'])->pluck('strategy_id')->toArray();
                $query =  Strategy::whereIn('id', $list_ids);
                break;
            case 'iplists':
                $list_ids = StrategiesIplist::where('iplist_id','=',$_GET['list_id'])->pluck('strategy_id')->toArray();
                $query =  Strategy::whereIn('id', $list_ids);
                break;
            case 'keywordslists':
                $list_ids = StrategiesKeywordslist::where('keywordslist_id','=',$_GET['list_id'])->pluck('strategy_id')->toArray();
                $query =  Strategy::whereIn('id', $list_ids);
                break;
            case 'ziplists':
                $list_ids = StrategiesZiplist::where('ziplist_id','=',$_GET['list_id'])->pluck('strategy_id')->toArray();
                $query =  Strategy::whereIn('id', $list_ids);
                break;
            case 'custom-datas':
                $list_ids = StrategiesCustomData::
                where('custom_datas','like', $_GET['list_id'].",%")->
                orWhere('custom_datas','like', "%,".$_GET['list_id'])->
                orWhere('custom_datas','like', "%,".$_GET['list_id'].",%")->
                orWhere('custom_datas','=', $_GET['list_id'])
                ->pluck('strategy_id')->toArray();
                $query =  Strategy::whereIn('id', $list_ids);
                break;
            case 'blocklists':
                $list_ids = DB::table('strategies_blacklist')->where('blacklist_id','=',$_GET['list_id'])->pluck('strategy_id')->toArray();
                $query =  Strategy::whereIn('id', $list_ids);
                break;
            default:
                return [];
                break;
        }

        if ($all != 1){
            $query->where('status', intval($_GET['strategy_active']));
 
            $campaign_active=intval($_GET['campaign_active']);
          
            $query->whereHas('Campaign', function($q) use ($campaign_active){
                $q->where('status', '=', $campaign_active);
            });
        }

        $reuslt = $query->with('Campaign')->get()->toArray();

        return $reuslt;

    }

    public function changeStatus($id){
        $strategy = Strategy::find(intval($id));
      
        if(!$strategy){
            return response()->json('invalid id' , 401);
        }
        $strategy->status =  $strategy->status == 0 ? 1 : 0;
        $strategy->save();
        return response()->json('success' , 200);
    }
}
