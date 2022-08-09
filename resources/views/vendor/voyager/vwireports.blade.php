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
            <i class="voyager-people"></i> VWI Reports
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
    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
            <li><a data-toggle="tab" onclick="document.location.href='/admin/campaigns/{{ intval($_GET["campaign_id"])-$wlprefix  }}/edit'" href="#"><i class="voyager-book"></i> Summary</a></li>
            <li><a data-toggle="tab" onclick="document.location.href='{{ route('voyager.strategies_campaign.index' , intval($_GET["campaign_id"])-$wlprefix ) }}'" href="#"><i class="voyager-lab"></i> Strategies</a></li>
            <li {!! 'class="active"' !!}><a data-toggle="tab" href="#"><i class="voyager-people"></i> VWI</a></li>
            <li><a data-toggle="tab" href="#commands"><i class="voyager-bar-chart"></i> Reports</a></li>
            <!--<li ><a data-toggle="tab" href="#conversions"><i class="voyager-check"></i> Conversions</a></li>-->
        </ul>
        <div class="tab-content">
                <div style="width: 100%; padding: 20px;">

                            @php if($_GET["campaign_id"]=="2000038"){ @endphp
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="row m-0 col-border-xl">
                                            <div class="col-md-12 col-lg-6 col-xl-3">
                                                <div class="card-body">
                                                    <div class="icon-rounded icon-rounded-primary float-left m-r-20">
                                                        <i class="icon dripicons-location"></i>
                                                    </div>
                                                    <h5 class="card-title m-b-5 counter" data-count="{{$total_loc}}">0</h5>
                                                    <h6 class="text-muted m-t-10">
                                                        Total Locations
                                                    </h6>
                                                    <div class="progress progress-active-sessions mt-4" style="height:7px;">
                                                        <div class="progress-bar bg-primary" role="progressbar" style="" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xl-3">
                                                <div class="card-body">
                                                    <div class="icon-rounded icon-rounded-accent float-left m-r-20">
                                                        <i class="icon dripicons-user"></i>
                                                    </div>
                                                    <h5 class="card-title m-b-5 append-percent counter" data-count="{{$total_vis}}">0</h5>
                                                    <h6 class="text-muted m-t-10">
                                                        Total Visits
                                                    </h6>
                                                    <div class="progress progress-add-to-cart mt-4" style="height:7px;">
                                                        <div class="progress-bar bg-accent" role="progressbar" style="" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xl-3">
                                                <div class="card-body">
                                                    <div class="icon-rounded icon-rounded-info float-left m-r-20">
                                                        <i class="icon dripicons-copy"></i>
                                                    </div>
                                                    <h5 class="card-title m-b-5 counter" data-count="{{$total_imp}}">0</h5>
                                                    <h6 class="text-muted m-t-10">
                                                        Total Impressions
                                                    </h6>
                                                    <div class="progress progress-new-account mt-4" style="height:7px;">
                                                        <div class="progress-bar bg-info" role="progressbar" style="" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 col-xl-3">
                                                <div class="card-body">
                                                    <div class="icon-rounded icon-rounded-success float-left m-r-20">
                                                        <i class="la la-dollar f-w-600"></i>
                                                    </div>
                                                    <h5 class="card-title m-b-5 prepend-currency counter" data-count="7,4">{{$avarage_cpm}}</h5>
                                                    <h6 class="text-muted m-t-10">
                                                        Avarage eCPM
                                                    </h6>
                                                    <div class="progress progress-total-revenue mt-4" style="height:7px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php } @endphp
                            <form>

                                    <div class="panel-body">
                                        <div style="margin-bottom: 20px;">
                                            <label>Creative</label>
                                            <select class="form-control" id="creative_delector">
                                                <option value="0">All Creatives</option>
                                                @foreach($creatives as $creative)
                                                    @php $creativeid = $creative->id+($_ENV["WL_PREFIX"]*1000000); @endphp
                                                    <option value="{{ $creativeid  }}" @php if(isset($_GET["creative_id"])&&$_GET["creative_id"]==$creativeid){ echo "selected"; } @endphp>{{ $creative->name  }}</option>
                                                @endforeach
                                                @if($_GET["campaign_id"]==123)
                                                <option value="2000038" @php if(isset($_GET["creative_id"])&&$_GET["creative_id"]==2000038){ echo "selected"; } @endphp>255332647F31 Royal Bank of Canada Layer 1a 320x480</option>
                                                <option value="2000037" @php if(isset($_GET["creative_id"])&&$_GET["creative_id"]==2000037){ echo "selected"; } @endphp>255003878F21 Royal Bank of Canada Layer1 300x250</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div id="gbytittle" style="font-size: 26px;">By Visits</div>
                                        <hr style="margin-top: 0px;">
                                        <div id="dtContainer">
                                            <table id="dataTable" class="display" style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Location</th>
                                                    <th>Impressions</th>
                                                    <th>CPM</th>
                                                    <th>Clicks</th>
                                                    <th>Conversion Cost</th>
                                                    <th>Time to Convert</th>
                                                    <th>Duration</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                            </form>


                </div>
            </div>

        </div>
    <script src="../dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
    <script src="../dsp-demo/dsp-demo/assets/js/components/countUp-init.js"></script>
    <script>
        $(document).ready(function() {
            $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js", function( data, textStatus, jqxhr ) {
                console.log( 'pdfmake' ); // Data returned
                $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js", function( data, textStatus, jqxhr ) {
                    console.log( 'vfs_fonts' ); // Data returned
                    $.getScript( "https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.js", function( data, textStatus, jqxhr ) {
                        console.log( 'datatables' ); // Data returned

                        $('#dataTable').DataTable( {
                            dom: 'Bfrtip',
                            buttons: [
                                'copyHtml5',
                                'excelHtml5',
                                'csvHtml5',
                                /*{
                                    text: 'My button',
                                    action: function ( e, dt, node, config ) {
                                        alert( 'Button activated' );
                                    }
                                }*/
                            ],
                            "ajax": '../vwis/?campaign_id={{ $_GET["campaign_id"] }}&groupby=Visit'
                        } );
                        $('<div class="pull-right" style="margin-left:5px;">' +
                            '<select class="form-control" id="groupBy">'+
                            '<option value="">Group By</option>'+
                            '<option value="Visit">Visit</option>'+
                            '<option value="Date">Date</option>'+
                            '<option value="Location">Location</option>'+
                            '</select>' +
                            '</div>').appendTo("#dataTable_filter");

                        $('#groupBy').on('change', function() {
                            $('#gbytittle').html("By "+$('#groupBy').val());
                            buildTable($('#groupBy').val());
                        });
                    });
                });
            });
            $("<link/>", {
                rel: "stylesheet",
                type: "text/css",
                href: "/styles/yourcss.css"
            }).appendTo("head");
        } );
        function buildTable(gby){
            $('#dtContainer').html('');

            if(gby=="Visit") {
                $('#dtContainer').html('                                            <table id="dataTable" class="display" style="width:100%">\n' +
                    '                                                <thead>\n' +
                    '                                                <tr>\n' +
                    '                                                    <th>Date</th>\n' +
                    '                                                    <th>Location</th>\n' +
                    '                                                    <th>Impressions</th>\n' +
                    '                                                    <th>CPM</th>\n' +
                    '                                                    <th>Clicks</th>\n' +
                    '                                                    <th>Conversion Cost</th>\n' +
                    '                                                    <th>Time to Convert</th>\n' +
                    '                                                    <th>Duration</th>\n' +
                    '                                                </tr>\n' +
                    '                                                </thead>\n' +
                    '                                            </table>');
            }
            if(gby=="Location") {
                $('#dtContainer').html('                                            <table id="dataTable" class="display" style="width:100%">\n' +
                    '                                                <thead>\n' +
                    '                                                <tr>\n' +
                    '                                                    <th>Location</th>\n' +
                    '                                                    <th>Visits</th>\n' +
                    '                                                    <th>Impressions</th>\n' +
                    '                                                    <th>CPM</th>\n' +
                    '                                                    <th>Clicks</th>\n' +
                    '                                                    <th>Conversion Cost</th>\n' +
                    '                                                    <th>Lift</th>\n' +
                    '                                                    <th>Duration</th>\n' +
                    '                                                </tr>\n' +
                    '                                                </thead>\n' +
                    '                                            </table>');
            }
            if(gby=="Date") {
                $('#dtContainer').html('                                            <table id="dataTable" class="display" style="width:100%">\n' +
                    '                                                <thead>\n' +
                    '                                                <tr>\n' +
                    '                                                    <th>Date</th>\n' +
                    '                                                    <th>Visits</th>\n' +
                    '                                                    <th>Impressions</th>\n' +
                    '                                                    <th>CPM</th>\n' +
                    '                                                    <th>Clicks</th>\n' +
                    '                                                    <th>Conversion Cost</th>\n' +
                    '                                                    <th>Lift</th>\n' +
                    '                                                    <th>Duration</th>\n' +
                    '                                                </tr>\n' +
                    '                                                </thead>\n' +
                    '                                            </table>');
            }

            $('#dataTable').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    /*{
                        text: 'My button',
                        action: function ( e, dt, node, config ) {
                            alert( 'Button activated' );
                        }
                    }*/
                ],
                @php if(isset($_GET["creative_id"])){ $creative_id=$_GET["creative_id"]; } else { $creative_id=""; } @endphp
                "ajax": '../vwis/?campaign_id={{ $_GET["campaign_id"] }}&creative_id={{ $creative_id  }}&groupby='+gby
            } );
            $('<div class="pull-right" style="margin-left:5px;">' +
                '<select class="form-control" id="groupBy">'+
                '<option value="">Group By</option>'+
                '<option value="Visit">Visit</option>'+
                '<option value="Date">Date</option>'+
                '<option value="Location">Location</option>'+
                '</select>' +
                '</div>').appendTo("#dataTable_filter");
            $('#groupBy').on('change', function() {
                $('#gbytittle').html("By "+$('#groupBy').val());
                buildTable($('#groupBy').val());
            });
        }
        $( "#creative_delector" ).change(function() {
            console.log($(this).children("option:selected").val());
            if($(this).children("option:selected").val()!=0) {
                document.location.href = "/admin/vwireports?campaign_id={{$_GET["campaign_id"]}}&creative_id=" + $(this).children("option:selected").val();
            } else {
                document.location.href = "/admin/vwireports?campaign_id={{$_GET["campaign_id"]}}";
            }
        });
    </script>
@stop