<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ConversionPixel;

class ConversionPixelController extends Controller
{


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
            //Get Iplist Details
            $pixel = ConversionPixel::find(intval($id));
    
            if(!$pixel){
                return response()->json([], 404);
            }
            $float_wlprefix = $_ENV['WL_PREFIX'].".0";
            $wlprefix = (float) $float_wlprefix*1000000;
    
            return collect([
                "id"=>  $pixel->id + $wlprefix,
                "name"=>  $pixel->name,
                "created_at"=>   $pixel->created_at->getTimestamp(),
                "updated_at"=>  $pixel->updated_at->getTimestamp(),
                "deleted_at"=> ($pixel->deleted_at) ? $pixel->deleted_at->getTimestamp() : null,
                "organization_id"=>  $pixel->organization_id + $wlprefix,
                "impression_range"=>  $pixel->impression_range,
                "click_range"=> $pixel->click_range
            ]);
    }

    /**
     * return all id and update_date recods
     */
    public function index(){

        $pixels = ConversionPixel::select('id', 'updated_at')->get();
        $reponse = []; 
        foreach($pixels as $pixel){
            $response[$pixel->id]= [
                "updated_at"=>$pixel->updated_at->getTimestamp()
            ];
        }
         return collect($response);
    }
}
