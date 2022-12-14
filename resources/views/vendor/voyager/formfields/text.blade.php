<input @if($row->required == 1) required @endif type="text"

@if(isset($options->style))
        style="
        @foreach($options->style as $key => $value) 
                {{$key . ':'. $value . ';'}} 
        @endforeach
       "
@endif

 class="form-control" name="{{ $row->field }}"
        placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
       {!! isBreadSlugAutoGenerator($options) !!}
       value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif">
