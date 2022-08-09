<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class EventerStatusController extends Controller
{
    public function index(Request $request)
    {
      $eventer = $request->has('id') ? $request->id : "01";
      $groupBy = $request->has('groupBy') && $request->groupBy != null ? $request->groupBy : "ssp";
      $headers = ['Content-type: application/json'];
      $curl = curl_init();
      $curlUrl = "http://e-us-east{$eventer}.resetdigital.co:9000/evts?project=impressions&groupby={$groupBy}&format=json";
      $curlOptions = array(
        CURLOPT_URL => $curlUrl,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 45,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => false,
      );
      curl_setopt_array($curl, $curlOptions);
      $response = curl_exec($curl);
      $error = curl_error($curl);
      $respuesta = collect(json_decode($response))
        ->map(function ($item, $key) {
          return array_slice($item, 0, 6);
      });

      if ($error === TRUE) {
        dd('error');
      }

      return response()->json($respuesta);
    }
 
}