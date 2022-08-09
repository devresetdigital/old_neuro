<?php
namespace App\Http\Controllers;

use App\RsnCampaigns;
use App\RsnAdNetworkDaypart;
use App\RsnNetworks;
use App\RsnDaypart;
use App\RsnAds;
use App\RsnResonances;
use App\RsnAdNeedstate;
use App\RsnNeedstates;
use App\RsnDayparts;
use App\RsnPrograms;
use App\RsnProgramGenres;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use DB;
use Log;
use App\Organization;
use App\RsnSignalCampaign;

class RsnController extends Controller
{
   

    /**
     * 
     */
    public function report()
    {
        if(!Auth::user()){
            return redirect('/admin/login');
        }
        $campaigns= [];

        $organizations = Organization::all();

        return Voyager::view('voyager::rsn.index', compact('campaigns','organizations'));
    }

        /**
     * 
     */
    public function x2_report()
    {
        if(!Auth::user()){
            return redirect('/admin/login');
        }
        $campaigns= [];

        $organizations = Organization::all();

        return Voyager::view('voyager::rsn.x2_index', compact('campaigns','organizations'));
    }

    /**
     * 
     */
    public function dashboard()
    {
        if(!Auth::user()){
            return redirect('/admin/login');
        }
        $campaigns = RsnSignalCampaign::all();

        $organizations = Organization::all();

        return Voyager::view('voyager::rsn.dashboard', compact('campaigns','organizations'));
    }
    
    public function getByAdvertiser(){
        $email = $_GET["email"];

        if(strpos(strtolower( $email), 'equinox.com')){
            $campaigns = RsnCampaigns::where(DB::raw('lower(name)'), 'LIKE', '%equinox%')->get()->toArray();
        }
        elseif(strpos(strtolower( $email), 'troybilt') || strpos(strtolower( $email), 'mtllc')){ //holmgren@mtllc.com.
            $campaigns = RsnCampaigns::where(DB::raw('lower(name)'), 'LIKE', '%troybilt%')->get()->toArray();
        }else{
            $advertiser = isset($_GET["advertiser"]) ? $_GET["advertiser"] : null;
            if($advertiser){
                $campaigns = RsnCampaigns::where('advertiser_id',$advertiser)->get()->toArray();
            }else{
                $campaigns = RsnCampaigns::get()->toArray();
            }
        }
 
        return response()->json( $campaigns );
    }

    public function getSignalByAdvertiser(){

        $advertiser = isset($_GET["advertiser"]) ? $_GET["advertiser"] : null;
        $type = isset($_GET["type"]) ? $_GET["type"] : 'hao';

        if($advertiser != null){
            $campaigns = RsnSignalCampaign::where('advertiser_id',$advertiser)->where('type',$type)->get()->toArray();
        }else{
            $campaigns = RsnSignalCampaign::get()->toArray();
        }
        return response()->json( $campaigns );
    }
    
    /**
    * 
    */
   public function getCampaign($id)
   {
        //Campaigns
        $campaigns = RsnCampaigns::where('id', intval($id))->with('RsnAds.RsnNetworks', 
        'RsnAds.RsnPrograms','RsnAds.RsnProgramGenres', 'RsnAds.RsnNeedstates')->first();
        if ($campaigns){
            $campaigns = $campaigns->toArray();
        }
        return response()->json($campaigns,200);
   } 

