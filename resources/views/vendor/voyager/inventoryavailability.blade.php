@extends('voyager::master')

@section('content')
    <div class="page-content">
        @include('voyager::alerts')

        <div class="analytics-container">
            <div class="panel">
                <div style="width: 100%; padding: 20px;">
                    <ul class="nav nav-tabs">
                        <!-- <li>
                             <a data-toggle="tab" href="#bydata" onclick="genDatatableById('bydata','Data')">By Data</a>
                         </li>-->
                        <li class="active">
                            <a data-toggle="tab" href="#custom">Inventory Availability</a>
                        </li>
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
                                                <select class="form-control select2" name="countries[]" id="countries" multiple>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Channels</label>
                                                <select class="form-control select2" name="channels[]" id="channels" multiple>
                                                        <option value="APP">App</option>
                                                        <option value="WEB">Web</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Media</label>
                                                <select class="form-control select2" name="media[]" id="media" multiple>
                                                    <option value="BANNER">Banner</option>
                                                    <option value="VIDEO">Video</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Domains</label>
                                                <select class="form-control select2" name="domains[]" id="domains" multiple>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Sizes</label>
                                                <select class="form-control select2" name="sizes[]" id="sizes"  multiple>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Cities</label>
                                                <select class="form-control select2" name="regions[]" id="regions" multiple>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Group By</label>
                                                <select class="form-control select2" name="groupby[]" id="groupby" multiple>
                                                    <option value="country">Country</option>
                                                    <option value="channel">Channel</option>
                                                    <option value="media">Media</option>
                                                    <option value="os">OS</option>
                                                    <option value="city_2">City</option>
                                                    <option value="domain_3">Domain</option>
                                                    <option value="size">Size</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Dates</label>
                                                <input class="form-control" style="width: 200px;" type="date" name="from" id="from" placeholder="From"> <input class="form-control" style="width: 200px;" type="date" name="until" id="until" placeholder="Until">
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <button class="btn btn-primary" type="button" onclick="genDatatableById('dataTable','Data'); loadChart();">Calculate</button>
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
    <script>
        function genDatatableById(id,cvalue,groupby){
            //Filters
                //Countries
                var fcountries = "";
                $('#countries').val().forEach(function(element) {
                    //console.log(element);
                    fcountries+=element+",";
                });

                //Channels
                var fchannels = "";
                $('#channels').val().forEach(function(element) {
                    //console.log(element);
                    fchannels+=element+",";
                });

                //Media
                var fmedia = "";
                $('#media').val().forEach(function(element) {
                    //console.log(element);
                    fmedia+=element+",";
                });
                //Domains
                var fdomains = "";
                $('#domains').val().forEach(function(element) {
                    //console.log(element);
                    fdomains+=element+",";
                });
                //Sizes
                var fsizes = "";
                $('#sizes').val().forEach(function(element) {
                    //console.log(element);
                    fsizes+=element+",";
                });
                //Region
                var fcities = "";
                $('#regions').val().forEach(function(element) {
                    //console.log(element);
                    fcities+=element+",";
                });
                //GroupBy
                var fgroupby = "";
                $('#groupby').val().forEach(function(element) {
                    //console.log(element);
                    fgroupby+=element+",";
                });

            //Dates
            var ffrom = $('#from').val();
            var funtil = $('#until').val();


            //Groupby

                $('#'+id).html('');
                $('#'+id).html(
                    '                            <table id="'+id+'_dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">\n' +
                    '                                <thead>\n' +
                    '                                <tr>\n' +
                    '                                    <th>'+cvalue+'</th>\n' +
                    '                                    <th>Requests</th>\n' +
                    '                                </tr>\n' +
                    '                                </thead>\n' +
                    '                            </table>'
                );
                $('#'+id+'_dataTable').DataTable({
                    "processing": true,
                    "pageLength": 100,
                    "ajax": {
                        "url": "/api/inreports",
                        "type": "POST",
                        "data": {
                            'groupby': fgroupby,
                            'countries' : fcountries,
                            'channels' : fchannels,
                            'media' : fmedia,
                            'domains' : fdomains,
                            'sizes' : fsizes,
                            'cities' : fcities,
                            'from' :  ffrom,
                            'until' :  funtil
                        }
                    },
                    columns: [
                        {data: cvalue},
                        {data: "Requests"}
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5'
                    ]
                });


        }
        function loadChart(){
            if (typeof chart !== 'undefined') {
                chart.destroy();
            }
            //Filters
            //Countries
            var fcountries = "";
            $('#countries').val().forEach(function(element) {
                //console.log(element);
                fcountries+=element+",";
            });

            //Channels
            var fchannels = "";
            $('#channels').val().forEach(function(element) {
                //console.log(element);
                fchannels+=element+",";
            });

            //Media
            var fmedia = "";
            $('#media').val().forEach(function(element) {
                //console.log(element);
                fmedia+=element+",";
            });
            //Domains
            var fdomains = "";
            $('#domains').val().forEach(function(element) {
                //console.log(element);
                fdomains+=element+",";
            });
            //Sizes
            var fsizes = "";
            $('#sizes').val().forEach(function(element) {
                //console.log(element);
                fsizes+=element+",";
            });
            //Region
            var fcities = "";
            $('#regions').val().forEach(function(element) {
                //console.log(element);
                fcities+=element+",";
            });

            //Check Days between Dates
            var date1 = new Date($('#from').val());
            var date2 = new Date($('#until').val());
            var timeDiff = Math.abs(date2.getTime() - date1.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

            console.log("Date Diff:"+diffDays);

            if(diffDays>0){ gby = "date"; } else { gby="datetime"; }


            $.post("/api/inreports",
                {
                    countries : fcountries,
                    channels : fchannels,
                    media : fmedia,
                    domains : fdomains,
                    sizes : fsizes,
                    cities : fcities,
                    groupby: gby,
                    from: $('#from').val(),
                    until: $('#until').val()
                },
                function(data){
                    var labels= [];
                    var values= [];
                    $.each(data.data, function( index, value ) {
                        //console.log( value.Data );
                        labels.push(value.Data);
                        values.push(value.Requests);
                    });
                    //console.log(labels);
                    console.log(Math.max.apply(null, values));
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
                                    label: 'Requests',
                                    yAxisID: 'IMP',
                                    data: values,
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
                                        labelString: 'Requests'
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

                    console.log(labels);

                },'json');

        }
        window.onload = function() {

            loadChart();
            $(".form-control").select2({
                tags: true
            });

        }
    </script>
@stop