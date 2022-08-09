import React, { useContext, useEffect } from 'react';
import StrategyCard from './StrategyCard';
import { Context as StrategyStatusContext } from '../context/StrategyStatusContext';

const StrategyCardList = props => {
  // const {
  //   state: { loaded, strategies },
  //   fetchStrategies
  // } = useContext(StrategyStatusContext);
  // console.log(props);
  // useEffect(() => {
  //   fetchStrategies(props.campaign_id);
  // }, []);
  // useEffect(() => {
  //   // fetchStrategies(props.campaign.campaign_id);
  //   // console.count();
  //   if (loaded) {
  //     // console.log(props.campaign_id);
  //     // fetchStrategies(props.campaign_id);
  //     console.log('Strategy Loaded.');
  //     console.log(strategies);
  //     console.count();

  //     // useEffect(() => {
  //     //   fetchStrategies(props.campaign.campaign_id);
  //     //   console.count();
  //     // }, []);
  //     // fetchStrategies(props.campaign.campaign_id);
  //   } else {
  //     console.log('Strategies Loading...');
  //     fetchStrategies(props.campaign_id);
  //     console.count();
  //   }
  //   if (loaded) {
  //     console.count();
  //   }
  // }, []);

  const renderList = () => {
    if (!loaded) {
      console.log('Loading Strategies');
      fetchStrategies(props.campaign_id);
    } else {
      console.log('Strategies Loaded');
      // return <div>Hello</div>;
      // return <StrategyCard />;
    }
  };

  return (
    <div>
      <div className="ui grid">
        {/* <div className="two wide column">{props.strategy.id}</div>
        <div className="six wide column">{props.strategy.name}</div>
        <div className="two wide column">{props.strategy.spent}</div> */}
        {/* {renderList()} */}
      </div>
      <div className="ui divider"></div>
    </div>
  );
};

export default StrategyCardList;
