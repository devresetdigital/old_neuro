@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/macros.css') }}">
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
    @if($dataTypeContent->concept != null)
    <li class="active">
        <a href="/admin/concepts/{{$dataTypeContent->concept->id}}/edit">{{$dataTypeContent->concept->name}}</a>
    </li>
    @endif
    <li class="active">
        <a href="/admin/creatives">Creatives</a>
    </li>
    <li>Edit</li>
</ol>
@stop
@if(!is_null($dataTypeContent->getKey()))
    @if($dataTypeContent->creative_type_id == 1)
        {{ $_GET["display"]=""  }}
    @endif
    @if($dataTypeContent->creative_type_id == 2)
        {{ $_GET["video"]=""  }}
    @endif
    @if($dataTypeContent->creative_type_id == 3)
        {{ $_GET["audio"]=""  }}
    @endif
@endif
@php 
    $is_admin = (Auth::user()->role_id == 1) ? true :false;
@endphp
@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    @if(is_null($dataTypeContent->getKey()) && (!isset($_GET["display"]) && !isset($_GET["video"]) && !isset($_GET["audio"])))
                        <script> document.location.href = document.location.href+"?display"; </script>
                       <!-- <div style="width: 100%; margin-left: 100px;"><div><label>Creative Type</label><select id="creativetype"><option value="1">Display</option><option value="2">Video</option></select> </div></div> -->
                    @endif
                    @if(isset($_GET["display"]) || isset($_GET["video"]) || isset($_GET["audio"]))
                    <!-- form start -->
                    <form role="form"
                            id="creatieveForm"
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
                                   
                                    @if($row->field=="start_date")
                                        <div class="form-group col-md-2"><label>Duration:</label><br><input type="checkbox" value="1" name="date_unlimited" id="date_unlimited"> Unlimited</div>
                                    @endif
                                    <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width or 12 }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                        {{ $row->slugify }}
                                        
                                        @if($row->field=="ad_serving_cost")
                                            @if($is_admin)
                                                <label for="name">{{ $row->display_name }}</label>
                                            @endif
                                        @else
                                            <label for="name">{{ $row->display_name }}</label>
                                        @endif
                                        
                                        @include('voyager::multilingual.input-hidden-bread-edit-add')
                                        @if($row->type == 'relationship')
                                            @include('voyager::formfields.relationship')
                                        @else

                                            @if($row->field=="start_date" || $row->field=="end_date")
                                                @if($row->field=="start_date")
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                @else
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                @endif
                                            @elseif($row->field=="advertiser_id")
                                                <select class="form-control select2 select2-hidden-accessible" name="advertiser_id"  id="advertiser_id_field" tabindex="-1" aria-hidden="true">
                                                        
                                                        @foreach($advertisers as $advertiser)
                                                        <option  {{($dataTypeContent->advertiser_id == $advertiser->id) ? 'selected' : ''}}
                                                            value="{{$advertiser->id}}">{{$advertiser->name}}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($row->field=="concept_id")
                                                <select class="form-control select2 select2-hidden-accessible" name="concept_id" id="concept_id_field" tabindex="-1" aria-hidden="true">
                                                        <option value="">None</option>
                                                </select>
                                            @elseif($row->field=="ad_serving_cost" )
                                                @if($is_admin)
                                                    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                @endif
                                            @elseif($row->field=="status")
                                                <br>
                                                <?php 
                                                $canEditThisAd = true;
                                                ?>
                                                @if($_ENV['ENABLE_TMT_SCAN'] == 1 && $dataTypeContent->TrustScan != null && $dataTypeContent->TrustScan->status == 'INCIDENT')                                            
                                                <span class="label label-danger" >Blocked</span>
                                                <?php 
                                                    $canEditThisAd = false;
                                                ?>
                                                @else
                                                
                                                    @if(isset($dataTypeContent->{$row->field}) || old($row->field))
                                                            <?php  $checked = old($row->field, $dataTypeContent->{$row->field});?>
                                                    @else
                                                        <?php $checked = isset($options->checked) && $options->checked ? true : false; ?>
                                                    @endif
                                                        <input type="checkbox" name="{{ $row->field }}" class="toggleswitch"
                                                            data-on="Active" {!! $checked ? 'checked="checked"' : '' !!} 
                                                            data-off="Inactive">
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
                        @if(isset($_GET["display"]) || $dataTypeContent->creative_type_id==1)
                         <div class="panel-body" >
                             <h4>Display Options:</h4>
                            <div class="row" style="border: 1px solid #e4eaec; margin: 0; padding: 0.8em;">
                           <!-- <div class="col-md-4">
                                MIME Type:<br>
                                <select class="form-control select2 select2-hidden-accessible" name="mime_type" tabindex="-1" aria-hidden="true">
                                    <option {{ isset($creativeDisplay->mime_type) ==5 ? 'selected="selected"' : ''  }} value="5">UNKNOWN</option>
                                    <option {{ isset($creativeDisplay->mime_type) ==1 ? 'selected="selected"' : ''  }} value="1">GIF</option>
                                    <option {{ isset($creativeDisplay->mime_type) ==2 ? 'selected="selected"' : ''  }} value="2">HTML5</option>
                                    <option {{ isset($creativeDisplay->mime_type) ==3 ? 'selected="selected"' : ''  }} value="3">SWF</option>
                                    <option {{ isset($creativeDisplay->mime_type) ==4 ? 'selected="selected"' : ''  }} value="4">JPG</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                MRAID:<br>
                                <input type="checkbox" name="mraid_required" class="toggleswitch" data-on="Yes" 
                                value="1" data-off="No" 
                                {{ isset($creativeDisplay->mraid_required) && $creativeDisplay->mraid_required ==1 ? 'checked' : ''  }}>
                            </div>
                            <div class="col-md-4">
                                Tag Type:<br>
                                <select class="form-control select2 select2-hidden-accessible" name="tag_type" tabindex="-1" aria-hidden="true">
                                    <option {{ isset($creativeDisplay->tag_type) ==1 ? 'selected="selected"' : ''  }} value="1">Javascript</option>
                                    <option {{ isset($creativeDisplay->tag_type) ==2 ? 'selected="selected"' : ''  }} value="2">IFRAME</option>
                                    <option {{ isset($creativeDisplay->tag_type) ==3 ? 'selected="selected"' : ''  }} value="3">Image</option>
                                </select>
                            </div> 
                            <div class="col-md-6">
                                Ad Format:<br>
                                <select class="form-control select2 select2-hidden-accessible" name="ad_format" tabindex="-1" aria-hidden="true">
                                    <option {{ isset($creativeDisplay->ad_format) ==1 ? 'selected="selected"' : ''  }} value="1">Display</option>
                                    <option {{ isset($creativeDisplay->ad_format) ==2 ? 'selected="selected"' : ''  }} value="2">Mobile</option>
                                    <option {{ isset($creativeDisplay->ad_format) ==3 ? 'selected="selected"' : ''  }} value="3">Expandable</option>
                                </select>
                            </div>-->
                            <div class="col-md-6">
                                Size:<br>
                                Width: &nbsp;<input name="ad_width" id="ad_width" placeholder="width" style="width: 100px;" value="{{ isset($creativeDisplay->ad_width) ? $creativeDisplay->ad_width : ''  }}">&nbsp;&nbsp; Height: &nbsp; <input name="ad_height" id="ad_height" placeholder="height" style="width: 100px;" value="{{ isset($creativeDisplay->ad_height) ? $creativeDisplay->ad_height : ''  }}">
                            </div>
                            <div class="col-md-12">
                            Tag Code:<br>
                            <!-- id="sweetalert_macros" -->
                                <span style="cursor: pointer; color: rebeccapurple" data-toggle="modal" data-target="#macrosModal">{Available Macros}</span><br>
                                <textarea id="tag_code" name="tag_code" style="width: 100%; height: 10em;">{{ isset($creativeDisplay->tag_code) ? $creativeDisplay->tag_code : ''  }}</textarea>
                                <br><button class="btn btn-primary" type="button" onclick="previewAd();">Preview Markup</button>
                            </div>
                            <div class="col-md-12">
                                3rd Party Tracking Code:<br>
                                <textarea name="3rd_tracking" style="width: 100%; height: 10em;">{{ isset($creativeDisplay->{'3rd_tracking'}) ? $creativeDisplay->{'3rd_tracking'} : ''  }}</textarea>
                            </div>
                            <div class="col-md-12">
                                Creative Attributes:<br>
                                <ul style="list-style-type: none; border: 1px solid #e4eaec; margin: 0; padding: 0.8em;">
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',1) ? 'checked' : ''  }} value="1" name="creative_attributes[]"> 1 - Audio Ad (Auto-Play)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',2) ? 'checked' : ''  }} value="2" name="creative_attributes[]"> 2 - Audio Ad (User Initiated)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',3) ? 'checked' : ''  }} value="3" name="creative_attributes[]"> 3 - Expandable (Automatic)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',4) ? 'checked' : ''  }} value="4" name="creative_attributes[]"> 4 - Expandable (User Initiated - Click)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',5) ? 'checked' : ''  }} value="5" name="creative_attributes[]"> 5 - Expandable (User Initiated - Rollover)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',6) ? 'checked' : ''  }} value="6" name="creative_attributes[]"> 6 - In-Banner Video Ad (Auto-Play)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',7) ? 'checked' : ''  }} value="7" name="creative_attributes[]"> 7 - In-Banner Video Ad (User Initiated)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',8) ? 'checked' : ''  }} value="8" name="creative_attributes[]"> 8 - Pop (e.g., Over, Under, or Upon Exit)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',9) ? 'checked' : ''  }} value="9" name="creative_attributes[]"> 9 - Provocative or Suggestive Imagery</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',10) ? 'checked' : ''  }} value="10" name="creative_attributes[]"> 10 - Shaky, Flashing, Flickering, Extreme Animation, Smileys</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',11) ? 'checked' : ''  }} value="11" name="creative_attributes[]"> 11 - Surveys</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',12) ? 'checked' : ''  }} value="12" name="creative_attributes[]"> 12 - Text Only</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',13) ? 'checked' : ''  }} value="13" name="creative_attributes[]"> 13 - User Interactive (e.g., Embedded Games)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',14) ? 'checked' : ''  }} value="14" name="creative_attributes[]"> 14 - Windows Dialog or Alert Style</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',15) ? 'checked' : ''  }} value="15" name="creative_attributes[]"> 15 - Has Audio On/Off Button</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',16) ? 'checked' : ''  }} value="16" name="creative_attributes[]"> 16 - Ad Provides Skip Button (e.g. VPAID-rendered skip button on pre-roll video)</li>
                                    <li><input type="checkbox" {{ $thisCreativeAttributes->contains('attribute_id',17) ? 'checked' : ''  }} value="17" name="creative_attributes[]"> 17 - Adobe Flash</li>

                                </ul>
                            </div>
                            </div> 
                          
                         </div>

                        @endif
                        @if(isset($_GET["video"]) || $dataTypeContent->creative_type_id==2)
                            <div class="panel-body">
                                <div style="margin-left:20px">
                                    <!-- <select class="select2 select2-hidden-accessible" name="type" id="type_field" style="width: 50%">                    
                                        <option value="tag">Tag</option>
                                        <option value="url">URL</option>
                                        <option value="template">Template</option>
                                    </select> -->
                                    VAST XML / VAST URL:<br>
                                    <textarea id="tag_code" name="vast_code" style="width: 70%; height: 150px;">{{ isset($creativeVideo->vast_code) ? $creativeVideo->vast_code : ''  }}</textarea>
                                    <br><button class="btn btn-primary" type="button" onclick="previewVast();">Preview Ad</button>
                                    <br>
                                    <div style="width: 70%; display:flex; justify-content: space-between;">
                                        <div>
                                            <input type="checkbox" name="skippable" value="1" {{ isset($creativeVideo->skippable) && $creativeVideo->skippable==1 ? 'checked' : ''  }}> 
                                            <label>Skippable</label>
                                        </div>
                                        <div>
                                            <label for="duration">Duration</label>
                                            <input type="number" name="duration" value="15" min="5" max="180" style="width: 100px;">
                                            <label for="bitrate">Bitrate (bts)</label>
                                            <input type="number" name="bitrate" value="700" min="32" max="1800" style="width: 100px;">
                                        </div>
                                    </div>
                                    <br><br>
                                </div>
                            </div>
                        @endif
                        @if(isset($_GET["audio"]) || $dataTypeContent->creative_type_id==3)
                            <div class="panel-body">
                                <div style="margin-left:20px">
                                    <div class="form-group">
                                        <label for="exampleFormControlFile1">Audio File</label>
                                        <br>
                                        @if(!is_null($dataTypeContent->getKey()))
                                            <audio controls>
                                                <source src="/storage/audios/{{$dataTypeContent->getKey()}}-audio.mp3" type="audio/mpeg">
                                            </audio>
                                        @endif
                                        <input type="file" class="form-control-file" id="audio_file" name="audio_file">
                                    </div><br><br>
                                </div>
                            </div>
                        @endif
                        <div class="panel-footer">
                            @if($canEditThisAd)
                                <button type="submit" id="savebutton" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @else
                                <a href="/admin/creatives" class="btn btn-primary">Menu</a>
                            @endif
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
                    @endif
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
    <div class="modal modal_multi" id="vastPreview">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-info"></i>Vast Preview</h4>
                </div>
                <div class="modal-body">
                    <a href="#" onclick="stop(1)" class="btn btn-default"><i class="glyphicon glyphicon-stop"></i></a>
                    <a href="#" onclick="play(1)" class="btn btn-default"><i class="glyphicon glyphicon-play"></i></a>
                    <a href="#" onclick="pause(1)" class="btn btn-default"><i class="glyphicon glyphicon-pause"></i></a>
                    <div  id="container_1"></div> 
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->

    <!-- Macros Modal -->
    @php
    $macros = [
        'amas_ip' => ['meaning' => 'test', 'description' => 'IP DONDE SALIO LA CREATIVIDAD'],
        'amas_country' => ['meaning' => '', 'description' => 'PAIS DONDE SALIO LA CREATIVIDAD'],    
        'amas_region' => ['meaning' => '', 'description' => 'REGION DONDE SALIO LA CREATIVIDAD'],    
        'amas_city' => ['meaning' => '', 'description' => 'CIUDAD  DONDE SALIO LA CREATIVIDAD'],    
        'amas_zipcode' => ['meaning' => '', 'description' => 'ZIP CODE DONDE SALIO LA CREATIVIDAD'],    
        'amas_isp' => ['meaning' => '', 'description' => 'ISP (servicio de internet) DONDE SALIO LA CREATIVIDAD'],    
        'amas_gps_lat' => ['meaning' => '', 'description' => 'Latitud'],    
        'amas_gps_long' => ['meaning' => '', 'description' => 'Longitud'],    
        'amas_organization' => ['meaning' => '', 'description' => 'Organization ids a la que pertenece la campaña'],    
        'amas_advertiser' => ['meaning' => '', 'description' => 'id del advertizer'],    
        'amas_campaign' => ['meaning' => '', 'description' => 'Id de campaña'],    
        'amas_concept' => ['meaning' => '', 'description' => 'Id del concepto'],    
        'amas_creative' => ['meaning' => '', 'description' => 'Id de la creatividad'],    
        'amas_creative_width' => ['meaning' => '', 'description' => 'Ancho de la creatividad'],    
        'amas_creative_height' => ['meaning' => '', 'description' => 'Alto de la creatividad'],
        'amas_creative_size' => ['meaning' => '', 'description' => 'Ancho x Alto de la creatividad'],
        'amas_creative_type' => ['meaning' => '', 'description' => 'Typo de creatividad(script, vast, banner, etc)'],
        'amas_ssp' => ['meaning' => '', 'description' => 'Nombre del spp donde salio la creatividad'],
        'amas_seat' => ['meaning' => '', 'description' => 'Seat id de la auction con el ssp'],
        'amas_pmp' => ['meaning' => '', 'description' => 'Si fue por deal id, el id de ese deal'],
        'amas_publisher' => ['meaning' => '', 'description' => 'id del publisher'],
        'amas_site' => ['meaning' => '', 'description' => 'Domain donde salio el ad'],
        'amas_app_name' => ['meaning' => '', 'description' => 'App name donde salio el ad'],
        'amas_app_domain' => ['meaning' => '', 'description' => 'Bundle de la app'],
        'amas_app_id' => ['meaning' => '', 'description' => 'no remplazamos'],
        'amas_app_site' => ['meaning' => '', 'description' => 'no remplazamos'],
        'amas_device_ifa' => ['meaning' => '', 'description' => 'ifa del dispositivo que salio el ad'],
        'amas_bidprice' => ['meaning' => '', 'description' => 'el precio del bid'],
        'amas_winprice' => ['meaning' => '', 'description' => 'el precio que gano el auction'],
        'amas_price' => ['meaning' => '', 'description' => 'Precio en cpm'],
        'amas_device_browser' => ['meaning' => '', 'description' => 'navegador donde salio el ad'],
        'amas_device_os' => ['meaning' => '', 'description' => 'sistema operativo donde salio el ad'],
        'amas_device_type' => ['meaning' => '', 'description' => 'el tipo de dispositivo donde salio el ad'],
        'amas_click_redirect_unencoded:redirectUrl' => ['meaning' => '', 'description' =>  'No encodeado, URL de nuestro evento click para que sea ejecutado por el navegador cuando corresponda, pero con una url aclarada despues de ":" para ser llamado una vez que nuestro click es ejecutado'],
        'amas_click_redirect_encoded:redirectUrl' =>['meaning' => '', 'description' =>  'Mismo que arriba pero encodeado'],
        'amas_click_redirect_doubleencoded:redirectUrl' => ['meaning' => '', 'description' =>  'Mismo quea rriba pero doblemente encodeado'],
        'amas_click_url_unencoded' => ['meaning' => '', 'description' => 'URL de nuestro evento click para que sea ejecutado por el navegador cuando corresponda.(Sin encodear)'],
        'amas_click_url_encoded' => ['meaning' => '', 'description' => 'Lo mismo que el otro pero encodeado'],
        'amas_roi_url_unencoded:000' => ['meaning' => '', 'description' =>  'URL no encodeada para contar ROI con el valor que viene despues de ":"'],
        'amas_roi_url_encoded:000' => ['meaning' => '', 'description' =>  'Lo mismo que arriba pero encodeada'],
        'amas_customevent1_url_unencoded:000' => ['meaning' => '', 'description' =>  'Lo mismo que roi pero para custom event, pasando valores que se necesiten. No encodeado'],
        'amas_customevent1_click_url_encoded:000' => ['meaning' => '', 'description' =>  'Mismo que anterior pero encodeado'],
        'amas_customevent2_url_unencoded:000' => ['meaning' => '', 'description' =>  'Mismo que el de arriba pero para otro custom'],
        'amas_customevent2_click_url_encoded:000' => ['meaning' => '', 'description' =>  'sdfsas'],
        'cachebuster' => ['meaning' => '', 'description' => 'UnixTime seconds'],
        'randomnumber' => ['meaning' => '', 'description' => 'UnixTme seconds']
    ];
    @endphp
    <div class="modal fade modal_multi" id="macrosModal">
        <div class="modal-dialog" style="width: 55%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-info"></i>List of Macros</h4>
                </div>
                <div class="modal-body">
                    <div class="summary">
                        @foreach ($macros as $key => $macro)
                        <dl>   
                            <dt>{{'{'.$key.'}'}}</dt>
                            <dd style="font-style:italic;">{{$macro['meaning']}}</dd>
                            <dd>{{$macro['description']}}</dd>
                        </dl>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('javascript')
