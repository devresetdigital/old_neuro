import createDataContext from './createDataContext';
import {
  TargetingToObject,
  targetingArrFormObj,
  arraysFormObj
} from '../helpers';
import instance from '../api';

const serviceReducer = (state, action) => {
  switch (action.type) {
    case 'sync_status':
      return {
        ...state,
        status: action.payload
      };
    case 'sync_name':
      return {
        ...state,
        name: action.payload
      };
    case 'sync_channel':
      return {
        ...state,
        channel: action.payload
      };
    case 'sync_date_start':
      return {
        ...state,
        date_start: action.payload
      };
    case 'sync_date_end':
      return {
        ...state,
        date_end: action.payload
      };
    case 'sync_budget':
      return {
        ...state,
        budget: action.payload
      };
    case 'sync_goal_type':
      return {
        ...state,
        goal_type: action.payload
      };
    case 'sync_goal_values':
      return {
        ...state,
        goal_values: action.payload
      };
    case 'sync_pacing_monetary_type':
      return {
        ...state,
        pacing_monetary_type: action.payload
      };
    case 'sync_pacing_monetary_amount':
      return {
        ...state,
        pacing_monetary_amount: action.payload
      };
    case 'sync_pacing_monetary_interval':
      return {
        ...state,
        pacing_monetary_interval: action.payload
      };
    case 'sync_pacing_impression_type':
      return {
        ...state,
        pacing_impression_type: action.payload
      };
    case 'sync_pacing_impression_amount':
      return {
        ...state,
        pacing_impression_amount: action.payload
      };
    case 'sync_pacing_impression_interval':
      return {
        ...state,
        pacing_impression_interval: action.payload
      };
    case 'sync_frequency_cap_type':
      return {
        ...state,
        frequency_cap_type: action.payload
      };
    case 'sync_frequency_cap_amount':
      return {
        ...state,
        frequency_cap_amount: action.payload
      };
    case 'sync_frequency_cap_interval':
      return {
        ...state,
        frequency_cap_interval: action.payload
      };
    case 'sync_sitelists':
      return {
        ...state,
        sitelists: action.payload
      };
    case 'sync_iplists':
      return {
        ...state,
        iplists: action.payload
      };
    case 'sync_pmps_data':
      return {
        ...state,
        pmps_data: action.payload
      };
    case 'sync_pmps_inc_exc':
      return {
        ...state,
        pmps_inc_exc: action.payload
      };
    case 'sync_ssps_data':
      return {
        ...state,
        ssps_data: action.payload
      };
    case 'sync_cities_data':
      return {
        ...state,
        cities_data: action.payload
      };
    case 'sync_cities_inc_exc':
      return {
        ...state,
        cities_inc_exc: action.payload
      };
    case 'sync_regions_data':
      return {
        ...state,
        regions_data: action.payload
      };
    case 'sync_regions_inc_exc':
      return {
        ...state,
        regions_inc_exc: action.payload
      };
    case 'sync_countries_data':
      return {
        ...state,
        countries_data: action.payload
      };
    case 'sync_countries_inc_exc':
      return {
        ...state,
        countries_inc_exc: action.payload
      };
    case 'sync_inventory_types_data':
      return {
        ...state,
        inventory_types_data: action.payload
      };
    case 'sync_inventory_types_inc_exc':
      return {
        ...state,
        inventory_types_inc_exc: action.payload
      };
    case 'sync_devices_data':
      return {
        ...state,
        devices_data: action.payload
      };
    case 'sync_isps_data':
      return {
        ...state,
        isps_data: action.payload
      };
    case 'sync_oss_data':
      return {
        ...state,
        oss_data: action.payload
      };
    case 'sync_oss_inc_exc':
      return {
        ...state,
        oss_inc_exc: action.payload
      };
    case 'sync_browsers_data':
      return {
        ...state,
        browsers_data: action.payload
      };
    case 'sync_custom_datas':
      return {
        ...state,
        custom_datas: action.payload
      };
    case 'sync_pixels_list':
      return {
        ...state,
        pixels_list: action.payload
      };
    case 'sync_geofencing':
      return {
        ...state,
        geofencing_data: action.payload
      };
    case 'get_strategy':
      return {
        status: action.payload.status,
        name: action.payload.name,
        channel: action.payload.channel,
        date_start: action.payload.date_start,
        date_end: action.payload.date_end,
        budget: action.payload.budget,
        goal_type: action.payload.goal_type,
        goal_values: action.payload.goal_values,
        pacing_monetary: action.payload.pacing_monetary,
        pacing_monetary_type: action.payload.pacing_monetary_type,
        pacing_monetary_amount: action.payload.pacing_monetary_amount,
        pacing_monetary_interval: action.payload.pacing_monetary_interval,
        pacing_impression: action.payload.pacing_impression,
        pacing_impression_type: action.payload.pacing_impression_type,
        pacing_impression_amount: action.payload.pacing_impression_amount,
        pacing_impression_interval: action.payload.pacing_impression_interval,
        frequency_cap: action.payload.frequency_cap,
        frequency_cap_type: action.payload.frequency_cap_type,
        frequency_cap_amount: action.payload.frequency_cap_amount,
        frequency_cap_interval: action.payload.frequency_cap_interval,
        sitelists: action.payload.sitelists,
        iplists: action.payload.iplists,
        pmps_data: action.payload.pmps_data,
        pmps_inc_exc: action.payload.pmps_inc_exc,
        ssps_data: action.payload.ssps_data,
        ziplists_data: action.payload.ziplists_data,
        ziplists_inc_exc: action.payload.ziplists_inc_exc,
        cities_data: action.payload.cities_data,
        cities_inc_exc: action.payload.cities_inc_exc,
        regions_data: action.payload.regions_data,
        regions_inc_exc: action.payload.regions_inc_exc,
        countries_data: action.payload.countries_data,
        countries_inc_exc: action.payload.countries_inc_exc,
        inventory_types_data: action.payload.inventory_types_data,
        inventory_types_inc_exc: action.payload.inventory_types_inc_exc,
        devices_data: action.payload.devices_data,
        isps_data: action.payload.isps_data,
        oss_data: action.payload.oss_data,
        oss_inc_exc: action.payload.oss_inc_exc,
        browsers_data: action.payload.browsers_data,
        custom_datas: action.payload.custom_datas,
        pixels_list: action.payload.pixels_list,
        geofencing: action.payload.geofencing,
        loaded: true
      };
    case 'update_strategy':
      return {
        ...state,
        name: action.payload.name,
        goal_values: action.payload.goal_values,
        pmps_inc_exc: action.payload.pmps_inc_exc,
        pmps_data: action.payload.pmpsToDB,
        geofencing: action.payload.geofencing
      };
    case 'update_targeting':
      return {
        ...state,
        pmps_inc_exc: action.payload.pmps_inc_exc,
        pmps_data: action.payload.pmpsToDB
      };
    default:
      return state;
  }
};