    public function createCampaign(Request $request){

        if (!$request->has('name') ||  $request->input('name')== ""){
            return response()->json([
                'error' => true,
                'message' => 'missing or empty field name'
            ],200);
        }
        if (!$request->has('organization') ||  $request->input('organization')== ""){
            return response()->json([
                'error' => true,
                'message' => 'missing or empty field organization'
            ],200);
        }
        if (!$request->has('advertiser') ||  $request->input('advertiser')== ""){
            return response()->json([
                'error' => true,
                'message' => 'missing or empty field advertiser'
            ],200);
        }


        $campaign = new RsnCampaigns();
        $campaign->name = $request->input('name');
        $campaign->organization_id = $request->input('organization');
        $campaign->advertiser_id = $request->input('advertiser');
        $campaign->save();

        
        return response()->json([
            'error' => false,
            'message' => "campaign was saved successfully",
            'data' => $campaign->toArray() 
        ],200);


    }
    public function createAd(Request $request){

        if (!$request->has('adName') ||  $request->input('adName')== ""){
            return response()->json([
                'error' => true,
                'message' => 'missing or empty field adName'
            ],200);
        }

        $newAd = new RsnAds();
        $newAd->campaign_id = intval($request->input('campaingId'));
        $newAd->name = $request->input('adName');
        $newAd->tag_preview = $request->input('adPreview');
        $newAd->save();

        $needstateData = $request->input('needstateData');

        foreach ($needstateData as $key => $needstate) {
            $nst = RsnNeedstates::firstOrCreate(['name'=> $needstate['needstate'], 'type' => 'needstate' ]);
            $newAdNeedstate = new RsnAdNeedstate();
            $newAdNeedstate->ad_id = $newAd->id;
            $newAdNeedstate->value = floatval(str_replace('%','',$needstate['value']));
            $newAdNeedstate->needstate_id = $nst->id; 
            $newAdNeedstate->save();
        }

        $motivationData = $request->input('motivationData');

        foreach ($motivationData as $key => $motivation) {
            $nst = RsnNeedstates::firstOrCreate(['name'=> $motivation['name'], 'type' => 'motivation' ]);
            $newMotivation = new RsnAdNeedstate();
            $newMotivation->ad_id = $newAd->id;
            $newMotivation->value = floatval(str_replace('%','',$motivation['value']));
            $newMotivation->needstate_id = $nst->id; 
            $newMotivation->save();
        }

        $daypartData = $request->input('daypartData');
   
        foreach ($daypartData as $key => $daypart) {
           
            if (array_key_exists('daypart', $daypart)) {
                $dayp= RsnDayparts::firstOrCreate([
                    'name'=> $daypart['daypart'],
                    'description'=> $daypart['daypart']
                ]);
                $net= RsnNetworks::firstOrCreate([
                    'name'=> $daypart['network'],
                ]);
    
                $newdaypart = new RsnAdNetworkDaypart();
                $newdaypart->ad_id = $newAd->id;
                $newdaypart->resonance_score = floatval(str_replace('%','',$daypart['value']));
                $newdaypart->daypart_id = $dayp->id; 
                $newdaypart->network_id = $net->id; 
                $newdaypart->save();
            }
        }


        $newAd->need_state_count = $newAd->needstatesCount;
        $newAd->dayparts_count = $newAd->DaypartsCount;
        $newAd->sales_lift = json_encode($request->input('liftData'));

        $newAd->save();

        return response()->json([
            'error' => false,
            'message' => "ad was saved successfully",
            'data' => $newAd->toArray() 
        ],200);
    }

    /**
     * 
     */
    public function deleteAd(Request $request, $adId){

        $response = $this->deleteAdData($adId);

        return response()->json($response,200);
    }


    private function deleteAdData($adId){

        try {
            RsnAdNetworkDaypart::where('ad_id', intval($adId))->delete();
            RsnAdNeedstate::where('ad_id', intval($adId))->delete();
            RsnResonances::where('ad_id', intval($adId))->delete();
            RsnAds::where('id', intval($adId))->delete();
        } catch (\Throwable $th) {
            return [
                'error' => true, 
                'message' => "there was an error, please try again",
                'data'=> $adId
            ];
        }


        return [
            'error' => false, 
            'message' => "ad was deleted successfully",
            'data'=> $adId
        ];
    }
    public function delete($campaignId){
        $ads = RsnAds::where("campaign_id", intval($campaignId))->get()->toArray();
        foreach ($ads as $key => $ad) {
            $this->deleteAdData($ad["id"]);
        }
        RsnCampaigns::where('id', intval($campaignId))->delete();

        return redirect('admin/rsn_campaigns');
    }

