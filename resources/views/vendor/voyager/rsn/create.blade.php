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
        position: absolute;
        top: 2em;
        right: 1em;
    }
    .invalidFile {
        background-image: url(https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcTmeaUCnLPXIPXxkRzsWiPMUEPXNzzt4F_JihrtPtcXwab2OpD7&usqp=CAU);
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 3em;
        width: 3em;
        position: absolute;
        top: 2em;
        right: 1em;
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
                    <h1 class="separator" style="font-size: 22px;">Cognitive Resonance</h1>
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
                                        <div class="card " style="overflow: unset; min-height:18em; margin-top: 2em;">
                                            <div class="card-body">
                                                <h2 class="text-center">Campaign</h2>
                                                <div class="form-group">
                                                    <label for="campaignname">Name</label>
                                                    <input type="text" required class="form-control" id="campaignName" name="campaignname">
                                                </div>
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
                                                
                                                <button class="button-continue btn btn-primary" onClick="saveCampaign()"  id="saveCampaign" style="margin-bottom:2em">Save and Continue</button>
                                                <p id="campaignMessageSuccess" hidden class="text-success pull-right campaign-message" style="top:2em;"></p>
                                                <p id="campaignMessageDanger" hidden class="text-danger pull-right campaign-message"></p>
                                            </div>
                                        </div>
                                        <div class="card" id="adsCard" style="overflow: unset; margin-top: 2em;">
                                            <div class="card-body">
                                                <h2 class="text-center">New Ad</h2>
                                                <div class="" id="adsContainer" style="min-height: 18em;">
                                                    <div class="card" id="newAd">
                                                        <div class="card-body row" >
                                                            <div class="adsContainer" style="">
                                                                <div class="col-md-12">
                                                                    <div class="col-md-12" >
                                                                        <div class="form-group">
                                                                            <label for="adName">Ad Name</label>
                                                                            <input type="text" required class="form-control" id="adName" >
                                                                            <small id="adNameEmpty" hidden class="form-text text-danger text-muted">Ad name can not be empty</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-10" style="min-height:10em">
                                                                        <div class="form-group">
                                                                            <label for="adPreview">Ad Preview Url/Tag</label>
                                                                            <textarea type="text" required class="form-control" id="adPreview" ></textarea>
                                                                            <small id="adPreviewEmpty"  hidden class="form-text text-danger text-muted">Ad preview can not be empty</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2" style="min-height:10em">
                                                                        <button class="btn btn-info" onClick="showPreview()" style="margin-top: 3em">
                                                                           Show Preview 
                                                                        </button>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="inputGroupFileAddon01">Needstates data</span>
                                                                            </div>

                                                                            <div class="custom-file">
                                                                                <input type="file" class="custom-file-input" id="needstatesFile">
                                                                                <div class="validFile" hidden id="needstateValid"></div>
                                                                                <div class="invalidFile" hidden id="needstateInvalid"></div>
                                                                            </div>
                                                                            </div>
                                                                        </div>
                                                                       
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="inputGroupFileAddon01">Motivation data</span>
                                                                            </div>

                                                                            <div class="custom-file">
                                                                                <input type="file" class="custom-file-input" id="motivationFile">
                                                                                <div class="validFile" hidden id="motivationValid"></div>
                                                                                <div class="invalidFile" hidden id="motivationInvalid"></div>
                                                                            </div>
                                                                            </div>
                                                                        </div>
                                                                       
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="inputGroupFileAddon01">Networks & Dayparts data</span>
                                                                            </div>
                                                                            <div class="custom-file">
                                                                                <input type="file" class="custom-file-input" id="daypartsFile">
                                                                                <div class="validFile" hidden id="daypartsValid"></div>
                                                                                <div class="invalidFile" hidden id="daypartsInvalid"></div>
                                                                            </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="inputGroupFileAddon01">Resonance data</span>
                                                                            </div>
                                                                            <div class="custom-file">
                                                                                <input type="file" class="custom-file-input" id="resonanceFile">
                                                                                <div class="validFile" hidden id="resonanceValid"></div>
                                                                                <div class="invalidFile" hidden id="resonanceInvalid"></div>
                                                                            </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text" id="inputGroupFileAddon01">Lift data</span>
                                                                            </div>
                                                                            <div class="custom-file">
                                                                                <input type="file" class="custom-file-input" id="liftFile">
                                                                                <div class="validFile" hidden id="liftValid"></div>
                                                                                <div class="invalidFile" hidden id="liftInvalid"></div>
                                                                            </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-5" styles="margin-top:1em">
                                                                    <img hidden src="{{ asset('/Loading.gif') }}" id="loadingAd" style="    float: right;
                                                                        position: absolute;
                                                                        bottom: 0;
                                                                        right: 8em;
                                                                        height: 3em;" alt="">
                                                                    <button class="btn btn-primary" style="float:right" onClick="saveNewAd()" id="saveAd">Save Ad</button>
                                                                
                                                                    </div>
                                                                   </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="adsAdded">
                                                 
                                                </div>
                                            </div>
                                        </div>
                                        <a  href="/admin/rsn_campaigns" class=" btn btn-success" id=""  style="margin-top: 2em;float: right;position: relative;">Back to List</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="modalDelete">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Delete Ad</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div style="text-align:right" class="modal-body">    
                <h3 style="text-align:center">This action is irreversible, are you sure you want to continue?</h3>
                <a onClick="deleteAD()" class="btn btn-danger">Yes, Delete It</a>
            </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="modalPreview">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ad Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalAdPreview" style="margin: 0; padding: 0; height: max-content;"><div>
            </div>
            </div>
        </div>
    </div>

    

    <script>
        let campaignEdition = false;
        @if(isset($campaign))    
            campaignEdition =  JSON.parse('@php echo json_encode($campaign);  @endphp');
        @endif 
        
        
    </script>
    <script src="{{ asset('js/resonances_new.js') }}"></script>

    @stop
</div>