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
                            <a data-toggle="tab" href="#custom">Creatives Bulk Upload</a>
                        </li>
                    </ul>
                    <canvas id="myChart" width="400" height="80"></canvas>
                    <div style="margin-top: 10px;" class="tab-content">
                        <div id="custom" class="tab-pane fade in active">
                            <form>
                                <div class="panel panel-primary panel-bordered">
                                    <div class="panel-body">
                                        <form>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="customFile">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop