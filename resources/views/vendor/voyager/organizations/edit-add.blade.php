@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#details">Details</a>
            </li>
            @if(!is_null($dataTypeContent->getKey()))
            <li>
                <a data-toggle="tab" href="#ssps">SSPS</a>
            </li>
            <li>
                <a data-toggle="tab" href="#dmps">DMPS</a>
            </li>
            <li>
                <a data-toggle="tab" href="#users">Users</a>
            </li>
            @endif
        </ul>

        <div class="panel-body">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">

                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if(!is_null($dataTypeContent->getKey()))
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}
                        <div class="tab-content">
                        <div id="details" class="tab-pane fade in active">
                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{(!is_null($dataTypeContent->getKey()) ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                                <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $options = json_decode($row->details);
                                    $display_options = isset($options->display) ? $options->display : NULL;
                                @endphp
                                @if ($options && isset($options->legend) && isset($options->legend->text))
                                    <legend class="text-{{$options->legend->align or 'center'}}" style="background-color: {{$options->legend->bgcolor or '#f0f0f0'}};padding: 5px;">{{$options->legend->text}}</legend>
                                @endif
                                @if ($options && isset($options->formfields_custom))
                                    @include('voyager::formfields.custom.' . $options->formfields_custom)
                                @else
                          
                                    <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width or 12 }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                        {{ $row->slugify }}
                                        
                                        @include('voyager::multilingual.input-hidden-bread-edit-add')
                                        @if($row->type == 'relationship')
                                           <label for="name">{{ $row->display_name }}</label>
                                            @include('voyager::formfields.relationship')
                                        @else
                                            @if($row->field=="quality_contact_name" || $row->field=="quality_contact_email")

                                                 
                                            @else   
                                                <label for="name">{{ $row->display_name }}</label> 
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                            @endif
                                           
                                        @endif

                                        @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                            {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                        @endforeach

                                        
                                    </div>
                                @endif
                            @endforeach
                            @if($_ENV['ENABLE_TMT_SCAN'] == 1)
                                 @php
                                     
                                 @endphp
                                <div class="col-md-12">
                                <h4>Ad quality Contact</h4>
                                </div>
                                <div class="form-group  col-md-12">
                                    <label for="name">Contact Name</label> 
                                    <input type="text" required class="form-control" name="quality_contact_name" placeholder="Contact Name" value="{{ $dataTypeContent->quality_contact_name ? $dataTypeContent->quality_contact_name : ''}}">
                                </div>
                                <div class="form-group  col-md-12">
                                    <label for="name">Contact Email</label> 
                                    <input type="email" required class="form-control" name="quality_contact_email" placeholder="Contact Email" value="{{ $dataTypeContent->quality_contact_email ? $dataTypeContent->quality_contact_email : ''}}">
                                </div>
                            @endif
                        </div><!-- panel-body -->
                        </div>
                        @if(!is_null($dataTypeContent->getKey()))
                            <div id="ssps" class="tab-pane fade in">
                                <div class="panel panel-bordered">
                                    <div class="card">
                                        <div class="card-header" style="padding:0" id="headingTwo">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                    SSPS
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" class="collapse in" aria-labelledby="headingTwo" data-parent="#accordion" style="margin: 20px">
                                            <div style="width: 900px;">
                                                @foreach($ssps as $ssp)
                                                    <div name="ssps" style="width: 300px; float: left"><input name="ssps[]" type="checkbox" value="{{$ssp->id}}" @php if(in_array($ssp->id,$selected_ssps)){ echo "checked"; } @endphp> {{$ssp->name}}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(!is_null($dataTypeContent->getKey()))
                            <div id="dmps" class="tab-pane fade in">
                                <div class="panel panel-bordered">
                                    <div class="card">
                                        <div class="card-header" style="padding:0" id="headingTwo">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                    DMPS
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" class="collapse in" aria-labelledby="headingTwo" data-parent="#accordion" style="margin: 20px">
                                            <div style="width: 900px;">

                                                <div style="width: 300px; float: left"><input name="dmps[]" type="checkbox" value="1" @php if(in_array(1,$selected_dmps)){ echo "checked"; } @endphp> 180 by two</div>
                                                <div style="width: 300px; float: left"><input name="dmps[]" type="checkbox" value="2" @php if(in_array(2,$selected_dmps)){ echo "checked"; } @endphp> Inspire</div>
                                                <div style="width: 300px; float: left"><input name="dmps[]" type="checkbox" value="3" @php if(in_array(3,$selected_dmps)){ echo "checked"; } @endphp> OnSpot</div>
                                                <div style="width: 300px; float: left"><input name="dmps[]" type="checkbox" value="4" @php if(in_array(4,$selected_dmps)){ echo "checked"; } @endphp> Neuro-Programmatic</div>
                                                <div style="width: 300px; float: left"><input name="dmps[]" type="checkbox" value="5" @php if(in_array(5,$selected_dmps)){ echo "checked"; } @endphp> Semcasting</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(!is_null($dataTypeContent->getKey()))
                            <div id="users" class="tab-pane fade in">
                                <div class="panel panel-bordered">
                                    <div class="card">
                                        <div class="card-header" style="padding:0" id="headingTwo">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                    Users of this Organization
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" class="collapse in" aria-labelledby="headingTwo" data-parent="#accordion">
                                            <table id="dataTable" class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($users as $user)
                                                    <tr>
                                                        <td><a href="/admin/users/{{$user['id']}}/edit" target="_blank">{{$user['id']}}</a></td>
                                                        <td>{{$user['name']}}</td>
                                                        <td><a href="mailto:{{$user['email']}}">{{$user['email']}}</a></td>
                                                        <td>{{$user['role']['display_name']}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                            @if(!is_null($dataTypeContent->getKey()))
                                <div id="reports" class="tab-pane fade in">
                                    <div class="panel panel-bordered">
                                        <div class="card">
                                            <div class="card-header" style="padding:0" id="headingTwo">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                        Organization Reports
                                                    </button>
                                                </h5>
                                            </div>
                                            <div id="collapseTwo" class="collapse in" aria-labelledby="headingTwo" data-parent="#accordion" style="margin: 20px">
                                                <div class="panel">
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
                                                    <div style="background-color: transparent; margin-top: 12px; margin-right: 25px; float: right; font-size: 12px; vertical-align: center;">
                                                        Include Video Reports
                                                    </div>
                                                    <div style="background-color: transparent; margin-top: 10px; margin-right: 4px; float: right; font-size: 12px; vertical-align: center;">
                                                        <input type="checkbox" id="includevast" value="1" onclick="addVideoReports()">
                                                    </div>
                                                    <ul class="nav nav-tabs">
                                                            <li class="active">
                                                                <a data-toggle="tab" href="#bydate">By Date</a>
                                                            </li>
                                                            <li>
                                                                <a data-toggle="tab" href="#bycamp" onclick="genDatatableById('bycamp','Campaign','advcamp_2')">By Campaign</a>
                                                            </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                            <!--<div id="bydate" class="tab-pane fade in  active"></div>-->
                                                            <div id="bycamp" class="tab-pane fade in"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
     </div>
    

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script>
        @if(!is_null($dataTypeContent->getKey()))
        function genDatatableById(id,cvalue,groupby,addvast){
            window.activeDatatableid = id;
            window.activeDatatablecvalue = cvalue;
            window.activeDatatablegroupby = groupby;

            if(document.getElementById("includevast").checked == true){
                addvast = 1;
            } else {
                addvast = "";
            }

            //if($('#'+id).html()==""){
            var fcountries = "";
            var fcamps = "";
            var fconcepts = "";
            var fdomains = "";
            var fcreatives = "";
            var fregions = "";

            //IF CUSTOM ADD FILTERS
            if(id == "customreport"){
                //Groupby
                groupby= $('#groupby').val();
                /*$.each( $('#groupby').val(), function( key, value ) {
                    groupby+=value+",";
                    console.log(value);
                });*/

                //Filters
                //Countries
                $.each( $('#country').val(), function( key, value ) {
                    fcountries+=value+"_*,";
                });

                //Campaigns
                $.each( $('#campaign').val(), function( key, value ) {
                    fcamps+="*_"+value+"_*,";
                });

                //Concepts
                $.each( $('#concept').val(), function( key, value ) {
                    fconcepts+=value+"_*,";
                });

                //Domains
                $.each( $('#domain').val(), function( key, value ) {
                    fdomains+="*_"+value+",";
                });

                //Creatives
                $.each( $('#creative').val(), function( key, value ) {
                    fcreatives+="*_"+value+",";
                });

                //Region
                $.each( $('#region').val(), function( key, value ) {
                    fregions+=value+"_*,";
                });

                // groupby = $("#groupby").val();

                //GroupBy
                /* var fgroupby = "";
                 $('#groupby').val().forEach(function(element) {
                     //console.log(element);
                     fgroupby+=element+",";
                 });*/
                switch(groupby) {
                    case 'concreat_2':
                        cvalue = "Creative";
                        break;
                    case 'concreat_1':
                        cvalue = "Concept";
                        break;
                    case 'advcamp_2':
                        cvalue = "Campaign";
                        break;
                    case 'advcamp_3':
                        cvalue = "Strategy";
                        break;
                    case 'channeldomain_2':
                        cvalue = "Domain/App";
                        break;
                    case 'countryisp_1':
                        cvalue = "Country";
                        break;
                    case 'regioncity_1':
                        cvalue = "Region";
                        break;
                    case 'regioncity_2':
                        cvalue = "City";
                        break;
                    case 'deviceosbrowser_3':
                        cvalue = "Browser";
                        break;
                    case 'date':
                        cvalue = "Date";
                        break;
                    case 'audience':
                        cvalue = "Segment";
                        break;
                }
            } else {

                @if(isset($_GET["campaign_id"]) && $_GET["campaign_id"]!="")
                    fcamps = "*_{{ $_GET["campaign_id"]  }}_*";
                @endif

            }

            toptabs="";
            if(id == "bygeo"){
                toptabs ='<ul class="nav nav-pills" style="margin-bottom:10px;">' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bygeo\',\'Country\',\'countryisp_1\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Country</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bygeo\',\'Region\',\'regioncity_1\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Region</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bygeo\',\'City\',\'regioncity_2\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">City</a>\n' +
                    '</li>' +
                    '</ul>';
            }
            @php //if(isset($_GET["campaign_id"]) && $_GET["campaign_id"]!="" ){ @endphp
            if(id == "bycamp"){
                /*toptabs ='<ul class="nav nav-pills" style="margin-bottom:10px;">' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bycamp\',\'Strategy\',\'advcamp_3\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Strategy</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bycamp\',\'Creative\',\'concreat_2\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Creative</a>\n' +
                    '</li>' +
                    '</ul>';*/
            }
            @php //} @endphp
            if(id == "bysupply"){
                toptabs ='<ul class="nav nav-pills" style="margin-bottom:10px;">' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" class="nav-link active show" data-toggle="tab" aria-expanded="true">Web</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" class="nav-link active show" data-toggle="tab" aria-expanded="true">App</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" class="nav-link active show" data-toggle="tab" aria-expanded="true">ALL</a>\n' +
                    '</li>' +
                    '</ul>';
            }
            if(id == "bydevice"){
                toptabs ='<ul class="nav nav-pills" style="margin-bottom:10px;">' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bydevice\',\'Device\',\'deviceosbrowser_1\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Device</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bydevice\',\'Os\',\'deviceosbrowser_2\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Os</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bydevice\',\'Browser\',\'deviceosbrowser_3\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">Browser</a>\n' +
                    '</li>' +
                    '<li class="nav-item" role="presentation">\n' +
                    '<a href="#tab-1" onclick="genDatatableById(\'bydevice\',\'Isp\',\'countryisp_2\')" class="nav-link active show" data-toggle="tab" aria-expanded="true">ISP</a>\n' +
                    '</li>' +
                    '</ul>';
            }
            if(addvast == 1){
                $('#' + id).html(
                    toptabs +
                    '                            <table id="' + id + '_dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">\n' +
                    '                                <thead>\n' +
                    '                                <tr>\n' +
                    '                                    <th>' + cvalue + '</th>\n' +
                    '                                    <th>Impressions</th>\n' +
                    '                                    <th>Clicks</th>\n' +
                    '                                    <th>Spent</th>\n' +
                    '                                    <th>eCPM</th>\n' +
                    '                                    <th>CTR</th>\n' +
                    '                                    <th>CPC</th>\n' +
                    '                                    <th>Conversion</th>\n' +
                    '                                    <th>CPA</th>\n' +
                    '                                    <th>Viewability</th>\n' +
                    '                                    <th>TOS</th>\n' +
                    '                                    <th>FirstQ</th>\n' +
                    '                                    <th>Middle</th>\n' +
                    '                                    <th>ThirdQ</th>\n' +
                    '                                    <th>Complete</th>\n' +
                    '                                </tr>\n' +
                    '                                </thead>\n' +
                    '                            </table>'
                );
            } else {
                $('#' + id).html(
                    toptabs +
                    '                            <table id="' + id + '_dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">\n' +
                    '                                <thead>\n' +
                    '                                <tr>\n' +
                    '                                    <th>' + cvalue + '</th>\n' +
                    '                                    <th>Impressions</th>\n' +
                    '                                    <th>Clicks</th>\n' +
                    '                                    <th>Spent</th>\n' +
                    '                                    <th>eCPM</th>\n' +
                    '                                    <th>CTR</th>\n' +
                    '                                    <th>CPC</th>\n' +
                    '                                    <th>Conversion</th>\n' +
                    '                                    <th>CPA</th>\n' +
                    '                                    <th>Viewability</th>\n' +
                    '                                    <th>TOS</th>\n' +
                    '                                    <th>VWI</th>\n' +
                    '                                </tr>\n' +
                    '                                </thead>\n' +
                    '                            </table>'
                );
            }
            if(addvast==1) {
                var builtColumns = [
                    {data: cvalue},
                    {data: "Impressions"},
                    {data: "Clicks"},
                    {data: "Spent"},
                    {data: "eCPM"},
                    {data: "CTR"},
                    {data: "CPC"},
                    {data: "Conversions"},
                    {data: "CPA"},
                    {data: "Viewability"},
                    {data: "TOS"},
                    {data: "FirstQ"},
                    {data: "Middle"},
                    {data: "ThirdQ"},
                    {data: "Complete"}];
            } else {
                var builtColumns = [
                    {data: cvalue},
                    {data: "Impressions"},
                    {data: "Clicks"},
                    {data: "Spent"},
                    {data: "eCPM"},
                    {data: "CTR"},
                    {data: "CPC"},
                    {data: "Conversions"},
                    {data: "CPA"},
                    {data: "Viewability"},
                    {data: "TOS"},
                    {data: "VWI"}];
            }
            if(cvalue=="Date"){ orderby = 0; } else { orderby=1; }
            $('#'+id+'_dataTable').DataTable({
                "dom": 'Bfrtip',
                "buttons": [

                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5'

                ],
                "processing": false,
                "order": [[ orderby, "desc" ]],
                "ajax": {
                    "url": "/api/hbreports",
                    "type": "POST",
                    "data": {
                        'groupby': groupby,
                        'advcamps' : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
                        'from' :  '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                        'until' :  '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}',
                        'organization' :  '{{ isset($userorganization) ? $userorganization : 10  }}',
                        'uid' :  '{{ isset($user_id) ? $user_id : 0  }}',
                        'urole' :  '{{ isset($user_role) ? $user_role : 0  }}',
                        'filters' : 'advcamps='+fcamps+'&concreats='+fconcepts+fcreatives+'&channeldomains='+fdomains+'&countryisps='+fcountries+'&regioncities='+fregions,
                        'addvast' : addvast
                    }
                },
                columns: builtColumns,
                /* {data: cvalue},
                 {data: "Impressions"},
                 {data: "Clicks"},
                 {data: "Spent"},
                 {data: "eCPM"},
                 {data: "CTR"},
                 {data: "CPC"},
                 {data: "Conversions"},
                 {data: "CPA"},
                 {data: "Viewability"},
                 {data: "TOS"}*/
            });

            //}

        }
        window.onload = function() {

            //By Date Datatable
            $(document).ready(function() {
                $.fn.dataTable.ext.errMode = 'throw';
                $('#bydate').html(
                    '                            <table id="dataTable" class="table table-hover dataTable no-footer" style="width: 100%;">\n' +
                    '                                <thead>\n' +
                    '                                <tr>\n' +
                    '                                    <th>Date</th>\n' +
                    '                                    <th>Impressions</th>\n' +
                    '                                    <th>Clicks</th>\n' +
                    '                                    <th>Spent</th>\n' +
                    '                                    <th>eCPM</th>\n' +
                    '                                    <th>CTR</th>\n' +
                    '                                    <th>CPC</th>\n' +
                    '                                    <th>Conversion</th>\n' +
                    '                                    <th>CPA</th>\n' +
                    '                                    <th>Viewability</th>\n' +
                    '                                    <th>TOS</th>\n' +
                    '                                    <th>VWI</th>\n' +
                    '                                </tr>\n' +
                    '                                </thead>\n' +
                    '                            </table>'
                );

                window.activeDatatableid = "bydate";
                window.activeDatatablecvalue = "Date";
                window.activeDatatablegroupby = gby;

                $('#dataTable').DataTable({
                    "dom": 'Bfrtip',
                    "buttons": [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5'
                    ],
                    "processing": false,
                    "ajax": {
                        "url": "/api/hbreports",
                        "type": "POST",
                        "data": {
                            'advcamps' : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
                            'from': '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                            'until': '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}',
                            'organization' :  '{{ isset($userorganization) ? $userorganization : 0  }}',
                            'uid' :  '{{ isset($user_id) ? $user_id : 0  }}',
                            'urole' :  '{{ isset($user_role) ? $user_role : 0  }}',
                            'filters' : ''
                        }
                    },
                    order: [[ 0, "asc" ]],
                    columns: [
                        {data: "Date"},
                        {data: "Impressions"},
                        {data: "Clicks"},
                        {data: "Spent"},
                        {data: "eCPM"},
                        {data: "CTR"},
                        {data: "CPC"},
                        {data: "Conversions"},
                        {data: "CPA"},
                        {data: "Viewability"},
                        {data: "TOS"},
                        {data: "VWI"}
                    ],
                });
                //ADD DATE PICKER
                        @if(isset($_GET["from"]) && isset($_GET["until"]))
                var start = moment("20{{substr($_GET["from"],0, strlen($_GET["from"])-2)  }}");
                var end = moment("20{{substr($_GET["until"],0, strlen($_GET["until"])-2)  }}");
                        @else
                var start = moment().subtract(6, 'days');
                var end = moment();
                @endif

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

            } );

            // Chart
                    @if(isset($_GET["from"]) && isset($_GET["until"]))
            var start = moment("20{{substr($_GET["from"],0, strlen($_GET["from"])-2)  }}");
            var end = moment("20{{substr($_GET["until"],0, strlen($_GET["until"])-2)  }}");
                    @else
            var start = moment().subtract(29, 'days');
            var end = moment();
            @endif
            //Check Days between Dates
            var date1 = new Date(start);
            var date2 = new Date(end);
            console.log(date1+','+date2);
            var timeDiff = Math.abs(date2.getTime() - date1.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

            console.log("Date Diff: "+diffDays);

            if(diffDays>0){ gby = "date"; } else { gby="datetime"; }

            $.post("/api/hbreports",
                {
                    /*countries : fcountries,
                    channels : fchannels,
                    media : fmedia,
                    domains : fdomains,
                    sizes : fsizes,
                    cities : fcities,*/
                    groupby: gby,
                    advcamps : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
                    organization :  '{{ isset($userorganization) ? $userorganization : 0  }}',
                    uid :  '{{ isset($user_id) ? $user_id : 0  }}',
                    urole :  '{{ isset($user_role) ? $user_role : 0  }}',
                    from: '{{ isset($_GET["from"]) ? $_GET["from"] : $from  }}',
                    until: '{{ isset($_GET["until"]) ? $_GET["until"] : $until  }}'
                },
                function(data){
                    var labels= [];
                    var valuesimpressions= [];
                    var valuesclicks= [];
                    $.each(data.data, function( index, value ) {
                        //console.log( value.Data );
                        labels.push(value.Date);
                        valuesimpressions.push(value.Impressions);
                        valuesclicks.push(value.Clicks);
                    });
                    //console.log(labels);
                    //console.log(Math.max.apply(null, values));
                    /*if( Math.max.apply(null, values)==0) {
                        var chartsuggestedMax = Math.max.apply(null, values);
                    } else {
                        var chartsuggestedMax = 100;
                    }*/

                    var ctx = document.getElementById("myChart");
                    var chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            datasets: [{
                                label: 'Impressions',
                                yAxisID: 'IMP',
                                data: valuesimpressions,
                                backgroundColor: "rgba(88, 103, 195,0.4)",
                                borderColor: "rgba(88, 103, 195,0.7)",
                                borderWidth: .6
                            },
                                {
                                    label: 'Clicks',
                                    yAxisID: 'CLC',
                                    data: valuesclicks,
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
                                        labelString: 'Impressions'
                                    },
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 2000
                                    },
                                    gridLines: {
                                        display: true,
                                        borderDashOffset: 30
                                    }
                                }, {
                                    id: 'CLC',
                                    labelString: 'Clicks',
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Clicks'
                                    },
                                    position: 'right',
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 500
                                    },
                                    gridLines: {
                                        display: false,
                                        borderDashOffset: 30
                                    }
                                }]
                            }
                        }

                    });

                    console.log(labels);

                },'json');

            //Get Data for Total Impressions and Total Clicks
            $.post("/api/hbreports",
                {
                    /*countries : fcountries,
                    channels : fchannels,
                    media : fmedia,
                    domains : fdomains,
                    sizes : fsizes,
                    cities : fcities,*/
                    groupby: 'date',
                    advcamps : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
                    organization :  '{{ isset($userorganization) ? $userorganization : 0  }}',
                    uid :  '{{ isset($user_id) ? $user_id : 0  }}',
                    urole :  '{{ isset($user_role) ? $user_role : 0  }}',
                    from :  moment().subtract(7, 'days').format('YYMMDD')+'00',
                    until :  moment().format('YYMMDD')+'23'
                },function(data){

                    console.log(data);
                    totalImpressions=0;
                    totalClicks=0;
                    $.each(data.data, function( index, value ) {
                        totalImpressions = +totalImpressions + +value.Impressions;
                        totalClicks = +totalClicks + +value.Clicks;
                    });
                    console.log(totalImpressions);
                },'json');
            // Month
            $.post("/api/hbreports",
                {
                    /*countries : fcountries,
                    channels : fchannels,
                    media : fmedia,
                    domains : fdomains,
                    sizes : fsizes,
                    cities : fcities,*/
                    groupby: 'date',
                    advcamps : '{{ (!isset($_GET["campaign_id"]) || $_GET["campaign_id"] == "") ? '' : "*_".$_GET["campaign_id"]."_*" }}',
                    organization :  '{{ isset($userorganization) ? $userorganization : 0  }}',
                    urole :  '{{ isset($user_role) ? $user_role : 0  }}',
                    from :  moment().subtract(30, 'days').format('YYMMDD')+'00',
                    until :  moment().format('YYMMDD')+'23'
                },function(data){

                    console.log(data);
                    totalImpressionsMonth=0;
                    totalClicksMonth=0;
                    $.each(data.data, function( index, value ) {
                        totalImpressionsMonth = +totalImpressionsMonth + +value.Impressions;
                        totalClicksMonth = +totalClicksMonth + +value.Clicks;
                    });
                    console.log(totalImpressionsMonth);


                },'json');
        }

        //add video reports
        function addVideoReports(){
            if(document.getElementById("includevast").checked == true){
                genDatatableById( activeDatatableid ,activeDatatablecvalue, activeDatatablegroupby,1);
            } else {
                genDatatableById( activeDatatableid ,activeDatatablecvalue, activeDatatablegroupby);
            }
        }
        @endif
    </script>
    <script>
        var params = {}
        var $image

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', function (e) {
                e.preventDefault();
                $image = $(this).siblings('img');

                params = {
                    slug:   '{{ $dataType->slug }}',
                    image:  $image.data('image'),
                    id:     $image.data('id'),
                    field:  $image.parent().data('field-name'),
                    _token: '{{ csrf_token() }}'
                }

                $('.confirm_delete_name').text($image.data('image'));
                $('#confirm_delete_modal').modal('show');
            });

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $image.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing image.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <!--<script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>-->
@stop
