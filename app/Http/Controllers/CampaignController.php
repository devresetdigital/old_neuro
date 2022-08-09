<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Campaign;
use App\Creative;
use App\Advertiser;
use App\Vwi;
use App\VwiLocation;
use App\Strategy;
use App\Http\Resources\Campaign as CampaignResource;
use App\Http\Resources\CampaignBase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Helpers\CloneHelper;
use Illuminate\Support\Facades\Schema;
use Response;
use DateTime;
use App\CustomData;
use App\IabCountry;
use App\IabRegion;
use App\Languages;
use App\IabCity;
use App\Pixel;
use App\Pmp;
use App\Ssp;
use App\Sitelist;
use App\Iplist;
use App\Ziplist;
use App\StrategiesIsp;
use App\Keywordslist;
use App\CampaignsBudgetFlight;


class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /**
         * 
         * Modificar este if cuando se separen los procesos:
         * 
         */
        if($_GET==[]){
            $_GET["fields"]='updated_at';
        }

        if(isset($_GET["fields"]) || isset($_GET["hide"])){
            $organization = isset($_GET['organization']) ? $_GET['organization'] : false;
            $fields = isset($_GET['fields']) ? $_GET['fields'] : '';
            return $this->getWithFields($fields, $organization);
        }

        $stop_bidding = setting('admin.stop_bidding');
   
        //echo  $stop_bidding;
        if($stop_bidding==1) {
            return "{}";
        } else {
                 //die(Carbon::now());
                 $float_wlprefix = $_ENV['WL_PREFIX'] . ".0";
                 $wlprefix = (float)$float_wlprefix * 1000000;
                 //Get Campaigns
                 if (isset($_GET["actives"])) {
                
                    if(str_contains($_SERVER['SERVER_NAME'], 'inspire.com')){
                        $campaigns = '';
                    }else{
                        $campaigns = Cache::get($_ENV["WL_PREFIX"]."_api_campaigns_actives");
                    }

                     
                     if( $campaigns == "") {
                         $campaigns = Campaign::select('campaigns.id', 'campaigns.updated_at', 'campaigns_budget_flights.budget')
                             ->where('campaigns.status', "=", 1)
                             ->join('campaigns_budget_flights', 'campaigns.id', '=', 'campaigns_budget_flights.campaign_id')
                             ->whereDate('campaigns_budget_flights.date_start', '<', Carbon::now())
                             ->whereDate('campaigns_budget_flights.date_end', '>', Carbon::now())
                             ->get();
                         //check for remaning budget
                            $campaigns = $campaigns->reject(function ($element) {
                             $redis_campaign_spnt = Redis::get('cmp_' . $element->id . '_spt');
                             $redis_campaign_impressions = Redis::get('cmp_' . $element->id . '_imp');

                             if (isset($element->budget)) {
                                 $remaining_budget = round($element->budget - ($redis_campaign_spnt / 1000000), 2);
                             } else {
                                 $remaining_budget = "";
                             }
                             return $remaining_budget <= 0;
                         });


                         //Check Update By strategy
                        foreach ($campaigns as $camp) {
                            $strategies = Strategy::where('campaign_id', '=', $camp->id)->get();
                            foreach ($strategies as $str) {

                                //Format Campaign Pacing and FreqCap
                                $str_pacing_monetary_values = explode(",",$str->pacing_monetary);
                                $str_pacing_impression_values = explode(",",$str->pacing_impression);
                                $str_frequency_cap_values = explode(",",$str->frequency_cap);

                               /* print_r($str_pacing_monetary_values);
                                print_r($str_pacing_impression_values);
                                print_r($str_frequency_cap_values);

                                die();*/

                                $remaining_budget = "";
                                $stid = $str->id + $wlprefix;
                                $redis_strategy_spent = Redis::get('stg_' . $stid . '_spt');
                                $redis_strategy_impressions = Redis::get('stg_' . $stid . '_imp');

                                if (isset($str->budget)) {
                                    $remaining_budget = round($str->budget - ($redis_strategy_spent / 1000000), 2);
                                } else {
                                    $remaining_budget = "";
                                }

                                // echo $str->id+$wlprefix."- ".$remaining_budget." -".$str->checked." | ";
                                if ($str->checked == 0 && ($remaining_budget <= 0 || $remaining_budget == "")) {
                                    // echo "update checked";
                                    Strategy::where('id', $str->id)->update(['checked' => 1]);
                                    Strategy::where('id', $str->id)->update(['updated_at' => Carbon::now()]);
                                    Campaign::where('id', $camp->id)->update(['updated_at' => Carbon::now()]);
                                }

                                //if daily spent mayor o igual a pacing monetary
                                if(isset($str_pacing_monetary_values[1]) && $str_pacing_monetary_values[1]!=""){
                                    //die($daily_spent);

                                    //die($pacing_monetary_values[1]);

                                    if((($redis_strategy_spent / 1000000) >= $str_pacing_monetary_values[1]) && $str->checked == 0) {
                                        Strategy::where('id', $str->id)->update(['checked' => 1]);
                                        Strategy::where('id', $str->id)->update(['updated_at' => Carbon::now()]);
                                        Campaign::where('id', $camp->id)->update(['updated_at' => Carbon::now()]);
                                    }
                                }

                                //die($pacing_monetary_values[1]);

                                //IF Pacing impressions set, and Daily impressons equal or more than Pacing IMP
                                if(isset($str_pacing_impression_values[1]) && $str_pacing_impression_values[1]!="" && $str->checked == 0){
                                    if($redis_strategy_impressions>=$str_pacing_impression_values[1]) {
                                        Strategy::where('id', $str->id)->update(['checked' => 1]);
                                        Strategy::where('id', $str->id)->update(['updated_at' => Carbon::now()]);
                                        Campaign::where('id', $camp->id)->update(['updated_at' => Carbon::now()]);
                                    }
                                }

                            }
                        }
                        if(!str_contains($_SERVER['SERVER_NAME'], 'inspire.com')){
                            Cache::put($_ENV["WL_PREFIX"] . '_api_campaigns_actives', $campaigns, 0.1);
                        }
                        
                        } else {
                         if(isset($_GET["debug"])) {
                             $campaigns = "cache actives";
                         }
                     }

                 } else {
                    if(str_contains($_SERVER['SERVER_NAME'], 'inspire.com')){
                        $campaigns ="";
                    }else{
                        $campaigns =Cache::get($_ENV["WL_PREFIX"]."_api_campaigns");
                    }
                    
                     if($campaigns=="") {
                         $campaigns = Campaign::select('id', 'updated_at')->get();
                        if(!str_contains($_SERVER['SERVER_NAME'], 'inspire.com')){
                             Cache::put($_ENV["WL_PREFIX"].'_api_campaigns', $campaigns, 0.1);
                        }
                     } else {
                         if(isset($_GET["debug"])) {
                             $campaigns = "cache";
                         }
                     }
                 }

                 //

                 //Return collection of campaigns as a resource

                 //return $campaigns;

                 //cache result
                 //$cachestats = Cache::getMemcached()->getStats();
                 //Cache::put('api_campaigns', CampaignResource::collection($campaigns)->keyBy('id'), 0.5);
                 // $cachedcampaigns = Cache::get("api_campaigns");

                if(isset($_GET["debug"])) {
                    return $campaigns;
                } else {
                    return CampaignResource::collection($campaigns)->keyBy('id');
                }
        }
    }


    private function getWithFields($fields, $organization){
        try {

            $campaigns_cache = Cache::get($_ENV["WL_PREFIX"]."_api_campaigns_index_fields");
            if( $campaigns_cache != null){
                return $campaigns_cache;
            }
            
            $schema = Schema::getColumnListing('campaigns');
            if(isset($_GET['hide'])){
                $fields = array_diff($schema, explode(',',$_GET['hide']));
            }else{
                $fields = explode(',',$fields);
            }
            $campaigns = new Campaign;
            if ($organization!= false){
                $organization = explode(',',$organization);
                $campaigns  = $campaigns->whereHas('Advertiser', function ($query) use ($organization){
                    $query->whereIn('organization_id', $organization);
                  
                });
            }

            $fieldsToReturn = $fields;

            if(!in_array('id',$fields)){
                $fields[] = 'id';
            }
           
            $campaigns = $campaigns->get($fields);

            $response = collect($campaigns)->keyBy('id')->map(function ($item) use ($fieldsToReturn) {
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
                if(isset($response['pacing_monetary'])) {
                    $pacing_monetary_values = explode(",",$item->pacing_monetary);
                    $response['pacing_monetary'] = [
                        "type"=>!isset($pacing_monetary_values[0]) ? "" : $pacing_monetary_values[0],
                        "amount"=>!isset($pacing_monetary_values[1]) ? "": $pacing_monetary_values[1],
                        "interval"=>!isset($pacing_monetary_values[2]) ? "": $pacing_monetary_values[2]
                    ];
                }
                if(isset($response['pacing_impression'])) {
                    $pacing_impression_values = explode(",",$item->pacing_impression);
                    $response['pacing_impression'] =  [
                        "type"=>!isset($pacing_impression_values[0]) ? "" : $pacing_impression_values[0],
                        "amount"=>!isset($pacing_impression_values[1]) ? "" : $pacing_impression_values[1],
                        "interval"=>!isset($pacing_impression_values[2]) ? "" : $pacing_impression_values[2]
                    ];
                }
                return $response;
            });

            Cache::put($_ENV["WL_PREFIX"].'_api_campaigns_index_fields', $response, 0.1);
            return $response;

        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['message' => 'There was an error trying to return the data, please check the fields']);

        }

    }


    /**
     * campaigns statuses
     * 
     */
    public function statuses(Request $request){
        try {
            $redirection =  0;
            if($request->has('redirection')){
                $redirection = $request->input('redirection');
                if(intval($redirection) > 2) {
                    return redirect('admin/login');
                }
            }

            $now = gmdate('Y-m-d H:i:s', time());    
   
            $start_date = gmdate('ymd', time());
            
            $from = $start_date .'00';
            $until =  gmdate('ymdH', strtotime($now . ' - 1 hours'));

            $order_direction =  ($request->has('order_direction')) ?  $request->query('order_direction') : 'DESC';
        
            $url = $_ENV['WL_HB_DATA_URL']."?groupby=advcamp_2,advcamp_3&NOCACHE=1&from=".$from."&until=". $until;

            //$client = new \GuzzleHttp\Client();
            //$hb_request = $client->get($url);
            $hb_request = file_get_contents($url);
            $hb_response = json_decode($hb_request,true);

            //real time data
            $url = $_ENV['WL_REALTIME_DATA_URL']."?project=impressions&groupby=campaign,strategy&format=json";
            //$hb_request = $client->get($url);
            $hb_request = file_get_contents($url);
            $real_time_data = json_decode($hb_request,true);

            $campaign_keys = [];

            foreach ($hb_response as $key => $value) {
                $id = explode(",",$key);
                $id = intval($id[0]) - ($_ENV['WL_PREFIX']*1000000);
                $campaign_keys[$id] = $id;
            }
            foreach ($real_time_data as $key => $value) {
                $id = explode(",",$key);
                $id = intval($id[0]) - ($_ENV['WL_PREFIX']*1000000);
                $campaign_keys[$id] = $id;
            }
            
            $campaigns = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.status')
            ->where('status', 1) ->orWhere(function($query) use ($campaign_keys)
            {
                $query->whereIn('id',  $campaign_keys);
            })
            ->whereHas('Strategies', function($q){
                $q->where('date_end', '>=', Carbon::now());
            })->with(array('Strategies'=>function($query) {
                $query->select('strategies.id', 'strategies.campaign_id',
                'strategies.name','strategies.status' , 'strategies.pacing_monetary',
                'strategies.pacing_impression');
            }))->get()->toArray();


            $data_response = [];
            $ids_shown = [];
        
            foreach($campaigns as $key_campaign =>  $campaign){

                $strategy_rows = [];

                $campaign_id = $campaign['id'] + ($_ENV['WL_PREFIX']*1000000);

                $campaign_row =  [
                    'id' => $campaign['id'],
                    'status' => $campaign['status'],
                    'id_to_show' => $campaign_id,
                    'key_search' => "-". $campaign_id ."-". $campaign['name'],
                    'name'=>  $campaign['name'],
                    'is_campaign'=> true,
                    'impressions' => 0,
                    'clicks'=> 0,
                    'spent'=> 0,
                    'pacing_impression'=> 0,
                    'pacing_money'=> 0,
                    'imp_per_minute'=> 0,
                    'spent_per_second'=> 0
                ];

                foreach($campaign['strategies'] as $key => $strategy){

                    $strategy_id = $strategy['id'] + ($_ENV['WL_PREFIX']*1000000);
                    $id_key = $campaign_id .','. $strategy_id;
                    /*if(!array_key_exists($id_key, $hb_response) && !array_key_exists($id_key, $real_time_data)){
                        //discart strategy if it doesn't have data on hb
                        continue;
                    }*/

                    $pacin_imp = explode(",", $strategy['pacing_impression']);
                    $pacin_mone = explode(",", $strategy['pacing_monetary']);

                    $strategy_rows[$key]=[
                        'id_to_show' => $strategy_id,
                        'status' => $strategy['status'],
                        'id' => $strategy['id'],
                        'key_search' => "-". $strategy_id ."-". $strategy['name'] . "-". $campaign_id ."-". $campaign['name'],
                        'name'=>   $strategy['name'],
                        'is_campaign'=> false,
                        'impressions' => 0,
                        'clicks'=> 0,
                        'spent'=> 0,
                        'pacing_impression'=> ($pacin_imp[2]== 1) ?  intval($pacin_imp[1]) * 24  : intval($pacin_imp[1]),
                        'pacing_money'=> ($pacin_mone[2]== 1) ?  intval($pacin_mone[1]) * 24  : intval($pacin_mone[1]),
                        'imp_per_minute'=> 0,
                        'spent_per_second'=> 0
                    ];

                    if(array_key_exists($id_key, $hb_response)){
                        $strategy_rows[$key]['impressions'] = $hb_response[$id_key][0];
                        $strategy_rows[$key]['spent'] = floatval($hb_response[$id_key][3]) / 1000;
                        $strategy_rows[$key]['clicks'] = $hb_response[$id_key][4];
                    }

                    if(array_key_exists($id_key, $real_time_data)){
                        $ids_shown[$campaign_id][] = $strategy_id;
                        $strategy_rows[$key]['impressions'] += $real_time_data[$id_key][0];
                        $strategy_rows[$key]['spent'] += floatval($real_time_data[$id_key][3]) / 1000;
                        $strategy_rows[$key]['clicks'] += $real_time_data[$id_key][4];
                        // spent/impression per second
                        $seconds_past = (gmdate("i") * 60 )+ gmdate("s");
                        $strategy_rows[$key]['imp_per_minute'] =intval(intval($real_time_data[$id_key][0]) / $seconds_past * 60);
                        $strategy_rows[$key]['spent_per_second'] =floatval($real_time_data[$id_key][3]) / 1000 / $seconds_past;
                    }


                    if($strategy_rows[$key]['status'] == 1){
                        $campaign_row['impressions'] +=  $strategy_rows[$key]['impressions'];
                        $campaign_row['clicks'] +=  $strategy_rows[$key]['clicks'];
                        $campaign_row['spent'] +=  $strategy_rows[$key]['spent'];
                        $campaign_row['pacing_impression'] +=  intval($strategy_rows[$key]['pacing_impression']);
                        $campaign_row['pacing_money'] +=  floatval($strategy_rows[$key]['pacing_money']);
                        $campaign_row['imp_per_minute'] +=  $strategy_rows[$key]['imp_per_minute'];
                        $campaign_row['spent_per_second'] +=  floatval($strategy_rows[$key]['spent_per_second']);
                    }
                    $campaign_row['key_search'] .= $strategy_rows[$key]['key_search'] ;
                }


                //discart capaign if doesn't have stategies
               /* if(count($strategy_rows) == 0){ continue;}*/
            

                if($request->has('orderBy')) {
                    if($request->query('orderBy') =='name'){
                        $this->orderArray($strategy_rows, 'name', $order_direction, true);
                    } else {
                        $this->orderArray($strategy_rows, $request->query('orderBy'), $order_direction, false);
                    }
                } else {
                    $this->orderArray($strategy_rows, 'name', 'ASC', true);
                }

                $campaign_row['strategies'] = $strategy_rows;
                $data_response[] = $campaign_row;
            }

         


            if($request->has('orderBy')) {
                if($request->query('orderBy') =='name'){
                    $this->orderArray($data_response, 'name', $order_direction, true);
                } else {
                    $this->orderArray($data_response, $request->query('orderBy'), $order_direction, false);
                }
            } else {
                $this->orderArray($data_response, 'name', 'ASC', true);
            }
            
            $order_direction = (strtoupper($order_direction) == 'ASC') ? 'DESC' : 'ASC';

           
            if ($request->has('format') && $request->get('format') == 'json'){
                return response()->json($data_response);
            }

            return Voyager::view('voyager::campaigns.campaignsstatuses',compact('data_response','order_direction','ids_shown') );

        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            $redirection += 1;
            return redirect('admin/campaigns_statuses?redirection='.$redirection);
        }
        
    }


    public function campaignRanges() {

        $campaigns = CampaignsBudgetFlight::get()->groupBy('campaign_id');

        $response = $campaigns->map(function ($camp) {
            $start_date =null;
            $end_date =null;
            $campaign_id = null;
            foreach ($camp as $key => $fligth) {
                if($fligth->date_start != null){
                    $f_start_date= DateTime::createFromFormat("Y-m-d H:i:s", $fligth->date_start);
                    $f_start_date=$f_start_date->getTimestamp();
                }else{
                    $f_start_date=null;
                }

                if($fligth->date_end != null){
                    $f_end_date= DateTime::createFromFormat("Y-m-d H:i:s", $fligth->date_end);
                    $f_end_date=$f_end_date->getTimestamp();
                }else{
                    $f_end_date=null;
                }
                
                if($start_date == null || $start_date > $f_start_date){
                    $start_date = $f_start_date;
                }
                if($end_date == null || $end_date <  $f_end_date ){
                    $end_date = $f_end_date;
                }
                $campaign_id = $fligth->campaign_id;
            }

            $campaign_id += $_ENV['WL_PREFIX']*1000000;
        
            return [
               "campaign_id_prefixed" => $campaign_id,
               "start_date" => $start_date,
               "end_date" => $end_date,
            ];
        });
        return $response;
    }

    /**
     *  returns a csv file with pacing data
     */
    public function exportPacing(Request $request)
    {
        try {
            $now = gmdate('Y-m-d H:i:s', time());    
   
            $start_date = gmdate('ymd', time());
            
            $from = $start_date .'00';
            $until =  gmdate('ymdH', strtotime($now . ' - 1 hours'));

            $order_direction =  'DESC';
        
            $url = $_ENV['WL_HB_DATA_URL']."?groupby=advcamp_2,advcamp_3&from=".$from."&until=". $until;
        
     
            $hb_request = file_get_contents($url);
            $hb_response = json_decode($hb_request,true);

            //real time data
            $url = $_ENV['WL_REALTIME_DATA_URL']."?project=impressions&groupby=campaign,strategy&format=json";
            $hb_request = file_get_contents($url);
            $real_time_data = json_decode($hb_request,true);

            $campaign_keys = [];

            foreach ($hb_response as $key => $value) {
                $id = explode(",",$key);
                $id = intval($id[0]) - ($_ENV['WL_PREFIX']*1000000);
                $campaign_keys[$id] = $id;
            }
            foreach ($real_time_data as $key => $value) {
                $id = explode(",",$key);
                $id = intval($id[0]) - ($_ENV['WL_PREFIX']*1000000);
                $campaign_keys[$id] = $id;
            }
            
            $campaigns = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.status')
            ->where('status', 1) ->orWhere(function($query) use ($campaign_keys)
            {
                $query->whereIn('id',  $campaign_keys);
            })
            ->whereHas('Strategies', function($q){
                $q->where('date_end', '>=', Carbon::now());
            })->with(array('Strategies'=>function($query) {
                $query->select('strategies.id', 'strategies.campaign_id',
                'strategies.name','strategies.status' , 'strategies.pacing_monetary',
                'strategies.pacing_impression');
            }))->get()->toArray();


            $data_response = [];
            $ids_shown = [];
        
            foreach($campaigns as $key_campaign =>  $campaign){

                $strategy_rows = [];

                $campaign_id = $campaign['id'] + ($_ENV['WL_PREFIX']*1000000);


                foreach($campaign['strategies'] as $key => $strategy){

                    $strategy_id = $strategy['id'] + ($_ENV['WL_PREFIX']*1000000);
                    $id_key = $campaign_id .','. $strategy_id;
                    if(!array_key_exists($id_key, $hb_response) && !array_key_exists($id_key, $real_time_data)){
                        //discart strategy if it doesn't have data on hb
                        continue;
                    }

                    $pacin_imp = explode(",", $strategy['pacing_impression']);
                    $pacin_mone = explode(",", $strategy['pacing_monetary']);

                    $strategy_rows=[
                        'camapign_id' => $campaign_id,
                        'camapign_name'=>   $campaign['name'],

                        'strategy_id' => $strategy_id,
                        'strategy_name'=>   $strategy['name'],

                        'impressions' => 0,
                        'clicks'=> 0,
                        'spent'=> 0,
                        'pacing_impression'=> ($pacin_imp[2]== 1) ?  intval($pacin_imp[1]) * 24  : intval($pacin_imp[1]),
                        'pacing_money'=> ($pacin_mone[2]== 1) ?  intval($pacin_mone[1]) * 24  : intval($pacin_mone[1]),
                        'pacing_imp_per'=> 0,
                        'pacing_money_per'=> 0,
                        'ctr'=> 0
                    ];

                    if(array_key_exists($id_key, $hb_response)){
                        $strategy_rows['impressions'] = $hb_response[$id_key][0];
                        $strategy_rows['spent'] = $hb_response[$id_key][3] / 1000;
                        $strategy_rows['clicks'] = $hb_response[$id_key][4];
                    }

                    if(array_key_exists($id_key, $real_time_data)){
                        $ids_shown[$campaign_id][] = $strategy_id;
                        $strategy_rows['impressions'] += $real_time_data[$id_key][0];
                        $strategy_rows['spent'] += $real_time_data[$id_key][3] / 1000;
                        $strategy_rows['clicks'] += $real_time_data[$id_key][4];
                    }

                    /**
                     * format spent
                     */

                    $strategy_rows['spent'] = number_format($strategy_rows['spent'],2);


                    if($strategy_rows['impressions'] > 0){
                        $strategy_rows['ctr'] =   number_format($strategy_rows['clicks'] / $strategy_rows['impressions'], 2);
                    }

                    if($strategy_rows['pacing_impression'] > 0){
                        $strategy_rows['pacing_imp_per'] =   number_format($strategy_rows['impressions']/$strategy_rows['pacing_impression'], 2);
                    }
                    if($strategy_rows['pacing_money'] > 0){
                        $strategy_rows['pacing_money_per'] =   number_format($strategy_rows['spent']/$strategy_rows['pacing_money'], 2);
                    }
                    $data_response[] = $strategy_rows;
                }
            }

        }  catch (\Exception $e) {
          
            Log::info($e->getMessage());
            Log::info($e->getTrace());
            return response()->json(['success' => false, 'message' => 'There was an error please contact the admin']);
        }

        if($request->has('json')){
            return response()->json(['success' => true, 'data' => $data_response]);
        }

        $filename = storage_path("testFile.csv");
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array(
            "camapign id",
            "campaign name",
            "strategy id",
            "strategy name",
            "impressions",
            "goal",
            "percentage",
            "spent",
            "percentage",
            "budget",
            "clicks",
            "ctr"
           ));
    
        foreach($data_response as $row) {
            fputcsv($handle, array(
                $row["camapign_id"],
                $row["camapign_name"],
                $row["strategy_id"],
                $row["strategy_name"],
                $row["impressions"],
                $row["pacing_impression"],
                $row["pacing_imp_per"],
                $row["spent"],
                $row["pacing_money_per"],
                $row["pacing_money"],
                $row["clicks"],
                $row["ctr"]
            ));
        }
    
        fclose($handle);
    
        $headers = array(
            'Content-Type' => 'text/csv',
        );
     
        return Response::download($filename, 'pacing.csv', $headers);
    }


    /**
     * 
     */
    private function orderArray(&$data, $order_key, $order_direction = 'ASC', $alphabetic = false){
        $order_direction = strtoupper($order_direction);

        if($alphabetic){
            if ($order_direction == 'ASC'){
                usort($data, function($a, $b) use ($order_key){
                    return strcmp(strtoupper($a[$order_key]),strtoupper($b[$order_key]));
                });
            }else{
                usort($data, function($a, $b) use ($order_key){
                    return strcmp(strtoupper($b[$order_key]),strtoupper($a[$order_key]));
                });
            }
        }else{
            if ($order_direction == 'ASC'){
                usort($data, function($a, $b) use ($order_key){
                    return $a[$order_key] - $b[$order_key];
                });
            }else{
                usort($data, function($a, $b) use ($order_key){
                    return $b[$order_key] - $a[$order_key];
                });
            }
        }
    }

    /**
     * 
     */
    public function getRealTime(){
        //$client = new \GuzzleHttp\Client();
        //real time data
        $seconds_past = (intval(gmdate("i",  time())) * 60 )+ intval(gmdate("s", time()));
        $url = $_ENV['WL_REALTIME_DATA_URL']."?project=impressions&groupby=campaign,strategy&format=json";
        $hb_request = file_get_contents($url);
        $real_time_data = json_decode($hb_request,true);

        $camapigns = [];
        foreach ($real_time_data as $key =>$data){
            $camapigns[$key]=[
                'impression' => $data[0],
                'spent' => $data[3] / 1000
            ];
        }
        $data = [
            'seconds' => $seconds_past,
            'campagins' =>$camapigns
        ];
        return response()->json(['success' => true, 'data' => $data]);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            //PREFIX
            if ($id > 1000000) {
                $float_wlprefix = $_ENV['WL_PREFIX'].".0";
                $wlprefix = (float) $float_wlprefix*1000000;
                $old_id = $id;

                $id = $id-$wlprefix;
                $prefix = $old_id - $id;

                //die($id);

                if ($id >= 1000000) {
                    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
                    die();
                }
            }

            $prefixed_id = intval($_ENV["WL_PREFIX"]) + intval($id);
            $cache_data = Cache::get($prefixed_id."_api_campaigns_show");
            if( $cache_data != null){
                return $cache_data;
            }

            //Get Campaign by id
            $campaign = Campaign::with("strategies.strategyConcept","CampaignsBudgetFlights")
                        ->where('campaigns.id','=',$id)
                        ->first();

            $schema = Schema::getColumnListing('campaigns'); 

            $exta_fields = [
            'campaign_id',    
            'organization_id',
            'margin',
            'remaning_budget',
            'daily_spent',
            'daily_impressions',
            'strategies',
            'flights'];

            $schema = array_merge($schema,$exta_fields);


            $fields = $schema;


            if(isset($_GET['hide'])){
                $fields = array_diff($schema, explode(',',$_GET['hide']));
            }else{
                $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : $schema;
            }
            
            $campaign->fields = $fields;
            //Return Single Campaign as a Resource
            $response = new CampaignResource($campaign);

            Cache::put($prefixed_id.'_api_campaigns_show', $response, 0.1);

            return $response;

        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['message' => 'There was an error trying to return the data, please check the fields']);

        }
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
    public function vwis($id)
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
        $campaign = Campaign::where("id","=",$id)->get();

        $campaign_vwis_locations_array = explode(",",$campaign[0]->vwis_location);
        $campaign_vwis_array = explode(",",$campaign[0]->vwis);

        $campaign_vwis_locations = VwiLocation::whereIn("id",$campaign_vwis_locations_array)->get();

        $campaign_vwis = Vwi::whereIn("vwi_locations_id",$campaign_vwis_locations_array)->get();
        

        $c=0;
        foreach ($campaign_vwis as $cw){

            $geos = json_decode($cw->geolocation);
            $days = json_decode($cw->days);

            $vwi[$c]["id"]=$cw->id;
            $vwi[$c]["name"]=$cw->name;
            $vwi[$c]["geolocation"]=$geos;
            $vwi[$c]["start"]=strtotime($cw->start);
            $vwi[$c]["end"]=strtotime($cw->end);
            $vwi[$c]["days"]=$days;
            $vwi[$c]["start_hour"]=$cw->start_hour;
            $vwi[$c]["end_hour"]=$cw->end_hour;
            $vwi[$c]["expiration"]=$cw->expiration;
            $c++;
        }

        return json_encode($vwi,true);
    }
    public function withvwis()
    {
        //Get Campaign by id
        $campaigns = Campaign::select("campaigns.id","campaigns.name","campaigns.vwis_location")
            ->where('vwis_location','<>','')
            ->whereNotNull('vwis_location')
            ->get()
            ->keyBy('id');

        return $campaigns;
    }




    public function getPathInteractiveData(Request $request){

        try {
            //code...
       

        $context = $request->get('context');

        $type = $request->get('type');

        $id = $request->get('campaign_id');

        $campaign_prefixed = $request->get('campaign_id');

        $strategy_id = $request->get('strategy_id');

        if(intval($id)>1000000){
            $id=intval($id)-1000000;
        }

        $fligths = CampaignsBudgetFlight::where('campaign_id', $id)->get();

        if($fligths->count()==0){
            return array("draw" => intval($request->input('draw')),  
            "recordsTotal"=> 0,
            "recordsFiltered"=>0,
            "data"=> []
            );
        }
        $from=$fligths[0]->date_start;

        $until=$fligths[0]->date_end;

        foreach ($fligths as $key => $item) {
            if ($from > $item->date_start){
                $from = $item->date_start;
            }
            if( $until < $item->date_end) {
                $until = $item->date_end;
            }
        }

        $from = substr($from,2,2)."".substr($from,5,2)."".substr($from,8,2)."00";
        $until = substr($until,2,2)."".substr($until,5,2)."".substr($until,8,2)."23";

        $start = $request->get('start');
        $length = $request->get('length');

        $search = $request->get('search') != null ?  $request->get('search')['value'] : '';

      

        if($type == 'CONTEXTUAL'){
            $url ="http://167.71.174.130:8080/Contextual?from=".$from."&until=".$until."&groupby=context&campaigns=". $campaign_prefixed ."&contexts=*".$search."*&typecontexts=".$context."&nocache=1&format=json&orderby=1" ;
        }else if($type == 'AUDIENCE'){
            $url ="http://167.71.174.130:8080/Audience?from=".$from."&until=".$until."&groupby=audience&campaigns=". $campaign_prefixed ."&audiences=*".$search."*&dmps=".$context."&nocache=1&format=json&orderby=1" ;
        }else{
            return response()->json([],200);  
        }
        if($start!=null){
            $url.="&paging=" . $length . "," . $start ;
        }

        if( $strategy_id != null){
            $url.="&strategies=".$strategy_id;
        }
       
        $report_json = file_get_contents($url);
        $report_json = json_decode($report_json) ?  json_decode($report_json) :[];

        if($type == 'CONTEXTUAL'){
            $url_totals ="http://167.71.174.130:8080/Contextual?from=".$from."&until=".$until."&groupby=context&campaigns=". $campaign_prefixed ."&contexts=*".$search."*&typecontexts=".$context."&nocache=1&format=details";
        }else if($type == 'AUDIENCE'){
            $url_totals ="http://167.71.174.130:8080/Audience?from=".$from."&until=".$until."&groupby=audience&campaigns=". $campaign_prefixed ."&audiences=*".$search."*&dmps=".$context."&nocache=1&format=details";
        }else{
            return response()->json([],200);  
        }
        if($start!=null){
            $url.="&paging=" . $length . "," . $start ;
        }
        if( $strategy_id != null){
            $url_totals.="&strategies=".$strategy_id;
        }
            
        $report_json_totals = file_get_contents($url_totals);
        $report_json_totals = json_decode($report_json_totals) ?  json_decode($report_json_totals) :[];


        //get total reach

        $url_reach = "http://51.161.86.82:9080/getcampaigns?nocache=1&format=details&campaignid=".$campaign_prefixed;
        if( $strategy_id != null){
            $url_reach.="&strategyid=".$strategy_id;
        }
        $request_reach = file_get_contents($url_reach);
        $data_reach  = json_decode($request_reach, true);
       
      
        $total_sum = intval($data_reach['totals']['reach']) > 0 ?  intval($data_reach['totals']['reach']) : 1 ;


        $data= [];
        foreach($report_json as $key => $item){
            $count = $item[0];
            $persentage =   floatval(intval($item[0])*100/$total_sum);
            $data[]=[
                $key,
                $count,
                round($persentage,1) . " %"
            ];
        }

        $total_item = $report_json_totals->totals->items > 100 ? 100 : $report_json_totals->totals->items;

        $response = array("draw" => intval($request->input('draw')),  
        "recordsTotal"=> $total_item,
        "recordsFiltered"=> $total_item,
        "data"=> $data
        );
        return response()->json($response,200);   

        } catch (\Throwable $th) {
            $response = array("draw" => intval($request->input('draw')),  
            "recordsTotal"=> 0,
            "recordsFiltered"=>0,
            "data"=> []
            );
            return response()->json($response,200);   
        }


    }

    
    /**
     * 
     */
    public function checkExistance(Request $request){
        
        if($request->has('advertiser')){
           
            $advertiser = Advertiser::find(intval($request->input('advertiser')));
            $advertisers = Advertiser::where('organization_id',$advertiser->organization_id)->get()->pluck('id')->toArray();
           
            $error = false;
            $message = [];
            $coincidences = [];
            $campaigns_field = $request->input('campaigns');
            $geofencing_fields = $request->input('geofencing');
            $targeting_fields = $request->input('targeting');

            $campaigns_ids = array_key_exists('integers', $campaigns_field ) ?  array_unique($campaigns_field['integers']): [];
            $campaigns_names =  array_key_exists('names', $campaigns_field ) ? array_unique($campaigns_field['names']) : [];

            $new_campaigns = 0;

            foreach ($campaigns_names as $key => $name) {
                $campaigns = Campaign::whereIn('advertiser_id', $advertisers)->where('name',$name)->get()->toArray();
          
                if(count($campaigns) == 0){
                    $new_campaigns++;
                }elseif (count($campaigns) > 1) {
                    $error = true;
                    $message[]="duplicated campaigns: " . $name;
                    $coincidences[]=$campaigns;
                }
            }
            foreach ($campaigns_ids as $key => $id) {
                $campaigns = Campaign::whereIn('advertiser_id', $advertisers)->where('id',$id)->get()->toArray();
                if(count($campaigns) == 0){
                    $error = true;
                    $message[]="invalid campaign id: ". $id;
                }
            }
         
            foreach ($geofencing_fields['country'] as $value) {
                if($value != null){
                    $validate = explode(',', $value);
                    foreach ($validate as $name) {
                        $exists = IabCountry::whereRaw("UPPER(`country`) LIKE '%". strtoupper($name)."%'")->count(); 
                        if($exists==0){
                            $error = true;
                            $message[]="invalid country: ". $name;
                        }    
                    }
                }
            }
           
            
           
            foreach ($geofencing_fields['city'] as $value) {
                if($value != null){
                    $validate = explode(',', $value);
                    foreach ($validate as $name) {
                        $exists = IabCity::whereRaw("UPPER(`city`) LIKE '%". strtoupper($name)."%'")->count(); 
                        if($exists==0){
                            $error = true;
                            $message[]="invalid city: ". $name;
                        }    
                    }
                }
            }
           

           
            foreach ($geofencing_fields['region'] as $value) {
                if($value != null){
                    $validate = explode(',', $value);
                    foreach ($validate as $name) {
                        $exists = IabRegion::whereRaw("UPPER(`region`) LIKE '%". strtoupper($name)."%'")->count(); 
                        if($exists==0){
                            $error = true;
                            $message[]="invalid region: ". $name;
                        }    
                    }
                }
            }
            


            
            foreach ($geofencing_fields['language'] as $value) {
                if($value != null){
                    $validate = explode(',', $value);
                    foreach ($validate as $name) {
                        $exists = Languages::whereRaw("UPPER(`language`) LIKE '%". strtoupper($name)."%'")->count(); 
                        if($exists==0){
                            $error = true;
                            $message[]="invalid Language: ". $name;
                        }    
                    }
                }
            }
           

           
            ////////////////////////////////////////////////////////////////////////////////////////////////////

            $strategies_field = $request->input('strategies');

            if($strategies_field!= null){
                $strategies =  array_key_exists('ids', $strategies_field ) ?  array_unique($strategies_field['ids']): [];
                $check_strategies = Strategy::whereIn('id', $strategies)->get()->pluck('id')->toArray();
                $strategies_diff = array_diff($strategies, $check_strategies);
                foreach ($strategies_diff as $key => $diff) {
                    $error = true;
                    $message[]="invalid strategy id: ". $diff . " change it or remove it to create a new strategy";
                }
            }

            
           
            if ( isset($targeting_fields['sitelist']) && count($targeting_fields['sitelist']) > 0){

                $data = $this->splitArray($targeting_fields['sitelist']);

                $values =  array_unique($data[0]);
                $check = Sitelist::whereIn('id', $values)->get()->pluck('id')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid sitelist: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = Sitelist::whereIn('name', $values)->get()->pluck('name')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid sitelist: ". $diff ;
                }

            }

            if (isset($targeting_fields['iplist']) &&  count($targeting_fields['iplist']) > 0) {

                $data = $this->splitArray($targeting_fields['iplist']);

                $values =  array_unique($data[0]);
                $check = Iplist::whereIn('id', $values)->get()->pluck('id')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid iplist: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = Iplist::whereIn('name', $values)->get()->pluck('name')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid iplist: ". $diff ;
                }
            }

            if (isset($targeting_fields['pmps']) &&  count($targeting_fields['pmps']) > 0) {

                $data = $this->splitArray($targeting_fields['pmps']);

                $values =  array_unique($data[0]);
           
                $check = Pmp::whereIn('id', $values)->get()->pluck('id')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid pmps: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = Pmp::whereIn('name', $values)->get()->pluck('name')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid pmps: ". $diff ;
                }
            }

            if (isset($targeting_fields['ssps']) &&  count($targeting_fields['ssps']) > 0) {

                $data = $this->splitArray($targeting_fields['ssps']);

                $values =  array_unique($data[0]);
                $check = Ssp::whereIn('id', $values)->get()->pluck('id')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid ssps: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = Ssp::whereIn('name', $values)->get()->pluck('name')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid ssps: ". $diff ;
                }
            }

            if (isset($targeting_fields['ziplist']) &&  count($targeting_fields['ziplist']) > 0) {

                $data = $this->splitArray($targeting_fields['ziplist']);

                $values =  array_unique($data[0]);
                $check = Ziplist::whereIn('id', $values)->get()->pluck('id')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid ziplist: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = Ziplist::whereIn('name', $values)->get()->pluck('name')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid ziplist: ". $diff ;
                }
            }

            if (isset($targeting_fields['keywords']) &&  count($targeting_fields['keywords']) > 0) {

                $data = $this->splitArray($targeting_fields['keywords']);

                $values =  array_unique($data[0]);
                $check = Keywordslist::whereIn('id', $values)->get()->pluck('id')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid keywords: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = Keywordslist::whereIn('name', $values)->get()->pluck('name')->toArray();
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid keywords: ". $diff ;
                }
            }

            if (isset($targeting_fields['devices']) &&  count($targeting_fields['devices']) > 0) {

                $data = $this->splitArray($targeting_fields['devices']);

                $values =  array_unique($data[0]);
                $check = array(1,2,3,4,5,6,7,8,9);
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid devices: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = array(
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
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid devices: ". $diff ;
                }
            }

            if (isset($targeting_fields['inventory']) &&  count($targeting_fields['inventory']) > 0) {

                $data = $this->splitArray($targeting_fields['inventory']);

                $values =  array_unique($data[0]);
                $check = array(1,2,3);
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid inventory type: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = array(
                    'Desktop & Mobile Web',
                    'Mobile In-App',
                    'Mobile Optimized Web'
                );
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid inventory type: ". $diff ;
                }
            }

            if (isset($targeting_fields['os']) &&  count($targeting_fields['os']) > 0) {

                $data = $this->splitArray($targeting_fields['os']);

                $values =  array_unique($data[0]);
                $check = array(1,2,3,4,5,6,7,8,9,10);
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid os: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = array(
                    'Windows 7',
                    'Windows 8',
                    'Windows 10',
                    'Mac OS',
                    'Linux',
                    'ANDROID',
                    'IOS',
                    'Roku OS',
                    'Tizen',
                    'Other',
                );
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid os: ". $diff ;
                }
            }
     
            if (isset($targeting_fields['browser']) &&  count($targeting_fields['browser']) > 0) {
                
                $data = $this->splitArray($targeting_fields['browser']);

                $values =  array_unique($data[0]);
                $check = array(1,2,3,4,5,6);
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid browser: ". $diff ;
                }

                $values =  array_unique($data[1]);
                $check = array(
                    'Chrome',
                    'Firefox',
                    'Windows 10',
                    'MSIE',
                    'Opera',
                    'Safari',
                    'Other'
                );
                $diffs = array_diff($values, $check);
                foreach ($diffs as $key => $diff) {
                    $error = true;
                    $message[]="invalid browser: ". $diff ;
                }
            }


            return [
                "coincidences"=> $coincidences,
                "new"=>$new_campaigns,
                "message" => $message,
                "error" => $error,
            ];
        }else{
            return false;
        }
    }

    private function splitArray($data){
        $response[0] = [];
        $response[1] = [];

        foreach ($data as $key => $element) {
            $auxs = explode(',', $element);
            foreach ($auxs as $key => $value) {
                if(intval($value) > 0){
                    $response[0][]=$value;
                }else{
                    if(strtolower($value) != 'all'){
                        $response[1][]=$value;
                    }
                }
            }
        }
        return $response;
    }



    public function dailyReportTable(Request $request){

        $from = explode('-',$request->input('from'));
        $until = explode('-',$request->input('until'));

        $from = substr($from[0], 2,2) .  $from[1] .$from[2] . '00';
        $until = substr($until[0], 2,2) .  $until[1] .$until[2] . '23';
        $groupby = rtrim($request->input('groupby'),',');
    
        $includeid = $request->has('includeid') &&  $request->input('includeid') == 'true' ? true : false; 
        
        $start =$request->has('start') ? $request->get('start') : '' ;
        $length = $request->has('length') ? $request->get('length') : '' ;;
        $orderby = $request->has('order') ? $request->get('order')[0]['column'] : 1;

        if($orderby > 5){
            $orderby++;
        }

        $campaigns = $request->input('campaigns') != null ? implode(',',$request->input('campaigns')) : '';
    
        $url = $_ENV['WL_HB_DATA_URL']."?groupby=".$groupby."&orderby=" . $orderby."&NOCACHE=1&from=".$from."&until=". $until;
     
        if($campaigns != ''){
            $url .= '&campaigns='.$campaigns;
        }
  
        if($length != ''){
            $url .="&paging=" . $length . "," . $start;
        }
        $hb_request = file_get_contents($url);
        $hb_response = json_decode($hb_request,true);

        //  VAST EVENTS

        $url_vast = $_ENV['WL_HB_DATA_URL']."_vast?groupby=".$groupby."&orderby=" . $orderby."&NOCACHE=1&from=".$from."&until=". $until;
        if($campaigns != ''){
            $url_vast .= '&campaigns='.$campaigns;
        }

        if($length != ''){
            $url_vast .="&paging=" . $length . "," . $start;
        }
        $hb_request_vast = file_get_contents($url_vast);
        $hb_response_vast = json_decode($hb_request_vast, true);

        $rows=[];
 
        $campaigns_names = [];
        $strategies_names = [];
        $creatives_names = [];
        $groupby_order = explode(',',$groupby);



        foreach ($hb_response as $group => $data) {
            $labels = explode(',' , $group);

            foreach ($labels as $key => &$value) {
                if(array_key_exists($key,$groupby_order)){
                    $model = $groupby_order[$key];
                    $id = intval($value) - ($_ENV["WL_PREFIX"] * 1000000);
                    switch ($model) {
                        case 'campaign':
                            if(!array_key_exists($value, $campaigns_names)){
                                $name = Campaign::where('id', $id )->select('name')->first();
                                $campaigns_names[$value] =  $name != null ? $name->name : '';
                            }
                            if($includeid == true){
                                $value = '['.$value.'] ' . $campaigns_names[$value];
                            }else{
                                $value = $campaigns_names[$value];
                            }
                            
                            break;
                        case 'strategy':
                            if(!array_key_exists($value, $strategies_names)){
                                $name = Strategy::where('id', $id )->select('name')->first();
                                $strategies_names[$value] =  $name != null ? $name->name : '';
                            }
                            if($includeid == true){
                                $value = '['.$value.'] ' . $strategies_names[$value];
                            } else {
                                $value = $strategies_names[$value];
                            }
                            break;
                        case 'creative':
                            if(!array_key_exists($value, $creatives_names)){
                                $name = Creative::where('id', $id )->select('name')->first();
                                $creatives_names[$value] = $name != null ? $name->name : '';
                            }
                            if($includeid == true){
                                $value = '['.$value.'] ' . $creatives_names[$value];
                            } else {
                                $value = $creatives_names[$value];
                            }
                            break;
                        case 'date':

                            $value =  substr($value,2,2) .'-'.substr($value,4,2).'-'. substr($value,0,2);
                            break;
                    }
                }
            }

            $divider =  floatval($data[0]);

            $aux = array_merge([],$labels);
            array_push($aux, $data[0]);
            array_push($aux,'$' . ( round(floatval($data[3]) / 1000 ,2) ));
            array_push($aux, '$' . ($divider <= 0 ? 'error' : round(floatval($data[3]) / floatval($data[0]),2) ));
            array_push($aux, $data[4] );
            array_push($aux, $divider <= 0 ? 'error' : round( floatval($data[4]) / floatval($data[0]) * 100, 3) . '%' );
         
            $number = array_key_exists($group, $hb_response_vast) ? $hb_response_vast[$group][0] : '-';
            array_push($aux, $number);

            $number = array_key_exists($group, $hb_response_vast) ? $hb_response_vast[$group][3] : '-';
            array_push($aux, $number);

            $number = array_key_exists($group, $hb_response_vast) ? $hb_response_vast[$group][4] : '-';
            array_push($aux, $number);

            $number = array_key_exists($group, $hb_response_vast) ? $hb_response_vast[$group][5] : '-';
            array_push($aux, $number);

            $number = array_key_exists($group, $hb_response_vast) ? $hb_response_vast[$group][6] : '-';
            array_push($aux, $number);

            $rows[]= $aux;
        }

        if( $request->has('format') &&  $request->get('format')=='json' ){
       
            return $rows;
        }

        $url = $_ENV['WL_HB_DATA_URL']."?groupby=".$groupby."&NOCACHE=1&from=".$from."&until=". $until . "&format=details";
        if($campaigns != ''){
            $url .= '&campaigns='.$campaigns;
        }
        $report_json = file_get_contents($url);
    
        $report_json = json_decode($report_json);

        $response = array (
            "draw" => intval($request->input('draw')),  
            "recordsTotal"=> $report_json->totals->items,
            "recordsFiltered"=> $report_json->totals->items,
            "data"=> $rows
        );

        return response()->json($response,200);    
        
    }


    
    public function changeStatus($id){
        $camapign = Campaign::find(intval($id));
      
        if(!$camapign){
            return response()->json('invalid id' , 401);
        }
        $camapign->status =  $camapign->status == 0 ? 1 : 0;
        $camapign->save();
        return response()->json('success' , 200);
    }


    public function getByAdvertiser($id)
    {
        return Campaign::select(['name','id'])->where('advertiser_id', $id)->get();
    }
}