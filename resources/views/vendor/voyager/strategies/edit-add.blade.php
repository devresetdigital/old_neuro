@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/strategies-edit.css') }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFhS8ws2aH_UjRQG94f81_co5oXXIFZds&libraries=places,drawing"></script>
@stop

@section('page_title', __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop
@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li class="active">
        <a href="/admin"><i class="voyager-boat"></i> Dashboard</a>
    </li>
    @if(!is_null($dataTypeContent->getKey()))
    <li class="active">
        <a href="/admin/campaigns/{{$dataTypeContent->campaign->id}}/edit">{{$dataTypeContent->campaign->name}}</a>
    </li>
    <li class="active">
        <a href="/admin/strategies_campaign/{{$dataTypeContent->campaign->id}}">Strategies</a>
    </li>
    <li>Edit</li>
    @else 
    <li>Create</li>
    @endif
</ol>
@stop
@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form id="strategyForm" role="form"
                          class="form-edit-add"
                          action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
                          method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                    @if(!is_null($dataTypeContent->getKey()))
                        {{ method_field("PUT") }}
                    @endif

                    <!-- CSRF TOKEN -->
                        {{ csrf_field() }}
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#details">Details</a>
                            </li>
                            @if(!is_null($dataTypeContent->getKey()))
                                <li>
                                    <a data-toggle="tab" href="#creative">Creatives</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#geofencing">GEOS</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#targeting">Targeting</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#data">Data</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#contextual">Contextual</a>
                                </li>
                                @if($logged_user_role == 1)
                                    <li>
                                        <a data-toggle="tab" href="#tags">Tags</a>
                                    </li>
                                @endif
                                <li>
                                    <a data-toggle="tab" href="#" onclick="document.location.href='/admin/strategies/reports/{{$dataTypeContent->getKey()}}'">Reports</a>
                                </li>
                            @endif
                        </ul>
                        <div class="panel-body">
                            <div class="tab-content">
                                @include('voyager::strategies.partials.detailstab')
                                
                                @if(!is_null($dataTypeContent->getKey()))
                                   
                                    @include('voyager::strategies.partials.creativestab')
                                    
                                    @include('voyager::strategies.partials.geofencingtab')
                                    
                                    @include('voyager::strategies.partials.targetingtab')

                                    @include('voyager::strategies.partials.datatab')

                                    @include('voyager::strategies.partials.contextualtab')
                                   
                                    @include('voyager::strategies.partials.tagtab')
                                    
                                @endif

                            </div>
                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary save" id="savebutton">{{ __('voyager::generic.save') }}</button>
                            <div id="displayError" style="display: none;" class="alert alert-warning" role="alert"></div>
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
@stop

@section('javascript')
    <script>
     @if(!is_null($dataTypeContent->getKey()))
        const STRATEGY_ID = "<?php echo $dataTypeContent->getKey(); ?>";
        let SITELIST_FIELDS = [];
        let IPLIST_FIELDS = [];
        let PMPSS_FIELDS = [];
        let SSP_FIELDS = [];
        let ZIPLIST_FIELDS = [];
        let COUNTRIES_FIELDS = [];
        let REGIONS_FIELDS = [];
        let CITIES_FIELDS = [];
        let ADVERTISER_CONCEPTS = [];
        let CUSTOM_DATAS = [];
        let PIXELS_LISTS = [];
        let INVENTORY_FIELDS = [
          { value: '1', label: 'Desktop & Mobile Web' },
          { value: '2', label: 'Mobile In-App' },
          { value: '3', label: 'Mobile Optimized Web' }
        ];
        let DEVICES_FIELDS = [
          { value: '1', label: 'Windows Computer' },
          { value: '2', label: 'Apple Computer' },
          { value: '3', label: 'Ipad' },
          { value: '4', label: 'Iphone' },
          { value: '5', label: 'Ipod' },
          { value: '6', label: 'Apple Device' },
          { value: '7', label: 'Android Phone' },
          { value: '8', label: 'Android Tablet' },
          { value: '9', label: 'Other' }
        ];

        let ISPS_FIELDS = [
          { value: '', label: 'All'}
        ];

        let OSS_FIELDS = [
          { value: '1', label: 'Windows 7'},
          { value: '2', label: 'Windows 8'},
          { value: '3', label: 'Windows 10'},
          { value: '4', label: 'Mac OS'},
          { value: '5', label: 'Linux'},
          { value: '6', label: 'ANDROID'},
          { value: '7', label: 'IOS'},
          { value: '9', label: 'Roku OS '},
          { value: '10', label: 'Tizen' },
          { value: '8', label: 'Other'}
        ];

        let BROWSER_FIELDS = [
          { value: '1', label: 'Chrome'},
          { value: '2', label: 'Firefox'},
          { value: '3', label: 'MSIE'},
          { value: '4', label: 'Opera'},
          { value: '5', label: 'Safari'},
          { value: '6', label: 'Other'}
        ];

        let CHANNEL_FIELDS = [
          { value: '1', label: 'Display'},
          { value: '2', label: 'Video/Audio'}
        ];

        let GOAL_VALUE_BID_FOR_FIELDS = [
          { value: '1', label: 'Total Spend'},
          { value: '2', label: 'Media Only'}
        ];

        let GOAL_TYPE_FIELDS = [
          { value: '1', label: 'CPC'},
          { value: '2', label: 'CTR'},
          { value: '3', label: 'Viewability Rate'},
          { value: '4', label: 'Viewable CTR'},
          { value: '5', label: 'CPM Reach'}
        ];

        let PACING_MONETARY_TYPE_FIELDS = [
          { value: '1', label: 'EVEN'},
          { value: '2', label: 'ASAP'}
        ];

        let PACING_MONETARY_INTERVAL_FIELDS = [
          { value: '1', label: 'Hour'},
          { value: '2', label: 'Day'}
        ];

        let PACING_IMPRESSION_TYPE_FIELDS = [
          { value: '1', label: 'EVEN'},
          { value: '2', label: 'ASAP'},
          { value: '3', label: 'NoCap'},
        ];

        let PACING_IMPRESSION_INTERVAL_FIELDS = [
          { value: '1', label: 'Hour'},
          { value: '2', label: 'Day'}
        ];


        let FREQUENCY_CAP_TYPE_FIELDS = [
          { value: '1', label: 'EVEN'},
          { value: '2', label: 'ASAP'}
        ];

        let FREQUENCY_CAP_INTERVAL_FIELDS = [
          { value: '1', label: 'Hour'},
          { value: '2', label: 'Day'},
          { value: '3', label: '7 Days'},
          { value: '4', label: '30 Days'}
        ];

        @foreach($advertiser_sitelists as $sitelist)
          SITELIST_FIELDS.push({ value: '{{ $sitelist->id }}', label: '{{ $sitelist->name }}' });
        @endforeach

        @foreach($advertiser_iplists as $iplist)
          IPLIST_FIELDS.push({ value: '{{ $iplist->id }}', label: '{{ $iplist->name }}' });
        @endforeach

        @foreach($organization_pmps as $pmp)
          PMPSS_FIELDS.push({ value: '{{ $pmp->deal_id }}', label: '{{ $pmp->deal_id  }}' });
        @endforeach

        @foreach($ssps as $ssp)
          SSP_FIELDS.push({ value: '{{ $ssp->name }}', label: '{{ $ssp->alias }}' });
        @endforeach

        @foreach($advertiser_ziplists as $ziplist)
          ZIPLIST_FIELDS.push({ value: '{{ $ziplist->id }}', label: '{{ $ziplist->name }}' });
        @endforeach

        @foreach($iab_countries as $country)
          COUNTRIES_FIELDS.push({ value: '{{ $country->code }}', label: '{{ $country->country }}' });
        @endforeach

        @foreach($iab_regions as $region)
          REGIONS_FIELDS.push({ value: '{{ $region->code }}', label: '{{ $region->region }} - {{ $region->pid }}' });
        @endforeach

        @foreach($selected_cities as $city)
          CITIES_FIELDS.push({ value: '{{ $city }}', label: '{{ $city }}' });
        @endforeach

        
        @foreach($pixels_list as $pixel)
          PIXELS_LISTS.push({ value: '{{ $pixel->id }}', label: '{{ $pixel->name }}' });
        @endforeach
        @foreach($custom_datas as $custom_data)
          CUSTOM_DATAS.push({ value: '{{ $custom_data->id }}', label: '{{ $custom_data->name }}' });
        @endforeach

        
        var params = {}
        var $image
     @endif
        $('document').ready(function () {

            $('#m_type').change(function(e){
                if(this.value == 3){
                    $('#m_amount').val(0);
                }
            })

            $('#i_type').change(function(e){
                if(this.value == 3){
                    $('#i_amount').val(0);
                }
            })

            $('#f_type').change(function(e){
                if(this.value == 2){
                    $('#f_amount').val(0);
                }
            })


            //Disable Save
            if($('#goal_min_bid').val()=="" || (parseInt($('#goal_min_bid').val())>parseInt($('#goal_max_bid').val()))){
                 $('#savebutton').prop('disabled', true);
             } else {
                 $('#savebutton').prop('disabled', false);
             }

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

            //Pass Selected Concept Values
            selectedConcepts();
            //Cities
            $('#cities').select2({
                ajax: {
                    url: '/api/cities',
                    data: function (params) {
                        var query = {
                            q: params.term
                        }

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    }
                }
            });
        });
        //update pacing // impressions

        $('#pacing_monetary').val($('#m_type').val()+","+$('#m_amount').val()+","+$('#m_stype').val());
        function changePacingMonetary(){
            $('#pacing_monetary').val($('#m_type').val()+","+$('#m_amount').val()+","+$('#m_stype').val());
        }

        $('#pacing_impression').val($('#i_type').val()+","+$('#i_amount').val()+","+$('#i_stype').val());
        function changePacingImpression(){
            $('#pacing_impression').val($('#i_type').val()+","+$('#i_amount').val()+","+$('#i_stype').val());
        }
        $('#frequency_cap').val($('#f_type').val()+","+$('#f_amount').val()+","+$('#f_stype').val());
        function changefrequencyCap(){
            $('#frequency_cap').val($('#f_type').val()+","+$('#f_amount').val()+","+$('#f_stype').val());
        }
        $('#goal_values').val($('#goal_amount').val()+","+$('#goal_bid_for').val()+","+$('#goal_min_bid').val()+","+$('#goal_max_bid').val());
        function changeGoalValues(){
            $('#goal_values').val($('#goal_amount').val()+","+$('#goal_bid_for').val()+","+$('#goal_min_bid').val()+","+$('#goal_max_bid').val());

            if($('#goal_min_bid').val()=="" || (parseInt($('#goal_min_bid').val())>parseInt($('#goal_max_bid').val()))){
                $('#savebutton').prop('disabled', true);
            } else {
                $('#savebutton').prop('disabled', false);
            }
        }

    </script>
    <script>
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("text", ev.target.id);
        }

        function drop(ev) {
            ev.preventDefault();
            let data = ev.dataTransfer.getData("text");

            let element = document.getElementById(data);

            let list = element.getAttribute('data-list')

            if (list === 'allowed'){
                element.setAttribute('data-list', 'selected')
                document.getElementById('concepts_list_selected_div').appendChild(element);
            }else{
                element.setAttribute('data-list', 'allowed')
                document.getElementById('concepts_list_div').appendChild(element);
            }
            setTimeout(() => {
                $('#search').trigger("keyup");
            }, 300);
            
        }
        function selectedConcepts(){
            var selectedList = document.getElementById('concepts_list_selected_div').querySelectorAll("li");
            var selected_concept_value = "";
            selectedList.forEach(function(element) {
                conceptid = element.id.split("-");
                selected_concept_value+=conceptid[1]+",";
            });
            $("#advertiser_creatives").trigger("change");
            document.getElementById('selected_concepts').value = selected_concept_value;
        }
        function getCities(q) {
            $.getJSON("http://{{ $_SERVER['HTTP_HOST'] }}/api/cities?q="+q, function (data) {
                var items = [];
                $.each(data, function (key, val) {
                    $('#cities').append('<option value="'+val["code"]+'">'+val["city"]+' ('+val["country"]+')</option>');
                });

            });
        }

        $( ".inc_exc").click(function() {
            if($(this).attr('value')==3){ // number 3 is OFF
                $('#'+$(this).data('input-id')).attr({ 'disabled': 'disabled' }); 
                $('.'+$(this).data('input-id')).attr({ 'disabled': 'disabled' }); 
                $('.'+$(this).data('input-tag')).tagsinput('removeAll'); 
                $('#'+$(this).data('input-id')).val(null).trigger('change');

                if($(this).data('input-id') == 'audiences_selection'){
                    $('.segments-container').attr("hidden", "hidden");
                    $('.segments-container-disabled').removeAttr('hidden');
                }    
                if($(this).data('input-id') == 'contextual_selection'){
                    $('.contextual-container').attr("hidden", "hidden");
                    $('.contextual-container-disabled').removeAttr('hidden');
                }
                if($(this).data('input-id') == 'isps'){
                    $('#isp_container').css("display", "none");
                    $('#isp_container_disabled').css("display", "block");
                }    
            } else {
                $('#'+$(this).data('input-id')).removeAttr('disabled');
                $('.'+$(this).data('input-id')).removeAttr('disabled');
                if($(this).data('input-id') == 'audiences_selection'){
                    $('.segments-container').removeAttr('hidden');
                    $('.segments-container-disabled').attr("hidden", "hidden");
                }   
                if($(this).data('input-id') == 'contextual_selection'){
                    $('.contextual-container').removeAttr('hidden');
                    $('.contextual-container-disabled').attr("hidden", "hidden");
                }
                if($(this).data('input-id') == 'isps'){
                    $('#isp_container').css("display", "block");
                    $('#isp_container_disabled').css("display", "none");
                }      
            }
        });
        $(".remove_context").click(function(event) {
            event.preventDefault();
            let contextID = $(this).data('context-id');

            let input_context = $('#contextual_selection').val().split(',');   
            $('#'+contextID+'-context-add').show();
            $('#'+contextID+'-context-remove').hide();
            const index = input_context.indexOf(""+contextID);
            if (index > -1) {
                input_context.splice(index, 1);
            }
            $('#contextual_selection').val(input_context.join());

            $('#selected-context-row-' + contextID).remove();
            
            return false;
        });
        

        $(".remove_audiece").click(function(event) {
                    event.preventDefault();
                    let audienceID = $(this).data('audience-id');

                    let input_audience = $('#audiences_selection').val().split(',');   
  
                    const index = input_audience.indexOf(""+audienceID);
                    if (index > -1) {
                        input_audience.splice(index, 1);
                    }
                    $('#'+audienceID+'-add').show();
                    $('#'+audienceID+'-remove').hide();
                    $('#audiences_selection').val(input_audience.join());

                    $('#selected-row-' + audienceID).remove();
                    
                    return false;
        });
        
    </script>
    <p id="demo"></p>

    <script>
        $(function(){
            var treeView = $("#treeview").dxTreeView({
                items: products,
                width: 500,
                searchEnabled: true
            }).dxTreeView("instance");

            $("#searchMode").dxSelectBox({
                dataSource: ["contains", "startswith"],
                value: "contains",
                onValueChanged: function(data) {
                    treeView.option("searchMode", data.value);
                }
            });
        });
        function getCats(id,catpath){
            if($('#segment-content-' + id).html()!=""){
                $('#segment-content-' + id).html("");
            } else {

                $.getJSON("http://{{ $_SERVER['HTTP_HOST'] }}/lab/oracle-categories.php?id=" + catpath, function (data) {
                    contenthtml = "";
                    iconmargins = "";
                    previconmarfin = $('#segmenticon-' + id).css('marginLeft').replace("px", "");
                    $.each(data, function (key, val) {
                        if(key!=""){
                            iconmargins = parseInt(previconmarfin) + 40 + "px";
                            if(val.childs > 0){
                                addicon = "voyager-angle-right";
                            } else {
                                addicon = "voyager-dot";
                            }
                            contenthtml = ''
                                + '<div style="width: 5%; display: inline-block; margin-top: 5px; height: 28px;"><i class="voyager-plus" style="font-size:16px; cursor:pointer" onclick="addRemoveSegment('+ val.id+',\''+key+'\')"></i></div>'
                                + '<div style="width: 82%; display: inline-block; margin-top: 5px;cursor: pointer;" onclick="getCats('+val.id+',\'' + val.path + '\')"><i id="segmenticon-' + val.id + '" class="'+addicon+'" style="margin-left: ' + iconmargins + ';"></i> ' + key + '</div>'
                                + '<div style="width: 5%; display: inline-block; margin-top: 5px; text-align: center">' + val.reach + '</div>'
                                + '<div style="width: 5%; display: inline-block; margin-top: 5px; text-align: center">$ '+val.priceCPM.toFixed(2)+'</div>'
                                + '<br><div style="border-bottom: solid 1px #cbdaea; height: 2px"></div>'
                                + '<div id="segment-content-' + val.id + '" style="font-size: 0.875rem;"></div>'
                                + contenthtml;
                        }
                    });
                    $('#segment-content-' + id).html(contenthtml);
                });
            }
            //check all segments checkboxes and see which is selected

        }
        function getCatsIn(id,dmp){
        
                $.getJSON("http://{{ $_SERVER['HTTP_HOST'] }}/lab/get-dmps.php?id=" + id, function (data) {
                    contenthtml = "";
                    iconmargins = "";
                    previconmarfin = $('#segmenticon-' + id).css('marginLeft').replace("px", "");
                    $.each(data, function (key, val) {
                        if(key==dmp){
                            $.each(val, function (key, val) {
                                iconmargins = parseInt(previconmarfin) + 5 + "px";
                                addicon = "voyager-dot";

                                contenthtml = ''
                                    + '<div style="width: 5%; display: inline-block; margin-top: 5px; height: 28px;"><i class="voyager-plus" style="font-size:16px; cursor:pointer" onclick="addRemoveSegmentIn(\'' + key + '\',\''+val.name+'\')"></i></div>'
                                    + '<div style="width: 82%; display: inline-block; margin-top: 5px;cursor: pointer;" onclick="getCats(' + val.id + ',\'' + val.path + '\')"><i id="segmenticon-' + val.id + '" class="' + addicon + '" style="margin-left: ' + iconmargins + ';"></i> ' + val.name + '</div>'
                                    + '<div style="width: 5%; display: inline-block; margin-top: 5px; text-align: center">' + val.reach + '</div>'
                                    + '<div style="width: 5%; display: inline-block; margin-top: 5px; text-align: center">$ ' + val.price.toFixed(2) + '</div>'
                                    + '<br><div style="border-bottom: solid 1px #cbdaea; height: 2px"></div>'
                                    + '<div id="segment-content-' + val.id + '" style="font-size: 0.875rem;"></div>'
                                    + contenthtml;
                            });
                        }
                    });
                    $('#segment-content-in-' + id).html(contenthtml);
                });
        }
        function findAudience (id,q) {
            if (q.length>2) {

                $.getJSON("http://{{ $_SERVER['HTTP_HOST'] }}/lab/oracle-categories.php?search=" + q, function (data) {
                    contenthtml = "";
                    iconmargins = "";
                    previconmarfin = $('#segmenticon-' + id).css('marginLeft').replace("px", "");
                    $.each(data, function (key, val) {
                
                        if (key != "") {
                            iconmargins = parseInt(previconmarfin) + 40 + "px";
                            if (val.childs > 0) {
                                addicon = "voyager-angle-right";
                            } else {
                                addicon = "voyager-dot";
                            }
                            contenthtml = ''
                                + '<div style="width: 5%; display: inline-block; margin-top: 5px; height: 28px;"><i class="voyager-plus" style="font-size:16px; cursor:pointer" onclick="addRemoveSegment(' + val.id + ',\'' + key + '\')"></i></div>'
                                + '<div style="width: 82%; display: inline-block; margin-top: 5px;cursor: pointer;" onclick="getCats(' + val.id + ',\'' + val.path + '\')"><i id="segmenticon-' + val.id + '" class="' + addicon + '" style="margin-left: ' + iconmargins + ';"></i> ' + key + '</div>'
                                + '<div style="width: 5%; display: inline-block; margin-top: 5px; text-align: center">' + val.reach + '</div>'
                                + '<div style="width: 5%; display: inline-block; margin-top: 5px; text-align: center">$ ' + val.priceCPM.toFixed(2) + '</div>'
                                + '<br><div style="border-bottom: solid 1px #cbdaea; height: 2px"></div>'
                                + '<div id="segment-content-' + val.id + '" style="font-size: 0.875rem;"></div>'
                                + contenthtml;
                            // console.log($('#segmenticon-'+id).css('marginLeft').replace("px",""));
                        }
                    });
                    $('#segment-content-' + id).html(contenthtml);
                });
            }
        }
        function randomMill(){
            return Math.floor((Math.random() * 100) + 1);
        }
        function randomCPM(){
            return Math.floor((Math.random() * 0.5 - 0.9) + 0.9).toFixed(2);
        }

        function addRemoveSegment(id,name){
            var data = {
                id: id,
                text: name
            };
            if (!$('#selected_segments').find("option[value='" + data.id + "']").length) {

                var newOption = new Option(data.text, data.id, false, true);
                $('#selected_segments').append(newOption).trigger('change');

            }
        }
        function addRemoveSegmentIn(id,name){
            var data = {
                id: id,
                text: name
            };
            if (!$('#selected_segments_in').find("option[value='" + data.id + "']").length) {

                var newOption = new Option(data.text, data.id, false, true);
                $('#selected_segments_in').append(newOption).trigger('change');

            }
        }

        
        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            
            $('#search').keyup(function (e) {
                $('.search-li').hide();
                let key = this.value.toUpperCase();
                if(key==''){
                    clearSearch();
                }else{
                    let result = $("li[data-search*='"+key+"']");
                    if(result.length>0){
                        result.show();
                    }
                }
            });

            
            $('#search-segments').keyup(function (e) {
                setTimeout(() => {
                    loadSegment();
                }, 1000);
            });
            
            $(".tabs-segments").click(function (e) {
                setTimeout(() => {
                    loadSegment();
                }, 500);
                
            });

            $(".segments_filters").click(function (e) {
                setTimeout(() => {
                    loadSegment();
                }, 500);
            });
