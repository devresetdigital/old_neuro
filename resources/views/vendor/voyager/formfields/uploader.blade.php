 <script>
    window.addEventListener("load", function(){
        $('#upToggle').hide();
        $("#newFileContainer").hide();

        $('#uploadButton').click(()=>{
            $('#uplist').empty();
            $('#upbrowse').prop('disabled', false);
        })


      // (A) NEW FLOW OBJECT
      var flow = new Flow({
        target: '/api/upload',
        chunkSize: 1024*1024, // 1MB
        singleFile: true,
        uploadMethod: 'post'
      });

      if (flow.support) {
        // (B) ASSIGN BROWSE BUTTON
        flow.assignBrowse(document.getElementById('upbrowse'));
        // OR DEFINE DROP ZONE
        // flow.assignDrop(document.getElementById('updrop'));

        // (C) ON FILE ADDED
        flow.on('fileAdded', function(file, event){
          // console.log(file, event);
          let fileslot = document.createElement("div");
          fileslot.id = file.uniqueIdentifier;
          fileslot.innerHTML = `${file.name} (${file.size}) - <strong>0%</strong>`;
          document.getElementById("uplist").appendChild(fileslot);
        });
        
        // (D) ON FILE SUBMITTED (ADDED TO UPLOAD QUEUE)
        flow.on('filesSubmitted', function(array, event){
          // console.log(array, event);
          flow.upload();
        });

        // (E) ON UPLOAD PROGRESS
        flow.on('fileProgress', function(file, chunk){
          // console.log(file, chunk);
          let progress = (chunk.offset + 1) / file.chunks.length * 100;inputvalue
          progress = progress.toFixed(2) + "%";

            if(progress != '100.00%'){
                $('#upToggle').show();
            }else{
                $('#upToggle').hide();
            }
            
            $('#upbrowse').prop('disabled', true);
            $('.tab-controllers').prop('disabled', true);

            

          // QUERYSELECTOR NOT WORKING WITH "-" IN ID...
          // document.querySelector(`${file.uniqueIdentifier} strong`)
          let fileslot = document.getElementById(file.uniqueIdentifier);
          fileslot = fileslot.getElementsByTagName("strong")[0];
          fileslot.innerHTML = progress;
        });
        
        // (F) ON UPLOAD SUCCESS
        flow.on('fileSuccess', function(file, message, chunk){
            
          let response = JSON.parse(message);
          let inputval= []
          inputval.push(response);
          document.getElementById("inputvalue").value = JSON.stringify(inputval);

          let fileslot = document.getElementById(file.uniqueIdentifier);
          fileslot = fileslot.getElementsByTagName("strong")[0];
          fileslot.innerHTML = "DONE";
          $('#upToggle').hide();
          $('#upbrowse').prop('disabled', false);
          $('.tab-controllers').prop('disabled', false);
          $("#newFile").empty();
                    $("#newFile").append(response.original_name);
                    $("#newFileContainer").show();
                    $('#myModal').modal('hide');
        });
        
        // (G) ON UPLOAD ERROR
        flow.on('fileError', function(file, message){inputvalue
          fileslot = fileslot.getElementsByTagName("strong")[0];
          fileslot.innerHTML = "ERROR";
        });
        
        // (H) PAUSE/CONTINUE UPLOAD
        document.getElementById("upToggle").addEventListener("click", function(){
          if (flow.isUploading()) { flow.pause(); }
          else { flow.resume(); }
        });
      }

    $( "#uploadPublicLink" ).click(function() {

        let publicUrl=  $( "#publicUrl" ).val();
        if(publicUrl != '' && publicUrl != lastPublcUrl){
            lastPublcUrl = publicUrl;
            $('.tab-controllers').prop('disabled', true);
            let url = '/api/publicLinkUpload';

            let postdata = {
                path: publicUrl
            };

            $.ajax({
                type: "POST",
                url: url,
                data: postdata,
                success: function(data)
                {
                    let fileslot = document.createElement("div");
                    fileslot.id = data.original_name;
                    fileslot.innerHTML = `${data.original_name} (${data.size}) - <strong>DONE</strong>`;
                    document.getElementById("uplist").appendChild(fileslot);
                    $("#publicUrl").val('');

                    let response = data;
                    let inputval=[]                    
                    inputval.push(response);
                    document.getElementById("inputvalue").value = JSON.stringify(inputval);
                    $('.tab-controllers').prop('disabled', false);

                    $("#newFile").empty();
                    $("#newFile").append(data.original_name);
                    $("#newFileContainer").show();
                    $('#myModal').modal('hide');
                }
            });

        }
    });
    });
let lastPublcUrl ='';
</script>

@if(isset($dataTypeContent->{$row->field}))
    @if(json_decode($dataTypeContent->{$row->field}))
        @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
             <br/><a class="fileType" download  href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}"> {{ $file->original_name ?: '' }} </a>
        @endforeach
    @else
        <a class="fileType" download href="{{ Storage::disk(config('voyager.storage.disk'))->url($dataTypeContent->{$row->field}) }}"> Download </a>
    @endif
@endif
    <br>
    <input @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif type="hidden" name="{{ $row->field }}" id="inputvalue"  value="">  
    <button type="button" class="btn btn-primary btn-lg" id="uploadButton" data-toggle="modal" data-target="#myModal">
        Upload
    </button>
    <p>(To upload more than one asset please zip them into a single file)</p>
    <Label id="newFileContainer">New file: <b id="newFile">archivo nuevo</b></Label>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" style="width: 80%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">File Uploader</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="tab-controllers active" role="presentation" ><a class="tab-controllers" href="#local" aria-controls="local" role="tab" data-toggle="tab">Local file</a></li>
                            <li class="tab-controllers" role="presentation"><a class="tab-controllers" href="#external" aria-controls="external" role="tab" data-toggle="tab">External file</a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="row" style="padding: 20px;
                                                border-radius: 4px;
                                                border: 1px solid #f1f1f1;
                                                margin: 0;
                                                padding-bottom: 0;">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="local">
                                    <h5>Select a file from your Computer</h5>
                                    <!-- UPLOAD BUTTON -->
                                    <input type="button" class="btn btn-primary" id="upbrowse" value="Browse"/>
                                    <input type="button" class="btn btn-primary" id="upToggle" value="Pause/Continue"/></div>
                            <div role="tabpanel" class="tab-pane" id="external">
                                <h5>Upload from external link</h5>
                                <div style="display:flex">
                                    <input type="text" id="publicUrl" class="form-control" placeholder="Public url" aria-label="Public url" aria-describedby="basic-addon2">
                                    <button class="btn btn-primary" style="margin:0; margin-left:1em;" id="uploadPublicLink" type="button">Upload</button>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin:0; padding:0;" id="uplist"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-primary">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->