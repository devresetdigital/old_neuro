<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\ConversionPixel;
use App\Campaign;
use App\Strategy;
use App\Creative;

class InventoryReportsController extends Controller
{
    public function index(Request $request){
        //die();
        //GET DATES
        //format from until
        if($request->get('from')!=""){
            $rfrom = explode("-",$request->get('from'));
        }
        if($request->get('until')!=""){
            $runtil = explode("-",$request->get('until'));
        }
        $from = $request->get('from') ? substr($rfrom[0],2,2).$rfrom[1].$rfrom[2]."00" : Carbon::now()->subDays(1)->format("ymdH");
        $until = $request->get('from') ? substr($runtil[0],2,2).$runtil[1].$runtil[2]."23" : Carbon::now()->format("ymdH");
        //GROUPBY
        $groupby = $request->get('groupby') ? rtrim($request->get('groupby'),',') : "domain_3";

        //FILTERS
        $country = $request->get('countries') ? rtrim($request->get('countries'),',') : "";
        $channel = $request->get('channels') ? rtrim($request->get('channels'),',') : "";
        $media = $request->get('media') ? rtrim($request->get('media'),',') : "";
        $domain = $request->get('domains') ? "*_".rtrim($request->get('domains'),',') : "";
        $size = $request->get('sizes') ? rtrim($request->get('sizes'),',') : "";
        $region = $request->get('regions') ? rtrim($request->get('regions'),',') : "";
        $city = $request->get('cities') ? "*_".rtrim($request->get('cities'),',') : "";



        if($groupby == "datetime" || $groupby == "date"){ $orderby=0; } else { $orderby=1; }

        //die("http://e-us-east01.resetdigital.co:8080/0_impressions?groupby=".$groupby."&from=".$from."&until=".$until."&format=json&".$filters);
        $url= "http://e-us-east01.resetdigital.co:8080/Opportunities?groupby=".$groupby."&from=".$from."&until=".$until."&format=json&orderby=".$orderby."&countries=".$country."&channels=".$channel."&medias=".$media."&domains=".$domain."&sizes=".$size."&regions=".$region."&cities=".$city;
        echo $url;
        die();
        //die("http://e-us-east01.resetdigital.co:8080/Opportunities?groupby=".$groupby."&from=".$from."&until=".$until."&format=json&orderby=".$orderby."&countries=".$country."&channels=".$channel."&medias=".$media."&domains=".$domain."&sizes=".$size."&regions=".$region."&cities=".$city);
        //if(isset($_POST["debug"])){
           // die($url);
       // }
        //kubient east04

        //GET REPORT CONTENT BY DATE
        $report_json = file_get_contents($url);



        $reports = "{\"data\": [";
        $nrecord = 1;
        foreach(json_decode($report_json,true) as $key => $val){
            //IF GROUPBY DATE FORMAT DATE
            if($groupby == "datetime") {
                $datey = substr($key, 0, 2);
                $datem = substr($key, 2, 2);
                $dated = substr($key, 4, 2);
                $dateh = substr($key, 6, 2);
                $key=$dateh;
            }
            if($groupby == "date") {
                $datey = substr($key, 0, 2);
                $datem = substr($key, 2, 2);
                $dated = substr($key, 4, 2);
                $dateh = substr($key, 6, 2);
                $key=$datem."-".$dated."-".$datey;
            }
            if($groupby != "date" && $groupby != "datetime") {
                $formated_keys = "";
                $fkeys = explode(",", $key);
                foreach ($fkeys as $k => $v) {
                    if($groupby=="domain"){
                        $vg = explode("_",$v);
                        $v= $vg[2];
                    }
                    if($groupby=="os"){
                        $vg = explode("_",$v);
                        $v= $vg[0];
                    }
                    if($groupby=="city"){
                        $vg = explode("_",$v);
                        $v= $vg[1];
                    }
                    $formated_keys .= "<div style='width: 200px; float: left; margin-right: 30px; word-wrap: break-word;'>" . $v . "</div>";
                }
            } else {
                $formated_keys = $key;
            }

                $reports .= "{\"Data\" : \"$formated_keys\",";
                $reports .= "\"Requests\" : \"$val[0]\"},";
        }
        $reports.="]}.";

        return str_replace(array(",]}.","]}."),array("]}","]}"),$reports);

    }

