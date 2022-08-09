@extends('voyager::master')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/eventerstatus.css') }}">
@stop
@section('content')
<div class="page-content">
  <div class="eventer-status">
    <div style="width: 100%;">
      <div class="row">
        <div class="col-lg-3">
          <div class="panel">
            <div class="eventer-selector">
              <label for="eventers">Eventers</label>
              <select class="eventers selector" name="eventers">
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
              </select>
            </div>
            <div class="agrupadores-selector">
              <label for="agrupadores">Group By</label>
              <select class="agrupadores selector" name="agrupadores">
                <option value="ssp">SSP</option>
                <option value="campaign">Campaign</option>
                <option value="strategy">Strategy</option>
                <option value="creative">Creative</option>
                <option value="domain" disabled="disabled">Domain</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <div class="panel">
            <table id="results-table" class="table">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">SSP</th>
                  <th scope="col">0</th>
                  <th scope="col">1</th>
                  <th scope="col">2</th>
                  <th scope="col">3</th>
                  <th scope="col">4</th>
                  <th scope="col">5</th>
                </tr>
              </thead>
              <tbody>
                @foreach($eventers as $key => $item)
                  <tr>
                    <td scope="row">{{ $loop->index + 1 }}</td>
                    <th scope="row">{{ $key }}</th>
                    <td scope="row" class="text-right">{{ $item[0] }}</td>
                    <td scope="row" class="text-right">{{ $item[1] }}</td>
                    <td scope="row" class="text-right">{{ $item[2] }}</td>
                    <td scope="row" class="text-right">{{ $item[3] }}</td>
                    <td scope="row" class="text-right">{{ $item[4] }}</td>
                    <td scope="row" class="text-right">{{ $item[5] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div> 
      </div>
  </div>
  </div>
</div>
@stop
@section('javascript')
  <script src="{{ asset('js/eventer-status.js') }}"></script>
@stop