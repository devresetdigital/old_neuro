@extends('voyager::master') @section('content')
<link rel="stylesheet" href="{{ asset('css/lib/bootstrap-multiselect.css') }}" type="text/css" />
<style>
    .select2-container {
        border: 1px solid #aaa !important;
        border-radius: 4px !important;
    }

    .page-header h1 {
        margin: 0;
        display: inline-block;
        padding: 7px 20px 7px 0;
    }

    .list-group {
        display: flex;
        flex-direction: column;
        padding-left: 0;
        margin-bottom: 0;
    }

    .badge.badge-circle {
        border-radius: 10%;
        width: 5em;
        height: 2.5em;
        font-size: 0.8em;
        font-weight: 600;
        line-height: 1.6;
        padding: 4px 5px;
        vertical-align: baseline;
    }

    .list-group-item {
        border: 1px solid rgba(210, 221, 234, .3);
        font-size: .875rem;
    }

    .form-control {
        border-color: #dfe7f3;
    }

    .form-control {
        border: 1px solid rgba(120, 141, 180, .3);
    }

    #adModalPreview {
        text-align: -webkit-center;
    }

    .modal-title {
        text-align: center;
    }

    .validFile {
        background-image: url(https://upload.wikimedia.org/wikipedia/commons/thumb/b/bd/Checkmark_green.svg/1180px-Checkmark_green.svg.png);
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 3em;
        width: 3em;
        position: relative;
        left: 18em;
        bottom: 4em;
    }
    .invalidFile {
        background-image: url(https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcTmeaUCnLPXIPXxkRzsWiPMUEPXNzzt4F_JihrtPtcXwab2OpD7&usqp=CAU);
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 3em;
        width: 3em;
        position: relative;
        left: 18em;
        bottom: 4em;
    }


    .multiselect-container {
        max-height: 20em;
        overflow-y: scroll;
    }

    .principal-card {
        overflow: unset !important;
    }

    #adModalNeedstates {
        max-height: 17em;
        overflow-y: scroll;
    }

    .toggle {
        width: 100% !important;
    }

    .card.top-resonances {
        background-color: #736bc7;
        color: #fff;
        height: 100%;
    }

    .top-resonances h5.card-header {
        border-bottom: none !important;
    }

    .top-resonances li {
        color: #76838f;
    }
    .top-resonances ul {
        background-color: #fff;
        height: 100%;
    }

    .row-same-height {
        display: flex;
        flex-wrap: wrap;
    }

    .groupby-container {
        position: absolute;
        bottom: 8em;
        right: 3em;
        display: flex;
    }

    .groupby-container .dropdown-toggle {
        width: 25em;
    }

    .chart-card{
        min-height:24em;
    }

    #scrollTopBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: #736ac7;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
    }

    div.preview-container > :first-child{
        width: 100% !important;;
        box-shadow: 0 10px 40px 0 rgba(18,106,211,.07), 0 2px 9px 0 rgba(18,106,211,.06) !important;;
        border-radius: .25rem !important;;
        height: auto !important;;
        padding-top: 75% !important;;
    }

    button.button-continue{
        position: absolute;
        bottom: 1em;
        right: 2em;
    }

    .campaign-message{
        margin-top: - 2em;
        margin-right: 0.2em;
        font-size: large;
        font-weight: 600;
    }

    .small-file{
        position: absolute;
        bottom: 0;
        margin-left: 1em;
    }
    .ads-jumbotron{
        padding: 0.5em;
        margin-top: 2em;
        text-align: center;
    }

