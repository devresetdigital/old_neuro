import React, { useContext, useEffect } from 'react';
import { Context as StrategyContext } from '../context/StrategyContext';
import DetailForm from '../components/strategy/DetailForm';
import CreativesForm from '../components/strategy/CreativesForm';
import DataForm from '../components/strategy/DataForm';
import TargetingForm from '../components/strategy/TargetingForm';

const StrategyEditScreen = () => {
  const {
    state,
    state: {
      name,
      status,
      channel,
      date_start,
      date_end,
      budget,
      goal_type,
      goal_values,
      pacing_monetary,
      pacing_monetary_type,
      pacing_monetary_amount,
      pacing_monetary_interval,
      pacing_impression,
      pacing_impression_type,
      pacing_impression_amount,
      pacing_impression_interval,
      frequency_cap,
      frequency_cap_type,
      frequency_cap_amount,
      frequency_cap_interval,
      concepts,
      custom_datas,
      pixels_lists,
      sitelists,
      iplists,
      pmps_data,
      pmps_inc_exc,
      ssps_data,
      ziplists_data,
      ziplists_inc_exc,
      cities_data,
      cities_inc_exc,
      regions_data,
      regions_inc_exc,
      countries_data,
      countries_inc_exc,
      inventory_types_data,
      inventory_types_inc_exc,
      devices_data,
      isps_data,
      oss_data,
      oss_inc_exc,
      browsers_data,
      geofencing,
      loaded
    },
    getStrategy,
    updateStrategy
  } = useContext(StrategyContext);

  useEffect(() => {
    getStrategy();
  }, []);

  const renderForm = () => {
    if (!loaded) {
      return <div>Loading...</div>;
    } else {
      return (
        <div className="tab-content">
          <div id="details-v2" className="tab-pane fade in active">
            <DetailForm
              initialValues={{
                name,
                status,
                channel,
                date_start,
                date_end,
                budget,
                goal_type,
                goal_values,
                pacing_monetary,
                pacing_monetary_type,
                pacing_monetary_amount,
                pacing_monetary_interval,
                pacing_impression,
                pacing_impression_type,
                pacing_impression_amount,
                pacing_impression_interval,
                frequency_cap,
                frequency_cap_type,
                frequency_cap_amount,
                frequency_cap_interval
              }}
            />
          </div>
          <div id="creative-v2" className="tab-pane fade in">
            <CreativesForm
              initialValues={{
                concepts
              }}
            />
          </div>
          <div id="targeting-v2" className="tab-pane fade in">
            <TargetingForm
              initialValues={{
                sitelists,
                iplists,
                pmps_data,
                pmps_inc_exc,
                ssps_data,
                ziplists_data,
                ziplists_inc_exc,
                cities_data,
                cities_inc_exc,
                regions_data,
                regions_inc_exc,
                countries_data,
                countries_inc_exc,
                inventory_types_data,
                inventory_types_inc_exc,
                devices_data,
                isps_data,
                oss_data,
                oss_inc_exc,
                browsers_data,
                geofencing
              }}
            />
          </div>
          <div id="data-v2" className="tab-pane fade in">
            <DataForm
              initialValues={{
                custom_datas,
                pixels_lists
              }}
            />
          </div>
        </div>
      );
    }
  };

  return (
    <div>
      <ul className="nav nav-tabs">
        <li className="active">
          <a data-toggle="tab" href="#details-v2">
            Details
          </a>
        </li>
        <li>
          <a data-toggle="tab" href="#creative-v2">
            Creatives
          </a>
        </li>
        <li>
          <a data-toggle="tab" href="#targeting-v2">
            Targeting
          </a>
        </li>
        <li>
          <a data-toggle="tab" href="#data-v2">
            Data
          </a>
        </li>
      </ul>
      <div className="panel-body">{renderForm()}</div>
      <button className="btn btn-primary" onClick={() => updateStrategy(state)}>
        Save
      </button>
      <button
        onClick={() => alert(JSON.stringify(state))}
        className="ui button"
      >
        STATE
      </button>
    </div>
  );
};

export default StrategyEditScreen;
