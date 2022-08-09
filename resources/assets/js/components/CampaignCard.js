import React, { useState, useEffect, useContext } from 'react';
import StrategyContainer from './StrategyContainer';
import styled from 'styled-components';

const ContainerCard = styled.div`
  margin-bottom: 1px;
  border: 1px solid rgba(34, 36, 38, 0.15);
  background-color: #fff;
  border-radius: 2px;
`;

const Column = styled.div`
  border-right: 1px solid rgba(34, 36, 38, 0.15);
  border-bottom: 1px solid rgba(34, 36, 38, 0.15);
  ${props =>
    props.cellName
      ? 'min-width: 286px;'
      : props.cellId
      ? 'min-width: 32px;'
      : props.cellStatus
      ? 'min-width: 85px;'
      : 'width: fit-content;'};
  display: table-cell;
  padding: 4px;
`;

const Status = styled.div`
  background: ${props => (props.active ? 'limegreen' : 'red')};
  color: white;
  font-size: 1em;
  font-weight: bold;
  width: fit-content;
  padding: 0.1em 0.4em 0em 0.4em;
  border: 2px solid ${props => (props.active ? 'limegreen' : 'red')};
  border-radius: 3px;
`;

const CampaignCard = props => {
  const [hidden, toggleStrategy] = useState(true);
  const strategies = Object.values(props.campaign.strategies);
  const impressions_percent = `${Math.round(
    (props.campaign.impression / props.campaign.impression_value) * 100
  )} %`;

  const monetary_percent = `${Math.round(
    (props.campaign.spent / props.campaign.monetary_value) * 100
  )} %`;

  const roundWithDecimal = num => {
    return Math.round((num + Number.EPSILON) * 100) / 100;
  };

  const status =
    props.campaign.status == 1 ? (
      <Status active>ACTIVE</Status>
    ) : (
      <Status>INACTIVE</Status>
    );

  return (
    <ContainerCard>
      <Column cellId>{props.campaign.campaign_id}</Column>
      <Column cellStatus>{status}</Column>
      <Column>
        <button
          className="btn btn-primary"
          onClick={() => {
            hidden ? toggleStrategy(false) : toggleStrategy(true);
          }}
        >
          <i className="voyager-lab" />
        </button>
      </Column>
      <Column cellName>
        <a
          href={`http://${SERVER_HOST}/admin/campaigns/${props.campaign.campaign_id}`}
        >
          {`${props.campaign.name.slice(0, 32)}...`}
        </a>
      </Column>
      <Column>
        {roundWithDecimal(props.campaign.spent)}
        {isNaN(props.campaign.monetary_value)
          ? '/ ∞'
          : `/ ${props.campaign.monetary_value} - ${monetary_percent}`}
      </Column>
      <Column>
        {roundWithDecimal(props.campaign.impression)}
        {isNaN(props.campaign.impression_value)
          ? '/ ∞'
          : `/ ${props.campaign.impression_value} - ${impressions_percent}`}
      </Column>

      <div className="ui container">
        <StrategyContainer strategies={strategies} hidden={hidden} />
      </div>
    </ContainerCard>
  );
};

export default CampaignCard;
