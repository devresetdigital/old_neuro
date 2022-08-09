@extends('voyager::master')
@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-people"></i> Auto Task Manager
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
            @php
                $ntodo = count($reports["todo"]);
                $ndoing = count($reports["doing"]);
                $ndone = count($reports["done"]);
            @endphp
            <button style="margin-left: 20px;" type="button" class="btn btn-primary" onclick="toggleTables('queue')">QUEUE ({{ $ntodo }})</button> <button type="button" class="btn btn-primary" onclick="toggleTables('current')">CURRENT ({{ $ndoing }})</button> <button type="button" class="btn btn-primary" onclick="toggleTables('finished')">FINISHED ({{ $ndone  }})</button> <button type="button" class="btn btn-danger" onclick="document.getElementById('addtask').style.display='inline'"> + ADD TASK</button>
            <div>
                <iframe style="display: none;" id="addtask" src="/lab/autotask" width="100%" height="700" frameborder="0"></iframe>
            </div>
            <div class="card" id="queue" style="display: inline">
                <h5 class="card-header">QUEUE</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 300px">ID</th>
                                <th>Name</th>
                                <th>Created</th>
                                <th>Status</th>
                                <!--<th>Functions</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($reports["todo"] as $todo)
                            <tr>
                                <td>{{ $todo["id"] }}</td>
                                <td>{{ $todo["TaskName"] }}</td>
                                <td>{{ $todo["CreationTime"]  }}</td>
                                <td>{{ $todo["Status"] }}</td>
                                <!--<td> "FunctionName": "DMPProcess", "args": "File.rar ", "status": "Status: WAITING, Message: WAITING"</td>-->
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card" id="current" style="display: none">
                <h5 class="card-header">CURRENT</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 300px">ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Working Time</th>
                                <th>Response</th>
                                <!--<th>Functions</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($reports["doing"] as $doing)
                            <tr>
                                <td>{{ $doing["id"] }}</td>
                                <td>{{ $doing["TaskName"] }}</td>
                                <td>{{ $doing["Status"] }}</td>
                                <td>{{ $doing["CreationTime"]  }}</td>
                                <td>{{ $doing["Working time"] }}</td>
                                <td>{{ $doing["Response"] }}</td>
                                <!--<td>  "FunctionName": "DMPProcess", "args": "File.rar ", "status": "Status: WAITING, Message: WORKING ON THIS FUNCTION"</td> -->
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card" id="finished" style="display: none">
                <h5 class="card-header">FINISHED</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 300px">ID</th>
                                <th>Name</th>
                                <!--<th>Status</th>-->
                                <th>Status</th>
                                <th>Response</th>
                                <th>CreationTime</th>
                                <th>Task ended at</th>
                                <th>Actions</th>
                               <!-- <th>Functions</th> -->
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($reports["done"] as $done)
                            <tr>
                                <td>{{ $done["id"]  }}</td>
                                <td>{{ $done["TaskName"]  }}</td>
                                <!--<td>{{ $done["Status"]  }}</td>-->
                                <th>{{ $done["Status Response"]  }}</th>
                                <th>{{ $done["Response"]  }}</th>
                                <th>{{ $done["CreationTime"]  }}</th>
                                <th>{{ $done["TaskEndedAt"]  }}</th>
                                <th><a href="http://104.131.2.141:9000/getresult?id={{ $done["id"]  }}" download target="_blank">[DOWNLOAD]</a></th>
                               <!-- <td> "FunctionName": "DMPProcess", "args": "File.rar ", "status": "Status: WAITING, Message: WAITING"</td> -->
                            </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div>
                &nbsp;
            </div>
        </div>
        <script src="../dsp-demo/dsp-demo/assets/vendor/countup.js/dist/countUp.min.js"></script>
        <script src="../dsp-demo/dsp-demo/assets/js/components/countUp-init.js"></script>
        <script>
            function toggleTables(table){

                document.getElementById('queue').style.display="none"
                document.getElementById('current').style.display="none";
                document.getElementById('finished').style.display="none";

                document.getElementById(table).style.display="inline";

            }
        </script>

@stop