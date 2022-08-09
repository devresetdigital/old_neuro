import React, { useContext, useEffect } from 'react';
import { Context as StrategyContext } from '../../context/StrategyContext';
import TargetingForm from './TargetingForm';

const TargetingEdit = () => {
  const {
    state: {
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
      loaded
    },
    getStrategy,
    updateTargeting
  } = useContext(StrategyContext);

  // const [syncTargeting] = useUpdateTargeting();

  useEffect(() => {
    getStrategy();
  }, []);

  const renderForm = () => {
    if (!loaded) {
      return <div>Loading...</div>;
    } else {
      return (
        <div>
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
              browsers_data
            }}
            onSubmit={updateTargeting}
          />
        </div>
      );
    }
  };
  return <div>{renderForm()}</div>;
};

export default TargetingEdit;
