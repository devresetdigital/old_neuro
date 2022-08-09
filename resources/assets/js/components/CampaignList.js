import React, { useContext, useEffect } from 'react';
import CampaignTableRow from './CampaignTableRow';
// import { Context as CampaignStatusContext } from '../context/CampaignStatusContext';

const CampaignList = props => {
  const arr = Object.values(props.campaigns);
  const renderList = arr.map(campaign => {
    // console.log(campaign);
    return (
      <CampaignTableRow
        name={campaign.name}
        key={campaign.campaign_id}
        id={campaign.campaign_id}
        monetary_goal={campaign.monetary_goal}
        spent={campaign.spent}
        impression_goal={campaign.impression_goal}
        impression={campaign.impression}
        status={campaign.status}
      />
    );
  });
  return (
    <div className="ui container">
      <table className="ui celled table">
        <tbody>{renderList}</tbody>
      </table>
    </div>
  );
};

export default CampaignList;

// const {
//   state: { name },
//   getCampaign
// } = useContext(CampaignStatusContext);
// useEffect(() => {
//   getCampaign(35);
//   const arr = Object.entries(props.campaigns);
//   const renderList = arr.map(campaign => {
//     return <div>{campaign[1].name}</div>;
//     // return <CampaignCard name={campaign.campaign_id} />;
//   });
//   const arr = Object.entries(props.campaigns);
//   arr.forEach(item => {
//     console.log(item[1].name);
//   });
// }, []);
// props.map(campaign => {
//   return <CampaignCard name={campaign.campaign_id} />;
// });
// const renderList = props.map(campaign => {
//   return <CampaignCard name={campaign.campaign_id} />;
// });
// console.log(props.props[0]);
