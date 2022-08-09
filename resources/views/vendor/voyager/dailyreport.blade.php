@extends('voyager::master')

@section('content')
    <div class="page-content">
        @include('voyager::alerts')

        <div class="analytics-container">
            <div class="panel">
                <div style="width: 100%; padding: 20px;">
                    <ul class="nav nav-tabs">
                        <li class="active">
        
                            <a data-toggle="tab" href="#custom">Daily report generator</a>
                        </li>
                    </ul>
                    <div style="margin-top: 10px;" class="tab-content">
                        <div id="custom" class="tab-pane fade in active">
                            <form>
                                <div class="panel panel-primary panel-bordered">
                                    <div class="panel-body">
                                        <div class="row clearfix">
                                        <div class="col-md-4 form-group">
                                                <label>Campaigns</label>
                                                <select id="campaingsOptions" class="form-control select2" name="campaigns[]"  multiple>
                                                </select>
                                                <input type="checkbox" checked name="all" id="all">
                                                <label for="all">Only active campaings</label>
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label>From</label>
                                                <div style="display: flex;">
                                                    <input class="form-control" style="width: 200px;"  value="<?php echo date('Y-m-d'); ?>" type="date" name="from" id="from" placeholder="From">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Until</label>
                                                <div style="display: flex;">
                                                    <input class="form-control" style="width: 200px;"  value="<?php echo date('Y-m-d'); ?>" type="date" name="until" id="until" placeholder="Until">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-4 form-group">
                                                <label>Group By</label>
                                                <select class="form-control" name="groupby[]" id="groupby" multiple>
                                                    <option value="campaign">Campaign</option>
                                                    <option value="strategy">Strategy</option>
                                                    <option value="creative">Creative</option>
                                                    <option value="date">Date</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-md-6 form-group">
                                                <button class="btn btn-primary" type="button" onclick="genDatatable();">Generate Report</button>
                                                <a class="btn btn-info" id="exportcsv" target="_blank" href="">Export csv</a>
                                                <input type="checkbox" checked name="includeid" id="includeid"  >&nbsp; Include ID in CSV</input>
                                            </div>
                                        </div>
                                        <div id="dataTable" style="overflow: auto;"></div>
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
   
        function updateExportLink(){

            let params = {
                groupby : $('#groupby').val() != '' ? $('#groupby').val().join() : ['campaign'],
                campaigns : $('#campaingsOptions').val().length > 0 ? $('#campaingsOptions').val().join() : 'all',
                from: $('#from').val(),
                until: $('#until').val(),
                includeid: $('#includeid').is(":checked")
            };
         
            let query = $.param(params);

            $("#exportcsv").attr('href','/api/exports/daily_report?'+query);

        }

        function loadCampaigns(){
            let wl_prefix = {{env('WL_PREFIX')}};
            $.get('/api/campaigns?fields=id,name,status', function(data){
                    $('#campaingsOptions').empty();    
                    for (const iterator in data) {
                        if( $('#all').prop('checked') ) {
                           if(data[iterator].status == 1){
                            html = ` <option value="${data[iterator].id + (wl_prefix * 1000000) }">${data[iterator].id +' - '+ data[iterator].name}</option>` ;
                                $('#campaingsOptions').append(html);
                           }
                        }else{
                            html = ` <option value="${data[iterator].id + (wl_prefix * 1000000) }">${data[iterator].id +' - '+ data[iterator].name}</option>` ;
                            $('#campaingsOptions').append(html);
                        }
                    }
            });

            $('#campaingsOptions').trigger('change.select2')
        }
        
        function genDatatable(){
            //Filters
            //Countries
            updateExportLink();
            
            let from = $('#from').val();
            let until = $('#until').val();
            let groupBy = $('#groupby').val();
            let campaigns = $('#campaingsOptions').val();
            let includeid = $('#includeid').is(":checked");


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
                    case 'campaign':
                        iteratorLabel = 'Campaign';
                        break;
                    case 'strategy':
                        iteratorLabel = 'Strategy';
                        break;
                    case 'creative':
                        iteratorLabel = 'Creative';
                        break;
                    default:
                        iteratorLabel = iterator;
                        break;
                }

                html += `<th><label class"capitalize">${iteratorLabel}</label></th>`;

                groupByString += iterator + ',';
            }
            html += `
                            <th><label class"capitalize">Impressions</label></th>
                            <th><label class"capitalize">Spent</label></th>
                            <th class="no-sort"><label class"capitalize">eCPM</label></th>
                            <th><label class"capitalize">Clics</label></th>
                            <th class="no-sort"><label class"capitalize">Ctr</label></th>
                            
                            <th class="no-sort"><label class"capitalize">Vast start</label></th>
                            <th class="no-sort"><label class"capitalize">Vast 25%</label></th>
                            <th class="no-sort"><label class"capitalize">Vast 50%</label></th>
                            <th class="no-sort"><label class"capitalize">Vast 75%</label></th>
                            <th class="no-sort"><label class"capitalize">Vast Completed</label></th>
                        </tr>
                    </thead>
                </table>            
            `;

            $('#dataTable').html(html);

            let ajaxUrl = '/api/daily_report_table';

            let table = $('#pixel_dataTable').DataTable({
                "processing": true,
                "pageLength": 10,
                "serverSide": true,
                "deferRender": true,
                "columnDefs": [
                    { className: "text-right", "targets": [ -1,-2,-3,-4,-5,-6,-7,-8,-9,-10] },
                    { targets: 'no-sort', "orderable": false }
                ],
                "ajax": {
                    "url": ajaxUrl,
                    "type": "POST",
                    "data": {
                        groupby : groupByString,
                        campaigns : campaigns,
                        from: from,
                        until: until,
                        includeid: includeid
                    }
                }
            });

        }
        window.onload = function() {
            $("#groupby").select2({
                maximumSelectionLength: 4
            });

            $('#all').change(function(){
                loadCampaigns();
            })

            $('#includeid').change(function(){
                updateExportLink();
            })

            loadCampaigns();

            $('#groupby').val(['campaign','strategy','creative','date']).trigger('change');
        }
    </script>

    <style>
    #pixel_dataTable_filter{
        display:none;
    }
    .capitalize {
            text-transform: capitalize !important;
    }
    </style>
@stop