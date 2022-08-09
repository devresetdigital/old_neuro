@section('css')
<link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@stop



@extends('voyager::master') 

@section('content')
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

    .checked-daypart {
        background-image: url(https://upload.wikimedia.org/wikipedia/commons/thumb/b/bd/Checkmark_green.svg/1180px-Checkmark_green.svg.png);
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 20px;
        width: 20px;
    }

    #reportTable_wrapper {
        /* overflow: scroll;
    height: 800px;*/
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

</style>
<div class="page-content container-fluid">
    @include('voyager::alerts')
    <div class="content" style="margin-left: 3.5em; margin-bottom: -10px">
        <header class="page-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                <label>Campaigns</label>
                <a href="admin/linear/create" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>Add New</span>
            </a>
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
                                    <div class="card-body">
                                        <table id="camnpaignTable" class="">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Campaign</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                foreach($campaigns as $campaign){
                                                echo '<tr>';    
                                                echo '<td>'.$campaign["campaign_id"].'</td>';
                                                echo '<td>'.$campaign["name"].'</td>'; 
                                                echo '<td><a href="/admin/linear/report?campaign_id='.$campaign["campaign_id"].'&from='.substr($campaign["date"],2,2).substr($campaign["date"],5,2) .substr($campaign["date"],8,2) .'&until='.substr($campaign["date"],2,2).substr($campaign["date"],5,2) .substr($campaign["date"],8,2) .'" class="btn btn-sm btn-primary view">Report</a><a href="/admin/linear/edit/'.$campaign['id'].'" class="btn btn-sm btn-primary edit">Edit/Add Data</a></td>';
                                                echo '</tr>';
                                                }
                                            @endphp
                                            </tbody>
                                        </table>
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
        const campaignsStructure = <?php echo json_encode($campaigns); ?>;
        let reporTable ;

        if (reporTable) { 
            reporTable.destroy(); 
        }else{
            reporTable = $('#camnpaignTable').DataTable({
                        paging: false,
                        scrollX: false,
                        lengthChange: false,
                        searching: false,
                        ordering: false,
                        info: false
            });
        }

        $(document).ready(function () {
            $('.delete').click(function(e){
                if(!confirm("This action is irreversible, are you sure you want to continue?")){
                    return e.preventDefault();
                };
            });

        });    
       
    </script>


    @stop
</div>