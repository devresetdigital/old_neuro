<div id="contextual" class="tab-pane fade in">
<label for="name"><b>Contextual targeting:</b></label><br>
        @php
            if(!isset($contextual_inc_exc)){
                $contextual_inc_exc = 3;
            }
            
        @endphp
        <input class="inc_exc" data-input-id="contextual_selection" {{ isset($contextual_inc_exc) && $contextual_inc_exc == 3 ? 'checked="checked"' : '' }} type="radio" name="contextual_inc_exc" id="custom_datas_inc" value="3"> Off 
        <input class="inc_exc" data-input-id="contextual_selection" {{ isset($contextual_inc_exc) && $contextual_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="contextual_inc_exc" id="custom_datas_inc" value="1"> Include 
        <input class="inc_exc" data-input-id="contextual_selection" {{ isset($contextual_inc_exc) && $contextual_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="contextual_inc_exc" id="custom_datas_exc" value="2"> Exclude
        <div class="contextual-container-disabled" {{ isset($contextual_inc_exc) && $contextual_inc_exc != 3 ? "hidden":'' }}>
            <select class="form-control select2"   multiple  disabled="disabled">
            </select>
        </div>
        <div class="contextual-container" {{ isset($contextual_inc_exc) && $contextual_inc_exc == 3 ? "hidden":'' }} >
        <label for="name">Items selection:</label>
        <div class="pull-right">
            <input class="inc_exc contextual_filters"  checked="checked" type="checkbox" name="contextual_target_1" id="contextual_target_1" value="1"> Website 
            <input class="inc_exc contextual_filters"  checked="checked" type="checkbox" name="contextual_target_2"  id="contextual_target_2" value="2"> Ios App
            <input class="inc_exc contextual_filters" checked="checked" type="checkbox" name="contextual_target_3"  id="contextual_target_3" value="3"> Android App
            <input class="inc_exc contextual_filters"  checked="checked" type="checkbox" name="contextual_target_4"  id="contextual_target_4" value="4"> Other
        </div>
      <br>


        <input type="hidden" id="contextual_selection" name="contextual_selection" value="{{ count($contextual_selected)>0 ? implode(',',$contextual_selected ):''}}" />
        <div class="context-tabs" style="min-height: 10em;">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @php $first = true;@endphp 
                @foreach($contextualTypes as $dmp_name => $context)
                <li class="{{$first ? 'active' : ''}} tabs-contextual" data-tab-name="{{$context}}">
                    <a class="nav-link" id="{{$dmp_name}}-tab" data-toggle="tab" href="#{{$dmp_name}}" role="tab" aria-controls="home" aria-selected="{{$first ? 'true' : 'false'}}">{{$dmp_name}}</a>
                </li>
                @php $first = false @endphp
                @endforeach
                <img src="{{ asset('/Loading.gif') }}" id="loading-contextual" style="position: absolute;
    width: 2.5em;
    right: 4em;
    z-index: 1;
    margin-top: 3px;" alt="">
                <input class="form-control search-context" id="search-contextual"  type="text" placeholder="Sub-Search">
            </ul>
            <div class="tab-content" id="myTabContent">
                <div style="padding: 0px 18px 0px 0px;">
                    <table class="table table-dark" style="margin-bottom:0;">
                        <thead >
                            <tr>
                                <th class="col-sm-3" scope="col">Id</th>
                                <th class="col-sm-4" scope="col">Name</th>
                                <th class="col-sm-2" scope="col">Channel</th>
                                <th class="col-sm-2 text-right" scope="col">Items</th>
                                <th class="col-sm-1 text-center" scope="col">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane active" style="padding: 0; height: 33em; overflow: auto; margin-bottom: 1em;">
                    
                    <table class="table table-dark">
                        <tbody id="tbody-contextual">
                        </tbody>
                    </table>
                    
                </div>   
                <div class="dataTables_paginate paging_simple_numbers" style="padding:0" id="contextualPagination">
                    <ul class="pagination" id="contextual_paginationContainer">
                        
                    </ul>
                </div>
            </div>
        </div>
    <label for="name">Items selected:</label><br>
    <div style="padding: 0px 18px 0px 0px;">
        <table class="table table-dark" style="margin-bottom:0;">
            <thead >
                <tr>
                    <th class="col-sm-3" scope="col">Id</th>
                    <th class="col-sm-4" scope="col">Name</th>
                    <th class="col-sm-2" scope="col">Channel</th>
                    <th class="col-sm-2 text-right" scope="col">Items</th>
                    <th class="col-sm-1 text-center" scope="col">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="context-selected" style="padding: 0; height: 33em; overflow: auto; margin-bottom: 4em;">
        
        <table class="table table-dark">
            <tbody id="tbodyContextualSelected">
                @foreach($contextual_selected_data as $key => $selected)
                    <tr id="selected-context-row-{{$selected['id']}}">
                        <td class="col-sm-3" style="padding-left: 0.5em;" scope="row">{{$selected['id']}}</td>
                        <td class="col-sm-4"  style="padding-left: 0.5em;" >{{$selected['name']}}</td>
                        <td class="col-sm-2" style="padding-left: 0.5em;" >{{$selected['type']}}</td>
                        <td class="col-sm-2 text-right" style="padding-right: 0.5em;">{{$selected['items']}}</td>
                        <td class="col-sm-1 text-center" ><button data-context-id="{{$selected['id']}}" class="btn btn-small btn-warning remove_context"  >-</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
</div>