    public function pixels(Request $request){
        //GET DATES
        //format from until
        if($request->get('from')!=""){
            $rfrom = explode("-",$request->get('from'));
        }
        if($request->get('until')!=""){
            $runtil = explode("-",$request->get('until'));
        }

        $from = $request->get('from') ? substr($rfrom[0],2,2).$rfrom[1].$rfrom[2]."00" : Carbon::now()->subDays(2)->format("ymdH");
        $until = $request->get('from') ? substr($runtil[0],2,2).$runtil[1].$runtil[2]."23" : Carbon::now()->subDays(1)->format("ymdH");
        //GROUPBY
        $groupby = $request->get('groupby') ? rtrim($request->get('groupby'),',') : "key";

        //FILTERS
        $country = $request->get('countries') ? rtrim($request->get('countries'),',') : "";
        $city = $request->get('cities') ? rtrim($request->get('cities'),',') : "";
        $region = $request->get('regions') ? rtrim($request->get('regions'),',') : "";
        $domain = $request->get('domains') ? rtrim($request->get('domains'),',') : "";
        $title = $request->get('titles') ? rtrim($request->get('titles'),',') : "";
        $device = $request->get('devices') ? rtrim($request->get('devices'),',') : "";
        $os = $request->get('oss') ? rtrim($request->get('oss'),',') : "";
        $browser = $request->get('browsers') ? rtrim($request->get('browsers'),',') : "";
        $key = $request->get('keys') ? rtrim($request->get('keys'),',') : "";

        $request->get('pixelid') ? $pixelid=$_ENV['WL_PREFIX'].$request->get('pixelid') : $pixelid = "";

        //die("http://e-us-east01.resetdigital.co:8080/0_impressions?groupby=".$groupby."&from=".$from."&until=".$until."&format=json&".$filters);

        if($groupby == "datetime" || $groupby == "date"){ $orderby=0; } else { $orderby=1; }

        if($pixelid=="") {
            $url = "http://45.35.192.162:8080/Pixels?groupby=" . $groupby . "&from=" . $from . "&until=" . $until . "&format=json&orderby=" . $orderby . "&countries=" . $country . "&cities=" . $city . "&devices=" . $device . "&oss=" . $os . "&browsers=" . $browser . "&keys=" . $key;
        } else {
            $url = "http://45.35.192.162:8080/smart_2_".$pixelid."?groupby=" . $groupby . "&from=" . $from . "&until=" . $until . "&format=json&orderby=" . $orderby . "&countries=" . $country . "&cities=" . $city . "&devices=" . $device . "&oss=" . $os . "&browsers=" . $browser . "&keys=" . $key. "&domains=" . $domain. "&regions=" . $region. "&titles=" . $title;
           // die($url);
        }
       // return $url;
        //GET REPORT CONTENT BY DATE
        $report_json = @file_get_contents($url);

        //return $report_json;

        $reports = "{\"data\": [";
        $nrecord = 1;
        if(is_array(json_decode($report_json, true))) {
            foreach (json_decode($report_json, true) as $key => $val) {
                //IF GROUPBY DATE FORMAT DATE
                if ($groupby == "datetime") {
                    $datey = substr($key, 0, 2);
                    $datem = substr($key, 2, 2);
                    $dated = substr($key, 4, 2);
                    $dateh = substr($key, 6, 2);
                    $key = $dateh;
                }
                if ($groupby == "date") {
                    $datey = substr($key, 0, 2);
                    $datem = substr($key, 2, 2);
                    $dated = substr($key, 4, 2);
                    $dateh = substr($key, 6, 2);
                    $key = $datem . "-" . $dated . "-" . $datey;
                }
                if ($groupby != "date" && $groupby != "datetime") {
                    $formated_keys = "";
                    $fkeys = explode(",", $key);
                    foreach ($fkeys as $k => $v) {
                        $formated_keys .= "<div style='width: 200px; float: left; margin-right: 30px; word-wrap: break-word;'>" . $v . "</div>";
                    }
                } else {
                    $formated_keys = $key;
                }

                $reports .= "{\"Data\" : \"$formated_keys\",";
                $reports .= "\"Views\" : \"$val[1]\"},";
            }
            $reports .= "]}.";
            return str_replace(array(",]}.","]}."),array("]}","]}"),$reports);
        } else {
            return "{\"data\": []}";
        }

    }

