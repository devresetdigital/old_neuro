@extends('voyager::master')
@section('css')

<link rel="stylesheet" href="{{ asset('css/strategies-edit.css') }}">
@stop

@section('page_header')
<div class="container-fluid">
    <h1 class="page-title">
        <i class="voyager-people"></i> Forecasting
    </h1>
</div>
@stop
@section('content')
    <div class="page-content">

        @include('voyager::alerts')
       
        <div class="analytics-container">
            @include('voyager::general.alerts' , ['type' =>'danger', 'message' => 'Work in progres' ])
            <div class="panel">
            <a style="margin-left: 20px;" type="button" class="btn btn-primary" href="/admin/forecasting">INVENTORY</a> 
            <a type="button" class="btn btn-primary"  href="/admin/forecasting">AUDIENCE</a>
            <a type="button" class="btn btn-primary active" onclick="document.href='/admin/app_domain_quality'">QUALITY</a>
                <div style="width: 100%; padding: 20px;">
                    <div style="margin-top: 10px;" class="tab-content">
                        <div id="custom" class="tab-pane fade in active">
                            <form>
                                <div class="panel panel-primary panel-bordered">
                                    <div class="panel-body">
                                        <div class="row"  style="display: flex; flex-wrap: wrap;">
                                            <div class="col-md-4">
                                                <label>Channels</label>
                                                <select class="form-control select2" name="channels[]" multiple="" id="channels" tabindex="-1" aria-hidden="true">
                                                    <option value="SITE">SITE</option>
                                                    <option value="APP">APP</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label>Oss</label>
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
                                                <label>Medias</label>
                                                <select class="form-control select2" name="medias[]" multiple="" id="medias" tabindex="-1" aria-hidden="true">
                                                    <option value="BANNER">BANNER</option>
                                                    <option value="VIDEO">VIDEO</option>
                                                </select>
                                            </div>
                                    
                                            <div class="col-md-4 form-group">
                                                <label>Publishers</label>
                                                <input class="form-control  bootstrap-tagsinput tagsinput " name="publishers" id="publishers" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Domains</label>
                                                <input class="form-control  bootstrap-tagsinput tagsinput" name="domains" id="domains" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Ssps</label>
                                                <input class="form-control  bootstrap-tagsinput tagsinput" name="ssps" id="ssps" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Devices</label>
                                                <input class="form-control  bootstrap-tagsinput tagsinput" name="devices" id="devices" >
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Group By</label>
                                                <select class="form-control select2" name="groupby[]" id="groupby" multiple>
                                                    <option value="channel">Channels</option>
                                                    <option value="media">Medias</option>
                                                    <option value="os">Oss</option>
                                                    <option value="publisher">Publishers</option>
                                                    <option value="domain">Domains</option>
                                                    <option value="ssp">Ssps</option>
                                                    <option value="device">Devices</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <button class="btn btn-primary" type="button" onclick="genDatatable();">Calculate</button>
                                                <a class="btn btn-info" id="exportcsv" target="_blank" href="">Export csv</a>
                                            </div>
                                        </div>
                                        <div id="dataTable" style="overflow:auto;"></div>
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
      function updateExportLink() {
            let until  = new Date()
            until.setDate(until.getDate() - 1);
            until = until.getFullYear() + '-' + ( "0" + (until.getMonth() + 1) ).slice(-2)+ '-' + ("0" + until.getDate()).slice(-2);
         
            let from  = new Date()
            from.setDate(from.getDate() - 7);
            from = from.getFullYear() + '-' +  ( "0" +(from.getMonth() + 1) ).slice(-2) + '-' +("0" + from.getDate()).slice(-2) ;
           
           let groupBy = $('#groupby').val();
            if(groupBy.length === 0){
                groupBy.push('domain');
            }

            let params = {
                channels : $('#channels').val().join(),
                medias : $('#medias').val().join(),
                oss : $('#oss').val().join(),
                publishers : $('#publishers').val(),
                domains : $('#domains').val(),
                devices : $('#devices').val(),
                groupBy : groupBy,
                until: until,
                from: from
            };
            let query = $.param(params);

            $("#exportcsv").attr('href','/api/exports/quality?'+query);
        }

        function genDatatable(){
            //Filters
            //Countries
            updateExportLink();

            let channels = $('#channels').val().join();
            let medias = $('#medias').val().join();
            let oss = $('#oss').val().join();
            let publishers = $('#publishers').val();
            let domains = $('#domains').val();
            let devices = $('#devices').val();
            let ssps = $('#ssps').val();

            let groupBy = $('#groupby').val();


            let until  = new Date()
            until.setDate(until.getDate() - 1);
            until = until.getFullYear() + '-' + ( "0" + (until.getMonth() + 1) ).slice(-2)+ '-' + ("0" + until.getDate()).slice(-2);
         
            let from  = new Date()
            from.setDate(from.getDate() - 7);
            from = from.getFullYear() + '-' +  ( "0" +(from.getMonth() + 1) ).slice(-2) + '-' +("0" + from.getDate()).slice(-2) ;

            if(groupBy.length === 0){
                groupBy.push('domain');
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
                html += `<th>${iterator}</th>`;
                groupByString += iterator + ',';
            }
            html += `
                            <th>Viewability</th>
                            <th>eCPM</th>
                            <th>Completion Rate</th>
                            <th>Tos</th>
                            <th>Above The Fold</th>
                            <th>Ctr</th>
                            <th>Viewable</th>
                            <th>Bots</th>
                            <th>Potential Bots</th>
                            <th>Search crawlers</th>
                            <th>Fake device</th>
                            <th>Stacke ads</th>
                        </tr>
                    </thead>
                </table>            
            `;

            $('#dataTable').html(html);

            let ajaxUrl = '/api/app_domain_quality_table';

            $('#pixel_dataTable').DataTable({
                "processing": true,
                "pageLength": 10,
                "serverSide": true,
                "deferRender": true,
                "columnDefs": [
                    { className: "text-right", "targets": [ 1,2,3,4,5,6 ] }
                ],
                "ajax": {
                    "url": ajaxUrl,
                    "type": "POST",
                    "data": {
                        channels :  channels ,
                        medias :  medias,
                        oss :  oss,
                        publishers : publishers,
                        domains : domains ,
                        devices : devices ,
                        ssps : ssps ,
                        groupby : groupByString,
                        from: from,
                        until: until
                    }
                }
            });

        }

        window.onload = function() {

            genDatatable();

            $("#groupby").select2({
                maximumSelectionLength: 4
            });

            $('#groupby').val(['domain']).trigger('change');

        }
    </script>
    <style>
        .dataTables_filter {
            display: none;
        }   
    </style>
@stop