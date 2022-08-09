@extends('voyager::master')
@section('content')
    <script>
       /* $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: "https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"
        }).appendTo("head");
        $("<link/>", {
            rel: "stylesheet",
            type: "text/css",
            href: "https://cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css"
        }).appendTo("head");*/
        <?php if($_SERVER["HTTP_HOST"] == "panel.neuro-programmatic.com"){ ?>
            document.location.href="http://panel.neuro-programmatic.com/admin/X2_report";
        <?php } ?>
    </script>
    <div class="page-content">
        @include('voyager::alerts')
        <div class="analytics-container">

                <div style="width: 100%;">
                @if($user_role == 1)
                <button type="button" class="btn btn-primary" onclick="document.location.href='/admin/reports_embeded'">Test mode</button>
                @endif  
                    @if($user_role!=5)
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card">
                                <div class="card-header">Campaign Reports</div>
                                <div class="card-body" style="max-height: 450px;">
                                    <canvas id="myChart" width="350" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-xxl-5">
                            @if($user_role!=5)
                            <div class="card">
                                <div class="card-header">Top Campaigns</div>
                                <div class="card-body" style="max-height: 420px; overflow-y:scroll;">
                                    <div class="topCampaings" style="max-height: 420px; overflow-y:scroll;">
                                        <table id="topCampaignsTable" class="table table-hover dataTable no-footer" style="width:100%"></table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>
                    @endif
                    <div class="panel">
                        <div class="row" style="margin-top: 10px; margin-right: 20px; margin-bottom: 10px;">
                            <div style="width: 100%; text-align: right">
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 330px; float: right; text-align: left">
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
                                            if(env('WL_PREFIX') !="" || env('WL_PREFIX') !="0"){
                                                $float_wlprefix = env('WL_PREFIX').".0";
                                                $wlprefix = (float) $float_wlprefix*1000000;
                                            } else {
                                                $wlprefix=0;
                                            }
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
                                                <option value="advcamp_2">Campaign</option>
                                                <option value="concreat_1">Concept</option>
                                                <option value="advcamp_3">Strategy</option>
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
                                            <button class="btn btn-primary" type="button" onclick="genDatatableById('customreport','Values','channeldomain_2')">Calculate</button>
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
                    //Groupby
                    groupby= $('#groupby').val();
                    /*$.each( $('#groupby').val(), function( key, value ) {
                        groupby+=value+",";
                    });*/

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

                   // groupby = $("#groupby").val();

                    //GroupBy
                   /* var fgroupby = "";
                    $('#groupby').val().forEach(function(element) {
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
                        case 'advcamp_3':
                            cvalue = "Strategy";
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
                        fcamps = "*_{{ $_GET["campaign_id"]  }}_*";
                    @endif

                }

                toptabs="";
                if(id == "bygeo"){
                    toptabs ='<ul class="nav nav-pills" style="margin-bottom:10px;">' +
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
                @php //if(isset($_GET["campaign_id"]) && $_GET["campaign_id"]!="" ){ @endphp
                if(id == "bycamp"){
                    toptabs ='<ul class="nav nav-pills" style="margin-bottom:10px;">' +
                        '<li class="nav-item" role="presentation">\n' +
                        '<a href="#tab-1" onclick="genDatatableById(\'bycamp\',\'Strategy\',\'advcamp_3\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Strategy</a>\n' +
                        '</li>' +
                        '<li class="nav-item" role="presentation">\n' +
                        '<a href="#tab-1" onclick="genDatatableById(\'bycamp\',\'Creative\',\'concreat_2\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Creative</a>\n' +
                        '</li>' +
                        '</ul>';
                }
                @php //} @endphp
                if(id == "bysupply"){
                    toptabs ='<ul class="nav nav-pills" style="margin-bottom:10px;">' +
                        '<li class="nav-item" role="presentation">\n' +
                        '<a href="#tab-1" class="nav-link active show" data-toggle="tab" aria-expanded="true">Web</a>\n' +
                        '</li>' +
                        '<li class="nav-item" role="presentation">\n' +
                        '<a href="#tab-1" class="nav-link active show" data-toggle="tab" aria-expanded="true">App</a>\n' +
                        '</li>' +
                        '<li class="nav-item" role="presentation">\n' +
                        '<a href="#tab-1" class="nav-link active show" data-toggle="tab" aria-expanded="true">ALL</a>\n' +
                        '</li>' +
                        '</ul>';
                }
                if(id == "bydevice"){
                    toptabs ='<ul class="nav nav-pills" style="margin-bottom:10px;">' +
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
                        {
                            data: "Spent",
                            width: '80px',
                            render: function(data, type) {
                                var number = $.fn.dataTable.render.number( ',', '.', 2, '$'). display(data);
            
                                if (type === 'display') {
                                    return '<span>' + number + '</span>';
                                }
                                return number;
                            }
                        },
                        {
                            data: "eCPM",
                            width: '80px',
                            render: function(data, type) {
                                var number = $.fn.dataTable.render.number( ',', '.', 2, '$'). display(data);
            
                                if (type === 'display') {
                                    return '<span>' + number + '</span>';
                                }
                                return number;
                            }
                        },
                        {data: "CTR"},
                        {
                            data: "CPC",
                            width: '60px',
                            render: function(data, type) {
                                var number = $.fn.dataTable.render.number( ',', '.', 2, '$'). display(data);
            
                                if (type === 'display') {
                                    return '<span>' + number + '</span>';
                                }
                                return number;
                            }
                        },
                        {data: "Conversions"},
                        {data: "CPA"},
                        {data: "Viewability"},
                        {data: "TOS"},
                        {data: "VWI"}];
                }
                if(cvalue=="Date"){ orderby = 0; } else { orderby=1; }
                $('#'+id+'_dataTable').DataTable({
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
                            'advcamps' : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
                            'from' :  '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                            'until' :  '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}',
                            'organization' :  '{{ isset($userorganization) ? $userorganization : 10  }}',
                            'uid' :  '{{ isset($user_id) ? $user_id : 0  }}',
                            'urole' :  '{{ isset($user_role) ? $user_role : 0  }}',
                            'filters' : 'advcamps='+fcamps+'&concreats='+fconcepts+fcreatives+'&channeldomains='+fdomains+'&countryisps='+fcountries+'&regioncities='+fregions,
                            'addvast' : addvast
                        }
                    },
                    columns: builtColumns,

                    @if($user_role==1000)
                    'columnDefs' : [
                        //hide the second & fourth column
                        { 'visible': false, 'targets': [3,4,6,8] }
                    ],
                    @else
                    'columnDefs' : [
                        //hide the second & fourth column
                        { 'visible': false, 'targets': [11] }
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
                $.fn.dataTable.ext.errMode = 'throw';
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

                var table = $('#dataTable').DataTable({
                    "autoWidth": false,
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
                            'advcamps' : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
                            'from': '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                            'until': '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}',
                            'organization' :  '{{ isset($userorganization) ? $userorganization : 0  }}',
                            'uid' :  '{{ isset($user_id) ? $user_id : 0  }}',
                            'urole' :  '{{ isset($user_role) ? $user_role : 0  }}',
                            'filters' : ''
                        }
                    },
                    order: [[ 0, "asc" ]],
                    columns: [
                        {data: "Date"},
                        {data: "Impressions"},
                        {data: "Clicks"},
                        {
                            data: "Spent",
                            width: '80px',
                            render: function(data, type) {
                                var number = $.fn.dataTable.render.number( ',', '.', 2, '$'). display(data);
            
                                if (type === 'display') {
                                    return '<span>' + number + '</span>';
                                }
                                return number;
                            }
                        },
                        {
                            data: "eCPM",
                            width: '80px',
                            render: function(data, type) {
                                var number = $.fn.dataTable.render.number( ',', '.', 2, '$'). display(data);
            
                                if (type === 'display') {
                                    return '<span>' + number + '</span>';
                                }
                                return number;
                            }
                        },
                        {data: "CTR"},
                        {
                            data: "CPC",
                            width: '60px',
                            render: function(data, type) {
                                var number = $.fn.dataTable.render.number( ',', '.', 2, '$'). display(data);
            
                                if (type === 'display') {
                                    return '<span>' + number + '</span>';
                                }
                                return number;
                            }
                        },
                        {data: "Conversions"},
                        {data: "CPA"},
                        {data: "Viewability"},
                        {data: "TOS"},
                        {data: "VWI"}
                    ],
                    @if($user_role==100)
                    'columnDefs' : [
                        //hide the second & fourth column
                        { 'visible': false, 'targets': [3,4,6,8] }
                    ],
                    @else
                    'columnDefs' : [
                        //hide the second & fourth column
                        { 'visible': false, 'targets': [11] }
                    ],
                    @endif
                    columnDefs: [
                        {
                            targets: [3,4,6],
                            className: 'dt-head-center dt-body-right'
                        }
                    ],
                });
                //ADD DATE PICKER
                @if(isset($_GET["from"]) && isset($_GET["until"]))
                    var start = moment("20{{substr($_GET["from"],0, strlen($_GET["from"])-2)  }}");
                    var end = moment("20{{substr($_GET["until"],0, strlen($_GET["until"])-2)  }}");
                @else
                    var start = moment().subtract(6, 'days');
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
            var timeDiff = Math.abs(date2.getTime() - date1.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));


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
                    advcamps : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
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
                        if (value.Date !== 'Totals') {
                            labels.push(value.Date);
                            valuesimpressions.push(value.Impressions);
                            valuesclicks.push(value.Clicks);
                        }
                    });

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

                    let charHeight = $('#myChart').height();
                    $(".topCampaings").height(charHeight);

            },'json');

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
                    advcamps : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
                    organization :  '{{ isset($userorganization) ? $userorganization : 0  }}',
                    uid :  '{{ isset($user_id) ? $user_id : 0  }}',
                    urole :  '{{ isset($user_role) ? $user_role : 0  }}',
                    from :  moment().subtract(7, 'days').format('YYMMDD')+'00',
                    until :  moment().format('YYMMDD')+'23'
                },function(data){
                    totalImpressions=0;
                    totalClicks=0;
                    $.each(data.data, function( index, value ) {
                        totalImpressions = +totalImpressions + +value.Impressions;
                        totalClicks = +totalClicks + +value.Clicks;
                    });
                },'json');

            let topCampaigns = null;

            $.post("/api/hbreports",
                {
                    groupby: 'advcamp_2',
                    uid :  '{{ isset($user_id) ? $user_id : 0  }}',
                    urole :  '{{ isset($user_role) ? $user_role : 0  }}',
                    from :  moment().subtract(7, 'days').format('YYMMDD')+'00',
                    until :  moment().format('YYMMDD')+'23'
                },function({data}){
                    topCampaigns = data.sort((a, b) => b.Impressions - a.Impressions).slice(1).slice(0, 10)
                    fillTopCampaings(topCampaigns);
                },'json');
        }

        function fillTopCampaings(data) {

            var r = new Array();
            var j = -1;
            r[++j] = '<tr><th>Campaigns</th><th>Impressions</th><th>Clicks</th><th>Spent</th></tr><tbody';
            data.forEach(({Campaign, Impressions, Clicks, Spent, eCPM}) => {
                r[++j] = '<tr><th>';
                r[++j] = Campaign;
                r[++j] = '</th>';
                r[++j] = '<td class="text-right">';
                r[++j] = Impressions;
                r[++j] = '</td>';
                r[++j] = '<td class="text-right">';
                r[++j] = Clicks;
                r[++j] = '</td>';
                r[++j] = '<td class="text-right">$';
                r[++j] = Spent;
                r[++j] = '</td>';
            });

            r[++j] = '</td></tr></tbody>';

            $('#topCampaignsTable').html(r.join(''));
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
           // $('#total-clicks-month').html(totalClicksMonth);
            $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js", function( data, textStatus, jqxhr ) {
                $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js", function( data, textStatus, jqxhr ) {
                    $.getScript( "https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.js", function( data, textStatus, jqxhr ) {

                    });
                });
            });
           /* $.getScript("https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js");
            $.getScript("https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js");
            $.getScript("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js");
            $.getScript("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js");
            $.getScript("https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js");*/

        }, 2000);

    });
</script>
@stop