<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

use App\StrategiesCustomData;
use App\StrategiesDataPixel;
use App\StrategiesTechnologiesOs;
use App\StrategiesTechnologiesBrowser;
use App\StrategiesIsp;
use App\StrategiesInventoryType;
use App\StrategiesTechnologiesDevice;
use App\StrategiesSsp;
use App\StrategiesGeofencing;
use App\StrategiesLang;
use App\StrategiesLocationsCity;
use App\StrategiesLocationsRegion;
use App\StrategiesLocationsCountry;
use App\StrategiesKeywordslist;
use App\StrategiesZiplist;
use App\StrategiesSegment;
use App\StrategiesPmp;
use App\StrategiesIplist;
use App\StrategiesSitelist;
use App\StrategiesPublisherlist;
use App\Pmp;


class Strategy extends Model
{
    use SoftDeletes;

    public function Campaign(){
        return $this->belongsTo('App\Campaign');
    }
    public function getBudgetAttribute($value)
    {
        return round($value,2);
    }
    public function StrategyConcept(){
        return $this->hasMany('App\StrategyConcept');
    }
    public function StrategiesLocationsCity(){
        return $this->hasOne('App\StrategiesLocationsCity');
    }
    public function StrategiesLang(){
        return $this->hasOne('App\StrategiesLang');
    }
    public function StrategiesGeofencing(){
        return $this->hasOne('App\StrategiesGeofencing');
    }
    public function StrategiesLocationsRegion(){
        return $this->hasOne('App\StrategiesLocationsRegion');
    }
    public function StrategiesLocationsCountry(){
        return $this->hasOne('App\StrategiesLocationsCountry');
    }
    public function StrategiesPmp(){
        return $this->hasMany('App\StrategiesPmp');
    }
    public function StrategiesSitelist(){
        return $this->hasMany('App\StrategiesSitelist');
    }
    public function StrategiesPublisherlist(){
        return $this->hasMany('App\StrategiesPublisherlist');
    }
    public function StrategiesIplist(){
        return $this->hasMany('App\StrategiesIplist');
    }
    public function StrategiesZiplist(){
        return $this->hasMany('App\StrategiesZiplist');
    }
    public function StrategiesSsp(){
        return $this->hasMany('App\StrategiesSsp');
    }
    public function StrategiesIsp(){
        return $this->hasOne('App\StrategiesIsp');
    }
    public function StrategiesKeywordslist(){
        return $this->hasMany('App\StrategiesKeywordslist');
    }
    public function StrategiesTechnologiesBrowser(){
        return $this->hasOne('App\StrategiesTechnologiesBrowser');
    }
    public function StrategiesTechnologiesDevice(){
        return $this->hasOne('App\StrategiesTechnologiesDevice');
    }
    public function StrategiesTechnologiesIsp(){
        return $this->hasOne('App\StrategiesTechnologiesIsp');
    }
    public function StrategiesTechnologiesOs(){
        return $this->hasOne('App\StrategiesTechnologiesOs');
    }
    public function StrategiesDataPixel(){
        return $this->hasOne('App\StrategiesDataPixel');
    }
    public function StrategiesInventoryType(){
        return $this->hasOne('App\StrategiesInventoryType');
    }
    public function StrategiesSegment(){
        return $this->hasOne('App\StrategiesSegment');
    }
    public function StrategiesContextuals(){
        return $this->hasOne('App\StrategiesContextuals');
    }
    public function StrategiesCustomData(){
        return $this->hasOne('App\StrategiesCustomData');
    }
    


