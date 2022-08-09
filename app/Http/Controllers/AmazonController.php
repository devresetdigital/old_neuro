<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\S3Helper;

class AmazonController extends Controller
{

    public function upload(Request $request){
        dd(S3Helper::uploadToS3($request->input('file'), 'resetdigital.png' ));
    }   

    public function index(){
   
    }
 
}