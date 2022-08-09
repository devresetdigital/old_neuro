@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/macros.css') }}">
    <link rel="stylesheet" href="{{ asset('css/creatives.css') }}">
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
                                        <div class="form-group col-md-2">
                                            <label>Date range:</label>
                                            <!-- <br> -->
                                            <div class="form-check">
                                                <label class="form-check-label" for="date_unlimited">
                                                    Unlimited
                                                </label>
                                                <input class="form-check-input" type="checkbox" value="1" name="date_unlimited" id="date_unlimited">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width or 12 }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                        {{ $row->slugify }}
                                        <label for="name">{{ $row->display_name }}</label>
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
                                   
                                    <div class="col-md-6">
                                        Size:<br>
                                        Width: &nbsp;<input name="ad_width" id="ad_width" placeholder="width" style="width: 100px;" value="{{ isset($creativeDisplay->ad_width) ? $creativeDisplay->ad_width : ''  }}">&nbsp;&nbsp; Height: &nbsp; <input name="ad_height" id="ad_height" placeholder="height" style="width: 100px;" value="{{ isset($creativeDisplay->ad_height) ? $creativeDisplay->ad_height : ''  }}">
                                    </div>
                                    <div class="col-md-12">
                                    Tag Code:<br>
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
                            @endif
                            @if(isset($_GET["video"]) || $dataTypeContent->creative_type_id==2)
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <div class="form-check">
                                                <label class="form-check-label">Skippable</label>
                                                <select class="" name="skippable" id="skippable" style="width: 200px">                    
                                                    <option value="1" {{ isset($creativeVideo->skippable) && $creativeVideo->skippable==1 ? 'selected' : ''  }}>Yes</option>
                                                    <option value="0" {{ isset($creativeVideo->skippable) && $creativeVideo->skippable==0? 'selected' : ''  }}>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                                <label for="duration">Duration</label>
                                                <input type="number" name="duration" id="duration" value="{{ isset($creativeVideo->duration) ? $creativeVideo->duration : 15 }}" min="5" max="180" style="width: 100px;">
                                                <label for="bitrate" style="margin-left: 20px;">Bitrate (kbts)</label>
                                                <input type="number" name="bitrate" id="bitrate" value="{{ isset($creativeVideo->bitrate) ? $creativeVideo->bitrate : 700 }}" min="32" max="1800" style="width: 100px;">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="name">VAST Type:</label>
                                        <select class="" name="vast_type" id="type_field" style="width: 50%">                    
                                            <option value="script" {{ isset($creativeVideo->vast_type) && $creativeVideo->vast_type==='script' ? 'selected' : ''  }}>VAST Script</option>
                                            <option value="url" {{ isset($creativeVideo->vast_type) && $creativeVideo->vast_type==='url' ? 'selected' : ''  }}>VAST URL</option>
                                            <option value="form" {{ isset($creativeVideo->vast_type) && $creativeVideo->vast_type==='form' ? 'selected' : ''  }}>VAST Form</option>
                                        </select>
                                        <!-- VAST XML / VAST URL:<br> -->
                                        <div id="vast_types" style="position:relative; margin-top:15px;">
                                            <textarea id="tag_code" name="vast_code" style="width: 70%; height: 300px; {{ $dataTypeContent->vast_type === 'script' ? 'visibility:visible;' : 'visibility:hidden;'  }} top:15px;">{{ isset($creativeVideo->vast_code) ? $creativeVideo->vast_code : ''  }}</textarea>
                                            <input id="url_code"style="position: absolute; {{ $dataTypeContent->vast_type === 'url' ? 'visibility:visible;' : 'visibility:hidden;' }} top:15px; left:0px; width:70%;" type="url" name="url" placeholder="https://example.com" pattern="https://.*" size="30">
                                            <div id="template_code" style="position:absolute; {{ $dataTypeContent->vast_type === 'form' ? 'visibility:visible;' : 'visibility:hidden;' }} top:15px; left:0px; width: 100%;">
                                                <div class="testbox col-md-6">
                                                    <!-- form -->
                                                
                                                    <form class="form" action="/">
                                                        <div class="item">
                                                            <label>Vastversion</label>
                                                            <select name="vastversion" id="vastversion" required>
                                                                <option value="2.0">2.0</option>
                                                                <option value="3.0">3.0</option>
                                                                <option value="4.0">4.0</option>
                                                            </select>
                                                            <!-- <input id="vastversion" type="text" name="vastversion" required> -->
                                                        </div>
                                                        <div class="item">
                                                            <label>Title</label>
                                                            <input id="title" type="text" name="title" required style="width:79%;">
                                                        </div>
                                                        <div class="item">
                                                            <label>Media URL</label>
                                                            <input id="mediaurl" type="url" name="mediaurl" placeholder="URL" required>
                                                        </div>
                                                        <div class="item">
                                                            <label>Dimensions</label>
                                                            <input id="height" type="number" name="height" placeholder="Height" required>
                                                            <input id="width" type="number" name="width" placeholder="Width" required>
                                                        </div>
                                                        <p>Tracking Events</p>
                                                        <div class="tracking">
                                                            <div class="item">
                                                                <label>Impression Event</label>
                                                                <input id="impressionevent" type="text" name="impressionevent" placeholder="URL" required>
                                                            </div>
                                                            <div class="item">
                                                                <label>Start</label>
                                                                <input id="start" type="url" name="start" placeholder="URL" required>
                                                            </div>
                                                            <div class="item">
                                                                <label>First Quartile</label>
                                                                <input id="firstQuartile" type="url" name="firstQuartile" placeholder="URL" required>
                                                            </div>
                                                            <div class="item">
                                                                <label>Mid Point</label>
                                                                <input id="midpoint" type="url" name="midpoint" placeholder="URL" required>
                                                            </div>
                                                            <div class="item">
                                                                <label>Third Quartile</label>
                                                                <input id="thirdQuartile" type="url" name="thirdQuartile" placeholder="URL" required>
                                                            </div>
                                                            <div class="item">
                                                                <label>Complete</label>
                                                                <input id="complete" type="url" name="complete" placeholder="URL" required>
                                                            </div>
                                                        </div>
                                                    </form>

                                                </div>
                                                <div class="xml col-md-6">
                                                @if (isset($creativeVideo->vast_type) && $creativeVideo->vast_type === 'form')
                                                    <textarea name="form_vast_code" id="template_xml" style="width: 100%; height: 430px;" readonly >{{ $creativeVideo->vast_code }}</textarea>
                                                @else
                                                    <textarea name="form_vast_code" id="template_xml" style="width: 100%; height: 430px;" readonly ><VAST xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="{VASTVERSION}"><Ad id="{ADID}"><InLine><AdSystem>{VASTVERSION}</AdSystem><AdTitle>{TITLE}</AdTitle><Creatives><Creative><Linear><Duration>15</Duration><VideoClicks><ClickThrough id="clic"><![CDATA[{CLICKTHROUGHURL}]]></ClickThrough></VideoClicks><MediaFiles><MediaFile height="{HEIGHT}" width="{WIDTH}" bitrate="700" type="video/mp4" delivery="progressive"><![CDATA[{MEDIAURL}]]></MediaFile></MediaFiles></Linear></Creative></Creatives></InLine></Ad></VAST></textarea>
                                                @endif
                                                    <div>
                                                    <a href="#" id="copyToClipboard" class="btn btn-primary"
                                                        role="button">Copy to clipboard</a>
                                                    <a href="#" id="download" class="btn btn-secondary"
                                                        role="button">Download</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br><button class="btn btn-primary" type="button" onclick="previewVast();">Preview Ad</button>
                                        <br>
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
                    <span id="vast_error" style="display:none; width:100%; display:block;" class="alert alert-warning"></span>
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
        'amas_ip' => ['meaning' => 'test', 'description' => 'Creativity IP'],
        'amas_country' => ['meaning' => '', 'description' => 'Creativity country'],    
        'amas_region' => ['meaning' => '', 'description' => 'Creativity region'],    
        'amas_city' => ['meaning' => '', 'description' => 'Creativity city'],    
        'amas_zipcode' => ['meaning' => '', 'description' => 'Creativity zip code'],    
        'amas_isp' => ['meaning' => '', 'description' => 'Creativity ISP'],    
        'amas_gps_lat' => ['meaning' => '', 'description' => 'Latitude'],    
        'amas_gps_long' => ['meaning' => '', 'description' => 'Longitude'],    
        'amas_organization' => ['meaning' => '', 'description' => 'Id of the organization to which the campaign belongs'],    
        'amas_advertiser' => ['meaning' => '', 'description' => 'Advertizer Id'],    
        'amas_campaign' => ['meaning' => '', 'description' => 'Campaign Id'],    
        'amas_concept' => ['meaning' => '', 'description' => 'Concept Id'],    
        'amas_creative' => ['meaning' => '', 'description' => 'Creativity Id'],    
        'amas_creative_width' => ['meaning' => '', 'description' => 'Creativity width'],    
        'amas_creative_height' => ['meaning' => '', 'description' => 'Creativity height'],
        'amas_creative_size' => ['meaning' => '', 'description' => 'Creativity size (width x height)'],
        'amas_creative_type' => ['meaning' => '', 'description' => 'Creativity type (script, vast, banner, etc)'],
        'amas_ssp' => ['meaning' => '', 'description' => 'Name of the spp where the creativity came from'],
        'amas_seat' => ['meaning' => '', 'description' => 'Seat id of the auction with the ssp'],
        'amas_pmp' => ['meaning' => '', 'description' => 'If it was by deal id, the id of that deal'],
        'amas_publisher' => ['meaning' => '', 'description' => 'Publisher ID'],
        'amas_site' => ['meaning' => '', 'description' => 'Ad domain'],
        'amas_app_name' => ['meaning' => '', 'description' => 'Ad App name'],
        'amas_app_domain' => ['meaning' => '', 'description' => 'App bundle'],
        'amas_app_id' => ['meaning' => '', 'description' => 'No replace'],
        'amas_app_site' => ['meaning' => '', 'description' => 'No replace'],
        'amas_device_ifa' => ['meaning' => '', 'description' => 'IFA of the device that came out the ad'],
        'amas_bidprice' => ['meaning' => '', 'description' => 'Bid price'],
        'amas_winprice' => ['meaning' => '', 'description' => 'Price that won the auction'],
        'amas_price' => ['meaning' => '', 'description' => 'CPM price'],
        'amas_device_browser' => ['meaning' => '', 'description' => 'Ad browser'],
        'amas_device_os' => ['meaning' => '', 'description' => 'Ad operating system'],
        'amas_device_type' => ['meaning' => '', 'description' => 'Ad device'],
        'amas_click_redirect_unencoded:redirectUrl' => ['meaning' => '', 'description' =>  'Unencoded URL of our redirect click event to be executed by the browser when appropriate : URL'],
        'amas_click_redirect_encoded:redirectUrl' =>['meaning' => '', 'description' =>  'Encoded URL of our redirect click event to be executed by the browser when appropriate: URL'],
        'amas_click_redirect_doubleencoded:redirectUrl' => ['meaning' => '', 'description' =>  'Double encoded URL of our redirect click event to be executed by the browser when appropriate : URL'],
        'amas_click_url_unencoded' => ['meaning' => '', 'description' => 'Unencoded URL of our click event to be executed by the browser when appropriate'],
        'amas_click_url_encoded' => ['meaning' => '', 'description' => 'Encoded URL of our click event to be executed by the browser when appropriate'],
        'amas_roi_url_unencoded:000' => ['meaning' => '', 'description' =>  'Unencoded ROI URL : ROI URL'],
        'amas_roi_url_encoded:000' => ['meaning' => '', 'description' =>  'Encoded ROI URL : ROI URL'],
        'amas_customevent1_url_unencoded:000' => ['meaning' => '', 'description' =>  'URL not encoded for custom event'],
        'amas_customevent1_click_url_encoded:000' => ['meaning' => '', 'description' =>  'URL encoded for custom event'],
        'amas_customevent2_url_unencoded:000' => ['meaning' => '', 'description' =>  'URL not encoded for custom event'],
        'amas_customevent2_click_url_encoded:000' => ['meaning' => '', 'description' =>  'URL encoded for custom event'],
        'cachebuster' => ['meaning' => '', 'description' => 'UnixTime seconds'],
        'randomnumber' => ['meaning' => '', 'description' => 'UnixTme seconds']
    ];
    @endphp
    <div class="modal fade modal_multi" id="macrosModal">
        <div class="modal-dialog" style="width: 76%;">
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
    
    let type_selected = $('#type_field').val();
    var text, xmlDoc, parser, xmlText, xmlTextNode, xmlDocument;

    xmlDoc = $.parseXML($('#template_xml').val());
    parser = new DOMParser();

    const showScriptOption = () => {
        document.getElementById("tag_code").style.visibility="visible";
        document.getElementById("url_code").style.visibility="hidden";
        document.getElementById("template_code").style.visibility="hidden";

        document.getElementById("vast_types").style.height = "300px";
    }
    const showURLOption = () => {
        document.getElementById("tag_code").style.visibility="hidden";
        document.getElementById("url_code").style.visibility="visible";
        document.getElementById("template_code").style.visibility="hidden";

        document.getElementById("vast_types").style.height = "50px";
    }
    const showFormOption = () => {
        document.getElementById("tag_code").style.visibility="hidden";
        document.getElementById("url_code").style.visibility="hidden";
        document.getElementById("template_code").style.visibility="visible";
        document.getElementById("vast_types").style.height = "450px";

        $(xmlDoc).find("ClickThrough")[0].childNodes[0].nodeValue = $("input[name='click_url']").val()
        $(xmlDoc).find("AdSystem")[0].childNodes[0].nodeValue = $('#vastversion').val()
        $(xmlDoc).find("VAST")[0].attributes['version'].nodeValue = $('#vastversion').val()
        $(xmlDoc).find("Ad")[0].attributes['id'].nodeValue = "{{ isset($dataTypeContent->id) ? $dataTypeContent->id : "{ADID}" }}"
        writeXML()
        loadXMLData()
    }

    const writeXML = () => {
        document.getElementById("template_xml").value = new XMLSerializer().serializeToString(xmlDoc);
    }

    if (type_selected === 'script') {
        showScriptOption()
    }
    if (type_selected === 'url') {
        showURLOption()
    }
    if (type_selected === 'form') {
        showFormOption();
    }

    $('#type_field').change(function(e) {
        type_selected = $('#type_field').val();

        if (type_selected === 'script') {
            showScriptOption()
        }
        if (type_selected === 'url') {
            showURLOption()
        }
        if (type_selected === 'form') {
            showFormOption();
        }
    });
        
        $('#vastversion').change(function (e) {
            $(xmlDoc).find("AdSystem")[0].childNodes[0].nodeValue = $('#vastversion').val()
            $(xmlDoc).find("VAST")[0].attributes['version'].nodeValue = $('#vastversion').val()
            writeXML()
        })
        $('#title').change(function (e) {
            $(xmlDoc).find("AdTitle")[0].childNodes[0].nodeValue = $('#title').val()
            writeXML()
        })
        $('#impressionevent').change(function (e) {
            if (!$(xmlDoc).find("Impression").length) {
                let x = $(xmlDoc).find("InLine")[0];
                let cdata = xmlDoc.createCDATASection($('#impressionevent').val());
                let impression = xmlDoc.createElement("Impression");
                let title = $(xmlDoc).find("Creatives")[0];
                x.insertBefore(impression,title);
                $(xmlDoc).find("Impression")[0].setAttribute("id", "impression")
                $(xmlDoc).find("Impression")[0].append(cdata);

                writeXML()
            } else {
                $(xmlDoc).find("Impression")[0].childNodes[0].data = $('#impressionevent').val()
                writeXML()
            }
        })
        $('input[name=click_url]').change(function (e) {
            $(xmlDoc).find("ClickThrough")[0].childNodes[0].nodeValue = $('input[name=click_url]').val()
            writeXML()
        })
        $('#mediaurl').change(function (e) {
            $(xmlDoc).find("MediaFile")[0].childNodes[0].nodeValue = $('#mediaurl').val()
            writeXML()
        })
        $('#height').change(function (e) {
            $(xmlDoc).find("MediaFile")[0].attributes['height'].nodeValue = $('#height').val()
            writeXML()
        })
        $('#width').change(function (e) {
            $(xmlDoc).find("MediaFile")[0].attributes['width'].nodeValue = $('#width').val()
            writeXML()
        })
        $('#duration').change(function (e) {
            $(xmlDoc).find("Duration")[0].childNodes[0].nodeValue = $('#duration').val()
            writeXML()
        });
        $('#bitrate').change(function (e) {
            $(xmlDoc).find("MediaFile")[0].attributes['bitrate'].nodeValue = $('#bitrate').val()
            writeXML()
        });
        $('#start').change(function (e) {
            let prop = 'start';
            handleTrackingInput(prop);
        });
        $('#complete').change(function (e) {
            let prop = 'complete';
            handleTrackingInput(prop);
        });
        $('#firstQuartile').change(function (e) {
            let prop = 'firstQuartile';
            handleTrackingInput(prop);
        });
        $('#midpoint').change(function (e) {
            let prop = 'midpoint';
            handleTrackingInput(prop);
        });
        $('#thirdQuartile').change(function (e) {
            let prop = 'thirdQuartile';
            handleTrackingInput(prop);
        });


        $('#copyToClipboard').click(function (e) {
            e.preventDefault();
            let textarea = document.getElementById('template_xml');
            textarea.select();
            document.execCommand("copy");
        });

        $('#download').click(function (e) {
            e.preventDefault();
            let textarea = document.getElementById('template_xml');
            saveTextAsFile(textarea.value, 'vast.txt');
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

        const handleTrackingInput = (prop) => {
            let value =  $(`#${prop}`).val();

            if (!value && trackingExists(xmlDoc, prop)) {
                let x = $(xmlDoc).find(`Tracking[event=${prop}]`)[0];
                x.parentNode.removeChild(x);
                if (!$(xmlDoc).find("Tracking").length) {
                    let y = $(xmlDoc).find(`TrackingEvents`)[0];
                    y.parentNode.removeChild(y)
                }
                writeXML();
                return;
            }

            if (!$(xmlDoc).find("TrackingEvents").length) {
                let linear = $(xmlDoc).find("Linear")[0];
                let trackingEvents = xmlDoc.createElement("TrackingEvents");
                linear.appendChild(trackingEvents);
                
                trackingEvents = $(xmlDoc).find("TrackingEvents")[0];
                let tracking = xmlDoc.createElement("Tracking");
                trackingEvents.appendChild(tracking);
                $(xmlDoc).find("Tracking")[0].setAttribute("event", prop)
                $(xmlDoc).find("Tracking")[0].appendChild(xmlDoc.createTextNode(`![CDATA[${value}]]`));
            } else {
                if (!trackingExists(xmlDoc, prop) ) {
                    trackingEvents = $(xmlDoc).find("TrackingEvents")[0];
                    let tracking = xmlDoc.createElement("Tracking");
                    trackingEvents.appendChild(tracking);
                    $(xmlDoc).find("Tracking").last()[0].setAttribute("event", prop)
                    $(xmlDoc).find(`Tracking[event=${prop}]`)[0].appendChild(xmlDoc.createTextNode(`![CDATA[${value}]]`));
                } else {
                    $(xmlDoc).find(`Tracking[event=${prop}]`)[0].childNodes[0].nodeValue = `![CDATA[${value}]]`;
                }
            }
            writeXML()
        }

        function loadXMLData () {
            $('#vastversion').val($(xmlDoc).find("AdSystem")[0].childNodes[0].nodeValue)
            $('#title').val($(xmlDoc).find("AdTitle")[0].childNodes[0].nodeValue)
            $('#mediaurl').val($(xmlDoc).find("MediaFile")[0].childNodes[0].nodeValue)
            $('#height').val($(xmlDoc).find("MediaFile")[0].attributes['height'].nodeValue)
            $('#width').val($(xmlDoc).find("MediaFile")[0].attributes['width'].nodeValue)
            $('#duration').val($(xmlDoc).find("Duration")[0].childNodes[0].nodeValue)
            $('#bitrate').val($(xmlDoc).find("MediaFile")[0].attributes['bitrate'].nodeValue)
            $(xmlDoc).find("Impression")[0] ? $('#impressionevent').val($(xmlDoc).find("Impression")[0].childNodes[0].data) : null
            $(xmlDoc).find("Tracking[event=start]")[0] ? $('#start').val(getDataFromCDATA($(xmlDoc).find("Tracking[event=start]")[0].childNodes[0].nodeValue)) : null
            $(xmlDoc).find("Tracking[event=complete]")[0] ? $('#complete').val(getDataFromCDATA($(xmlDoc).find("Tracking[event=complete]")[0].childNodes[0].nodeValue)) : null
            $(xmlDoc).find("Tracking[event=firstQuartile]")[0] ? $('#firstQuartile').val(getDataFromCDATA($(xmlDoc).find("Tracking[event=firstQuartile]")[0].childNodes[0].nodeValue)) : null
            $(xmlDoc).find("Tracking[event=midpoint]")[0] ? $('#midpoint').val(getDataFromCDATA($(xmlDoc).find("Tracking[event=midpoint]")[0].childNodes[0].nodeValue)) : null
            $(xmlDoc).find("Tracking[event=thirdQuartile]")[0] ? $('#thirdQuartile').val(getDataFromCDATA($(xmlDoc).find("Tracking[event=thirdQuartile]")[0].childNodes[0].nodeValue)) : null
        }

        function getDataFromCDATA(value) {
            const regex = /\!\[CDATA\[(.*)\]\]/gm;
            let m, data;

            while ((m = regex.exec(value)) !== null) {
                // This is necessary to avoid infinite loops with zero-width matches
                if (m.index === regex.lastIndex) {
                    regex.lastIndex++;
                }
                
                // The result can be accessed through the `m`-variable.
                m.forEach((match, groupIndex) => {
                    if (groupIndex === 1) {
                        data = match
                    }
                });
            }
            return data;
        }

        const validateRequiredFields = () => {

            let message='';
            let error = false;
            let nameLenght = $('input[name="name"]').val().length;

            if (nameLenght <= 0 || nameLenght >= 256 ){
                message +='<p>[DETAILS] please verify field "name" it must contain 1-256 characters</p>';
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

        function trackingExists(xmlDoc, attr) {
            return $(xmlDoc).find(`Tracking[event=${attr}]`).length > 0;
        }

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
            $('#vast_error').hide();

            let type = $('#type_field').val();
            let error_message = null;

            if (type === 'script' || type === 'form') {
                let template = type === 'script' ? $('#tag_code').val() : $("#template_xml").val();
                let key=null;
                await $.post("/api/save_vast_markup",
                {
                    markup: template
                },function(data){
                    key = data;
                    return data;
                },'json');
              
                player1 = new window.VASTPlayer(document.getElementById('container_1'));
                try {
                    await player1.load('/api/vast_preview/'+key);
                } catch (error) {
                    error_message = "Malformed XML please check and try again"
                }
            } else {
                player1 = new window.VASTPlayer(document.getElementById('container_1'));
                try {
                    await player1.load($('#url_code').val());
                } catch (error) {
                    error_message = "Malformed URL please check and try again"
                }
            }
            if (error_message !== null) {
                $('#vast_error').empty();
                $('#vast_error').append(error_message);
                $('#vast_error').show();
            }
            $('#vastPreview').modal('show');
        }

        $('#vastPreview').on('hidden.bs.modal', function () {
            $('#container_1').empty();
        })
       
    </script>
    <script src="https://cdn.jsdelivr.net/npm/vast-player@0.2/dist/vast-player.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="http://dsp.resetdigital.co/dsp-demo/dsp-demo/assets/js/components/sweetalert2.js"></script>
@stop