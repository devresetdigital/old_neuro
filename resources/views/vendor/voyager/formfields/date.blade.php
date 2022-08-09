<input type="date" class="form-control" 
       @if(isset($options->style))
        style="
        @foreach($options->style as $key => $value) 
                {{$key . ':'. $value . ';'}} 
        @endforeach
       "
       @endif
       name="{{ $row->field }}"
       placeholder="{{ $row->display_name }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ \Carbon\Carbon::parse(old($row->field, $dataTypeContent->{$row->field}))->format('Y-m-d') }}@else{{old($row->field)}}@endif">
