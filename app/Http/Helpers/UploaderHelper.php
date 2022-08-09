<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;
use Response;

class UploaderHelper
{

    static function formatData($data, $slug, $deleteFile=null){
        $response='[';
        $files = json_decode($data,true);

        Log::info('inicio');

        if($deleteFile!=null){
            $deletes = json_decode($deleteFile,true);
            if ($deletes!=null){
                foreach ($deletes as $key => $delete) {
                    $path = public_path('storage/'.$delete['download_link']);
                    if(file_exists($path)) {
                        unlink($path);
                    }
                }
            }
        }
        if (!file_exists(public_path('storage/'.$slug.'/'))) {
            mkdir(public_path('storage/'.$slug.'/'), 0755, true);
        }

        foreach ($files as $key => $file) {
            if(file_exists($file['path'])) {
                $filename =  explode('/', $file['path']);
                $filename = end($filename);    
                copy($file['path'], public_path('storage/'.$slug.'/'.$filename));
                $response.='{"download_link":"'.$slug.'\/'.$filename.'","original_name":"'.$file['original_name'].'"},';
                unlink($file['path']);
            }else{
                Log::error('file not exists:'.$file['path']);
            }
        }
        $response.=']';
        $response= str_replace(',]',']',$response);
        return  $response;
    }

}