const syncStatus = dispatch => status => {
  dispatch({ type: 'sync_status', payload: status });
};
const syncName = dispatch => name => {
  dispatch({ type: 'sync_name', payload: name });
};

const syncChannel = dispatch => channel => {
  dispatch({ type: 'sync_channel', payload: channel });
};

const syncDateStart = dispatch => date_start => {
  dispatch({ type: 'sync_date_start', payload: date_start });
};

const syncDateEnd = dispatch => date_end => {
  dispatch({ type: 'sync_date_end', payload: date_end });
};

const syncBudget = dispatch => budget => {
  dispatch({ type: 'sync_budget', payload: budget });
};

const syncGoalType = dispatch => goal_type => {
  dispatch({ type: 'sync_goal_type', payload: goal_type });
};

const syncGoalValues = dispatch => goal_values => {
  dispatch({ type: 'sync_goal_values', payload: goal_values });
};

const syncPacingMonetaryType = dispatch => pacing_monetary_type => {
  dispatch({
    type: 'sync_pacing_monetary_type',
    payload: pacing_monetary_type
  });
};
const syncPacingMonetaryAmount = dispatch => pacing_monetary_amount => {
  dispatch({
    type: 'sync_pacing_monetary_amount',
    payload: pacing_monetary_amount
  });
};
const syncPacingMonetaryInterval = dispatch => pacing_monetary_interval => {
  dispatch({
    type: 'sync_pacing_monetary_interval',
    payload: pacing_monetary_interval
  });
};

const syncPacingImpressionType = dispatch => pacing_impression => {
  dispatch({ type: 'sync_pacing_impression_type', payload: pacing_impression });
};

const syncPacingImpressionAmount = dispatch => pacing_impression => {
  dispatch({
    type: 'sync_pacing_impression_amount',
    payload: pacing_impression
  });
};

const syncPacingImpressionInterval = dispatch => pacing_impression => {
  dispatch({
    type: 'sync_pacing_impression_interval',
    payload: pacing_impression
  });
};

const syncFrequencyCapType = dispatch => frequency_cap => {
  dispatch({ type: 'sync_frequency_cap_type', payload: frequency_cap });
};

const syncFrequencyCapAmount = dispatch => frequency_cap => {
  dispatch({ type: 'sync_frequency_cap_amount', payload: frequency_cap });
};

