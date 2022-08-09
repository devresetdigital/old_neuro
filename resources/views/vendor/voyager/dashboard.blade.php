@extends('voyager::master')
@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div style="width: 100%; padding: 10px 60px 0px 60px;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row m-0 col-border-xl">
                        <div class="col-md-12 col-lg-6 col-xl-3">
                            <div class="card-body">
                                <div class="icon-rounded icon-rounded-primary float-left m-r-20">
                                    <i class="icon voyager-wifi"></i>
                                </div>
                                <h5 class="card-title m-b-5 counter" data-count="5627">0</h5>
                                <h6 class="text-muted m-t-10">
                                    Bids per Second
                                </h6>
                                <div class="progress progress-active-sessions mt-4" style="height:7px;">
                                    <div id="progressbar-bps" class="progress-bar bg-primary" role="progressbar" style="" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-3">
                            <div class="card-body">
                                <div class="icon-rounded icon-rounded-accent float-left m-r-20">
                                    <i class="icon voyager-check"></i>
                                </div>
                                <h5 class="card-title m-b-5 counter" data-count="5">0</h5>
                                <h6 class="text-muted m-t-10">
                                    Avarage CPM
                                </h6>
                                <div class="progress progress-add-to-cart mt-4" style="height:7px;">
                                    <div id="progressbar-wps" class="progress-bar bg-accent" role="progressbar" style="" aria-valuenow="11" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-3">
                            <div class="card-body">
                                <div class="icon-rounded icon-rounded-info float-left m-r-20">
                                    <i class="icon voyager-news"></i>
                                </div>
                                <h5 class="card-title m-b-5 counter" data-count="4564337">0</h5>
                                <h6 class="text-muted m-t-10">
                                    Impressions
                                </h6>
                                <div class="progress progress-new-account mt-4" style="height:7px;">
                                    <div id="progressbar-imp" class="progress-bar bg-info" role="progressbar" style="" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-3">
                            <div class="card-body">
                                <div class="icon-rounded icon-rounded-success float-left m-r-20">
                                    <i class="icon voyager-dollar"></i>
                                </div>
                                <h5 class="card-title m-b-5 prepend-currency counter" data-count="3773">0</h5>
                                <h6 class="text-muted m-t-10">
                                    Total Spent
                                </h6>
                                <div class="progress progress-total-revenue mt-4" style="height:7px;">
                                    <div id="progressbar-spt" class="progress-bar bg-success" role="progressbar" style="" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="analytics-container">
            <div style="width: 100%; padding: 0px 20px 0px 20px;">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="card p-10">
                            <h5 class="card-header p-10" style="border-bottom: 0px;">Las 7 Days</h5>
                            <canvas id="myChart" width="350" height="120"></canvas>
                        </div>
                    </div>
                    <div class="col-xl-5 col-xxl-3" style="padding-right: 16px;">
                        <div class="card">
                            <div class="card-header">Activity Log
                                <div class="actions top-right">
                                    <div class="dropdown">
                                        <a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="la la-ellipsis-h"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right animation" x-placement="bottom-end" style="position: absolute; transform: translate3d(22px, 25px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a class="dropdown-item" href="#">Check Latest Logs</a>
                                            <a class="dropdown-item" href="#">Logs History</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="timeline timeline-border">
                                    <div class="timeline-list">
                                        <div class="timeline-info">
                                            <div class="d-inline-block">Campaign #45564 Pending</div>
                                            <small class="float-right text-muted">Now</small>
                                        </div>
                                    </div>
                                    <div class="timeline-list timeline-border timeline-primary">
                                        <div class="timeline-info">
                                            <div class="d-inline-block">Campaign #34455 Completed</div>
                                            <small class="float-right text-muted">10min.</small>
                                        </div>
                                    </div>
                                    <div class="timeline-list  timeline-border timeline-accent">
                                        <div class="timeline-info">
                                            <div class="d-inline-block">Creativity #433221 Rejected</div>
                                            <small class="float-right text-muted">1hr.</small>
                                        </div>
                                    </div>
                                    <div class="timeline-list  timeline-border timeline-success">
                                        <div class="timeline-info">
                                            <div class="d-inline-block">Payment Recorded</div>
                                            <small class="float-right text-muted">11:22am</small>
                                        </div>
                                    </div>
                                    <div class="timeline-list  timeline-border timeline-warning">
                                        <div class="timeline-info">
                                            <div class="d-inline-block">Campaign #4522 Paused</div>
                                            <small class="float-right text-muted">9:30AM</small>
                                        </div>
                                    </div>
                                    <div class="timeline-list  timeline-border timeline-info">
                                        <div class="timeline-info">
                                            <div class="d-inline-block">Campaign #3455 Updated</div>
                                            <small class="float-right text-muted">9:27am</small>
                                        </div>
                                    </div>
                                    <div class="timeline-list  timeline-border timeline-info">
                                        <div class="timeline-info">
                                            <div class="d-inline-block">Audit #627 Approved</div>
                                            <small class="float-right text-muted">8:02am</small>
                                        </div>
                                    </div>
                                    <div class="timeline-list  timeline-border timeline-info">
                                        <div class="timeline-info">
                                            <div class="d-inline-block">Audit #2312 Submitted</div>
                                            <small class="float-right text-muted">6:00am</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" style="padding-right: 16px">
                        <div class="card-deck m-b-30">
                            <div class="card">
                                <h5 class="card-header border-none">Impressions</h5>
                                <div class="card-body p-0">
                                    <div class="h-200">
                                        <canvas id="impressionsChart" style="max-height: 200px; height: 200px;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <h5 class="card-header border-none">Clicks</h5>
                                <div class="card-body p-0">
                                    <div class="h-200">
                                        <canvas id="clicksChart" style="max-height: 200px; height: 200px;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <h5 class="card-header">Spent</h5>
                                <div class="card-body">
                                    <canvas id="chartjs_pieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#bydate">By Date</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#bycamp" onclick="genDatatableById('bycamp','Campaign','advcamp_2')">By Campaign</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#bygeo" onclick="genDatatableById('bygeo','Region','regioncity_1')">By Geo</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#bydevice" onclick="genDatatableById('bydevice','Device','deviceosbrowser_1')">By Technology</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#bysupply" onclick="genDatatableById('bysupply','Domain/App','channeldomain_2')">By Supply</a>
                        </li>
                        <!-- <li>
                             <a data-toggle="tab" href="#bydata" onclick="genDatatableById('bydata','Data')">By Data</a>
                         </li>-->
                        <li>
                            <a data-toggle="tab" href="#custom" onclick="setTimeout(tagableDomainInput, 1000);">Custom Report</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="bydate" class="tab-pane fade in active"></div>
                        <div id="bycamp" class="tab-pane fade in"></div>
                        <div id="bygeo" class="tab-pane fade in"></div>
                        <div id="bydevice" class="tab-pane fade in"></div>
                        <div id="bysupply" class="tab-pane fade in"></div>
                        <div id="bydata" class="tab-pane fade in"></div>
                        <div id="custom" class="tab-pane fade in">
                            <form>
                                <div class="panel panel-primary panel-bordered">
                                    <div class="panel-body">
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Campaigns</label>
                                                <select class="form-control select2" name="campaigns[]" multiple id="campaign">
                                                    @foreach($campaigns as $val)
                                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Concepts</label>
                                                <select class="select2" name="concepts[]" multiple id="concept">
                                                    @foreach($concepts as $val)
                                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Creatives</label>
                                                <select class="form-control select2" name="creatives[]" multiple id="creative">
                                                    @foreach($creatives as $val)
                                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Domain</label>
                                                <select type="text" class="form-control select2" multiple id="domain"></select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Country</label>
                                                <select class="form-control select2" name="country[]" multiple id="country">
                                                    @foreach($countries as $val)
                                                        <option value="{{$val->code}}">{{$val->country}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Region</label>
                                                <select class="form-control select2" name="region[]" multiple id="region">
                                                    @foreach($regions as $val)
                                                        <option value="{{$val->code}}">{{$val->region}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Group By</label>
                                                <select class="form-control select2" name="groupby" id="groupby">
                                                    <option value="date">Date</option>
                                                    <option value="advcamp_2">Campaign</option>
                                                    <option value="concreat_1">Concept</option>
                                                    <option value="concreat_2">Creative</option>
                                                    <option value="channeldomain_2">Domain</option>
                                                    <option value="countryisp_1">Country</option>
                                                    <option value="regioncity_1">Region</option>
                                                    <option value="regioncity_2">City</option>
                                                    <option value="deviceosbrowser_3">Browser</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <button class="btn btn-primary" type="button" onclick="genDatatableById('customreport','Domain/App','channeldomain_2')">Calculate</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div id="customreport"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script>
        function genDatatableById(id,cvalue,groupby){
            //if($('#'+id).html()==""){
            var fcountries = "";
            var fcamps = "";
            var fconcepts = "";
            var fdomains = "";
            var fcreatives = "";
            var fregions = "";

            //IF CUSTOM ADD FILTERS
            if(id == "customreport"){
                //Filters
                //Countries
                $.each( $('#country').val(), function( key, value ) {
                    fcountries+=value+"_*,";
                });

                //Campaigns
                $.each( $('#campaign').val(), function( key, value ) {
                    fcamps+="*_"+value+",";
                });

                //Concepts
                $.each( $('#concept').val(), function( key, value ) {
                    fconcepts+=value+"_*,";
                });

                //Domains
                $.each( $('#domain').val(), function( key, value ) {
                    fdomains+="*_"+value+"_*,";
                });

                //Creatives
                $.each( $('#creative').val(), function( key, value ) {
                    fcreatives+="*_"+value+",";
                });

                //Region
                $.each( $('#region').val(), function( key, value ) {
                    fregions+=value+"_*,";
                });

                groupby = $("#groupby").val();

                //GroupBy
                /* var fgroupby = "";
                 $('#groupby').val().forEach(function(element) {
                     //console.log(element);
                     fgroupby+=element+",";
                 });*/
                switch(groupby) {
                    case 'concreat_2':
                        cvalue = "Creative";
                        break;
                    case 'concreat_1':
                        cvalue = "Concept";
                        break;
                    case 'advcamp_2':
                        cvalue = "Campaign";
                        break;
                    case 'channeldomain_2':
                        cvalue = "Domain/App";
                        break;
                    case 'countryisp_1':
                        cvalue = "Country";
                        break;
                    case 'regioncity_1':
                        cvalue = "Region";
                        break;
                    case 'regioncity_2':
                        cvalue = "City";
                        break;
                    case 'deviceosbrowser_3':
                        cvalue = "Browser";
                        break;
                    case 'date':
                        cvalue = "Date";
                        break;
                }
            }

            toptabs="";
            if(id == "bygeo"){
                toptabs ='<ul class="nav nav-pills">' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bygeo\',\'Country\',\'countryisp_1\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Country</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bygeo\',\'Region\',\'regioncity_1\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Region</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bygeo\',\'City\',\'regioncity_2\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">City</a>\n' +
                    '</li>' +
                    '</ul>';
            }
            if(id == "bysupply"){
                toptabs ='<ul class="nav nav-pills">' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" class="nav-link active show" data-toggle="tab" aria-expanded="true">Web</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" class="nav-link active show" data-toggle="tab" aria-expanded="true">App</a>\n' +
                    '</li>' +
                    '</ul>';
            }
            if(id == "bydevice"){
                toptabs ='<ul class="nav nav-pills">' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bydevice\',\'Device\',\'deviceosbrowser_1\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Device</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bydevice\',\'Os\',\'deviceosbrowser_2\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Os</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bydevice\',\'Browser\',\'deviceosbrowser_3\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Browser</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bydevice\',\'Isp\',\'countryisp_2\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">ISP</a>\n' +
                    '</li>' +
                    '</ul>';
            }
            $('#'+id).html(
                toptabs +
                '                            <table id="'+id+'_dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">\n' +
                '                                <thead>\n' +
                '                                <tr>\n' +
                '                                    <th>'+cvalue+'</th>\n' +
                '                                    <th>Impressions</th>\n' +
                '                                    <th>Clicks</th>\n' +
                '                                    <th>Spent</th>\n' +
                '                                    <th>eCPM</th>\n' +
                '                                    <th>CTR</th>\n' +
                '                                    <th>CPC</th>\n' +
                '                                    <th>Conversion</th>\n' +
                '                                    <th>CPA</th>\n' +
                '                                    <th>Viewability</th>\n' +
                '                                    <th>TOS</th>\n' +
                '                                </tr>\n' +
                '                                </thead>\n' +
                '                            </table>'
            );
            $('#'+id+'_dataTable').DataTable({
                "processing": true,
                "order": [[ 1, "desc" ]],
                "ajax": {
                    "url": "/api/hbreports",
                    "type": "POST",
                    "data": {
                        'groupby': groupby,
                        'advcamps' : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"] }}',
                        'from' :  '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                        'until' :  '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}',
                        'filters' : 'advcamps='+fcamps+'&concreats='+fconcepts+fcreatives+'&channeldomains='+fdomains+'&countryisps='+fcountries+'&regioncities='+fregions
                    }
                },
                columns: [
                    {data: cvalue},
                    {data: "Impressions"},
                    {data: "Clicks"},
                    {data: "Spent"},
                    {data: "eCPM"},
                    {data: "CTR"},
                    {data: "CPC"},
                    {data: "Conversions"},
                    {data: "CPA"},
                    {data: "Viewability"},
                    {data: "TOS"}
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ]
            });
            //}

        }
        function tagableDomainInput(){
            $("#domain").select2({
                tags: true
            });
        }
        window.onload = function() {
            //Mini Charts
            //Impressions Chart
            var ctximp = document.getElementById('impressionsChart');
            var impressionsChart = new Chart(ctximp, {
                type: 'bar',
                data: {
                    datasets: [{
                        label: 'Impressions',
                        yAxisID: 'IMP',
                        data: [1000000,1500000,3000000,1000000,1500000,3000000],
                        backgroundColor: "rgba(246,74,145,.4)",
                        borderColor: "rgba(246,74,145,.4)",
                        borderWidth: 2
                    }
                    ],
                    labels: ['4-12-18','5-12-18','6-12-18','7-12-18','8-12-18','9-12-18']
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            id: 'IMP',
                            scaleLabel: {
                                display: false,
                                labelString: 'Impressions'
                            },
                            ticks: {
                                display: false,
                                suggestedMin: 0,
                                suggestedMax: 2000
                            },
                            gridLines: {
                                display: false,
                                borderDashOffset: 30
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false,
                                borderDashOffset: 30
                            }
                        }]

                    }
                }

            });

            //Clicks Chart
            var ctxclc = document.getElementById('clicksChart');
            var clicksChart = new Chart(ctxclc, {
                type: 'bar',
                data: {
                    datasets: [{
                        label: 'clicks',
                        yAxisID: 'CLC',
                        data: [1200,1000,1204,2100,2001,1002],
                        backgroundColor: "rgba(115,108,199,.4)",
                        borderColor: "rgba(115,108,199,.4)",
                        borderWidth: 2
                    }
                    ],
                    labels: ['4-12-18','5-12-18','6-12-18','7-12-18','8-12-18','9-12-18']
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            id: 'CLC',
                            scaleLabel: {
                                display: false,
                                labelString: 'Clicks'
                            },
                            ticks: {
                                display: false,
                                suggestedMin: 0,
                                suggestedMax: 2000
                            },
                            gridLines: {
                                display: false,
                                borderDashOffset: 30
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false,
                                borderDashOffset: 30
                            }
                        }]

                    }
                }

            });
            //Spent Chart
           /* var ctxspt = document.getElementById('spentChart');
            var spentChart = new Chart(ctxspt, {
                type: 'bar',
                data: {
                    datasets: [{
                        label: 'spent',
                        yAxisID: 'SPT',
                        data: [1200,1000,1204,2100,2001,1002],
                        backgroundColor: "rgba(102, 255, 102,.4)",
                        borderColor: "rgba(102, 255, 102,.4)",
                        borderWidth: 2
                    }
                    ],
                    labels: ['4-12-18','5-12-18','6-12-18','7-12-18','8-12-18','9-12-18']
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            id: 'SPT',
                            scaleLabel: {
                                display: false,
                                labelString: 'Clicks'
                            },
                            ticks: {
                                display: false,
                                suggestedMin: 0,
                                suggestedMax: 2000
                            },
                            gridLines: {
                                display: false,
                                borderDashOffset: 30
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false,
                                borderDashOffset: 30
                            }
                        }]

                    }
                }

            });*/




            //By Date Datatable
            $(document).ready(function() {

                $('#bydate').html(
                    '                            <table id="dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">\n' +
                    '                                <thead>\n' +
                    '                                <tr>\n' +
                    '                                    <th>Date</th>\n' +
                    '                                    <th>Impressions</th>\n' +
                    '                                    <th>Clicks</th>\n' +
                    '                                    <th>Spent</th>\n' +
                    '                                    <th>eCPM</th>\n' +
                    '                                    <th>CTR</th>\n' +
                    '                                    <th>CPC</th>\n' +
                    '                                    <th>Conversion</th>\n' +
                    '                                    <th>CPA</th>\n' +
                    '                                    <th>Viewability</th>\n' +
                    '                                    <th>TOS</th>\n' +
                    '                                </tr>\n' +
                    '                                </thead>\n' +
                    '                            </table>'
                );
                $('#dataTable').DataTable({
                    "processing": true,
                    "ajax": {
                        "url": "/api/hbreports",
                        "type": "POST",
                        "data": {
                            'advcamps' : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"] }}',
                            'from': '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                            'until': '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}',
                            'filters' : ''
                        }
                    },
                    columns: [
                        {data: "Date"},
                        {data: "Impressions"},
                        {data: "Clicks"},
                        {data: "Spent"},
                        {data: "eCPM"},
                        {data: "CTR"},
                        {data: "CPC"},
                        {data: "Conversions"},
                        {data: "CPA"},
                        {data: "Viewability"},
                        {data: "TOS"}
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5'
                    ]
                });
                //ADD DATE PICKER
                        @if(isset($_GET["from"]) && isset($_GET["until"]))
                var start = moment("20{{substr($_GET["from"],0, strlen($_GET["from"])-2)  }}");
                var end = moment("20{{substr($_GET["until"],0, strlen($_GET["until"])-2)  }}");
                        @else
                var start = moment().subtract(29, 'days');
                var end = moment();
                @endif

                function cb(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);

                cb(start, end);

                $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                    var reportStartDate = picker.startDate.format('YYMMDD')+'00';
                    var reportUntilDate = picker.endDate.format('YYMMDD')+'23';

                    document.location.href="/admin/reports?from="+reportStartDate+"&until="+reportUntilDate+'&campaign_id={{ isset($_GET["campaign_id"]) ? $_GET["campaign_id"] : ''  }}';

                });

            } );


            //Get Data for Total Impressions and Total Clicks
            $.post("/api/hbreports",
                {
                    /*countries : fcountries,
                    channels : fchannels,
                    media : fmedia,
                    domains : fdomains,
                    sizes : fsizes,
                    cities : fcities,*/
                    groupby: 'date',
                    advcamps : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"] }}',
                    from :  moment().subtract(7, 'days').format('YYMMDD')+'00',
                    until :  moment().format('YYMMDD')+'23'
                },function(data){

                    console.log(data);
                    totalImpressions=0;
                    totalClicks=0;
                    $.each(data.data, function( index, value ) {
                        totalImpressions = +totalImpressions + +value.Impressions;
                        totalClicks = +totalClicks + +value.Clicks;
                    });
                    console.log(totalImpressions);
                },'json');
            // Month
            $.post("/api/hbreports",
                {
                    /*countries : fcountries,
                    channels : fchannels,
                    media : fmedia,
                    domains : fdomains,
                    sizes : fsizes,
                    cities : fcities,*/
                    groupby: 'date',
                    advcamps : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"] }}',
                    from :  moment().subtract(30, 'days').format('YYMMDD')+'00',
                    until :  moment().format('YYMMDD')+'23'
                },function(data){

                    console.log(data);
                    totalImpressionsMonth=0;
                    totalClicksMonth=0;
                    $.each(data.data, function( index, value ) {
                        totalImpressionsMonth = +totalImpressionsMonth + +value.Impressions;
                        totalClicksMonth = +totalClicksMonth + +value.Clicks;
                    });
                    console.log(totalImpressionsMonth);
                },'json');
        }

    </script>

    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jvectormap-next/jquery-jvectormap.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
    <!--<script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/chartist/dist/chartist.js"></script>-->
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery.flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.resize.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.time.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot.curvedlines/curvedLines.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/sweetalert2.js"></script>

    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/countUp-init.js"></script>
    <!--<script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/cards/total-visits-chart.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/cards/total-unique-visits-chart.js"></script>-->
    <script>
        $(document).ready(function() {
        @php
        $_GET["from"] = "19103100";
        $_GET["until"] = "19103123";
        @endphp
            // Chart
            @if(isset($_GET["from"]) && isset($_GET["until"]))
            var start = moment("20{{substr($_GET["from"],0, strlen($_GET["from"])-2)  }}");
            var end = moment("20{{substr($_GET["until"],0, strlen($_GET["until"])-2)  }}");
                    @else
            var start = moment().subtract(29, 'days');
            var end = moment();
            @endif
            //Check Days between Dates
            var date1 = new Date(start);
            var date2 = new Date(end);
            console.log(date1+','+date2);
            var timeDiff = Math.abs(date2.getTime() - date1.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

            console.log("Date Diff: "+diffDays);

            if(diffDays>0){ gby = "date"; } else { gby="datetime"; }

            $.post("/api/hbreports",
                {
                    /*countries : fcountries,
                    channels : fchannels,
                    media : fmedia,
                    domains : fdomains,
                    sizes : fsizes,
                    cities : fcities,*/
                    groupby: gby,
                    advcamps : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"] }}',
                    organization :  '{{ isset($userorganization) ? $userorganization : 0  }}',
                    uid :  '{{ isset($user_id) ? $user_id : 0  }}',
                    urole :  '{{ isset($user_role) ? $user_role : 0  }}',
                    from: '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                    until: '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}'
                },
                function(data){
                    var labels= [];
                    var valuesimpressions= [];
                    var valuesclicks= [];
                    $.each(data.data, function( index, value ) {
                        //console.log( value.Data );
                        labels.push(value.Date);
                        valuesimpressions.push(value.Impressions);
                        valuesclicks.push(value.Clicks);
                    });
                    //console.log(labels);
                    //console.log(Math.max.apply(null, values));
                    /*if( Math.max.apply(null, values)==0) {
                        var chartsuggestedMax = Math.max.apply(null, values);
                    } else {
                        var chartsuggestedMax = 100;
                    }*/

                    var ctx = document.getElementById("myChart");
                    var chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            datasets: [{
                                label: 'Impressions',
                                yAxisID: 'IMP',
                                data: valuesimpressions,
                                backgroundColor: "rgba(88, 103, 195,0.4)",
                                borderColor: "rgba(88, 103, 195,0.7)",
                                borderWidth: .6
                            },
                                {
                                    label: 'Clicks',
                                    yAxisID: 'CLC',
                                    data: valuesclicks,
                                    backgroundColor: "rgba(28, 134, 191,0.4)",
                                    borderColor: "rgba(28, 134, 191,0.7)",
                                    borderWidth: .6
                                }
                            ],
                            labels: labels
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    id: 'IMP',
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Impressions'
                                    },
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 2000
                                    },
                                    gridLines: {
                                        display: true,
                                        borderDashOffset: 30
                                    }
                                }, {
                                    id: 'CLC',
                                    labelString: 'Clicks',
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Clicks'
                                    },
                                    position: 'right',
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 500
                                    },
                                    gridLines: {
                                        display: false,
                                        borderDashOffset: 30
                                    }
                                }]
                            }
                        }

                    });

                    console.log(labels);

                },'json');

            setTimeout(function(){
                //$('#total-impressions-week').html(totalImpressions);
                //$('#total-clicks-week').html(totalClicks);
                //$('#total-impressions-month').html(totalImpressionsMonth);
                // $('#total-clicks-month').html(totalClicksMonth);
                //numAnim = new CountUp(document.getElementById('total-impressions-week'), 0, totalImpressions);
               // numAnim.start();
               // numAnim = new CountUp(document.getElementById('total-impressions-month'), 0, totalImpressionsMonth);
              //  numAnim.start();
               // numAnim = new CountUp(document.getElementById('total-clicks-week'), 0, totalClicks);
              //  numAnim.start();
               // numAnim = new CountUp(document.getElementById('total-clicks-month'), 0, totalClicksMonth);
              //  numAnim.start();
            }, 2000);
            setTimeout(function(){
                document.getElementById('progressbar-bps').style.width='80%';
                document.getElementById('progressbar-wps').style.width='11%';
                document.getElementById('progressbar-imp').style.width='80%';
                document.getElementById('progressbar-spt').style.width='75%';
                var ctx = document.getElementById("chartjs_pieChart").getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ["M", "T", "W", "T", "F", "S", "S"],
                        datasets: [{
                            backgroundColor: [
                                "#5867C3",
                                "#1C86BF",
                                "#28BEBD",
                                "#FEB38D",
                                "#EE6E73",
                                "#EC407A",
                                "#F8C200"
                            ],
                            data: [12, 19, 3, 17, 28, 24, 7]
                        }]
                    }
                });

            },1000)
        });
    </script>
@stop