     /**
     * 
     */
    static function getDataToExport($id){

        $strategy =self::find($id);

        //GET SELECTED CONCEPTS
        $selected_concepts = StrategyConcept::where('strategy_id','=',$id)->get()->pluck('concept_id')->toArray();
       
        //SELECTED Countries
        $strategy_countries = StrategiesLocationsCountry::where('strategy_id','=',$id)->first();
        $country_inc_exc =''; // $strategy_countries!=null ?  $strategy_countries->inc_exc : ''; 
        $strategy_countries =''; // $strategy_countries!=null ? $strategy_countries->country: ''; 
  
        //REGIONS
        $strategy_regions = StrategiesLocationsRegion::where('strategy_id','=',$id)->first();
        $region_inc_exc = ''; //$strategy_regions!=null ?  $strategy_regions->inc_exc: ''; 
        $strategy_regions =''; // $strategy_regions!=null ?  $strategy_regions->region : ''; 

        //GET Selected Cities
        $strategy_cities = StrategiesLocationsCity::where('strategy_id',$id)->first();
        $city_inc_exc = ''; //$strategy_cities!=null ?  $strategy_cities->inc_exc: ''; 
        $strategy_cities = ''; //$strategy_cities!=null ?  $strategy_cities->city : ''; 
  
        //GET Selected Langs
        $strategy_langs = StrategiesLang::where('strategy_id',$id)->first();
        $lang_inc_exc = $strategy_langs!=null ?  $strategy_langs->inc_exc: ''; 
        $strategy_langs = $strategy_langs!=null ?  $strategy_langs->lang : ''; 
       
        //GET Selected Geofencing
        $strategy_geofencing = StrategiesGeofencing::where('strategy_id',$id)->first();
        $geofencing_inc_exc = $strategy_geofencing!=null ?  $strategy_geofencing->inc_exc: ''; 
        $strategy_geofencing = $strategy_geofencing!=null ?  $strategy_geofencing->geolocation : ''; 


        //GET SELECTED Sitelists
        $selected_sitelists = StrategiesSitelist::where('strategy_id','=',$id)->get()->pluck('sitelist_id')->toArray();
        $sitelists_inc_exc = 3;
        if($selected_sitelists != []){
            $sitelists_inc_exc = StrategiesSitelist::where('strategy_id','=',$id)->first()->inc_exc;
        }

        //IP LIST
        $selected_iplists = StrategiesIplist::where('strategy_id','=',$id)->with('iplist')->get()->pluck('iplist_id')->toArray();
        $iplists_inc_exc = 3;
        if($selected_iplists != []){
            $iplists_inc_exc = StrategiesIplist::where('strategy_id','=',$id)->first()->inc_exc;
        }

        //PMPS
        $strategy_pmps = StrategiesPmp::where('strategy_id','=',$id)->first();
        $pmps_open_market = $strategy_pmps!=null ?  $strategy_pmps->open_market: ''; 
        $strategy_pmps = $strategy_pmps!=null ?  $strategy_pmps->pmp_id : []; 

        if($strategy_pmps != []){
            $deal_ids = explode(',', $strategy_pmps);
            $strategy_pmps = Pmp::whereIn('deal_id',$deal_ids)->get()->pluck('name')->toArray();
            $strategy_pmps = implode(',',$strategy_pmps );
        }else{
            $strategy_pmps = '';
        }

        //SSPS
        $strategy_ssps = StrategiesSsp::where('strategy_id','=',$id)->first();
        $strategy_ssps =  $strategy_ssps != null ?  $strategy_ssps->ssp_id : '';
        $ssps_inc_exc = $strategy_ssps != null ? 1 : 3;

        //GET SELECTED Ziplists
        $selected_ziplists = StrategiesZiplist::where('strategy_id','=',$id)->get()->pluck('ziplist_id')->toArray();;
        $ziplists_inc_exc = 3;
        if($selected_ziplists != []){
            $ziplists_inc_exc = StrategiesZiplist::where('strategy_id','=',$id)->first()->inc_exc;
        }

        //GET SELECTED 
        $selected_keywordslists = StrategiesKeywordslist::where('strategy_id','=',$id)->get()->pluck('keywordslist_id')->toArray();;
        $keywordslists_inc_exc = 3;
        if($selected_ziplists != []){
            $keywordslists_inc_exc = StrategiesKeywordslist::where('strategy_id','=',$id)->first()->inc_exc;
        };

        //GET Selected Devices
        $strategy_devices = StrategiesTechnologiesDevice::where('strategy_id',$id)->first();
        $devices_inc_exc = $strategy_devices != null ?  $strategy_devices->inc_exc: ''; 
        $strategy_devices = $strategy_devices != null ?  $strategy_devices->device_id : ''; 

        //GET Selected Inventory Types
        $strategy_itypes = StrategiesInventoryType::where('strategy_id',$id)->first();
        $itypes_inc_exc = $strategy_itypes != null ?  $strategy_itypes->inc_exc: ''; 
        $strategy_itypes = $strategy_itypes != null ?  $strategy_itypes->inventory_type : ''; 

        //GET Selected ISPs
        $selected_isps = StrategiesIsp::where('strategy_id',$id)->first();
        $isps_inc_exc = $selected_isps != null ?  $selected_isps->inc_exc: ''; 
        $selected_isps = $selected_isps != null ?  $selected_isps->isps : ''; 

        //GET Selected Browsers
        $strategy_oss = StrategiesTechnologiesOs::where('strategy_id',$id)->first();
        $oss_inc_exc = $strategy_oss != null ?  $strategy_oss->inc_exc: ''; 
        $strategy_oss = $strategy_oss != null ?  $strategy_oss->os : ''; 
        
        //GET Selected Browsers
        $strategy_browsers = StrategiesTechnologiesBrowser::where('strategy_id',$id)->first();
        $browsers_inc_exc = $strategy_browsers != null ? 1 : ''; 
        $strategy_browsers = $strategy_browsers != null ?  $strategy_browsers->browser_id : ''; 
                
        //GET SELECTED PIXELS
        $strategy_pixels = StrategiesDataPixel::where('strategy_id',$id)->first();
        $pixels_inc_exc = $strategy_pixels != null ?  $strategy_pixels->inc_exc: ''; 
        $strategy_pixels = $strategy_pixels != null ?  $strategy_pixels->pixels : ''; 

        //Custom Datas
        $strategy_custom_datas = StrategiesCustomData::where('strategy_id',$id)->first();
        $custom_datas_inc_exc = $strategy_custom_datas != null ?  $strategy_custom_datas->inc_exc: ''; 
        $strategy_custom_datas = $strategy_custom_datas != null ?  $strategy_custom_datas->custom_datas : ''; 

        //GET SELECTED SEGMENTS
        $segments = StrategiesSegment::where('strategy_id','=',$id)->first();
        $segments_inc_exc = $segments != null ?  $segments->inc_exc: ''; 
        $segments = $segments != null ?  $segments->segment_id : ''; 
        

        $goal_values = explode(',', $strategy->goal_values);
        $pacing_monetary = explode(',', $strategy->pacing_monetary);
        $pacing_impression = explode(',', $strategy->pacing_impression);
        $frequency_cap = explode(',', $strategy->frequency_cap);


        //format date
     
        $start =  substr($strategy->date_start,0,10);
        $end =  substr($strategy->date_end,0,10);

        if($start != '') {
            $start = explode("-", $start);
            $start = $start[1] ."-". $start[2] ."-". $start[0];
        }
        if($end != '') {
            $end = explode("-", $end);
            $end = $end[1] ."-". $end[2] ."-". $end[0];
        }

        return  array(array(
            $strategy->id,
            $strategy->campaign_id,
            $strategy->name,
            $start,
            $end,
            $strategy->budget,
            $strategy->goal_type,
            array_key_exists(0,$goal_values) ? $goal_values[0] : '',
            array_key_exists(1,$goal_values) ? $goal_values[1] : '',
            array_key_exists(2,$goal_values) ? $goal_values[2] : '',
            array_key_exists(3,$goal_values) ? $goal_values[3] : '',
            array_key_exists(0,$pacing_monetary) ? $pacing_monetary[0] : '',
            array_key_exists(1,$pacing_monetary) ? $pacing_monetary[1] : '',
            array_key_exists(2,$pacing_monetary) ? $pacing_monetary[2] : '',
            array_key_exists(0,$pacing_impression) ? $pacing_impression[0] : '',
            array_key_exists(1,$pacing_impression) ? $pacing_impression[1] : '',
            array_key_exists(2,$pacing_impression) ? $pacing_impression[2] : '',
            array_key_exists(0,$frequency_cap) ? $frequency_cap[0] : '',
            array_key_exists(1,$frequency_cap) ? $frequency_cap[1] : '',
            array_key_exists(2,$frequency_cap) ? $frequency_cap[2] : '',
            implode(',',$selected_concepts),
            $country_inc_exc,
            $strategy_countries,
            $region_inc_exc,
            $strategy_regions,
            $city_inc_exc,
            $strategy_cities,
            $lang_inc_exc,
            $strategy_langs,
            $geofencing_inc_exc,
            $strategy_geofencing,
            $sitelists_inc_exc,
            implode(',',$selected_sitelists),
            $iplists_inc_exc,
            implode(',',$selected_iplists),
            $strategy_pmps,
            $pmps_open_market,
            $ssps_inc_exc,
            $strategy_ssps,
            $ziplists_inc_exc,
            implode(',',$selected_ziplists),
            $keywordslists_inc_exc,
            implode(',',$selected_keywordslists),
            $devices_inc_exc,
            $strategy_devices,
            $itypes_inc_exc,
            $strategy_itypes,
            $isps_inc_exc,
            $selected_isps,
            $oss_inc_exc,
            $strategy_oss,
            $browsers_inc_exc,
            $strategy_browsers,
            $pixels_inc_exc,
            $strategy_pixels,
            $custom_datas_inc_exc,
            $strategy_custom_datas,
            $segments_inc_exc,
            $segments
        ));
        
    }
}
