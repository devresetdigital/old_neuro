@extends('voyager::master')
@php
if($_ENV['WL_PREFIX'] !="" || $_ENV['WL_PREFIX'] !="0"){
$float_wlprefix = $_ENV['WL_PREFIX'].".0";
$wlprefix = (float) $float_wlprefix*1000000;
} else {
$wlprefix=0;
}
@endphp
@section('page_header')
<div class="container-fluid">
    <h1 class="page-title">
        <i class="voyager-people"></i> Forecasting
    </h1>
</div>
@stop
@section('content')
@php
if(isset($_GET["creative_id"])){
if($_GET["creative_id"]==2000038){
$total_loc = "572";
$total_vis="2899";
$total_imp ="16662";
$avarage_cpm="13.1";
}
if($_GET["creative_id"]==2000037){
$total_loc = "854";
$total_vis="13042";
$total_imp ="391106";
$avarage_cpm="7.2";
}
} else {
$total_loc = "876";
$total_vis="15419";
$total_imp ="407768";
$avarage_cpm="7.4";
}
@endphp
<!-- ======================= LINE AWESOME ICONS ===========================-->
<link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/line-awesome.min.css">
<link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/simple-line-icons.css">
<!-- ======================= DRIP ICONS ===================================-->
<link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/dripicons.min.css">
@include('voyager::compass.includes.styles')
@include('voyager::alerts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.5/jquery.csv.min.js"></script>
<div class="page-content ">
    <div class="analytics-container">
        <div class="panel">
            <button style="margin-left: 20px;" type="button" class="btn btn-primary" onclick="document.getElementById('mainiframe').src='http://uirst.resetdigital.co/forecasting_inventory'">INVENTORY</button> 
            <button type="button" class="btn btn-primary" onclick="document.getElementById('mainiframe').src='http://uirst.resetdigital.co/forecasting_audience'">AUDIENCE</button>
            <a type="button" class="btn btn-primary" href="/admin/app_domain_quality">QUALITY</a>
            <iframe id="mainiframe" width="100%" height="1800" src="http://uirst.resetdigital.co/forecasting_inventory" frameborder="0"></iframe>
        </div>
    </div>
</div>
    <script src="../dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
    <script src="../dsp-demo/dsp-demo/assets/js/components/countUp-init.js"></script>

    @stop