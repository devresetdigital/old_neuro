import React, { useState, useContext, useEffect } from 'react';
import MapModel from './MapModel';
import MapForm from './MapForm';
import mapStyles from './mapStyles.css';
import { Context as StrategyContext } from '../context/StrategyContext';

const MapContainer = ({ geofencing }) => {
  const [geoIncExc, setGeoIncExc] = useState(geofencing.inc_exc);
  const [geodata, updateGeodata] = useState(geofencing.data);
  const [eraseMode, setEraseMode] = useState(false);
  const [number, setNumber] = useState(6);
  const { syncGeofencing } = useContext(StrategyContext);

  const removeOverlay = (overlay, geodata) => {
    if (eraseMode) return;
    const index = geodata.indexOf(overlay);
    geodata.splice(index, 1);
    overlay.setMap(null);
    // setGeodata(geodata);
    // updateGeodata([
    //   ...geodata,
    //   {
    //     id: 7
    //   }
    // ]);
    console.log('Remove completed');
    syncGeofencing(geodata);
  };

  const drawOverlays = (overlaysData, mapId) => {
    const baseOpt = {
      strokeColor: '#FF0000',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#FF0000',
      fillOpacity: 0.35,
      map: mapId,
      editable: true,
      draggable: true,
      geodesic: true
    };
    const resultOverlays = [];
    for (var i in overlaysData) {
      const overlayData = overlaysData[i];
      let overlay;

      if (overlayData.type == 'circle') {
        const circleOpt = Object.assign(
          {
            center: overlayData.center,
            radius: overlayData.radius
          },
          baseOpt
        );
        overlay = new google.maps.Circle(circleOpt);
        overlay.type = 'circle';
      } else if (overlayData.type == 'rectangle') {
        const rectangleOpt = Object.assign(
          {
            bounds: overlayData.bounds
          },
          baseOpt
        );
        overlay = new google.maps.Rectangle(rectangleOpt);
        overlay.type = 'rectangle';
      } else if (overlayData.type == 'polygon') {
        const polygonOpt = Object.assign(
          {
            paths: overlayData.points
          },
          baseOpt
        );
        overlay = new google.maps.Polygon(polygonOpt);
        overlay.type = 'polygon';
      }

      overlay.addListener('click', event => {
        removeOverlay(overlay, geodata);
        console.log('removing overlay');
        console.log(overlay);
      });
      resultOverlays.push(overlay);
    }
    return resultOverlays;
  };

  const erasePointer = () => {
    setEraseMode(true);
    console.log('Eraser Mode On');
  };

  const addEraserButton = (map, mapForm) => {
    var eraserDiv = document.createElement('div');
    eraserDiv.appendChild(mapForm.eraserElement);
    eraserDiv.index = 1;

    // mapForm.eraserElement.addEventListener(
    //   'click',
    //   () => (this.eraserMode = !this.eraserMode)
    // );

    map.controls[google.maps.ControlPosition.LEFT_TOP].push(eraserDiv);
  };

  useEffect(() => {
    const mapForm = new MapForm();
    const options = {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      center: { lat: 43.6532, lng: -79.3832 },
      zoom: 13
    };
    const mapId = mapForm.mapElement;
    const map = new google.maps.Map(mapId, options);
    addEraserButton(map, mapForm);
    drawOverlays(geodata, map);
  }, []);

  // addEraserButton = (map, mapForm) => {
  //   console.log('erasing');
  //   var eraserDiv = document.createElement('div');
  //   eraserDiv.appendChild(mapForm.eraserElement);
  //   eraserDiv.index = 1;

  //   mapForm.eraserElement.addEventListener(
  //     'click',
  //     () => (this.eraserMode = !this.eraserMode)
  //   );

  //   map.controls[google.maps.ControlPosition.LEFT_TOP].push(eraserDiv);
  // };
  const logMapState = () => {
    console.log(geodata);
  };

  const updateNumber = () => {
    updateGeodata([
      ...geodata,
      {
        id: 7
      }
    ]);
  };

  // const handleRemoveItem = e => {
  //   // const lat = e.target.getAttribute('lat');
  //   // updateGeodata(geodata.filter(item => item.center.lat !== lat));
  //   console.log('Removing Item');
  // };

  return (
    <div>
      {eraseMode ? <p>true</p> : <p>false</p>}
      {geodata.length}
      <a onClick={updateNumber}>Numero</a>
      {number}
      <a onClick={logMapState}>MapState</a>

      <div className="form-group">
        <label>Geofencing</label>
        <div> selected.</div>
      </div>
      {/* <div className="ui list">{renderMapList(geodata.data)}</div> */}
      {/* <MapList geodata={geodata} /> */}
      {/* <div className="ui list">
        {geodata.map(item => {
          return (
            <div className="item" key={`${item.center.lat}`}>
              <i className="map marker icon"></i>
              <div className="content">
                Latitude: {item.center.lat} Longitude: {item.center.lng}
              </div>
              <span lat={item.center.lat} onClick={handleRemoveItem}>
                x
              </span>
            </div>
          );
        })}
      </div> */}
      <button
        onClick={erasePointer}
        id="eraser"
        type="button"
        className="btn btn-light"
        data-toggle="button"
      >
        <span className="glyphicon glyphicon-erase"></span>
      </button>
      <input
        id="pac-input"
        placeholder="Enter a location"
        className="field"
      ></input>
      <div
        id="map"
        style={{
          width: '100%',
          height: '540px'
        }}
      ></div>
    </div>
  );
};

