<style>
.voyager .bootstrap-switch, .voyager .table>tbody>tr>td, .voyager .table>tbody>tr>th, .voyager .table>tfoot>tr>td, .voyager .table>tfoot>tr>th {
    padding: 0;
    vertical-align: middle;
    margin: 0;
}
.btn {
    padding: 0.1em 0.5em;
}
</style>
<div id="data" class="tab-pane fade in">
    <label for="name"><b>Pixel Lists:</b></label><br>
    @php
        if(!isset($pixels_inc_exc)){
            $pixels_inc_exc = 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="pixels_selector" type="radio" name="pixels_inc_exc" value="3"  {{ (isset($pixels_inc_exc) && $pixels_inc_exc == 3) || !isset($pixels_inc_exc) ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="pixels_selector" {{ isset($pixels_inc_exc) && $pixels_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="pixels_inc_exc" value="1" > Include 
    <input class="inc_exc" data-input-id="pixels_selector" {{ isset($pixels_inc_exc) && $pixels_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="pixels_inc_exc" value="2" > Exclude 
    <select class="form-control select2" id="pixels_selector" name="pixels[]" multiple {{ (isset($pixels_inc_exc) && $pixels_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
    @foreach($pixels_list as $pixel)
        <option {{ in_array($pixel->id,$selected_pixels) ? 'selected' : ''  }}  value="{{$pixel->id}}" >[{{$pixel->id}}] {{$pixel->name}}</option>
    @endforeach
    </select><br><br>
    <label for="name"><b>Custom Data:</b></label><br>
    @php
        if(!isset($custom_datas_inc_exc)){
            $custom_datas_inc_exc = 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="custom_data" {{ isset($custom_datas_inc_exc) && $custom_datas_inc_exc == 3 ? 'checked="checked"' : '' }} type="radio" name="custom_datas_inc_exc" id="custom_datas_inc" value="3"> Off 
    <input class="inc_exc" data-input-id="custom_data" {{ isset($custom_datas_inc_exc) && $custom_datas_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="custom_datas_inc_exc" id="custom_datas_inc" value="1"> Include 
    <input class="inc_exc" data-input-id="custom_data" {{ isset($custom_datas_inc_exc) && $custom_datas_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="custom_datas_inc_exc" id="custom_datas_exc" value="2"> Exclude
    <select class="form-control select2" name="custom_datas[]" id="custom_data" multiple {{ (isset($custom_datas_inc_exc) && $custom_datas_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        @foreach($custom_datas as $custom_data)
            <option {{ in_array($custom_data->id,$selected_custom_datas) ? 'selected' : ''  }}  value="{{$custom_data->id}}" >[{{$custom_data->id}}] {{$custom_data->name}}</option>
        @endforeach
    </select><br><br>
    <label for="name"><b>DMPS Segments:</b></label><br>
        @php
            if(!isset($segments_inc_exc)){
                $segments_inc_exc = 3;
            }
        @endphp
        <input class="inc_exc" data-input-id="audiences_selection" {{ isset($segments_inc_exc) && $segments_inc_exc == 3 ? 'checked="checked"' : '' }} type="radio" name="segments_inc_exc" id="custom_datas_inc" value="3"> Off 
        <input class="inc_exc" data-input-id="audiences_selection" {{ isset($segments_inc_exc) && $segments_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="segments_inc_exc" id="custom_datas_inc" value="1"> Include 
        <input class="inc_exc" data-input-id="audiences_selection" {{ isset($segments_inc_exc) && $segments_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="segments_inc_exc" id="custom_datas_exc" value="2"> Exclude
        <div class="segments-container-disabled" {{ isset($segments_inc_exc) && $segments_inc_exc != 3 ? "hidden":'' }}>
            <select class="form-control select2 pacing-container-disabled"   multiple  disabled="disabled">
            </select>
        </div>
        <div class="segments-container" {{ isset($segments_inc_exc) && $segments_inc_exc == 3 ? "hidden":'' }} >
        <label for="name">Segments selection:</label>
        <div class="pull-right">
            <input class="inc_exc segments_filters"  checked="checked" type="checkbox" name="segments_target_1" id="segments_target_1" value="1"> Andriod 
            <input class="inc_exc segments_filters"  checked="checked" type="checkbox" name="segments_target_2"  id="segments_target_2" value="2"> Ios 
            <input class="inc_exc segments_filters" checked="checked" type="checkbox" name="segments_target_3"  id="segments_target_3" value="3"> Ip
            <input class="inc_exc segments_filters"  checked="checked" type="checkbox" name="segments_target_4"  id="segments_target_4" value="4"> Cookie
        </div>
      <br>
        <input type="hidden" id="audiences_selection" name="audiences_selection" value="{{count($selecteds_segments)>0 ? implode(',',$selecteds_segments ):''}}" />
        <input type="hidden" id="audiences_cpm" name="audiences_cpm" value="{{ isset($segments_cpm) ? $segments_cpm : 0}}" />
        <div class="audiences-tabs" style="min-height: 10em;">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @php $first = true;@endphp 
                @foreach($dmp as $dmp_name => $audiences)
                <li class="{{$first ? 'active' : ''}} tabs-segments" data-tab-name="{{$audiences}}">
                    <a class="nav-link" id="{{$dmp_name}}-tab" data-toggle="tab" href="#{{$dmp_name}}" role="tab" aria-controls="home" aria-selected="{{$first ? 'true' : 'false'}}">{{$dmp_name}}</a>
                </li>
                @php $first = false @endphp
                @endforeach
                <img src="{{ asset('/Loading.gif') }}" id="loading" style="position: absolute;
                width: 2.5em;
                right: 4em;
                z-index: 1;
                margin-top: 3px;" alt="">
                <input class="form-control search-segments" id="search-segments"  type="text" placeholder="Sub-Search">
            </ul>
            <div class="tab-content" id="myTabContent">
                <div role="tabpanel" class="tab-pane active" style="padding: 0; height: 33em; overflow: auto; margin-bottom: 1em;">
                        <table class="table table-dark">
                            <thead >
                                <tr>
                                    <th class="col-sm-2" scope="col">Id</th>
                                    <th class="col-sm-5" scope="col">Name</th>
                                    <th class="col-sm-1" scope="col">Type</th>
                                    <th class="col-sm-2 text-right"  scope="col">Reach</th>
                                    <th class="col-sm-1 text-right"   scope="col">Price</th>
                                    <th class="col-sm-1 text-center" scope="col" >Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-dmp">
                                
                            </tbody>
                        </table>
                        
                    </div>   
                    <div class="dataTables_paginate paging_simple_numbers" style="padding:0" id="segmentsPagination">
                        <ul class="pagination" id="paginationContainer">
                            
                        </ul>
                    </div>
            </div>
        </div>
    <label for="name">Segments selected:</label><br>
    <div class="audiences-selected" style="padding: 0; height: 33em; overflow: auto; margin-bottom: 4em;">
        <table class="table table-dark">
            <thead >
                <tr>
                    <th class="col-sm-2" scope="col">Id</th>
                    <th class="col-sm-5" scope="col">Name</th>
                    <th class="col-sm-1" scope="col">Type</th>
                    <th class="col-sm-2 text-right" scope="col">Reach</th>
                    <th class="col-sm-1 text-right" scope="col">Price</th>
                    <th class="col-sm-1 text-center" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody id="tbodyAudiencesSelected">
                @foreach($selecteds_segments_data as $key => $selected)
                    <tr id="selected-row-{{$selected['id']}}" data-price="{{$selected['price']}}">
                        <td style="padding-left: 0.5em;" scope="row">{{$selected['id']}}</td>
                        <td style="padding-left: 0.5em;">{{$selected['name']}}</td>
                        <td style="padding-left: 0.5em;">{{$selected['type']}}</td>
                        <td class="text-right" style="padding-right: 0.5em;">{{$selected['reach']}}</td>
                        <td class="text-right" style="padding-right: 0.5em;">$ {{number_format(floatval($selected['price']), 2)}}</td>
                        <td class="text-center" ><button data-price="{{$selected['price']}}" data-audience-id="{{$selected['id']}}" class="btn btn-small btn-warning remove_audiece"  >-</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>