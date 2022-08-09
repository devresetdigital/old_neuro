<?php

$url = "104.131.90.165:9000/logsearch?" . $_SERVER['QUERY_STRING'] . "&format=json";

$ch = curl_init();
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL, $url);
// set TIMEOUT
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);//time in seconds
// Execute
$result = curl_exec($ch);
// Closing
curl_close($ch);


$result = json_decode($result, true);
$logs_output = [];

$lines = 100;

if ($result !== null && count($result['array'])>0) {

    $lines = intval($result['lines']);
    $page = $result['offset'] / $lines;

    foreach ($result['array'] as $item) {
        $logs_output[] = [
            "id" => array_key_exists('id', $item) ? $item['id'] : '',
            "date" => substr($item['date'],0,19),
            "type" => $item['type'],
            "user_email" => $item['user_email'],
            "path" => $item['path'],
            "request" => $item['request'],
            "search" =>strtoupper( htmlspecialchars(json_encode($item['request']))."-".$item['path']."-".$item['type'] . "-" . $item['user_email'] . "-" . (array_key_exists('id', $item) ? $item['id'] : ''))
        ];
    }
} else {
    $logs_output[] = [
        "id" => '',
        "date" => '',
        "type" => 'NO RESULTS',
        "user_email" => 'NO RESULTS',
        "path" =>'',
        "request" => 'NO RESULTS',
        "search" =>''
    ];
    $result['total'] = 0;
    $result['offset'] = 0;
    $result['lines'] = 100;
    $page = 0;
}



$from = '' ;
$until = '' ;
if (array_key_exists('from', $_GET)){
    $aux = str_split($_GET['from'], 2);
    $from = $aux[0] . "/" . $aux[1] . "/" . $aux[2] . " " . $aux[3] . ":" . $aux[4] . ":" . $aux[5] ;
}
if (array_key_exists('until', $_GET)){
    $aux = str_split($_GET['until'], 2);
    $until = $aux[0] . "/" . $aux[1] . "/" . $aux[2] . " " . $aux[3] . ":" . $aux[4] . ":" . $aux[5] ;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/darkly/bootstrap.min.css">-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/spacelab/bootstrap.min.css">
    <link rel="stylesheet" href="./jquery.datetimepicker.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="./jquery.datetimepicker.js"></script>
    <script src="./saver.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js" integrity="sha512-Izh34nqeeR7/nwthfeE0SI3c8uhFSnqxV0sI9TvTcXiFJkMd6fB644O64BRq2P/LA/+7eRvCw4GmLsXksyTHBg==" crossorigin="anonymous"></script>
</head>
<body>
<style>
    .col-request {
        max-width: 30em;
        cursor: pointer;
    }
    .col-request>pre {
        overflow: hidden;
    }

    table {
        max-width: 100% !important;
    }

    .table-container {
        -webkit-box-flex: 0;
        -ms-flex: 0 0 97%;
        flex: 0 0 97%;
        max-width: 97%;
    }
    .table td.fit,
    .table th.fit {
        white-space: nowrap;
        width: 1%;
    }
.string { color: green; }
.number { color: darkorange; }
.boolean { color: blue; }
.null { color: magenta; }
.key { color: orchid; }

</style>
<div class="table-container">
    <h3 style="margin-left: 0.4em;">logs</h3>
    <nav style="margin-left: 1em;" class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="collapse navbar-collapse" id="navbarColor01">
            <input class="form-control mr-sm-2 w-25" type="text" name="wlid" value="<?php echo array_key_exists('wlid', $_GET) ? $_GET['wlid']: '' ?>" placeholder="Wl id">
            <input class="form-control mr-sm-2 w-25" type="text" name="id" value="<?php echo array_key_exists('id', $_GET) ? $_GET['id']: '' ?>" placeholder="Id">
            <input class="form-control mr-sm-2" type="text" name="type"  value="<?php echo array_key_exists('type', $_GET) ? $_GET['type']: '' ?>" placeholder="Type">
            <input class="form-control mr-sm-2 w-50" type="text" name="user_email"  value="<?php echo array_key_exists('user_email', $_GET) ? $_GET['user_email']: '' ?>" placeholder="User email">
            <input autocomplete="off" class="form-control mr-sm-2" type="text" id="datefrom" name="from"  value="<?php echo $from ?>" placeholder="From">
            <input autocomplete="off" class="form-control mr-sm-2" type="text" id="dateuntil" name="until"  value="<?php echo $until ?>" placeholder="Until">
            <select class="custom-select form-control mr-sm-2  w-25" name="lines">
                <option <?php echo ($lines==50)? 'selected':''; ?> value="50">50</option>
                <option <?php echo ($lines==100)? 'selected':''; ?>  value="100">100</option>
                <option <?php echo ($lines==150)? 'selected':''; ?>  value="150">150</option>
                <option <?php echo ($lines==250)? 'selected':''; ?> value="250">250</option>
                <option <?php echo ($lines==500)? 'selected':''; ?> value="500">500</option>
            </select>
            <button class="btn btn-secondary mr-sm-2" onclick="submitform(0)">Search</button>
            <input class="form-control w-100" id="search" type="text" placeholder="Sub-Search">
        </div>
    </nav>
    <table class="table table-striped" style="margin: 1em">
        <thead>
        <tr>
            <th class="fit" scope="col">Date</th>
            <th class="fit" scope="col">Id</th>
            <th class="fit" scope="col">Type</th>
            <th class="fit" scope="col">User email</th>
            <th class="fit" scope="col">Path</th>
            <th scope="col">Request</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs_output as $key => $log): ?>
            <tr class=" search-log" data-key-search="<?php echo $log['search']?>">
                <th class="fit" scope="row"><?php echo $log['date']; ?></th>
                <th class="fit"><?php echo $log['id']; ?></th>
                <td class="fit"><?php echo $log['type']; ?></td>
                <td class="fit"><?php echo $log['user_email']; ?></td>
                <td><?php echo $log['path']; ?></td>
                <td onclick="showModal(<?php echo $key; ?>)" class="col-request">
                    <pre id="request-<?php echo $key; ?>"><?php echo htmlspecialchars(json_encode($log['request'])); ?></pre>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="w-100" >
        <ul class="pagination float-right">
            <li class="page-item " <?php echo ($page==0) ?'hidden':''?>>
                <a  onclick="submitform(0)" class="page-link" href="#">&laquo;&laquo;</a>
            </li>
            <li class="page-item <?php echo ($page==0 ?'disabled':'')?>">
                <a  onclick="submitform(<?php echo $page - 1; ?>)" class="page-link" href="#">&laquo;</a>
            </li>
            <?php
            $x=0;
            for($i=$page; $i<=intval($result['total']/$result['lines']); $i++ ){
                if ($x<10){
                    echo '<li class="page-item '. ($x==0 ? 'disabled' : null)   .'"><a  '. ($x==0 ? '' : 'onclick="submitform('.$i.')"')   .' class="page-link" href="#">'.$i.'</a></li>';
                    $x++;
                }else{
                    echo '<li class="page-item"><a class="page-link" href="#">...</a></li>';
                    break;
                }
            }
            ?>
            <li class="page-item <?php echo ($page==intval($result['total']/$result['lines']) ?'disabled':'')?>">
                <a  onclick="submitform(<?php echo $page + 1; ?>)" class="page-link" href="#">&raquo;</a>
            </li>
            <li class="page-item " <?php echo ($page==intval($result['total']/$result['lines']) ?'hidden':'')?>>
                <a  onclick="submitform(<?php echo intval($result['total']/$result['lines']); ?>)" class="page-link" href="#">&raquo;&raquo;</a>
            </li>
        </ul>
    </div>
