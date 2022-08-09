@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->display_name_plural)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->display_name_plural }}
        </h1>
        @can('add',app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
            <a href="/admin/bulk/creatives" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>Bulk processing</span>
            </a>
        @endcan
        @can('delete',app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit',app($dataType->model_name))
        @if(isset($dataType->order_column) && isset($dataType->order_display_column))
            <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary">
                <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
            </a>
        @endif
        @endcan
        @include('voyager::multilingual.language-selector')
    </div>
@stop



@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
       
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get" class="form-search">
                                <div id="search-input">
                                    <div class="input-group col-md-12">
                                        <select id="advertiserSelector" name ="advertiser" class="form-control advertiser-for-concepts" style="">
                                            <option value="0">All Advertisers</option>
                                            @foreach($advertisers as $advertiser)
                                                <option {{$advertiser->id == $advertiser_selected ? 'selected' : '' }}        
                                                    value="{{$advertiser->id}}">{{$advertiser->name}}</option>
                                            @endforeach                
                                        </select>  
                                        <input type="text" class="form-control" placeholder="{{ __('voyager::generic.search') }}" name="s" value="{{ $search->value }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-lg" type="submit">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        @endif
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        @can('delete',app($dataType->model_name))
                                        @endcan
                                        @foreach($dataType->browseRows as $row)
                                        <th>
                                            @if ($isServerSide)
                                                <a href="{{ $row->sortByUrl() }}">
                                            @endif
                                            {{ $row->display_name }}
                                            @if ($isServerSide)
                                                @if ($row->isCurrentSortField())
                                                    @if (!isset($_GET['sort_order']) || $_GET['sort_order'] == 'asc')
                                                        <i class="voyager-angle-up pull-right"></i>
                                                    @else
                                                        <i class="voyager-angle-down pull-right"></i>
                                                    @endif
                                                @endif
                                                </a>
                                            @endif
                                        </th>
                                        @endforeach
                                        <th >{{ 'Concept' }}</th>
                                        <th class="actions text-right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                  
                                    <tr>
                                        @can('delete',app($dataType->model_name))
                                        @endcan
                                        @foreach($dataType->browseRows as $row)
                                            @php 
                                                if($row->field == 'creative_type_id'){
                                                    $creative_type = $data->{$row->field};
                                                }
                                            @endphp
                                            <td>
                                                <?php $options = json_decode($row->details); ?>
                                                @if($row->type == 'image')
                                                    <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                                                @elseif($row->type == 'relationship')
                                                    @include('voyager::formfields.relationship', ['view' => 'browse'])
                                                @elseif($row->type == 'select_multiple')
                                                    @if(property_exists($options, 'relationship'))

                                                        @foreach($data->{$row->field} as $item)
                                                            @if($item->{$row->field . '_page_slug'})
                                                            <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field} }}</a>@if(!$loop->last), @endif
                                                            @else
                                                            {{ $item->{$row->field} }}
                                                            @endif
                                                        @endforeach

                                                        {{-- $data->{$row->field}->implode($options->relationship->label, ', ') --}}
                                                    @elseif(property_exists($options, 'options'))
                                                        @foreach($data->{$row->field} as $item)
                                                         {{ $options->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                        @endforeach
                                                    @endif

                                                @elseif($row->type == 'select_dropdown' && property_exists($options, 'options'))

                                                    @if($data->{$row->field . '_page_slug'})
                                                        <a href="{{ $data->{$row->field . '_page_slug'} }}">{!! $options->options->{$data->{$row->field}} !!}</a>
                                                    @else
                                                        {!! $options->options->{$data->{$row->field}} or '' !!}
                                                    @endif


                                                @elseif($row->type == 'select_dropdown' && $data->{$row->field . '_page_slug'})
                                                    <a href="{{ $data->{$row->field . '_page_slug'} }}">{{ $data->{$row->field} }}</a>
                                                @elseif($row->type == 'date' || $row->type == 'timestamp')
                                                {{ $options && property_exists($options, 'format') ? \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($options->format) : $data->{$row->field} }}
                                                @elseif($row->type == 'checkbox')
                                                 
                                                    @if ($row->field=='status' && $_ENV['ENABLE_TMT_SCAN'] == 1)
                                                        @php 

                                                            $canChangeStatus = true;
                                                            $color = 'default';
                                                            $label = 'Inactive';
                                                           
                                                            if($data->{$row->field}){
                                                                if($data->TrustScan == null) {
                                                                    $color = 'warning';
                                                                    $label = 'Pending';
                                                                } else {

                                                                    if($data->TrustScan->status == 'LIVE' || $data->TrustScan->status == 'PAUSED'){
                                                                        $color = 'info';
                                                                        $label = 'Active';
                                                                    }
                                                    
                                                                    if($data->TrustScan->status == 'ERROR' || $data->TrustScan->status == 'PENDING'  ){
                                                                        $color = 'warning';
                                                                        $label = 'Pending';
                                                                    }

                                                                }
                                                            }   
                                                            if($data->TrustScan != null && $data->TrustScan->status == 'INCIDENT'){
                                                                $color = 'danger';
                                                                $label = 'Blocked';
                                                                $canChangeStatus = false;
                                                            }
                                                    
                                                        @endphp
                                                        <span class="label label-{{$color}} {{ $canChangeStatus ? 'changeStatus' : '' }}" data-id="{{$data->id}}" >{{ $label }}</span>
                                                    
                                                    @else
                                                        @if($options && property_exists($options, 'on') && property_exists($options, 'off'))
                                                            @if($data->{$row->field})
                                                            <span class="label label-info changeStatus" data-id="{{$data->id}}">{{ $options->on }}</span>
                                                            @else
                                                            <span class="label label-primary changeStatus" data-id="{{$data->id}}">{{ $options->off }}</span>
                                                            @endif
                                                        @else
                                                        {{ $data->{$row->field} }}
                                                        @endif
                                                    @endif

                                                @elseif($row->type == 'color')
                                                    <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>
                                                @elseif($row->type == 'text')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    @if ($row->field=='id')
                                                        <div class="readmore"> <a target="_blank" href="/admin/creatives/{{$data->id}}/edit">{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</a></div>
                                                    @else
                                                        <div class="readmore">{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                    @endif
                                                @elseif($row->type == 'text_area')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div class="readmore">{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    @if(json_decode($data->{$row->field}))
                                                        @foreach(json_decode($data->{$row->field}) as $file)
                                                            <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}" target="_blank">
                                                                {{ $file->original_name ?: '' }}
                                                            </a>
                                                            <br/>
                                                        @endforeach
                                                    @else
                                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($data->{$row->field}) }}" target="_blank">
                                                            Download
                                                        </a>
                                                    @endif
                                                @elseif($row->type == 'rich_text_box')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div class="readmore">{{ mb_strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? mb_substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>
                                                @elseif($row->type == 'coordinates')
                                                    @include('voyager::partials.coordinates-static-image')
                                                @elseif($row->type == 'multiple_images')
                                                    @php $images = json_decode($data->{$row->field}); @endphp
                                                    @if($images)
                                                        @php $images = array_slice($images, 0, 3); @endphp
                                                        @foreach($images as $image)
                                                            <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif" style="width:50px">
                                                        @endforeach
                                                    @endif
                                                @else
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <span>{{ $data->{$row->field} }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            @if($data->concept!=null)
                                             <a href="/admin/concepts/{{$data->concept->id}}/edit" target="_blank" rel="noopener noreferrer">{{$data->concept->name}}</a> 
                                            @else
                                                Not Assigned
                                            @endif
                                        </td>
                                        <td class="no-sort no-click actions text-right" id="bread-actions">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary edit"
                                                        data-toggle="dropdown">
                                                    Actions &nbsp<span class="caret"></span>
                                                </button>
                                                @php
                                                    if($_ENV['WL_PREFIX'] !="" || $_ENV['WL_PREFIX'] !="0"){
                                                        $float_wlprefix = $_ENV['WL_PREFIX'].".0";
                                                        $wlprefix = (float) $float_wlprefix*1000000;
                                                    } else {
                                                        $wlprefix=0;
                                                    }
                                                    $idprefixed = $wlprefix+intval($data->id);
            
                                           
                                                    $sendManualy = false;
                                                    if($data->TrustScan == null || $data->TrustScan->status == 'ERROR') {
                                                        $sendManualy = true;
                                                    }
                                                @endphp

                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a data-id="{{$idprefixed}}" onClick="showPreview({{$idprefixed}},{{$creative_type}})" href="#">Preview</a></li>
                                                    <li><a href="/admin/creatives/{{$data->id}}/edit">Edit</a></li>
                                                    <li><a href="/admin/creatives/{{$data->id}}/export">Export</a></li>
                                                    <li><a  onClick="clone({{$data->id}})" href="#">Clone</a></li>
                                                    @if($sendManualy)
                                                    <li><a href="/admin/creatives/{{$data->id}}/manual_scan">Send for scan</a></li>
                                                    @endif
                                                    <li><a href="javascript:;" style="margin-left: 0px;"  class="delete" data-id="{{$data->id}}" id="delete-{{$data->id}}">Delete</a></li>
                                                </ul>
                                            </div>


                                        </td>
                                      
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                                    'voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total()
                                    ]) }}</div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                    's' => $search->value,
                                    'filter' => $search->filter,
                                    'key' => $search->key,
                                    'order_by' => $orderBy,
                                    'sort_order' => $sortOrder
                                ])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->display_name_singular) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="modal fade bs-example-modal-lg" id="preview_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="     width: 99%;
    margin-left: 0.5em;
    margin-top: 1em;
    height: 100%; ">
            <div class="modal-content" style="height: 95%;">
                <div class="modal-header" style="height: 1em;
    margin: 0;
    padding: 0;">
                    <button style="margin-right: 0.5em;" type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-footer" style="width: 100%;
    height: 100%;">
                <iframe style="    width: 100%; height: 100%;" src="" frameborder="0"></iframe> 

                 </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->

    </div>
@stop

@section('css')
<style>
.changeStatus{
    cursor: pointer;
}
</style>
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
<link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@endif
@stop

@section('javascript')
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>

    @endif
    <script>
         
        const clone = (id)=> {
            if (confirm('Are you sure you want to clone this creative?')){
            $(window).attr('location','/admin/cloneCreatives/'+id);
            }else{
                return false;
            }
        }
                                    
        const showPreview = (id,type) => {
            $('#preview_modal').find('iframe').attr('src','/multipreview/?cid='+id+'&type='+type);
            $('#preview_modal').modal({show:true})
        }
        $(document).ready(function () {

            $('.changeStatus').click(function (e) {
                e.preventDefault();
                let id =$(this).data('id');
                
                if(confirm('Are you going to change the status, do you want to continue?')){
                    $.post("/api/creatives/changeStatus/"+id ,{},function(data){},'json').done(function(data) {
                        location.reload()
                    }).fail(function(data, textStatus, xhr) {
                        alert('Error - There was an error trying to change the status, please try again');
                    });
                }
            });

            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => [],
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function(){
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked'));
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', ['id' => '__id']) }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });
    </script>
@stop
