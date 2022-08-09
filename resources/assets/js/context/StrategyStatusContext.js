import createDataContext from './createDataContext';
import instance from '../api';

const serviceReducer = (state, action) => {
  switch (action.type) {
    case 'fetch_strategies':
      return { loaded: true, strategies: action.payload };
    default:
      return state;
  }
};

const fetchStrategies = dispatch => async campaign_id => {
  // const response = await instance.get(
  //   `/v2/strategies_by_campaign/${campaign_id}`
  // );
  // console.log(response.data);
  // const data = ['Strategies'];
  dispatch({ type: 'fetch_strategies', payload: 'strategies' });
};

export const { Context, Provider } = createDataContext(
  serviceReducer,
  { fetchStrategies },
  { loaded: false, strategies: '' }
);
