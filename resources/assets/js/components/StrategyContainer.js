import React from 'react';
import styled from 'styled-components';
import StrategyCard from './StrategyCard';

const StrategyDiv = styled.div`
  margin: 8px;
`;

const StrategyContainer = props => {
  const renderStrategies = props.strategies.map(strategy => {
    return <StrategyCard strategy={strategy} key={strategy.id} />;
  });

  return (
    <div>
      {props.hidden ? (
        <StrategyDiv>
          <table className="ui fluid celled table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Name</th>
                <th>Spent</th>
                <th>Impressions</th>
              </tr>
            </thead>
            <tbody>{renderStrategies}</tbody>
          </table>
        </StrategyDiv>
      ) : (
        false
      )}
    </div>
  );
};
export default StrategyContainer;
