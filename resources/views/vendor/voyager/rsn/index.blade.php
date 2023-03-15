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

</style>
<div class="page-content container-fluid">
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
                                    <li class="active">
                                        <a data-toggle="active" href="#">Level 1</a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="" href="/admin/HAO-AI_Dashboard">HAO</a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="" href="/admin/X2_report">X2</a>
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <div class="form-group col-sm-12">
                                        <div class="card">
                                            <div class="card-header">Upload and submit files</div>
                                            <div class="card-body">
                                                @if ($errors->any())
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                <form id="formCreatives" method="POST" enctype="multipart/form-data">
                                                    @csrf

                                                    <div style="display: flex;flex-direction: column;">
                                                        <label for="campaign-name">Name: </label>
                                                        <input id="campaign-name" name="campaign_name" type="text" max="350" style="max-width: 300px;">
                                                    </div>
                                                    <div>
                                                        <label for="creative_file">Upload Creatives: </label>
                                                        <input id="creative_file" name="creative_file" type="file" class="file"  data-show-upload="true" data-show-caption="true" multiple>
                                                    </div>
                                                    <button class="btn btn-primary">Submit</button>
                                                    <br>
                                                </form>
                                            </div>
                                        </div>
                                        <br>
                                        @php if(str_contains(Auth::user()->email,"@horizonmedia.com")){ @endphp
                                        <div class="form-group">
                                            <label for="Campaign">Campaigns</label>
                                            <select class="form-control select2 select2-hidden-accessible" name="Campaign"
                                                    id="campaignSelector">
                                                <option selected="selected" value="0 ">Select Campaign</option>
                                                <option value="101">Reset Digital</option>
                                                <option value="137">CBS Good Sam Program</option>
                                            </select>
                                        </div>
                                        @php } else { @endphp
                                        <div class="form-group">
                                            <label for="organization">Organization</label>
                                            <select class="form-control select2 select2-hidden-accessible " name="organization" id="organization" style="width: 100%">
                                                <option value="">None</option>
                                                <option value="10">Reset Digital</option>
                                            @foreach($organizations as $organization)
                                                <!--<option value="{{$organization['id']}}">{{$organization['name']}}</option>-->
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
                                                <option selected="selected" value="0 ">Select Campaign</option>
                                            </select>
                                        </div>
                                        @php } @endphp
                                    </div>
                                    <div class="col-sm-12">
                                        <label>Filters</label>
                                        <div class="card " style="overflow: unset; min-height: 8em;">
                                            <div class="card-body">
                                            <div class="spinner" id="filters-container-loading"></div>
                                                <div id="filters-container">
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Ads</label><br>
                                                        <select class="form-control " name="as" id="filterAd"
                                                            multiple="multiple" style="width: 50%">
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Network Name</label><br>
                                                        <select class="form-control select2-multiple" name="as"
                                                            id="filterNetwork" multiple>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Network Type</label><br>
                                                        <select class="form-control " name="as" id="filterNetworkType"
                                                            multiple="multiple">
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Program Genre</label><br>
                                                        <select class="form-control " name="as" id="filterProgramGenre"
                                                            multiple="multiple">
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="as">Program title</label><br>
                                                        <select class="form-control " name="as" id="filterProgram"
                                                            multiple="multiple">
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">&nbsp;</div>
                                    <div class="col-sm-12">
                                        <label>ADS</label>
                                        <div class="card" id="adsCardContainer">
                                            <div class="card" style="">
                                                <div class="card-body" style="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">&nbsp;</div>

                                    <div class="col-sm-12" id='top-rated'>
                                        <label>Top rated resonances</label>
                                        <div class="card">
                                            <div class="card" style="">
                                                <div class="card-body" style="min-height: 8em;">
                                                    <div class="spinner" id="topRalatedResonancesContainer-loading"></div>
                                                    <div class="row row-same-height" id="topRalatedResonancesContainer">
                                                        <div class="col-sm-3">
                                                            <div class="card top-resonances">
                                                                <h5 class="card-header">Top 3 Networks</h5>
                                                                <ul class="list-group" id="topNetworksContainer"></ul>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="card  top-resonances">
                                                                <h5 class="card-header">Top 3 Programs</h5>
                                                                <ul class="list-group" id="topProgramsContainer"></ul>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="card  top-resonances">
                                                                <h5 class="card-header">Top 3 Daypart</h5>
                                                                <ul class="list-group" id="topDaypartContainer"></ul>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="card top-resonances">
                                                                <h5 class="card-header">Resonance Score</h5>
                                                                <span id="resonancePercentage"
                                                                    class="card-title text-white font-size-40 font-w-20 p-l-20"
                                                                    data-count="0">48.88 %</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">&nbsp;</div>
                                    <div class="col-sm-12">
                                        <label>Resonance Report</label>
                                        <div class="card principal-card">
                                            <div class="card-body" style="min-height: 8em;  overflow:auto;">
                                                <div class="spinner" id="reportTableContainer-loading"></div>
                                                <div id="reportTableContainer">
                                                    <table id="reportTable" class="display" style="width:100%">
                                                        <thead style="font-size: 12px;">
                                                            <tr id="reportTableHeaders">
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <div class="groupby-container" id="groupbyContainer">
                                                        <label style="margin-right:1em;" for="as">Columns:</label>
                                                        <select class="form-control " name="as" id="filterGroupBy"
                                                            multiple="multiple">
                                                            <option value="ADS" selected>Ads</option>
                                                            <option value="NETWORK_NAME" selected>Network Name</option>
                                                            <option value="NETWORK_TYPE" selected>Network Type</option>
                                                            <option value="PROGRAM_GENRE" selected>Program Genre
                                                            </option>
                                                            <option value="PROGRAM_TITLE" selected>Program title
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">&nbsp;</div>
                                    <div class="row row-same-height" style="padding: 15px;">
                                        <div class="col-md-12">
                                            <label>Resonance Score by:</label>
                                            <div class="card chart-card">
                                                <select class="form-control select2 select2-hidden-accessible"
                                                    id="ChartBy">
                                                    <option value="ADS">Ads</option>
                                                    <option value="NETWORK_NAME" selected>Network Name</option>
                                                    <option value="NETWORK_TYPE">Network Type</option>
                                                    <option value="PROGRAM_GENRE">Program Genre</option>
                                                    <option value="PROGRAM_TITLE">Program title</option>
                                                    <option value="DAYPARTS">Dayparts</option>
                                                </select>
                                                <div class="card-body"  style="overflow-x: auto;">
                                                    <div id="verticalChartContainer" style="
