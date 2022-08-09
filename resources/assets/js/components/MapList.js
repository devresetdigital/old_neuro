import React, { useEffect } from 'react';

const MapList = ({ geodata }) => {
  const renderMapList = geodata => {
    return (
      <div className="ui list">
        {geodata.map(item => {
          return (
            <div className="item" key={`${item.center.lat}`}>
              <i className="map marker icon"></i>
              <div className="content">
                Latitude: {item.center.lat} Longitude: {item.center.lng}
              </div>
            </div>
          );
        })}
      </div>
    );
  };

  // useEffect(() => {
  //   renderMapList();
  // });
  return ({ renderMapList() });
};

// MapList = React.memo(MapList);

export default MapList;
