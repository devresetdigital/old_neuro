<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Iplist;

class IplistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Iplist::select('id', 'updated_at')->get();
        $reponse = []; 
        foreach($models as $model){
            $response[$model->id]= [
                "updated_at"=>$model->updated_at->getTimestamp()
            ];
        }
         return collect($response);
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
            $iplist = Iplist::find(intval($id));
    
            if(!$iplist){
                return response()->json([], 404);
            }

            $iplist_file = json_decode($iplist->list_file,1);

            //$domains = file_get_contents("http://".$_SERVER["HTTP_HOST"]."/storage"."/".$iplist_file[0]["download_link"]);
            $domains = file_get_contents(public_path('storage/'.$iplist_file[0]["download_link"]));
            
            $domains = explode("\r\n",$domains);
            foreach ($domains as $domain) {
                $ldomain[]=$domain;
            }

            if(array_key_exists('txt', $_GET)){
                return response(implode("\r\n",$ldomain))->header('Content-Type', 'text/plain');
            }
            return collect($ldomain);
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
}
