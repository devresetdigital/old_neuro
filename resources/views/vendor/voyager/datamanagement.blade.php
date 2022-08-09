@extends('voyager::master')
@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-people"></i> Data Management
        </h1>
    </div>
   
@stop
@section('content')

    <!-- ======================= LINE AWESOME ICONS ===========================-->
    <link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/line-awesome.min.css">
    <link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/simple-line-icons.css">
    <!-- ======================= DRIP ICONS ===================================-->
    <link rel="stylesheet" href="../dsp-demo/dsp-demo/assets/css/icons/dripicons.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    @include('voyager::compass.includes.styles')
    @include('voyager::alerts')
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.5/jquery.csv.min.js"></script>
    <div class="page-content compass container-fluid">
        <div class="tab-content">
            &nbsp; <button type="button" class="btn btn-danger" onclick="document.getElementById('addtask').style.display='inline'"> + CREATE AUDIENCE</button>
            <div>
                <iframe style="display: none;" id="addtask" src="/lab/datamanagement" width="100%" height="700" frameborder="0"></iframe>


        <!-- ======================= START DMPS SEGMENTS ===================================-->
   
        <div class="segments-container" >
            <label for="name">Dmps Segments selection:</label>
            <div class="pull-right">
                <input class="inc_exc segments_filters"  checked="checked" type="checkbox" name="segments_target_1" id="segments_target_1" value="1"> Andriod 
                <input class="inc_exc segments_filters"  checked="checked" type="checkbox" name="segments_target_2"  id="segments_target_2" value="2"> Ios 
                <input class="inc_exc segments_filters" checked="checked" type="checkbox" name="segments_target_3"  id="segments_target_3" value="3"> Ip
                <input class="inc_exc segments_filters"  checked="checked" type="checkbox" name="segments_target_4"  id="segments_target_4" value="4"> Cookie
            </div>
            <br>
            
            <!-- ======================= INPUT HIDDEN  ===================================-->
            <input type="hidden" id="audiences_selection" name="audiences_selection" value="" />
            <!-- ======================= INPUT HIDDEN  ===================================-->



            <input type="hidden" id="audiences_cpm" name="audiences_cpm" value="{{ isset($segments_cpm) ? $segments_cpm : 0}}" />
            <div class="audiences-tabs" style="min-height: 10em;">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    @php $first = true;@endphp 
                    @foreach($dmp as $dmp_name => $audiences)
                    <li class="{{$first ? 'active' : ''}} tabs-segments" data-tab-name="{{$audiences}}">
                        <a class="nav-link" id="{{$dmp_name}}-tab" data-toggle="tab" href="#{{$dmp_name}}" role="tab" aria-controls="home" aria-selected="{{$first ? 'true' : 'false'}}">{{$dmp_name}}</a>
                    </li>
                    @php $first = false @endphp
                    @endforeach
                    <img src="{{ asset('/Loading.gif') }}" id="loading" style="position: absolute;
                    width: 2.5em;
                    right: 4em;
                    z-index: 1;
                    margin-top: 3px;" alt="">
                    <input class="form-control search-segments" id="search-segments"  type="text" placeholder="Sub-Search">
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div role="tabpanel" class="tab-pane active" style="padding: 0; height: 33em; overflow: auto; margin-bottom: 1em;">
                            <table class="table table-dark">
                                <thead >
                                    <tr>
                                        <th class="col-sm-2" scope="col">Id</th>
                                        <th class="col-sm-5" scope="col">Name</th>
                                        <th class="col-sm-1" scope="col">Type</th>
                                        <th class="col-sm-2 text-right"  scope="col">Reach</th>
                                        <th class="col-sm-1 text-right"   scope="col">Price</th>
                                        <th class="col-sm-1 text-center" scope="col" >Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-dmp">
                                    
                                </tbody>
                            </table>
                            
                        </div>   
                        <div class="dataTables_paginate paging_simple_numbers" style="padding:0" id="segmentsPagination">
                            <ul class="pagination" id="paginationContainer">
                                
                            </ul>
                        </div>
                    </div>
                </div>
                <label for="name">Segments selected:</label><br>
                <div class="audiences-selected" style="padding: 0; height: 33em; overflow: auto; margin-bottom: 4em;">
                    <table class="table table-dark">
                        <thead >
                            <tr>
                                <th class="col-sm-2" scope="col">Id</th>
                                <th class="col-sm-5" scope="col">Name</th>
                                <th class="col-sm-1" scope="col">Type</th>
                                <th class="col-sm-2 text-right" scope="col">Reach</th>
                                <th class="col-sm-1 text-right" scope="col">Price</th>
                                <th class="col-sm-1 text-center" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyAudiencesSelected">
            
                        </tbody>
                    </table>
                </div>
                </div>
                <div class="card" id="finished" style="display: inline">
                    <h5 class="card-header">FINISHED</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 300px">ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($reports as $key => $val)
                                    <tr>
                                        <td>{{$key}}</td>
                                        <td style="word-wrap: break-word">{{ $val["name"]  }}</td>
                                        <td>{{ $val["status"]  }}</td>
                                        <td style="word-wrap: break-word">{{ str_replace("%20"," ",$val["message"])  }}</td>
                                        <td>{{ $val["date"]  }}</td>
                                        <td><a href="http://134.209.171.185:9000/getresult?id={{ $key  }}" download target="_blank">[DOWNLOAD]</a></td>
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
        <!-- ======================= END DMPS SEGMENTS ===================================-->

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
        <script src="{{ asset('js/datamanagement/segments.js')}}"></script>

@stop

