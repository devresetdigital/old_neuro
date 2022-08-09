<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Advertiser;


class AdvertisersController extends Controller
{

    public function index(){
        $organization = isset($_GET["organization"]) ? $_GET["organization"] : null;
        
        if($organization){
            $advertisers = Advertiser::where('organization_id',$organization)->get();
        }else{
            $advertisers = Advertiser::all();
        }
        return response()->json( $advertisers->toArray() );
    }
 
}