    public function pixelsAnalyticsReport(Request $request){

        try {
            if($request->get('from')!=""){
                $rfrom = explode("-",$request->get('from'));
            }
            if($request->get('until')!=""){
                $runtil = explode("-",$request->get('until'));
            }
    
            $countries = $request->get('countries') !="**" ?  $request->get('countries') : "";
    
            $regions = $request->get('regions');
            $cities = $request->get('cities');
    
            $devices = $request->get('devices');
            $oss = $request->get('oss');
            $browsers = $request->get('browsers');
           
            $titles = $request->get('titles') !="**" ? $request->get('titles')  : "";
            $domains = $request->get('domains') !="**" ? $request->get('domains')  : "";
            $keys = $request->get('keys') !="**" ? $request->get('keys')  : "";
    
            $from = $request->get('from') ? substr($rfrom[0],2,2).$rfrom[1].$rfrom[2]."00" : Carbon::now()->subDays(2)->format("ymdH");
            $until = $request->get('from') ? substr($runtil[0],2,2).$runtil[1].$runtil[2]."23" : Carbon::now()->subDays(1)->format("ymdH");
            //GROUPBY
            $groupby = $request->get('groupby') ? rtrim($request->get('groupby'),',') : "key";
    
            $pixelid = $request->get('pixelid') + ($_ENV['WL_PREFIX'] * 1000000);
    
            if($groupby == "datetime" || $groupby == "date"){ $orderby=0; } else { $orderby=1; }
    
    
            $url = "http://45.35.192.162:8080/pixelhits_".$pixelid."?groupby=" . $groupby .
             "&from=" . $from . "&until=" . $until . 
             "&format=json&orderby=" . $orderby . "&countries=" . $countries . 
             "&regioncities_1=" . $regions . 
             "&regioncities_2=" . $cities . 
             "&deviceosbrowsers_1=" . $devices . 
             "&deviceosbrowsers_2=" . $oss . 
             "&deviceosbrowsers_3=" . $browsers . 
             "&titles=" . $titles . "&domains=" . $domains . "&keys=" . $keys;
    
            $report_json = file_get_contents($url);
        } catch (\Throwable $th) {
            $report_json = []; 
        }
        return $report_json;
  
    }


