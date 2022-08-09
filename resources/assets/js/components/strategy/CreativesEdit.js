import React, { useContext, useEffect } from 'react';
import { Context as StrategyContext } from '../../context/StrategyContext';
import CreativesForm from './CreativesForm';

const CreativesEdit = () => {
  const {
    state: { concepts, loaded },
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
          <CreativesForm
            initialValues={{
              concepts,
              loaded
            }}
            onSubmit={updateStrategy}
          />
        </div>
      );
    }
  };
  return <div>{renderForm()}</div>;
};

export default CreativesEdit;
