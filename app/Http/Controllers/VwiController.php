<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vwi;
use App\Http\Resources\Vwi as VwiResource;

class VwiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //Get Custom Data Details
        $vwi = Vwi::find($id);

        /*$sitelist_file = json_decode($sitelist->file,1);
        $domains = file_get_contents("http://".$_SERVER["HTTP_HOST"]."/storage"."/".$sitelist_file[0]["download_link"]);
        $domains = explode("\r\n",$domains);
        foreach ($domains as $domain) {
            $ldomain[]=$domain;
        }*/
        return new VwiResource($vwi);
        //return $vwi;
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