export default MapContainer;

// constructor(props) {
//   super(props);
//   this.defaultCenter = { lat: 45.5017, lng: -73.5673 };
//   this.overlays = [];
//   this.options = {
//     mapTypeId: google.maps.MapTypeId.ROADMAP,
//     center: { lat: 43.6532, lng: -79.3832 },
//     zoom: 13
//   };
//   this.eraserMode = false;
//   this.state = {
//     geodata: 0
//   };
//   this.updateGeodata = () => {
//     let newCount = this.state.geodata + 1;
//     this.setState({
//       geodata: newCount
//     });
//   };
// }

// initSearchBox(map, mapForm) {
//   console.log(mapForm);
//   const searchBox = new google.maps.places.SearchBox(
//     mapForm.searchBoxElement
//   );
//   map.controls[google.maps.ControlPosition.TOP_LEFT].push(
//     mapForm.searchBoxElement
//   );

//   map.addListener('bounds_changed', () => {
//     searchBox.setBounds(map.getBounds());
//   });

//   searchBox.addListener('places_changed', () => {
//     const places = searchBox.getPlaces();
//     if (places.length == 0) return;

//     const bounds = new google.c.LatLngBounds();
//     this.removeMarkers();
//     places.forEach(place => {
//       this.addMarker(place);
//       if (place.geometry.viewport) {
//         bounds.union(place.geometry.viewport); // Only geocodes have viewport.
//       } else {
//         bounds.extend(place.geometry.location);
//       }
//     });
//     map.fitBounds(bounds);
//   });
// }

// addMarker(place) {
//   if (!place.geometry) {
//     form.showAlert('Returned place contains no geometry');
//     return;
//   }

//   const icon = {
//     url: place.icon,
//     size: new google.maps.Size(71, 71),
//     origin: new google.maps.Point(0, 0),
//     anchor: new google.maps.Point(17, 34),
//     scaledSize: new google.maps.Size(25, 25)
//   };

//   this.markers.push(
//     new google.maps.Marker({
//       map: this.map,
//       icon: icon,
//       title: place.name,
//       position: place.geometry.location
//     })
//   );
// }

// addEraserButton(map, mapForm) {
//   console.log('erasing');
//   var eraserDiv = document.createElement('div');
//   eraserDiv.appendChild(mapForm.eraserElement);
//   eraserDiv.index = 1;

//   mapForm.eraserElement.addEventListener(
//     'click',
//     () => (this.eraserMode = !this.eraserMode)
//   );

//   map.controls[google.maps.ControlPosition.LEFT_TOP].push(eraserDiv);
// }

// loadOverlays(overlaysData, map) {
//   const geoOverlays = this.drawOverlays(overlaysData, map);
//   this.overlays.push(...geoOverlays);
//   // ---- Refactor to Show Geofencing in a List ----
//   // this.model.overlays.push(...overlays);
//   // const values = this.extractValuesFromOverlays(this.overlays);
//   // setTimeout(function() {
//   //   document.getElementById('geofencingjson').innerHTML = JSON.stringify(
//   //     values,
//   //     undefined,
//   //     4
//   //   );
//   // }, 1500);
// }

// ---- Extract Values to Text (deprecated in react) ----- Erase when completed!
// extractValuesFromOverlays(overlays) {
//   const values = [];
//   overlays.forEach(overlay => {
//     const value = { type: overlay.type };
//     if (overlay.type == 'rectangle') {
//       value.bounds = overlay.bounds;
//     } else if (overlay.type == 'circle') {
//       value.center = overlay.center;
//       value.radius = overlay.radius;
//     } else if (overlay.type == 'polygon') {
//       const path = overlay.getPath();
//       const pathLength = path.getLength();
//       value.points = [];
//       for (var i = 0; i < pathLength; i++) {
//         value.points.push(path.getAt(i));
//       }
//     }
//     values.push(value);
//   });
//   return values;
// }

