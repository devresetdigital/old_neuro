@extends('voyager::master')
@php
    if(env('WL_PREFIX') !="" || env('WL_PREFIX') !="0"){
        $float_wlprefix = env('WL_PREFIX').".0";
        $wlprefix = (float) $float_wlprefix*1000000;
    } else {
        $wlprefix=0;
    }

    $campaignId = intval($_GET["campaign_id"]) - intval($wlprefix);

    $idprefixed = $wlprefix+intval($campaignId); 

@endphp
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('voyager::compass.includes.styles')
@stop
@include('voyager::compass.includes.styles')
@section('page_header')
    <h1 class="page-title">
        <i class="voyager-bar-chart"></i>
        Campaign Reports
    </h1>
    @include('voyager::multilingual.language-selector')
@stop
@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li class="active">
        <a href="/admin"><i class="voyager-boat"></i> Dashboard</a>
    </li>
    <li class="active"><a href="/admin/campaigns">Campaigns</a></li>
    <li class="active"><a href="#">Audience Report</a>
                                </li>
</ol>
@stop
@section('content')
    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
    
            <li><a data-toggle="tab" href="#" onclick="document.location.href='/admin/campaigns/{{  $campaignId  }}/edit'"><i class="voyager-book"></i> Summary</a></li>
            <li><a data-toggle="tab" onclick="document.location.href='/admin/strategies_campaign/{{  $campaignId  }}'" href="#"><i class="voyager-lab"></i> Strategies</a></li>
            <!--<li ><a data-toggle="tab" onclick="document.location.href='/admin/vwireports?campaign_id={{ $_GET["campaign_id"]  }}'" href="#"><i class="voyager-people"></i> VWI</a></li>-->
            <li><a data-toggle="tab" onclick="document.location.href='/admin/creports?campaign_id={{ $campaignId  }}'" href="#commands"><i class="voyager-bar-chart"></i> Reports</a></li>
            @if(str_contains($_SERVER['SERVER_NAME'], 'inspire.com'))
                @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 5)
                    <li><a href='/admin/reach_frequency?campaign_id={{ $_GET["campaign_id"]  }}'><i class="voyager-activity"></i>Reach & Frecuency</a></li>
                @endif
            @else
            <li ><a href='/admin/reach_frequency?campaign_id={{ $idprefixed  }}'><i class="voyager-activity"></i>Reach & Frecuency</a></li>
            @endif
            <li class="active"><a data-toggle="tab" href="#"><i class="voyager-list"></i>Audience Report</a></li>
        </ul>
        @include('voyager::alerts')
        <div class="tab-content" style="margin-top: -10px">
            <div class="analytics-container">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#contextual">Contextual</a>
                    </li>
                    <!--
                    <li>
                        <a data-toggle="tab" href="#contextual_neuro">Neuro</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#contextual_categories">Categories</a>
                    </li>
                    -->
                    <li>
                        <a data-toggle="tab" href="#audience_neuro">Neuroprogrammatic</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#audience_semcasting">Semcasting</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#audience_180By2">180By2</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#audience_onspot">Onspot</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="contextual" class="tab-pane fade in active">
                        <a id="export_KEYWORDS" class="btn btn-small btn-info" target="_blank" href="">Export csv</a>
                        <div id="dataTable_KEYWORDS"></div>
                    </div>
                    <!-- <div id="contextual_neuro" class="tab-pane fade in">
                        <a id="export_NEURO" class="btn btn-small btn-info" target="_blank" href="">Export csv</a>
                        <div id="dataTable_NEURO"></div>
                    </div>
                    <div id="contextual_categories" class="tab-pane fade in">
                         <a id="export_CATEGORIES" class="btn btn-small btn-info" target="_blank" href="">Export csv</a>
                        <div id="dataTable_CATEGORIES"></div>
                    </div> -->
                    <div id="audience_neuro" class="tab-pane fade in">
                        <a id="export_NEURO" class="btn btn-small btn-info" target="_blank" href="">Export csv</a>
                        <div id="dataTable_NEURO"></div>
                    </div>
                    <div id="audience_semcasting" class="tab-pane fade in">
                        <a id="export_SEMCASTING" class="btn btn-small btn-info" target="_blank" href="">Export csv</a>
                        <div id="dataTable_SEMCASTING"></div>
                    </div>
                    <div id="audience_180By2" class="tab-pane fade in">
                        <a id="export_180x2" class="btn btn-small btn-info" target="_blank" href="">Export csv</a>
                        <div id="dataTable_180x2"></div>
                    </div>
                    <div id="audience_onspot" class="tab-pane fade in">
                        <a id="export_ONSPOT" class="btn btn-small btn-info" target="_blank" href="">Export csv</a>
                        <div id="dataTable_ONSPOT"></div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <style>
        .scrollbarhide{
            height: 15em;
            overflow: auto;
        }
        table {
            margin: 0 !important;
        }
    
        ::-webkit-scrollbar {
    display: none;
}
    </style>
    <script>

    function genDatatable(Id, type, context){
            //Groupby
            $('#'+Id).html('');
            let html = `
                <table id="table_${Id}" class="table table-hover dataTable no-footer" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                </table>            
            `;

            $('#'+Id).html(html);

            let ajaxUrl = '/api/get_path_interactive_data';

            let table = $('#table_'+Id).DataTable({
                "processing": true,
                "pageLength": 10,
                "serverSide": true,
                "deferRender": true,
                "columnDefs": [
                        { className: "text-right", "targets": [ 1,2] }
                ],
                "ajax": {
                    "url": ajaxUrl,
                    "type": "POST",
                    "data": {
                        campaign_id :  {{$_GET['campaign_id']}},
                        type: type,
                        context: context
                    }
                }
            });




    }

    function setHrefLink(Id, type, context, search){
        let search_str = $('#'+search).val() !== undefined ? $('#'+search).val() : '';
        $("#"+Id).attr('href','/api/exports/pi?context='+context+'&type='+type+'&campaign_id={{$_GET['campaign_id']}}&search='+search_str);
    }

    window.onload = function() {
        genDatatable('dataTable_KEYWORDS', 'CONTEXTUAL' ,'KEYWORDS');
        // genDatatable('dataTable_NEURO', 'CONTEXTUAL','NEURO'); // old
        // genDatatable('dataTable_CATEGORIES', 'CONTEXTUAL','CATEGORIES'); // old

        genDatatable('dataTable_NEURO', 'AUDIENCE' ,'neuro');
        genDatatable('dataTable_SEMCASTING', 'AUDIENCE','semcasting');
        genDatatable('dataTable_180x2', 'AUDIENCE','180x2');
        genDatatable('dataTable_ONSPOT', 'AUDIENCE','onspot');
        //////////////////////////////////////////////////////////////////////////////
        setHrefLink('export_KEYWORDS', 'CONTEXTUAL' ,'KEYWORDS','dataTable_KEYWORDS_search');
        // setHrefLink('export_NEURO', 'CONTEXTUAL','NEURO','dataTable_NEURO_search'); // old
        // setHrefLink('export_CATEGORIES', 'CONTEXTUAL','CATEGORIES','dataTable_CATEGORIES_search'); // old

        setHrefLink('export_NEURO', 'AUDIENCE' ,'neuro','dataTable_NEURO_search');
        setHrefLink('export_SEMCASTING', 'AUDIENCE','semcasting','dataTable_SEMCASTING_search');
        setHrefLink('export_180x2', 'AUDIENCE','180x2','dataTable_180x2_search');
        setHrefLink('export_ONSPOT', 'AUDIENCE','onspot','dataTable_ONSPOT_search');
        

        $('#table_dataTable_KEYWORDS_filter label input').on( 'focus', function () {
            this.setAttribute( 'id', 'dataTable_KEYWORDS_search' );
            $("#dataTable_KEYWORDS_search").keyup(function(o){ 
                setHrefLink('export_KEYWORDS', 'CONTEXTUAL' ,'KEYWORDS','dataTable_KEYWORDS_search');
            });
        });

        // $('#table_dataTable_NEURO_filter label input').on( 'focus', function () {
        //     this.setAttribute( 'id', 'dataTable_NEURO_search' );
        //     $("#dataTable_NEURO_search").keyup(function(o){ 
        //         setHrefLink('export_NEURO', 'CONTEXTUAL','NEURO','dataTable_NEURO_search');
        //     });
        // }); // old
        // $('#table_dataTable_CATEGORIES_filter label input').on( 'focus', function () {
        //     this.setAttribute( 'id', 'dataTable_CATEGORIES_search' );
        //     $("#dataTable_CATEGORIES_search").keyup(function(o){ 
        //         setHrefLink('export_CATEGORIES', 'CONTEXTUAL','CATEGORIES','dataTable_CATEGORIES_search');
        //     });
        // }); // old
        ////////////////////////////////////////////////////////////////////////////////////////////
        $('#table_dataTable_NEURO_filter label input').on( 'focus', function () {
            this.setAttribute( 'id', 'dataTable_NEURO_search' );
            $("#dataTable_NEURO_search").keyup(function(o){ 
                setHrefLink('export_NEURO', 'AUDIENCE' ,'neuro','dataTable_NEURO_search');
            });
        });
        $('#table_dataTable_SEMCASTING_filter label input').on( 'focus', function () {
            this.setAttribute( 'id', 'dataTable_SEMCASTING_search' );
            $("#dataTable_SEMCASTING_search").keyup(function(o){ 
                setHrefLink('export_SEMCASTING', 'AUDIENCE','semcasting','dataTable_SEMCASTING_search');
            });
        });
        $('#table_dataTable_180x2_filter label input').on( 'focus', function () {
            this.setAttribute( 'id', 'dataTable_180x2_search' );
            $("#dataTable_180x2_search").keyup(function(o){ 
                setHrefLink('export_180x2', 'AUDIENCE','180x2','dataTable_180x2_search');
            });
        });
        $('#table_dataTable_ONSPOT_filter label input').on( 'focus', function () {
            this.setAttribute( 'id', 'dataTable_ONSPOT_search' );
            $("#dataTable_ONSPOT_search").keyup(function(o){ 
                setHrefLink('export_ONSPOT', 'AUDIENCE','onspot','dataTable_ONSPOT_search');
            });
        });
    }
</script>
@stop