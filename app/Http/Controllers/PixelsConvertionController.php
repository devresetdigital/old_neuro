<?php

namespace App\Http\Controllers;

use App\ConversionPixel;
use DateTime;
class PixelsConvertionController extends Controller
{

    public function index(){
        $pixels  = ConversionPixel::with('Campaign')->get();
      
        $response=[];
        foreach ($pixels as $pixel) {
            $prefixed_id = $pixel->id + ($_ENV['WL_PREFIX']*1000000);
            $smart_pixel_id=  $pixel->smart_pixel_id + ($_ENV['WL_PREFIX']*1000000);
           
            $response[$prefixed_id] = $pixel->getAttributes();
            $response[$prefixed_id]['start_date'] = DateTime::createFromFormat("Y-m-d H:i:s", $pixel->start_date)->getTimestamp();
            $response[$prefixed_id]['end_date'] = DateTime::createFromFormat("Y-m-d H:i:s", $pixel->end_date)->getTimestamp();
            $response[$prefixed_id]['id'] = $prefixed_id;
            $response[$prefixed_id]['smart_pixel_id'] = $smart_pixel_id;
            $camapigns_id = [];
            foreach ($pixel->Campaign as $campaign) {
                $camapigns_id[]=$campaign->id + ($_ENV['WL_PREFIX']*1000000);
            }
            $response[$prefixed_id]['campaigns'] = implode(',',$camapigns_id);;
        }
        return $response;
    }
  
}