    public function pixelsAnalyticsReportTable(Request $request){

        try {

            if($request->get('from')!=""){
                $rfrom = explode("-",$request->get('from'));
            }
            if($request->get('until')!=""){
                $runtil = explode("-",$request->get('until'));
            }
    
            $countries = $request->get('countries') !="**" ?  $request->get('countries') : "";
           
            $regions = $request->get('regions');
            $cities = $request->get('cities');
    
            $devices = $request->get('devices');
            $oss = $request->get('oss');
            $browsers = $request->get('browsers');
    
            $hours = $request->get('hours') == '' ? '' : implode(',', $request->get('hours'));
           
            $titles = $request->get('titles') !="**" ? $request->get('titles')  : "";
            $domains = $request->get('domains') !="**" ? $request->get('domains')  : "";
            $keys = $request->get('keys') !="**" ? $request->get('keys')  : "";
    
            $from = $request->get('from') ? substr($rfrom[0],2,2).$rfrom[1].$rfrom[2]."00" : Carbon::now()->subDays(2)->format("ymdH");
            $until = $request->get('from') ? substr($runtil[0],2,2).$runtil[1].$runtil[2]."23" : Carbon::now()->subDays(1)->format("ymdH");
            //GROUPBY
            $groupby = $request->get('groupby') ? rtrim($request->get('groupby'),',') : "key";
    
            $groupby_ar = explode(',' , $groupby);
    
            $orderby = $request->get('order')[0]['column'];
          
            if($orderby > count($groupby_ar)){
                $orderby = $orderby + 2;
            }
    
            $start = $request->get('start');
            $length = $request->get('length');
    
            $pixelid = $request->get('pixelid') + ($_ENV['WL_PREFIX'] * 1000000);
            $conversionPixelPrefixed = $request->get('pixelid') + ($_ENV['WL_PREFIX'] * 1000000);
            
            $url = "http://45.35.192.162:8080/pixelhits_".$pixelid."?groupby=" . $groupby . "&from=" . $from . "&until=" . $until . 
            "&format=json&orderby=" . $orderby . "&id=" . $pixelid . "&countries=" . $countries .
            "&regioncities_1=" . $regions . 
            "&regioncities_2=" . $cities . 
            "&deviceosbrowsers_1=" . $devices . 
            "&deviceosbrowsers_2=" . $oss . 
            "&deviceosbrowsers_3=" . $browsers .  
            "&hours=" . $hours .  
            "&titles=" . $titles . "&domains=" . $domains .
            "&keys=" . $keys ; 

            if($length != ''){
                $url .="&paging=" . $length . "," . $start;
            }
          
            $report_json = file_get_contents($url);
    
            $report_json = json_decode($report_json) ?  json_decode($report_json) :[];
         
            $data = [];
         
           
            foreach ($report_json as $key => $value) {
                $keys_aux = explode(',' , $key);
                $aux = [];
                foreach ($groupby_ar as $index => $group) {
    
                    switch ($group) {
                        case 'date':
                            if(array_key_exists($index,$keys_aux)){
                                $label =   substr($keys_aux[$index], 2, 2) .'-'. substr($keys_aux[$index], 4, 2) .'-'. substr($keys_aux[$index], 0, 2);
                            } else {
                              $label ='**';
                            }
                            array_push($aux,$label);
                        break;
                        case 'datetime':
                            if(array_key_exists($index,$keys_aux)){
                                $label =  substr($keys_aux[$index], 2, 2) .'-'. substr($keys_aux[$index], 4, 2) .'-'. substr($keys_aux[$index], 0, 2) .'  '. substr($keys_aux[$index], 6, 2) .' hs';
                            } else {
                              $label ='**';
                            }
                            array_push($aux,$label);
                        break;
                        
                        default:
                            if(array_key_exists($index,$keys_aux)){
                                array_push($aux,$keys_aux[$index]);
                            }else{
                                array_push($aux,'**');
                            }
                            break;
                    }
                 
                }
                $hits = intval($value[0]);
                $uniques =  $hits > 0 ? $value[1] . " (". round(intval($value[1] ) * 100 / intval($value[0]), 2) ." %)"  : 0;
                array_push($aux,$hits);
                array_push($aux, $uniques);
                $data[] = $aux;
            }
    
            $url = "http://45.35.192.162:8080/pixelhits_".$pixelid."?groupby=" . $groupby . "&from=" . $from . "&until=" . $until 
            . "&format=details&orderby=" . $orderby . "&id=" . $pixelid . "&countries=" . $countries . 
            "&regioncities_1=" . $regions . 
            "&regioncities_2=" . $cities . 
            "&deviceosbrowsers_1=" . $devices . 
            "&deviceosbrowsers_2=" . $oss . 
            "&deviceosbrowsers_3=" . $browsers .  
            "&hours=" . $hours .  
            "&titles=" . $titles 
            . "&domains=" . $domains .  "&keys=" . $keys ; 

            if($length != ''){
                $url .="&paging=" . $length . "," . $start;
            }
          
            $report_json = file_get_contents($url);
    
            $report_json = json_decode($report_json);
    
            $count = count($groupby_ar)+1;
            $hits = intval($report_json->totals->sums[0]);
            $uniques =  $hits > 0 ? $report_json->totals->sums[1] . " (". round(intval($report_json->totals->sums[1] ) * 100 / intval($report_json->totals->sums[0]), 2) ." %)"  : 0;
            $aux =  array_fill(0, $count, 'Totals');
           
            $aux[$count-1]=$hits;
            $aux[$count]=$uniques;
    
            $data[]=$aux;

            $response = array("draw" => intval($request->input('draw')),  
            "recordsTotal"=> $report_json->totals->items,
            "recordsFiltered"=> $report_json->totals->items,
            "data"=> $data);
        } catch (\Throwable $th) {
            $response = array("draw" => 0,  
            "recordsTotal"=> 0,
            "recordsFiltered"=> 0,
            "data"=> []);
        }
        
        return response()->json($response,200);    

    }

