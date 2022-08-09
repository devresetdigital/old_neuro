<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vast Tester</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vast-player@0.2/dist/vast-player.min.js"></script>
</head>
<body>
<style>
    body{
        background-image: url(emails/images/background.png);
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    .text-white{
        color: white;
        margin-bottom: 4em;
    }
    .logo {
        background-image: url(emails/images/output-onlinepngtools.png);
        background-position: center center;
        background-repeat: no-repeat;
        height: 8em;
        background-size: contain;
        position: absolute;
        top: 1em;
        left: 1em;
        z-index: 999999999;
        width: 8em;
    }

</style>
<?php
    $id = isset($_GET['id']) ? $_GET['id'] : null;
?>
<script>        
    var pause1=false;
    let player1=null;
</script>


<div class="">
    <div class="col-sm-12 text-white text-center">
        <h1>Vast Preview</h1>
        <div class="logo"></div>
    </div>
    <div class="col-sm-3"></div>
    <div class="col-sm-6 text-center">
    <?php  if($id!=null): ?>
    <div class="thumbnail">
        <div  id="container_1"></div> 
        <div class="caption">
            <a href="#" onclick="stop_preview(1)" class="btn btn-default"><i class="glyphicon glyphicon-stop"></i></a>
            <a href="#" onclick="play(1)" class="btn btn-default"><i class="glyphicon glyphicon-play"></i></a>
            <a href="#" onclick="pause(1)" class="btn btn-default"><i class="glyphicon glyphicon-pause"></i></a>
            <br><br>
        </div>
    </div>
    <script>
        player1 = new window.VASTPlayer(document.getElementById('container_1'));
        player1.load('https://data.resetdigital.co/evts?S0B=1&R0E=1&R0M=10_15&R0A=1000048_<?php echo $id ?>_1001117_1627360746&R0P=resetio_12345678_TEST.COM_SITE_*_banner&R0L=*_*_*_*_*&R0D=*_*_*_*_*_*&R0B=*_*_*');
    </script>          

<?php else: ?>
    <textarea name="tag_code" id="tag_code"  style="width: 70%; height: 150px;"></textarea>
    <br><button class="btn btn-primary" type="button" onclick="previewVast();">Preview Ad</button>
    <div class="modal modal_multi" id="vastPreview">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-info"></i>Vast Preview</h4>
                </div>
                <div class="modal-body">
                    <div  id="container_1"></div> 
                    <a href="#" onclick="stop(1)" class="btn btn-default"><i class="glyphicon glyphicon-stop"></i></a>
                    <a href="#" onclick="play(1)" class="btn btn-default"><i class="glyphicon glyphicon-play"></i></a>
                    <a href="#" onclick="pause(1)" class="btn btn-default"><i class="glyphicon glyphicon-pause"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>


    </div>
    <div class="col-sm-3"></div>
</div>



<script>

        /**
         * 
         * vast preview markup
         */

        const play = (id) =>{
            if(window['pause'+id] == false ){
                switch (id) {
                    case 1:
                        player1.startAd();
                        break;
                }
            }else{
                switch (id) {
                    case 1:
                        pause1=false;
                        player1.resumeAd();
                        break;
                }
            }
            
        }
        const stop = (id) =>{
            previewVast();
        }
        const stop_preview = (id) =>{
                    switch (id) {
                        case 1:
                            player1.stopAd();
                            player1.load('https://data.resetdigital.co/evts?S0B=1&R0E=1&R0M=10_15&R0A=1000048_<?php echo $id ?>_1001117_1627360746&R0P=resetio_12345678_TEST.COM_SITE_*_banner&R0L=*_*_*_*_*&R0D=*_*_*_*_*_*&R0B=*_*_*');
                            break;
                    }
                }
        const pause = (id) =>{
            switch (id) {
                case 1:
                    pause1=true;
                    player1.pauseAd();
                    break;
            }
        }
        let previewVast = async () => {
            $('#container_1').empty();
            let key=null;
            await $.post("/api/save_vast_markup",
            {
                markup: $('#tag_code').val()
            },function(data){
                key = data;
                return data;
            },'json');
          
            player1 = new window.VASTPlayer(document.getElementById('container_1'));
            player1.load('/api/vast_preview/'+key);
            $('#vastPreview').modal('show');
            return true;
        }

        $('#vastPreview').on('hidden.bs.modal', function () {
            $('#container_1').empty();
        })
       
    </script>
</body>
</html>



