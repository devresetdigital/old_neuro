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

                                @elseif($row->field == "impression_range")
                                <div class="form-group  col-md-12">
                                    <label for="name">Attribution Windows</label>
                                    <div style="border: 1px solid #e4eaec;  margin: 0; padding-bottom: 6em; padding-top: 1em;">
                                        <div class="form-group  col-md-5">
                                            <label for="name">Impressions Viewthrough (Days)</label>
                                            <input type="number" class="form-control" name="impression_range" step="any" placeholder="Impressions Viewthrough (Days)" 
                                            value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif">
                                        </div>
                                        <div class="form-group  col-md-5">
                                            <label for="name">Clickthrough (Days)</label>
                                            <input type="number" class="form-control" name="click_range" step="any" placeholder="Clickthrough (Days)" 
                                            value="@if(isset($dataTypeContent->click_range)){{ old($row->field, $dataTypeContent->click_range) }}@else{{old('click_range')}}@endif">
                                        </div>
                                    </div> 
                                </div>
                                @elseif($row->field == "click_range") 
                                @else
                                    <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width or 12 }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                        {{ $row->slugify }}
                                        <label for="name">{{ $row->display_name }}</label>
                                        @include('voyager::multilingual.input-hidden-bread-edit-add')
                                        @if($row->type == 'relationship')
                                          
                                            @if($row->display_name == "Campaigns" )
                                            @php
                                                $selected_values = isset($dataTypeContent) ? $dataTypeContent->belongsToMany($options->model, $options->pivot_table)->pluck($options->table.'.'.$options->key)->all() : array();
                                            @endphp
                                            <select class="form-control select2 select2-hidden-accessible" name="conversion_pixel_belongsto_campaign_relationship[]" multiple="" tabindex="-1" aria-hidden="true">
                                                    @foreach($campaigns as $campaign)
                                                    <option  @if(in_array($campaign->id, $selected_values)){{ 'selected="selected"' }} @endif 
                                                        value="{{$campaign->id}}">{{$campaign->name}}</option>
                                                    @endforeach
                                            </select>
                                          
                                            @elseif($row->display_name == "Smart pixel" )
                                            
                                            <select class="form-control select2 select2-hidden-accessible" name="smart_pixel_id"  id="smart_pixel_id" tabindex="-1" aria-hidden="true">
                                                    @foreach($pixels as $pixel)
                                                    <option  {{($dataTypeContent->smart_pixel_id == $pixel->id) ? 'selected' : ''}}
                                                        value="{{$pixel->id}}">{{$pixel->name}}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                @include('voyager::formfields.relationship')
                                            @endif
                                            
                                        @else
                                            {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                        @endif

                                        @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                            {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                        @endforeach
                                    </div>
                                @endif
                            
                            @endforeach
                        </div><!-- panel-body -->
                        <div>
                            @php
                                $aurl = explode(".",$_SERVER["HTTP_HOST"]);
                                $cpurl = count($aurl);
                                $domain = $aurl[$cpurl-2].".".$aurl[$cpurl-1];
                            @endphp
                            @if(false && !is_null($dataTypeContent->getKey()))
                            <div class="container"><h3 class="panel-title">Pixel Code</h3><textarea id="pixel_code" style="width: 80%">&#x3C;script src=&#x22;https://meta.{{ $domain }}/Scripts/smart.js?px={{ $dataTypeContent->id  }}&amp;cid=0003-{{ $dataTypeContent->campaign_id  }}&amp;md=cpa&#x22;&#x3E;&#x3C;/script&#x3E;</textarea><br>
                                <button type="button" class="btn btn-default" onclick="copyToClipboard();">Copy to Clipboard</button> <button type="button" class="btn btn-default" onclick="downloadPixel('{{ str_slug($dataTypeContent->name,'-') }}',document.getElementById('pixel_code').value);">Download</button> <button type="button" class="btn btn-default" onclick="document.location.href='/admin/pixel_conversion?pixelid={{ $dataTypeContent->id }}'">View Reports</button>
                            </div>
                            @endif
                        </div>
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
    <script>
        var deleteFormAction;
        $('.delete').on('click', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) { // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');
            console.log(form.action);

            $('#delete_modal').modal('show');
        });
        function copyToClipboard() {
            /* Get the text field */
            var copyText = document.getElementById("pixel_code");

            /* Select the text field */
            copyText.select();

            /* Copy the text inside the text field */
            document.execCommand("copy");

            /* Alert the copied text */
            //alert("Copied the text: " + copyText.value);
        }
        function downloadPixel(filename, text) {
            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);
        }
    </script>
@stop
