import React, { Component } from 'react';

class MapForm extends Component {
  constructor(props) {
    super(props);
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

export default MapForm;
