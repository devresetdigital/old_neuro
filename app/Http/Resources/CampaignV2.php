<?php

namespace App\Http\Resources;

use App\Advertiser;
use App\CampaignsBudgetFlight;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class CampaignV2 extends JsonResource
{
    public function toArray($request)
    {
      $pacing_monetary_values = explode(",",$this->pacing_monetary);
      $pacing_impression_values = explode(",",$this->pacing_impression);
      //Prefix
      $float_wlprefix = env('WL_PREFIX').".0";
      if((float)$float_wlprefix>0) {
          $wlprefix = (float)$float_wlprefix * 1000000;
      } else {
          $wlprefix =0;
      }
      return [
          $this->mergeWhen($this->name!=null, [
              "campaign_id" => $this->id,
              "status" => $this->status,
              "name" => $this->name,
              "monetary_goal"=>!isset($pacing_monetary_values[2]) ? "": $pacing_monetary_values[2],
              "impression_goal"=>!isset($pacing_impression_values[0]) ? "" : $pacing_impression_values[0]
          ]),
          "updated_at" => $this->updated_at->getTimestamp()
      ];
    }
}
