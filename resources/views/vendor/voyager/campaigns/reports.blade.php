@extends('voyager::master')

@section('css')
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
@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li class="active">
        <a href="/admin"><i class="voyager-boat"></i> Dashboard</a>
    </li>
    <li class="active"><a href="/admin/campaigns">Campaigns</a></li>
    <li class="active"><a href="#">Creports</a></li>
</ol>

@stop
@section('content')
    <div class="page-content compass container-fluid">
        @if(!is_null($dataTypeContent->getKey()))
            <ul class="nav nav-tabs">
                <li {!! 'class="active"' !!}><a data-toggle="tab" href="#resources"><i class="voyager-book"></i> Summary</a></li>
                <li><a data-toggle="tab" onclick="document.location.href='{{ route('voyager.strategies_campaign.index', $dataTypeContent->getKey()) }}'" href="#"><i class="voyager-lab"></i> Strategies</a></li>
            <!--<li ><a data-toggle="tab" onclick="document.location.href='/admin/vwireports?campaign_id={{ $idprefixed  }}'" href="#"><i class="voyager-people"></i> VWI</a></li>-->
                <li><a data-toggle="tab" onclick="document.location.href='/admin/reports?campaign_id={{ $idprefixed  }}'" href="#commands"><i class="voyager-bar-chart"></i> Reports</a></li>
                <!--<li ><a data-toggle="tab" href="#conversions"><i class="voyager-check"></i> Conversions</a></li>-->
              
            </ul>
        @endif
        <div class="tab-content">
            <div class="row">
                <div class="col-md-12">
                    Reports
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
@stop
