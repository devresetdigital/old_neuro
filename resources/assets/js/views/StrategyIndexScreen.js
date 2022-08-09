import React, { useContext, useState } from 'react';
import Select from 'react-select';
import { Context as StrategyContext } from '../context/StrategyContext';

const StrategyIndexScreen = () => {
  const [id, setId] = useState();
  const { duplicateStrategy } = useContext(StrategyContext);
  return (
    <div>
      <Select
        options={STRATEGIES}
        onChange={event => {
          setId(event.value);
        }}
      />
      <button onClick={() => duplicateStrategy(id)} className="btn btn-success">
        Save
      </button>
    </div>
  );
};

export default StrategyIndexScreen;
