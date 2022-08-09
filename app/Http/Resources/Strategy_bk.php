<?php

namespace App\Http\Resources;

use App\IabCity;
use App\Pmp;
use App\Ssp;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class Strategy extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        $browsers_names = array('','chrome','firefox','msie','opera','safari','other');
        //$devices_names = array('','laptop-descktop','smartphone','tablet','connectedtv','other');
        $devices_names = array('','Windows Computer','Apple Computer','iPad','iPhone','iPod','Apple Device','Android Phone','Android Tablet','Other','ctv','ott');
        $oss_names = array('','windows', 'windows 8', 'windows 10', 'macos','linux', 'android', 'ios','other','rokuos','tizen');

        //Locations
        $countries = explode(",",$this->StrategiesLocationsCountry["country"]);
        if(count($countries)==1 && $countries[0]==""){ $countries=array(); }
        $regions = explode(",",$this->StrategiesLocationsRegion["region"]);
        if(count($regions)==1 && $regions[0]==""){ $regions=array(); }
        $langs = explode(",",$this->StrategiesLang["lang"]);
        if(count($langs)==1 && $langs[0]==""){ $langs=array(); }
        $cities = explode(",",$this->StrategiesLocationsCity["city"]);
        //get citiies names
        $city_names= array();
        foreach ($cities as $city){
            if($city!="") {
                $cityparts = explode("-", $city);
                if (count($cityparts)>1) {
                    $city_country = $cityparts[0];
                    $city_code = $cityparts[1];
                    $city_name = IabCity::where('country', '=', $city_country)
                        ->where('code', '=', $city_code)
                        ->first();
                    $city_names[] = $city_name->city;
                }
            }
        }

        //STRATEGY SITELISTS
        $sitelists_ids = array();
        $nsl=0;
        $sitelists = $this->StrategiesSitelist;
        $slincexc = 3;
        foreach ($this->StrategiesSitelist as $val){
            $nsl++;
            $sitelists_ids[]=$val->sitelist_id;
            $slincexc=$val->inc_exc;
        }
        //INC EXC
        if($nsl>0){ $sitelists_incexc = $slincexc; } else { $sitelists_incexc = 3;}

        //STRATEGY IP LISTS
        $iplists_ids = array();
        $ipl=0;
        $iplists = $this->StrategiesIplist;
        $iplincexc = 3;
        foreach ($this->StrategiesIplist as $val){
            $ipl++;
            $iplists_ids[]=$val->iplist_id;
            $iplincexc=$val->inc_exc;
        }
        //INC EXC
        if($ipl>0){ $iplists_incexc = $iplincexc; } else { $iplists_incexc = 3;}

        //STRATEGY ZIP
        $ziplists_ids = array();
        $zpl=0;
        $ziplists = $this->StrategiesZiplist;
        $zplincexc=3;
        foreach ($ziplists as $val){
            $zpl++;
            $ziplists_ids[]=$val->ziplist_id;
            $zplincexc=$val->inc_exc;
        }
        //INC EXC
        if($zpl>0){ $zplists_incexc = $zplincexc; } else { $zplists_incexc = 3;}


        //STRATEGY ZIP
        $keywordslist_ids = array();
        $keywordslist_incexc=3;
        foreach ($this->StrategiesKeywordslist as $val){
            $keywordslist_ids[]=$val->keywordslist_id;
            $keywordslist_incexc=$val->inc_exc;
        }

        //Technologies
        $browsers = array_map('intval', explode(",",$this->StrategiesTechnologiesBrowser["browser_id"]));
        $devices = array_map('intval',explode(",",$this->StrategiesTechnologiesDevice["device_id"]));
        $oss = array_map('intval',explode(",",$this->StrategiesTechnologiesOs["os"]));
        $pixels = array_map('intval',explode(",",$this->StrategiesDataPixel["pixels"]));
        $custom_datas = array_map('intval',explode(",",$this->StrategiesCustomData["custom_datas"]));
        //if (strpos($_SERVER['HTTP_HOST'], 'sitoaudience') !== false) { $custom_datas=array(""); }
        $inventoryType = array_map('intval',explode(",",$this->StrategiesInventoryType["inventory_type"]));

        if($this->StrategiesIsp){
            $isps = explode(",",$this->StrategiesIsp->isps);
            $isps_inc_exc = $this->StrategiesIsp->inc_exc;
        }else{
            $isps = [];
            $isps_inc_exc = 3;
        }

        $selected_segments = explode(",",$this->StrategiesSegment["segment_id"]);
        foreach(array_keys($selected_segments) as $key){
            $selected_segments[$key] = intval($selected_segments[$key]);
        }
        //$nsegments = count($selected_segments);
        if(count($selected_segments)==1 && $selected_segments[0]==0){
            $selected_segments = array();
        }

        $segments = array('');

        //Convert Int Vals to Names
        $browsers_by_name=array();
        foreach ($browsers as $browser){
            $browsers_by_name[] = $browsers_names[$browser];
        }
        $devices_by_name=array();
        foreach ($devices as $device){
            $devices_by_name[] = $devices_names[$device];
        }
        $oss_by_name=array();
        foreach ($oss as $os){
            $oss_by_name[] = $oss_names[$os];
        }

        //Goal Values
        $goal_values = explode(",",$this->goal_values);

        //Format Campaign Pacing and FreqCap
        $pacing_monetary_values = explode(",",$this->pacing_monetary);
        $pacing_impression_values = explode(",",$this->pacing_impression);
        $frequency_cap_values = explode(",",$this->frequency_cap);

        //Prefix
        // die(env('WL_PREFIX'));
        $float_wlprefix = env('WL_PREFIX').".0";
        if((float)$float_wlprefix>0) {
            $wlprefix = (float)$float_wlprefix * 1000000;
        } else {
            $wlprefix =0;
        }

        $prefixedid = $this->id+$wlprefix;

        $redis_strategy_spent = Redis::get('stg_'.$prefixedid.'_spt');
        if(isset($this->budget)){ $remaining_budget = round($this->budget-($redis_strategy_spent/1000000),2); } else { $remaining_budget=""; }
        //$redis_strategy_spent = 5000000;
        $daily_spent = $redis_strategy_spent != null && $redis_strategy_spent > 0 ? round($redis_strategy_spent/1000000,2) : "";

        //die($daily_spent);

        //die($redis_strategy_spent);
        //die($remaining_budget);
        $impressions = Redis::get('stg_'.$prefixedid.'_imp');

        switch ($this->id) {
            case 18:
                $segments = array("840141"); // Oracle > Financial Services
                break;
            case 20:
                $segments = array("840141"); // Oracle > Financial Services
                break;
            case 21:
                $segments = array("840070"); // Oracle > Restaurants
                break;
            default:
                $segments = array(""); // Oracle > Financial Services
        }

        //PMPS
        $pmps = array("");
        $pmps_inc_exc = "";
        $pmps_open_market="";
        if($this->StrategiesPmp->count() > 0) {
            $pmps = explode(",", $this->StrategiesPmp[0]["pmp_id"]);
            $pmps_inc_exc = $this->StrategiesPmp[0]["inc_exc"];
            $pmps_open_market = $this->StrategiesPmp[0]["open_market"];
        }

        if(count($pmps)==1 && $pmps[0]==""){
            $pmps = array();
        }
        // $pmps = array("");
        // if($this->StrategiesPmp->count() > 0) {
        //     $pmps = explode(",", $this->StrategiesPmp[0]["pmp_id"]);
        // }
        // if(count($pmps)==1 && $pmps[0]==""){
        //     $pmps = array();
        // }

        $ssps = array("");
        if($this->StrategiesSsp->count() > 0) {
            $ssps = explode(",", $this->StrategiesSsp[0]["ssp_id"]);
        }
        if(count($ssps)==1 && $ssps[0]==""){
            $ssps = array();
        }

        //Geofencing
        $geofencing = array();
        $geofencing_inc_exc = "";
        if(isset($this->StrategiesGeofencing)) {
            $geofencing = $this->StrategiesGeofencing["geolocation"];
            $geofencing = json_decode($geofencing);
            $geofencing_inc_exc = $this->StrategiesGeofencing["inc_exc"];
        }

        //if budget less than 0 status 0
        if($remaining_budget < 0 || $remaining_budget==""){
            $this->status=0;
        }
        //if date not in range status 0
        if(Carbon::now()->lt($this->date_start)){
            $this->status=0;
        }
        if(Carbon::now()->gt($this->date_end)){
            $this->status=0;
        }
        //$daily_spent=289.08;
        //if daily spoent mayor o igual a pacing monetary status 0
        if(isset($pacing_monetary_values[1]) && $pacing_monetary_values[1]!=""){
            //die($daily_spent);

            //die($pacing_monetary_values[1]);

            if($daily_spent >= $pacing_monetary_values[1]) {
                $this->status=0;
            }
        }

        //die($pacing_monetary_values[1]);

        //IF Pacing impressions set, and Daily impressons equal or more than Pacing IMP status 0
        if(isset($pacing_impression_values[1]) && $pacing_impression_values[1]!=""){
            if($impressions>=$pacing_impression_values[1]) {
                $this->status=0;
            }
        }

        //die(Carbon::now());
        //die($this->date_start);

        $response = [
            "campaign_id" => $this->campaign_id,
            "name" => $this->name,
            "status" => $this->status,
            "channel" => $this->channel,
            "date_start" => strtotime($this->date_start),
            "date_end" => strtotime($this->date_end),
            "budget" => $this->budget,
            "remaining_budget" => $remaining_budget != null && $remaining_budget > 0 ? $remaining_budget : "",
            "daily_spent" => $redis_strategy_spent != null && $redis_strategy_spent > 0 ? round($redis_strategy_spent/1000000,2) : "",//$redis_strategy_spent
            "daily_impressions" => $impressions != null ? $impressions : "" ,
            "goal_type" => $this->goal_type,
            "goal_values" => [
                "value"=>!isset($goal_values[0]) ? 0 : floatval($goal_values[0]),
                "bid_for"=>!isset($goal_values[1]) ? 0: floatval($goal_values[1]),
                "min_bid"=>!isset($goal_values[2]) ? 0.03: floatval($goal_values[2]),
                "max_bid"=>!isset($goal_values[3]) ? 20: floatval($goal_values[3])
            ],
            "pacing_monetary" => [
                "type"=>!isset($pacing_monetary_values[0]) ? 0 : floatval($pacing_monetary_values[0]),
                "amount"=>!isset($pacing_monetary_values[1]) ? 0: floatval($pacing_monetary_values[1]),
                "interval"=>!isset($pacing_monetary_values[2]) ? 0: floatval($pacing_monetary_values[2])
            ],
            "pacing_impression" => [
                "type"=>!isset($pacing_impression_values[0]) ? 0 : floatval($pacing_impression_values[0]),
                "amount"=>!isset($pacing_impression_values[1]) ? 0: floatval($pacing_impression_values[1]),
                "interval"=>!isset($pacing_impression_values[2]) ? 0: floatval($pacing_impression_values[2])
            ],
            "frequency_cap" => [
                "type"=>!isset($frequency_cap_values[0]) ? 0 : floatval($frequency_cap_values[0]),
                "amount"=>!isset($frequency_cap_values[1]) ? 0: floatval($frequency_cap_values[1]),
                "interval"=>!isset($frequency_cap_values[2]) ? 0: floatval($frequency_cap_values[2])
            ],
            "created_at" => $this->created_at->getTimestamp(),
            "updated_at" => $this->updated_at->getTimestamp(),
            "concepts" => $this->Strategyconcept->keyBy("concept_id"),

            //$this->mergeWhen($countries[0]!="", [
            "countries" => ["inc_exc"=>isset($this->StrategiesLocationsCountry["inc_exc"]) ? $this->StrategiesLocationsCountry["inc_exc"] : 3,"data"=>$countries],
            // ]),
            // $this->mergeWhen($regions[0]!="", [
            "regions" => ["inc_exc"=>isset($this->StrategiesLocationsRegion["inc_exc"]) ? $this->StrategiesLocationsRegion["inc_exc"] : 3,"data"=>$regions],
            "languages" => ["inc_exc"=>isset($this->StrategiesLang["inc_exc"]) ? $this->StrategiesLang["inc_exc"] : 3,"data"=>$langs],
            //  ]),
            //  $this->mergeWhen($cities[0]!="", [
            "cities" => ["inc_exc"=>isset($this->StrategiesLocationsCity["inc_exc"]) ? $this->StrategiesLocationsCity["inc_exc"] : 3,"data"=>$city_names],
            "metrocodes" => ["inc_exc"=>isset($this->StrategiesLocationsCity["inc_exc"]) ? $this->StrategiesLocationsCity["inc_exc"] : 3,"data"=>$cities],
            //    ]),

            "pmps" => ["inc_exc"=> $pmps_inc_exc, "data"=>$pmps, "open_market"=>$pmps_open_market],
            "sitelists" => ["inc_exc"=> $sitelists_incexc,"data"=> $sitelists_ids],
            "iplists" => ["inc_exc"=> $iplists_incexc,"data"=> $iplists_ids],
            "ziplists" => ["inc_exc"=> $zplists_incexc,"data"=> $ziplists_ids],
            "keywordslists" => ["inc_exc"=> $keywordslist_incexc,"data"=> $keywordslist_ids],
            /*"sitelists" => $this->StrategiesSitelist->keyBy("sitelist_id"),
            "iplists" => $this->StrategiesIplist->keyBy("iplist_id"),
            "ziplists" => $this->StrategiesZiplist->keyBy("ziplist_id"),*/
            "ssps" => ["inc_exc"=> "1","data"=> $ssps],
            //"pmps" => ["inc_exc"=> 1,"data"=> ['spotx-123','spotx1234']],
            "browsers" => ["inc_exc"=> (count($browsers)==0 || $browsers[0]==0 || $browsers[0]=="")? 3 : 1,"data"=> $browsers[0]==0 ? [] : $browsers_by_name],
            "devices" => ["inc_exc"=> (count($devices)==0 || $devices[0]==0 || $devices[0]=="")? 3 : 1,"data"=> $devices[0]==0 ? [] : $devices_by_name  ],
            "isps" => ["inc_exc"=> $isps_inc_exc,"data"=> $isps],
            "oss" => ["inc_exc"=> (count($oss)==0 || $oss[0]==0 || $oss[0]=="")? 3 : 1,"data" => $oss[0]==0 ? [] : $oss_by_name],
            "pixels_lists" => ["inc_exc"=> (count($pixels)==0 || $pixels[0]==0 || $pixels[0]=="")? 3 : 1,"data" => $pixels[0]==0 ? [] : $pixels],
            "custom_datas" => ["inc_exc"=> (count($custom_datas)==0 || $custom_datas[0]==0 || $custom_datas[0]=="")? 3 : $this->StrategiesCustomData["inc_exc"],"data" => $custom_datas[0]==0 ? [] : $custom_datas],
            /*"segments" => ["inc_exc"=> (count($segments)==0 || $segments[0]==0 || $segments[0]=="") ? 1 : 1,"data" => $segments[0]==0 ? $segments : $segments],*/
            "segments" => ["inc_exc"=> (count($selected_segments)==0 || $selected_segments[0]==0 || $selected_segments[0]=="") ? 3 : 1,"data" => $selected_segments],
            "inventory_types" => ["inc_exc"=> (count($inventoryType)==0 || $inventoryType[0]==0 || $inventoryType[0]=="")? 3 : 1,"data"=> $inventoryType[0]==0 ? [] : $inventoryType],
            "geofencing" => ["inc_exc"=> (is_null($geofencing_inc_exc)=== true || $geofencing_inc_exc=="")? 3 : $geofencing_inc_exc,"data"=> (is_null($geofencing)=== true) ? [] : $geofencing]
        ];

        if($this->fields != null) {
            $comparative = $this->fields;
            $response = array_filter($response, function($k) use($comparative) {
                return in_array($k, $comparative);
            }, ARRAY_FILTER_USE_KEY);
        }

        return  $response;

    }
}
