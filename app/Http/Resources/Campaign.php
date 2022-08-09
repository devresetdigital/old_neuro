<?php

namespace App\Http\Resources;

use App\Advertiser;
use App\CampaignsBudgetFlight;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class Campaign extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $response = [];
     
        if ($this->fields != null){
         
            //Format Campaign Pacing
            $pacing_monetary_values = explode(",",$this->pacing_monetary);
            $pacing_impression_values = explode(",",$this->pacing_impression);

            //Prefix
            $float_wlprefix = env('WL_PREFIX').".0";
            if((float)$float_wlprefix>0) {
                $wlprefix = (float)$float_wlprefix * 1000000;
            } else {
                $wlprefix =0;
            }

            //VWIS
            $vwis = explode(",",$this->vwis);

            //GET Advertiser Details
            $campaign_advertiser = Advertiser::find($this->advertiser_id);
            //GET Flights
            $campaign_flights = CampaignsBudgetFlight::where("campaign_id" , "=", $this->id)
                                ->whereDate('date_start','<',Carbon::now())
                                ->whereDate('date_end','>',Carbon::now())
                                ->first();
            $idprefixed = $this->id+$wlprefix;
            $redis_campaign_spnt = Redis::get('cmp_'.$idprefixed.'_spt');
            $redis_campaign_impressions = Redis::get('cmp_'. $idprefixed.'_imp');
            
            //return $campaign_flights->budget;

            if(isset($campaign_flights->budget)){ $remaining_budget = round($campaign_flights->budget-($redis_campaign_spnt/1000000),2); } else { $remaining_budget=""; }
            $impressions = $redis_campaign_impressions;

            //die($this->strategies);
            //Check Strategies Monetary Limit
            $this->strategies = $this->strategies->reject(function($element) {
                $monetary_limit = explode(",",$element->pacing_monetary);
                //die($monetary_limit[1]);
                if(count($monetary_limit)>1 && $monetary_limit[1]!="") {
                    //Prefix
                    $float_wlprefix = env('WL_PREFIX').".0";
                    if((float)$float_wlprefix>0) {
                        $wlprefix = (float)$float_wlprefix * 1000000;
                    } else {
                        $wlprefix =0;
                    }
                    $idprefixed = $element->id + $wlprefix;
                    $strategy_daily_spent = Redis::get('stg_'.$idprefixed.'_spt');
                    if($strategy_daily_spent!="" && $strategy_daily_spent>0){ $strategy_daily_spent=round($strategy_daily_spent/1000000,2); } else { $strategy_daily_spent=""; }
                    return $monetary_limit[1] < $strategy_daily_spent;
                }
            });

            //Si remaining Budget vacio o menor a 0

            if($remaining_budget == "" || $remaining_budget < 0){
                $this->strategies = "";
            }
        

            $response = [
                "campaign_id" => $this->id,
                "user_id" => $this->user_id,
                "status" => $this->status,
                "organization_id" => isset($campaign_advertiser->organization_id) ? $campaign_advertiser->organization_id : "",
                "advertiser_id" => $this->advertiser_id,
                "margin" => isset($campaign_advertiser->margin) && $campaign_advertiser->margin>0 ? $campaign_advertiser->margin/100 : "",
                "name" => $this->name,
                "managed" => $this->managed,
                "ad_collitions" => $this->ad_collitions,
                "goal_type_id" => $this->goal_type_id,
                "goal_v1" => isset($this->goal_v1) ? $this->goal_v1 : 0,
                "goal_v2" => isset($this->goal_v2) ? $this->goal_v2 : "",
                "goal_v3" => isset($this->goal_v3) ? $this->goal_v3 : "",
                "pacing_monetary" => [
                    "type"=>!isset($pacing_monetary_values[0]) ? "" : $pacing_monetary_values[0],
                    "amount"=>!isset($pacing_monetary_values[1]) ? "": $pacing_monetary_values[1],
                    "interval"=>!isset($pacing_monetary_values[2]) ? "": $pacing_monetary_values[2]
                    ],
                "pacing_impression" => [
                    "type"=>!isset($pacing_impression_values[0]) ? "" : $pacing_impression_values[0],
                    "amount"=>!isset($pacing_impression_values[1]) ? "" : $pacing_impression_values[1],
                    "interval"=>!isset($pacing_impression_values[2]) ? "" : $pacing_impression_values[2]
                ],
                "remaning_budget" => $remaining_budget != null ? $remaining_budget : "",
                "daily_spent" => $redis_campaign_spnt != null && $redis_campaign_spnt > 0 ? $redis_campaign_spnt/1000000 : "",
                "daily_impressions" => $impressions != null ? $impressions : "" ,
                //"concepts" => $this->strategies->strategyconcepts->concept_id,
                "strategies"=> is_string($this->strategies) == true ? "" : $this->strategies->keyBy("id"),
                "flights" => $campaign_flights
            ];
        }
  

        $response["updated_at"] = $this->updated_at->getTimestamp();

        if($this->fields != null) {
            $comparative = $this->fields;
            $response = array_filter($response, function($k) use($comparative) {
                return in_array($k, $comparative);
            }, ARRAY_FILTER_USE_KEY);
        }
    

        return $response;
    }
}
