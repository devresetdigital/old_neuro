import React, { useState, useContext } from 'react';
import Select from 'react-select';
import switchMenu from '../switchMenu';
import { Context as StrategyContext } from '../../context/StrategyContext';

// import MapContainer from '../MapContainer';
// import MapList from '../MapList';

const TargetingForm = ({ onSubmit, initialValues }) => {
  const {
    syncSitelistsData,
    syncSitelistsIncExc,
    syncIplistData,
    syncIplistIncExc,
    syncPmpsData,
    syncPmpsIncExc,
    syncZiplistData,
    syncZiplistIncExc,
    syncSspsData,
    syncCitiesData,
    syncCitiesIncExc,
    syncRegionsData,
    syncRegionsIncExc,
    syncCountriesData,
    syncCountriesIncExc,
    syncInventoryTypesData,
    syncInventoryTypesIncExc,
    syncDevicesData,
    syncIspsData,
    syncOssIncExc,
    syncBrowsersData
  } = useContext(StrategyContext);

  const incExcBooleanable = num => {
    if (num == 1 || num == 2) {
      return false;
    } else {
      return true;
    }
  };

  // Sitelist
  const [sitelistData, setSitelistData] = useState(initialValues.sitelists);
  const [sitelistIncExc, setSitelistIncExc] = useState(
    initialValues.sitelist_inc_exc
  );
  const [hideSitelist, showSitelist] = useState(
    incExcBooleanable(initialValues.sitelist_inc_exc)
  );

  // Iplist
  const [iplistData, setIplistData] = useState(initialValues.iplist_data);
  const [iplistIncExc, setIplistIncExc] = useState(
    initialValues.iplist_inc_exc
  );
  const [hideIplist, showIplist] = useState(
    incExcBooleanable(initialValues.iplist_inc_exc)
  );

  // Pmps
  const [pmpsData, setPmpsData] = useState(initialValues.pmps_data);
  const [pmpsIncExc, setPmpsIncExc] = useState(initialValues.pmps_inc_exc);
  const [hidePmps, showPmps] = useState(
    incExcBooleanable(initialValues.pmps_inc_exc)
  );

  // SSps
  const [sspsData, setSspsData] = useState(initialValues.ssps_data);

  // Ziplist
  const [ziplistData, setZiplistData] = useState(initialValues.ziplist_data);
  const [ziplistIncExc, setZiplistIncExc] = useState(
    initialValues.ziplist_inc_exc
  );
  const [hideZiplist, showZiplist] = useState(
    incExcBooleanable(initialValues.ziplist_inc_exc)
  );

  // Country
  const [countriesData, setCountriesData] = useState(
    initialValues.countries_data
  );
  const [countriesIncExc, setCountriesIncExc] = useState(
    initialValues.countries_inc_exc
  );
  const [hideCountries, showCountries] = useState(
    incExcBooleanable(initialValues.countries_inc_exc)
  );

  // Region
  const [regionsData, setRegionsData] = useState(initialValues.regions_data);
  const [regionsIncExc, setRegionsIncExc] = useState(
    initialValues.regions_inc_exc
  );
  const [hideRegions, showRegions] = useState(
    incExcBooleanable(initialValues.regions_inc_exc)
  );

  // City
  const [citiesData, setCitiesData] = useState(initialValues.cities_data);
  const [citiesIncExc, setCitiesIncExc] = useState(
    initialValues.cities_inc_exc
  );
  const [hideCities, showCities] = useState(
    incExcBooleanable(initialValues.cities_inc_exc)
  );

  // Device
  const [devicesData, setDevicesData] = useState(initialValues.devices_data);

  // Inventory Type
  const [inventory_typesData, setInventoryData] = useState(
    initialValues.inventory_types_data
  );
  const [inventory_typesIncExc, setInventoryIncExc] = useState(
    initialValues.inventory_types_inc_exc
  );
  const [hideInventory, showInventory] = useState(
    incExcBooleanable(initialValues.inventory_inc_exc)
  );

  // ISP
  const [ispsData, setIspsData] = useState(initialValues.isps_data);

  // OSs
  const [ossData, setOssData] = useState(initialValues.oss_data);
  const [ossIncExc, setOssIncExc] = useState(initialValues.oss_inc_exc);
  const [hideOss, showOss] = useState(
    incExcBooleanable(initialValues.oss_inc_exc)
  );

  // Browser
  const [browserData, setBrowserData] = useState(initialValues.browser_data);

  const handleSubmit = event => {
    event.preventDefault();
  };

  return (
    <div>
      <form onSubmit={handleSubmit}>
        {/* Sitelist */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Sitelist</label>
          {switchMenu(
            sitelistIncExc,
            setSitelistIncExc,
            syncSitelistsIncExc,
            showSitelist
          )}
          {hideSitelist ? null : (
            <Select
              defaultValue={sitelistData}
              isMulti
              options={SITELIST_FIELDS}
              onChange={event => {
                setSitelistData(event);
                syncSitelistsData(event);
              }}
            />
          )}
        </div>
        {/* Iplist */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Iplist</label>
          {switchMenu(
            iplistIncExc,
            setIplistIncExc,
            syncIplistIncExc,
            showIplist
          )}
          {hideIplist ? null : (
            <Select
              defaultValue={iplistData}
              isMulti
              options={IPLIST_FIELDS}
              onChange={event => {
                setIplistData(event);
                syncIplistData(event);
              }}
            />
          )}
        </div>
        {/* Pmps */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Pmps</label>
          {switchMenu(pmpsIncExc, setPmpsIncExc, syncPmpsIncExc, showPmps)}
          {hidePmps ? null : (
            <Select
              defaultValue={pmpsData}
              isMulti
              options={PMPSS_FIELDS}
              onChange={event => {
                setPmpsData(event);
                syncPmpsData(event);
              }}
            />
          )}
        </div>
        {/* Ssps */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Ssps</label>
          <Select
            defaultValue={sspsData}
            isMulti
            options={SSP_FIELDS}
            onChange={event => {
              setSspsData(event);
              syncSspsData(event);
            }}
          />
        </div>
        {/* Ziplist */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Ziplist</label>
          {switchMenu(
            ziplistIncExc,
            setZiplistIncExc,
            syncZiplistIncExc,
            showZiplist
          )}
          {hideZiplist ? null : (
            <Select
              defaultValue={ziplistData}
              isMulti
              options={ZIPLIST_FIELDS}
              onChange={event => {
                setZiplistData(event);
                syncZiplistData(event);
              }}
            />
          )}
        </div>
        {/* Countries */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Countries</label>
          {switchMenu(
            countriesIncExc,
            setCountriesIncExc,
            syncCountriesIncExc,
            showCountries
          )}
          {hideCountries ? null : (
            <Select
              defaultValue={countriesData}
              isMulti
              options={COUNTRIES_FIELDS}
              onChange={event => {
                setCountriesData(event);
                syncCountriesData(event);
              }}
            />
          )}
        </div>
        {/* Regions */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Regions</label>
          {switchMenu(
            regionsIncExc,
            setRegionsIncExc,
            syncRegionsIncExc,
            showRegions
          )}
          {hideRegions ? null : (
            <Select
              defaultValue={regionsData}
              isMulti
              options={REGIONS_FIELDS}
              onChange={event => {
                setRegionsData(event);
                syncRegionsData(event);
              }}
            />
          )}
        </div>
        {/* Cities */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Cities</label>
          {switchMenu(
            citiesIncExc,
            setCitiesIncExc,
            syncCitiesIncExc,
            showCities
          )}
          {hideCities ? null : (
            <Select
              defaultValue={citiesData}
              isMulti
              options={CITIES_FIELDS}
              onChange={event => {
                setCitiesData(event);
                syncCitiesData(event);
              }}
            />
          )}
        </div>
        {/* Inventory */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Inventory Types</label>
          {switchMenu(
            inventory_typesIncExc,
            setInventoryIncExc,
            syncInventoryTypesIncExc,
            showInventory
          )}
          {hideInventory ? null : (
            <Select
              defaultValue={inventory_typesData}
              isMulti
              options={INVENTORY_FIELDS}
              onChange={event => {
                setInventoryData(event);
                syncInventoryTypesData(event);
              }}
            />
          )}
        </div>
        {/* Devices */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Devices</label>
          <Select
            defaultValue={devicesData}
            isMulti
            options={DEVICES_FIELDS}
            onChange={event => {
              setDevicesData(event);
              syncDevicesData(event);
            }}
          />
        </div>
        {/* Browsers */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Browsers</label>
          <Select
            defaultValue={browserData}
            isMulti
            options={BROWSER_FIELDS}
            onChange={event => {
              setBrowserData(event);
              syncBrowsersData(event);
            }}
          />
        </div>
        {/* Oss */}
        <div className="form-group">
          <label style={{ display: 'block' }}>Operating Systems</label>
          {switchMenu(ossIncExc, setOssIncExc, syncOssIncExc, showOss)}
          {hideOss ? null : (
            <Select
              defaultValue={ossData}
              isMulti
              options={OSS_FIELDS}
              onChange={event => {
                setOssData(event);
                syncOssData(event);
              }}
            />
          )}
        </div>
        {/* Isps */}
        <div className="form-group">
          <label style={{ display: 'block' }}>
            Internet Service Provider / Mobile Carrier
          </label>
          <Select
            defaultValue={ispsData}
            isMulti
            options={ISPS_FIELDS}
            onChange={event => {
              setIspsData(event);
              syncIspsData(event);
            }}
          />
        </div>
      </form>
      {/* <MapList geodata={initialValues.geofencing.data} /> */}
      {/* <MapContainer geofencing={initialValues.geofencing} /> */}
    </div>
  );
};

export default TargetingForm;
