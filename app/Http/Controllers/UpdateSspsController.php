<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sitelist;
use App\StrategiesSsp;

class UpdateSspsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $new_ssps = array('sonobi','imonomy','advangelists','brave','nativeads','ninthdecimal','mobuppsdisplay','m51','advinteo','mobupps','smaato','adx','advenue','mopub','tappx','acuityads','fyber','smartrtb','adcolony','web3','yieldnexus','beachfront','mobfox','loopme','disciplinex','trion','arbigoprem','streamkeytv');

        foreach ($new_ssps as $ssp) {
            $strategy_ssp = array("");
            $strategy_ssp = StrategiesSsp:: where('ssp_id', 'LIKE', '%' . $ssp . '%')->get(); //->where('name','LIKE','%'.$term.'%')

            foreach ($strategy_ssp as $st) {

                $newssps = str_replace($ssp.",",$ssp."!sm,", $st->ssp_id);
                //UPDATE STRATEGY SSP
                StrategiesSsp::where('strategy_id', $st->strategy_id)->delete();

                StrategiesSsp::create([
                    "strategy_id" => $st->strategy_id,
                    "ssp_id" => $newssps
                ]);

            }

            /* if($strategy_ssp["ssp_id"]!="") {
                $newssps = str_replace($ssp.",",$ssp."!sm,", $strategy_ssp->ssp_id);
                //UPDATE STRATEGY SSP
                StrategiesSsp::where('strategy_id', $strategy_ssp->strategy_id)->delete();

                StrategiesSsp::create([
                    "strategy_id" => $strategy_ssp->strategy_id,
                    "ssp_id" => $newssps
                ]);

                //echo $strategy_ssp;
            }*/
            //$strategy_ssp->ssp_id = str_replace($ssp,$ssp."!sm", $strategy_ssp->ssp_id);
           // echo "hola";
           /* if() {

                //UPDATE STRATEGY SSP
                StrategiesSsp::where('strategy_id', $strategy_ssp->strategy_id)->delete();
                if ($request->get("ssps")) {
                    foreach ($request->get('ssps') as $ssp) {
                        $ssps .= $ssp . ",";
                    }
                    $cssps = count($request->get("ssps"));
                }
                //if($countries!="") {
                StrategiesSsp::create([
                    "strategy_id" => $id,
                    "ssp_id" => rtrim($ssps, ",")
                ]);
            }*/
        }

        //$strategy_ssps = StrategiesSsp:: whereIn('id', $new_ssps)->get();

        return "";
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
