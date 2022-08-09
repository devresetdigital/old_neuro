@extends('voyager::master') @section('content')
<link rel="stylesheet" href="{{ asset('css/lib/bootstrap-multiselect.css') }}" type="text/css" />
<style>
    .select2-container {
        border: 1px solid #aaa !important;
        border-radius: 4px !important;
    }

    .page-header h1 {
        margin: 0;
        display: inline-block;
        padding: 7px 20px 7px 0;
    }

    .list-group {
        display: flex;
        flex-direction: column;
        padding-left: 0;
        margin-bottom: 0;
    }

    .badge.badge-circle {
        border-radius: 10%;
        width: 5em;
        height: 2.5em;
        font-size: 0.8em;
        font-weight: 600;
        line-height: 1.6;
        padding: 4px 5px;
        vertical-align: baseline;
    }

    .list-group-item {
        border: 1px solid rgba(210, 221, 234, .3);
        font-size: .875rem;
    }

    .form-control {
        border-color: #dfe7f3;
    }

    .form-control {
        border: 1px solid rgba(120, 141, 180, .3);
    }

    #adModalPreview {
        text-align: -webkit-center;
    }

    .modal-title {
        text-align: center;
    }

    .checked-daypart {
        background-image: url(https://upload.wikimedia.org/wikipedia/commons/thumb/b/bd/Checkmark_green.svg/1180px-Checkmark_green.svg.png);
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 20px;
        width: 20px;
    }



    .multiselect-container {
        max-height: 20em;
        overflow-y: scroll;
    }

    .principal-card {
        overflow: unset !important;
    }

    #adModalNeedstates {
        max-height: 17em;
        overflow-y: scroll;
    }

    .toggle {
        width: 100% !important;
    }

    label.toggle-off{
        background-color: cadetblue !important;
        color: white !important;
    }

    .card.top-resonances {
        background-color: #736bc7;
        color: #fff;
        height: 100%;
    }

    .top-resonances h5.card-header {
        border-bottom: none !important;
    }

    .top-resonances li {
        color: #76838f;
    }

    .top-resonances ul {
        background-color: #fff;
        height: 100%;
    }

    .row-same-height {
        display: flex;
        flex-wrap: wrap;
    }

    .groupby-container {
        position: absolute;
        bottom: 8em;
        right: 3em;
        display: flex;
    }

    .groupby-container .dropdown-toggle {
        width: 25em;
    }

    .chart-card {
        min-height: 24em;
    }

    #scrollTopBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: #736ac7;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
    }

    div.preview-container> :first-child {
        width: 100% !important;
        ;
        box-shadow: 0 10px 40px 0 rgba(18, 106, 211, .07), 0 2px 9px 0 rgba(18, 106, 211, .06) !important;
        ;
        border-radius: .25rem !important;
        ;
        height: auto !important;
        ;
        padding-top: 75% !important;
        ;
    }

    .spinner {
   position: absolute;
   left: 50%;
   top: 24%;
   height:60px;
   width:60px;
   margin:0px auto;
   -webkit-animation: rotation .6s infinite linear;
   -moz-animation: rotation .6s infinite linear;
   -o-animation: rotation .6s infinite linear;
   animation: rotation .6s infinite linear;
   border-left:6px solid rgba(0,174,239,.15);
   border-right:6px solid rgba(0,174,239,.15);
   border-bottom:6px solid rgba(0,174,239,.15);
   border-top:6px solid rgba(0,174,239,.8);
   border-radius:100%;
   z-index: 1;
}

@-webkit-keyframes rotation {
   from {-webkit-transform: rotate(0deg);}
   to {-webkit-transform: rotate(359deg);}
}
@-moz-keyframes rotation {
   from {-moz-transform: rotate(0deg);}
   to {-moz-transform: rotate(359deg);}
}
@-o-keyframes rotation {
   from {-o-transform: rotate(0deg);}
   to {-o-transform: rotate(359deg);}
}
@keyframes rotation {
   from {transform: rotate(0deg);}
   to {transform: rotate(359deg);}
}
TD {
    font-size:10px !important;
    padding: 8px 10px !important;
    font-weight: bold !important;
}



.highcharts-figure .chart-container {
    width: 100%;
    height: 200px;
    float: left;
    display: flex !important;
}

.highcharts-figure,
.highcharts-data-table table {
    width: 100%;
    margin: 0 auto;
    display: flex !important;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
   
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}

.highcharts-credits{
    display:none !important;
}

