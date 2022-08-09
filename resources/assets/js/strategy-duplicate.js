import React from 'react';
import ReactDOM from 'react-dom';
import StrategyIndexScreen from './views/StrategyIndexScreen';
import { Provider as StrategyProvider } from './context/StrategyContext';

const App = () => {
  return (
    <StrategyProvider>
      <StrategyIndexScreen />
    </StrategyProvider>
  );
};

ReactDOM.render(<App />, document.querySelector('#strategy-duplicate'));
