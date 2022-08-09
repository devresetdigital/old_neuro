<div id="tags" class="tab-pane fade in">
    <div>External Tags</div>
    <br>
    <div>
        @php
        $prefix = $_ENV["WL_PREFIX"]*1000000;
        $goal_values = explode(",",$dataTypeContent->goal_values);
        @endphp
        <div id="tags-list" style="border: 1px #c0c0c0; width: 50%; height: 400px; overflow-y:scroll; font-size: 12px; word-wrap: break-word;">
            @foreach($selected_concepts as $concepts)
                @foreach($concepts->concept->creatives as $creative)
                    @if($creative->creative_type_id == 1)
                        <strong> Creative:</strong> {{$creative->name}} [{{ ($creative->id + $prefix) }}] <br>
                        <strong> Concept:</strong> {{$concepts->concept->name}} [{{($concepts->concept->id+$prefix)}}]<br>
                        <strong> Strategy:</strong> {{$dataTypeContent->name}} [{{($dataTypeContent->getKey()+$prefix)}}]<br><br>

                        <textarea id="script-{{ $creative->id }}" class="script" name="{{$creative->name}}" id="script"
                            style="width: 100%;">&lt;script src=&quot;https://data.resetdigital.co/evts?S0B=1&amp;R0E=1&amp;R0M={{ $goal_values[3] }}_{$PRICE}&amp;R0A={{ ($dataTypeContent->campaign_id+$prefix) }}_{{ ($creative->id + $prefix) }}_{{ ($dataTypeContent->getKey()+$prefix) }}_{$TIMESTAMP}&amp;R0P=resetio_{$PUBLISHER}_{$DOMAIN}_{$CHANNEL}_{$DEAL}_banner&amp;R0L=*_*_*_*_*&amp;R0D=*_*_*_*_*_{$IFA}&amp;R0B=*_*_*&quot; type=&quot;text/javascript&quot;&gt;&lt;/script&gt;
                        </textarea>
                        <div class="text-right">
                            <a href="#" class="btn btn-primary copyToClipboard creative-{{ $creative->id }}"
                                role="button">Copy to clipboard</a>
                            <a href="#" class="btn btn-secondary download creative-{{ $creative->id }}"
                                role="button">Download</a>
                        </div>
                        <br><br>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>

</div>