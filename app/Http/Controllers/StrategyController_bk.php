<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Strategy;
use App\Campaign;
use App\Http\Resources\Strategy as StrategyResource;
use App\Http\Helpers\CloneHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StrategyController extends Controller
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
            $schema = Schema::getColumnListing('strategies');
            if(isset($_GET['hide'])){
                $fields = array_diff($schema, explode(',',$_GET['hide']));
            }else{
                $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : [];
            }

            $strategies = new Strategy;
            if ($organization!= false){
                $organization = explode(',',$organization);
                $campaigns  = Campaign::whereHas('Advertiser', function ($query) use ($organization){
                    $query->whereIn('organization_id', $organization);
                })->get(['id'])->pluck('id')->toArray();

                if(empty($campaigns)){
                    return response()->json([]);
                }
                $strategies = $strategies->whereIn('campaign_id', $campaigns);
            }

            if(empty($fields)){
                $strategies = $strategies->get();
                $fieldsToReturn = $schema;
            }else{
                $fieldsToReturn = $fields;
                if(!in_array('id',$fields)) {
                    $fields[] = 'id';
                }
                $strategies = $strategies->get($fields);
            }

            return collect($strategies)->keyBy('id')->map(function ($item) use ($fieldsToReturn) {
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
                if(isset($response['date_start'])) {
                    $response['date_start'] =  strtotime($item->date_start);
                }
                if(isset($response['date_end'])) {
                    $response['date_end'] = strtotime($item->date_end);
                }
                return $response;
            });

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
     *
     */
    public function dmpinfo(Request $request){
        $url = "http://104.131.2.141:9000/dmpinfo?" . $_SERVER['QUERY_STRING'];
        $ch = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // set TIMEOUT
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//time in seconds
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);

        return response()->json(['data'=> json_decode($result,true)]);

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
        //Get Campaign by id
        $strategy = Strategy::with("StrategiesLocationsCity","StrategiesLocationsRegion","StrategiesLocationsCountry","StrategiesPmp","StrategiesSitelist.Sitelist","StrategiesIplist.Iplist","StrategiesSsp","StrategiesTechnologiesBrowser","StrategiesTechnologiesDevice","StrategiesTechnologiesIsp","StrategiesTechnologiesOs","StrategiesInventoryType","StrategiesGeofencing")
            ->where('id','=',$id)
            ->first();

        $schema = Schema::getColumnListing('strategies');
        $fields = $schema;

        if(isset($_GET['hide'])){
            $fields = array_diff($schema, explode(',',$_GET['hide']));
        }else{
            $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : [];
        }

        $strategy->fields = $fields;

        //Return Single Campaign as a Resource
        return new StrategyResource($strategy);
    }

    public function geofencing($id)
    {
        //Get Campaign by id
        $strategy = Strategy::with("StrategiesLocationsCity","StrategiesLocationsRegion","StrategiesLocationsCountry","StrategiesPmp","StrategiesSitelist.Sitelist","StrategiesIplist.Iplist","StrategiesSsp","StrategiesTechnologiesBrowser","StrategiesTechnologiesDevice","StrategiesTechnologiesIsp","StrategiesTechnologiesOs","StrategiesInventoryType","StrategiesGeofencing")
            ->where('id','=',$id)
            ->first();

        //Geofencing
        $geofencing = array();
        $geofencing_inc_exc = "";
        if(isset($strategy->StrategiesGeofencing)) {
            $geofencing = $strategy->StrategiesGeofencing["geolocation"];
            $geofencing = json_decode($geofencing);
            $geofencing_inc_exc = $strategy->StrategiesGeofencing["inc_exc"];
        }
        return [
            "geofencing" => ["inc_exc"=> (is_null($geofencing_inc_exc)=== true || $geofencing_inc_exc=="")? 3 : $geofencing_inc_exc,"data"=> (is_null($geofencing)=== true) ? [] : $geofencing]
        ];
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

            foreach ($request->input('strategies') as $key => $strategy) {

                if(intval($strategy['campaing']) > 0 ){
                    $campaing = Campaign::where('id', intval($strategy['campaing']))->first();
                }else{
                    $campaing = Campaign::whereIn('advertiser_id', $advertisers)->where('name',$strategy['campaing'])->first();
                }
                if($campaing == null){
                    $campaing = new campaing();
                    $campaing->advertiser_id = intval($request->input('advertiser'));
                    $campaing->name = $strategy['campaing'];
                    $campaing->save();
                }
                $new_strategy = new Strategy();





                $new_strategy->save();

            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return [
                'error' => true,
                'message' =>'there was an error trying to proccess bulk strategies, please try again'
            ];
        }
        DB::commit();
        return [
            'error' => false,
            'message' =>'bulk upload process completed successfully'
        ];
    }
}
