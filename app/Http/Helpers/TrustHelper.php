<?php

namespace App\Http\Helpers;

use App\Creative;
use App\Concept;
use App\Strategy;
use App\Campaign;
use App\TrustScan;
use App\CreativeDisplay;
use App\CreativeVideo;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Log;

class TrustHelper
{

    static $params = array(
        'key' => 'b3369f8070b80bea3154ef89b71d14a',
        'app' => 'rb336',
        'base_url' =>'https://rb336.api.themediatrust.com/v2/?key=b3369f8070b80bea3154ef89b71d14a'
    );


    static public function SendForScaning(int $id){

        if($_ENV['ENABLE_TMT_SCAN'] != 1){
            return true;
        }
        
        try {
            $ad_ID = $id + ($_ENV['WL_PREFIX'] * 1000000);

            //self::deleteTag($ad_ID);

            $model = Creative::find($id);
    
            $url = self::$params['base_url'] . '&action=JSON_tag_import';
    
            $ch = curl_init();
    
            $base64 = self::getTagBase64($ad_ID, $model->creative_type_id);

            $name = $ad_ID ." - " . $model->name;
    
            $raw = '{
                "enabled_tags" :[
                    {
                    "tagName": "'.$name.'", 
                    "creativeBase64":"'.$base64.'", 
                    "adTagId": "'.$ad_ID.'",
                    "per_day": 5,
                    "country": "USA",
                    "runXnow": "10"
                    }
                ]
            }';
            curl_setopt($ch, CURLOPT_URL,            $url );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt($ch, CURLOPT_POST,           1 );
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $raw ); 
            curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json')); 
    
            $result=curl_exec ($ch);
    
            $response = json_decode($result,true);
    
