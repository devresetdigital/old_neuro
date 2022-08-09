import createDataContext from './createDataContext';
import tally from '../tally';
import instance from '../api';

const serviceReducer = (state, action) => {
  switch (action.type) {
    case 'get_campaigns':
      return { loaded: true, campaigns: action.payload };
    case 'fetch_campaigns':
      return { loaded: true, campaigns: action.payload };
    default:
      return state;
  }
};

const getCampaigns = dispatch => async () => {
  const response = await instance.get(`/v2/campaigns`);
  dispatch({ type: 'get_campaigns', payload: response.data });
};

const fetchCampaigns = dispatch => async (date_start, date_end) => {
  const tallyResponse = await tally.get(
    `wl_2_impressions?groupby=advcamp_1,advcamp_2&from=${date_start}&until=${date_end}&format=json&orderby=0&nocache=1`
  );

  const localResponse = await instance.get(`/v2/campaigns`);

  const localCampaigns = Object.values(localResponse.data);
  let hbCampaigns = [];

  let tallyResult = Object.entries(tallyResponse.data).map(item => {
    let campaign_id = item[0].split(',')[0] % 200000;
    let strategy_id = item[0].split(',')[1] % 200000;
    let impression = item[1][0];
    let spent = item[1][3];
    let strategy = {
      campaign_id: `${campaign_id}`,
      strategy_id: `${strategy_id}`,
      impression: `${impression}`,
      spent: `${spent}`
    };
    return strategy;
  });

  let campaigns = [];

  // const addStrategies = localCampaigns.map(async item => {
  //   let strategies = await instance.get(
  //     `/v2/strategies_by_campaign/${item.campaign_id}`
  //   );
  //   item['strategies'] = strategies.data;
  //   campaigns.push(item);
  // });

  const fetchStrategy = async item => {
    let strategies = await instance.get(
      `/v2/strategies_by_campaign/${item.campaign_id}`
    );
    item['strategies'] = strategies.data;
    return item;
  };

  const hbStrategies = async () => {
    return Promise.all(localCampaigns.map(item => fetchStrategy(item)));
  };

  // data = Campaigns with their Strategies
  // tally = HB Strategies
  hbStrategies().then(data => {
    // results form HB
    tallyResult.forEach(item => {
      let campaign = data.find(y => y.campaign_id == item.campaign_id);
      if (campaign) {
        let strategy = campaign.strategies.find(x => (x.id = item.strategy_id));
        strategy['spent'] = item.spent;
        strategy['impression'] = item.impression;
      } else {
      }
    });
  });

  // setTimeout(() => {
  //   // let campaign = campaigns.find(y => y.campaign_id == HB_CAMPAIGN_ID);
  //   // let strategy = campaign.strategies.find(x => (x.id = HB_STRATEGY_ID));
  //   // console.log(strategy);
  // }, 3000);
  // tallyResult.forEach(item => {
  //   if (hbCampaigns.length > 0) {
  //     if (hbCampaigns[hbCampaigns.length - 1].campaign_id == item.campaign_id) {
  //       hbCampaigns[hbCampaigns.length - 1].strategy_ids.push(
  //         item.strategy_ids[0]
  //       );
  //     } else {
  //       hbCampaigns.push(item);
  //     }
  //   } else {
  //     hbCampaigns.push(item);
  //   }
  // });

  let tableObject = [];

  function mergeCampaigns(hb, local) {
    for (var key in local) {
      let id = local[key].campaign_id;
      let campaign = hb.find(y => y.campaign_id == id);
      if (campaign) {
        campaign['name'] = local[key].name;
        campaign['status'] = local[key].status;
        campaign['updated_at'] = local[key].updated_at;
        campaign['monetary_goal'] = local[key].monetary_goal;
        campaign['impression_goal'] = local[key].impression_goal;
        tableObject.push(campaign);
      } else {
        tableObject.push(local[key]);
      }
    }
  }

  mergeCampaigns(hbCampaigns, localCampaigns);
  setTimeout(() => {
    dispatch({ type: 'fetch_campaigns', payload: tableObject });
    console.log(tableObject);
  }, 15000);
};

export const { Context, Provider } = createDataContext(
  serviceReducer,
  { fetchCampaigns, getCampaigns },
  { loaded: false, campaigns: [] }
);
