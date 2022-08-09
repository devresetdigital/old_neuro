<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignsBudgetFlight;
use App\Strategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Carbon;
use App\Http\Helpers\TrustHelper;
use App\Http\Helpers\MailerHelper;
use App\TrustScan;
use App\Creative;

class CronController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
   
        //$redis_values = Redis::get('cmp_1_spt')/1000000.", ".Redis::get('stg_1_spt')/1000000;

        $redis = Redis::connection('remote');

        //return $redis_values;
        //Prefix
        $float_wlprefix = $_ENV['WL_PREFIX'].".0";
        if((float)$float_wlprefix>0) {
            $wlprefix = (float)$float_wlprefix * 1000000;
        } else {
            $wlprefix =0;
        }

        //Subtract Redis Budget from Campaign Budget
        $campaigns = Campaign::all();
        $camp = "";
        foreach ($campaigns as $val){
            //$camp.= "cmp_".$val->id."_imp,";
            //$camp.= "cmp_".$val->id."_spt,";
            $idprefixed = $val->id+$wlprefix;
            
            $cmp_redis_imp = $redis->get("cmp_".$idprefixed."_imp");

            $cmp_redis_spt = $redis->get("cmp_".$idprefixed."_spt");

            $cmp_redis_spt > 0 ?  $cmp_redis_spt = $cmp_redis_spt/1000000 : $cmp_redis_spt = 0;

            $campaign_flight = CampaignsBudgetFlight::where("campaign_id" , "=", $val->id)
                ->whereDate('date_start','<',Carbon::now())
                ->whereDate('date_end','>',Carbon::now())
                ->first();
                
            if($campaign_flight) {
                $campaign_flight->budget = $campaign_flight->budget - $cmp_redis_spt;
                $campaign_flight->impression = $campaign_flight->impression - intval($cmp_redis_imp);
                $campaign_flight->save();
            }

            //DELETE CAMPAIGN DAILY DATA FROM REDIS
            $redis->DEL("cmp_".$idprefixed."_imp");
            $redis->DEL("cmp_".$idprefixed."_spt");

            //UPDATE CAMPAIGN UPDATED_AT
            $campaign = Campaign::find($val->id);
            $campaign->updated_at = Carbon::now();
            $campaign->save();

        }

        //Substract Redis Budget from Strategies
        $strategies = Strategy::all();
        $stg = "";
        foreach ($strategies as $val){
            //$stg.="stg_".$strategy->id."_imp,";
            //$stg.="stg_".$strategy->id."_spt,";

            $idprefixed = $val->id+$wlprefix;

            $stg_redis_imp = $redis->get("stg_".$idprefixed."_imp");
            $stg_redis_spt = $redis->get("stg_".$idprefixed."_spt");

            $stg_redis_spt > 0 ?  $stg_redis_spt = $stg_redis_spt/1000000 : $stg_redis_spt = 0;

            //DELETE DAILY STRATEGY DATA FROM REDIS
            $redis->DEL("stg_".$idprefixed."_imp");
            $redis->DEL("stg_".$idprefixed."_spt");

            //UPDATE STRATEGY
            $strategy = Strategy::find($val->id);
            $strategy->budget = $strategy->budget - $stg_redis_spt;
            $strategy->checked = 0;
            $strategy->save();

        }


        return $camp;

    }

    /**
     * enable or disable creatives according its activity
     */
    public function TrustScanDaily()
    {   

        try {
            $yesterday_date = date('ymd',strtotime("-1 days"));
    
            $from = $yesterday_date.'00';
            $until = $yesterday_date.'23';
    
            $url = $_ENV['WL_HB_DATA_URL']."?groupby=creative&NOCACHE=1&from=".$from."&until=". $until;

            $hb_request = file_get_contents($url);
            $hb_response = json_decode($hb_request,true);
    
    
            $live_content = TrustScan::where('status', 'LIVE')->get()->pluck('creative_id')->toArray();

            $for_pausing = [];
            $for_resumming = [];
        
            foreach ($hb_response as $key => $ad){
    
                $ad_ID = intval($key) - ($_ENV['WL_PREFIX'] * 1000000);
                if (($index = array_search(intval($ad_ID), $live_content)) !== false) {
                    unset($live_content[$index]);
                }
             
                if($ad[0] < 100){
                    $for_pausing[]=$key;
               
                }else{
                    $for_resumming[]=$key;
            
                }
    
            }

            foreach($live_content as $id){
                $ad_ID = intval($id) + ($_ENV['WL_PREFIX'] * 1000000);
                $for_pausing[]=$ad_ID;
            }

            $already_paused = TrustScan::where('status', 'PAUSED')->get()->pluck('creative_id')->toArray();

            $for_pausing = array_diff($for_pausing, $already_paused);

            $ids= array_chunk($for_pausing,200);
          
            foreach ($ids as $key => $pause) {
                $pause = implode(',', $pause);
                TrustHelper::pauseTag($pause);
                sleep(10);
            }

            $already_live = TrustScan::where('status', 'LIVE')->get()->pluck('creative_id')->toArray();

            $for_resumming = array_diff($for_resumming, $already_live);

            $ids= array_chunk($for_resumming,200);
        
            foreach ($ids as $key => $resume) {
                $resume = implode(',', $resume);
                TrustHelper::resumeTag($resume);
                sleep(10);
            }


        } catch (\Throwable $th) {
            dd($th);
        }
     
        return  [
            'error'=>false,
            'message'=>'success'
        ];
    }

    /**
     * send for scaning tags with status error and pending and check incidents
     */
    public function TrustScan(){

        $statuses = ['ERROR', 'PENDING'];

        $pendings = TrustScan::whereIn('status', $statuses)->get()->pluck('creative_id')->toArray();
        $pendings =array_values(array_unique($pendings));
        $send_report = TrustHelper::SendForScaningOnBulk($pendings);
        sleep(20);
 
        $statuses = ['PAUSSING'];

        $pausing = TrustScan::whereIn('status', $statuses)->get()->pluck('creative_id')->toArray();
        $for_pausing=[];
        foreach($pausing as $id){
            $ad_ID = intval($id) + ($_ENV['WL_PREFIX'] * 1000000);
            $for_pausing[]=$ad_ID;
        }
        $pause = implode(',', $for_pausing);
        TrustHelper::pauseTag($pause);

        sleep(20);
        $incidents = TrustHelper::getIncidentList();
        if(array_key_exists('count',$incidents) && $incidents['count'] > 0) {
            foreach($incidents['incidents'] as $incident){
                $ad_ID = $incident['adTagId'];
                $ad_ID = intval($ad_ID) - ($_ENV['WL_PREFIX'] * 1000000);

                $scan = TrustScan::where('creative_id', $ad_ID)->first();
                if ($scan==null){
                    $saveScan = new TrustScan();
                    $saveScan->provider = 'TMT';
                    $saveScan->status = 'INCIDENT';
                    $saveScan->last_scan = Carbon::now();
                    $saveScan->creative_id = $ad_ID;
                    $saveScan->save();
                } else {
                    $scan->status = 'INCIDENT';
                    $scan->save();
                }

                $creative = Creative::where('id',$ad_ID)->first();

                if ($creative!=null){
                    $creative->status = 0;
                    $creative->save();
                }
                MailerHelper::creativeBlocked($ad_ID);
                Creative::updateTimestamp($ad_ID);
            }
        }




        return $send_report;

    }



    public function fouData() {
        $dt = Carbon::now()->subDay()->format("Y-m");
        $rows = $this->getDataFromFou($dt);
        
        // array_shift($rows);
        $rows = array_slice($rows, 1);
        $data = [];

        foreach ($rows as $r) {
           
            $d = explode(",", $r);
            $ssp = isset($d[5]) && $d[5] ? explode("-",$d[5])[0] : 'unknown';
            $publisher = isset($d[4]) && $d[4] ? $d[4] : 'unknown';
            $domain = isset($d[0]) && $d[0] ? $d[0] : (isset($d[1]) ? $d[1] : "unknown");
            $channel = $d[0] ? 'site' : 'app';
            $media = isset($d[2]) && $d[2] ? $d[2] : 'unknown';
            $platform = isset($d[3]) && $d[3] ? $d[3] : 'unknown';
            
            $total = isset($d[6]) ? intval($d[6]) : 0;
            $viewable = isset($d[7]) ? intval($d[7]) : 0;
            $yes = isset($d[8]) ? intval($d[8]) : 0;
            $yesish = isset($d[9]) ? intval($d[9]) : 0;
            $search = isset($d[10]) ? intval($d[10]) : 0;
            $declared = isset($d[11]) ? intval($d[11]) : 0;
            $fake_device = isset($d[12]) ? intval($d[12]) : 0;
            $stacked_ads = isset($d[13]) ? intval($d[13]) : 0;

            $data[$ssp][$publisher][$domain][$channel][$media][$platform] = [$total, $viewable, $yes, $yesish, $search, $declared, $fake_device, $stacked_ads];

        }

        return response()->json($data);

        // $this->saveData($data);
    }

    private function getDataFromFou($date) {
        $headers = ['Authorization: Bearer 19Zjolx7Kjxl0Pz62fn2Ie3hJjL84zm43Sa6zopKgsSkEBImBnUXBHYo7kp3c5up'];
        $curl = curl_init();
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => "https://data.fouanalytics.com/report/266/{$date}.csv",
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false
        );
        // curl_setopt($curl, CURLOPT_WRITEFUNCTION, $callback);
        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);
        
        $rows = explode("\n", $response);
        return $rows;
    }

    private function saveData($data) {
        $date = Carbon::now()->format("ymdh");
        $payload = json_encode($data);
        $curl = curl_init();
        $curlUrl = "http://209.97.154.200:8080/drfou?incremental&datetime={$date}";
        $curlOptions = array(
            CURLOPT_URL             => $curlUrl,
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_HTTPHEADER      => ['Content-type: application/json'],
            CURLOPT_TIMEOUT         => 45,
            CURLOPT_CUSTOMREQUEST   => "POST",
            CURLOPT_SSL_VERIFYHOST  => 0,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_POSTFIELDS      => $payload
        );
        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);

        if (!$response) {
            $error = curl_error($curl);
            $error_code = curl_errno($curl);
        } else {
            dd($response);
        }

        curl_close($curl);
    }



}
