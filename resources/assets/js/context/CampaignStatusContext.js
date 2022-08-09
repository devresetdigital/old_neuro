import createDataContext from './createDataContext';
import tally from '../tally';
import instance from '../api';
import { getDate, getYear, getHours, getMonth, subHours } from 'date-fns';
import { utcToZonedTime } from 'date-fns-tz';

const serviceReducer = (state, action) => {
  switch (action.type) {
    case 'fetch_campaigns':
      return { loaded: true, campaigns: action.payload };
    case 'sorted_campaigns':
      return { loaded: true, campaigns: action.payload };
    default:
      return state;
  }
};

const getStrategy = async item => {
  let strategies = await instance.get(
    `/v2/strategies_by_campaign/${item.campaign_id}`
  );
  item['strategies'] = strategies.data;
  return Promise.resolve(item);
};

const fetchStrategies = async campaigns => {
  return Promise.all(campaigns.map(item => getStrategy(item)));
};

const sortCampaigns = dispatch => data => {
  let sortedData = data.sort((a, b) => (a.status < b.status ? 1 : -1));
  console.log(sortedData);
  // const higherSpent = data.filter(campaign => {
  //   console.log(campaign);
  // });
  // const result = data.filter(campaign => campaign);
  dispatch({ type: 'sorted_campaigns', payload: sortedData });
};

const searchCampaigns = dispatch => (data, term) => {
  let sortedData = [];
  const value = term.toLowerCase();

  if (data.length == 0) {
    console.log('data is empty');
  } else {
    data.forEach(campaign => {
      if (campaign.name.toLowerCase().includes(value)) {
        sortedData.push(campaign);
      } else {
        campaign.strategies.forEach(strategy => {
          const found = strategy.name.toLowerCase().includes(value);
          if (found) {
            const duplicate = sortedData.find(
              y => y.campaign_id == strategy.campaign_id
            );
            if (duplicate) {
            } else {
              sortedData.push(campaign);
            }
          }
        });
      }
    });
  }
  dispatch({ type: 'sorted_campaigns', payload: sortedData });
};

const fetchCampaigns = dispatch => async () => {
  const response = await instance.get(`/v2/campaigns`);
  const arr = Object.values(response.data);

  let today = new Date();
  today = utcToZonedTime(today, 'Europe/London');
  today = subHours(today, 1);
  const utcToday = date => {
    let year = getYear(today);
    let day = getDate(today);

    if (day < 10) {
      day = ('0' + day).slice(-2);
    }

    let month = ('0' + (getMonth(today) + 1)).slice(-2);
    let time = '';
    if (date) {
      time = ('0' + getHours(date)).slice(-2);
    } else {
      time = '00';
    }
    year = year % 2000;
    return `${year}${month}${day}${time}`;
  };

  const date_start = utcToday();
  const date_end = utcToday(today);

  console.log(date_start);
  console.log(date_end);
  const tallyResponse = await tally.get(
    `wl_2_impressions?groupby=advcamp_2,advcamp_3&from=${date_start}&until=${date_end}&format=json&orderby=0&nocache=1`
  );

  const tallyResult = Object.entries(tallyResponse.data).map(item => {
    let campaign_id = item[0].split(',')[0] % 200000;
    let strategy_id = item[0].split(',')[1] % 200000;
    let impression = item[1][0];
    let spent = item[1][3];
    let strategy = {
      campaign_id: `${campaign_id}`,
      strategy_id: `${strategy_id}`,
      impression: parseInt(`${impression}`),
      spent: parseInt(`${spent}`) / 1000
    };
    return strategy;
  });

  const sumValues = (value, sum) => {
    value = sum;
  };

  fetchStrategies(arr).then(data => {
    data.forEach(item => {
      item.strategies.forEach(strategy => {
        const monetary_value = parseInt(strategy.pacing_monetary.split(',')[1]);
        const impression_value = parseInt(
          strategy.pacing_impression.split(',')[1]
        );
        const x = tallyResult.find(y => y.strategy_id == strategy.id);
        if (x) {
          strategy['spent'] = x.spent;
          strategy['impression'] = x.impression;
          // Sum Spent to Campaign
          !item['spent']
            ? (item['spent'] = x.spent)
            : (item['spent'] += x.spent);
          // Sum Impression to Campaign
          !item['impression']
            ? (item['impression'] = x.impression)
            : (item['impression'] += x.impression);
        } else {
          strategy['spent'] = 0;
          strategy['impression'] = 0;
          item['spent'] = 0;
          item['impression'] = 0;
        }
        strategy['impression_value'] = impression_value;
        strategy['monetary_value'] = monetary_value;
        // Sum strategy impressions to Campaign
        !item['impression_value']
          ? (item['impression_value'] = impression_value)
          : (item['impression_value'] += impression_value);
        // Sum strategy monetary to Campaign
        !item['monetary_value']
          ? (item['monetary_value'] = monetary_value)
          : (item['monetary_value'] += monetary_value);
      });
    });
    // WHO HAS THE HIGHER SPENT?
    let sortedData = data.sort((a, b) => (a.status > b.status ? 1 : -1));
    sortedData = sortedData.sort((a, b) => (a.spent < b.spent ? 1 : -1));
    console.log(sortedData);
    // const higherSpent = data.filter(campaign => {
    //   console.log(campaign);
    // });
    // const result = data.filter(campaign => campaign);
    dispatch({ type: 'fetch_campaigns', payload: sortedData });
  });
};

export const { Context, Provider } = createDataContext(
  serviceReducer,
  { fetchCampaigns, sortCampaigns, searchCampaigns },
  { loaded: false, id: '', name: '', campaigns: [] }
);