    public function pixelsConversionReport(Request $request){
        try {
            if($request->get('from')!=""){
                $rfrom = explode("-",$request->get('from'));
            }
            if($request->get('until')!=""){
                $runtil = explode("-",$request->get('until'));
            }
            $countries = $request->get('countries') !="**" ?  $request->get('countries') : "";
            $regions = $request->get('regions');
            $cities = $request->get('cities');
    
            $devices = $request->get('devices');
            $oss = $request->get('oss');
            $browsers = $request->get('browsers');
            $type = $request->get('type') == '' ? '' : implode(',', $request->get('type'));
            $titles = $request->get('titles') !="**" ? $request->get('titles')  : "";
            $domains = $request->get('domains') !="**" ? $request->get('domains')  : "";
            $hours = $request->get('hours') == '' ? '' : implode(',', $request->get('hours'));
    
            $campaigns = $request->get('campaigns') !="**" ? $request->get('campaigns')  : "";
            $strategies = $request->get('strategies') !="**" ? $request->get('strategies')  : "";
            $creatives = $request->get('creatives') !="**" ? $request->get('creatives')  : "";
      
    
            $from = $request->get('from') ? substr($rfrom[0],2,2).$rfrom[1].$rfrom[2]."00" : Carbon::now()->subDays(2)->format("ymdH");
            $until = $request->get('until') ? substr($runtil[0],2,2).$runtil[1].$runtil[2]."23" : Carbon::now()->subDays(1)->format("ymdH");
            //GROUPBY
            $groupby = $request->get('groupby') ? rtrim($request->get('groupby'),',') : "key";
    
            $model = ConversionPixel::where('id',$request->get('pixelid'))->first();
            $pixelid = $model->id + ($_ENV['WL_PREFIX'] * 1000000);

            if($groupby == "datetime" || $groupby == "date"){ $orderby=0; } else { $orderby=1; }
            
            $conversionPixelPrefixed = $request->get('pixelid') + ($_ENV['WL_PREFIX'] * 1000000);
            $url = "http://167.71.174.130:8080/pixelconversion_" . $pixelid . "?groupby=" . $groupby . 
            "&from=" . $from . "&until=" . $until . "&ids=" .  $conversionPixelPrefixed .
            "&format=json&orderby=" . $orderby . "&countries=" . $countries . 
            "&regioncities_1=" . $regions . 
            "&regioncities_2=" . $cities . 
            "&deviceosbrowsers_1=" . $devices . 
            "&deviceosbrowsers_2=" . $oss . 
            "&deviceosbrowsers_3=" . $browsers .  
            "&hours=" . $hours .  
            "&types=" . $type .  
            "&campstrat_1=" . $campaigns . 
            "&campstrat_2=" . $strategies . 
            "&campstrat_3=" . $creatives . 
            "&titles=" . $titles . "&domains=" . $domains ;
    
            $report_json = file_get_contents($url);
        } catch (\Throwable $th) {
            return [$th->getMessage(),$th->getTrace()];
            $report_json=[];
        }
        

        return $report_json;
  
    }


