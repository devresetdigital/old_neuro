import React from 'react';
import styled from 'styled-components';

const CampaignTableRow = props => {
  return (
    <tr>
      <td>{props.id}</td>
      <td>{props.status}</td>
      <td>{props.name}</td>
      <td>{props.spent}</td>
      <td>{props.monetary_goal}</td>
      <td>{props.impression}</td>
      <td>{props.impression_goal}</td>
    </tr>
  );
};

export default CampaignTableRow;
