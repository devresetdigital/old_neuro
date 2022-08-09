<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Concept;
use App\Creative;
use App\Advertiser;
use App\Organization;
use App\Http\Resources\Concept as ConceptResource;
use App\Http\Helpers\CloneHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Auth;
class ConceptController extends Controller
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

            $schema = Schema::getColumnListing('concepts'); 
            if(isset($_GET['hide'])){
                $fields = array_diff($schema, explode(',',$_GET['hide']));
            }else{
                $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : [];
            }

          
            $concept = new Concept;
            if ($organization!= false){
                $organization = explode(',',$organization);
                $concept  = $concept->whereHas('Advertiser', function ($query) use ($organization){
                    $query->whereIn('organization_id', $organization);
                });
            }

            if(empty($fields)){
                $data_cache = Cache::get($_ENV["WL_PREFIX"]."_api_concepts_index");
                if( $data_cache != null){
                    return $data_cache;
                }

                $concept = $concept->get();
                $fieldsToReturn = $schema; 
            }else{
                $fieldsToReturn = $fields;
                if(!in_array('id',$fields)){
                    $fields[] = 'id';
                }
                $concept = $concept->get($fields);
            }

            $response = collect($concept)->keyBy('id')->map(function ($item) use ($fieldsToReturn) {
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

            Cache::put($_ENV["WL_PREFIX"].'_api_concepts_index', $response, 0.1);

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

    public function getByAdvertiser(Request $request)
    {
        $id = $request->input('id');
        $user = Auth::user();

        if($id==0) {
            if($user->role_id == 1 ) {
                $concept = Concept::select('id','name')->withCount('creatives')->get();
            } else {
                $advertisers = Advertiser::select('id')->where('organization_id', '=', $user->organization_id)->get()->pluck('id')->toArray();
                $concept = Concept::select('id','name')->whereIn('advertiser_id',$advertisers)->withCount('creatives')->get();
            }
        }else{
            $concept = Concept::where('advertiser_id', $id)->select('id','name')->withCount('creatives')->get();
        }
  
        return $concept->toArray();
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
        $cache_data = Cache::get($prefixed_id."_api_concept_show");
        if( $cache_data != null){
            return $cache_data;
        }

        //Get Campaign by id
        $concept = Concept::with("creatives:id,concept_id,status")
        ->where('id','=',$id)->first();


        $schema = Schema::getColumnListing('concepts'); 
        $schema[]='creatives';
        $fields = $schema;

        if(isset($_GET['hide'])){
            $fields = array_diff($schema, explode(',',$_GET['hide']));
        }else{
            $fields = isset($_GET['fields']) ? explode(',',$_GET['fields']) : [];
        }

        $concept->fields = $fields;

        //Return Single Campaign as a Resource
        $response = new ConceptResource($concept);

        Cache::put($prefixed_id.'_api_creative_show', $response, 0.1);

        return $response;
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

    public function checkExistance(Request $request) {
        
        if($request->has('advertiser')) {
           
            $advertiser = Advertiser::find(intval($request->input('advertiser')));
            $advertisers = Advertiser::where('organization_id',$advertiser->organization_id)->get()->pluck('id')->toArray();
           
            $error = false;
            $message = [];
            $coincidences = [];
            $concepts_field = $request->input('concepts');

            $concepts_ids = array_key_exists('integers', $concepts_field ) ?  array_unique($concepts_field['integers']): [];
            $concepts_names =  array_key_exists('names', $concepts_field ) ? array_unique($concepts_field['names']) : [];

            $new_concepts = 0;

            foreach ($concepts_names as $key => $name) {
                $concepts = Concept::whereIn('advertiser_id', $advertisers)->where('name',$name)->get()->toArray();
          
                if(count($concepts) == 0){
                    $new_concepts++;
                }elseif (count($concepts) > 1) {
                    $error = true;
                    $message[]="duplicated concepts: " . $name;
                    $coincidences[]=$concepts;
                }
            }
            foreach ($concepts_ids as $key => $id) {
                $concepts = Concept::whereIn('advertiser_id', $advertisers)->where('id',$id)->get()->toArray();
                if(count($concepts) == 0){
                    $error = true;
                    $message[]="invalid concept id: ". $id;
                }
            }


            $creatives_field = $request->input('creatives');
            if($creatives_field!= null){
                $creatives =  array_key_exists('ids', $creatives_field ) ?  array_unique($creatives_field['ids']): [];
                $check_creatives = Creative::whereIn('id', $creatives)->get()->pluck('id')->toArray();
                $creatives_diff = array_diff($creatives, $check_creatives);
    
                foreach ($creatives_diff as $key => $diff) {
                    $error = true;
                    $message[]="invalid creative id: ". $diff . "change it or remove it to create a new creative";
                }
            }


            return [
                "coincidences"=> $coincidences,
                "new"=>$new_concepts,
                "message" => $message,
                "error" => $error,
            ];
        }else{
            return false;
        }
    }
}
