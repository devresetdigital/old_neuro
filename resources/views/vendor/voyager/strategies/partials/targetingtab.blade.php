<div id="targeting" class="tab-pane fade in">
    @php
        $sitelists_inc_exc=3;
        if(isset($selected_sitelists[0])){
            $sitelists_inc_exc = $selected_sitelists[0]->inc_exc;
        }
    @endphp
    <label for="name"><b>Sitelist</b></label><br>
    <input class="inc_exc" data-input-id="sitelists" type="radio"  name="sitelists_inc_exc" value="3"  {{ (isset($sitelists_inc_exc) && $sitelists_inc_exc == 3) || !isset($sitelists_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="sitelists" {{ isset($sitelists_inc_exc) && $sitelists_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="sitelists_inc_exc" id="sitelists_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="sitelists" {{ isset($sitelists_inc_exc) && $sitelists_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="sitelists_inc_exc" id="sitelists_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="sitelists[]" multiple id="sitelists" {{ isset($sitelists_inc_exc) && $sitelists_inc_exc ==3 ? 'disabled="disabled"' : '' }} >
        @foreach($advertiser_sitelists as $sitelist)
            <option {{ $selected_sitelists->contains("sitelist_id",$sitelist->id) ? "selected" : ""  }}  value="{{$sitelist->id}}" >[{{$sitelist->id}}] {{$sitelist->name}}</option>
        @endforeach
    </select>
    <br><br>

    @php
        $publisherlists_inc_exc=3;
        if(isset($selected_publisherlists[0])){
            $publisherlists_inc_exc = $selected_publisherlists[0]->inc_exc;
        }
    @endphp
    <label for="name"><b>Publisherlist (Work in progress)</b></label><br>
    <input class="inc_exc" data-input-id="publisherlists" type="radio"  name="publisherlists_inc_exc" value="3"  {{ (isset($publisherlists_inc_exc) && $publisherlists_inc_exc == 3) || !isset($publisherlists_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off
    <input class="inc_exc" data-input-id="publisherlists" {{ isset($publisherlists_inc_exc) && $publisherlists_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="publisherlists_inc_exc" id="publisherlists_inc" value="1" > Include
    <input class="inc_exc" data-input-id="publisherlists" {{ isset($publisherlists_inc_exc) && $publisherlists_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="publisherlists_inc_exc" id="publisherlists_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="publisherlists[]" multiple id="publisherlists" {{ isset($publisherlists_inc_exc) && $publisherlists_inc_exc ==3 ? 'disabled="disabled"' : '' }} >
        @foreach($advertiser_publisherlists as $publisherlist)
            <option {{ $selected_publisherlists->contains("publisherlist_id",$publisherlist->id) ? "selected" : ""  }}  value="{{$publisherlist->id}}" >[{{$publisherlist->id}}] {{$publisherlist->name}}</option>
        @endforeach
    </select>
    <br><br>
    
    <label for="name"><b>IpList</b></label><br>
    @php
        $iplists_inc_exc=3;
        if(isset($selected_iplists[0])){
            $iplists_inc_exc = $selected_iplists[0]->inc_exc;
        }
    @endphp
    <input class="inc_exc" data-input-id="iplists" type="radio" name="iplists_inc_exc" value="3"  {{ (isset($iplists_inc_exc) && $iplists_inc_exc == 3) || !isset($iplists_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="iplists" {{ isset($iplists_inc_exc) && $iplists_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="iplists_inc_exc" id="iplists_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="iplists" {{ isset($iplists_inc_exc) && $iplists_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="iplists_inc_exc" id="iplists_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="iplists[]" multiple id="iplists" {{ (isset($iplists_inc_exc) && $iplists_inc_exc == 3) ? 'disabled="disabled"' : '' }} >
        @foreach($advertiser_iplists as $iplist)
            <option {{ $selected_iplists->contains("iplist_id",$iplist->id) ? "selected" : ""  }}  value="{{$iplist->id}}" >[{{$iplist->id}}] {{$iplist->name}}</option>
        @endforeach
    </select>
    <br><br>
    <label for="name"><b>PMPs</b></label><br>
    <select class="form-control select2" name="pmps[]" id="pmps" multiple >
        <option value="" {{  $selected_pmps[0]=="" ?  "selected" : '' }}>None</option>
        @foreach($organization_pmps as $pmp)
            <option {{ in_array($pmp->deal_id,$selected_pmps) ? 'selected' : ''  }}  value="{{$pmp->deal_id}}" >{{$pmp->name}}</option>
        @endforeach
    </select><br><input type="checkbox" id="open_market" name="open_market" value="1" {{ isset($pmps_open_market) && $pmps_open_market == 1 ? 'checked="checked"' : '' }}> Open Market<br><br>
    
    <label for="name"><b>SSPs</b></label><br>
    @php
        
        if($selected_ssps[0]!= ""){
            $ssps_inc_exc = 1;
        }else{
            $ssps_inc_exc = 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="ssps" type="radio" name="ssps_inc_exc" value="3"  {{ (isset($ssps_inc_exc) && $ssps_inc_exc == 3) || !isset($ssps_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="ssps" {{ isset($ssps_inc_exc) && $ssps_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="ssps_inc_exc" id="iplists_inc" value="1" > Include 
    <select class="form-control select2" name="ssps[]" multiple id="ssps" {{ (isset($ssps_inc_exc) && $ssps_inc_exc == 3) ? 'disabled="disabled"' : '' }}>
        @foreach($ssps as $ssp)
            <option {{ in_array($ssp->name,$selected_ssps) ? 'selected' : ''  }} value="{{$ssp->name}}" >{{$ssp->alias}}</option>
        @endforeach
    </select><br><a class="btn btn-info ssps" id="selectallssps" {{ (isset($ssps_inc_exc) && $ssps_inc_exc == 3) ? 'disabled="disabled"' : '' }} > Select All SSPs</a>
    <br>
    <br>
    <label for="name"><b>Zip List</b></label><br>
    @php
        $ziplists_inc_exc = 3; 
        if(isset($selected_ziplists[0])){
            $ziplists_inc_exc = $selected_ziplists[0]->inc_exc;
        }
    @endphp
    <input class="inc_exc" data-input-id="ziplists"  type="radio" name="ziplists_inc_exc" value="3"  {{ (isset($ziplists_inc_exc) && $ziplists_inc_exc == 3) || !isset($ziplists_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="ziplists" {{ isset($ziplists_inc_exc) && $ziplists_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="ziplists_inc_exc" id="ziplists_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="ziplists" {{ isset($ziplists_inc_exc) && $ziplists_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="ziplists_inc_exc" id="ziplists_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="ziplists[]" multiple id="ziplists"  {{ (isset($ziplists_inc_exc) && $ziplists_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        @foreach($advertiser_ziplists as $ziplist)
            <option {{ $selected_ziplists->contains("ziplist_id",$ziplist->id) ? "selected" : ""  }}  value="{{$ziplist->id}}" >[{{$ziplist->id}}] {{$ziplist->name}}</option>
        @endforeach
    </select>
    <br>
    <br>
    <label for="name"><b>Keywords List</b></label><br>
    @php
        $keywordslist_inc_exc = 3; 
        if(isset($selected_keywordslists[0])){
            $keywordslist_inc_exc = $selected_keywordslists[0]->inc_exc;
        }
    @endphp
    <input class="inc_exc" data-input-id="keywordslists"  type="radio" name="keywordslist_inc_exc" value="3"  {{ (isset($keywordslist_inc_exc) && $keywordslist_inc_exc == 3) || !isset($keywordslist_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="keywordslists" {{ isset($keywordslist_inc_exc) && $keywordslist_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="keywordslist_inc_exc" id="keywordslists_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="keywordslists" {{ isset($keywordslist_inc_exc) && $keywordslist_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="keywordslist_inc_exc" id="keywordslists_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="keywordslists[]" multiple id="keywordslists"  {{ (isset($keywordslist_inc_exc) && $keywordslist_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        @foreach($advertiser_keywordslist as $keywordslist)
            <option {{ $selected_keywordslists->contains("keywordslist_id",$keywordslist->id) ? "selected" : ""  }}  value="{{$keywordslist->id}}" >[{{$keywordslist->id}}] {{$keywordslist->name}}</option>
        @endforeach
    </select>
    <br>
    <br>
    <label for="name"><b>Device</b></label><br>
    @php
        if(count($selected_devices)> 0){
            $devices_inc_exc = 1;
        }else{
            $devices_inc_exc = 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="devices" type="radio" name="devices_inc_exc" value="3"  {{ (isset($devices_inc_exc) && $devices_inc_exc == 3) || !isset($devices_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="devices" {{ isset($devices_inc_exc) && $devices_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="devices_inc_exc" id="iplists_inc" value="1" > Include 
    <select class="form-control select2" name="device[]" id="devices" multiple {{ (isset($devices_inc_exc) && $devices_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        <option {{ in_array("1",$selected_devices) ? 'selected' : ''  }} value="1" >Windows Computer</option>
        <option {{ in_array("2",$selected_devices) ? 'selected' : ''  }} value="2" >Apple Computer</option>
        <option {{ in_array("3",$selected_devices) ? 'selected' : ''  }} value="3" >iPad</option>
        <option {{ in_array("4",$selected_devices) ? 'selected' : ''  }} value="4" >iPhone</option>
        <option {{ in_array("5",$selected_devices) ? 'selected' : ''  }} value="5" >iPod</option>
        <option {{ in_array("6",$selected_devices) ? 'selected' : ''  }} value="6" >Apple Device</option>
        <option {{ in_array("7",$selected_devices) ? 'selected' : ''  }} value="7" >Android Phone</option>
        <option {{ in_array("8",$selected_devices) ? 'selected' : ''  }} value="8" >Android Tablet</option>
        <option {{ in_array("9",$selected_devices) ? 'selected' : ''  }} value="9" >Other</option>
        <option {{ in_array("10",$selected_devices) ? 'selected' : ''  }} value="10" >Connected TV</option>
        <option {{ in_array("11",$selected_devices) ? 'selected' : ''  }} value="11" >OTT</option>
        <option {{ in_array("12",$selected_devices) ? 'selected' : ''  }} value="12" >Linux computer</option>
        <option {{ in_array("13",$selected_devices) ? 'selected' : ''  }} value="13" >Android Computer</option>
        <option {{ in_array("14",$selected_devices) ? 'selected' : ''  }} value="14" >Windows Phone</option>
    </select><br><br>
    <label for="name"><b>Inventory Type</b></label><br>
    @php
        if(count($selected_itypes)> 0){
            $inventories_inc_exc = 1;
        }else{
            $inventories_inc_exc = 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="inventories" type="radio" name="inventories_inc_exc" value="3"  {{ (isset($inventories_inc_exc) && $inventories_inc_exc == 3) || !isset($inventories_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="inventories" {{ isset($inventories_inc_exc) && $inventories_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="inventories_inc_exc" id="iplists_inc" value="1" > Include 
    <select class="form-control select2" name="inventory_type[]" id="inventories"  multiple {{ (isset($inventories_inc_exc) && $inventories_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        <option {{ in_array("1",$selected_itypes) ? 'selected' : ''  }} value="1">Desktop & Mobile Web</option>
        <option {{ in_array("2",$selected_itypes) ? 'selected' : ''  }} value="2">Mobile In-App</option>
        <option {{ in_array("3",$selected_itypes) ? 'selected' : ''  }} value="3">Mobile Optimized Web</option>
    </select><br><br>
    <label for="name"><b>Internet Service Provider / Mobile Carrier</b></label><br>
    @php
    $isp_inc_exc = 3;
    if($selected_isps){
        $isp_inc_exc = $selected_isps['inc_exc'];
    }
 
    @endphp
    <input class="inc_exc" data-input-id="isps" data-input-tag="tagsinput" type="radio" name="isp_inc_exc" value="3"  {{ isset($isp_inc_exc) && $isp_inc_exc == 3 ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="isps" {{ isset($isp_inc_exc) && $isp_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="isp_inc_exc" id="isp_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="isps" {{ isset($isp_inc_exc) && $isp_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="isp_inc_exc" id="isp_exc" value="2" > Exclude<br>
    <div class="input-group" style="{{ isset($isp_inc_exc) && $isp_inc_exc == 3 ? 'display: none;' : 'display: block;' }}" id="isp_container">
        <input class="form-control bootstrap-tagsinput tagsinput"  type="text" name="isps" id="isps" value="{{$selected_isps['isps']}}" data-role="taginput" {{ (isset($isp_inc_exc) && $isp_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
    </div>
    <div class="input-group" id="isp_container_disabled" style="{{ isset($isp_inc_exc) && $isp_inc_exc != 3 ? 'display: none;' : 'display: block;'}}">
        <input class="form-control"  type="text" disabled="disabled">
    </div>

    <br>
    <label for="name"><b>Operating System:</b></label><br>
    @php
        if(count($selected_oss)> 0){
            $os_inc_exc = 1;
        }else{
            $os_inc_exc = 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="operatingSystem" type="radio" name="os_inc_exc" value="3"  {{ (isset($os_inc_exc) && $os_inc_exc == 3) || !isset($os_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="operatingSystem" {{ isset($os_inc_exc) && $os_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="os_inc_exc" value="1" > Include 
    <select class="form-control select2" name="os[]" id="operatingSystem"  multiple {{ (isset($os_inc_exc) && $os_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        <option {{ in_array("1",$selected_oss) ? 'selected' : ''  }} value="1">Windows</option>
        <option {{ in_array("11",$selected_oss) ? 'selected' : ''  }} value="11">Windows Phone</option>
        <option {{ in_array("4",$selected_oss) ? 'selected' : ''  }} value="4">Mac OS</option>
        <option {{ in_array("5",$selected_oss) ? 'selected' : ''  }} value="5">Linux</option>
        <option {{ in_array("6",$selected_oss) ? 'selected' : ''  }} value="6">ANDROID</option>
        <option {{ in_array("7",$selected_oss) ? 'selected' : ''  }} value="7">IOS</option>
        <option {{ in_array("9",$selected_oss) ? 'selected' : ''  }} value="9">Roku OS</option>
        <option {{ in_array("10",$selected_oss) ? 'selected' : ''  }} value="10">Tizen</option>
        <option {{ in_array("12",$selected_oss) ? 'selected' : ''  }} value="12">CHROMEOS</option>
        <option {{ in_array("8",$selected_oss) ? 'selected' : ''  }} value="8">Other</option>
    </select><br><br>
    <label for="name"><b>Browser</b></label><br>
    @php
        if(count($selected_browsers)> 0){
            $browser_inc_exc = 1;
        }else{
            $browser_inc_exc = 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="browser" type="radio" name="browser_inc_exc" value="3"  {{ (isset($browser_inc_exc) && $browser_inc_exc == 3) || !isset($browser_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="browser" {{ isset($browser_inc_exc) && $browser_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="browser_inc_exc" id="iplists_inc" value="1" > Include 
    <select class="form-control select2" name="browser[]" id="browser" multiple {{ (isset($browser_inc_exc) && $browser_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        <option {{ in_array("1",$selected_browsers) ? 'selected' : ''  }} value="1">Chrome</option>
        <option {{ in_array("2",$selected_browsers) ? 'selected' : ''  }} value="2">Firefox</option>
        <option {{ in_array("3",$selected_browsers) ? 'selected' : ''  }} value="3">MSIE</option>
        <option {{ in_array("4",$selected_browsers) ? 'selected' : ''  }} value="4">Opera</option>
        <option {{ in_array("5",$selected_browsers) ? 'selected' : ''  }} value="5">Safari</option>
        <option {{ in_array("6",$selected_browsers) ? 'selected' : ''  }} value="6">Other</option>
    </select>
</div>
