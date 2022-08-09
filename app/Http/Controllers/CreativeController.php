<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Creative;
use App\Concept;
use App\Advertiser;
use App\CreativesAttribute;
use App\CreativeDisplay;
use App\CreativeVideo;
use App\TrustScan;
use App\Http\Resources\Creative as CreativeResource;
use Illuminate\Support\Carbon;
use App\Http\Helpers\CloneHelper;
use App\Http\Helpers\TrustHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class CreativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $organization = isset($_GET['organization']) ? $_GET['organization'] : false;

            $schema = Schema::getColumnListing('creatives'); 
            if(isset($_GET['hide'])){
                $fields = array_diff($schema, explode(',',$_GET['hide']));
            }else{
                $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : [];
            }

          
            $creatives = new Creative;
            if ($organization!= false){
                $organization = explode(',',$organization);
                $creatives  = $creatives->whereHas('Advertiser', function ($query) use ($organization){
                    $query->whereIn('organization_id', $organization);
                });
            }

            if(empty($fields)){
                $creatives_cache = Cache::get($_ENV["WL_PREFIX"]."_api_creatives_index");
                if( $creatives_cache != null){
                    return $creatives_cache;
                }
                $creatives = $creatives->get();
                $fieldsToReturn = $schema; 
            }else{
                $fieldsToReturn = $fields;
                if(!in_array('id',$fields)){
                    $fields[] = 'id';
                }
                $creatives = $creatives->get($fields);
            }
          
            $response =  collect($creatives)->keyBy('id')->map(function ($item) use ($fieldsToReturn) {
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
                return $response;
            });

            Cache::put($_ENV["WL_PREFIX"].'_api_creatives_index', $response, 0.1);

            return $response;

        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['message' => 'There was an error trying to return the data, please check the fields']);

        }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkStore(Request $request)
    {
      
        $advertiser = Advertiser::find(intval($request->input('advertiser')));
        $advertisers = Advertiser::where('organization_id',$advertiser->organization_id)->get()->pluck('id')->toArray();
        
        try {
            DB::beginTransaction();
            $creatives_id_list=[];
            foreach ($request->input('creatives') as $key => $creative) {


     
                if(intval($creative['concept']) > 0 ){
                    $concept = Concept::where('id', intval($creative['concept']))->first();
                }else{
                    $concept = Concept::whereIn('advertiser_id', $advertisers)->where('name',$creative['concept'])->first();
                }
                if($concept == null){
                    $concept = new Concept();
                    $concept->advertiser_id = intval($request->input('advertiser'));
                    $concept->name = $creative['concept'];
                    $concept->save();
                }

                if (intval($creative['id']) >0 ){
                    $new_creative = Creative::where('id',intval($creative['id']))->first();

                    if( $creative['creative_type_id']!= null){
                        $new_creative->creative_type_id = $creative['creative_type_id'];
                    }
                    if( $creative['name']!= null){
                        $new_creative->name = $creative['name'];
                    }
                    if( $creative['click_url']!= null){
                        $new_creative->name = $creative['click_url'];
                    }
                    if( $creative['3pas_tag_id']!= null){
                        $new_creative->{'3pas_tag_id'} = $creative['3pas_tag_id'];
                    }
                    if( $creative['landing_page']!= null){
                        $new_creative->landing_page = $creative['landing_page'];
                    }

                    if( $creative['start_date']){
                        $aux = explode("-", $creative['start_date']);
                        $aux =$aux[2] ."-". $aux[0] ."-". $aux[1];
                        $new_creative->start_date =$aux;
                    }
                    if( $creative['end_date']){
                        $aux = explode("-", $creative['end_date']);
                        $aux =$aux[2] ."-". $aux[0] ."-". $aux[1];
                        $new_creative->end_date = $aux;
                    }
                } else {
                    $new_creative = new Creative();
                    $new_creative->creative_type_id = $creative['creative_type_id'];
                    $new_creative->name = $creative['name'];
                    $new_creative->click_url = $creative['click_url'];
                    $new_creative->{'3pas_tag_id'} = $creative['3pas_tag_id'];
                    $new_creative->landing_page = $creative['landing_page'];

                    if( $creative['start_date']){
                        $aux = explode("-", $creative['start_date']);
                        $aux =$aux[2] ."-". $aux[0] ."-". $aux[1];
                        $new_creative->start_date =  $aux;
                    }
                    if( $creative['end_date']){
                        $aux = explode("-", $creative['end_date']);
                        $aux =$aux[2] ."-". $aux[0] ."-". $aux[1];
                        $new_creative->end_date = $aux;
                    }
                }
                $new_creative->secure = 1;
                $new_creative->concept_id = $concept->id;
                $new_creative->status = 0;
                $new_creative->advertiser_id = intval($request->input('advertiser'));

                $new_creative->save();

                $creatives_id_list[]=$new_creative->id;

                if($creative['creative_attributes'] !=null){
                    $creative_attributes = explode(',' , $creative['creative_attributes']);
                    $creative_attributes = array_unique($creative_attributes);
                    CreativesAttribute::where('creative_id', $new_creative->id)->delete();
                    foreach ($creative_attributes as $attribute) {
                        CreativesAttribute::create([
                            'creative_id' => $new_creative->id,
                            'attribute_id' => $attribute
                        ]);
                    }
                }
               
    
                if($new_creative->creative_type_id == 1){
                    CreativeDisplay::where('creative_id', $new_creative->id)->delete();
                    CreativeDisplay::create([
                        'creative_id' => $new_creative->id,
                        'mime_type' => null,
                        'mraid_required' => 0,
                        'tag_type' => null,
                        'ad_format' => null,
                        'ad_width' => $creative['ad_width'],
                        'ad_height' => $creative['ad_height'],
                        'tag_code' => $creative['tag_code'],
                        '3rd_tracking' =>  $creative['3rd_tracking']
                    ]);
                }
                if( $new_creative->creative_type_id == 2){
                    CreativeVideo::where('creative_id', $new_creative->id)->delete();
                    CreativeVideo::create([
                        'creative_id' => $new_creative->id,
                        'vast_code' =>  $creative['vast_code'],
                        'skippable' =>$creative['skippable']
                    ]);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            dd($e->getTrace());
            dd($e->getMessage());
            return [
                'error' => true,
                'message' =>'there was an error trying to proccess bulk creatives, please try again'
            ];
        }  
        DB::commit();

        return [
            'error' => false,
            'message' =>'bulk upload process completed successfully'
        ];
    }
       /**
     * clones a creative and redirect to the list
     */
    public function clone($id){
        try {
            DB::beginTransaction();
            $creative =  Creative::find(intval($id));

            $clone = CloneHelper::cloneCreative($creative);
            if($clone['status']){
                DB::commit();
                return redirect('/admin/creatives');
            }

            DB::rollBack();
            Log::info($clone['message']);
            return "there was an error trying to clone the creative, please try again";
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return "there was an error trying to clone the creative, please try again";
        }
       

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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

        $prefixed_id = intval($_ENV["WL_PREFIX"]) + intval($id);
        $cache_data = Cache::get($prefixed_id."_api_creative_show");
        if( $cache_data != null){
            return $cache_data;
        }

        //Get Campaign by id
        $creative = Creative::leftjoin('creatives_display', 'creatives.id', '=', 'creatives_display.creative_id')
                                ->leftjoin('creatives_video', 'creatives.id', '=', 'creatives_video.creative_id')
                                ->where('id','=',$id)->first();
        


        $schema = Schema::getColumnListing('creatives'); 
        array_push($schema,        
                    "mime_type","mraid_required","tag_type",
                    "ad_format", "ad_width", "ad_height",
                    "tag_code", "3rd_tracking", "vast_code",
                    "skippable");
  
        $fields = $schema;

        if(isset($_GET['hide'])){
            $fields = array_diff($schema, explode(',',$_GET['hide']));
        }else{
            $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : [];
        }

        $creative->fields = $fields;

        //Return Single Campaign as a Resource
        $response =  new CreativeResource($creative);

        Cache::put($prefixed_id.'_api_creative_show', $response, 0.1);

        return $response;
    }

    public function sendForBulkScan(){

        if(!array_key_exists('ids', $_GET )){
            return 'ids query needed';
        }

        $ids = explode(',', $_GET['ids']);

        foreach($ids as $id){

            if(intval($id) == 0 ){
                continue;
            }

            TrustScan::where('creative_id', $id)->delete();
    
            $saveScan = new TrustScan();
            $saveScan->provider = 'TMT';
            $saveScan->status = 'PENDING';
            $saveScan->last_scan = Carbon::now();
            $saveScan->creative_id = $id;
            $saveScan->save();
        }

        
        return response()->json('success' , 200);
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

    public function changeStatus($id){
        $creative = Creative::find(intval($id));
      
        if(!$creative){
            return response()->json('invalid id' , 401);
        }
        $creative->status =  $creative->status == 0 ? 1 : 0;
        $creative->save();

        if($creative->status == 1){
            TrustScan::where('creative_id', $id)->delete();

            $saveScan = new TrustScan();
            $saveScan->provider = 'TMT';
            $saveScan->status = 'PENDING';
            $saveScan->last_scan = Carbon::now();
            $saveScan->creative_id = $id;
            $saveScan->save();
        }    



        return response()->json('success' , 200);
    }
    
    public function vastPreview($id){
        $response = Cache::get($id);
        return response($response)->header('Content-Type', 'application/xml');
    }
    public function saveVastMarkup(Request $request){
        $markup = $request->input('markup');
        $key = $_ENV["WL_PREFIX"].'_markup_'.time();
        Cache::put($key, $markup, 1);
        return response()->json($key);
    }
}