////////////////////////////////////////////////////////////////////

            $('#search-contextual').keyup(function (e) {
                setTimeout(() => {
                    loadContextual();
                }, 1000);
            });
            
            $(".tabs-contextual").click(function (e) {
                setTimeout(() => {
                    loadContextual();
                }, 500);
                
            });

            $(".contextual_filters").click(function (e) {
                setTimeout(() => {
                    loadContextual();
                }, 500);
            });

            const loadContextual = async () => {
                $('#tbody-contextual').empty();
                $('#loading-contextual').show();
                const tab = $('.tabs-contextual.active').data('tab-name');
                const search = $('#search-contextual').val();
                
                $('#contextualPagination').hide();
                
                let channel = '';
                if($('#contextual_target_1').prop("checked")){
                    channel += 'SITE';
                }

                if($('#contextual_target_2').prop("checked")){
                    if(channel == ''){
                        channel += 'IOSAPP';
                    }else{
                        channel += ',IOSAPP';
                    }
                }
                if($('#contextual_target_3').prop("checked")){
                    if(channel == ''){
                        channel += 'ANDROIDAPP';
                    }else{
                        channel += ',ANDROIDAPP';
                    }
                }
                if($('#contextual_target_4').prop("checked")){
                    if(channel == ''){
                        channel += 'OTHER';
                    }else{
                        channel += ',OTHER';
                    }
                }

                url = '/api/contextualinfo?origin='+tab+'&name='+search+'&id='+search+'&channel='+channel;
            
                await $.get(url, function (res) {
                    contextualData = [];
                    for (const id in res.data) {
                        contextualData.push({
                        id: id,
                        channel: res.data[id].channel,
                        name: res.data[id].name,
                        items: res.data[id].items,
                        });
                    }    
                });
               
                currentIndex=0;
                fillContext(currentIndex);
            }
            
            const loadSegment = async () => {
                $('#tbody-dmp').empty();
                $('#loading').show();
                
                const tab = $('.tabs-segments.active').data('tab-name');
                const search = $('#search-segments').val();
                
                $('#segmentsPagination').hide();
                
            
                url = '/api/dmpinfo?dmp='+tab+'&name='+search+'&id='+search;
            
                await $.get(url, function (res) {
                    segmentsData = [];
                    for (const id in res.data) {
                        
                        if(res.data[id].ANDROID != undefined && $('#segments_target_1').prop("checked")){
                            segmentsData.push({
                            id: id+'-ANDROID',
                            type: 'Android',
                            name: res.data[id].name,
                            reach: res.data[id].ANDROID,
                            price: res.data[id].price,
                            });
                        }
                        if(res.data[id].IOS != undefined && $('#segments_target_2').prop("checked")){
                            segmentsData.push({
                            id: id+'-IOS',
                            type: 'Ios',
                            name: res.data[id].name,
                            reach: res.data[id].IOS,
                            price: res.data[id].price,
                            });
                        }
                        if(res.data[id].IP != undefined && $('#segments_target_3').prop("checked")){
                            segmentsData.push({
                            id: id+'-IP',
                            type: 'Ip',
                            name: res.data[id].name,
                            reach: res.data[id].IP,
                            price: res.data[id].price,
                            });
                        }
                        if(res.data[id].COOKIE != undefined && $('#segments_target_4').prop("checked")){
                            segmentsData.push({
                            id: id+'-COOKIE',
                            type: 'Cookie',
                            name: res.data[id].name,
                            reach: res.data[id].COOKIE,
                            price: res.data[id].price,
                            });
                        }
                    }    
                });
               
                currentIndex=0;
                fillSegments(currentIndex);
            }

            $('#advertiser_creatives').change(function (e) {
                loadConcepts($(this).val());
            });

            $("#advertiser_creatives").trigger("change");
            const clearSearch = () => {
                $('#search').val('');
                $('.search-li').show();
            }

            document.getElementById("savebutton").addEventListener("click", function(event){
                event.preventDefault();
                if (validateRequiredFields()){
                    $('#strategyForm').submit();
                }else{
                    return false;
                }
            });

            loadSegment();
            loadContextual();

        });

        const fillSegments = (page) => {

            $('#tbody-dmp').empty();

            let resultsAmount = Object.keys(segmentsData).length;

            pagesAmount = Math.ceil(resultsAmount/pagination);

            if(pagesAmount > 1){
                $('#paginationContainer').empty();
                $('#segmentsPagination').show();
                let paginationHtml = `
                        <li class="paginate_button previous" id="dataTable_prev">
                            <a href="#" aria-controls="dataTable" data-index="prev" tabindex="0">Previous</a>
                        </li>`;
                paginationHtml += `
                        <li id="pageNumber0" class="paginate_button"><a href="#" class="pageNumberButton" aria-controls="dataTable" data-index="0" tabindex="0">1</a>
                        </li>
                `;

                for (let index = 1; index < pagesAmount-1; index++) {
                    if(index >= (currentIndex - 4 ) && index <= (currentIndex +4 ) ){
                        paginationHtml += `
                        <li id="pageNumber${index}" class="paginate_button ${index==0 ? 'active': ''}"><a href="#" class="pageNumberButton" aria-controls="dataTable" data-index="${index}" tabindex="${index}">${index+1}</a>
                        </li>
                    `;
                    }
                
                }                    
                paginationHtml += `
                        <li id="pageNumber${pagesAmount-1}" class="paginate_button"><a href="#" class="pageNumberButton" aria-controls="dataTable" data-index="${pagesAmount-1}" tabindex="${pagesAmount-1}">${pagesAmount}</a>
                        </li>
                `;
                paginationHtml += `
                    <li class="paginate_button next" id="dataTable_next">
                        <a href="#" aria-controls="dataTable"  data-index="next" tabindex="0">Next</a>
                    </li>
                `;
                $('#paginationContainer').append(paginationHtml);
            }



            $(".pageNumberButton").click(function (e) {
                e.preventDefault();
                let page = $(this).data('index');
                currentIndex = page;
                fillSegments(page);
                return false;
            });

            $("#dataTable_next").click(function (e) {
                e.preventDefault();
                currentIndex++;
                fillSegments(currentIndex);
                return false;
            });

            $("#dataTable_prev").click(function (e) {
                e.preventDefault();
                currentIndex--;
                fillSegments(currentIndex);
                return false;
            });

            if(page < 0 ){currentIndex = 0; page = 0;}
            if(page >= pagesAmount){ currentIndex =pagesAmount - 1; page = pagesAmount - 1; }
            currentIndex=page;

            $('.paginate_button').removeClass('active');
            $('#pageNumber'+page).addClass('active');


            let input_audience = $('#audiences_selection').val().replace(' ','').split(',');   
            
            
            let html = '';

            let count=0;

            for (const segment of segmentsData) {
                if(count < (page*pagination)){ count++; continue;}
                if(count >= (page*pagination)+pagination ){break;}

                let included = false;
                if(input_audience.includes(segment.id)){
                    included = true;
                }
                html+=`<tr  data-price="${segment.price}">
                        <td style="padding-left: 0.5em;" scope="row">${segment.id}</td>
                        <td style="padding-left: 0.5em;">${segment.name}</td>
                        <td style="padding-left: 0.5em;">${segment.type}</td>
                        <td class="text-right" style="padding-right: 0.5em;">${segment.reach}</td>
                        <td class="text-right" style="padding-right: 0.5em;">$ ${parseFloat(segment.price).toFixed(2)}</td>
                        <td class="text-center">
                            <button ${included ? 'style="display: none;"' : ''  }  id="${segment.id}-add" data-id="${segment.id}" data-name="${segment.name}" data-reach="${segment.reach}" data-price="${segment.price}" data-type="${segment.type}" class="btn btn-small btn-success add_audiece">+</button>
                            <button ${!included ?'style="display: none;"' : ''  }  id="${segment.id}-remove" data-audience-id="${segment.id}" data-price="${segment.price}" class="btn btn-small btn-warning remove_audiece"  >-</button>
                        </td>
                    </tr>
                `;
                count++;
            }

            $('#tbody-dmp').append(html);

         
            $('#loading').hide();
            
           


            $( ".add_audiece").click(function(event) {
                event.preventDefault();
                let audience ={
                    id: $(this).data('id'),
                    name: $(this).data('name'),
                    reach: $(this).data('reach'),
                    price: $(this).data('price'),
                    type: $(this).data('type') 
                }
        
                if(!$('#selected-row-'+ audience.id).length){

                    let input_audience = $('#audiences_selection').val().replace(' ','').split(',');   
        
                    if(!input_audience.includes(audience.id)){
                        input_audience.push(audience.id);
                    }
                    const index = input_audience.indexOf("");
                        if (index > -1) {
                            input_audience.splice(index, 1);
                    }

                    $('#'+audience.id+'-add').hide();
                    $('#'+audience.id+'-remove').show();

                    $('#audiences_selection').val(input_audience.join());

                    let price = parseFloat($(this).data('price'));
                    let audiences_cpm =  parseFloat($('#audiences_cpm').val());
                    $('#audiences_cpm').val(audiences_cpm + price);


                    let newTr =  `<tr id="selected-row-${audience.id}" data-price="${audience.price}">
                                    <td style="padding-left: 0.5em;" scope="row">${audience.id}</td>
                                    <td style="padding-left: 0.5em;">${audience.name}</td>
                                    <td style="padding-left: 0.5em;" >${audience.type}</td>
                                    <td class="text-right" style="padding-right: 0.5em;">${audience.reach}</td>   
                                    <td class="text-right" style="padding-right: 0.5em;">$ ${parseFloat(audience.price).toFixed(2)}</td>
                                    <td class="text-center"><button data-price="${audience.price}" data-audience-id="${audience.id}" class="btn btn-small btn-warning remove_audiece"  >-</button></td>
                                </tr>`;
                    $('#tbodyAudiencesSelected').append(newTr);   


                    $(".remove_audiece").click(function(event) {
                        event.preventDefault();
                        let audienceID = $(this).data('audience-id');
                        let price = parseFloat($(this).data('price'));

                        let audiences_cpm =  parseFloat($('#audiences_cpm').val());
                        if(audiences_cpm - price < 0){
                            $('#audiences_cpm').val(0);
                        }  else{
                            $('#audiences_cpm').val(audiences_cpm - price);
                        }

                        
                        let input_audience = $('#audiences_selection').val().split(',');   
                        $('#'+audienceID+'-add').show();
                        $('#'+audienceID+'-remove').hide();
                        const index = input_audience.indexOf(""+audienceID);
                        if (index > -1) {
                            input_audience.splice(index, 1);
                        }
                        $('#audiences_selection').val(input_audience.join());

                        $('#selected-row-' + audienceID).remove();
                        
                        return false;
                    });
                }
                return false;
                
            });
        }
        const fillContext = (page) => {

            $('#tbody-contextual').empty();

            let resultsAmount = Object.keys(contextualData).length;

            pagesAmount = Math.ceil(resultsAmount/pagination_contex);

            if(pagesAmount > 1){
                $('#contextual_paginationContainer').empty();
                $('#contextualPagination').show();
                let paginationHtml = `
                        <li class="paginate_button_contex previous" id="dataTable_prev_contex">
                            <a href="#" aria-controls="dataTable" data-index="prev" tabindex="0">Previous</a>
                        </li>`;
                paginationHtml += `
                        <li id="pageNumber0" class="paginate_button_contex"><a href="#" class="pageNumberButton_contex" aria-controls="dataTable" data-index="0" tabindex="0">1</a>
                        </li>
                `;

                for (let index = 1; index < pagesAmount-1; index++) {
                    if(index >= (currentIndex_contex - 4 ) && index <= (currentIndex_contex +4 ) ){
                        paginationHtml += `
                        <li id="pageNumber${index}" class="paginate_button_contex ${index==0 ? 'active': ''}"><a href="#" class="pageNumberButton_contex" aria-controls="dataTable" data-index="${index}" tabindex="${index}">${index+1}</a>
                        </li>
                    `;
                    }
                
                }                    
                paginationHtml += `
                        <li id="pageNumber${pagesAmount-1}" class="paginate_button_contex"><a href="#" class="pageNumberButton_contex" aria-controls="dataTable" data-index="${pagesAmount-1}" tabindex="${pagesAmount-1}">${pagesAmount}</a>
                        </li>
                `;
                paginationHtml += `
                    <li class="paginate_button_contex next" id="dataTable_next_contex">
                        <a href="#" aria-controls="dataTable"  data-index="next" tabindex="0">Next</a>
                    </li>
                `;
                $('#contextual_paginationContainer').append(paginationHtml);
            }



            $(".pageNumberButton_contex").click(function (e) {
                e.preventDefault();
                let page = $(this).data('index');
                currentIndex_contex = page;
                fillContext(page);
                return false;
            });

            $("#dataTable_next_contex").click(function (e) {
                e.preventDefault();
                currentIndex_contex++;
                fillContext(currentIndex_contex);
                return false;
            });

            $("#dataTable_prev_contex").click(function (e) {
                e.preventDefault();
                currentIndex_contex--;
                fillContext(currentIndex_contex);
                return false;
            });

            if(page < 0 ){currentIndex_contex = 0; page = 0;}
            if(page >= pagesAmount){ currentIndex_contex =pagesAmount - 1; page = pagesAmount - 1; }
            currentIndex_contex=page;

            $('.paginate_button_contex').removeClass('active');
            $('#pageNumber'+page).addClass('active');


            let input_context = $('#contextual_selection').val().replace(' ','').split(',');   
            
            
            let html = '';

            let count=0;

            for (const context of contextualData) {
                if(count < (page*pagination_contex)){ count++; continue;}
                if(count >= (page*pagination_contex)+pagination_contex ){break;}

                let included = false;
                if(input_context.includes(context.id)){
                    included = true;
                }
                html+=`<tr>
                        <td class="col-sm-3"  style="padding-left: 0.5em;"  scope="row">${context.id}</td>
                        <td class="col-sm-4"  style="padding-left: 0.5em;" >${context.name}</td>
                        <td class="col-sm-2"  style="padding-left: 0.5em;" >${context.channel}</td>
                        <td class="text-right col-sm-2" style="padding-right: 0.5em;">${context.items}</td>
                        <td class="text-center col-sm-1">
                            <button ${included ? 'style="display: none;"' : ''  }  id="${context.id}-context-add" data-id="${context.id}" data-name="${context.name}" data-items="${context.items}" data-channel="${context.channel}" class="btn btn-small btn-success add_context">+</button>
                            <button ${!included ?'style="display: none;"' : ''  }  id="${context.id}-context-remove" data-context-id="${context.id}" class="btn btn-small btn-warning remove_context"  >-</button>
                        </td>
                    </tr>
                `;
                count++;
            }

            $('#tbody-contextual').append(html);

         
            $('#loading-contextual').hide();

            $( ".add_context").click(function(event) {
                event.preventDefault();
                let context ={
                    id: $(this).data('id'),
                    name: $(this).data('name'),
                    items: $(this).data('items'),
                    channel: $(this).data('channel') 
                }
        
                if(!$('#selected-context-row-'+ context.id).length){

                    let input_context = $('#contextual_selection').val().replace(' ','').split(',');   
        
                    if(!input_context.includes(context.id)){
                        input_context.push(context.id);
                    }
                    const index = input_context.indexOf("");
                        if (index > -1) {
                            input_context.splice(index, 1);
                    }

                    $('#'+context.id+'-context-add').hide();
                    $('#'+context.id+'-context-remove').show();

                    $('#contextual_selection').val(input_context.join());

                    let newTr =  `<tr id="selected-context-row-${context.id}">
                                    <td class="col-sm-3" style="padding-left: 0.5em;" scope="row">${context.id}</td>
                                    <td class="col-sm-4" style="padding-left: 0.5em;" >${context.name}</td>
                                    <td class="col-sm-2" style="padding-left: 0.5em;" >${context.channel}</td>
                                    <td class="text-right col-sm-2" style="padding-right: 0.5em;">${context.items}</td>   
                                    <td class="text-center col-sm-1" ><button data-context-id="${context.id}" class="btn btn-small btn-warning remove_context"  >-</button></td>
                                </tr>`;
                    $('#tbodyContextualSelected').append(newTr);   


                  
                }
                return false;
                
            });
            $(".remove_context").click(function(event) {
                        event.preventDefault();
                        let contextID = $(this).data('context-id');

                        let input_context = $('#contextual_selection').val().split(',');   
                        $('#'+contextID+'-context-add').show();
                        $('#'+contextID+'-context-remove').hide();
                        const index = input_context.indexOf(""+contextID);
                        if (index > -1) {
                            input_context.splice(index, 1);
                        }
                        $('#contextual_selection').val(input_context.join());

                        $('#selected-context-row-' + contextID).remove();
                        
                        return false;
                    });
        }
        // global variable to handdle segmentation data
        let segmentsData=[];
        let currentIndex=0;
        let pagesAmount=0;
        let pagination=25;

        let contextualData=[];
        let currentIndex_contex=0;
        let pagesAmount_contex=0;
        let pagination_contex=25;

        const validateRequiredFields = () => {

            let message='';
            let error = false;

            //tab details

            let nameLenght = $('input[name="name"]').val().length;
  
            if (nameLenght <= 0 || nameLenght >= 256 ){
                message +='<p>[DETAILS] please verify field "name" it must contain 1-256 characters</p>';
                error=true;
            }


            let date_start =  $('input[name ="date_start"]').val();
            let date_end =  $('input[name ="date_end"]').val();

            if (date_start==''){
                message +='<p>[DETAILS] please complete start date</p>';
                error=true;
            }
            if (date_end==''){
                message +='<p>[DETAILS] please complete end date</p>';
                error=true;
            }

            let d1 = new Date(date_start);
            let d2 = new Date(date_end);

            if(d1.getTime() > d2.getTime()){
                message +='<p>[DETAILS] please verify dates (start date is greater than end date)</p>';
                error=true;
            }

            if ($('input[name ="budget"]').val()<=0){
                message +='<p>[DETAILS] budget must be greater than zero</p>';
                error=true;
            }


            if($('#m_type').val() == 3 &&  $('#i_type').val() == 3  && $('#f_type').val() == 2 ) {
                message +='<p>[DETAILS] please verify Pacings, can not disable all of them</p>';
                error=true;
            }

            if($('#m_type').val() == 1 && !parseInt($('#m_amount').val())>0 ){
                message +='<p>[DETAILS] please verify Pacing Monetary, can not select "even" without an amount</p>';
                error=true;
            }
            if($('#i_type').val() == 1 && !parseInt($('#i_amount').val())>0 ){
                message +='<p>[DETAILS] please verify Pacing Impression, can not select "even" without an amount</p>';
                error=true;
            }
            if($('#f_type').val() == 1 && !parseInt($('#f_amount').val())>0 ){
                message +='<p>[DETAILS] please verify Frequency Cap, can not select "even" without an amount</p>';
                error=true;
            }

            if ($('#m_type').val() === 3) {
                $('#m_amount').val("");
            }
            if ($('#i_type').val() === 3) {
                $('#i_amount').val("");
            }
            if ($('#f_type').val() === 3) {
                $('#f_amount').val("");
            }



            let goal_min_bid =  isNaN(parseFloat($('input[name ="goal_min_bid"]').val()))   ? 0: parseFloat($('input[name ="goal_min_bid"]').val());
            let goal_max_bid =  isNaN(parseFloat($('input[name ="goal_max_bid"]').val())) ? 0 : parseFloat($('input[name ="goal_max_bid"]').val());

            if( goal_min_bid <= 0.09 || goal_max_bid <=0.09 ||  goal_max_bid <= goal_min_bid ){
                message +='<p>[DETAILS] please verify goals, must be greater than zero and max goal > min goal</p>';
                error=true;
            }

            //tab targeting
            $('.inc_exc:checked').each(function(){
                if($(this).attr('value')!=3 && $('#'+$(this).data('input-id')).val()==''){
                 
                    message +='<p>[TARGETING]please complete '+$(this).data('input-id')+' or turn it off</p>';
                    error=true;
                }
            });

            $('.inc_exc:checked').each(function(){
                if($(this).attr('value')!=3 && $('#'+$(this).data('input-id')).val()==''){
                 
                    message +='<p>[TARGETING]please complete '+$(this).data('input-id')+' or turn it off</p>';
                    error=true;
                }
            });
            
            
            if ($('#open_market').prop("checked") === false) {
                let pmp = $('#pmps').val();
                if(pmp.length == 0){
                    message +='<p>[TARGETING] Please select a pmp or check the open market box</p>';
                    error=true;
                } else {
                    if(pmp[0] ==''){
                        message +='<p>[TARGETING] open market box is not ckeched, please select a Pmp</p>';
                        error=true;
                    }   
                }
            }
            if(error){
                $('#displayError').empty();
                $('#displayError').append(message);
                $('#displayError').css("display", "block");
                setTimeout(() => {
                    $('#displayError').css("display", "none");
                }, 7000);
                return false;
            }

            return true;
        }

        $("#selectallssps").click(function(){
            if (!$( this ).attr( "disabled" )){
                $("#ssps > option").prop("selected","selected");
                $("#ssps").trigger("change");
            }
        });

    </script>
    <script src="{{ asset('js/strategy-edit.js')}}"></script>
    <script>
   

   (function ($) {
  "use strict";

  var defaultOptions = {
      tagClass: function (item) {
          return 'label label-info';
      },
      itemValue: function (item) {
          return item ? item.toString() : item;
      },
      itemText: function (item) {
          return this.itemValue(item);
      },
      itemTitle: function (item) {
          return null;
      },
      freeInput: true,
      addOnBlur: true,
      maxTags: undefined,
      maxChars: undefined,
      confirmKeys: [13, 44],
      onTagExists: function (item, $tag) {
          $tag.hide().fadeIn();
      },
      trimValue: false,
      allowDuplicates: false,
      splitOn: ','
  };

  /**
   * Constructor function
   */
  function TagsInput(element, options) {
      this.itemsArray = [];

      this.$element = $(element);
      this.$element.hide();

      this.isSelect = (element.tagName === 'SELECT');
      this.multiple = (this.isSelect && element.hasAttribute('multiple'));
      this.objectItems = options && options.itemValue;
      this.placeholderText = element.hasAttribute('placeholder') ? this.$element.attr('placeholder') : '';
      this.inputSize = Math.max(1, this.placeholderText.length);

      this.$container = $('<div class="bootstrap-tagsinput"></div>');
      this.$input = $('<input type="text" placeholder="' + this.placeholderText + '"/>').appendTo(this.$container);

      this.$element.before(this.$container);

      this.build(options);
  }

  TagsInput.prototype = {
      constructor: TagsInput,

      /**
       * Adds the given item as a new tag. Pass true to dontPushVal to prevent
       * updating the elements val()
       */
      add: function (item, dontPushVal, options) {
          var self = this;

          if (self.options.maxTags && self.itemsArray.length >= self.options.maxTags) return;

          // Ignore falsey values, except false
          if (item !== false && !item) return;

          // Trim value
          if (typeof item === "string" && self.options.trimValue) {
              item = $.trim(item);
          }

          // Throw an error when trying to add an object while the itemValue option was not set
          if (typeof item === "object" && !self.objectItems) throw ("Can't add objects when itemValue option is not set");

          // Ignore strings only containg whitespace
          if (item.toString().match(/^\s*$/)) return;

          // If SELECT but not multiple, remove current tag
          if (self.isSelect && !self.multiple && self.itemsArray.length > 0) self.remove(self.itemsArray[0]);

          if (self.options.splitOn && typeof item === "string" && this.$element[0].tagName === 'INPUT') {
              var items = item.split(self.options.splitOn);
              if (items.length > 1) {
                  for (var i = 0; i < items.length; i++) {
                      this.add(items[i], true);
                  }

                  if (!dontPushVal) self.pushVal();
                  return;
              }
          }
          
          var itemValue = self.options.itemValue(item),
              itemText = self.options.itemText(item),
              tagClass = self.options.tagClass(item),
              itemTitle = self.options.itemTitle(item);

          // Ignore items allready added
          var existing = $.grep(self.itemsArray, function (item) {
              return self.options.itemValue(item) === itemValue;
          })[0];
          if (existing && !self.options.allowDuplicates) {
              // Invoke onTagExists
              if (self.options.onTagExists) {
                  var $existingTag = $(".tag", self.$container).filter(function () {
                      return $(this).data("item") === existing;
                  });
                  self.options.onTagExists(item, $existingTag);
              }
              return;
          }

          // if length greater than limit
          if (self.items().toString().length + item.length + 1 > self.options.maxInputLength) return;

          // raise beforeItemAdd arg
          var beforeItemAddEvent = $.Event('beforeItemAdd', {
              item: item,
              cancel: false,
              options: options
          });
          self.$element.trigger(beforeItemAddEvent);
          if (beforeItemAddEvent.cancel) return;

          // register item in internal array and map
          self.itemsArray.push(item);

          // add a tag element

          var $tag = $('<span class="tag ' + htmlEncode(tagClass) + (itemTitle !== null ? ('" title="' + itemTitle) : '') + '">' + htmlEncode(itemText) + '<span data-role="remove"></span></span>');
          $tag.data('item', item);
          self.findInputWrapper().before($tag);
          $tag.after(' ');

          // add <option /> if item represents a value not present in one of the <select />'s options
          if (self.isSelect && !$('option[value="' + encodeURIComponent(itemValue) + '"]', self.$element)[0]) {
              var $option = $('<option selected>' + htmlEncode(itemText) + '</option>');
              $option.data('item', item);
              $option.attr('value', itemValue);
              self.$element.append($option);
          }

          if (!dontPushVal) self.pushVal();

          // Add class when reached maxTags
          if (self.options.maxTags === self.itemsArray.length || self.items().toString().length === self.options.maxInputLength) self.$container.addClass('bootstrap-tagsinput-max');

          self.$element.trigger($.Event('itemAdded', {
              item: item,
              options: options
          }));
      },

      /**
       * Removes the given item. Pass true to dontPushVal to prevent updating the
       * elements val()
       */
      remove: function (item, dontPushVal, options) {
          var self = this;

          if (self.objectItems) {
              if (typeof item === "object") item = $.grep(self.itemsArray, function (other) {
                  return self.options.itemValue(other) == self.options.itemValue(item);
              });
              else item = $.grep(self.itemsArray, function (other) {
                  return self.options.itemValue(other) == item;
              });

              item = item[item.length - 1];
          }

          if (item) {
              var beforeItemRemoveEvent = $.Event('beforeItemRemove', {
                  item: item,
                  cancel: false,
                  options: options
              });
              self.$element.trigger(beforeItemRemoveEvent);
              if (beforeItemRemoveEvent.cancel) return;

              $('.tag', self.$container).filter(function () {
                  return $(this).data('item') === item;
              }).remove();
              $('option', self.$element).filter(function () {
                  return $(this).data('item') === item;
              }).remove();
              if ($.inArray(item, self.itemsArray) !== -1) self.itemsArray.splice($.inArray(item, self.itemsArray), 1);
          }

          if (!dontPushVal) self.pushVal();

          // Remove class when reached maxTags
          if (self.options.maxTags > self.itemsArray.length) self.$container.removeClass('bootstrap-tagsinput-max');

          self.$element.trigger($.Event('itemRemoved', {
              item: item,
              options: options
          }));
      },

      /**
       * Removes all items
       */
      removeAll: function () {
          var self = this;

          $('.tag', self.$container).remove();
          $('option', self.$element).remove();

          while (self.itemsArray.length > 0)
          self.itemsArray.pop();

          self.pushVal();
      },

      /**
       * Refreshes the tags so they match the text/value of their corresponding
       * item.
       */
      refresh: function () {
          var self = this;
          $('.tag', self.$container).each(function () {
              var $tag = $(this),
                  item = $tag.data('item'),
                  itemValue = self.options.itemValue(item),
                  itemText = self.options.itemText(item),
                  tagClass = self.options.tagClass(item);

              // Update tag's class and inner text
              $tag.attr('class', null);
              $tag.addClass('tag ' + htmlEncode(tagClass));
              $tag.contents().filter(function () {
                  return this.nodeType == 3;
              })[0].nodeValue = htmlEncode(itemText);

              if (self.isSelect) {
                  var option = $('option', self.$element).filter(function () {
                      return $(this).data('item') === item;
                  });
                  option.attr('value', itemValue);
              }
          });
      },

      /**
       * Returns the items added as tags
       */
      items: function () {
          return this.itemsArray;
      },

      /**
       * Assembly value by retrieving the value of each item, and set it on the
       * element.
       */
      pushVal: function () {
          var self = this,
              val = $.map(self.items(), function (item) {
                  return self.options.itemValue(item).toString();
              });

          self.$element.val(val, true).trigger('change');
      },

      /**
       * Initializes the tags input behaviour on the element
       */
      build: function (options) {
          var self = this;

          self.options = $.extend({}, defaultOptions, options);
          // When itemValue is set, freeInput should always be false
          if (self.objectItems) self.options.freeInput = false;

          makeOptionItemFunction(self.options, 'itemValue');
          makeOptionItemFunction(self.options, 'itemText');
          makeOptionFunction(self.options, 'tagClass');

          // Typeahead Bootstrap version 2.3.2
          if (self.options.typeahead) {
              var typeahead = self.options.typeahead || {};

              makeOptionFunction(typeahead, 'source');

              self.$input.typeahead($.extend({}, typeahead, {
                  source: function (query, process) {
                      function processItems(items) {
                          var texts = [];

                          for (var i = 0; i < items.length; i++) {
                              var text = self.options.itemText(items[i]);
                              map[text] = items[i];
                              texts.push(text);
                          }
                          process(texts);
                      }

                      this.map = {};
                      var map = this.map,
                          data = typeahead.source(query);

                      if ($.isFunction(data.success)) {
                          // support for Angular callbacks
                          data.success(processItems);
                      } else if ($.isFunction(data.then)) {
                          // support for Angular promises
                          data.then(processItems);
                      } else {
                          // support for functions and jquery promises
                          $.when(data)
                              .then(processItems);
                      }
                  },
                  updater: function (text) {
                      self.add(this.map[text]);
                      return this.map[text];
                  },
                  matcher: function (text) {
                      return (text.toLowerCase().indexOf(this.query.trim().toLowerCase()) !== -1);
                  },
                  sorter: function (texts) {
                      return texts.sort();
                  },
                  highlighter: function (text) {
                      var regex = new RegExp('(' + this.query + ')', 'gi');
                      return text.replace(regex, "<strong>$1</strong>");
                  }
              }));
          }

          // typeahead.js
          if (self.options.typeaheadjs) {
              var typeaheadConfig = null;
              var typeaheadDatasets = {};

              // Determine if main configurations were passed or simply a dataset
              var typeaheadjs = self.options.typeaheadjs;
              if ($.isArray(typeaheadjs)) {
                  typeaheadConfig = typeaheadjs[0];
                  typeaheadDatasets = typeaheadjs[1];
              } else {
                  typeaheadDatasets = typeaheadjs;
              }

              self.$input.typeahead(typeaheadConfig, typeaheadDatasets).on('typeahead:selected', $.proxy(function (obj, datum) {
                  if (typeaheadDatasets.valueKey) self.add(datum[typeaheadDatasets.valueKey]);
                  else self.add(datum);
                  self.$input.typeahead('val', '');
              }, self));
          }

          self.$container.on('click', $.proxy(function (event) {
              if (!self.$element.attr('disabled')) {
                  self.$input.removeAttr('disabled');
              }
              self.$input.focus();
          }, self));

          if (self.options.addOnBlur && self.options.freeInput) {
              self.$input.on('focusout', $.proxy(function (event) {
                  // HACK: only process on focusout when no typeahead opened, to
                  //       avoid adding the typeahead text as tag
                  if ($('.typeahead, .twitter-typeahead', self.$container).length === 0) {
                      self.add(self.$input.val());
                      self.$input.val('');
                  }
              }, self));
          }


          self.$container.on('keydown', 'input', $.proxy(function (event) {
              var $input = $(event.target),
                  $inputWrapper = self.findInputWrapper();

              if (self.$element.attr('disabled')) {
                  self.$input.attr('disabled', 'disabled');
                  return;
              }


              switch (event.which) {
                  // BACKSPACE
                  case 8:
                      if (doGetCaretPosition($input[0]) === 0) {
                          var prev = $inputWrapper.prev();
                          if (prev) {
                              self.remove(prev.data('item'));
                          }
                      }
                      break;

                      // DELETE
                  case 46:
                      if (doGetCaretPosition($input[0]) === 0) {
                          var next = $inputWrapper.next();
                          if (next) {
                              self.remove(next.data('item'));
                          }
                      }
                      break;

                      // LEFT ARROW
                  case 37:
                      // Try to move the input before the previous tag
                      var $prevTag = $inputWrapper.prev();
                      if ($input.val().length === 0 && $prevTag[0]) {
                          $prevTag.before($inputWrapper);
                          $input.focus();
                      }
                      break;
                      // RIGHT ARROW
                  case 39:
                      // Try to move the input after the next tag
                      var $nextTag = $inputWrapper.next();
                      if ($input.val().length === 0 && $nextTag[0]) {
                          $nextTag.after($inputWrapper);
                          $input.focus();
                      }
                      break;
                  case 13:
                      self.add($input.val());
                      $input.val('');
                      event.preventDefault();
                      break;
                  default:
                      // ignore
              }

              // Reset internal input's size
              var textLength = $input.val().length,
                  wordSpace = Math.ceil(textLength / 5),
                  size = textLength + wordSpace + 1;
              $input.attr('size', Math.max(this.inputSize, $input.val().length));
          }, self));

          self.$container.on('keypress', 'input', $.proxy(function (event) {
              var $input = $(event.target);

              if (self.$element.attr('disabled')) {
                  self.$input.attr('disabled', 'disabled');
                  return;
              }

              var text = $input.val(),
                  maxLengthReached = self.options.maxChars && text.length >= self.options.maxChars;
              if (self.options.freeInput && (keyCombinationInList(event, self.options.confirmKeys) || maxLengthReached)) {
                  self.add(maxLengthReached ? text.substr(0, self.options.maxChars) : text);
                  $input.val('');
                  event.preventDefault();
              }

              // Reset internal input's size
              var textLength = $input.val().length,
                  wordSpace = Math.ceil(textLength / 5),
                  size = textLength + wordSpace + 1;
              $input.attr('size', Math.max(this.inputSize, $input.val().length));
          }, self));

          // Remove icon clicked
          self.$container.on('click', '[data-role=remove]', $.proxy(function (event) {
              if (self.$element.attr('disabled')) {
                  return;
              }
              self.remove($(event.target).closest('.tag').data('item'));
          }, self));

          // Only add existing value as tags when using strings as tags
          if (self.options.itemValue === defaultOptions.itemValue) {
              if (self.$element[0].tagName === 'INPUT') {
                  self.add(self.$element.val());
              } else {
                  $('option', self.$element).each(function () {
                      self.add($(this).attr('value'), true);
                  });
              }
          }
      },

      /**
       * Removes all tagsinput behaviour and unregsiter all event handlers
       */
      destroy: function () {
          var self = this;

          // Unbind events
          self.$container.off('keypress', 'input');
          self.$container.off('click', '[role=remove]');

          self.$container.remove();
          self.$element.removeData('tagsinput');
          self.$element.show();
      },

      /**
       * Sets focus on the tagsinput
       */
      focus: function () {
          this.$input.focus();
      },

      /**
       * Returns the internal input element
       */
      input: function () {
          return this.$input;
      },

      /**
       * Returns the element which is wrapped around the internal input. This
       * is normally the $container, but typeahead.js moves the $input element.
       */
      findInputWrapper: function () {
          var elt = this.$input[0],
              container = this.$container[0];
          while (elt && elt.parentNode !== container)
          elt = elt.parentNode;

          return $(elt);
      }
  };

  /**
   * Register JQuery plugin
   */
  $.fn.tagsinput = function (arg1, arg2, arg3) {
      var results = [];

      this.each(function () {
          var tagsinput = $(this).data('tagsinput');
          // Initialize a new tags input
          if (!tagsinput) {
              tagsinput = new TagsInput(this, arg1);
              $(this).data('tagsinput', tagsinput);
              results.push(tagsinput);

              if (this.tagName === 'SELECT') {
                  $('option', $(this)).attr('selected', 'selected');
              }

              // Init tags from $(this).val()
              $(this).val($(this).val());
          } else if (!arg1 && !arg2) {
              // tagsinput already exists
              // no function, trying to init
              results.push(tagsinput);
          } else if (tagsinput[arg1] !== undefined) {
              // Invoke function on existing tags input
              if (tagsinput[arg1].length === 3 && arg3 !== undefined) {
                  var retVal = tagsinput[arg1](arg2, null, arg3);
              } else {
                  var retVal = tagsinput[arg1](arg2);
              }
              if (retVal !== undefined) results.push(retVal);
          }
      });

      if (typeof arg1 == 'string') {
          // Return the results from the invoked function calls
          return results.length > 1 ? results : results[0];
      } else {
          return results;
      }
  };

  $.fn.tagsinput.Constructor = TagsInput;

  /**
   * Most options support both a string or number as well as a function as
   * option value. This function makes sure that the option with the given
   * key in the given options is wrapped in a function
   */
  function makeOptionItemFunction(options, key) {
      if (typeof options[key] !== 'function') {
          var propertyName = options[key];
          options[key] = function (item) {
              return item[propertyName];
          };
      }
  }

  function makeOptionFunction(options, key) {
      if (typeof options[key] !== 'function') {
          var value = options[key];
          options[key] = function () {
              return value;
          };
      }
  }
  /**
   * HtmlEncodes the given value
   */
  var htmlEncodeContainer = $('<div />');

  function htmlEncode(value) {
      if (value) {
          return htmlEncodeContainer.text(value).html();
      } else {
          return '';
      }
  }

  /**
   * Returns the position of the caret in the given input field
   * http://flightschool.acylt.com/devnotes/caret-position-woes/
   */
  function doGetCaretPosition(oField) {
      var iCaretPos = 0;
      if (document.selection) {
          oField.focus();
          var oSel = document.selection.createRange();
          oSel.moveStart('character', -oField.value.length);
          iCaretPos = oSel.text.length;
      } else if (oField.selectionStart || oField.selectionStart == '0') {
          iCaretPos = oField.selectionStart;
      }
      return (iCaretPos);
  }

  /**
   * Returns boolean indicates whether user has pressed an expected key combination.
   * @param object keyPressEvent: JavaScript event object, refer
   *     http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
   * @param object lookupList: expected key combinations, as in:
   *     [13, {which: 188, shiftKey: true}]
   */
  function keyCombinationInList(keyPressEvent, lookupList) {
      var found = false;
      $.each(lookupList, function (index, keyCombination) {
          if (typeof (keyCombination) === 'number' && keyPressEvent.which === keyCombination) {
              found = true;
              return false;
          }

          if (keyPressEvent.which === keyCombination.which) {
              var alt = !keyCombination.hasOwnProperty('altKey') || keyPressEvent.altKey === keyCombination.altKey,
                  shift = !keyCombination.hasOwnProperty('shiftKey') || keyPressEvent.shiftKey === keyCombination.shiftKey,
                  ctrl = !keyCombination.hasOwnProperty('ctrlKey') || keyPressEvent.ctrlKey === keyCombination.ctrlKey;
              if (alt && shift && ctrl) {
                  found = true;
                  return false;
              }
          }
      });

      return found;
  }

  /**
   * Initialize tagsinput behaviour on inputs and selects which have
   * data-role=tagsinput
   */
  $(function () {
      $("input[data-role=tagsinput]").tagsinput();
  });
})(window.jQuery);

