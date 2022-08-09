@extends('voyager::master')
@php
  
    if(env('WL_PREFIX') !="" || env('WL_PREFIX') !="0"){
        $float_wlprefix = env('WL_PREFIX').".0";
        $wlprefix = (float) $float_wlprefix*1000000;
    } else {
        $wlprefix=0;
    }
    

    if ($id > $wlprefix){
        $idNOprefixed =$id -$wlprefix;
    }else{
        $idNOprefixed = $id;
    }
 @endphp
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('voyager::compass.includes.styles')
@stop
@include('voyager::compass.includes.styles')
@section('page_header')
    <h1 class="page-title">
        <i class="voyager-bar-chart"></i>
        Strategies Reports
    </h1>
    @include('voyager::multilingual.language-selector')
@stop
@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li class="active">
        <a href="/admin"><i class="voyager-boat"></i> Dashboard</a>
    </li>
    <li class="active"><a href="/admin/campaigns">Campaigns</a></li>
    <li class="active"><a href="#">Reach Frequency</a>
                                </li>
</ol>
@stop
@section('content')
    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
            <li><a data-toggle="tab" onclick="document.location.href='/admin/strategies/reports/{{$idNOprefixed}}'" href="#commands"><i class="voyager-bar-chart"></i> Reports</a></li>
            <li class="active"><a data-toggle="tab" href="#"><i class="voyager-activity"></i>Reach & Frecuency</a></li>
            <li ><a href='/admin/strategies/special_reports/{{ $id }}' ><i class="voyager-list"></i>Audience Report</a></li>
        </ul>
        @include('voyager::alerts')
        <div class="tab-content" style="margin-top: -10px">
        <div class="analytics-container">

            <div style="width: 100%; padding: 20px;">
                <div class="panel">
                <div class="panel-body" style="width:70%">
                <h4>Summary from  <b>{{$start_date}}</b>  to  <b>{{$end_date}}</b></h4>
                @if(count($data['all']))
                <table class="table table-striped">
                    <thead> 
                        <tr class="active"> 
                            <th class="numbers" width="25%">Reach (Id)</th>
                            <th class="numbers" width="25%">Frecuency (Id)</th>
                            <th class="numbers" width="25%">Reach (IP)</th>
                            <th class="numbers" width="25%">Frecuency (IP)</th>
                        </tr> 
                    </thead>
                    <tbody> 
                            <tr class="info"> 
                                <td class="numbers"  >{{ $data['totals']['reach'] }}</td>
                                <td class="numbers"  >{{ number_format ( floatval($data['totals']['freq']),2 ) }}</td>
                                <td class="numbers"  >{{ $data['totals_ip']['reach'] }}</td>
                                <td class="numbers"  >{{ number_format ( floatval($data['totals_ip']['freq']),2 ) }}</td>
                            </tr>
                    </tbody>  
                </table>
                <br>
                <h4>Detail</h4>
                <a class="btn btn-small btn-info" target="_blank" href="/api/exports/reachFrequency?strategy_id={{ $id  }}">Export csv</a>
                <table class="table table-striped">
                    <thead> 
                        <tr class="active"> 
                            <th width="20%">Date</th>
                            <th class="numbers" width="20%">Reach (Id)</th>
                            <th class="numbers" width="20%">Frecuency (Id)</th>
                            <th class="numbers" width="20%">Reach (IP)</th>
                            <th class="numbers" width="20%">Frecuency (IP)</th>
                        </tr> 
                    </thead>
                </table>
                <div class="scrollbarhide">
                    <table class="table table-striped">
                    
                        <tbody> 
                            @foreach($data['all'] as $date =>  $total)
                                <tr> 
                                    <td width="20%" scope="row">{{$date}}</td>
                                    <td class="numbers" width="20%">{{$total['reach']}}</td> 
                                    <td class="numbers"  width="20%">{{number_format ( floatval($total['freq']),2 )}}</td>
                                    <td class="numbers"   width="20%">{{$total['reach_ip']}}</td> 
                                    <td class="numbers"  width="20%">{{number_format ( floatval($total['freq_ip']),2 )}}</td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                </div>
                @else
                <h4 >Sorry, there's no data at this moment</h4>
                @endif
                </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <style>
        .scrollbarhide{
            height: 50em;
            overflow: auto;
        }
        table {
            margin: 0 !important;
            text-allign: right;
        }

        b {
            font-weight: 600;
        }

        .numbers {
            text-align: end !important;
        }
    
    </style>

@stop