</div>
<div class="modal " id="myModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="json"></pre>
                <input type="hidden" name="" id="requestShowed">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="saveAsTxt()">Save as Txt</button>
                <button type="button" class="btn btn-secondary" onclick="copyToClipboard()">Copy to Clipboard</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    let startdate;
    const showModal = (key) => {
        $('#json').empty();
        let json = JSON.parse($('#request-'+key).text());
        json = JSON.stringify(json, undefined, 4);
        $('#requestShowed').val(json);
        $('#json').append(syntaxHighlight(json));
        $('#myModal').modal('show');
    }

    $(document).ready(function () {

        $.datetimepicker.setDateFormatter({
            parseDate: function (date, format) {
                var d = moment(date, format);
                return d.isValid() ? d.toDate() : false;
            },
            formatDate: function (date, format) {
                return moment(date).format(format);
            },
        });

        startdate = $('#datefrom').datetimepicker({
            format:'YY/MM/DD/ HH:00:00',    // added formatTime
            formatTime: 'HH:mm',
            lang: 'en',
            theme:'dark',
        });
        $('#dateuntil').datetimepicker({
            format:'YY/MM/DD HH:59:59',    // added formatTime
            formatTime: 'HH:mm',
            lang: 'en',
            theme:'dark'
        });

        $('#search').keyup(function (e) {
            $('.search-log').hide();
            let key = this.value.toUpperCase();
            if(key==''){
                clearSearch();
            }else{
                let result = $("tr[data-key-search*='"+key+"']");
                if(result.length>0){
                    result.show();
                }
            }
        });
    });

    const clearSearch = () => {
        $('#search').val('');
        $('.search-log').show();

    }

    const saveAsTxt = () => {
        let blob = new Blob([$('#requestShowed').val()],
                { type: "text/plain;charset=utf-8" });
            saveAs(blob, "request.txt");
    }


    const formatToSend = (date) => {
        return date.replaceAll('/','').replaceAll(':', '').replaceAll(' ','');
    }

    const copyToClipboard = () => {
        var range = document.createRange();
        range.selectNode(document.getElementById("json"));
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges();// to deselect
    }


    const submitform = (page) => {
        let id = $('input[name ="id"]').val();
        let wlid = $('input[name ="wlid"]').val();
        let type = $('input[name ="type"]').val();
        let user_email = $('input[name ="user_email"]').val();
        let lines = $('select[name="lines"] option').filter(':selected').val()

        let from = $('input[name ="from"]').val();
        from = formatToSend(from);
        let until = $('input[name ="until"]').val();
        until = formatToSend(until);

        let query = '?wlid='+wlid+'&id='+id+'&type='+type+"&user_email="+user_email+"&from="+from+"&until="+until+"&page="+page*lines+","+lines ;
        window.location.href =window.location.origin + window.location.pathname + query;

    }


    const syntaxHighlight = (json) => {
    if (typeof json != 'string') {
         json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}

</script>
</body>
</html>
