import React, { useContext, useEffect } from 'react';
import CampaignCard from './CampaignCard';
// import { Context as CampaignStatusContext } from '../context/CampaignStatusContext';

const CampaignCardList = props => {
  console.log(props);
  // const arr = Object.values(props.campaigns);
  // const renderList = arr.map(campaign => {
  //   // console.log(campaign.strategies);
  //   return <CampaignCard campaign={campaign} key={campaign.campaign_id} />;
  // });

  // const renderList = arr.map(campaign => {
  //   // console.log(campaign.strategies);
  //   return (
  //     <CampaignCard
  //       name={campaign.name}
  //       key={campaign.campaign_id}
  //       id={campaign.campaign_id}
  //       monetary_goal={campaign.monetary_goal}
  //       spent={campaign.spent}
  //       impression_goal={campaign.impression_goal}
  //       impression={campaign.impression}
  //       strategies={campaign.strategies}
  //       status={campaign.status}
  //     />
  //   );
  // });
  // const renderList = arr.map(campaign => {
  //   // console.log(campaign);
  //   setTimeout(() => {
  //     if (campaign.strategies) {
  //       console.log(campaign.strategies[0]);
  //     }
  //     return (
  //       <CampaignCard
  //         name={campaign.name}
  //         key={campaign.campaign_id}
  //         id={campaign.campaign_id}
  //         monetary_goal={campaign.monetary_goal}
  //         spent={campaign.spent}
  //         impression_goal={campaign.impression_goal}
  //         impression={campaign.impression}
  //         strategies={campaign.strategies}
  //         status={campaign.status}
  //       />
  //     );
  //   }, 3000);
  //   return null;
  // });
  return (
    <div className="ui container">
      <div className="">Campaign</div>
      <div className="">{renderList}</div>
    </div>
  );
};

export default CampaignCardList;

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
