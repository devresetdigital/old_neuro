import React from 'react';
import ReactDOM from 'react-dom';
import StrategyEditScreen from './views/StrategyEditScreen';
import { Provider as StrategyProvider } from './context/StrategyContext';

const App = () => {
  return (
    <StrategyProvider>
      <StrategyEditScreen />
    </StrategyProvider>
  );
};

ReactDOM.render(<App />, document.querySelector('#strategy-add-edit'));