const syncFrequencyCapInterval = dispatch => frequency_cap => {
  dispatch({ type: 'sync_frequency_cap_interval', payload: frequency_cap });
};

const syncSitelistsData = dispatch => sitelists => {
  dispatch({ type: 'sync_sitelists', payload: sitelists });
};

const syncSitelistsIncExc = dispatch => sitelists_inc_exc => {
  dispatch({ type: 'sync_sitelists_inc_exc', payload: sitelists_inc_exc });
};

const syncIplistData = dispatch => iplists_data => {
  dispatch({ type: 'sync_iplists_data', payload: iplists_data });
};

const syncIplistIncExc = dispatch => iplists_inc_exc => {
  dispatch({ type: 'sync_iplists_inc_exc', payload: iplists_inc_exc });
};

const syncPmpsData = dispatch => pmps_data => {
  dispatch({ type: 'sync_pmps_data', payload: pmps_data });
};

const syncPmpsIncExc = dispatch => pmps_inc_exc => {
  dispatch({ type: 'sync_pmps_inc_exc', payload: pmps_inc_exc });
};

const syncZiplistData = dispatch => ziplist_data => {
  dispatch({ type: 'sync_ziplist_data', payload: ziplist_data });
};

const syncZiplistIncExc = dispatch => ziplist_inc_exc => {
  dispatch({ type: 'sync_ziplist_inc_exc', payload: ziplist_inc_exc });
};

const syncSspsData = dispatch => ssps_data => {
  dispatch({ type: 'sync_ssps_data', payload: ssps_data });
};

const syncCitiesData = dispatch => cities_data => {
  dispatch({ type: 'sync_cities_data', payload: cities_data });
};

const syncCitiesIncExc = dispatch => cities_inc_exc => {
  dispatch({ type: 'sync_cities_inc_exc', payload: cities_inc_exc });
};

const syncRegionsData = dispatch => regions_data => {
  dispatch({ type: 'sync_regions_data', payload: regions_data });
};

const syncRegionsIncExc = dispatch => regions_inc_exc => {
  dispatch({ type: 'sync_regions_inc_exc', payload: regions_inc_exc });
};

const syncCountriesData = dispatch => countries_data => {
  dispatch({ type: 'sync_countries_data', payload: countries_data });
};

const syncCountriesIncExc = dispatch => countries_inc_exc => {
  dispatch({ type: 'sync_countries_inc_exc', payload: countries_inc_exc });
};

const syncInventoryTypesData = dispatch => inventory_types_data => {
  dispatch({
    type: 'sync_inventory_types_data',
    payload: inventory_types_data
  });
};

const syncInventoryTypesIncExc = dispatch => inventory_types_inc_exc => {
  dispatch({
    type: 'sync_inventory_types_inc_exc',
    payload: inventory_types_inc_exc
  });
};

const syncDevicesData = dispatch => devices_data => {
  dispatch({ type: 'sync_devices_data', payload: devices_data });
};

const syncIspsData = dispatch => isps_data => {
  dispatch({ type: 'sync_isps_data', payload: isps_data });
};

const syncOssData = dispatch => oss_data => {
  dispatch({ type: 'sync_oss_data', payload: oss_data });
};

const syncOssIncExc = dispatch => oss_inc_exc => {
  dispatch({ type: 'sync_oss_inc_exc', payload: oss_inc_exc });
};

const syncBrowsersData = dispatch => browsers_data => {
  dispatch({ type: 'sync_browsers_data', payload: browsers_data });
};

const syncCustomDatas = dispatch => custom_datas => {
  dispatch({ type: 'sync_custom_datas', payload: custom_datas });
};

const syncPixelsList = dispatch => pixels_list => {
  dispatch({ type: 'sync_pixels_list', payload: pixels_list });
};

const syncGeofencing = dispatch => geofencing => {
  console.log(geofencing);
  dispatch({ type: 'sync_geofencing', payload: geofencing });
};