    public function pixelsConversionReportTable(Request $request){
        try {
            if($request->get('from')!=""){
                $rfrom = explode("-",$request->get('from'));
            }
            if($request->get('until')!=""){
                $runtil = explode("-",$request->get('until'));
            }
            $countries = $request->get('countries') !="**" ?  $request->get('countries') : "";
            $regions = $request->get('regions');
            $cities = $request->get('cities');
    
            $devices = $request->get('devices');
            $oss = $request->get('oss');
            $browsers = $request->get('browsers');
    
            $titles = $request->get('titles') !="**" ? $request->get('titles')  : "";
            $domains = $request->get('domains') !="**" ? $request->get('domains')  : "";
            $campaigns = $request->get('campaigns') !="**" ? $request->get('campaigns')  : "";
            $strategies = $request->get('strategies') !="**" ? $request->get('strategies')  : "";
            $creatives = $request->get('creatives') !="**" ? $request->get('creatives')  : "";
      
            $hours = $request->get('hours') == '' ? '' : implode(',', $request->get('hours'));
            $type = $request->get('type') == '' ? '' : implode(',', $request->get('type'));
            $from = $request->get('from') ? substr($rfrom[0],2,2).$rfrom[1].$rfrom[2]."00" : Carbon::now()->subDays(2)->format("ymdH");
            $until = $request->get('until') ? substr($runtil[0],2,2).$runtil[1].$runtil[2]."23" : Carbon::now()->subDays(1)->format("ymdH");
            //GROUPBY
            $groupby = $request->get('groupby') ? rtrim($request->get('groupby'),',') : "key";
            $groupby_ar = explode(',' , $groupby);
    
            $orderby = $request->get('order')[0]['column'];
    
            if($orderby > count($groupby_ar)+1){
                $orderby = $orderby + 2;
            }
      
            $start = $request->get('start');
            $length = $request->get('length');
         
            $model = ConversionPixel::where('id',$request->get('pixelid'))->first();
            $pixelid = $model->id + ($_ENV['WL_PREFIX'] * 1000000);

            $conversionPixelPrefixed = $request->get('pixelid') + ($_ENV['WL_PREFIX'] * 1000000);
    
            $url = "http://167.71.174.130:8080/pixelconversion_" . $pixelid . "?groupby=" . $groupby . 
            "&from=" . $from . "&until=" . $until . "&ids=" .  $conversionPixelPrefixed .
            "&format=json&orderby=" . $orderby . 
            "&id=" . $pixelid . "&countries=" . $countries .
            "&regioncities_1=" . $regions . 
            "&regioncities_2=" . $cities . 
            "&types=" . $type .  
            "&deviceosbrowsers_1=" . $devices . 
            "&deviceosbrowsers_2=" . $oss . 
            "&deviceosbrowsers_3=" . $browsers .  
            "&hours=" . $hours .  
            "&campstrat_1=" . $campaigns . 
            "&campstrat_2=" . $strategies . 
            "&campstrat_3=" . $creatives .   
            "&titles=" . $titles . 
            "&domains=" . $domains;
    

            if($length != ''){
                $url .="&paging=" . $length . "," . $start;
            }


            $report_json = file_get_contents($url);
    
            $report_json = json_decode($report_json) ?  json_decode($report_json) :[];
    
            $data = [];
          
            $campaigns_names = [];
            $strategies_names = [];
            $creatives_names = [];
           
            foreach ($report_json as $key => $value) {
                $keys_aux = explode(',' , $key);
                $aux = [];
               
                foreach ($groupby_ar as $index => $group) {
                    
                    switch ($group) {
                        case 'date':
                            if(array_key_exists($index,$keys_aux)){
                                $label =   substr($keys_aux[$index], 2, 2) .'-'. substr($keys_aux[$index], 4, 2) .'-'. substr($keys_aux[$index], 0, 2);
                            } else {
                              $label ='**';
                            }
                            array_push($aux,$label);
                        break;
                        case 'datetime':
                            if(array_key_exists($index,$keys_aux)){
                                $label =  substr($keys_aux[$index], 2, 2) .'-'. substr($keys_aux[$index], 4, 2) .'-'. substr($keys_aux[$index], 0, 2) .'  '. substr($keys_aux[$index], 6, 2) .' hs';
                            } else {
                              $label ='**';
                            }
                            array_push($aux,$label);
                        break;
                        case 'campstrat_1':
                            $val=$keys_aux[$index];
                            $id = intval($val) - ($_ENV["WL_PREFIX"] * 1000000);
                            if(!array_key_exists($val, $campaigns_names)){
                                $name = Campaign::where('id', $id )->select('name')->first();
                                $campaigns_names[$val] =  $name != null ? $name->name : '';
                            }
                            array_push($aux,"[".$val."] ". $campaigns_names[$val]);
                            break;
                        case 'campstrat_2':
                            $val=$keys_aux[$index];
                            $id = intval($val) - ($_ENV["WL_PREFIX"] * 1000000);
                            if(!array_key_exists($val, $strategies_names)){
                                $name = Strategy::where('id', $id )->select('name')->first();
                                $strategies_names[$val] =  $name != null ? $name->name : '';
                            }
                            array_push($aux,"[".$val."] ".$strategies_names[$val]);
                            break;
                        case 'campstrat_3':
                            $val=$keys_aux[$index];
                            $id = intval($val) - ($_ENV["WL_PREFIX"] * 1000000);
                            if(!array_key_exists($val, $creatives_names)){
                                $name = Creative::where('id', $id )->select('name')->first();
                                $creatives_names[$val] = $name != null ? $name->name : '';
                                
                            }

                            array_push($aux,"[".$val."] ".$creatives_names[$val]);

                            break;
                        
                        default:
                            if(array_key_exists($index,$keys_aux)){
                                array_push($aux,$keys_aux[$index]);
                            }else{
                                array_push($aux,'**');
                            }
                            
                            break;
                    }
                 
                }
                array_push($aux,$value[0]);
                array_push($aux,$value[1]);
                array_push($aux,$value[2]);
    
                $data[] = $aux;
            }
    
    
            $url = "http://167.71.174.130:8080/pixelconversion_" . $pixelid . "?groupby=" . $groupby . 
            "&from=" . $from . "&until=" . $until . "&ids=" .  $conversionPixelPrefixed .
            "&format=details" . 
            "&id=" . $pixelid . "&countries=" . $countries . 
            "&regioncities_1=" . $regions . 
            "&regioncities_2=" . $cities . 
            "&types=" . $type .  
            "&deviceosbrowsers_1=" . $devices . 
            "&deviceosbrowsers_2=" . $oss . 
            "&deviceosbrowsers_3=" . $browsers .  
            "&hours=" . $hours .  
            "&campstrat_1=" . $campaigns . 
            "&campstrat_2=" . $strategies . 
            "&creatives=" . $creatives .  
            "&titles=" . $titles . 
            "&domains=" . $domains;
          
            if($length != ''){
                $url .="&paging=" . $length . "," . $start;
            }

            $report_json = file_get_contents($url);
    
            $report_json = json_decode($report_json);
    
    
            $count = count($groupby_ar)+2;
    
    
      
            $aux =  array_fill(0, $count, 'Totals');
            
            $aux[$count-2]=$report_json->totals->sums[0];
            $aux[$count-1]=$report_json->totals->sums[1];
            $aux[$count]=$report_json->totals->sums[2];

            $data[]=$aux;

            $response = array("draw" => intval($request->input('draw')),  
                "recordsTotal"=> $report_json->totals->items,
                "recordsFiltered"=> $report_json->totals->items,
                "data"=> $data);
    
        } catch (\Throwable $th) {
            return [$th->getMessage(),$th->getTrace()];
            $response = array("draw" =>0,  
                "recordsTotal"=> 0,
                "recordsFiltered"=> 0,
                "data"=> []);
        }

        return response()->json($response,200);    

    }
}
