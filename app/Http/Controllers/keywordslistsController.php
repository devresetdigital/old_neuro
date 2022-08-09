<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Keywordslist;

class keywordslistsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Keywordslist::select('id', 'updated_at')->get();
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
        //Get keywordslist Details
        $keywordslist = Keywordslist::find($id);

        if($keywordslist == null){
            return  collect([]);
        }

        $response = [];

        $files = ($keywordslist['list_file']!==null ) ?  json_decode($keywordslist['list_file'], true) : [];
        foreach($files as $file){
            // $url = $_ENV['APP_URL'] . "/storage/".$file['download_link'];
            $url = public_path('storage/'.$file["download_link"]);
            $report_json = file_get_contents($url);
            $lines = preg_split('/\r\n|\r|\n/', $report_json);
            foreach($lines as $line){
                if(!in_array($line, $response)){
                    $response[]=$line;
                }
            }
        }
        return response(implode("\r\n",$response))->header('Content-Type', 'text/plain');
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
