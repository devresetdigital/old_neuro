@extends('voyager::master')
@section('content')
<div class="page-content">
        @include('voyager::alerts')

        <div class="analytics-container">
            <div class="tab-content">
                <button type="button" class="btn btn-primary" onclick="document.location.href='/admin/reports'">Old view</button>
                <iframe id="mainiframe" width="100%" height="1800" src="{{$iframe_new}}" frameborder="0"></iframe>
            </div>
        </div>
</div>
@stop