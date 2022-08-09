@extends('voyager::master')
@section('css')

<link rel="stylesheet" href="{{ asset('css/strategies-edit.css') }}">
@stop
@section('content')
    <div class="page-content">
        @include('voyager::alerts')

        <div class="analytics-container">
            <div class="panel">
                <div style="width: 100%; padding: 20px;">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#custom">Pixel Analytics</a>
                        </li>
                        <h4 style="padding-left: 9em;">Last Update: {{$pixel->last_update}}</h4>
                    </ul>
                    <canvas id="myChart" width="400" height="80"></canvas>
                    <div style="margin-top: 10px;" class="tab-content">
                        <div id="custom" class="tab-pane fade in active">
                            <form>
                                <div class="panel panel-primary panel-bordered">
                                    <div class="panel-body">
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Countries</label>
                                                <input class="form-control bootstrap-tagsinput tagsinput" name="countries" id="countries" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Region</label>
                                                <input class="form-control bootstrap-tagsinput tagsinput" name="regions" id="regions" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>City</label>
                                                <input class="form-control bootstrap-tagsinput tagsinput" name="cities" id="cities" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Devices</label>
                                                <input class="form-control bootstrap-tagsinput tagsinput" name="devices" id="devices" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Os</label>
                                                <select class="form-control select2" name="oss[]" multiple="" id="oss" tabindex="-1" aria-hidden="true">
                                                    <option value="CHROMEOS">CHROMEOS</option>
                                                    <option value="WINDOWS">WINDOWS</option>
                                                    <option value="TIZEN">TIZEN</option>
                                                    <option value="ROKUOS">ROKUOS</option>
                                                    <option value="*">UNKNOW</option>
                                                    <option value="OTHER">OTHER</option>
                                                    <option value="MACOS">MACOS</option>
                                                    <option value="LINUX">LINUX</option>
                                                    <option value="IOS">IOS</option>
                                                    <option value="ANDROID">ANDROID</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Browsers </label>
                                                <input class="form-control bootstrap-tagsinput tagsinput" name="browsers" id="browsers" >
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Titles</label>
                                                <input class="form-control bootstrap-tagsinput tagsinput" name="titles" id="titles" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Domains</label>
                                                <input class="form-control bootstrap-tagsinput tagsinput" name="domains" id="domains"  >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Keys</label>
                                                <input class="form-control bootstrap-tagsinput tagsinput" name="keys" id="keys" >
                                         
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Hours</label>
                                                <select class="form-control select2" name="hours[]" id="hours" multiple>
                                                    @for($i=0; $i<24; $i++) 
                                                    <option value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Dates</label>
                                                <div style="display: flex;">
                                                    <input class="form-control" style="width: 200px;"  value="<?php echo date('Y-m-d',strtotime( "yesterday" )); ?>" type="date" name="from" id="from" placeholder="From">
                                                    <input  value="<?php echo date('Y-m-d',strtotime( "yesterday" )); ?>" class="form-control" style="width: 200px;" type="date" name="until" id="until" placeholder="Until">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Group By</label>
                                                <select class="form-control" name="groupby[]" id="groupby" multiple>
                                                    <option value="country">Countries</option>
                                                    <option value="regioncity_1">Regions</option>
                                                    <option value="regioncity_2">Cities</option>
                                                    <option value="deviceosbrowser_1">Devices</option>
                                                    <option value="deviceosbrowser_2">Os</option>
                                                    <option value="deviceosbrowser_3">Browsers</option>
                                                    <option value="domain">Domains</option>
                                                    <option value="title">Titles</option>
                                                    <option value="date">Dates</option>
                                                    <option value="hour">Hours</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-6 form-group">
                                                <button class="btn btn-primary" type="button" onclick="genDatatable(); loadChart();">Calculate</button>
                                                <a class="btn btn-info" id="exportcsv" target="_blank" href="">Export csv</a>
                                            </div>
                                        </div>
                                        <div id="dataTable"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="{{ asset('js/tagsinputs.js') }}"></script>

    <script>
   
        function updateExportLink(){

            let params = {
                countries : $('#countries').val(),
                regions : $('#regions').val(),
                cities : $('#cities').val(),
                devices : $('#devices').val(),
                oss : $('#oss').val(),
                browsers : $('#browsers').val(),
                titles : $('#titles').val() ,
                domains : $('#domains').val(),
                keys : $('#keys').val(),
                groupBy : $('#groupby').val() != '' ? $('#groupby').val() : ['date'],
                hours : $('#hours').val(),
                from: $('#from').val(),
                until: $('#until').val(),
                pixelid : '{{ isset($_GET["pixelid"]) ? $_GET["pixelid"] : '' }}'
            };

            let query = $.param(params);

            $("#exportcsv").attr('href','/api/exports/pixel_analytics?'+query);

        }
        
        function genDatatable(){
            //Filters
            //Countries
            updateExportLink();
            
            let countries = $('#countries').val();
            let regions = $('#regions').val();
            let cities = $('#cities').val();
            let devices = $('#devices').val();
            let oss = $('#oss').val();
            let browsers = $('#browsers').val();
            let titles = $('#titles').val();
            let domains = $('#domains').val();
            let keys = $('#keys').val();
            let groupBy = $('#groupby').val();
            let hours = $('#hours').val();


            if(groupBy.length === 0){
                groupBy.push('date');
            }
            let groupByString=''
        
            //Groupby
            $('#dataTable').html('');
            let html = `
                <table id="pixel_dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">
                    <thead>
                        <tr>
            `;
            for (const iterator of groupBy) {

                let iteratorLabel = '';
                switch (iterator) {
                    case 'regioncity_1':
                        iteratorLabel = 'region';
                        break;
                    case 'regioncity_2':
                        iteratorLabel = 'city';
                        break;
                    case 'deviceosbrowser_1':
                        iteratorLabel = 'device';
                        break;
                    case 'deviceosbrowser_2':
                        iteratorLabel = 'os';
                        break;
                    case 'deviceosbrowser_3':
                        iteratorLabel = 'browser';
                        break;
                    default:
                        iteratorLabel = iterator;
                        break;
                }

                html += `<th><label class"capitalize">${iteratorLabel}</label></th>`;



                groupByString += iterator + ',';
            }
            html += `
                            <th><label class"capitalize">Hits</label></th>
                            <th><label class"capitalize">Uniques</label></th>
                        </tr>
                    </thead>
                </table>            
            `;

            $('#dataTable').html(html);

        let ajaxUrl = '/api/pixel_analytics_report_table';


            let table = $('#pixel_dataTable').DataTable({
                "processing": true,
                "pageLength": 10,
                "serverSide": true,
                "deferRender": true,
                "columnDefs": [
                    { className: "text-right", "targets": [ -1,-2] }
                ],
                "ajax": {
                    "url": ajaxUrl,
                    "type": "POST",
                    "data": {
                        countries : countries,
                        regions : regions ,
                        cities :  cities,
                        devices :  devices ,
                        oss : oss ,
                        browsers :  browsers,
                        titles : titles ,
                        domains : domains ,
                        keys : keys ,
                        groupby : groupByString,
                        hours : hours,
                        from: $('#from').val(),
                        until: $('#until').val(),
                        pixelid : '{{ isset($_GET["pixelid"]) ? $_GET["pixelid"] : '' }}'
                    }
                }
            });

        }



        function loadChart(){
            if (typeof chart !== 'undefined') {
                chart.destroy();
            }
            //Filters

            let countries = $('#countries').val();
            let regions = $('#regions').val();
            let cities = $('#cities').val();
            let devices = $('#devices').val();
            let oss = $('#oss').val();
            let browsers = $('#browsers').val();
            let titles = $('#titles').val();
            let domains = $('#domains').val();
            let keys = $('#keys').val();
            let hours = $('#hours').val();

            //Check Days between Dates
            var date1 = new Date($('#from').val());
            var date2 = new Date($('#until').val());
            var timeDiff = Math.abs(date2.getTime() - date1.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));


            if(diffDays>0){ gby = "date"; } else { gby="datetime"; }


            $.post("/api/pixel_analytics_report",
                {
                    countries : countries,
                    regions : regions ,
                    cities :  cities,
                    devices :  devices ,
                    oss : oss ,
                    browsers :  browsers,
                    titles : titles ,
                    domains :  domains,
                    keys : keys ,
                    groupby : gby,
                    hours : hours,
                    from: $('#from').val(),
                    until: $('#until').val(),
                    pixelid : '{{ isset($_GET["pixelid"]) ? $_GET["pixelid"] : '' }}'
                },
                function(data){
                    var labels= [];
                    var values= [];
                    var uniques=[]
                    $.each(data, function( index, value ) {
                        if(gby == "date"){
                            let aux = index.match(/.{1,2}/g);
                            let date = aux[1] + '-' + aux[2] + '-20' + aux[0];
                            labels.push(date);
                        } else {
                            let hour =  index.substr(index.length -2) + " HS";
                            labels.push(hour);
                        }

                        values.push(value[0]);
                        uniques.push(value[1]);
                    });
            
           
                    if( Math.max.apply(null, values)==0) {
                        var chartsuggestedMax = Math.max.apply(null, values);
                    } else {
                        var chartsuggestedMax = 100;
                    }

                    var ctx = document.getElementById("myChart");
                    chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            datasets: [
                                {
                                    label: 'Hits',
                                    yAxisID: 'IMP',
                                    data: values,
                                    backgroundColor: "rgba(28, 134, 191,0.4)",
                                    borderColor: "rgba(28, 134, 191,0.7)",
                                    borderWidth: .6
                                },
                                {
                                    label: 'Uniques',
                                    yAxisID: 'IMP',
                                    data: uniques,
                                    backgroundColor: "rgba(88, 103, 195,0.4)",
                                    borderColor: "rgba(88, 103, 195,0.7)",
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
                                        labelString: ''
                                    },
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: chartsuggestedMax
                                    }
                                }
                                ]
                            }
                        }
                    });
                },'json');

        }

        window.onload = function() {
            loadChart();
            genDatatable();

            $("#groupby").select2({
                maximumSelectionLength: 3
            });
            $('#groupby').val(['date']).trigger('change');

        }
    </script>

    <style>
    #pixel_dataTable_filter{
        display:none;
    }
    .capitalize {
            text-transform: capitalize;
    }
    #dataTable tbody tr:last-child {  
        background-color: #9797e8;
        font-weight: 900 !important;
        color: black; 
    }
    </style>
@stop