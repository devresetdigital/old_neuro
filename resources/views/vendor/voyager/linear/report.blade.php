@extends('voyager::master')
@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-people"></i> Linear Reports
        </h1>
    </div>
@stop
@section('content')
<style>
    .hr-droid {
        display: flex;
        padding-bottom:0;
    }
    .hr-line{
        width: 100%;
        position: relative;
        margin: 10px;
        border-bottom: 5px solid #a4c639;
    }
    .green{
        border-color: #aaaaaa;
    }
    .purple {
        border-color: #aaaaaa;
    }
    .fa {
        position: relative;
        top: 3px;
        color: #aaaaaa;
    }
    .filter-icon{
        min-width: 9em;
        cursor: pointer;
    }
    .card-body{
        padding-top: 0 !important;
        margin-top: 0 !important;
    }
    .filter-clik{
        cursor: pointer;
    }
 </style>
    <link rel="stylesheet" href="{{ asset('css/lib/bootstrap-multiselect.css') }}" type="text/css" />
    <!-- ======================= DRIP ICONS ===================================-->
    @include('voyager::compass.includes.styles')
    @include('voyager::alerts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.5/jquery.csv.min.js"></script>
    <div class="page-content compass container-fluid">
        <div class="tab-content">
        <button style="margin-left: 20px;" id="tab1" type="button" class="btn btn-primary" onclick="toggleTables('netdeliver')">Network Delivery</button> 
        <button  id="tab2" type="button" class="btn btn-primary" onclick="toggleTables('weekdeliver')">Weekly Delivery</button> 
            <button  id="tab3" type="button" class="btn btn-primary" onclick="toggleTables('daypart')">Dayparts Delivery</button> 
            <button type="button" id="tab4" class="btn btn-primary" onclick="toggleTables('current')">DMA Summary</button> 
            <button type="button" id="tab5" class="btn btn-primary" onclick="toggleTables('finished')">DMA Reports</button>
            <div id="reportrange"  style="margin-top: 0.5em; margin-right: 0.5em; background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 400px; float: right; text-align: left">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
            </div>
         
            <div class="row filters" id="filtersContainer">
                <div class="col-sm-12" style="margin: 0px;" id="container-networks">
                    <label for="as">Network Name</label><br>
                    <select class="form-control select2 select2-hidden-accessible" multiple="" id="networks" tabindex="-1" aria-hidden="true">
                            @php
                                $items = array_key_exists('network', $_GET) ? explode(',',$_GET['network']) : []; 
                            @endphp
                            @foreach($filters['Networks'] as $aux)
                                <option {{(in_array($aux,$items) ? 'selected': '' )}} value="{{$aux}}">{{$aux}}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-sm-12" style="margin: 0px;" id="container-dayparts">
                    <label for="network">DayParts</label>
                    <select class="form-control select2 select2-hidden-accessible" multiple="" id="dayparts" tabindex="-1" aria-hidden="true">
                            @php
                                $items = array_key_exists('daypart', $_GET) ? explode(',',$_GET['daypart']) : []; 
                            @endphp
                            @foreach($filters['DayParts'] as $aux)
                                <option {{(in_array($aux,$items) ? 'selected': '' )}} value="{{$aux}}">{{$aux}}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-sm-12" style="margin: 0px;" id="container-dmas">
                    <label for="network">DMAs</label>
                    <select class="form-control select2 select2-hidden-accessible" multiple="" id="dmas" tabindex="-1" aria-hidden="true">
                            @php
                                $items = array_key_exists('dma', $_GET) ? explode(',',$_GET['dma']) : []; 
                            @endphp
                            @foreach($filters['DMAs'] as $aux)
                                <option {{(in_array($aux,$items) ? 'selected': '' )}} value="{{$aux}}">{{$aux}}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-sm-12" style="margin: 0px;" id="container-creative">
                    <label for="network">Creative</label>
                    <select class="form-control select2 select2-hidden-accessible" multiple="" id="creative" tabindex="-1" aria-hidden="true">
                            @php
                                $items = array_key_exists('creative', $_GET) ? explode(',',$_GET['creative']) : []; 
                            @endphp
                            @foreach($filters['Creative'] as $aux)
                                <option {{(in_array($aux,$items) ? 'selected': '' )}} value="{{$aux}}">{{$aux}}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-sm-12" style="margin: 0px;" id="container-advertisers">
                    <label for="network">Advertisers</label>
                    <select class="form-control select2 select2-hidden-accessible" multiple="" id="advertisers" tabindex="-1" aria-hidden="true">
                             @php
                                $items = array_key_exists('advertiser', $_GET) ? explode(',',$_GET['advertiser']) : []; 
                            @endphp
                            @foreach($filters['Advertisers'] as $aux)
                                <option {{(in_array($aux,$items) ? 'selected': '' )}} value="{{$aux}}">{{$aux}}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-sm-12" style="margin: 0px;" id="container-demos">
                    <label for="network">Demos</label>
                    <select class="form-control select2 select2-hidden-accessible" multiple="" id="demos" tabindex="-1" aria-hidden="true">
                            @php
                                $items = array_key_exists('demo', $_GET) ? explode(',',$_GET['demo']) : []; 
                            @endphp
                        @foreach($filters['Demos'] as $aux)
                            <option {{(in_array($aux,$items) ? 'selected': '' )}} value="{{$aux}}">{{$aux}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12" style="margin: 0px;" id="container-programs">
                    <label for="network">Programs</label>
                    <select class="form-control select2 select2-hidden-accessible" multiple="" id="programs" tabindex="-1" aria-hidden="true">
                        @php
                            $items = array_key_exists('program', $_GET) ? explode(',',$_GET['program']) : []; 
                        @endphp
                        @foreach($filters['Programs'] as $aux)
                            <option {{(in_array($aux,$items) ? 'selected': '' )}} value="{{$aux}}">{{$aux}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12" style="margin-top: 1em;" id="container-search">
                    <button type="button" class="btn btn-primary btn-block"  id="search">Apply Filter</button>
                </div>

            </div>
            <div class="hr-droid">
                <div class="hr-line green"></div>
                <div id="filters"><i class="fa fa-filter fa-1x filter-icon">Advanced filters</i></div>
                <div class="hr-line purple"></div>
            </div>
          
            <div class="card" id="netdeliver" style="display: inline;  padding: 0;">
                <div class="card-body">
                    <div class="card-deck m-b-30">
                    <div class="card">
                        <h5 class="card-header">Network Delivery</h5>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Network</th>
                                        <th  style="text-align: right;" >Contracted</th>
                                        <th style="text-align: right;" >Delivered</th>
                                        <th style="text-align: right;" >Delivery %</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body-network"></tbody>
                                </table>
                            </div>
                         </div>
                         </div>
                         <div class="card">
                            <div class="tab-content clearfix">
                                <div class="tab-pane active" id="net-tab">
                                    <canvas id="netChart"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card" id="weekdeliver" style="display: none; padding: 0;">
                <div class="card-body">
                    <div class="card-deck m-b-30">
                        <div class="card">
                            <h5 class="card-header">Weekly Delivery</h5>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Week</th>
                                            <th style="text-align: right;" >Contracted</th>
                                            <th style="text-align: right;" >Delivered</th>
                                            <th style="text-align: right;" >Delivery %</th>
                                        </tr>
                                        </thead>
                                        <tbody id="body-weekly">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="tab-content clearfix">
                            <canvas id="weekChart"></canvas>
                                   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="daypart" style="display: none; padding: 0;">
                <div class="card-body">
                    <div class="card-deck m-b-30">
                        <div class="card">
                            <h5 class="card-header">M-Su</h5>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Dayparts</th>
                                            <th style="text-align: right;" >Contracted</th>
                                            <th style="text-align: right;" >Delivered </th>
                                            <th style="text-align: right;" >Delivery %</th>
                                        </tr>
                                        </thead>
                                        <tbody id="body-msu">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="tab-content clearfix">
                                <canvas id="msuChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="current" style="display: none; padding: 0;">
                <div class="card-body">
                        <div class="card-deck m-b-30">
                            <div class="card">
                                <h5 class="card-header">DMA Summary</h5>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th>DMA Rank</th>
                                                <th>DMA Name</th>
                                                <th style="text-align: right;">A25-54 IMPs</th>
                                                <th style="text-align: right;">HH IMPs</th>
                                            </tr>
                                            </thead>
                                            <tbody  id="body-dma">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <div class="card" id="finished" style="display: none; padding: 0;">
                <div class="card-body">
                    <div class="card-deck m-b-30">
                        <div class="card">
                            <h5 class="card-header">DMA Report</h5>
                            <div class="card-body">
                                <div class="row" style="margin-top: 10px; margin-right: 20px; margin-bottom: 10px;">
                                    <div style="width: 100%; text-align: right">
                                   
                                        <!-- <button type="button" class="btn btn-primary btn-rounded" id="sweetalert_export_audit" style="margin-top: 0px">
                                             Export to Audit
                                         </button>
                                         <br><br>-->
                                    </div>
                                </div>
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Creative Isci</th>
                                            <th>Advertiser Name</th>
                                            <th>Spot Length</th>
                                            <th style="min-width: 7em;">Broadcast Date</th>
                                            <th>Network</th>
                                            <th style="min-width: 7em;">Delivery Insertion Date</th>
                                            <th>Delivery Insertion Time of Day</th>
                                            <th>Dma Name</th>
                                            <th style="min-width: 7em;">Demo</th>
                                            <th>Program</th>
                                            <th>Daypart Name</th>
                                            <th style="text-align: right;">Total Delivered Impressions</th>
                                            <th style="text-align: right;">Net CPM</th>
                                            <th style="text-align: right;">Estimated Net Spend</th>
                                        </tr>
                                        </thead>
                                        <tbody id="body-dma-report">
                                       
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                &nbsp;
            </div>
        </div>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery/dist/jquery.min.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jvectormap-next/jquery-jvectormap.min.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
        
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery.flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.resize.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.time.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot.curvedlines/curvedLines.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/sweetalert2.js"></script>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
        <!-- ================== GLOBAL VENDOR SCRIPTS ==================-->
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/modernizr/modernizr.custom.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery/dist/jquery.min.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/js-storage/js.storage.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/js-cookie/src/js.cookie.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/pace/pace.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/metismenu/dist/metisMenu.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/switchery-npm/index.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
        <!-- ================== PAGE LEVEL VENDOR SCRIPTS ==================-->
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/d3/dist/d3.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/c3/c3.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/global/app.js"></script>
        <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/charts/c3charts-init.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
        <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
        <script>
      

            let queryfrom = '{{ array_key_exists('from',$_GET) ? $_GET['from'] : ''  }}';
            let queryuntil = '{{ array_key_exists('until',$_GET) ? $_GET['until'] : ''  }}';
             

            if(queryfrom != ''){
             
                let year = '20'+queryfrom.substr(0,2);
                let month = queryfrom.substr(2,2);
                let date = queryfrom.substr(4,2);
                let aux = month + '/' + date + '/'+year ;
                aux = new Date(aux);
                var start = moment(aux);
            }else{
                var start = moment().subtract(6, 'days');
            }
         
            if(queryuntil != ''){
                let year = '20'+queryuntil.substr(0,2);
                let month = queryuntil.substr(2,2);
                let date = queryuntil.substr(4,2);
                let aux = month + '/' + date + '/'+year ;
                aux = new Date(aux);
                var end =  moment(aux);
            }else{
                var end = moment();
            }
           

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Last Week': [moment().subtract(7, 'days'), moment()],
                    'Last 4 Weeks': [moment().subtract(24, 'days'), moment()],
                    'Last 6 Month': [moment().subtract(6, 'month'), moment()],
                  
                    //'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                var reportStartDate = picker.startDate.format('YYMMDD')+'00';
                var reportUntilDate = picker.endDate.format('YYMMDD')+'23';

                document.location.href="/admin/linear_report?from="+reportStartDate+"&until="+reportUntilDate+'&campaign_id={{ isset($_GET["campaign_id"]) ? $_GET["campaign_id"] : ''  }}';

            });
            function toggleTables(table){
                document.getElementById('netdeliver').style.display="none";
                document.getElementById('weekdeliver').style.display="none";
                document.getElementById('daypart').style.display="none";
                document.getElementById('current').style.display="none";
                document.getElementById('finished').style.display="none";
                document.getElementById(table).style.display="inline";
            }


      
            const get_vars = <?php echo json_encode($_GET); ?>;
            const campaign_id = <?php echo ($_ENV['WL_PREFIX']*1000000) +  $_GET['campaign_id'] ?>;
            const campaign_id_query = <?php echo $_GET['campaign_id'] ?>;

            let currentTab = <?php echo (array_key_exists('tab', $_GET )) ? $_GET['tab'] : 1; ?>;

        </script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
         <script src="{{ asset('js/linear_report.js') }}"></script>
@stop