.page-content{
    max-width: 1920px;
    margin: auto;

}
.signals-card-body{
    height: -webkit-fill-available;
}
</style>
<div class="page-content container-fluid" >
    @include('voyager::alerts')
    <div class="content" style="margin-left: 3.5em; margin-bottom: -10px">
        <header class="page-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h1 class="separator" style="font-size: 22px;">Neuro-Programmatic™</h1>
                </div>
            </div>
        </header>
    </div>
    <div style="width: 100%; padding: 10px 60px 0px 60px;">
        <div class="row">
            <div class="col">
                <div class="card principal-card">
                    <div class="card-body p-0">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs">
                                    <li class="">
                                        <a data-toggle="" href="/admin/level1_report">Level 1</a>
                                    </li>
                                    <li class="active">
                                        <a data-toggle="active" href="#">HAO</a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="" href="/admin/X2_report">X2</a>
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <div class="form-group col-sm-12">
                                        <div class="form-group">
                                            <label for="organization">Organization</label>
                                            <select class="form-control select2 select2-hidden-accessible " name="organization" id="organization" style="width: 100%">
                                                <option value="">None</option>
                                                @foreach($organizations as $organization)
                                                    <option value="{{$organization['id']}}">{{$organization['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="advertiser">Advertiser</label>
                                            <select class="form-control select2 select2-hidden-accessible " name="advertiser" id="advertiser" style="width: 100%">
                                                <option value="">None</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="Campaign">Campaigns</label>
                                            <select class="form-control select2 select2-hidden-accessible" name="Campaign"
                                                    id="campaignSelector">
                                                <option selected="selected" value="0">Select Campaign</option>
                                            </select>
                                        </div>
                                      
                                    </div>
                                    <div class="col-sm-12">
                                        <label>Filters</label>
                                        <div class="card " style="overflow: unset; min-height: 8em;">
                                            <div class="card-body">
                                            <div class="spinner" id="filters-container-loading"></div>
                                                <div id="filters-container">
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Signal</label><br>
                                                        <select class="form-control " name="as" id="filterSignal"
                                                            multiple="multiple" style="width: 50%">
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Ontological Goal States</label><br>
                                                        <select class="form-control select2-multiple" name="as"
                                                            id="filterNeedStates" multiple>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Soma-Semantics</label><br>
                                                        <select class="form-control " name="as" id="filterSomaSemantic"
                                                            multiple="multiple">
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Mythic Narratives</label><br>
                                                        <select class="form-control " name="as" id="filterMythicNarrative"
                                                            multiple="multiple">
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Pathos/Ethos</label><br>
                                                        <select class="form-control " name="as" id="filterPathosEthos"
                                                            multiple="multiple">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="">&nbsp;</div>
                                    <div class="col-sm-12">
                                        <label>Signals</label>
                                        <div id='reportWarning'></div>
                                        <div class="card" id="signalsCardContainer">
                                            <div class="card" style="">
                                                <div class="card-body" style="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">&nbsp;</div>

                                    <div class="col-sm-12" >
                                        <label>Top rated</label>
                                        <div class="card">
                                            <div class="card" style="">
                                                <div class="card-body" style="min-height: 8em;">
                                                    <div class="spinner" id="topRalatedResonancesContainer-loading"></div>
                                                    <div class="row row-same-height" id="topRalatedResonancesContainer">
                                                        <div class="col-sm-3">
                                                            <div class="card top-resonances">
                                                                <h5 class="card-header">Top 3 Ontological Goal States</h5>
                                                                <ul class="list-group" id="top-ns"></ul>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="card  top-resonances">
                                                                <h5 class="card-header">Top 3 Soma Semantic</h5>
                                                                <ul class="list-group" id="top-sose"></ul>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="card  top-resonances">
                                                                <h5 class="card-header">Top 3 Mythic Narratives</h5>
                                                                <ul class="list-group" id="top-my"></ul>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="card top-resonances text-center" >
                                                                <h5 class="card-header">Ontological Suitability Score™</h5>
                                                                <p class="card-title text-white font-size-20 font-w-20 " id="best-signal-name">
                                                                
                                                                </p>
                                                                <p id="best-signal-score"
                                                                    class="card-title text-white font-size-20 font-w-20"
                                                                    data-count="0">78.28 %</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button onclick="topFunction()" id="scrollTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script src="{{ asset('js/resonances_dashboard.js') }}"></script>

 
    @stop
</div>