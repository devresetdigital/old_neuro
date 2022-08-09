<div id="creative" class="tab-pane fade in">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="card">
                <h5 class="card-header">Advertiser</h5>
                <select id="advertiser_creatives" class="form-control advertiser-for-concepts" style="position: absolute;
    width: 30%;
    top: 0.4vw;
    right: 23vw;">
                <option value="0">All</option>
                @foreach($advertisers as $advertiser)
                    <option  {{($dataTypeContent->Campaign->advertiser_id == $advertiser->id) ? 'selected' : ''}}         
                          value="{{$advertiser->id}}">{{$advertiser->name}}</option>
                @endforeach                
                </select>   
                <input class="form-control search-concept" id="search" type="text" placeholder="Sub-Search" style ="position: absolute;
    width: 50%;
    top: 0.4vw;
    right: 0.5vw;">

                <div class="card-body p-0" style="min-height: 40em;" id="concepts_list_div" ondrop="drop(event); selectedConcepts();" ondragover="allowDrop(event)">
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="card">
                <h5 class="card-header">Selected Concepts (Drag & Drop from Concepts List)</h5>
                <div class="card-body p-0" style="min-height: 40em;" id="concepts_list_selected_div" ondrop="drop(event);selectedConcepts();" ondragover="allowDrop(event)">
                @foreach($selected_concepts as $sconcept)
                        <li data-list="selected" class="list-group-item d-flex justify-content-between align-items-center" draggable="true" ondragstart="drag(event)" id="concept-{{ $sconcept["concept"]->id  }}" style="cursor: grab">
                        <a href="/admin/concepts/{{ $sconcept["concept"]->id  }}/edit" target="_blank" rel="noopener noreferrer">{{ $sconcept["concept"]->id  }} </a> - {{ $sconcept["concept"]->name  }}
                            <span class="badge badge-primary badge-circle"><a style="color: white" href="/admin/creatives?concept_id={{$sconcept["concept"]->id}}" target="_blank">{{ count($sconcept["concept"]["creatives"]) }}</a></span>
                        </li>
                 @endforeach
                </div>
            </div>
        </div>
    </div>
    <input name="selected_concepts" id="selected_concepts" value="" type="hidden">
</div>


<script>
const loadConcepts = async (id) => {
    let params = {
        id:    id,
        _token: '{{ csrf_token() }}'
    }


        await $.post("/api/concepts_by_advertiser/", params, function (data) {

        $('#concepts_list_div').empty();
        for (const iterator of data) {
            if($('#concept-'+iterator.id).length == 0){
                let html = `<li data-search="${iterator.name.toUpperCase()}" data-list="allowed" class=" search-li list-group-item d-flex justify-content-between align-items-center" draggable="true" ondragstart="drag(event)" id="concept-${iterator.id}" style="cursor: grab">
                            <a href="/admin/concepts/${iterator.id}/edit" target="_blank" rel="noopener noreferrer">${iterator.id} </a> - ${iterator.name}
                            <span class="badge badge-primary badge-circle" style="cursor: pointer"><a style="color: white" href="/admin/creatives?concept_id=${iterator.id}" target="_blank">${iterator.creatives_count}</a></span>
                            </li>`;
                $('#concepts_list_div').append(html);
            }
        }
    });

}



</script>