position: relative;
    margin: auto;
    height: 27em;
    overflow-y: hidden;
    ">
                                                        <canvas id="chartjs_barChart"
                                                            class="chartjs-render-monitor"></canvas>
                                                    </div>
                                                    <div id="chartByDaypartContainer" style="
position: relative;
    margin: auto;
    height: 27em;
    overflow-y: hidden;
    ">
                                                        <canvas id="chartist_horizontalBar"
                                                            class="chartjs-render-monitor"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="">PREDICTED CONTEXT EFFECT BASED ON CHOICE OF OTT CTV NETWORKS</label>
                                            <div class="card chart-card">
                                                <div class="card-body">
                                                    <div id="ad_lift">
                                                        
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
    <script>
        const campaignsStructure = <?php echo json_encode($campaigns); ?>;
        const emailLogedin =  '<?php echo Auth::user()->email; ?>';
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.0/Chart.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/chartist/dist/chartist.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jvectormap-next/jquery-jvectormap.min.js">
    </script>
    <script
        src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jvectormap-next/jquery-jvectormap-world-mill.js">
    </script>
    <!--<script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/chartist/dist/chartist.js"></script>-->
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.js"></script>
    <script
        src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery.flot.tooltip/js/jquery.flot.tooltip.min.js">
    </script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.resize.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.time.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot.curvedlines/curvedLines.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/sweetalert2/dist/sweetalert2.min.js">
    </script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/sweetalert2.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/countUp-init.js"></script>

    <script src="{{ asset('js/resonances.js') }}"></script>

    @stop
</div>