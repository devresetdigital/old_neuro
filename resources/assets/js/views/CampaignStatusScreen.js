import React, { useContext, useEffect, useState } from 'react';
import { Context as CampaignStatusContext } from '../context/CampaignStatusContext';
import CampaignCard from '../components/CampaignCard';
import styled from 'styled-components';

const ButtonId = styled.button`
  margin: 8px;
`;

const CampaignStatusScreen = () => {
  const {
    state: { loaded, campaigns },
    fetchCampaigns,
    sortCampaigns,
    searchCampaigns
  } = useContext(CampaignStatusContext);

  useEffect(() => {
    fetchCampaigns();
  }, []);

  const [item, findItem] = useState('');

  const renderList = campaigns.map(campaign => {
    return <CampaignCard campaign={campaign} key={campaign.campaign_id} />;
  });

  return (
    <div className="ui container">
      <div>
        <div className="ui menu">
          <div className="item">
            <ButtonId onClick={() => sortCampaigns(campaigns)}>ID</ButtonId>
            <input
              value={this.item}
              onChange={event => findItem(event.target.value)}
              placeholder="Search ..."
            ></input>
            <button onClick={() => searchCampaigns(campaigns, item)}>
              Search
            </button>
            <button onClick={() => fetchCampaigns()}>Reset</button>
          </div>
          <div className="right menu">
            <div className="item">Resultados: {campaigns.length}</div>
          </div>
        </div>
      </div>
      <div>{loaded ? renderList : 'Fetching Servers ...'}</div>
    </div>
  );
};

export default CampaignStatusScreen;
