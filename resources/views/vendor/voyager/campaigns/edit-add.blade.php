@extends('voyager::master')

@section('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('voyager::compass.includes.styles')
@stop

@section('page_title', __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
    @php
        if($_ENV['WL_PREFIX'] !="" || $_ENV['WL_PREFIX'] !="0"){
            $float_wlprefix = $_ENV['WL_PREFIX'].".0";
            $wlprefix = (float) $float_wlprefix*1000000;
        } else {
            $wlprefix=0;
        }
    @endphp
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
    </h1>
    @if(!is_null($dataTypeContent->getKey()))
        @php $idprefixed = $wlprefix+intval($dataTypeContent->getKey()); @endphp
    @endif
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content compass container-fluid">
        @if(!is_null($dataTypeContent->getKey()))
        <ul class="nav nav-tabs">
            <li {!! 'class="active"' !!}><a data-toggle="tab" href="#resources"><i class="voyager-book"></i> Summary</a></li>
            <li><a data-toggle="tab" onclick="document.location.href='{{ route('voyager.strategies_campaign.index', $dataTypeContent->getKey()) }}'" href="#"><i class="voyager-lab"></i> Strategies</a></li>
            <!--<li ><a data-toggle="tab" onclick="document.location.href='/admin/vwireports?campaign_id={{ $idprefixed  }}'" href="#"><i class="voyager-people"></i> VWI</a></li>-->
            <li><a data-toggle="tab" onclick="document.location.href='/admin/creports?campaign_id={{ $dataTypeContent->getKey()  }}'" href="#commands"><i class="voyager-bar-chart"></i> Reports</a></li>
            <!--<li ><a data-toggle="tab" href="#conversions"><i class="voyager-check"></i> Conversions</a></li>-->
     
            @if(str_contains($_SERVER['SERVER_NAME'], 'inspire.com'))
                @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 5)
                    <li><a href='/admin/reach_frequency?campaign_id={{ $idprefixed  }}'><i class="voyager-activity"></i>Reach & Frecuency</a></li>
                @endif
            @else
            <li ><a href='/admin/reach_frequency?campaign_id={{ $idprefixed  }}'><i class="voyager-activity"></i>Reach & Frecuency</a></li>
            @endif
            <li ><a href='/admin/special_reports?campaign_id={{ $idprefixed  }}' ><i class="voyager-list"></i>Audience Report</a></li>
        </ul>
        @endif
        <div class="tab-content">
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
                                    <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width or 12 }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif
                                    @if($row->field == "goal_type_id" || $row->field == "goal_v1" || $row->field == "pacing_monetary" || $row->field == "pacing_impression")
                                    style="display: none;"
                                    @endif
                                    >
                                        {{ $row->slugify }}
                                        @if($row->display_name == 'Users')
                                        @else
                                        <label for="name">{{ $row->display_name }}</label>
                                        @endif
                                        @include('voyager::multilingual.input-hidden-bread-edit-add')
                                        @if($row->type == 'relationship')
                                            @if($row->display_name == "Users")
                                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                            @else
                                                @if($row->display_name == "Advertisers" && (Auth::user()->role_id ==2 || Auth::user()->role_id ==3) )
                                                    <br><select name="advertiser_id">
                                                        @foreach($organization_advertisers as $advertiser)
                                                            <option value="{{$advertiser->id}}" @if(!is_null($dataTypeContent->getKey())) @if($advertiser->id == $dataTypeContent->advertiser_id) selected @endif  @endif>{{$advertiser->name}}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                @include('voyager::formfields.relationship')
                                                @endif
                                            @endif
                                        @else
                                            @if($row->field == "pacing_monetary" || $row->field == "pacing_impression")
                                                @php
                                                    $pacing_monetary_values = explode(",",$dataTypeContent->pacing_monetary);
                                                    $pacing_impression_values = explode(",",$dataTypeContent->pacing_impression);
                                                @endphp
                                                @if($row->field == "pacing_monetary")
                                                    <br><select id="m_type" name="m_type" onchange="changePacingMonetary()">
                                                        <option {{ isset($pacing_monetary_values[0]) && $pacing_monetary_values[0] == 1 ? 'selected' : '' }} value="1">ASAP</option>
                                                        <option {{ isset($pacing_monetary_values[0]) && $pacing_monetary_values[0] == 2 ? 'selected' : '' }} value="2">EVEN</option>
                                                        <option {{ isset($pacing_monetary_values[0]) && $pacing_monetary_values[0] == 3 ? 'selected' : '' }} value="3">NoCap</option>
                                                    </select>
                                                    <select id="m_stype" name="m_stype" onchange="changePacingMonetary()">
                                                        <option {{ isset($pacing_monetary_values[1]) && $pacing_monetary_values[1] == 1 ? 'selected' : '' }} value="1">Auto</option>
                                                        <option {{ isset($pacing_monetary_values[1]) && $pacing_monetary_values[1] == 2 ? 'selected' : '' }} value="2">Manual</option>
                                                    </select>
                                                    <input id="m_amount" name="m_amount" type="text" style="width:100px" onkeyup="changePacingMonetary()" value="{{ isset($pacing_monetary_values[2]) ? $pacing_monetary_values[2] : '' }}"> per day
                                                    <input type="hidden" id="pacing_monetary" name="pacing_monetary" value="{{ $dataTypeContent->pacing_monetary!="" ? $dataTypeContent->pacing_monetary : ""  }}">
                                                @else
                                                    <br><select id="i_type" name="i_type" onchange="changePacingImpression()">
                                                        <option {{ isset($pacing_impression_values[0]) && $pacing_impression_values[0] == 1 ? 'selected' : '' }} value="1">ASAP</option>
                                                        <option {{ isset($pacing_impression_values[0]) && $pacing_impression_values[0] == 2 ? 'selected' : '' }} value="2">EVEN</option>
                                                        <option {{ isset($pacing_impression_values[0]) && $pacing_impression_values[0] == 3 ? 'selected' : '' }} value="3">NoCap</option>
                                                    </select>
                                                    <select id="i_stype" name="i_stype" onchange="changePacingImpression()">
                                                        <option {{ isset($pacing_impression_values[1]) && $pacing_impression_values[1] == 1 ? 'selected' : '' }} value="1">Auto</option>
                                                        <option {{ isset($pacing_impression_values[1]) && $pacing_impression_values[1] == 2 ? 'selected' : '' }} value="2">Manual</option>
                                                    </select>
                                                    <input id="i_amount" name="i_amount" type="text" style="width:100px" onkeyup="changePacingImpression()" value="{{ isset($pacing_impression_values[2]) ? $pacing_impression_values[2] : '' }}"> per day
                                                    <input type="hidden" id="pacing_impression" name="pacing_impression" value="{{ $dataTypeContent->pacing_impression!="" ? $dataTypeContent->pacing_impression : ""  }}">
                                                @endif
                                            @else
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                            @endif
                                        @endif

                                        @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                            {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div><!-- panel-body -->
                        <div class="container">
                            <div class="row">
                                <input type="hidden" name="count" value="1" />
                                <div class="control-group" id="fields">
                                    <label class="control-label" for="field1">Budget Flights</label><br>
                                    <small>Press + to add another flight</small>
                                    <div class="controls" id="profs">
                                        @php $cf=1; @endphp
                                        @if(!isset($campaignFlights) || count($campaignFlights)==0)
                                            <div id="field" class="field">Start: <input class="input date" id="flight_sdate1" name="flight_sdate[]" type="text" placeholder="Start Date"> End: <input type="text" class="input date" id="flight_edate1" name="flight_edate[]" placeholder="End Date"> $&nbsp;<input autocomplete="off" class="input" id="flight_monetary1" name="flight_monetary[]" type="text" placeholder="Monetary Budget"/> <input autocomplete="off" class="input" id="flight_impression1" name="flight_impression[]" type="text" step="1" placeholder="Impression Budget"/><button id="b1" class="btn add-more" type="button">+</button></div>
                                        @else
                                            @foreach($campaignFlights as $flight)
                                            <div id="field" class="field"> Start: <input class="input date" id="flight_sdate{{$cf}}" name="flight_sdate[]" type="text" placeholder="Start Date" value="{{ date("m-d-Y",strtotime($flight["date_start"])) }}"> End: <input type="text" class="input date" id="flight_edate{{$cf}}" name="flight_edate[]" value="{{ date("m-d-Y",strtotime($flight["date_end"])) }}" placeholder="End Date"> $&nbsp;<input autocomplete="off" class="input" id="flight_monetary{{$cf}}" name="flight_monetary[]" type="text" placeholder="Monetary Budget" value="{{ round($flight["budget"],2)  }}"/> <input autocomplete="off" class="input" id="flight_impression{{$cf}}" name="flight_impression[]" type="text" step="1" placeholder="Impression Budget" value="{{ $flight["impression"]  }}"/><button id="remove{{$cf}}" class="btn btn-danger remove-me" >-</button></div><div id="field"></div>
                                                @php $cf++; @endphp
                                            @endforeach
                                            <div id="field" class="field"> Start: <input class="input date" id="flight_sdate{{$cf}}" name="flight_sdate[]" type="text" placeholder="Start Date"> End: <input type="text" class="input date" id="flight_edate{{$cf}}" name="flight_edate[]" placeholder="End Date"> $&nbsp;<input autocomplete="off" class="input" id="flight_monetary{{$cf}}" name="flight_monetary[]" type="text" placeholder="Monetary Budget"/> <input autocomplete="off" class="input" id="flight_impression{{$cf}}" name="flight_impression[]"  step="1"  type="text" placeholder="Impression Budget"/><button id="b1" class="btn add-more" type="button">+</button></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-bordered" style="display: none">
                            <div class="panel-body" style="margin-left: 20px;">
                        Attribution:<br>
                           <!-- <label for="name">Verified Walk In:</label>
                            <select class="form-control select2" name="vwis[]" multiple>
                                @foreach($vwis_list as $vwi)
                                    <option {{ in_array($vwi->id,$selected_vwis) ? 'selected' : ''  }}  value="{{$vwi->id}}" >[{{$vwi->id}}] {{$vwi->name}}</option>
                                @endforeach
                            </select> -->
                            <label for="name">Verified Walk In:</label>
                            <select class="form-control select2" name="vwi_locations[]" multiple>
                                @foreach($vwi_locations as $vwi_location)
                                    <option {{ in_array($vwi_location->id,$selected_vwi_locations) ? 'selected' : ''  }}  value="{{$vwi_location->id}}" >{{$vwi_location->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            <a href="{{ route('voyager.'.$dataType->slug.'.index') }}"><button type="button" class="btn btn-danger">{{ __('voyager::generic.cancel') }}</button></a>
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

                <a class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
    <script>
        var params = {}
        var $image

        $('document').ready(function () {
            $(".date").datepicker({
               dateFormat: "mm-dd-yy"
            });

            $( ".date" ).datepicker( "option", "dateFormat", "mm-dd-yy" );
            
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
        //Budget Flights
        $(document).ready(function(){
            var next = {{$cf}};

            $(".add-more").click(function(e){
                e.preventDefault();
                var addto = "#flight_impression" + next;
                var addRemove = "#flight_impression" + (next);
                next = next + 1;
                var newIn = `<div id="field${next}">Start: <input class="input date" type="text" id="flight_sdate${next}" name="flight_sdate[]" placeholder="Start Date"> End: <input class="input date" type="text" id="flight_edate${next}" name="flight_edate[]" placeholder="End Date"> $&nbsp;<input autocomplete="off" class="input" id="flight_monetary${next}" name="flight_monetary[]" type="text" placeholder="Monetary Budget"/> <input autocomplete="off" class="input" id="flight_impression${next}" name="flight_impression[]" type="text" step="1" placeholder="Impression Budget"/><button id="remove${next}" class="btn btn-danger remove-me" >-</button></div></div>`;
                var newInput = $(newIn);
                $(".field").last().after(newInput);
                $("#flight_impression" + next).attr('data-source',$(addto).attr('data-source'));

                $('.remove-me').click(function(e){
                    e.preventDefault();
                    $(this).parent().remove();
                });
            });

            $('.remove-me').click(function(e){
                e.preventDefault();
                $(this).parent().remove();
            });
        });
        //update pacing // impressions

            $('#pacing_monetary').val($('#m_type').val()+","+$('#m_stype').val()+","+$('#m_amount').val());
            function changePacingMonetary(){
                $('#pacing_monetary').val($('#m_type').val()+","+$('#m_stype').val()+","+$('#m_amount').val());
            }

        //$('#pacing_impression').val($('#i_type').val()+","+$('#i_stype').val()+","+$('#i_amount').val());
        function changePacingImpression(){
            $('#pacing_impression').val($('#i_type').val()+","+$('#i_stype').val()+","+$('#i_amount').val());
        }

    </script>
@stop
