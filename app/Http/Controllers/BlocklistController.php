<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Blacklist As Blocklist;
use Illuminate\Support\Facades\Log;

class BlocklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {



        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['message' => 'There was an error trying to return the data, please check the params']);

        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getByType(Request $request, $type)
    {
        try {
            $model = new Blocklist;
            if(isset($_GET['status'])){
                $model = $model->where('status',$_GET['status']);
            }
            $blocklists = $model->where('type',intval($type))->get(['list_file','status'])->toArray();

            $response = [];
            foreach($blocklists as $list){
                $files = ($list['list_file']!==null ) ?  json_decode($list['list_file'], true) : [];
                foreach($files as $file){
                    $url = $_ENV['APP_URL'] . "/storage/".$file['download_link'];
                    $report_json = file_get_contents($url);
                    $lines = preg_split('/\r\n|\r|\n/', $report_json);
                    foreach($lines as $line){
                        if(!in_array($line, $response)){
                            $response[]=$line;
                        }
                    }
                }
            }
            return response(implode("\r\n",$response))->header('Content-Type', 'text/plain');
        }  catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['message' => 'There was an error trying to return the data, please check the params']);

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
            //Get Sitelist Details
            $sitelist = Sitelist::find(intval($id));

            if(!$sitelist){
                return response()->json([], 404);
            }

            $sitelist_file = json_decode($sitelist->list_file,1);
            // $domains = file_get_contents("http://".$_SERVER["HTTP_HOST"]."/storage"."/".$sitelist_file[0]["download_link"]);
            $domains = file_get_contents(public_path('storage/'.$sitelist_file[0]["download_link"]));

            $domains = explode("\r\n",$domains);
            foreach ($domains as $domain) {
                $ldomain[]=$domain;
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
