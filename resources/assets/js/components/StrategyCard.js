import React from 'react';
import styled from 'styled-components';

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

const StrategyCard = props => {
  const monetary_value = props.strategy.pacing_monetary.split(',')[1];
  const impression_value = props.strategy.pacing_impression.split(',')[1];

  const status =
    props.strategy.status == 1 ? (
      <Status active>ACTIVE</Status>
    ) : (
      <Status>INACTIVE</Status>
    );

  return (
    <tr>
      <td>{props.strategy.id}</td>
      <td>{status}</td>
      <td>
        <a href={`http://${SERVER_HOST}/admin/strategies/${props.strategy.id}`}>
          {`${props.strategy.name}`}
        </a>
      </td>
      <td>
        {props.strategy.spent ? props.strategy.spent : '-'} /{' '}
        {monetary_value ? monetary_value : '∞'}
      </td>
      <td>
        {props.strategy.impression ? props.strategy.impression : '-'} /
        {impression_value ? impression_value : '∞'}
      </td>
    </tr>
  );
};

export default StrategyCard;
