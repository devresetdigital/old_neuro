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

.dx-texteditor-input-container{
  display: none !important;
}



/* 
chart
*/
.highcharts-figure,
.highcharts-data-table table {
  min-width: 320px;
  max-width: 800px;
  margin: 1em auto;
}

.highcharts-data-table table {
  font-family: Verdana, sans-serif;
  border-collapse: collapse;
  border: 1px solid #ebebeb;
  margin: 10px auto;
  text-align: center;
  width: 100%;
  max-width: 500px;
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
                                    <li class="">
                                        <a data-toggle="" href="/admin/level1_report">Level 1</a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="" href="/admin/HAO-AI_Dashboard">HAO</a>
                                    </li>
                                    <li class="active">
                                        <a data-toggle="active" href="#">X2</a>
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <div class="form-group col-sm-12">
                                        <div class="form-group">
                                            <label for="organization">Organization</label>
                                            <select class="form-control select2 select2-hidden-accessible "
                                                name="organization" id="organization" style="width: 100%">
                                                @foreach($organizations as $organization)
                                                    <option value="{{ $organization['id'] }}">
                                                        {{ $organization['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="advertiser">Advertiser</label>
                                            <select class="form-control select2 select2-hidden-accessible "
                                                name="advertiser" id="advertiser" style="width: 100%">
                                                <option value="">None</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="Campaign">Campaigns</label>

                                            <select class="form-control select2 select2-hidden-accessible"
                                                name="Campaign" id="campaignSelector">
                                                <option selected="selected" value="0">Select Campaign</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="">&nbsp;</div>
                                    <div class="col-sm-12">
                                            <p>Signals</p>
                                            <div class="card" id="">
                                            <img src="{{ asset('/Loading.gif') }}" id="loading" style="position: absolute;
                                                    width: 3.5em;
                                                    z-index: 1;
                                                    margin-top: 3px;" alt="">
                                                <div class="card-body">
                                                
                                                    <div class="col-sm-12" id="signalPreview"></div>
                                                    <div class="col-sm-12" style="padding-left: left 20%; margin-bottom: 2em;">
                                                        <div class="card" id="signalsCardContainer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="">&nbsp;</div>
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

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
 

    <script src="{{ asset('js/resonances_dashboard_x2.js') }}"></script>

    <script>

    </script>

    @stop
