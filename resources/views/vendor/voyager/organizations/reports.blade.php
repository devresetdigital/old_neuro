@extends('voyager::master')
@php
    $_GET["campaign_id"] = $id;
    if(env('WL_PREFIX') !="" || env('WL_PREFIX') !="0"){
        $float_wlprefix = env('WL_PREFIX').".0";
        $wlprefix = (float) $float_wlprefix*1000000;
    } else {
        $wlprefix=0;
    }
    $user_role = auth()->user()->role_id;

    if ($id > $wlprefix){
        $idprefixed =$id;
    }else{
        $idprefixed = $wlprefix+intval($_GET["campaign_id"]);
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
        Organization Report
    </h1>
    @include('voyager::multilingual.language-selector')
@stop
@section('content')
    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
            <li  class="active"><a data-toggle="tab" href="#"><i class="voyager-bar-chart"></i> Reports</a></li>
        </ul>
        @include('voyager::alerts')
        <div class="tab-content" style="margin-top: -10px">
            <div style="width: 100%; padding: 20px;">
                <div class="panel">
                    <div class="row" style="margin-top: 10px; margin-right: 20px; margin-bottom: 10px;">
                        <div style="width: 100%; text-align: right">
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 300px; float: right; text-align: left">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                            <!-- <button type="button" class="btn btn-primary btn-rounded" id="sweetalert_export_audit" style="margin-top: 0px">
                                 Export to Audit
                             </button>
                             <br><br>-->
                        </div>
                    </div>
                    <div style="background-color: transparent; margin-top: 12px; margin-right: 25px; float: right; font-size: 12px; vertical-align: center;">
                        Include Video Reports
                    </div>
                    <div style="background-color: transparent; margin-top: 10px; margin-right: 4px; float: right; font-size: 12px; vertical-align: center;">
                        <input type="checkbox" id="includevast" value="1" onclick="addVideoReports()">
                    </div>
                    <ul class="nav nav-tabs">
                        @if($user_role!=5)
                            <li class="active">
                                <a data-toggle="tab" href="#bydate">By Date</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#bycamp" onclick="genDatatableById('bycamp','Campaign','campaign')">By Campaign</a>
                            </li>
                        @else
                            <li class="active">
                                <a data-toggle="tab" href="#custom" onclick="setTimeout(tagableDomainInput, 1000);">Custom Report</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        @if($user_role!=5)
                            <div id="bydate" class="tab-pane fade in  active"></div>
                            <div id="bycamp" class="tab-pane fade in"></div>
                            <div id="bygeo" class="tab-pane fade in"></div>
                            <div id="bydevice" class="tab-pane fade in"></div>
                            <div id="bysupply" class="tab-pane fade in"></div>
                            <div id="bydata" class="tab-pane fade in"></div>
                            <div id="custom" class="tab-pane fade in">
                                @else
                                    <div id="custom" class="tab-pane fade in active">
                                        @endif
                                        <form>
                                            <div class="panel panel-primary panel-bordered">
                                                <div class="panel-body">
                                                    <div class="row clearfix">
                                                        <div class="col-md-4 form-group">
                                                            <label>Campaigns</label>
                                                            @php
                                                                $_GET["campaign_id"] = $idprefixed;
                                                            @endphp
                                                            <select class="form-control select2" name="campaigns[]" multiple id="campaign">
                                                                @foreach($campaigns as $val)
                                                                    <option value="{{$val->id+$wlprefix}}">{{$val->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Concepts</label>
                                                            <select class="select2" name="concepts[]" multiple id="concept">
                                                                @foreach($concepts as $val)
                                                                    <option value="{{$val->id+$wlprefix}}">{{$val->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Creatives</label>
                                                            <select class="form-control select2" name="creatives[]" multiple id="creative">
                                                                @foreach($creatives as $val)
                                                                    <option value="{{$val->id+$wlprefix}}">{{$val->name}}</option>
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
                                                                <option value="campaigns">Campaign</option>
                                                                <option value="concreat_1">Concept</option>
                                                                <option value="concreat_2">Creative</option>
                                                                <option value="audience">Segment</option>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script>
        function genDatatableById(id,cvalue,groupby,addvast){
            window.activeDatatableid = id;
            window.activeDatatablecvalue = cvalue;
            window.activeDatatablegroupby = groupby;

            if(document.getElementById("includevast").checked == true){
                addvast = 1;
            } else {
                addvast = "";
            }

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
                    fcamps+="*_"+value+"_*,";
                });

                //Concepts
                $.each( $('#concept').val(), function( key, value ) {
                    fconcepts+=value+"_*,";
                });

                //Domains
                $.each( $('#domain').val(), function( key, value ) {
                    fdomains+="*_"+value+",";
                });

                //Creatives
                $.each( $('#creative').val(), function( key, value ) {
                    fcreatives+="*_"+value+",";
                });

                //Region
                $.each( $('#region').val(), function( key, value ) {
                    fregions+=value+"_*,";
                });

                //IF ROLE 5
                @if($user_role==5)
                if(fcamps =="" && fconcepts =="" && fcreatives == ""){ fcamps="asdfg"; }
                @endif

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
                    case 'campaigns':
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
                    case 'audience':
                        cvalue = "Segment";
                        break;
                }
            } else {

                @if(isset($_GET["campaign_id"]) && $_GET["campaign_id"]!="")
                    fcamps = " {{$campaigns_list}}";
                @endif

            }

            toptabs="";
          
            if(addvast == 1){
                $('#' + id).html(
                    toptabs +
                    '                            <table id="' + id + '_dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">\n' +
                    '                                <thead>\n' +
                    '                                <tr>\n' +
                    '                                    <th>' + cvalue + '</th>\n' +
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
                    '                                    <th>FirstQ</th>\n' +
                    '                                    <th>Middle</th>\n' +
                    '                                    <th>ThirdQ</th>\n' +
                    '                                    <th>Complete</th>\n' +
                    '                                </tr>\n' +
                    '                                </thead>\n' +
                    '                            </table>'
                );
            } else {
                $('#' + id).html(
                    toptabs +
                    '                            <table id="' + id + '_dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">\n' +
                    '                                <thead>\n' +
                    '                                <tr>\n' +
                    '                                    <th>' + cvalue + '</th>\n' +
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
                    '                                    <th>VWI</th>\n' +
                    '                                </tr>\n' +
                    '                                </thead>\n' +
                    '                            </table>'
                );
            }
            if(addvast==1) {
                var builtColumns = [
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
                    {data: "TOS"},
                    {data: "FirstQ"},
                    {data: "Middle"},
                    {data: "ThirdQ"},
                    {data: "Complete"}];
            } else {
                var builtColumns = [
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
                    {data: "TOS"},
                    {data: "VWI"}];
            }
            if(cvalue=="Date"){ orderby = 0; } else { orderby=1; }
     
            var table = $('#'+id+'_dataTable').DataTable({
                "dom": 'Bfrtip',
                "buttons": [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5'
                ],
                "processing": true,
                "order": [[ orderby, "desc" ]],
                "ajax": {
                    "url": "/api/hbreports",
                    "type": "POST",
                    "data": {
                        'groupby': groupby,
                        'from' :  '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                        'until' :  '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}',
                        'organization' :  '{{ $organization_id }}',
                        'uid' :  '{{ isset($user_id) ? $user_id : 0  }}',
                        'urole' :  '{{ isset($user_role) ? $user_role : 0  }}',
                        'filters' : 'campaigns={{ $campaigns_list }}',
                        'addvast' : addvast,
                        'report_for': 'organization'
                    }
                },
                columns: builtColumns,
            
                @if($user_role==5)
                'columnDefs' : [
                    //hide the second & fourth column
                    { 'visible': false, 'targets': [3,4,6,8] }
                ],
                @endif
            });                     

            //}

        }
        function tagableDomainInput(){
            $("#domain").select2({
                tags: true
            });
        }
        window.onload = function() {

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
                    '                                    <th>VWI</th>\n' +
                    '                                </tr>\n' +
                    '                                </thead>\n' +
                    '                            </table>'
                );

                window.activeDatatableid = "bydate";
                window.activeDatatablecvalue = "Date";
                window.activeDatatablegroupby = gby;

                $('#dataTable').DataTable({
                    "dom": 'Bfrtip',
                    "buttons": [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5'
                    ],
                    "processing": true,
                    "ajax": {
                        "url": "/api/hbreports",
                        "type": "POST",
                        "data": {
                            'from': '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                            'until': '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}',
                            'organization' :  '{{ $organization_id  }}',
                            'uid' :  '{{ isset($user_id) ? $user_id : 0  }}',
                            'urole' :  '{{ isset($user_role) ? $user_role : 0  }}',
                            'filters' : 'campaigns={{ $campaigns_list  }}',
                            'report_for': 'organization'
                        }
                    },
                    order: [[ 0, "asc" ]],
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
                        {data: "TOS"},
                        {data: "VWI"}
                    ],
                    @if($user_role==5)
                    'columnDefs' : [
                        //hide the second & fourth column
                        { 'visible': false, 'targets': [3,4,6,8] }
                    ],
                    @endif
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

                    document.location.href="/admin/organizations/reports/{{$organization_id}}?from="+reportStartDate+"&until="+reportUntilDate;

                });

            } );

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



        }

        //add video reports
        function addVideoReports(){
            if(document.getElementById("includevast").checked == true){
                genDatatableById( activeDatatableid ,activeDatatablecvalue, activeDatatablegroupby,1);
            } else {
                genDatatableById( activeDatatableid ,activeDatatablecvalue, activeDatatablegroupby);
            }
        }

    </script>

    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jvectormap-next/jquery-jvectormap.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/chartist/dist/chartist.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery.flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.resize.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot/jquery.flot.time.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/flot.curvedlines/curvedLines.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/sweetalert2.js"></script>

    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/countUp-init.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/cards/total-visits-chart.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/cards/total-unique-visits-chart.js"></script>
    <script>
        $(document).ready(function() {

            setTimeout(function(){
                //$('#total-impressions-week').html(totalImpressions);
                //$('#total-clicks-week').html(totalClicks);
                //$('#total-impressions-month').html(totalImpressionsMonth);
               /*  // $('#total-clicks-month').html(totalClicksMonth);
                numAnim = new CountUp(document.getElementById('total-impressions-week'), 0, totalImpressions);
                numAnim.start();
                numAnim = new CountUp(document.getElementById('total-impressions-month'), 0, totalImpressionsMonth);
                numAnim.start();
                numAnim = new CountUp(document.getElementById('total-clicks-week'), 0, totalClicks);
                numAnim.start();
                numAnim = new CountUp(document.getElementById('total-clicks-month'), 0, totalClicksMonth);
                numAnim.start();
                $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js", function( data, textStatus, jqxhr ) {
                    console.log( 'pdfmake' ); // Data returned
                    $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js", function( data, textStatus, jqxhr ) {
                        console.log( 'vfs_fonts' ); // Data returned
                        $.getScript( "https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.js", function( data, textStatus, jqxhr ) {
                            console.log( 'datatables' ); // Data returned

                        });
                    });
                });
                $.getScript("https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js");
                 $.getScript("https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js");
                 $.getScript("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js");
                 $.getScript("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js");
                 $.getScript("https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js");*/

            }, 2000);

        });
    </script>
@stop