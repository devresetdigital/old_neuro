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
            <i class="voyager-people"></i> Performance Reports
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
        <!--<ul class="nav nav-tabs">
            <li><a data-toggle="tab" onclick="document.location.href='/admin/campaigns/{{ intval($_GET["campaign_id"])-$wlprefix  }}/edit'" href="#"><i class="voyager-book"></i> Summary</a></li>
            <li><a data-toggle="tab" onclick="document.location.href='{{ route('voyager.strategies.index') }}?campaign_id={{ intval($_GET["campaign_id"])-$wlprefix  }}'" href="#"><i class="voyager-lab"></i> Strategies</a></li>
            <li {!! 'class="active"' !!}><a data-toggle="tab" href="#"><i class="voyager-people"></i> VWI</a></li>
            <li><a data-toggle="tab" href="#commands"><i class="voyager-bar-chart"></i> Reports</a></li>
            <!--<li ><a data-toggle="tab" href="#conversions"><i class="voyager-check"></i> Conversions</a></li>-->
        <!--</ul>-->
        <div class="tab-content">
            @php if($_GET["campaign_id"]==0){ @endphp
            <div>Campaign: <select id="selectCampaign" onchange="document.location.href='?campaign_id='+this.options[this.selectedIndex].value"><option value="">Select Campaign</option><option value="0000001">Campaign 0000001</option></select></div>
            @php } else { @endphp
            <div>                Group By:<br>
                <select id="groupBy" onchange="buildTable(this.options[this.selectedIndex].value); toggleDate();">
                    <option value="dates">Date</option>
                    <option value="conv">Conversions</option>
                    <option value="clics">Clicks</option>
                </select>
                <br><br>
                <div id="dateDiv" style="display: none;">
                Date:<br>
                    <input type="date" name="date" id="date" value="@php echo date("Y-m-d") @endphp" /> <button onclick="buildTable(document.getElementById('groupBy').options[document.getElementById('groupBy').selectedIndex].value);">GO</button>
                </div>
            </div>
            <div style="overflow-x: scroll;">
                <div id="dtContainer"></div>
            </div>
            @php }  @endphp
        </div>

    </div>
    <script>
        function buildTable(gby) {
            if (gby=="dates") {

            $('#dtContainer').html('                                            <table id="dataTable" class="display" style="width:100%">\n' +
                '                                                <thead>\n' +
                '                                                <tr>\n' +
                '                                                    <th>DATE</th>\n' +
                '                                                    <th>CLICKS</th>\n' +
                '                                                    <th>CONVERSIONS</th>\n' +
                '                                                </tr>\n' +
                '                                                </thead>\n' +
                '                                            </table>');
            }
            if (gby=="conv") {

                $('#dtContainer').html('                                            <table id="dataTable" class="display" style="width:100%">\n' +
                    '                                                <thead>\n' +
                    '                                                <tr>\n' +
                    '                                                    <th>TIME</th>\n' +
                    '                                                    <th>REGION</th>\n' +
                    '                                                    <th>CITY</th>\n' +
                    '                                                    <th>ISP</th>\n' +
                    '                                                    <th>DEVICE</th>\n' +
                    '                                                    <th>OS</th>\n' +
                    '                                                </tr>\n' +
                    '                                                </thead>\n' +
                    '                                            </table>');
            }
            if (gby=="clics") {

                $('#dtContainer').html('                                            <table id="dataTable" class="display" style="width:100%">\n' +
                    '                                                <thead>\n' +
                    '                                                <tr>\n' +
                    '                                                    <th>HOUR</th>\n' +
                    '                                                    <th>REGION</th>\n' +
                    '                                                    <th>CITY</th>\n' +
                    '                                                    <th>ISP</th>\n' +
                    '                                                    <th>DEVICE</th>\n' +
                    '                                                    <th>OS</th>\n' +
                    '                                                    <th>SOURCE</th>\n' +
                    '                                                    <th>CLIENT</th>\n' +
                    '                                                    <th>AD ID</th>\n' +
                    '                                                    <th>ADSET ID</th>\n' +
                    '                                                    <th>CAMPAIGN ID</th>\n' +
                    '                                                    <th>AD NAME</th>\n' +
                    '                                                    <th>ADSET NAME</th>\n' +
                    '                                                    <th>CAMPAIGN NAME</th>\n' +
                    '                                                    <th>PLACEMENT</th>\n' +
                    '                                                    <th>TRAFFIC</th>\n' +
                    '                                                    <th>CREATIVE</th>\n' +
                    '                                                    <th>TYPE</th>\n' +
                    '                                                </tr>\n' +
                    '                                                </thead>\n' +
                    '                                            </table>');
            }
           // $.fn.dataTable.ext.errMode = 'throw';
            $('#dataTable').DataTable( {
                pageLength: 50,
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
                "ajax": '../../preports/?campaign_id={{ $_GET["campaign_id"] }}&creative_id={{ $creative_id  }}&groupby='+gby+'&date='+$("#date").val()
            } );

        }
        $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js", function( data, textStatus, jqxhr ) {
            console.log( 'pdfmake' ); // Data returned
            $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js", function( data, textStatus, jqxhr ) {
                console.log( 'vfs_fonts' ); // Data returned
                $.getScript( "https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.js", function( data, textStatus, jqxhr ) {
                    console.log('datatables'); // Data returned
                    buildTable("dates");
                });
            });
        });
        function toggleDate() {
            var gbyValue = document.getElementById('groupBy');
            if(gbyValue.options[gbyValue.selectedIndex].value == "dates"){
                document.getElementById('dateDiv').style.display="none";
            } else {
                document.getElementById('dateDiv').style.display="inline";
            }
        }
    </script>
    <script src="../dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
    <script src="../dsp-demo/dsp-demo/assets/js/components/countUp-init.js"></script>
@stop