@extends('voyager::master')


@section('page_title','Strategies bulk upload')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-images"></i>
        Bulk processing
        <a href="/bulk_templates/bulkStrategies.csv" target="_blank" class="btn btn-small btn-primary"><label >Download template</label></a>
    </h1>
@stop
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/bulk-creatives.css') }}">
@stop
@section('breadcrumbs')
<ol class="breadcrumb hidden-xs">
    <li class="active">
    <a href="/admin"><i class="voyager-boat"></i> Dashboard</a>
    </li>
    <li>Bulk</li>
   <li> <a href="/admin/strategies">Strategies</a></li>
</ol>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="father" >
                            <div class="child1 bordered">
                                <label for="advertiser_id">Advertiser:&nbsp;<div class="icon voyager-lightbulb help" data-toggle="modal" data-target="#helpModal">&nbsp;help</div></label>
                                <select class="form-control select2 select2-hidden-accessible" name="advertiser_id"  id="advertiser_id_field" tabindex="-1" aria-hidden="true">
                                    <option value="">Please select an advertiser</option>
                                    @foreach($advertisers as $advertiser)
                                        <option value="{{$advertiser->id}}">{{$advertiser->name}}</option>
                                    @endforeach
                                </select>
                                <br>
                                <br>
                                <div id="fileContainer">
                                    <label for="file">Csv file:&nbsp;  </label>
                                    <input type="file" class="file-field" id="bulkFile" name="file">
                                </div>
                                <br>
                            </div>
                            <div class="child3 bordered">
                                <div id="ErrorsContainer">
                                    <label for="advertiser_id">Errors:&nbsp;</label>
                                    <div id="errors" class="bordered">
                                        
                                    </div>
                                </div>
                                <br  class="duplicatedContainer">
                                <div class="duplicatedContainer">
                                    <label for="advertiser_id">Duplicated Campaigns:&nbsp;</label>
                                    <div id="duplicated" class="bordered">
                                        
                                    </div>
                                </div>
                                <div id="messageContainer">
                                   <h4 id="messageSuccess" class="text-success">bulk upload process completed successfully</h4>
                                   <h4 id="messageError" class="text-danger">There was an error trying to proccess Strategies, please try again</h4>
                                </div>
                                <div id="summaryContainer">
                                    <label for="advertiser_id">Summary:&nbsp;</label>
                                    <div id="summary " class="summary bordered">
                                        <dl>   
                                            <dt>New Strategies</dt>
                                            <dd id="newStrategiesAmount">-</dd>
                                        </dl>
                                        <dl>   
                                            <dt>New Campaings</dt>
                                            <dd id="newCampaignsAmount">-</dd>
                                        </dl>
                                    </div>
                                </div>
                                <br>
                                <div id="reviewContainer">
                                    <label for="advertiser_id">Entries Review:&nbsp;</label>
                                    <div id="results" class="bordered">
                                        <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Campaign</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Start date</th>
                                                <th scope="col">End date</th>
                                                <th scope="col">Budget</th>
                                                <th scope="col">Goal Values</th>
                                                <th scope="col">Min Bid CPM</th>
                                                <th scope="col">Max Bid CPM</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyreview">
                                    
                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="save-container">
                                    <br>
                                    <a id="executeButton" class="btn btn-success push-rigth">EXECUTE BULK UPLOAD</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade modal-info" id="helpModal">
        <div class="modal-dialog" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-info"></i>Help</h4>
                </div>
                <div class="modal-body">
                    <div class="summary">
                        <dl><dt>id (strategy id - blank to create)</dt></dl>
                        <dl><dt>campaign (name or id)</dt></dl>
                        <dl><dt>name (strategy name)</dt></dl>
                        <dl><dt>date_start (start date mm-dd-yyyy)</dt></dl>
                        <dl><dt>date_end (end date mm-dd-yyyy)</dt></dl>
                        <dl><dt>budget (strategy budget)</dt></dl>
                        <dl><dt>goal_type (1-CPC 2-CTR 3-Viewability Rate 4-Viewable CPM 5-CPM REACH)</dt></dl>
                        <dl><dt>goal_amount (Goal Values)</dt></dl>
                        <dl><dt>goal_bid_for (1-Total Spent 2-Media only)</dt></dl>
                        <dl><dt>goal_min_bid (min bid goal )</dt></dl>
                        <dl><dt>goal_max_bid (max bid goal)</dt></dl>

                        <dl><dt>m_type (Pacing Monetary type 1-EVEN 2-ASAP)</dt></dl>
                        <dl><dt>m_amount (Pacing Monetary amount)</dt></dl>
                        <dl><dt>m_stype (Pacing Monetary type 2-DAY)</dt></dl>

                        <dl><dt>i_type (Pacing Impressions  type 2-ASAP 3-NoCap)</dt></dl>
                        <dl><dt>i_amount (Pacing Impressions  amount)</dt></dl>
                        <dl><dt>i_stype (Pacing Impressions  type 2-DAY)</dt></dl>
                        
                        <dl><dt>f_type (Frecuency Cap type 1-EVEN 2-ASAP)</dt></dl>
                        <dl><dt>f_amount (Frecuency Cap amount)</dt></dl>
                        <dl><dt>f_stype (Frecuency Cap type 2-DAY 3-7 Days 4-30 Days)</dt></dl>

                        <dl><dt>selected_concepts (concepts id or name)</dt></dl>

                        <dl><dt>country_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>country (id or name)</dt></dl>
                 
                        <dl><dt>region_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>region (id or name)</dt></dl>
                 
                        <dl><dt>city_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>city (id or name)</dt></dl>
                 
                        <dl><dt>lang_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>language (id or name)</dt></dl>
                 
                        <dl><dt>geofencing_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>geofencingjson (Geofencing Json)</dt></dl>
                 
                        <dl><dt>sitelists_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>sitelists (id or name)</dt></dl>+

                        <dl><dt>iplists_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>iplists (id or name)</dt></dl>

                        <dl><dt>pmps (id or name)</dt></dl>
                        <dl><dt>open_market (1/0)</dt></dl>

                        <dl><dt>ssps_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>ssps (id or name)</dt></dl>
                 
                        <dl><dt>ziplists_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>ziplists (id or name)</dt></dl>
                 
                        <dl><dt>keywordslist_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>keywordslists (id or name)</dt></dl>
                 
                        <dl><dt>devices_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>device (id or name)</dt></dl>
                 
                        <dl><dt>inventories_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>inventory_type (id or name)</dt></dl>
                 
                        <dl><dt>isp_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>isps (id or name)</dt></dl>
                 
                        <dl><dt>os_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>os (id or name)</dt></dl>
                 
                        <dl><dt>browser_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>browser (id or name)</dt></dl>
                 
                        <dl><dt>pixels_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>pixels (id or name)</dt></dl>
                 
                        <dl><dt>custom_datas_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>custom_datas (id or name)</dt></dl>
                 
                        <dl><dt>segments_inc_exc (1-include 2-exclude 3-OFF )</dt></dl>
                        <dl><dt>segments (id or name)</dt></dl>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script src="{{ asset('js/bulk/strategies.js')}}"></script>
@stop