// drawOverlays(overlaysData, mapId) {
//   const baseOpt = {
//     strokeColor: '#FF0000',
//     strokeOpacity: 0.8,
//     strokeWeight: 2,
//     fillColor: '#FF0000',
//     fillOpacity: 0.35,
//     map: mapId,
//     editable: true,
//     draggable: true,
//     geodesic: true
//   };
//   const resultOverlays = [];
//   for (var i in overlaysData) {
//     const overlayData = overlaysData[i];
//     let overlay;

//     if (overlayData.type == 'circle') {
//       const circleOpt = Object.assign(
//         {
//           center: overlayData.center,
//           radius: overlayData.radius
//         },
//         baseOpt
//       );
//       overlay = new google.maps.Circle(circleOpt);
//       overlay.type = 'circle';
//     } else if (overlayData.type == 'rectangle') {
//       const rectangleOpt = Object.assign(
//         {
//           bounds: overlayData.bounds
//         },
//         baseOpt
//       );
//       overlay = new google.maps.Rectangle(rectangleOpt);
//       overlay.type = 'rectangle';
//     } else if (overlayData.type == 'polygon') {
//       const polygonOpt = Object.assign(
//         {
//           paths: overlayData.points
//         },
//         baseOpt
//       );
//       overlay = new google.maps.Polygon(polygonOpt);
//       overlay.type = 'polygon';
//     }

//     overlay.addListener('click', event => {
//       this.removeOverlay(overlay);
//     });
//     resultOverlays.push(overlay);
//   }
//   return resultOverlays;
// }

// removeMarkers() {
//   this.markers.forEach(marker => marker.setMap(null));
//   this.markers.length = 0;
// }

// removeOverlay(overlay) {
//   if (!this.eraserMode) return;
//   const index = this.model.overlays.indexOf(overlay);
//   this.model.overlays.splice(index, 1);
//   overlay.setMap(null);
//   console.log('removing!!');
// }

// initDrawingManager(map) {
//   const savePolygons = () => {
//     setTimeout(() => {
//       const values = this.extractValuesFromOverlays(this.model.overlays);
//       document.getElementById('geofencingjson').innerHTML = JSON.stringify(
//         values,
//         undefined,
//         4
//       );
//     }, 500);
//   };
//   const drawingManager = new google.maps.drawing.DrawingManager({
//     //  drawingMode: google.maps.drawing.OverlayType.CIRCLE,
//     drawingControl: true,
//     drawingControlOptions: {
//       position: google.maps.ControlPosition.LEFT_TOP,
//       drawingModes: ['rectangle', 'circle', 'polygon']
//     },
//     rectangleOptions: {
//       draggable: true,
//       editable: true
//     },
//     circleOptions: {
//       draggable: true,
//       editable: true
//     },
//     polygonOptions: {
//       draggable: true,
//       editable: true
//     }
//   });

//   drawingManager.setMap(map);

//   google.maps.event.addListener(drawingManager, 'overlaycomplete', function(
//     event
//   ) {
//     console.log('bater shape');
//     savePolygons();
//     if (event.type == 'circle') {
//       // add resize behaviour to the circle
//       event.overlay.addListener('radius_changed', function(e) {
//         savePolygons();
//       });

//       // add drag behaviour to circle
//       event.overlay.addListener('center_changed', function(e) {
//         savePolygons();
//       });
//     }
//     if (event.type == 'rectangle') {
//       google.maps.event.addListener(event.overlay, 'dragend', function() {
//         savePolygons();
//       });
//       google.maps.event.addListener(
//         event.overlay,
//         'bounds_changed',
//         function() {
//           savePolygons();
//         }
//       );
//     }

//     if (event.type == 'polygon') {
//       event.overlay.getPaths().forEach(function(path, index) {
//         google.maps.event.addListener(path, 'insert_at', function() {
//           savePolygons();
//         });

//         google.maps.event.addListener(path, 'remove_at', function() {
//           savePolygons();
//         });
//       });

//       google.maps.event.addListener(event.overlay, 'dragend', function() {
//         savePolygons();
//       });
//     }
//   });
// }

// componentDidMount() {
//   const mapForm = new MapForm();
//   const overlayData = this.props.geofencingData.data;
//   const mapId = mapForm.mapElement;
//   const map = new google.maps.Map(mapId, this.options);
//   this.initSearchBox(map, mapForm);
//   this.loadOverlays(overlayData, map);
//   this.initDrawingManager(map);
//   this.addEraserButton(map, mapForm);
// }
