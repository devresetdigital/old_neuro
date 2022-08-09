@php $action = new $action($dataType, $data); @endphp
@php
    //if($_ENV['WL_PREFIX']==1){ $_ENV['WL_PREFIX']=2; }
    if($_ENV['WL_PREFIX'] !="" || $_ENV['WL_PREFIX'] !="0"){
        $float_wlprefix = $_ENV['WL_PREFIX'].".0";
        $wlprefix = (float) $float_wlprefix*1000000;
    } else {
        $wlprefix=0;
    }
@endphp
@if ($action->shouldActionDisplayOnDataType())
    @can($action->getPolicy(), $data)
        @php $idprefixed = $wlprefix+intval($data["id"]); @endphp
        <a href="{{ $action->getTitle()=="View" ? "reports?campaign_id=".$idprefixed : $action->getRoute($dataType->name) }}" title="{{ $action->getTitle() }}" {!! $action->convertAttributesToHtml() !!}>
            <i class="{{ $action->getIcon() }}"></i> <span class="hidden-xs hidden-sm">{{ $action->getTitle()=="View" ? 'Reports' : $action->getTitle() }}</span>
        </a>
    @endcan
@endif