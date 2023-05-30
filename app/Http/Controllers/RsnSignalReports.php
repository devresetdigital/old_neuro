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
use App\Rsn_x_two_items_domains;
use Illuminate\Support\Facades\DB;
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


    public function get_domains_by_item(Request $request, $id)
    {
        $query = Rsn_x_two_items_domains::where('rsn_x_two_item_id', $id)->orderBy('score', 'desc')->with('domains:id,url');

        // Si hay un valor de búsqueda, agregue una cláusula where para filtrar los resultados.
        if ($request->has('search') && !empty($request->input('search.value'))) {
            $query->whereHas('domains', function($q) use($request) {
                $q->where('url', 'LIKE', '%' . $request->input('search.value') . '%');
            });
        }

        // Obtener el número total de registros para usar en la respuesta de DataTable.
        $total = $query->count();

        // Obtener el número de registros filtrados para usar en la respuesta de DataTable.
        $filtered = $query->count();

        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        // Aplicar la paginación a la consulta.
        $data = $query->skip($start)
              ->take($rowperpage)
              ->get();

        // Transformar los resultados para la respuesta de DataTable.
        $data = $data->map(function($item) {
            return [
                'score' => $item->score,
                'url' => $item->domains->url
            ];
        });

        // Devolver la respuesta de DataTable.
        return [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ];
    }

    public function getCampaign(Request $request, $id){
        
        $signals = RsnSignalCampaign::find($id);

        return response()->json( $signals );
    }

    private function get_signals($campaign_id) {

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
            $response = RsnXTwoItems::where('signal_campaign_id', $campaign_id)->with('RsnXTwoItemsData')->get()->toArray();
        }
        return $response;

    }



}
