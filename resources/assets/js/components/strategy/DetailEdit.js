import React, { useContext, useEffect } from 'react';
import { Context as StrategyContext } from '../../context/StrategyContext';
import DetailForm from './DetailForm';

const DetailEdit = () => {
  const {
    state: {
      loaded,
      name,
      status,
      channel,
      date_start,
      date_end,
      budget,
      goal_type,
      goal_values,
      pacing_monetary,
      pacing_impression,
      frequency_cap
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
        <DetailForm
          onSubmit={updateStrategy}
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
            pacing_impression,
            frequency_cap
          }}
        />
      );
    }
  };
  return <div>{renderForm()}</div>;
};

export default DetailEdit;
