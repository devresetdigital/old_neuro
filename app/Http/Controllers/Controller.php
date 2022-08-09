<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

use App\Bid;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    public function bidderStatuses()
    {
        return view('voyager::bidderstatuses');
    }

    public function makeRequest(Request $request){

        $response = [];

        $data = $this->curlRequest($request->all());

        if($data['error']){
            return response()->json([$data]);
        }
        $response[]=$data;
        if($request->input('index')==true &&  is_array($data['data'])){
            $ids = array_keys($data['data']);
            $ids = $this->getNextScanIds($ids,3);
            foreach ($ids as $id) {
                $next = $request->all();
                $next['url'] = $next['url'].'/'.$id;
                $data = $this->curlRequest($next);
                $response[]=$data;
            }
        }
        return response()->json($response);
    }

    public function makeGetRequest(Request $request){

        $url = $request->input('url');
        //Initialize cURL.
        $ch = curl_init();

        //Set the URL that you want to GET by using the CURLOPT_URL option.
        curl_setopt($ch, CURLOPT_URL, $url);

        //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        //Execute the request.
        $data = curl_exec($ch);

        //Close the cURL handle.
        curl_close($ch);

        //Print the data out onto the page.

       
        return  $data ? $data : '';

    }

    private function curlRequest(array $request) {

        $response=[] ;

        if($request['baseurl'] ==''){
            $request['baseurl'] = $_ENV['APP_URL'];
        }
        if($request['baseurl'] =='none'){
            $request['baseurl'] ='';
        }
        $url = $request['baseurl'].$request['url'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch,CURLOPT_TIMEOUT, 3);//time in seconds
        $data = curl_exec($ch); 

        if ($data !== false) {
            $validated = $this->validateData($data,$request['validation']);
            $info = curl_getinfo($ch);
            $response = [
                'status' =>$info['http_code'],
                'executionTime' => $info['total_time'],
                'content_type' =>$info['content_type'],
                'info' => $info,
                'data' =>json_decode($data, true),
                'validated' => $validated,
                'error' =>false,
                'url' => $url
            ];
        } else {
            $response = [
                'status' =>500,
                'executionTime' => 0,
                'content_type' =>'',
                'info' => [],
                'data' =>[],
                'validated' =>false,
                'error' =>true,
                'url' => $url
            ];
        }
        curl_close($ch); 

        return $response;

    }

    private function validateData($data,$type){
        switch ($type) {
            case 'json':
                return json_decode($data) != null;
                break;
            case 'html':
                return strpos($data,'<!DOCTYPE html>') !== false;
                break;
            case 'text':
                return json_decode($data) != null || is_string($data);
                break;
            default:
                return true;
                break;
        }
    }

    private function getNextScanIds(array $ids, int $amount){

        rsort($ids);

        if(count($ids) > ($amount * 3) ) {
            $ids = array_slice($ids, intval(count($ids) / 3 * 2 )); 
        }
      
        shuffle($ids);

        $ids = array_slice($ids, 0, $amount);

        rsort($ids);

        return $ids;
    }

    public function getBid(Request $request, $id){

        $json = Bid::findOrFail($id);
        $payLoad = json_decode(request()->getContent(), true);

        $json=  json_decode($json->json, true);
      

        foreach ($json['bids'] as $key => &$value) {
            $value['bid_id']= isset($payLoad['imps'][$key]) ? $payLoad['imps'][$key]['bid_id']:'';
            $value['imp_id']= isset($payLoad['imps'][$key]) ? $payLoad['imps'][$key]['imp_id']:'';
        }

        return response($json, 200, ['Content-Type => application/json']);

    }
}