    public function edit($campaignId){
        $campaign = RsnCampaigns::where('id', intval($campaignId))->with('RsnAds')->first();
        $campaign->RsnAds = $campaign->RsnAds->map(function ($aux){
            return [
                "id" => $aux->id,
                "name" => $aux->name,
                "organization_id" =>  $aux->organization_id,
                "advertiser_id" =>  $aux->advertiser_id,
            ];
        });
      
        $campaign = $campaign->toArray();
        $campaign["RsnAds"] = $campaign["RsnAds"]->toArray();
        unset($campaign["rsn_ads"]);
        $organizations = Organization::all();
        return Voyager::view('voyager::rsn.create', compact('campaign','organizations'));
    }

    public function fillAdResonance(Request $request, $adId){
        
        if (!$request->has('resonance')){
            return response()->json([
                'error' => true,
                'message' => 'missing or empty field resonance'
            ],200);
        }

        $ad = RsnAds::findOrFail(intval($adId));
        $adTagUpdated = false;

        $resonances = json_decode($request->input('resonance'),true);
        $headers = array_shift($resonances);
        try {
            foreach ($resonances as  $resonance) {
                if(intval($resonance[0]) <= 0){continue;}
                $newResonance = new RsnResonances();
                $newResonance->ad_id = intval($adId);
                $daypart = [];

                foreach ($headers as $key => $header) {
                    switch (trim($header)) {
                        case 'Program Title':
                            $aux = RsnPrograms::firstOrCreate(['name'=> $resonance[$key]]);
                            $newResonance->program_id = $aux->id;
                            $newResonance->program_name = $aux->name;
                            break;
                        case 'Program Genre':
                            $aux = RsnProgramGenres::firstOrCreate(['name'=> $resonance[$key]]);
                            $newResonance->program_genre_id = $aux->id;
                            $newResonance->program_genre_name = $aux->name;
                            break;
                        case 'Network':
                            $aux = RsnNetworks::firstOrCreate(['name'=> $resonance[$key]]);
                            if($aux->type == ''){
                                $aux->type = $resonance[$key+1];
                                $aux->save();
                            }
                            $newResonance->network_id = $aux->id;
                            $newResonance->network_type = $aux->type;
                            $newResonance->network_name = $aux->name;
                            break;
                        case '"MATCH:':
                            $newResonance->tags_match = intval($resonance[$key]);
                            break;
                        case '# DTags in Ad':
                           
                            if (!$adTagUpdated){
                                $ad->tags = $resonance[$key];
                                $ad->save();
                                $adTagUpdated=true;
                            }
                            break;
                        default:
                            if(
                                trim($header)!='#  Tags' &&
                                trim($header)!='UniqueID' &&
                                trim($header)!='Ad Selection'
                            ){
                                $aux= RsnDayparts::firstOrCreate([
                                    'name'=> trim($header),
                                    'description'=> trim($header)
                                ]);
                                if ($resonance[$key] == "YES"){
                                    $daypart [$aux->id] = true ;
                                }else{
                                    $daypart [$aux->id] = false ;
                                }
                            }
                            break;
                    }
                } 
            
                $newResonance->needstate_match = intval($resonance[count($resonance)-1]);
                $newResonance->resonance_score =floatval(str_replace('%','',$resonance[count($resonance)-2]));
                $newResonance->dayparts = json_encode($daypart);
                $newResonance->save();
            }

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'error' => true,
                'message' => "something went wrong, please try again",
            ],200);
        }

        $ad->programs_genre_count = $ad->ProgramGenresCount;
        $ad->programs_count = $ad->ProgramsCount;
        $ad->networks_count = $ad->NetworksCount;
        $ad->save();

        return response()->json([
            'error' => false,
            'message' => "ad resonances was saved successfully",
            'data' =>$ad->toArray()
        ],200);
    }
    
    /**
     * 
     */
    public function index()
    {

        //phpinfo();
        //Campaigns
        $campaigns = RsnCampaigns::get()->toArray();
     
        return Voyager::view('voyager::rsn.campaigns', compact('campaigns'));
    } 
        /**
     * 
     */
    public function create()
    {
        $organizations = Organization::all();
        return Voyager::view('voyager::rsn.create',compact('organizations'));
    } 


    public function getByDaypart(Request $request, $id){
        $ads = RsnAds::where('campaign_id', intval($id))->pluck('id')->toArray();
        $data = RsnAdNetworkDaypart::whereIn('ad_id', $ads)->with('RsnNetworks','RsnAds', 'RsnDayparts')->get();
        $data = $data->map(function ($aux){
            return [
                "ad_id" => $aux->ad_id,
                "network_id" => $aux->network_id,
                "network_name" => $aux->RsnNetworks->name,
                "ad_name" => $aux->RsnAds->name,
                "daypart_id" => $aux->daypart_id,
                "daypart_name" => $aux->RsnDayparts->name,
                "resonance" => $aux->resonance_score
            ];
        });

        $resumedData = [];
        $resumedkeys = [];

        foreach ($data as $key => $value) {
            $resumedkeys[$value['daypart_name']] = 0;
        }

        foreach ($data as $key => $value) {
            if (!array_key_exists($value['ad_id'], $resumedData)){
                $resumedData[$value['ad_id']] = [
                    "keys" => [$value['ad_id'].'_'.$value['daypart_id']],
                    "ad_id" => $value['ad_id'],
                    "ad_name" => $value['ad_name'],
                    "resonances" => $resumedkeys,
                ];
            }
            if ($resumedData[$value['ad_id']]["resonances"][$value['daypart_name']] == 0){
                $average =  floatval($value['resonance']);
            } else {
                $average = floatval($resumedData[$value['ad_id']]["resonances"][$value['daypart_name']] + $value['resonance'])/2;
            }
            $resumedData[$value['ad_id']]["resonances"][$value['daypart_name']] = $average;
           
        }

        return response()->json([
            'all' => $data,
            'resumedkeys' => $resumedkeys,
            'resumed' => $resumedData
        ],200);
    }
    /**
     * 
     */
    public function getByNetwork(Request $request){

        $id = $request->input('campaign_id');

        $ads = RsnAds::where('campaign_id', intval($id))->pluck('id')->toArray();

        $networks =[];
        if($request->has('networks_ids')){
            $networks =  $request->input('networks_ids');
        }

        $data = RsnAdNetworkDaypart::whereIn('ad_id', $ads)->whereIn('network_id', $networks)->with('RsnNetworks','RsnAds', 'RsnDayparts')->get();
        $data = $data->map(function ($aux){
            return [
                "ad_id" => $aux->ad_id,
                "network_id" => $aux->network_id,
                "network_name" => $aux->RsnNetworks->name,
                "ad_name" => $aux->RsnAds->name,
                "daypart_id" => $aux->daypart_id,
                "daypart_name" => $aux->RsnDayparts->name,
                "resonance" => $aux->resonance_score
            ];
        });

        $resumedData = [];
        $resumedkeys = [];

        foreach ($data as $key => $value) {
            $resumedkeys[$value['network_name']] = 0;
        }

        foreach ($data as $key => $value) {
            if (!array_key_exists($value['ad_id'], $resumedData)){
                $resumedData[$value['ad_id']] = [
                    "ad_id" => $value['ad_id'],
                    "ad_name" => $value['ad_name'],
                    "resonances" => $resumedkeys,
                ];
            }
            if ($resumedData[$value['ad_id']]["resonances"][$value['network_name']] == 0){
                $average =  floatval($value['resonance']);
            } else {
                $average = floatval($resumedData[$value['ad_id']]["resonances"][$value['network_name']] + $value['resonance'])/2;
            }
            $resumedData[$value['ad_id']]["resonances"][$value['network_name']] = $average;
           
        }

        return response()->json([
            'all' => $data,
            'resumedkeys' => $resumedkeys,
            'resumed' => $resumedData
        ],200);
    } 
    

    public function calculatePrediction($id){
        $ad = RsnAds::findOrFail($id);
        $ad->sales_lift = json_encode($ad->calculateSaleLift());
        $ad->save();
        return json_decode($ad->sales_lift,true);
    }
    
    public function getTopNetworks(Request $request){

        $ads_ids =[];
        $networks_ids =[];

        if($request->has('ads_ids')){
            $ads_ids =  json_decode($request->input('ads_ids'),true);
        }   

        if($request->has('networks_ids')){
            $networks_ids = json_decode($request->input('networks_ids'),true);
        }

        if($request->has('network_types_ids')){
            $network_types_ids = json_decode($request->input('network_types_ids'),true);;
            $networks_ids = RsnNetworks::whereIn('id', $networks_ids)
            ->whereIn('type', $network_types_ids)->pluck('id')->toArray();
        }


        if($request->has('type') && $request->input('type')=='NETWORK_TYPE'){   
            $data = RsnResonances::
            selectRaw('AVG(resonance_score) net_average, network_type, ad_id')->with('RsnNetworks')
            ->whereIn('ad_id', $ads_ids)->whereIn('network_id', $networks_ids)
            ->groupBy('ad_id', 'network_type')
            ->orderBy('net_average', 'desc')
            ->orderBy('ad_id', 'desc')
            ->orderBy('network_type', 'desc');
        }else{
            $data = RsnResonances::selectRaw('AVG(resonance_score) net_average, network_id, ad_id')->with('RsnNetworks')
            ->whereIn('ad_id', $ads_ids)->whereIn('network_id', $networks_ids)
            ->groupBy('network_id', 'ad_id')
            ->orderBy('net_average', 'desc')
            ->orderBy('ad_id', 'desc')
            ->orderBy('network_id', 'desc');
        }
 

        if($request->has('limit')){   
            $data = $data->limit($request->input('limit'));
        } 
        return response()->json($data->get()->toArray(),200);    
        
    }

    public function getTopPrograms(Request $request){

        $ads_ids =[];
        $networks_ids =[];

        if($request->has('ads_ids')){
            $ads_ids =  json_decode($request->input('ads_ids'),true);
        }   

        if($request->has('programs_ids')){
            $programs_ids =  json_decode($request->input('programs_ids'),true);;
        }    

        if($request->has('program_genres_ids')){
            $program_genres_ids =  json_decode($request->input('program_genres_ids'),true);;
        }


        if($request->has('type') && $request->input('type')=='PROGRAM_TITLE'){   
            $data = RsnResonances::
            selectRaw('AVG(resonance_score) net_average, program_id, ad_id')->with('RsnPrograms')
            ->whereIn('ad_id', $ads_ids)->whereIn('program_id', $programs_ids)
            ->groupBy('ad_id', 'program_id')
            ->orderBy('net_average', 'desc')
            ->orderBy('ad_id', 'desc')
            ->orderBy('program_id', 'desc');
        }else{
            $data = RsnResonances::
            selectRaw('AVG(resonance_score) net_average, program_genre_id, ad_id')->with('RsnProgramGenres')
            ->whereIn('ad_id', $ads_ids)->whereIn('program_genre_id', $program_genres_ids)
            ->groupBy('ad_id', 'program_genre_id')
            ->orderBy('net_average', 'desc')
            ->orderBy('ad_id', 'desc')
            ->orderBy('program_genre_id', 'desc');
        }

        if($request->has('limit')){   
            $data = $data->limit($request->input('limit'));
        } 

        $response['programs']= $data->get()->toArray();

        $daypartData = RsnAdNetworkDaypart::
        selectRaw('AVG(resonance_score) net_average, ad_id, daypart_id')->with('RsnDayparts')
        ->whereIn('ad_id', $ads_ids)
        ->groupBy('ad_id', 'daypart_id')
        ->orderBy('net_average', 'desc')
        ->orderBy('ad_id', 'desc')
        ->orderBy('daypart_id', 'desc')->limit(3); 

        $response['dayparts'] = $daypartData->get()->toArray();

        return response()->json($response,200);    
        
    }

    public function getTopAd(Request $request){

        $ads_ids =[];
        $networks_ids =[];

        if($request->has('ads_ids')){
            $ads_ids =  json_decode($request->input('ads_ids'),true);
        }   
        if($request->has('networks_ids')){
            $networks_ids = json_decode($request->input('networks_ids'),true);
        }   
        if($request->has('network_types_ids')){
            $network_types_ids = json_decode($request->input('network_types_ids'),true);;
            $networks_ids = RsnNetworks::whereIn('id', $networks_ids)
            ->whereIn('type', $network_types_ids)->pluck('id')->toArray();
        }   
        if($request->has('programs_ids')){
            $programs_ids =  json_decode($request->input('programs_ids'),true);;
        }    
        if($request->has('program_genres_ids')){
            $program_genres_ids =  json_decode($request->input('program_genres_ids'),true);;
        }
    

        $data = RsnResonances::
        selectRaw('AVG(resonance_score) net_average, ad_id')
        ->whereIn('ad_id', $ads_ids)
        ->whereIn('network_id', $networks_ids)
        ->whereIn('program_id', $programs_ids)
        ->whereIn('program_genre_id', $program_genres_ids)
        ->groupBy('ad_id')
        ->orderBy('ad_id', 'desc');
   

        if($request->has('limit')){   
            $data = $data->limit($request->input('limit'));
        } 
        return response()->json($data->get()->toArray(),200);    
        
    }
    /**
    * 
    */
   public function getResonance(Request $request){

    $ads_ids =[];
    $networks_ids =[];
    $programs_ids =[];
    $program_genres_ids =[];

    if($request->has('ads_ids')){
        $ads_ids =  json_decode($request->input('ads_ids'),true);
    }   
    if($request->has('networks_ids')){
        $networks_ids = json_decode($request->input('networks_ids'),true);
    }   
    if($request->has('network_types_ids')){
        $network_types_ids = json_decode($request->input('network_types_ids'),true);;
        $networks_ids = RsnNetworks::whereIn('id', $networks_ids)
        ->whereIn('type', $network_types_ids)->pluck('id')->toArray();
    }   
    if($request->has('programs_ids')){
        $programs_ids =  json_decode($request->input('programs_ids'),true);;
    }    
    if($request->has('program_genres_ids')){
        $program_genres_ids =  json_decode($request->input('program_genres_ids'),true);;
    }


    $data = RsnResonances::selectRaw('AVG(resonance_score) resonance_average')
        ->whereIn('ad_id', $ads_ids)
        ->whereIn('network_id', $networks_ids)
        ->whereIn('program_id', $programs_ids)
        ->whereIn('program_genre_id', $program_genres_ids)->orderBy('resonance_score', 'desc')
        ->first()->toArray();
  
    return response()->json($data,200);    

   }
   /**
    * 
    */
    public function getResonancePaginated(Request $request){

        $ads_ids =[];
        $networks_ids =[];
        $programs_ids =[];
        $program_genres_ids =[];
    
        if($request->has('ads_ids')){
            $ads_ids =  json_decode($request->input('ads_ids'),true);
        }   
        if($request->has('networks_ids')){
            $networks_ids = json_decode($request->input('networks_ids'),true);
        }   
        if($request->has('network_types_ids')){
            $network_types_ids = json_decode($request->input('network_types_ids'),true);;
            $networks_ids = RsnNetworks::whereIn('id', $networks_ids)
            ->whereIn('type', $network_types_ids)->pluck('id')->toArray();
        }   
        if($request->has('programs_ids')){
            $programs_ids =  json_decode($request->input('programs_ids'),true);;
        }    
        if($request->has('program_genres_ids')){
            $program_genres_ids =  json_decode($request->input('program_genres_ids'),true);;
        }

        $daypartLabels = $request->input('daypartLabels');
  
        $dayparts = RsnDayparts::whereIn('name', $daypartLabels)->get()->toArray();
        $daypart_labels = [];
        foreach ($dayparts as $day) {
            $daypart_labels[$day['id']] =$day['name'];
        }

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
       

        if(empty($request->input('search.value'))){
            $dataResonance = RsnResonances::
                whereIn('ad_id', $ads_ids)
            ->whereIn('network_id', $networks_ids)
            ->whereIn('program_id', $programs_ids)
            ->whereIn('program_genre_id', $program_genres_ids)->with('RsnAds')
            ->offset($start)
            ->limit($limit)
            ->orderBy('resonance_score', 'desc')
            ->get();

            $totalFiltered = RsnResonances::whereIn('ad_id', $ads_ids)
                          ->whereIn('network_id', $networks_ids)
                          ->whereIn('program_id', $programs_ids)
                          ->whereIn('program_genre_id', $program_genres_ids)->count();

        }else{
            $search = $request->input('search.value'); 
            $dataResonance = RsnResonances::
                where('network_name','LIKE',"%{$search}%")
                ->orWhere('program_genre_name', 'LIKE',"%{$search}%")
                ->orWhere('program_name', 'LIKE',"%{$search}%")
                ->orWhere('network_type', 'LIKE',"%{$search}%")
            ->whereIn('ad_id', $ads_ids)
            ->whereIn('network_id', $networks_ids)
            ->whereIn('program_id', $programs_ids)
            ->whereIn('program_genre_id', $program_genres_ids)->with('RsnAds')
            ->offset($start)
            ->limit($limit)
            ->orderBy('resonance_score', 'desc')
            ->get();

            $totalFiltered = RsnResonances::whereIn('ad_id', $ads_ids)
                        ->whereIn('network_id', $networks_ids)
                        ->whereIn('program_id', $programs_ids)
                        ->whereIn('program_genre_id', $program_genres_ids)->count();

        }
        




        $data = array();
        if(!empty($dataResonance)) {
            foreach ($dataResonance as $resonance) {
                $daypartValues = json_decode($resonance->dayparts,true);
                $nestedData['Ads'] = $resonance->RsnAds->name;
                $nestedData['Network Name'] = $resonance->network_name;
                $nestedData['Network Type'] = $resonance->network_type;
                $nestedData['Program Genre'] = $resonance->program_genre_name;
                $nestedData['Program Title'] = $resonance->program_name;
                foreach($daypart_labels as $key => $day){
                    if ($daypartValues[$key]){
                        $nestedData[$day] = '<div class="checked-daypart"></div>';
                    }else{
                        $nestedData[$day] = '';
                    }
                }
                $nestedData['Tags'] = $resonance->RsnAds->tags;
                $nestedData['Tags Matched'] = $resonance->tags_match;
                $nestedData['Resonance Avg'] = floatVal($resonance->resonance_score);
                
                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalFiltered),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data );
            
        return response()->json($json_data,200);    
    
       }
       
    
    

   public function getNeedstateByAd(Request $request, $id){
        $data = RsnAdNeedstate::Where('ad_id', intval($id))->with('RsnNeedstates', 'RsnAds')->get();
        $data = $data->map(function ($aux){
            return [
                "ad_id"=> $aux->ad_id,
                "tag_preview"=> $aux->RsnAds->tag_preview,
                "needstate_id"=> $aux->needstate_id,
                "needstate_name"=> $aux->RsnNeedstates->name,
                "value"=>  $aux->value
            ];
        });
        return response()->json($data,200);
   }

}