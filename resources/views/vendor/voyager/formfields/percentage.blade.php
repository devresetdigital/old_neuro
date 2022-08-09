
<div class="input-group">
    <input 
       class="form-control percent"
       name="{{ $row->field }}"
       data-name="{{ $row->display_name }}"
       type="number"
       max="100"
       min="0"
       step="1"
       @if($row->required == 1) required @endif
             step="any"
       placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif">
    <span class="input-group-addon">%</span>
</div>

<script>
/*
$(document).ready(function() {
  $('.percent').change(function() {
     var value =parseFloat($('.percent').val());
     $('.percent').val( value.toFixed(2) );
  });
})*/
</script>