<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IabCountry;

class ImportLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $locations = json_decode(file_get_contents("http://dsp.resetdigital.co/lab/Countries.json"), true);

        //print_r($locations);

        foreach ($locations as $key => $val) {
            $country = new IabCountry;
            $country->country = $val;
            $country->code = $key;
            $country->save();
            echo $key."\n";
            /*foreach ($val as $rkey => $rval) {
                echo "\t\t".$rkey."\n";
                foreach ($rval as $ckey => $cval) {
                    echo "\t\t\t\t".$cval."\n";
                }
            }*/
        }

    }

}