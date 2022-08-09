<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;
use App\Organization;

class DataManagementController extends Controller
{
    public function index()
    {
        //get reports

        $reports=json_decode(file_get_contents("http://134.209.171.185:9000/alldata"),true);
        //$reports = array();
        //print_r($reports);
        //die();

        $user = Auth::user();

        $organization = Organization::where('id', $user->organization_id)->first();

        $dmp =[];

        if($_SERVER['HTTP_HOST']=="dsp-panel.inspire.com") {
            $dmp = [
                "180byTWO" => '180x2',
                "OnSpot" => 'onspot',
                "Neuro-Programmatic" => 'neuro',
                "Inspire" => 'inspire',
                "Semcasting" => 'semcasting',
            ];
        }


        if(strpos($organization->dmps, '1') !== false){
            $dmp["180byTWO"] ='180x2';
        }
        if(strpos($organization->dmps, '2') !== false){
            $dmp["Inspire"] ='inspire';
        }
        if(strpos($organization->dmps, '3') !== false){
            $dmp["OnSpot"] ='onspot';
        }
        if(strpos($organization->dmps, '4') !== false){
            $dmp["Neuro-Programmatic"] ='neuro';
        }
        if(strpos($organization->dmps, '5') !== false){
            $dmp["Semcasting"] ='semcasting';
        }


        return Voyager::view('voyager::datamanagement', compact('reports','dmp'));
    }
}