<script>
        $("#type_field").select2({
            width: '50%' // need to override the changed default
        }).on('change', function(e) {
            let selected = $('#type_field')
                .select2('data')
                .map((d) => d.id)[0];
            console.log(selected)
        });

        @if(isset($dataTypeContent->concept_id))
            let concept_id = {{$dataTypeContent->concept_id}}; 
        @else
            let concept_id = ''; 
        @endif
        const loadConcepts = async (id) => {
            let params = {
                    id:    id,
                    _token: '{{ csrf_token() }}'
            }
            await $.post("/api/concepts_by_advertiser/", params, function (data) {
                $('#concept_id_field').empty();
                let html = '<option value="">None</option>';
                for (const iterator of data) {
                    console.log(iterator.id)
                        html += `<option ${iterator.id== concept_id ? 'selected': '' } value="${iterator.id}">${iterator.name}</option>`;
                }
                $('#concept_id_field').append(html);
            });
        }
        var params = {}
        var $image
        $('document').ready(function () {
            
            document.getElementById("savebutton").addEventListener("click", function(event){
                event.preventDefault();
                if (validateRequiredFields()){
                    $('#creatieveForm').submit();
                }else{
                    return false;
                }
            });
            $('#advertiser_id_field').change(function (e) {
                loadConcepts($(this).val());
            });
            $("#advertiser_id_field").trigger("change");
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
            //ADD Behavior to voyager inputs
            @if(isset($_GET["display"])){ $("[name=creative_type_id]" ).val(1).trigger('change'); } @endif
            @if(isset($_GET["video"])){ $("[name=creative_type_id]" ).val(2).trigger('change'); } @endif
                @if(isset($_GET["audio"])){ $("[name=creative_type_id]" ).val(3).trigger('change'); } @endif
            @if (is_null($dataTypeContent->getKey()))
            $( "[name=creative_type_id]" ).change(function() {
                if($( "[name=creative_type_id]" ).val()==1){ document.location.href="/admin/creatives/create?display" };
                if($( "[name=creative_type_id]" ).val()==2){ document.location.href="/admin/creatives/create?video" };
                if($( "[name=creative_type_id]" ).val()==3){ document.location.href="/admin/creatives/create?audio" };
            });
            @endif
            $("#date_unlimited").change(function() {
                $('input[name$="start_date"]').val('');
                $('input[name$="end_date"]').val('');
                if(this.checked) {
                    $('input[name$="start_date"]').prop('disabled', true);
                    $('input[name$="end_date"]').prop('disabled', true);
                }else{
                    $('input[name$="start_date"]').prop('disabled', false);
                    $('input[name$="end_date"]').prop('disabled', false);
                }
            });
            if($('input[name$="start_date"]').val()=="" && $('input[name$="end_date"]').val()==""){
                $("#date_unlimited").prop("checked", true)
                $("#date_unlimited").trigger("change");
            }
            
        });
        const validateRequiredFields = () => {
            
            let message='';
            let error = false;
            let nameLenght = $('input[name="name"]').val().length;
            if (nameLenght <= 0 || nameLenght >= 256 ){
                message +='<p>[DETAILS] please verify field "name" it must contain 1-256 characters</p>';
                error=true;
            }

            let duration = $('input[name="duration"]').val();
            let bitrate = $('input[name="bitrate"]').val();

            if (duration < 5 || duration > 180 ) {
                message +='<p>[DETAILS] please verify field "duration" it must be between 5 to 180</p>';
                error=true;
            }
            if (bitrate < 5 || bitrate > 1800 ) {
                message +='<p>[DETAILS] please verify field "Bitrate" it must be between 32 to 1800</p>';
                error=true;
            }
            let date_start =  $('input[name ="start_date"]').val();
            let date_end =  $('input[name ="end_date"]').val();
            if (date_start=='' && !$('#date_unlimited:checkbox:checked').length){
                message +='<p>Please complete start date</p>';
                error=true;
            }
            if (date_end=='' && !$('#date_unlimited:checkbox:checked').length){
                message +='<p>Please complete end date</p>';
                error=true;
            }
            let d1 = new Date(date_start);
            let d2 = new Date(date_end);
            if(d1.getTime() > d2.getTime()){
                message +='<p>Please verify dates (start date is greater than end date)</p>';
                error=true;
            }
            if(error){
                $('#displayError').empty();
                $('#displayError').append(message);
                $('#displayError').show();
                setTimeout(() => {
                    $('#displayError').hide();
                }, 7000);
                return false;
            }
            return true;
        }
        function previewAd() {
            var w = window.open('','Ad Preview','"height='+document.getElementById('ad_height').value+',width='+document.getElementById('ad_width').value+'"');
            var html = $("#tag_code").val();
            w.document.write(html);
            w.resizeTo(parseInt(document.getElementById('ad_width').value)+60, parseInt(document.getElementById('ad_height').value)+100);
        }



        /**
         * 
         * vast preview markup
         */
        var pause1=false;
        let player1=null;
        const play = (id) =>{
            if(window['pause'+id] == false ){
                switch (id) {
                    case 1:
                        player1.startAd();
                        break;
                }
            }else{
                switch (id) {
                    case 1:
                        pause1=false;
                        player1.resumeAd();
                        break;
                }
            }
            
        }
        const stop = (id) =>{
            previewVast();
        }
        const pause = (id) =>{
            switch (id) {
                case 1:
                    pause1=true;
                    player1.pauseAd();
                    break;
            }
        }
        let previewVast = async () => {
            $('#container_1').empty();
            let key=null;
            await $.post("/api/save_vast_markup",
            {
                markup: $('#tag_code').val()
            },function(data){
                key = data;
                return data;
            },'json');
          
            player1 = new window.VASTPlayer(document.getElementById('container_1'));
            player1.load('/api/vast_preview/'+key);
            $('#vastPreview').modal('show');
            return true;
        }

        $('#vastPreview').on('hidden.bs.modal', function () {
            $('#container_1').empty();
        })

       
    </script>
    <script src="https://cdn.jsdelivr.net/npm/vast-player@0.2/dist/vast-player.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/sweetalert2.js"></script>
@stop