$('.tagsinput').tagsinput({
  allowDuplicates: false,
  trimValue: true,
  onTagExists: function (item, $tag) {
      alert('Tag already exists')
  }
});

    </script>
<script>
    // tagtab
    const lista = document.getElementById('tags-list');

    lista.addEventListener('click', (e) => {
        if (e.target && e.target.tagName === 'A') {
            // copyToClipboard
            if (e.target.classList.contains('copyToClipboard')) {
                let creative_id = e.target.classList.value
                    .split(' ')
                    .find(c => c.match('creative-'))
                    .split('-')[1];
                
                let textarea = document.getElementById(`script-${creative_id}`);
                textarea.select();
                document.execCommand("copy");
            }

            // download
            if (e.target.classList.contains('download')) {
                let creative_id = e.target.classList.value
                .split(' ')
                .find(c => c.match('creative-'))
                .split('-')[1];

                let textarea = document.getElementById(`script-${creative_id}`);
                let creative_name = textarea.getAttribute('name')
                saveTextAsFile(textarea.value, creative_name);
            }
        }
    })

    function saveTextAsFile(textToWrite, fileNameToSaveAs)
    {
        var textFileAsBlob = new Blob([textToWrite], {type:'text/plain'});
        var downloadLink = document.createElement("a");
        downloadLink.download = fileNameToSaveAs;
        downloadLink.innerHTML = "Download File";
        if (window.webkitURL != null)
        {
            // Chrome allows the link to be clicked
            // without actually adding it to the DOM.
            downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
        }
        else
        {
            // Firefox requires the link to be added to the DOM
            // before it can be clicked.
            downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
            downloadLink.onclick = destroyClickedElement;
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
        }

        downloadLink.click();
    }
</script>
@stop
<style>
.inputdisabled {
    background-color: #ccc;
}
</style>