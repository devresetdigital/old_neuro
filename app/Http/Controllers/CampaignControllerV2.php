<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Campaign;
use App\Vwi;
use App\VwiLocation;
use App\Strategy;
use App\Http\Resources\StrategyV2 as StrategyResource;
use App\Http\Resources\CampaignV2 as CampaignResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class CampaignControllerV2 extends Controller
{
    public function index()
    {
      $campaigns = Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.status', 'campaigns.pacing_monetary', 'campaigns.pacing_impression', 'campaigns.updated_at')
      ->get();
      return CampaignResource::collection($campaigns)->keyBy('id');
    }
    public function strategies_by_campaign($id)
    { 
      $strategies = Strategy::select('strategies.id', 'strategies.campaign_id','strategies.name','strategies.status' , 'strategies.pacing_monetary', 'strategies.pacing_impression')->where('campaign_id', $id)->get();
      return $strategies;
    }
    public function show($id)
    {
      //PREFIX
      if($id>1000000){
        $float_wlprefix = $_ENV['WL_PREFIX'].".0";
        $wlprefix = (float) $float_wlprefix*1000000;
        $old_id = $id;
        $id = $id-$wlprefix;
        $prefix = $old_id - $id;
        if($id>=1000000){
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            die();
        }
      }
      $campaign = DB::table('campaigns')->where('id', $id)->first();
      return $campaign->name;

    }
    public function status()
    {
      return view('campaigns_status');
    }
}
