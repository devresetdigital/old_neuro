<?php

namespace App\Http\Controllers;

use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index() {
        return Voyager::view('voyager::appdomainquality');
    }

    public function appDomainQualityTable(Request $request){
        if($request->get('from')!=""){
            $rfrom = explode("-",$request->get('from'));
        }
        if($request->get('until')!=""){
            $runtil = explode("-",$request->get('until'));
        }

        $channels = $request->get('channels') !="**" ?  $request->get('channels') : "";
        $medias = $request->get('medias') !="**" ?  $request->get('medias') : "";
        $oss = $request->get('oss') !="**" ?  $request->get('oss') : "";
        $publishers = $request->get('publishers') !="**" ?  $request->get('publishers') : "";
        $domains = $request->get('domains') !="**" ?  $request->get('domains') : "";
        $devices = $request->get('devices') !="**" ?  $request->get('devices') : "";
        $ssps = $request->get('ssps') !="**" ?  $request->get('ssps') : "";

        $from = $request->get('from') ? substr($rfrom[0],2,2).$rfrom[1].$rfrom[2]."00" : Carbon::now()->subDays(2)->format("ymdH");
        $until = $request->get('until') ? substr($runtil[0],2,2).$runtil[1].$runtil[2]."23" : Carbon::now()->subDays(1)->format("ymdH");
        
        //GROUPBY
        $groupby = $request->get('groupby') ? rtrim($request->get('groupby'),',') : "key";

        $start = $request->get('start');
        $length = $request->get('length');

        $pixelid = $request->get('pixelid') + ($_ENV['WL_PREFIX'] * 1000000);

        $orderby = $request->get('order');
    
        $url ="http://167.71.174.130:8080/Quality?".
        "groupby=" . $groupby . "&from=" . $from . "&until=" . $until . "&orderby=" . $orderby[0]["column"] .
        "&format=json" .
        "&channels=" . $channels . 
        "&medias=" . $medias . 
        "&oss=" . $oss ."&ssps=" . $ssps . 
        "&publishers=" . $publishers . 
        "&domains=" . $domains ."&devices=" . $devices ."&NOCACHE=1" ;

        if($length != ''){
            $url .="&paging=" . $length . "," . $start;
        }
       
        $report_json = file_get_contents($url);

        $report_json = json_decode($report_json) ?  json_decode($report_json) :[];

        $data = [];
        
        $groupby_ar = explode(',' , $groupby);
        foreach ($report_json as $key => $value) {
            $keys_aux = explode(',' , $key);
            $aux = [];
            foreach ($groupby_ar as $index => $group) {
                array_push($aux,$keys_aux[$index]);
            }
            $count = intval($value[6]) != 0 ? intval($value[6]) : 1;

            array_push($aux,number_format(floatval($value[0])*100/$count,2) ."%");
            array_push($aux," $".number_format(floatval($value[1])/$count,2));
            array_push($aux,number_format(floatval($value[2])*100/$count,2)."%");
            array_push($aux,number_format(floatval($value[3])/$count,2)."s");
            array_push($aux,number_format(floatval($value[4])*100/$count,2)."%");
            array_push($aux,number_format(floatval($value[5])*100/$count,2)."%");

            $count = intval($value[8]) != 0 ? intval($value[8]) : 1;

            array_push($aux,number_format(floatval($value[9])*100/$count,2)."%");
            array_push($aux,number_format(floatval($value[10])*100/$count,2)."%");
            array_push($aux,number_format(floatval($value[11])*100/$count,2)."%");
            array_push($aux,number_format(floatval($value[12])*100/$count,2)."%");
            array_push($aux,number_format(floatval($value[13])*100/$count,2)."%");
            array_push($aux,number_format(floatval($value[14])*100/$count,2)."%");
            $data[] = $aux;
        }

        $url ="http://167.71.174.130:8080/Quality?".
        "groupby=" . $groupby . "&from=" . $from . "&until=" . $until .
        "&format=details" .
        "&channels=" . $channels . 
        "&medias=" . $medias . 
        "&oss=" . $oss ."&ssps=" . $ssps . 
        "&publishers=" . $publishers . 
        "&domains=" . $domains ."&devices=" . $devices ."&NOCACHE=1" ;
        
        if($length != ''){
            $url .="&paging=" . $length . "," . $start;
        }

        $report_json = file_get_contents($url);

        $report_json = json_decode($report_json);
  
        $response = array("draw" => intval($request->input('draw')),  
        "recordsTotal"=> $report_json->totals->items,
        "recordsFiltered"=> $report_json->totals->items,
        "data"=> $data);
        return response()->json($response,200);    


    
    }

}
