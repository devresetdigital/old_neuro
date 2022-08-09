@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(!is_null($dataTypeContent->getKey()) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop
@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if(!is_null($dataTypeContent->getKey()))
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">
                            @if(!is_null($dataTypeContent->getKey()))
                                <a class="btn btn-success" href="../../vwis?vwi_location_id={{$dataTypeContent->getKey()}}">Locations List</a>
                                <style>
                                    #description {
                                        font-family: Roboto;
                                        font-size: 15px;
                                        font-weight: 300;
                                    }

                                    #infowindow-content .title {
                                        font-weight: bold;
                                    }

                                    /*#infowindow-content {
                                        display: none;
                                    }*/

                                    /*#map #infowindow-content {
                                        display: inline;
                                    }*/

                                    .pac-card {
                                        margin: 10px 10px 0 0;
                                        border-radius: 2px 0 0 2px;
                                        box-sizing: border-box;
                                        -moz-box-sizing: border-box;
                                        outline: none;
                                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
                                        background-color: #fff;
                                        font-family: Roboto;
                                    }

                                    #pac-container {
                                        padding-bottom: 12px;
                                        margin-right: 12px;
                                    }

                                    .pac-controls {
                                        display: inline-block;
                                        padding: 5px 11px;
                                    }

                                    .pac-controls label {
                                        font-family: Roboto;
                                        font-size: 13px;
                                        font-weight: 300;
                                    }

                                    #pac-input {
                                        background-color: #fff;
                                        font-family: Roboto;
                                        font-size: 15px;
                                        font-weight: 300;
                                        margin-left: 12px;
                                        padding: 0 11px 0 13px;
                                        text-overflow: ellipsis;
                                        width: 400px;
                                    }

                                    #pac-input:focus {
                                        border-color: #4d90fe;
                                    }

                                    #title {
                                        color: #fff;
                                        background-color: #4d90fe;
                                        font-size: 25px;
                                        font-weight: 500;
                                        padding: 6px 12px;
                                    }
                                    #target {
                                        width: 345px;
                                    }

                                    textarea {
                                        width: 100%;
                                        min-height: 40rem;
                                        font-family: "Lucida Console", Monaco, monospace;
                                        font-size: 0.9rem;
                                        line-height: 1.2;
                                    }
                                </style>
                                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
                                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
                                <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDHZAo7na0317gZzGD8r7sXwxN6loJ5fUU&libraries=places,drawing"></script>
                                <script>
                                    class GoogleMapPage {
                                        model; form; map; markers = []; eraserMode = false;

                                        constructor() {
                                            this.model = new Model();
                                            this.form = new Form();
                                            this.initMap();
                                            this.initSearchBox();
                                            //this.initDrawingManager();
                                        }

                                        initMap() {
                                            const options = {
                                                mapTypeId: google.maps.MapTypeId.ROADMAP,
                                                center: this.model.defaultCenter,
                                                zoom: 13
                                            };
                                            this.map = new google.maps.Map(this.form.mapElement, options);
                                            this.setCurrentPositionAsCenter();
                                            @if(!is_null($dataTypeContent->getKey()))
                                                this.loadOverlays();
                                            @endif
                                            // this.drawSelected();

                                        }

                                        /* drawSelected(){
                                             // Define the LatLng coordinates for the polygon's path.
                                             var triangleCoords = [
                                                 {
                                                     "lat": -22.89889692815891,
                                                     "lng": -43.24960143476562
                                                 },
                                                 {
                                                     "lat": -22.915183673933786,
                                                     "lng": -43.25509459882812
                                                 },
                                                 {
                                                     "lat": -22.89320401202794,
                                                     "lng": -43.20376784711914
                                                 },
                                                 {
                                                     "lat": -22.874384061904482,
                                                     "lng": -43.2207623234375
                                                 },
                                                 {
                                                     "lat": -22.876598309099847,
                                                     "lng": -43.24136168867187
                                                 }
                                             ];

                                             // Construct the polygon.
                                             var bermudaTriangle = new google.maps.Polygon({
                                                 paths: triangleCoords,
                                                 strokeColor: '#FF0000',
                                                 strokeOpacity: 0.8,
                                                 strokeWeight: 2,
                                                 fillColor: '#FF0000',
                                                 fillOpacity: 0.35,
                                                 draggable: true,
                                                 editable: true
                                             });
                                             bermudaTriangle.setMap(this.map);
                                         }*/

                                        setCurrentPositionAsCenter() {
                                            if (!navigator.geolocation) return;
                                            /*navigator.geolocation.getCurrentPosition(position => {
                                                this.map.setCenter({
                                                    lat: position.coords.latitude,
                                                    lng: position.coords.longitude
                                                });
                                            });*/
                                            this.map.setCenter({
                                                lat: 45.5017,
                                                lng: -73.5673
                                            });
                                        }

                                        initSearchBox() {
                                            const searchBox = new google.maps.places.SearchBox(this.form.searchBoxElement);
                                            this.map.controls[google.maps.ControlPosition.TOP_RIGHT].push(this.form.searchBoxElement);

                                            this.map.addListener('bounds_changed', () => {
                                                searchBox.setBounds(this.map.getBounds());
                                            });

                                            searchBox.addListener('places_changed', () => {
                                                const places = searchBox.getPlaces();
                                                if (places.length == 0) return;

                                                const bounds = new google.maps.LatLngBounds();
                                                this.removeMarkers();
                                                places.forEach(place => {
                                                    this.addMarker(place);
                                                    if (place.geometry.viewport) {
                                                        bounds.union(place.geometry.viewport); // Only geocodes have viewport.
                                                    } else {
                                                        bounds.extend(place.geometry.location);
                                                    }
                                                });
                                                this.map.fitBounds(bounds);
                                            });
                                        }

                                        addMarker(place) {
                                            if (!place.geometry) {
                                                this.form.showAlert('Returned place contains no geometry');
                                                return;
                                            }

                                            const icon = {
                                                url: place.icon,
                                                size: new google.maps.Size(71, 71),
                                                origin: new google.maps.Point(0, 0),
                                                anchor: new google.maps.Point(17, 34),
                                                scaledSize: new google.maps.Size(25, 25)
                                            };

                                            this.markers.push(new google.maps.Marker({
                                                map: this.map,
                                                icon: icon,
                                                title: place.name,
                                                position: place.geometry.location
                                            }));
                                        }

                                        removeMarkers() {
                                            this.markers.forEach(marker => marker.setMap(null));
                                            this.markers.length = 0;
                                        }

                                        removeOverlay(overlay) {
                                            if (!this.eraserMode) return;
                                            const index = this.model.overlays.indexOf(overlay);
                                            this.model.overlays.splice(index, 1);
                                            overlay.setMap(null);
                                        }

                                        addEraserButton() {
                                            var eraserDiv = document.createElement('div');
                                            eraserDiv.appendChild(this.form.eraserElement);
                                            eraserDiv.index = 1;

                                            this.form.eraserElement.addEventListener('click', () => this.eraserMode = !this.eraserMode);

                                            this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(eraserDiv);
                                        }

                                        addSaveButton() {
                                            var saveDiv = document.createElement('div');
                                            saveDiv.appendChild(this.form.saveElement);
                                            saveDiv.index = 1;

                                            this.form.saveElement.addEventListener('click', () => {
                                                const values = this.extractValuesFromOverlays(this.model.overlays);
                                                //this.form.jsonViewerElement.innerHTML = JSON.stringify(values, undefined, 4);
                                                document.getElementById("geofencingjson").innerHTML = JSON.stringify(values, undefined, 4);
                                                console.log(JSON.stringify(values, undefined, 4));
                                            });

                                            this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(saveDiv);
                                        }

                                        extractValuesFromOverlays(overlays) {
                                            const values = [];
                                            overlays.forEach(overlay => {
                                                const value = { type: overlay.type };

                                                if (overlay.type == 'rectangle') {
                                                    value.bounds = overlay.bounds;
                                                }
                                                else if (overlay.type == 'circle') {
                                                    value.center = overlay.center;
                                                    value.radius = overlay.radius;
                                                }
                                                else if (overlay.type == 'polygon') {
                                                    const path = overlay.getPath();
                                                    const pathLength = path.getLength();
                                                    value.points = [];
                                                    for (var i = 0; i < pathLength; i++) {
                                                        value.points.push(path.getAt(i));
                                                    }
                                                }

                                                values.push(value);
                                            });
                                            return values;
                                        }
                                        //If Geolocations

                                        loadOverlays() {
                                            const overlaysData = this.readOverlaysData();
                                            const overlays = this.drawOverlays(overlaysData);
                                            this.model.overlays.push(...overlays);

                                            const values = this.extractValuesFromOverlays(this.model.overlays);
                                            setTimeout(function(){
                                                document.getElementById("geofencingjson").innerHTML = JSON.stringify(values, undefined, 4);
                                            }, 1500);

                                        }

                                        readOverlaysData() {
                                            return JSON.parse(`{!! htmlspecialchars_decode($vwilist)  !!}`);
                                        }

                                        drawOverlays(overlaysData) {
                                            const baseOpt = {
                                                strokeColor: '#FF0000',
                                                strokeOpacity: 0.8,
                                                strokeWeight: 2,
                                                fillColor: '#FF0000',
                                                fillOpacity: 0.35,
                                                map: this.map,
                                                editable: true,
                                                draggable: true,
                                                geodesic: true
                                            }

                                            const resultOverlays = [];
                                            for (var i in overlaysData) {
                                                const overlayData = overlaysData[i];
                                                let overlay;

                                                if (overlayData.type == 'circle') {
                                                    const circleOpt = Object.assign({
                                                        center: overlayData.center,
                                                        radius: overlayData.radius
                                                    }, baseOpt);
                                                    overlay = new google.maps.Circle(circleOpt);
                                                    overlay.type = 'circle';
                                                } else if (overlayData.type == 'rectangle') {
                                                    const rectangleOpt = Object.assign({
                                                        bounds: overlayData.bounds
                                                    }, baseOpt);
                                                    overlay = new google.maps.Rectangle(rectangleOpt);
                                                    overlay.type = 'rectangle';
                                                } else if (overlayData.type == 'polygon') {
                                                    const polygonOpt = Object.assign({
                                                        paths: overlayData.points
                                                    }, baseOpt);
                                                    overlay = new google.maps.Polygon(polygonOpt);
                                                    overlay.type = 'polygon';
                                                }

                                                overlay.addListener('click', event => {
                                                    this.removeOverlay(overlay);
                                                });
                                                resultOverlays.push(overlay);
                                            }
                                            return resultOverlays;
                                        }

                                    }

                                    class Model {
                                        defaultCenter = { lat: 45.5017, lng: -73.5673 };
                                        overlays = [];
                                    }

                                    class Form {
                                        mapElement;
                                        eraserElement;
                                        saveElement;
                                        searchBoxElement;

                                        constructor() {
                                            this.mapElement = document.getElementById('map');
                                            this.searchBoxElement = document.getElementById('pac-input');
                                            this.eraserElement = document.getElementById('eraser');
                                            this.saveElement = document.getElementById('save');
                                        }

                                        get jsonViewerElement() {
                                            return document.getElementById('json-viewer');
                                        }

                                        showAlert(message, type) {
                                            console.log(message);
                                        }
                                    }
                                </script>
                                <div style="width: 100%; height: 600px">

                                    <input id="pac-input" placeholder="Enter a location">
                                    <div id="map" style="width: 100%; height: 600px"></div>

                                    <script>
                                        new GoogleMapPage();
                                    </script>

                                    <div class="modal fade" id="saveModal" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body" style="text-align: center">
                                                    Geofencing data saved ...
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <textarea name="geofencingjson" id="geofencingjson" style="display: none"></textarea>
                                <br><br>
                            @endif
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
                                    <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width or 12 }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                        {{ $row->slugify }}
                                        <label for="name">{{ $row->display_name }}</label>
                                        @include('voyager::multilingual.input-hidden-bread-edit-add')
                                        @if($row->type == 'relationship')
                                            @include('voyager::formfields.relationship')
                                        @else
                                            {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                        @endif

                                        @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                            {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>
        var params = {}
        var $image

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', function (e) {
                e.preventDefault();
                $image = $(this).siblings('img');

                params = {
                    slug:   '{{ $dataType->slug }}',
                    image:  $image.data('image'),
                    id:     $image.data('id'),
                    field:  $image.parent().data('field-name'),
                    _token: '{{ csrf_token() }}'
                }

                $('.confirm_delete_name').text($image.data('image'));
                $('#confirm_delete_modal').modal('show');
            });

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $image.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing image.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@stop
