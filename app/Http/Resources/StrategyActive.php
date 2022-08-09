<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class StrategyActive extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $float_wlprefix = env('WL_PREFIX').".0";
        if((float)$float_wlprefix>0) {
            $wlprefix = (float)$float_wlprefix * 1000000;
        } else {
            $wlprefix =0;
        }
        $prefixedid = $this->id+$wlprefix;

        $pacing_impressions = explode(",",$this->pacing_impression);
        $pacing_monetary = explode(",",$this->pacing_monetary);

        if(isset($pacing_impressions[1])){ $pimpressions=$pacing_impressions[1]; } else { $pimpressions=""; }
        if(isset($pacing_monetary[1])){ $pmonetary = $pacing_monetary[1]; } else { $pmonetary=""; }

        //daily spent
        $redis_strategy_spent = Redis::get('stg_'.$prefixedid.'_spt');
        $impressions = Redis::get('stg_'.$prefixedid.'_imp');

        $daily_spent = $redis_strategy_spent != null && $redis_strategy_spent > 0 ? round($redis_strategy_spent/1000000,2) : "0";

        return [ $pmonetary == "" ? 0 : $pmonetary, $daily_spent == "" ? 0 : $daily_spent, $pimpressions, $impressions != null ? $impressions : "0" ];
    }
}