            if(array_key_exists($model->name,$response)){
    
                TrustScan::where('creative_id', $id)->delete();
    
                $saveScan = new TrustScan();
                $saveScan->provider = 'TMT';
                $saveScan->status = 'LIVE';
                $saveScan->last_scan = Carbon::now();
                $saveScan->creative_id = $id;
                $saveScan->save();
    
                return true;
            }
    
          
        } catch (\Throwable $th) {
        
        }

        TrustScan::where('creative_id', $id)->delete();
    
        $saveScan = new TrustScan();
        $saveScan->provider = 'TMT';
        $saveScan->status = 'ERROR';
        $saveScan->last_scan = Carbon::now();
        $saveScan->creative_id = $id;
        $saveScan->save();
        
        return false;
                
    }

    static public function SendForScaningOnBulk(array $ids){
    
        if($_ENV['ENABLE_TMT_SCAN'] != 1){
            return true;
        }

        try {

            if($ids==[]){
                return  [
                    'error'=>false,
                    'message'=>'Nothing to send by now'
                ];
            }

            $list_ids = [];
            $ads_keys= [];
            $raw = '{
                "enabled_tags" :[ ';

            $ad_sended=[];    
            
            foreach($ids as $id){
                $model = Creative::where('id',$id)->first();
         
                if($model == null){
                    continue;
                }

                $ad_ID = $model->id + ($_ENV['WL_PREFIX'] * 1000000);

                $ads_keys[$model->name]= $model->id;

                $list_ids[]=$ad_ID;
               
                $base64 = self::getTagBase64($ad_ID, $model->creative_type_id);

                $name = $ad_ID ." - " . $model->name;

                $raw .= '{
                        "tagName": "'.$name.'", 
                        "creativeBase64":"'.$base64.'", 
                        "adTagId": "'.$ad_ID.'",
                        "per_day": 5,
                        "country": "USA",
                        "runXnow": "10"
                        },';
            }
         

            $raw = substr($raw, 0, -1);
        
            $list_ids = implode(',',$list_ids);

            $raw .= ']}';

            if(strpos($raw, '[]')!=false){
                return  [
                    'error'=>true,
                    'message'=>'no creatives found'
                ];
            }

         

            $url = self::$params['base_url'] . '&action=JSON_tag_import';
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,            $url );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt($ch, CURLOPT_POST,           1 );
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $raw ); 
            curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json')); 
    
            $result=curl_exec ($ch);
    
            $response = json_decode($result,true);

            foreach($response as $key => $ad){
                if (is_array($ad)) {
                    $id= intval($ad["id"]) - ($_ENV['WL_PREFIX'] * 1000000);

                    $ad_sended[]=$id;

                    TrustScan::where('creative_id',$id )->delete();
                    $saveScan = new TrustScan();
                    $saveScan->provider = 'TMT';
                    $saveScan->status = 'LIVE';
                    $saveScan->last_scan = Carbon::now();
                    $saveScan->creative_id = $id;
                    $saveScan->save();

                    Creative::updateTimestamp($id);
        
                } else {
               
                    TrustScan::where('creative_id', $id)->delete();

                    $saveScan = new TrustScan();
                    $saveScan->provider = 'TMT';
                    $saveScan->status = 'ERROR';
                    $saveScan->last_scan = Carbon::now();
                    $saveScan->creative_id = $ads_keys[$key];
                    $saveScan->save();

                    Creative::updateTimestamp($saveScan->creative_id);
                }
            }

            return  [
                'error'=>false,
                'message'=>'creatives ids: '.implode(', ', $ad_sended).' were sent for scanning' 
            ];
           
        } catch (\Throwable $th) {
            dd($th);
        }

        return  [
            'error'=>true,
            'message'=>'exception occurred' 
        ];
      
    }
    
    
    /**
     * get status and scan id
     */
    static public function getStatus(int  $id){
        if($_ENV['ENABLE_TMT_SCAN'] != 1){
            return true;
        }

        $ad_ID = $id + ($_ENV['WL_PREFIX'] * 1000000);
        
        $url = self::$params['base_url'] . '&action=tag_scan_check&adTagIds='.$ad_ID.'&csid=true&malware=true&policy_violations=true';

        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true)
        ));
        
        $result = file_get_contents($url, false, $context);

        $response = json_decode($result,true);
     
        if(array_key_exists('errors', $response)){
            return false;
        }

        return $response;
    }
    /**
     * get a list of incidents
     */
    static public function getIncidentList(){

        if($_ENV['ENABLE_TMT_SCAN'] != 1){
            return true;
        }

        $url = self::$params['base_url'] . '&action=tag_incident_summary&hours=48';

        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));
        
        $result = file_get_contents($url, false, $context);

        $response = json_decode($result,true);

        return $response;
    }
    /**
     * pause the scans
     */
    static public function pauseTag($ids){

        if($_ENV['ENABLE_TMT_SCAN'] != 1){
            return true;
        }

        if($ids == ""){
            return true;
        }

        $url = self::$params['base_url'] . '&action=tag_pause&adTagIds='.$ids;
     
        $response = json_decode(file_get_contents($url),true);

        if(array_key_exists('SUCCESS',$response)){
            foreach ($response['SUCCESS'] as $key => $id) {

                $noPrefixed = $id - ($_ENV['WL_PREFIX'] * 1000000);

                $saveScan = TrustScan::where('creative_id', $noPrefixed)->first();

                if($saveScan!= null){
                    $saveScan->status = 'PAUSED';
                    $saveScan->save();
                }

                Creative::updateTimestamp($noPrefixed);
                

            }
        }

        if(array_key_exists('FAILED',$response)){
            if($_ENV['ENABLE_TMT_SCAN'] != 1){
                return true;
            }

            foreach ($response['FAILED'] as $key => $id) {

                $aux_id = substr($id,0,7);
           
                $noPrefixed = $aux_id - ($_ENV['WL_PREFIX'] * 1000000);
    
                $saveScan = TrustScan::where('creative_id', $noPrefixed)->first();
    
                if($saveScan!= null){
                    $saveScan->status = 'ERROR';
                    $saveScan->save();
                }
    
                Creative::updateTimestamp($noPrefixed);
    
            }
        }
       
        return true;

    }
    /**
     * start scaning again
     */
    static public function resumeTag($ids){

        if($_ENV['ENABLE_TMT_SCAN'] != 1){
            return true;
        }

        if($ids == ""){
            return true;
        }

        $url = self::$params['base_url'] . '&action=tag_resume&adTagIds='.$ids;

        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));
        
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result,true);


        if(array_key_exists('SUCCESS',$response)){
            foreach ($response['SUCCESS'] as $key => $id) {

                $noPrefixed = $id - ($_ENV['WL_PREFIX'] * 1000000);

                $saveScan = TrustScan::where('creative_id', $noPrefixed)->first();

                if($saveScan!= null){
                    $saveScan->status = 'LIVE';
                    $saveScan->save();
                }

                Creative::updateTimestamp($noPrefixed);
                

            }
        }

        if(array_key_exists('FAILED',$response)){
            foreach ($response['FAILED'] as $key => $id) {

                $aux_id = substr($id,0,7);
                $noPrefixed = $aux_id - ($_ENV['WL_PREFIX'] * 1000000);
    
                $saveScan = TrustScan::where('creative_id', $noPrefixed)->first();
    
                if($saveScan!= null){
                    $saveScan->status = 'ERROR';
                    $saveScan->save();
                }
    
                Creative::updateTimestamp($noPrefixed);
    
            }
        }
   
        return true;

    }
    /**
     * delete a tag 
     */
    static public function deleteTag($ad_ID){

        if($_ENV['ENABLE_TMT_SCAN'] != 1){
            return true;
        }

        $forDelete = [];
       
        foreach(explode(",",$ad_ID) as $id){
         
            $noPrefixed = $id - ($_ENV['WL_PREFIX'] * 1000000);
            Creative::updateTimestamp($noPrefixed);
            $forDelete[]= $noPrefixed;
        }

        TrustScan::whereIn('creative_id', $forDelete)->delete();

        $url = self::$params['base_url'] . '&action=tag_remove&adTagIds='.$ad_ID;

        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));
        
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result,true);

        if(array_key_exists('SUCCESS', $response)){
            return true;
        }
        return false;
    }


    /**
     * 
     * AUXILIAR function
     * 
     */

    static function getTagBase64($creative_id, $type){
        if ($type == 1){
            return base64_encode('http://e-us-east01.resetdigital.co:81/preview?'.$creative_id);
        }else{
            try {

                $campaign_id = intval($_ENV['TMT_CAMPAIGN']) + ($_ENV['WL_PREFIX'] * 1000000);
                $strategy_id = intval($_ENV['TMT_STRATEGY']) + ($_ENV['WL_PREFIX'] * 1000000);

                $context = stream_context_create(array(
                    'http' => array('ignore_errors' => true)
                ));
                $url = 'https://data.resetdigital.co/evts?S0B=1&R0E=1&R0M=10_15&R0A='.$campaign_id.'_'.$creative_id.'_'.$strategy_id.'_1627360746&R0P=resetio_12345678_TEST.COM_SITE_*_banner&R0L=*_*_*_*_*&R0D=*_*_*_*_*_*&R0B=*_*_*';

                $result = file_get_contents($url, false, $context);
                return base64_encode($result);
            } catch (\Throwable $th) {
                return null;
            }
         
        }
        
    }
 
}
