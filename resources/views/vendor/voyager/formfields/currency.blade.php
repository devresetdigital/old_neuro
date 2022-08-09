
<div class="input-group">
  <span class="input-group-addon">$</span>
    <input 
       class="form-control percent"
       name="{{ $row->field }}"
       data-name="{{ $row->display_name }}"
       type="number"
       min="0"
       @if($row->required == 1) required @endif
             step="any"
       placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif">
</div>