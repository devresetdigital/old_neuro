<?php
$bidder_output= [];
for ($i=1; $i<20; $i++){
    $index = str_pad($i, 2, "0", STR_PAD_LEFT);
    $url="http://b-us-east".$index.".resetdigital.co:9999/Status.json";
    $ch = curl_init();
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL,$url);
    // set TIMEOUT
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);//time in seconds
    // Execute
    $result=curl_exec($ch);
    // Closing
    curl_close($ch);

    $result = json_decode($result, true);
    $not_response =[];
    if($result !== null && array_key_exists('Id', $result)){
        $bidder_output[$i] = [
                "get_response" =>true,
                "Number"=> $index,
                "Id" => $result["Id"],
                "Version" => $result["Version"],
                "Enabled" => $result["Enabled"],
                "NotPaused" => $result["NotPaused"],
                "RunTime" => $result["RunTime"],
                "Qps" => $result["Performance"]["Qps"],
                "Bps" => $result["Performance"]["Bps"]
        ];
    }else{
        $not_response[$i] = [
            "response" =>$result,
            "Number"=> $index,
            ];
        $bidder_output[$i] = [
                "get_response" =>false,
                "Number"=> $index,
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bidder Statuses</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/darkly/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <h1>Bidder Statuses</h1>
    <h2 style="position: absolute;right: 3em; top: 0.4em;"><?php echo date("Y-m-d H:i:s");?></h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">Number</th>
            <th scope="col">Id</th>
            <th scope="col">Version</th>
            <th scope="col">Enabled</th>
            <th scope="col">NotPaused</th>
            <th scope="col">RunTime</th>
            <th scope="col">Qps</th>
            <th scope="col">Bps</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($bidder_output as $bidder):
            if($bidder['get_response']):
        ?>
                <tr class="<?php echo ($bidder['Enabled'] && $bidder['NotPaused'])? "table-success" : "table-warning"; ?>">
                    <th scope="row"><?php echo $bidder['Number']; ?></th>
                    <td><?php echo array_key_exists('Id', $bidder)? $bidder['Id']: ''; ?></td>
                    <td><?php echo array_key_exists('Version', $bidder)? $bidder['Version']: ''; ?></td>
                    <td><?php echo array_key_exists('Enabled', $bidder)? $bidder['Enabled']: ''; ?></td>
                    <td><?php echo array_key_exists('NotPaused', $bidder)? $bidder['NotPaused']: ''; ?></td>
                    <td><?php echo array_key_exists('RunTime', $bidder)? $bidder['RunTime']: ''; ?></td>
                    <td><?php echo array_key_exists('Qps', $bidder)? $bidder['Qps']: ''; ?></td>
                    <td><?php echo array_key_exists('Bps', $bidder)? $bidder['Bps']: ''; ?></td>
                </tr>

        <?php  else: ?>

                <tr class="table-danger">
                    <th scope="row"><?php echo $bidder['Number']; ?></th>
                    <td>Not Response</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>

        <?php  endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <h2>Request failed</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">Number</th>
            <th scope="col">response</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($not_response as $bidder):
          
        ?>
        <tr class="<?php echo ($bidder['Enabled'] && $bidder['NotPaused'])? "table-success" : "table-warning"; ?>">
            <th scope="row"><?php echo $bidder['Number']; ?></th>
            <td><?php echo json_encode($bidder['response']); ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