</style>
<div class="page-content container-fluid">
    @include('voyager::alerts')
    <div class="content" style="margin-left: 3.5em; margin-bottom: -10px">
        <header class="page-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h1 class="separator" style="font-size: 22px;">Linear Campaign</h1>
                </div>
            </div>
        </header>
    </div>
    <div style="width: 100%; padding: 10px 60px 0px 60px;">
        <div class="row">
            <div class="col">
                <div class="card principal-card">
                    <div class="card-body p-0">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <div class="card-body">
                          
                                    <div class="col-sm-12">
                                        <label>New Campaign</label>
                                        <form action="/api/linear/import" method="POST"  id="newCampaign" name="newCampaign" enctype='multipart/form-data'>
                                            <div class="card " style="overflow: unset; min-height:18em; margin-top: 2em;">
                                                <div class="card-body">
                                                    <h2 class="text-center">Linear Campaign</h2>
                                                    <div class="form-group">
                                                        <label for="campaignname">Name</label>
                                                        <input type="text" required class="form-control" id="campaignName" name="campaignname">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="campaignname">Date</label>
                                                        <input type="date" class="form-control" 
                                                            name="date" required
                                                            placeholder="date"
                                                            value="">
                                                    </div>
                                                    <input type="hidden" id="campaign_id" name="campaign_id" value="">
                                                    <div class="form-group">
                                                            <label for="organization">Organization</label>
                                                            <select class="form-control " name="organization" id="organization" style="width: 50%">
                                                            <option value="">None</option>
                                                            @foreach($organizations as $organization)
                                                            <option value="{{$organization['id']}}">{{$organization['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="advertiser">Advertiser</label>
                                                        <select class="form-control " name="advertiser" id="advertiser" style="width: 50%">
                                                            <option value="">None</option>
                                                        </select>
                                                    </div>
                                                    @if(isset($campaign_selected))  
                                                    <div class="form-group">
                                                        <label for="campaign">Campaign</label>
                                                        <select class="form-control " name="campaign" id="campaign" style="width: 50%">
                                                            <option value="">None</option>
                                                            @foreach($campaigns as $campaign)
                                                            <option value="{{$campaign['campaign_id']}}">{{$campaign['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @endif

                                                    <div class="form-group custom-file fileContainer" >
                                                        <label for="network">Network Delivery</label>
                                                        <input type="file" required name="network" class="custom-file-input" id="network">
                                                        <div class="validFile" hidden id="fileValid-network"></div>
                                                        <div class="invalidFile" hidden id="fileInvalid-network"></div>
                                                    </div>

                                                    <div class="form-group custom-file fileContainer" >
                                                        <label for="weekly">Weekly Delivery </label>
                                                        <input type="file" required name="weekly" class="custom-file-input" id="weekly">
                                                        <div class="validFile" hidden id="fileValid-weekly"></div>
                                                        <div class="invalidFile" hidden id="fileInvalid-weekly"></div>
                                                    </div>

                                                    <div class="form-group custom-file fileContainer"  >
                                                        <label for="msu">M-Su</label>
                                                        <input type="file" required name="msu" class="custom-file-input" id="msu">
                                                        <div class="validFile" hidden id="fileValid-msu"></div>
                                                        <div class="invalidFile" hidden id="fileInvalid-msu"></div>
                                                    </div>

                                                    <div class="form-group custom-file fileContainer"  >
                                                        <label for="mda">MDA Summary</label>
                                                        <input type="file" required name="mda" class="custom-file-input" id="mda">
                                                        <div class="validFile" hidden id="fileValid-mda"></div>
                                                        <div class="invalidFile" hidden id="fileInvalid-mda"></div>
                                                    </div>

                                                    <div class="form-group custom-file fileContainer"  >
                                                        <label for="mdareport">DMA Report</label>
                                                        <input type="file" required name="mdareport" class="custom-file-input" id="mdareport">
                                                        <div class="validFile" hidden id="fileValid-mdareport"></div>
                                                        <div class="invalidFile" hidden id="fileInvalid-mdareport"></div>
                                                    </div>


                                                    
                                                    <button class="button-continue btn btn-primary"  id="saveCampaign" style="margin-bottom:2em">Save</button>
                                                    <p id="campaignMessageSuccess" hidden class="text-success pull-right campaign-message" style="top:2em;"></p>
                                                    <p id="campaignMessageDanger" hidden class="text-danger pull-right campaign-message"></p>
                                                </div>
                                            </div>
                                        </form>
                                        <a  href="/admin/linear" class=" btn btn-success" id=""  style="margin-top: 2em;float: right;position: relative;">Back to List</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let campaignEdition = false;
        @if(isset($campaign_selected))    
           campaignEdition =  JSON.parse('@php echo json_encode($campaign_selected);  @endphp');
        @endif 
    </script>
    <script src="{{ asset('js/linear_create.js') }}"></script>
    @stop
</div>