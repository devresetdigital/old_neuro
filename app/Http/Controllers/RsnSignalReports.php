<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\RsnSignalCampaign;
use App\RsnXTwoItems;
use App\RsnSignalSignal;
use App\RsnSignalSoma;
use App\RsnSignalMythic;
use App\RsnSignalPathosEthos;
use App\RsnSignalPersonalState;
use Illuminate\Support\Facades\Cache;


class RsnSignalReports extends Controller
{
    /**
     * returns signals for a certain campaign
     */
    public function getSignalsByCampaign(Request $request, $id){
        
        $signals = Cache::get('signal_campaigns_'.intval($id));

        if($signals == null){
            $signals = $this->get_signals($id);
            Cache::forever('signal_campaigns_'.intval($id), $signals);
        }

        return response()->json( $signals );
    }

    public function getCampaign(Request $request, $id){
        
        $signals = RsnSignalCampaign::find($id);

        return response()->json( $signals );
    }

    private function get_signals($campaign_id) {

        try {
           




        } catch (\Throwable $th) {
          return [];
        }

        Cache::forget('signal_campaigns_'.intval($campaign_id));

        $campaign = RsnSignalCampaign::find($campaign_id);

        $response = [];

        if($campaign->type == "hao"){

            $signals = RsnSignalSignal::where('signal_campaign_id', $campaign_id)->get();

            
    
            foreach ($signals as $key => $signal) {
    
                $aux['data'] = $signal->toArray();
    
                $personal_state = RsnSignalPersonalState::where('signal_id', $signal->id)->select('id','name','average')->get()->toArray();
                $aux['need_states'] = $personal_state;
    
                $soma_semantics = RsnSignalSoma::where('signal_id', $signal->id)->select('id','name','average')->get()->toArray();
                $aux['soma_semantics'] = $soma_semantics;
    
                $mythic_narratives = RsnSignalMythic::where('signal_id', $signal->id)->select('id','name','score')->get()->toArray();
                $aux['mythic_narratives'] = $mythic_narratives;
    
                $pathos_ethos = RsnSignalPathosEthos::where('signal_id', $signal->id)->select('id','name','score')->get()->toArray();
                $aux['pathos_ethos'] = $pathos_ethos;
    
                $response[] = $aux;
            }

        } else {
            $response = RsnXTwoItems::where('signal_campaign_id', $campaign_id)->get()->toArray();
        }
        return $response;

    }



}
