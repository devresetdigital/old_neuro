<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<div id="details" class="tab-pane fade in active">
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<!-- Adding / Editing -->
    @php
        $dataTypeRows = $dataType->{(!is_null($dataTypeContent->getKey()) ? 'editRows' : 'addRows' )};
    @endphp

    @foreach($dataTypeRows as $row)
    <!-- GET THE DISPLAY OPTIONS -->
        @php
            $options = json_decode($row->details);
            $display_options = isset($options->display) ? $options->display : NULL;
        @endphp
        @if ($options && isset($options->legend) && isset($options->legend->text))
            <legend class="text-{{$options->legend->align or 'center'}}" style="background-color: {{$options->legend->bgcolor or '#f0f0f0'}};padding: 5px;">{{$options->legend->text}}</legend>
        @endif
        @if ($options && isset($options->formfields_custom))
            @include('voyager::formfields.custom.' . $options->formfields_custom)
        @else
            @php
                $pacing_monetary_values = explode(",",$dataTypeContent->pacing_monetary);
                $pacing_impression_values = explode(",",$dataTypeContent->pacing_impression);
                $pacing_frequency_values = explode(",",$dataTypeContent->frequency_cap);
                $goal_values = explode(",",$dataTypeContent->goal_values);
            @endphp
            <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width or 5 }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                {{ $row->slugify }}
                <label for="name">{{ $row->display_name }}</label>
                @include('voyager::multilingual.input-hidden-bread-edit-add')
                @if($row->type == 'relationship')
                    @include('voyager::formfields.relationship')
                @else
                        @if($row->field == "pacing_monetary")
                        <div class="pacing-container" style="width: 33%;">
                            <div style="float: left; margin-right: 5px;">
                            <select id="m_type" name="m_type" onchange="changePacingMonetary(); checkDisabled();" class="form-control" style="width:10em; display:inline;">
                                <option {{ isset($pacing_monetary_values[0]) && $pacing_monetary_values[0] == 3 ? 'selected' : '' }} value="3">DISABLED</option>
                                <option {{ isset($pacing_monetary_values[0]) && $pacing_monetary_values[0] == 1 ? 'selected' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'selected') }} value="1">EVEN</option>
                                <option {{ isset($pacing_monetary_values[0]) && $pacing_monetary_values[0] == 2 ? 'selected' : '' }} value="2">ASAP</option>

                            </select>
                            </div>
                            <div id="pmonetary_values">
                                $
                                <input id="m_amount" name="m_amount" type="text" class="form-control" style="width:12em; display:inline;" onkeyup="changePacingMonetary()" value="{{ isset($pacing_monetary_values[1]) ? $pacing_monetary_values[1] : '' }}">
                                per Day
                                <select id="m_stype" name="m_stype" onchange="changePacingMonetary()" class="form-control" style="width:6em; display:inline; visibility: hidden;">
                                    <option {{ isset($pacing_monetary_values[2]) && $pacing_monetary_values[2] == 2 ? 'selected' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'selected') }} value="2">Day</option>
                                </select>
                                <input type="hidden" id="pacing_monetary" name="pacing_monetary" value="">
                            </div>
                        </div>
                        @elseif($row->field == "pacing_impression")
                            <div class="pacing-container" style="width: 33%;">
                                <div style="float: left; margin-right: 5px;">
                                <select id="i_type" name="i_type" onchange="changePacingImpression(); checkDisabled();" class="form-control" style="width:10em; display:inline;">
                                    <option {{ isset($pacing_impression_values[0]) && $pacing_impression_values[0] == 3 ? 'selected' : '' }} value="3">DISABLED</option>
                                    <option {{ isset($pacing_impression_values[0]) && $pacing_impression_values[0] == 1 ? 'selected' : '' }} value="1" {{ (!is_null($dataTypeContent->getKey()) ? '' : 'selected') }}>EVEN</option>
                                    <option {{ isset($pacing_impression_values[0]) && $pacing_impression_values[0] == 2 ? 'selected' : '' }} value="2">ASAP</option>

                                </select>
                                </div>
                                <div id="pimpression_values">
                                <input id="i_amount" name="i_amount" type="text" class="form-control" style="width:12em; display:inline;" onkeyup="changePacingImpression()" value="{{ isset($pacing_impression_values[1]) ? $pacing_impression_values[1] : '' }}">
                                per Day
                                <select id="i_stype" name="i_stype" onchange="changePacingImpression()" class="form-control" style="width:6em; display:inline; visibility: hidden">
                                    <option {{ isset($pacing_impression_values[2]) && $pacing_impression_values[2] == 2 ? 'selected' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'selected') }} value="2">Day</option>
                                </select>
                                <input type="hidden" id="pacing_impression" name="pacing_impression" value="">
                                </div>
                            </div>
                        @elseif($row->field == "frequency_cap")
                        <div class="pacing-container" style="width: 33%;">
                            <div style="float: left; margin-right: 5px;">
                            <select id="f_type" name="f_type" onchange="changefrequencyCap();checkDisabled()" class="form-control" style="width:10em; display:inline;">
                                <option {{ isset($pacing_frequency_values[0]) && $pacing_frequency_values[0] == 2 ? 'selected' : '' }} value="2">DISABLED</option>
                                <option {{ isset($pacing_frequency_values[0]) && $pacing_frequency_values[0] == 1 ? 'selected' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'selected') }}  value="1">EVEN</option>

                            </select>
                            </div>
                            <div id="frequency_values">
                            <input id="f_amount" name="f_amount" type="text" class="form-control" style="width:12em; display:inline;"onkeyup="changefrequencyCap()" value="{{ isset($pacing_frequency_values[1]) ? $pacing_frequency_values[1] : '' }}">
                            per Day
                            <select id="f_stype" name="f_stype" onchange="changefrequencyCap()" class="form-control" style="width:6em; display:inline; visibility: hidden">
                                <option {{ isset($pacing_frequency_values[2]) && $pacing_frequency_values[2] == 2 ? 'selected' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'selected') }} value="2">Day</option>
                            </select>
                            <input type="hidden" id="frequency_cap" name="frequency_cap" value="">
                            </div>
                        </div>
                        <br>
                    <!--
                        <label for="name">Hours</label>
                        <div class="pacing-container" style="width: 33%;">
                            <strong>AM</strong> &nbsp; &nbsp; &nbsp;<input name="hours[]" type="checkbox" checked value="1"> 1 <input name="hours[]" type="checkbox" checked value="2"> 2 <input name="hours[]" type="checkbox" checked value="1"> 3 <input name="hours[]" type="checkbox" checked value="2"> 4 <input name="hours[]" type="checkbox" checked value="1"> 5 <input name="hours[]" type="checkbox" checked value="2"> 6
                            <input name="hours[]" type="checkbox" checked value="1"> 7 <input name="hours[]" type="checkbox" checked value="2"> 8 <input name="hours[]" type="checkbox" checked value="1"> 9 <input name="hours[]" type="checkbox" checked value="2"> 10 <input name="hours[]" type="checkbox" checked value="1"> 11 <input name="hours[]" type="checkbox" checked value="2"> 12 &nbsp;&nbsp;&nbsp;<strong>AM</strong>
                            <br>
                            <strong>PM</strong> &nbsp; &nbsp; &nbsp;<input name="hours[]" type="checkbox" checked value="1"> 1 <input name="hours[]" type="checkbox" checked value="2"> 2 <input name="hours[]" type="checkbox" checked value="1"> 3 <input name="hours[]" type="checkbox" checked value="2"> 4 <input name="hours[]" type="checkbox" checked value="1"> 5 <input name="hours[]" type="checkbox" checked value="2"> 6
                            <input name="hours[]" type="checkbox" checked value="1"> 7 <input name="hours[]" type="checkbox" checked value="2"> 8 <input name="hours[]" type="checkbox" checked value="1"> 9 <input name="hours[]" type="checkbox" checked value="2"> 10 <input name="hours[]" type="checkbox" checked value="1"> 11 <input name="hours[]" type="checkbox" checked value="2"> 12 &nbsp;&nbsp;&nbsp;<strong>PM</strong>
                        </div>
                    <br>
                        <label for="name">Quality</label>
                        <div class="pacing-container" style="width: 33%;">
                         <div><div style="width: 200px; float: left">Viabavility rate:</div> <div style="float: left"><input checked type="radio" name="vr" value="1"> Low <input type="radio" name="vr" value="2"> med <input type="radio" name="vr" value="3"> str</div></div><br><br>
                            <div><div style="width: 200px; float: left">Completion rate:</div> <div style="float: left"><input checked type="radio" name="vr2" value="1"> Low <input type="radio" name="vr2" value="2"> med <input type="radio" name="vr2" value="3"> str</div></div><br><br>
                            <div><div style="width: 200px; float: left">Avarage TOS:</div> <div style="float: left"><input checked type="radio" name="vr3" value="1"> Low <input type="radio" name="vr3" value="2"> med <input type="radio" name="vr3" value="3"> str</div></div><br><br>
                            <div><div style="width: 200px; float: left">Transparency:</div> <div style="float: left"><input checked type="radio" name="vr4" value="1"> Low <input type="radio" name="vr4" value="2"> med <input type="radio" name="vr4" value="3"> str</div></div><br><br>
                            <div><div style="width: 200px; float: left">Above the fold:</div> <div style="float: left"><input checked type="radio" name="vr5" value="1"> Low <input type="radio" name="vr5" value="2"> med <input type="radio" name="vr5" value="3"> str</div></div><br><br>
                        </div> -->
                        @elseif($row->field == "goal_type")
                  
                        @elseif($row->field == "goal_values")
                            <div class="pacing-container" style="display: flex;">
                                <div  style="    width: 16em; display: flex;margin-right: 1em;align-items: center;">
                                Type&nbsp;<select class="form-control select2 select2-hidden-accessible" style="margin-left:1em;" name="goal_type" tabindex="-1" aria-hidden="true">
                                    <option value="1">CPC</option>
                                    <option value="2">CTR</option>
                                    <option value="3">Viewability Rate</option>
                                    <option value="4" selected="&quot;selected&quot;">Viewable CPM</option>
                                    <option value="5">CPM REACH</option>
                                </select>
                                </div> 

                                <div>
                                    Goal Values&nbsp;
                                    <input id="goal_amount" step="0.01" name="goal_amount" class="form-control" type="number" style="width: 11em; display:inline;" onkeyup="changeGoalValues()" value="{{ isset($goal_values[0]) ? $goal_values[0] : '' }}"> 
                                    for
                                    <select id="goal_bid_for" name="goal_bid_for" onchange="changeGoalValues()" style="width:11em; display:inline;" class="form-control">
                                        <option {{ isset($goal_values[1]) && $goal_values[1] == 1 ? 'selected' : '' }} value="1">Total Spend</option>
                                        <option {{ isset($goal_values[1]) && $goal_values[1] == 2 ? 'selected' : '' }} value="2">Media Only</option>
                                    </select>
                                    Min Bid CPM: $ <input step="0.01" id="goal_min_bid" name="goal_min_bid" type="number" class="form-control" style="width:11em; display:inline;" onkeyup="changeGoalValues()" value="{{ isset($goal_values[2]) ? $goal_values[2] : '' }}">
                                    Max Bid CPM: $ <input step="0.01" id="goal_max_bid" name="goal_max_bid" type="number" class="form-control" style="width:11em; display:inline;" onkeyup="changeGoalValues()" value="{{ isset($goal_values[3]) ? $goal_values[3] : '' }}">
                                    <input  type="hidden" id="goal_values" name="goal_values" value="">
                                </div>
                            </div>
                    @else
                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                    @endif
                @endif

                @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                    {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                @endforeach
            </div>
        @endif
    @endforeach
    <input type="hidden" name="campaign_id" value="{{ (!is_null($dataTypeContent->getKey()) ? $dataTypeContent->campaign_id : $_GET["campaign_id"]) }}">
</div>
<script>
     $(':input[type="number"]').change(function () {
          let formated =   parseFloat($(this).val()).toFixed(2);
          $(this).val(formated);
    });
</script>
<script>
    checkDisabled();
    function checkDisabled(){
            document.getElementById('pmonetary_values').style.visibility="visible";
            document.getElementById('pimpression_values').style.visibility="visible";
            document.getElementById('frequency_values').style.visibility="visible";

        if(document.getElementById('m_type').value==3){
            document.getElementById('pmonetary_values').style.visibility="hidden";
        }
        if(document.getElementById('i_type').value==3){
            document.getElementById('pimpression_values').style.visibility="hidden";
        }
        if(document.getElementById('f_type').value==2){
            document.getElementById('frequency_values').style.visibility="hidden";
        }
    }
</script>