const getStrategy = dispatch => async () => {
  console.log('Getting Strategy');
  let formObject = {};
  const arr = [
    'pmps',
    'countries',
    'cities',
    'regions',
    'ssps',
    'devices',
    'isps',
    'oss',
    'inventory_types',
    'custom_datas',
    'pixels_list'
  ];
  const sss = ['sitelist'];
  const response = await instance.get(`/v2/strategies/${STRATEGY_ID}`);
  targetingArrFormObj(response.data, formObject, arr);
  arraysFormObj(response.data, formObject, sss);

  const {
    campaign_id,
    status,
    name,
    channel,
    date_start,
    date_end,
    budget,
    goal_type,
    goal_values,
    pacing_monetary,
    pacing_impression,
    frequency_cap,
    geofencing
  } = response.data;

  console.log(response.data);
  console.log('------');
  // To Do: Make it smarter
  formObject['campaign_id'] = campaign_id;
  formObject['status'] = status;
  formObject['name'] = name;
  formObject['channel'] = channel;
  formObject['date_start'] = date_start;
  formObject['date_end'] = date_end;
  formObject['budget'] = budget;
  formObject['goal_type'] = goal_type;
  formObject['goal_values'] = goal_values;
  formObject['pacing_monetary'] = pacing_monetary;
  formObject['pacing_monetary_type'] = pacing_monetary.type;
  formObject['pacing_monetary_amount'] = pacing_monetary.amount;
  formObject['pacing_monetary_interval'] = pacing_monetary.interval;
  formObject['pacing_impression'] = pacing_impression;
  formObject['pacing_impression_type'] = pacing_impression.type;
  formObject['pacing_impression_amount'] = pacing_impression.amount;
  formObject['pacing_impression_interval'] = pacing_impression.interval;
  formObject['frequency_cap'] = frequency_cap;
  formObject['frequency_cap_type'] = frequency_cap.type;
  formObject['frequency_cap_amount'] = frequency_cap.amount;
  formObject['frequency_cap_interval'] = frequency_cap.interval;
  formObject['geofencing'] = geofencing;
  console.log(formObject);
  dispatch({ type: 'get_strategy', payload: formObject });
};

const updateStrategy = dispatch => async state => {
  try {
    console.log(state);
    const obj = TargetingToObject(state);
    console.log(obj);
    await dispatch({ type: 'update_strategy', payload: state });
    const response = await instance
      .put(`/v2/strategies/${STRATEGY_ID}`, obj)
      .then(function(response) {
        console.log(response);
        location.reload();
      });
  } catch (err) {
    console.log(err);
  }
};

const duplicateStrategy = dispatch => async id => {
  try {
    const response = await instance
      .post(`/v2/strategies/${id}/duplicate`)
      .then(function(response) {
        location.reload();
      });
  } catch (err) {
    console.log(err);
  }
};

const updateTargeting = dispatch => async formObj => {
  const {
    pmps,
    countries,
    cities,
    regions,
    oss,
    inventory_types,
    ssps,
    devices,
    isps,
    browser
  } = formObj;
  console.log(formObj);
  try {
    // Respect params from Strategy Controller V2
    // pmpsToDB, inc_exc
    dispatch({
      type: 'update_targeting',
      payload: {
        pmps,
        countries,
        cities,
        regions,
        oss,
        inventory_types,
        ssps,
        devices,
        isps,
        browser,
        geofencing
      }
    });
    const response = await instance.put(
      `/v2/strategies/${STRATEGY_ID}/update_targeting/`,
      {
        pmps,
        countries,
        cities,
        regions,
        oss,
        inventory_types,
        ssps,
        devices,
        isps,
        browser
      }
    );
    console.log('Done');
  } catch (err) {
    console.log(err);
  }
};

export const { Context, Provider } = createDataContext(
  serviceReducer,
  {
    getStrategy,
    duplicateStrategy,
    updateStrategy,
    updateTargeting,
    syncStatus,
    syncName,
    syncChannel,
    syncDateStart,
    syncDateEnd,
    syncBudget,
    syncGoalType,
    syncGoalValues,
    syncPacingMonetaryType,
    syncPacingMonetaryAmount,
    syncPacingMonetaryInterval,
    syncPacingImpressionType,
    syncPacingImpressionAmount,
    syncPacingImpressionInterval,
    syncFrequencyCapType,
    syncFrequencyCapAmount,
    syncFrequencyCapInterval,
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
    syncOssData,
    syncOssIncExc,
    syncBrowsersData,
    syncCustomDatas,
    syncPixelsList,
    syncGeofencing
  },
  {
    loaded: false,
    name: '',
    channel: '',
    status: '',
    date_start: '',
    date_end: '',
    budget: '',
    goal_type: '',
    goal_values: [],
    pacing_monetary: [],
    pacing_monetary_type: '',
    pacing_monetary_amount: '',
    pacing_monetary_interval: '',
    pacing_impression: [],
    pacing_impression_type: '',
    pacing_impression_amount: '',
    pacing_impression_interval: '',
    frequency_cap: [],
    frequency_cap_type: '',
    frequency_cap_amount: '',
    frequency_cap_interval: '',
    sitelists: [],
    pmps: [],
    pmps_data: [],
    pmps_inc_exc: '',
    iplists: [],
    ssps: [],
    ziplists: [],
    cities: [],
    regions: [],
    countries: [],
    inventory_types: [],
    devices: [],
    isps: [],
    oss: [],
    browsers: [],
    geofencing: []
  }
);
