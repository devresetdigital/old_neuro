@if(isset($options->extra))
<div class="input-group">
  <span class="input-group-addon">{{$options->extra->prefix}}</span>
  <input type="number"
       class="form-control"
       name="{{ $row->field }}"
       
       @if(isset($options->style))
        style="
        @foreach($options->style as $key => $value) 
                {{$key . ':'. $value . ';'}} 
        @endforeach
       "
       @endif

       type="number"
       @if($row->required == 1) required @endif
       step="any"
       placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif">
</div>
@else
<input type="number"
       class="form-control"
       @if(isset($options->style))
        style="
        @foreach($options->style as $key => $value) 
                {{$key . ':'. $value . ';'}} 
        @endforeach
       "
       @endif
       name="{{ $row->field }}"
       type="number"
       @if($row->required == 1) required @endif
       step="any"
       placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif">
@endif