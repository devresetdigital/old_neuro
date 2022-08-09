@extends('voyager::master')
@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-people"></i> Linear Reports
        </h1>
    </div>
@stop
@section('content')

    <!-- ======================= LINE AWESOME ICONS ===========================-->
    <link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/line-awesome.min.css">
    <link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/simple-line-icons.css">
    <!-- ======================= DRIP ICONS ===================================-->
    <link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/dripicons.min.css">
    @include('voyager::compass.includes.styles')
    @include('voyager::alerts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.5/jquery.csv.min.js"></script>
    <div class="page-content compass container-fluid">
        <div class="tab-content">
            <button style="margin-left: 20px;" type="button" class="btn btn-primary" onclick="toggleTables('queue')">Pacing Reports</button> <button type="button" class="btn btn-primary" onclick="toggleTables('current')">DMA Summary</button> <button type="button" class="btn btn-primary" onclick="toggleTables('finished')">DMA Reports</button><br>
            <div class="card" id="queue" style="display: inline">
                <h5 class="card-header">Pacing Reports</h5>
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
                                            <th>Contracted</th>
                                            <th>Delivery %</th>
                                            <th>Delivery</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>AEN</td>
                                            <td>1,415,045,928</td>
                                            <td>0.39 %</td>
                                            <td>5,528,531</td>
                                        </tr>
                                        <tr>
                                            <td>APL</td>
                                            <td>1,354,051,854</td>
                                            <td>1.11 %</td>
                                            <td>14,871,727</td>
                                        </tr>
                                        <tr>
                                            <td>CNN</td>
                                            <td>326,766,748</td>
                                            <td>0.71 %</td>
                                            <td>2,307,285</td>
                                        </tr>
                                        <tr>
                                            <td>FOXNC</td>
                                            <td>266,794,980</td>
                                            <td>1.06 %</td>
                                            <td>2,803,601</td>
                                        </tr>
                                        <tr>
                                            <td>FXM</td>
                                            <td>210,867,954</td>
                                            <td>0.75 %</td>
                                            <td>1,579,676</td>
                                        </tr>
                                        <tr>
                                            <td>HLN</td>
                                            <td>200,813,818</td>
                                            <td>1.93 %</td>
                                            <td>3,797,863</td>
                                        </tr>
                                        <tr>
                                            <td>LOGO</td>
                                            <td>195,875,237</td>
                                            <td>2.61 %</td>
                                            <td>4,988,926</td>
                                        </tr>
                                        <tr>
                                            <td>MSNBC</td>
                                            <td>166,368,149</td>
                                            <td>1.03 %</td>
                                            <td>1,698,398</td>
                                        </tr>
                                        <tr>
                                            <td>OXYG</td>
                                            <td>143,964,709</td>
                                            <td>-0.02 %</td>
                                            <td>-25,045</td>
                                        </tr>
                                        <tr>
                                            <td>TBSC</td>
                                            <td>130,759,074</td>
                                            <td>1.24 %</td>
                                            <td>1,595,798</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold">Total</td>
                                            <td style="font-weight: bold">10210,867,954</td>
                                            <td style="font-weight: bold">87 %</td>
                                            <td style="font-weight: bold">11,579,676</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <div class="card">
                        <h5 class="card-header">M-Su</h5>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Early Morning (6a-9a)</th>
                                        <th>Contracted</th>
                                        <th>Delivery %</th>
                                        <th>Delivery</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Daytime (9a-4p)</td>
                                        <td>1,415,045,928</td>
                                        <td>0.39 %</td>
                                        <td>5,528,531</td>
                                    </tr>
                                    <tr>
                                        <td>Early Fringe (4-8p)</td>
                                        <td>1,354,051,854</td>
                                        <td>1.11 %</td>
                                        <td>14,871,727</td>
                                    </tr>
                                    <tr>
                                        <td>Prime (8p-12m)</td>
                                        <td>326,766,748</td>
                                        <td>0.71 %</td>
                                        <td>2,307,285</td>
                                    </tr>
                                    <tr>
                                        <td>Late Fringe (12m-2a)</td>
                                        <td>266,794,980</td>
                                        <td>1.06 %</td>
                                        <td>2,803,601</td>
                                    </tr>
                                    <tr>
                                        <td>Overnight (2a-6a)</td>
                                        <td>210,867,954</td>
                                        <td>0.75 %</td>
                                        <td>1,579,676</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Total</td>
                                        <td style="font-weight: bold">10210,867,954</td>
                                        <td style="font-weight: bold">87 %</td>
                                        <td style="font-weight: bold">11,579,676</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="card-deck m-b-30">

                        <div class="card">
                            <h5 class="card-header">Weekly Delivery</h5>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Week</th>
                                            <th>Contracted</th>
                                            <th>Delivery %</th>
                                            <th>Delivery</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>4/26/21</td>
                                            <td>1,415,045,928</td>
                                            <td>0.39 %</td>
                                            <td>5,528,531</td>
                                        </tr>
                                        <tr>
                                            <td>5/3/21</td>
                                            <td>1,354,051,854</td>
                                            <td>1.11 %</td>
                                            <td>14,871,727</td>
                                        </tr>
                                        <tr>
                                            <td>5/10/21</td>
                                            <td>326,766,748</td>
                                            <td>0.71 %</td>
                                            <td>2,307,285</td>
                                        </tr>
                                        <tr>
                                            <td>5/17/21</td>
                                            <td>266,794,980</td>
                                            <td>1.06 %</td>
                                            <td>2,803,601</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold">Total</td>
                                            <td style="font-weight: bold">10210,867,954</td>
                                            <td style="font-weight: bold">87 %</td>
                                            <td style="font-weight: bold">11,579,676</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <h5 class="card-header">Charts</h5>
                            <div class="card-body">
                                <div class="card-body">
                                    <div id="c3_donut"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" id="current" style="display: none">
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
                                                <th>A25-54 IMPs</th>
                                                <th>HH IMPs</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>New York</td>
                                                <td>1,348,531</td>
                                                <td>5,528,531</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Los Angeles</td>
                                                <td>14,871,727</td>
                                                <td>14,871,727</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Chicago</td>
                                                <td>14,871,727</td>
                                                <td>2,307,285</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Philadelphia</td>
                                                <td>1.06 </td>
                                                <td>2,803,601</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Dallas-Ft. Worth</td>
                                                <td>23,445.75</td>
                                                <td>1,579,676</td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>Houston</td>
                                                <td>1.93 </td>
                                                <td>3,797,863</td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>San Francisco-Oak-San Jose</td>
                                                <td>14,871,727</td>
                                                <td>4,988,926</td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>Atlanta</td>
                                                <td>1.03 </td>
                                                <td>1,698,398</td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>Washington, DC (Hagrstwn)</td>
                                                <td>133,340.02 </td>
                                                <td>-25,045</td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td>Boston (Manchester)</td>
                                                <td>143,563.24</td>
                                                <td>1,595,798</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold"> </td>
                                                <td style="font-weight: bold">National</td>
                                                <td style="font-weight: bold">111,287</td>
                                                <td style="font-weight: bold">11,579,676</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <div class="card" id="finished" style="display: none">
                <div class="card-body">
                    <div class="card-deck m-b-30">
                        <div class="card">
                            <h5 class="card-header">DMA Report</h5>
                            <div class="card-body">
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
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Creative Isci</th>
                                            <th>Advertiser Name</th>
                                            <th>Spot Length</th>
                                            <th>Broadcast Date</th>
                                            <th>Network</th>
                                            <th>Delivery Insertion Date</th>
                                            <th>Delivery Insertion Time of Day</th>
                                            <th>Dma Name</th>
                                            <th>Demo</th>
                                            <th>Program</th>
                                            <th>Daypart Name</th>
                                            <th>Total Delivered Impressions</th>
                                            <th>Net CPM</th>
                                            <th>Estimated Net Spend</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>ISCI</td>
                                            <td>Sample</td>
                                            <td>15</td>
                                            <td>2021-05-28</td>
                                            <td>OXYG</td>
                                            <td>2021-05-30</td>
                                            <td>08:43</td>
                                            <td>Duluth-Superior</td>
                                            <td>A25-54</td>
                                            <td>BURIED IN THE BACKYARD</td>
                                            <td>Early Morning (6-9am)</td>
                                            <td>15</td>
                                            <td>$5.13</td>
                                            <td>$0.07</td>
                                        </tr>
                                        <tr>
                                            <td>ISCI</td>
                                            <td>Sample</td>
                                            <td>15</td>
                                            <td>2021-05-28</td>
                                            <td>OXYG</td>
                                            <td>2021-05-30</td>
                                            <td>08:43</td>
                                            <td>Duluth-Superior</td>
                                            <td>A25-54</td>
                                            <td>BURIED IN THE BACKYARD</td>
                                            <td>Early Morning (6-9am)</td>
                                            <td>8</td>
                                            <td>$5.23</td>
                                            <td>$0.03</td>
                                        </tr>
                                        <tr>
                                            <td>ISCI</td>
                                            <td>Sample</td>
                                            <td>15</td>
                                            <td>2021-05-28</td>
                                            <td>OXYG</td>
                                            <td>2021-05-30</td>
                                            <td>08:43</td>
                                            <td>Duluth-Superior</td>
                                            <td>A25-54</td>
                                            <td>BURIED IN THE BACKYARD</td>
                                            <td>Early Morning (6-9am)</td>
                                            <td>8</td>
                                            <td>$5.23</td>
                                            <td>$0.03</td>
                                        </tr>
                                        <tr>
                                            <td>ISCI</td>
                                            <td>Sample</td>
                                            <td>15</td>
                                            <td>2021-05-28</td>
                                            <td>OXYG</td>
                                            <td>2021-05-30</td>
                                            <td>08:43</td>
                                            <td>Duluth-Superior</td>
                                            <td>A25-54</td>
                                            <td>BURIED IN THE BACKYARD</td>
                                            <td>Early Morning (6-9am)</td>
                                            <td>8</td>
                                            <td>$5.23</td>
                                            <td>$0.03</td>
                                        </tr>
                                        <tr>
                                            <td>ISCI</td>
                                            <td>Sample</td>
                                            <td>15</td>
                                            <td>2021-05-28</td>
                                            <td>OXYG</td>
                                            <td>2021-05-30</td>
                                            <td>08:43</td>
                                            <td>Duluth-Superior</td>
                                            <td>A25-54</td>
                                            <td>BURIED IN THE BACKYARD</td>
                                            <td>Early Morning (6-9am)</td>
                                            <td>18</td>
                                            <td>$3.23</td>
                                            <td>$0.04</td>
                                        </tr>
                                        <tr>
                                            <td>ISCI</td>
                                            <td>Sample</td>
                                            <td>15</td>
                                            <td>2021-05-28</td>
                                            <td>OXYG</td>
                                            <td>2021-05-30</td>
                                            <td>08:43</td>
                                            <td>Duluth-Superior</td>
                                            <td>A25-54</td>
                                            <td>BURIED IN THE BACKYARD</td>
                                            <td>Early Morning (6-9am)</td>
                                            <td>28</td>
                                            <td>$1.23</td>
                                            <td>$0.03</td>
                                        </tr>
                                        <tr>
                                            <td>ISCI</td>
                                            <td>Sample</td>
                                            <td>15</td>
                                            <td>2021-05-28</td>
                                            <td>OXYG</td>
                                            <td>2021-05-30</td>
                                            <td>08:43</td>
                                            <td>Duluth-Superior</td>
                                            <td>A25-54</td>
                                            <td>BURIED IN THE BACKYARD</td>
                                            <td>Early Morning (6-9am)</td>
                                            <td>8</td>
                                            <td>$5.23</td>
                                            <td>$0.03</td>
                                        </tr>
                                        <tr>
                                            <td>ISCI</td>
                                            <td>Sample</td>
                                            <td>15</td>
                                            <td>2021-05-28</td>
                                            <td>OXYG</td>
                                            <td>2021-05-30</td>
                                            <td>08:43</td>
                                            <td>Duluth-Superior</td>
                                            <td>A25-54</td>
                                            <td>BURIED IN THE BACKYARD</td>
                                            <td>Early Morning (6-9am)</td>
                                            <td>68</td>
                                            <td>$1.23</td>
                                            <td>$0.13</td>
                                        </tr>
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
        <script src="../dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
        <script src="../dsp-demo/dsp-demo/assets/js/components/countUp-init.js"></script>
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
        <script>
            var start = moment().subtract(6, 'days');
            var end = moment();

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
            function toggleTables(table){

                document.getElementById('queue').style.display="none"
                document.getElementById('current').style.display="none";
                document.getElementById('finished').style.display="none";

                document.getElementById(table).style.display="inline";

            }
        </script>

@stop