<!DOCTYPE html>
<html lang="en">
<head>
    <title>Campaigns Statuses</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/spacelab/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<style>

.searchrow-campaign{
    opacity: 90%;
}
.searchrow-campaign>th, .searchrow-campaign>tr ,.searchrow-campaign>td{
    padding-bottom: 0.2em !important;
    padding-top: 0.2em !important;
}
.searchrow-strategy>th, .searchrow-strategy>tr ,.searchrow-strategy>td{
    padding-bottom: 0em !important;
    padding-top: 0em !important;
}
.strategies {
    font-size: smaller;
 
}
.campaigns {
    font-weight: bolder;
}
.ordering {
    cursor: pointer !important;
}
.link {
    color: #fff;
    cursor: pointer !important;
}
.main-container {
    margin-top: 1.1em;
}

</style>
<div class="container-fluid main-container">
    <div class="row">
        <div class="col-6">
            <a href="/api/exportPacing" class="btn btn-info" target="_blank" rel="noopener noreferrer">Export to Csv</a>
            <a href="#" id="copy" class="btn btn-info" >Copy to Clipboard</a>
            <select id="separator" style="max-width: 13em;" class="custom-select" name="separator">
                <option selected="">Decimals separator</option>
                <option value="period">Period (.)</option>
                <option value="comma">Comma (,)</option>
            </select>
        </div>
        <div class="col-2">
        <div id="speedWarning">calculating speed: &nbsp;<label id="countdown_number"></label>&nbsp;s.</div>
        </div>
        <div class="col-4">
                <input id="search" class="form-control  float-right" type="text" placeholder="Search">
        </div>

    </div>

     <table class="table table-hover ">

        <thead>
            <tr>
                <th class="fit" scope="col"><a  href="/admin/campaigns_statuses?orderBy=id&order_direction=<?php echo $order_direction;?>"><label class="ordering">ID</label></th>
                <th scope="col"><a  href="/admin/campaigns_statuses?orderBy=name&order_direction=<?php echo $order_direction;?>"><label  class="ordering" >Name</label></th>
                <th class="fit" scope="col"><a  href="/admin/campaigns_statuses?orderBy=impressions&order_direction=<?php echo $order_direction;?>"><label class="float-right ordering">Impressions</label></a></th>
                <th scope="col"><a  href="/admin/campaigns_statuses?orderBy=pacing_impression&order_direction=<?php echo $order_direction;?>"><label class="float-right ordering">Goal</label></a></th>
                <th class="fit" scope="col"><a  href="/admin/campaigns_statuses?orderBy=imp_per_minute&order_direction=<?php echo $order_direction;?>"><label class="float-right ordering">Imps/m</label></a></th>
                <th class="fit" scope="col"><a  href="/admin/campaigns_statuses?orderBy=spent&order_direction=<?php echo $order_direction;?>"><label class="float-right ordering">Spent</label></a></th>
                <th class="fit" scope="col"><a  href="/admin/campaigns_statuses?orderBy=pacing_money&order_direction=<?php echo $order_direction;?>"><label class="float-right ordering">Budget</label></a></th>
                <th class="fit" scope="col"><a  href="/admin/campaigns_statuses?orderBy=spent_per_second&order_direction=<?php echo $order_direction;?>"><label class="float-right ordering">Spent/m</label></a></th>
                <th class="fit" scope="col"><a  href="/admin/campaigns_statuses?orderBy=clicks&order_direction=<?php echo $order_direction;?>"><label class="float-right ordering">Clicks</label></a></th>
                <th class="fit" scope="col"><a  href="/admin/campaigns_statuses?orderBy=clicks&order_direction=<?php echo $order_direction;?>"><label class="float-right ordering">CTR</label></a></th>
            </tr>
        </thead>
        <tbody>
            @foreach($data_response as $row_camp)
                <?php
                        if(!isset($row_camp['pacing_impression'])){ $row_camp['pacing_impression'] =0; }
                        if(!isset($row_camp['pacing_money'])){ $row_camp['pacing_money']==0; }

                    $allign = $row_camp['is_campaign'] ==true ? 'left' :'right';
                    if ($row_camp['pacing_impression']!= 0) {
                        $pacing_imp_percentage = $row_camp['impressions'] /  $row_camp['pacing_impression'] * 100;
                    } else {
                        $pacing_imp_percentage = 0;
                    }

                    if ($row_camp['pacing_money']!= 0) {
                        $pacing_money_percentage =  $row_camp['spent'] /  $row_camp['pacing_money'] * 100;
                    } else {
                        $pacing_money_percentage =  0;
                    }

                    /**
                     * row colors
                     */
                    $color = 'light';
                    if($row_camp['status']==1){
                        $color = 'success';
                    }


                    if($row_camp['impressions']==0 && $row_camp['spent']==0){
                        $color = 'info';
                    }

                    if($row_camp['impressions']==0 && $row_camp['spent']==0){
                        $color = 'warning';
                    }

         
                    if($pacing_money_percentage >= 100 || $pacing_imp_percentage >= 100){
                        $color = 'danger';
                    }

                    $campaign_url = "/admin/campaigns/".$row_camp['id']."/edit";

                    $ctr = 0;
                    if($row_camp['clicks'] > 0 && $row_camp['impressions']>0){
                        $ctr = intval($row_camp['clicks']) / intval($row_camp['impressions']) * 100;
                    }

                ?>

                <tr id="campaign-{{$row_camp['id_to_show']}}"  data-status="<?php echo $row_camp['status']; ?>" data-key-search="<?php echo strtoupper($row_camp['key_search']); ?>" class="table-<?php echo  $color; ?> searchrow-campaign">
                    <th scope="row"><a href="{{$campaign_url}}" target="_blank"> <label class="link float-<?php echo  $allign; ?>"><?php echo $row_camp['id_to_show']; ?></label></a></th>
                    <td><label class="campaigns"> <?php echo $row_camp['name'];?></label></td>
                    <td><label class="float-right"><?php echo $row_camp['impressions']; ?></label></td>
                    <td>
                        @if($row_camp['pacing_impression']>0)
                            <label class="float-right period"><?php echo $row_camp['pacing_impression'] . '  ['.number_format($pacing_imp_percentage, 2, '.', ' ').'%]'; ?></label>
                            <label class="float-right comma" style="display: none;"><?php echo $row_camp['pacing_impression'] . '  ['.number_format($pacing_imp_percentage, 2, ',', ' ').'%]'; ?></label>
                        @endif
                    </td>
                    <td><label id="campaign-impressions-{{$row_camp['id_to_show']}}" class="float-right">-</label></td>
                    <td>
                        <label class="float-right period"><?php echo '$'.number_format($row_camp['spent'], 2, '.', ' ') ; ?></label>
                        <label class="float-right comma" style="display: none;"><?php echo '$'.number_format($row_camp['spent'], 2, ',', ' ') ; ?></label>
                    </td>
                    <td> 
                        @if($row_camp['pacing_money']>0)
                            <label class="float-right period"><?php echo '$'.number_format($row_camp['pacing_money'], 2, '.', ' ');   echo '  ['.number_format($pacing_money_percentage, 2, '.', ' ').'%]'; ?></label>
                            <label class="float-right comma" style="display: none;"><?php echo '$'.number_format($row_camp['pacing_money'], 2, ',', ' ');   echo '  ['.number_format($pacing_money_percentage, 2, ',', ' ').'%]'; ?></label>
                        @endif
                    </td>
                    <td><label  id="campaign-spent-{{$row_camp['id_to_show']}}" class="float-right">-</label></td>
                    <td><label class="float-right">{{$row_camp['clicks']}}</label></td>
                    <td>
                        <label class="float-right period"><?php echo number_format($ctr, 2, '.', ' ')."%";  ?></label>
                        <label class="float-right comma" style="display: none;"><?php echo number_format($ctr, 2, ',', ' ')."%";  ?></label>
                    </td>
                </tr>

                    @foreach($row_camp['strategies'] as $row)
                    <?php 
                        $allign = $row['is_campaign'] ==true ? 'left' :'right';
                        if ($row['pacing_impression']!= 0) {
                            $pacing_imp_percentage = $row['impressions'] /  $row['pacing_impression'] * 100;
                        } else {
                            $pacing_imp_percentage = 0;
                        }

                        if ($row['pacing_money']!= 0) {
                            $pacing_money_percentage =  $row['spent'] /  $row['pacing_money'] * 100;
                        } else {
                            $pacing_money_percentage =  0;
                        }


                        $color = 'light';
                        if($row['status'] == 1 ) {
                            $color = 'info';
                        }

                        if($row['impressions']==0 && $row['spent']==0){
                            $color = 'warning';
                        }
  

                        if($pacing_money_percentage >= 100 || $pacing_imp_percentage >= 100){
                            $color = 'danger';
                        }

                        if($row['status'] == 0 ) {
                            $color = 'secondary';
                        }

                        $id="";
                        $strategy_url = "/admin/strategies/".$row['id']."/edit";
                        $ctr = 0;
                        if($row['clicks'] > 0 && $row['impressions']>0){
                            $ctr = intval($row['clicks']) / intval($row['impressions']) * 100;
                        }

                        if(strlen($row['name']) > 60){
                            $strategy_name = substr($row['name'], 0, 60) . "...";
                        }else {
                            $strategy_name = $row['name'];
                        }
                    ?>

                    <tr  id="strategy-{{$row['id_to_show']}}" data-status="<?php echo $row['status']; ?>" data-key-search="<?php echo strtoupper($row['key_search']); ?>" class="table-<?php echo  $color; ?> searchrow-strategy">
                        <th scope="row"><a href="{{$strategy_url}}" target="_blank"><label class="link float-<?php echo  $allign; ?>"><?php echo $row['id_to_show']; ?></label></a></th>
                        <td>@if($row['is_campaign'] == false)<label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;</label>@endif<label class="strategies"> <?php echo $strategy_name;?></label></td>
                        <td><label class="float-right"><?php echo $row['impressions']; ?></label></td>
                        <td>
                            @if($row['pacing_impression']>0)
                                <label class="float-right period"><?php echo $row['pacing_impression'] . '  ['.number_format($pacing_imp_percentage, 2, '.', ' ').'%]'; ?></label>
                                <label class="float-right comma" style="display: none;" ><?php echo $row['pacing_impression'] . '  ['.number_format($pacing_imp_percentage, 2, ',', ' ').'%]'; ?></label>
                            @endif
                        </td>
                        <td><label id="strategy-impressions-{{$row['id_to_show']}}" class="float-right">-</label></td>
                        <td>
                            <label class="float-right period"><?php echo '$'.number_format($row['spent'], 2, '.', ' ') ; ?></label>
                            <label class="float-right comma" style="display: none;"><?php echo '$'.number_format($row['spent'], 2, ',', ' ') ; ?></label>
                        </td>
                        <td>
                            @if($row['pacing_money']>0)
                                <label class="float-right period"><?php echo '$'.number_format($row['pacing_money'], 2, '.', ' ');   echo '  ['.number_format($pacing_money_percentage, 2, '.', ' ').'%]'; ?></label>
                                <label class="float-right comma" style="display: none;"><?php echo '$'.number_format($row['pacing_money'], 2, ',', ' ');   echo '  ['.number_format($pacing_money_percentage, 2, ',', ' ').'%]'; ?></label>
                            @endif
                        </td>
                        <td><label id="strategy-spent-{{$row['id_to_show']}}" class="float-right">-</label></td>
                        <td><label class="float-right">{{$row['clicks']}}</label></td>
                        <td>
                            <label class="float-right period"><?php echo number_format($ctr, 2, '.', ' ')."%"; ?></label>
                            <label class="float-right comma" style="display: none;"><?php echo number_format($ctr, 2, ',', ' ')."%"; ?></label>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
<script>
        const idShown = <?php echo json_encode($ids_shown); ?>
</script>
<script src="{{ asset('js/campaigns_statuses.js') }}"></script>
</body>
</html>
<?php //dd($data_response); ?>