<div id="geofencing" class="tab-pane fade in">
    <label for="name"><b>Country</b></label><br>
    @php
 
        if(!isset($country_inc_exc) || $country_inc_exc== []){
            $country_inc_exc= 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="country_list"  type="radio" name="country_inc_exc" value="3"  {{ isset($country_inc_exc) && $country_inc_exc == 3 ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="country_list" {{ isset($country_inc_exc) && $country_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="country_inc_exc" id="country_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="country_list" {{ isset($country_inc_exc) && $country_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="country_inc_exc" id="country_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="country[]" multiple id="country_list" {{ (isset($country_inc_exc) && $country_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        @foreach($iab_countries as $country)
            <option {{ in_array($country->code,$selected_countries) ? 'selected' : ''  }} value="{{$country->code}}" >{{$country->country}}</option>
        @endforeach
    </select><br><br>
    <label for="name"><b>Region</b></label><br>
    @php
        if(!isset($region_inc_exc) || $region_inc_exc== []){
            $region_inc_exc= 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="region_list" type="radio" name="region_inc_exc" value="3"  {{ isset($region_inc_exc) && $region_inc_exc == 3 ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="region_list" {{ isset($region_inc_exc) && $region_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="region_inc_exc" id="region_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="region_list" {{ isset($region_inc_exc) && $region_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="region_inc_exc" id="region_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="region[]" multiple id="region_list" {{ (isset($region_inc_exc) && $region_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        @foreach($iab_regions as $region)
            <!--<option {{ in_array($region->code,$selected_regions) ? 'selected' : ''  }} value="{{$region->code}}" >{{$region->region}} ({{ $region->pid }})</option>-->
                <option value="{{$region->code}}" >{{$region->region}} ({{ $region->pid }})</option>
        @endforeach
        @foreach($selected_regions as $sregion)
            @if($sregion!="")
            <option selected value="{{$sregion}}" >{{$sregion}}</option>
            @endif
        @endforeach
    </select><br><br>
    <label for="name"><b>City</b></label><br>
    @php
        if(!isset($city_inc_exc) || $city_inc_exc== []){
            $city_inc_exc= 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="cities" type="radio" name="city_inc_exc" value="3"  {{ isset($city_inc_exc) && $city_inc_exc == 3 ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="cities" {{ isset($city_inc_exc) && $city_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="city_inc_exc" id="city_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="cities" {{ isset($city_inc_exc) && $city_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="city_inc_exc" id="city_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="city[]" id="cities" style="width: 100%;" multiple {{ (isset($city_inc_exc) && $city_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        @foreach($selected_cities as $city)
            @if($city!=""){
                <option selected value="{{$city}}" >{{$cities_labels[$city]}}</option>
            @endif
        @endforeach
    </select><br><br>
    <label for="name"><b>Language</b></label><br>
    @php
        if(!isset($lang_inc_exc)  || $lang_inc_exc== []){
            $lang_inc_exc= 3;
        }
    @endphp
    <input class="inc_exc" data-input-id="langs" type="radio" name="lang_inc_exc" value="3"  {{ isset($lang_inc_exc) && $lang_inc_exc == 3 ? 'checked="checked"' : '' }} {{ (!is_null($dataTypeContent->getKey()) ? '' : 'checked="checked"') }}> Off 
    <input class="inc_exc" data-input-id="langs" {{ isset($lang_inc_exc) && $lang_inc_exc == 1 ? 'checked="checked"' : '' }} type="radio" name="lang_inc_exc" id="lang_inc" value="1" > Include 
    <input class="inc_exc" data-input-id="langs" {{ isset($lang_inc_exc) && $lang_inc_exc == 2 ? 'checked="checked"' : '' }} type="radio" name="lang_inc_exc" id="lang_exc" value="2" > Exclude<br>
    <select class="form-control select2" name="language[]" id="langs" style="width: 100%;" multiple {{ (isset($lang_inc_exc) && $lang_inc_exc == 3) ? 'disabled="disabled"' : ''  }}>
        @foreach($langs as $lang)
            <option {{ in_array($lang->code,$selected_langs) ? 'selected' : ''  }} value="{{$lang->code}}" >{{$lang->language}}</option>
        @endforeach
    </select><br><br>
    @php
        if(!isset($selected_geofencing_inc_exc)  || $selected_geofencing_inc_exc== ""){
            $selected_geofencing_inc_exc= 3;
        }
    @endphp
    <label for="name"><b>Geofencing</b></label><br>
    <input type="radio" name="geofencing_inc_exc" value="3" {{ !isset($selected_geofencing_inc_exc) || (isset($selected_geofencing_inc_exc) && ($selected_geofencing_inc_exc == 3)) ? 'checked="checked"' : '' }}>Off 
    <input name="geofencing_inc_exc" type="radio" value="1" {{ isset($selected_geofencing_inc_exc) && $selected_geofencing_inc_exc == 1 ? 'checked="checked"' : '' }}> Include 
    <input name="geofencing_inc_exc" type="radio" value="2" {{ isset($selected_geofencing_inc_exc) && $selected_geofencing_inc_exc == 2 ? 'checked="checked"' : '' }}> Exclude
    <script>
        class GoogleMapPage {
            model; form; map; markers = []; eraserMode = false; bounds; 

            constructor() {
                this.model = new Model();
                this.form = new Form();
                this.initMap();
                this.initSearchBox();
                this.initDrawingManager();
            }

            initMap() {
                const options = {
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: this.model.defaultCenter,
                    zoom: 13,
                };
                this.bounds =  new google.maps.LatLngBounds(null);
                this.map = new google.maps.Map(this.form.mapElement, options);
                this.setCurrentPositionAsCenter();
                // this.drawSelected();
                @if($selected_geofencing!="")
                this.loadOverlays();

                this.fitToBounds();
                
                
                @endif
            }
            
            fitToBounds(){
                
                this.map.setCenter(this.bounds.getCenter());
                    setTimeout(() => {
                    this.map.setZoom(2);     
                }, 500);

                
            }

            setCurrentPositionAsCenter() {
                if (!navigator.geolocation) return;
                this.map.setCenter({
                    lat: 40.71182461837265, 
                    lng: -74.01495375015041
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

            initDrawingManager() {

                const savePolygons = () => {
                    setTimeout(() => {
                        const values = this.extractValuesFromOverlays(this.model.overlays);
                        document.getElementById("geofencingjson").innerHTML = JSON.stringify(values, undefined, 4);
                    }, 500);
                };
                const drawingManager = new google.maps.drawing.DrawingManager({
                    //  drawingMode: google.maps.drawing.OverlayType.CIRCLE,
                    drawingControl: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_LEFT,
                        drawingModes: ['rectangle', 'circle', 'polygon']
                    },
                    rectangleOptions: {
                        draggable: true,
                        editable: true
                    },
                    circleOptions: {
                        draggable: true,
                        editable: true
                    },
                    polygonOptions: {
                        draggable: true,
                        editable: true
                    }
                });
                drawingManager.setMap(this.map);
                google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
                    savePolygons();
                    if (event.type == 'circle') {
                        // add resize behaviour to the circle
                        event.overlay.addListener('radius_changed', function(e){
                            savePolygons();
                        });
                        
                        // add drag behaviour to circle
                        event.overlay.addListener('center_changed', function(e){
                            savePolygons();
                        });
                    }
                    if (event.type == 'rectangle') {
                        google.maps.event.addListener(event.overlay, 'dragend', function(){
                            savePolygons();
                        });
                        google.maps.event.addListener(event.overlay, 'bounds_changed', function(){
                            savePolygons();
                        });

                    }
                    
                    if (event.type == 'polygon') {
                        event.overlay.getPaths().forEach(function(path, index){
                            
                            google.maps.event.addListener(path, 'insert_at', function(){
                                savePolygons();
                            });
                            
                            google.maps.event.addListener(path, 'remove_at', function(){
                                savePolygons();
                            });
                    
                        });
                        
                        google.maps.event.addListener(event.overlay, 'dragend', function(){
                            savePolygons();
                        });
                        
                    }
                });
                this.addEraserButton();
                //this.addSaveButton();

                drawingManager.addListener('overlaycomplete', event => {
                    const overlay = event.overlay;
                    overlay.type = event.type;
                    this.model.overlays.push(overlay);

                    overlay.addListener('click', event => {
                        this.removeOverlay(overlay);
                    });
                });
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
                    document.getElementById("geofencingjson").innerHTML = JSON.stringify(values, undefined, 4);
                    console.log(JSON.stringify(values, undefined, 4))    
                });

                this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(saveDiv);
            }

            savePolygons(){
                setTimeout(() => {
                    const values = this.extractValuesFromOverlays(this.model.overlays);
                    document.getElementById("geofencingjson").innerHTML = JSON.stringify(values, undefined, 4);
                }, 500);
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
            @if($selected_geofencing!="")
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
                return JSON.parse(`{!! htmlspecialchars_decode($selected_geofencing)  !!} `);
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

                const savePolygons = () => {
                    setTimeout(() => {
                        const values = this.extractValuesFromOverlays(this.model.overlays);
                        document.getElementById("geofencingjson").innerHTML = JSON.stringify(values, undefined, 4);
                    }, 500);
                };

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

                        overlay.addListener('radius_changed', function(e){
                            savePolygons();
                        });
                        
                        // add drag behaviour to circle
                        overlay.addListener('center_changed', function(e){
                            savePolygons();
                        });
                        
                        this.bounds.union(overlay.getBounds());
                        
                    } else if (overlayData.type == 'rectangle') {
                        const rectangleOpt = Object.assign({
                            bounds: overlayData.bounds
                        }, baseOpt);
                        overlay = new google.maps.Rectangle(rectangleOpt);
                        overlay.type = 'rectangle';

                            // add drag behaviour to circle
                        overlay.addListener('dragend', function(e){
                            savePolygons();
                        });
                            overlay.addListener('bounds_changed', function(e){
                            savePolygons();
                        });

                        this.bounds.union(overlay.getBounds());
                    } else if (overlayData.type == 'polygon') {
                        const polygonOpt = Object.assign({
                            paths: overlayData.points
                        }, baseOpt);
                        
                        overlay = new google.maps.Polygon(polygonOpt);
                        overlay.type = 'polygon';
                        overlay.getPaths().forEach(function(path, index){
                            path.addListener('insert_at', function(e){
                                savePolygons();
                            });
                            path.addListener('remove_at', function(e){
                                savePolygons();
                            });
                        });
                        
                        overlay.addListener('dragend', function(e){
                            savePolygons();
                        });
                        overlay.addListener('mouseup', function(e){
                            savePolygons();
                        });

                        for(const p of overlayData.points){
                            this.bounds.extend(p);
                        }
                        
                    }

                    overlay.addListener('click', event => {
                        this.removeOverlay(overlay);
                    });
                    resultOverlays.push(overlay);
                }
                return resultOverlays;
            }
            @endif
        }

        class Model {
            defaultCenter = { lat: 40.6971494, lng: -74.2598655 };
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
        <button id="eraser" type="button" class="btn btn-light" data-toggle="button">
            <span class="glyphicon glyphicon-erase"></span>
        </button>
        

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
    <button type="button" onclick="document.getElementById('geofencingjson').style.display = 'inline'"></button>
</div>