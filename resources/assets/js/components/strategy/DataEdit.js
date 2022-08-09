import React, { useContext, useEffect } from 'react';
import { Context as StrategyContext } from '../../context/StrategyContext';
import DataForm from './DataForm';

const DetaEdit = () => {
  const {
    state: { custom_datas, pixels_lists, loaded },
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
        <div>
          <DataForm
            initialValues={{
              custom_datas,
              pixels_lists
            }}
            onSubmit={updateStrategy}
          />
        </div>
      );
    }
  };
  return <div>{renderForm()}</div>;
};

export default DetaEdit;
