<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;
use Carbon\Carbon;


use Flow\Config as FlowConfig;
use Flow\Request as FlowRequest;
use Flow\ConfigInterface;
use Flow\RequestInterface;

class UploaderController extends Controller
{


    public function postUpload()
    {

        if (!file_exists(public_path('storage/uploader/'))) {
            mkdir(public_path('storage/uploader/'), 0777, true);
        }

        $temp_dir=public_path('storage/uploader/');
        $destination=public_path('storage/uploader/');

        $file_name ='test';
        error_log('Documento 22');

        $request = new FlowRequest();
        $config = new FlowConfig(array(
            'tempDir' => $temp_dir, //With write access
        ));

        $file = new \Flow\File($config, $request);
        $response = response()->json('', 200);

        $destination = $destination . strtotime("now").$request->getFileName();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            error_log('Documento GET');
            if (!$file->checkChunk()) {
                error_log('Documento GET2');
                return response()->json('', 204);
            }
        } else {
            if ($file->validateChunk()) {
                $file->saveChunk();
                error_log('Documento VaLido !!!');

            } else {
                error_log('Documento INVALIDO');
                // error, invalid chunk upload request, retry
                return response()->json('Error in chunck', 400);
            }
        }
        if ($file->validateFile() && $file->save($destination)) {
            error_log('EXITO!');
            $response = response()->json([
                'path'=>$destination,
                'original_name'=> $request->getFileName()
            ], 200);
        }
        return $response;
    }


    public function publicLinkUpload(Request $request){
        if(!$request->has('path')) {
            return response()->json('Missing Path param', 400);
        }

        $filename = md5(uniqid(rand(), true)).'.txt';

        $url = $request->input('path');

        $destination=public_path('storage/uploader/').$filename;

        if(copy($url, $destination)){
            return response()->json([
                'path'=>$destination,
                'original_name'=> $filename,
                'size'=>filesize($destination)
            ], 200);
        }else{
            return response()->json('invalid link', 400);
        }

    }
}
