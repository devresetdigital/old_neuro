// HELPERS
export const TargetingToObject = state => {
  let formObj = {};
  const {
    pmps_inc_exc,
    pmps_data,
    countries_inc_exc,
    countries_data,
    cities_inc_exc,
    cities_data,
    regions_data,
    regions_inc_exc,
    oss_inc_exc,
    oss_data,
    inventory_types_inc_exc,
    inventory_types_data,
    ssps_data,
    devices_data,
    isps_data,
    browser_data,
    name,
    status,
    channel,
    budget,
    date_end,
    date_start,
    goal_type,
    goal_values,
    pacing_monetary_type,
    pacing_monetary_amount,
    pacing_monetary_interval,
    pacing_impression_type,
    pacing_impression_amount,
    pacing_impression_interval,
    frequency_cap_type,
    frequency_cap_amount,
    frequency_cap_interval
  } = state;

  const fieldsIncExc = [
    'pmps',
    'countries',
    'cities',
    'regions',
    'oss',
    'inventory_types'
  ];

  const fieldsData = ['ssps', 'devices', 'isps', 'browser'];

  const fields = [
    'channel',
    'budget',
    'date_end',
    'date_start',
    'name',
    'status'
  ];

  const fieldsDetail = [
    'goal_values',
    'pacing_impression',
    'pacing_monetary',
    'frequency_cap'
  ];

  fieldsIncExc.map(item => {
    const item_status = eval(`${item}_inc_exc`);
    if (item_status == 3) {
      formObj[`${item}`] = { data: '', inc_exc: 3 };
    } else {
      const item_data = eval(`${item}_data`);
      let data_to_array = [];
      for (var i in item_data) {
        data_to_array.push(item_data[i].value);
      }
      data_to_array = data_to_array.toString();
      formObj[`${item}`] = {
        data: data_to_array,
        inc_exc: item_status
      };
    }
  });

  fieldsData.map(item => {
    const item_data = eval(`${item}_data`);
    let data_to_array = [];
    for (var i in item_data) {
      data_to_array.push(item_data[i].value);
    }
    data_to_array = data_to_array.toString();
    formObj[`${item}`] = {
      data: data_to_array
    };
  });

  fieldsDetail.map(item => {
    const item_data = eval(`${item}`);
    let data_to_array = [];
    for (var i in item_data) {
      data_to_array.push(item_data[i]);
    }
    data_to_array = data_to_array.toString();
    formObj[`${item}`] = data_to_array;
  });

  fields.map(item => {
    formObj[`${item}`] = eval(`${item}`);
  });

  formObj[
    'pacing_monetary'
  ] = `${pacing_monetary_type},${pacing_monetary_amount},${pacing_monetary_interval}`;

  formObj[
    'pacing_impression'
  ] = `${pacing_impression_type},${pacing_impression_amount},${pacing_impression_interval}`;

  formObj[
    'frequency_cap'
  ] = `${frequency_cap_type},${frequency_cap_amount},${frequency_cap_interval}`;

  return formObj;
};

export const targetingArrFormObj = (inputObj, outputObj, arr) => {
  arr.map(item => {
    for (let [key] of Object.entries(inputObj)) {
      if (key === item) {
        outputObj[`${item}_inc_exc`] = `${inputObj[item].inc_exc}`;
        const dataToMap = inputObj[item].data.map(item => ({
          value: `${item}`,
          label: `${item}`
        }));
        outputObj[`${item}_data`] = dataToMap;
      }
    }
  });
};

export const arraysFormObj = (inputObj, outputObj, arr) => {
  arr.map(item => {
    for (let [key] of Object.entries(inputObj)) {
      if (key === item) {
        const objToArray = Object.keys(inputObj[item]).map(
          i => inputObj[item][i]
        );
        const singular = item.slice(0, -1);
        const dataToMap = objToArray.map(item => ({
          value: `${item[singular].id}`,
          label: `${item[singular].name}`
        }));
        outputObj[`${item}`] = dataToMap;
      }
    }
  });
};
