<script>
    (function () {
       var t=0;
        for(i=0;i<7;i++){
            setTimeout(function(){
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "https://pixels.resetdigital-test.co/tos?time="+t, true);
                xhttp.send();
                t++;
            }, 10000*i);
        }
        t=0;
    } ());
</script>