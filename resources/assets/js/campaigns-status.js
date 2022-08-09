import React from 'react';
import ReactDOM from 'react-dom';
import CampaignStatusScreen from './views/CampaignStatusScreen';
import { Provider as CampaignStatusProvider } from './context/CampaignStatusContext';
import { Provider as TallyProvider } from './context/TallyContext';

const App = () => {
  return (
    <TallyProvider>
      <CampaignStatusProvider>
        <CampaignStatusScreen />
      </CampaignStatusProvider>
    </TallyProvider>
  );
};

ReactDOM.render(<App />, document.querySelector('#campaigns-status'));
