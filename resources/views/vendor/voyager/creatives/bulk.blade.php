@extends('voyager::master')


@section('page_title','Creatives bulk upload')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-images"></i>
        Bulk processing
        <a href="/bulk_templates/bulkCreatives.csv" target="_blank" class="btn btn-small btn-primary"><label >Download template</label></a>
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
   <li> <a href="/admin/creatives">Creatives</a></li>
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
                                <label for="advertiser_id">Advertiser:&nbsp;&nbsp;<div class="icon voyager-lightbulb help" data-toggle="modal" data-target="#helpModal">&nbsp;help</div></label>
                                <select class="form-control select2 select2-hidden-accessible" name="advertiser_id"  id="advertiser_id_field" tabindex="-1" aria-hidden="true">
                                    <option value="">Plese select an advertiser</option>
                                    @foreach($advertisers as $advertiser)
                                        <option value="{{$advertiser->id}}">{{$advertiser->name}}</option>
                                    @endforeach   
                                </select>
                                <br>
                                <br>
                                <div id="fileContainer">
                                    <label for="file">Csv file:</label>
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
                                    <label for="advertiser_id">Duplicated Concepts:&nbsp;</label>
                                    <div id="duplicated" class="bordered">
                                        
                                    </div>
                                </div>
                                <div id="messageContainer">
                                   <h4 id="messageSuccess" class="text-success">bulk upload process completed successfully</h4>
                                   <h4 id="messageError" class="text-danger">There was an error trying to proccess creatives, please try again</h4>
                                </div>
                                <div id="summaryContainer">
                                    <label for="advertiser_id">Summary:&nbsp;</label>
                                    <div id="summary " class="summary bordered">
                                        <dl>   
                                            <dt>New Creatives</dt>
                                            <dd id="newCreativesAmount">-</dd>
                                        </dl>
                                        <dl>   
                                            <dt>New Concepts</dt>
                                            <dd id="newConceptsAmount">-</dd>
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
                                                <th scope="col">Id</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Concept</th>
                                                <th scope="col">Start date</th>
                                                <th scope="col">End date</th>
                                                <th scope="col">Width</th>
                                                <th scope="col">Height</th>
                                                <th scope="col">Markup</th>
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
                        <dl>   
                            <dt>id (creative id)</dt>
                            <dd>blank to create</dd>
                            <dd>fill to update</dd>
                        </dl>
                        <dl>   
                            <dt>creative_type_id (Number)</dt>
                            <dd>1 -> Display</dd>
                            <dd>2 -> Video</dd>
                        </dl>
                        <dl>   
                            <dt>name (Text)</dt>
                            <dd>Creative name</dd>
                        </dl>
                        <dl>   
                            <dt>click_url (Text)</dt>
                            <dd>Click url</dd>
                        </dl>
                        <dl>   
                            <dt>3pas_tag_id (Text)</dt>
                            <dd>Alphanumeric external id</dd>
                        </dl>
                        <dl>   
                            <dt>landing_page (Text)</dt>
                            <dd>Landing page</dd>
                        </dl>
                        <dl>   
                            <dt>start_date (Text)</dt>
                            <dd>format: mm-dd-yyyy</dd>
                        </dl>
                        <dl>   
                            <dt>end_date  (Text)</dt>
                            <dd>format: mm-dd-yyyy</dd>
                        </dl>
                        <dl>   
                            <dt>concept (Text)</dt>
                            <dd>concept name (if it doesn't exist it will be created)</dd>
                        </dl>

                        <dl>   
                            <dt>ad_height (Number)</dt>
                            <dd>Ad's height </dd>
                        </dl>
                        <dl>   
                            <dt>ad_width (Number)</dt>
                            <dd>Ad's width </dd>
                        </dl>
                        <dl>   
                            <dt>3rd_tracking (Text)</dt>
                            <dd>3rd tracking</dd>
                        </dl>
                        <dl>   
                            <dt>vast_code (text)</dt>
                            <dd>Only for type video</dd>
                        </dl>
                        <dl>   
                            <dt>tag_code (Text)</dt>
                            <dd>Available macros:</dd>
                            <dd>{amas_ip}</dd>
                            <dd>{amas_country}</dd>
                            <dd>{amas_region}</dd>
                            <dd>{amas_city}</dd>
                            <dd>{amas_zipcode}</dd>
                            <dd>{amas_isp}</dd>
                            <dd>{amas_gps_lat}</dd>
                            <dd>{amas_gps_long}</dd>
                            <dd>{amas_organization}</dd>
                            <dd>{amas_advertiser}</dd>
                            <dd>{amas_campaign}</dd>
                            <dd>{amas_concept}</dd>
                            <dd>{amas_creative}</dd>
                            <dd>{amas_creative_width}</dd>
                            <dd>{amas_creative_height}</dd>
                            <dd>{amas_creative_size}</dd>
                            <dd>{amas_creative_type}</dd>
                            <dd>{amas_ssp}</dd>
                            <dd>{amas_seat}</dd>
                            <dd>{amas_pmp}</dd>
                            <dd>{amas_publisher}</dd>
                            <dd>{amas_site}</dd>
                            <dd>{amas_app_name}</dd>
                            <dd>{amas_app_domain}</dd>
                            <dd>{amas_app_id}</dd>
                            <dd>{amas_app_site}</dd>
                            <dd>{amas_device_ifa}</dd>
                            <dd>{amas_bidprice}</dd>
                            <dd>{amas_winprice}</dd>
                            <dd>{amas_price}</dd>
                            <dd>{amas_device_browser}</dd>
                            <dd>{amas_device_os}</dd>
                            <dd>{amas_device_type}</dd>
                            <dd>{amas_click_redirect_unencoded:redirectUrl}</dd>
                            <dd>{amas_click_redirect_encoded:redirectUrl}</dd>
                            <dd>{amas_click_redirect_doubleencoded:redirectUrl}</dd>
                            <dd>{amas_click_url_unencoded}</dd>
                            <dd>{amas_click_url_encoded}</dd>
                            <dd>{amas_roi_url_unencoded:000}</dd>
                            <dd>{amas_roi_url_encoded:000}</dd>
                            <dd>{amas_customevent1_url_unencoded:000}</dd>
                            <dd>{amas_customevent1_click_url_encoded:000}</dd>
                            <dd>{amas_customevent2_url_unencoded:000}</dd>
                            <dd>{amas_customevent2_click_url_encoded:000}</dd>
                            <dd>{amas_customevent2_url_unencoded:000}</dd>
                            <dd>{amas_customevent2_click_url_encoded:000}</dd>
                            <dd>{cachebuster}</dd>
                            <dd>{randomnumber}</dd>
                        </dl>
                            
                        <dl>   
                            <dt>creative_attributes (Text) Format: 1,2,3,4,5</dt>
                            <dd>1 - Audio Ad (Auto-Play)</dd>
                            <dd> 2 - Audio Ad (User Initiated)</dd>
                            <dd>3 - Expandable (Automatic)</dd>
                            <dd>4 - Expandable (User Initiated - Click)</dd>
                            <dd>5 - Expandable (User Initiated - Rollover)</dd>
                            <dd>6 - In-Banner Video Ad (Auto-Play)</dd>
                            <dd>7 - In-Banner Video Ad (User Initiated)</dd>
                            <dd>8 - Pop (e.g., Over, Under, or Upon Exit)</dd>
                            <dd>9 - Provocative or Suggestive Imagery</dd>
                            <dd>10 - Shaky, Flashing, Flickering, Extreme Animation, Smileys</dd>
                            <dd>11 - Surveys</dd>
                            <dd>12 - Text Only</dd>
                            <dd>13 - User Interactive (e.g., Embedded Games)</dd>
                            <dd>14 - Windows Dialog or Alert Style</dd>
                            <dd>16 - Ad Provides Skip Button (e.g. VPAID-rendered skip button on pre-roll video)</dd>
                            <dd>17 - Adobe Flash</dd>
                        </dl>
                        <dl>   
                            <dt>skippable (0/1)</dt>
                            <dd>Only for type video</dd>
                        </dl>
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
    <script src="{{ asset('js/bulk/creatives.js')}}"></script>
@stop
