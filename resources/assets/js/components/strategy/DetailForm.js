import React, { useState, useEffect, useContext } from 'react';
import { useForm } from 'react-hook-form';
import DatePicker from 'react-datepicker';
import Select from 'react-select';
import { Context as StrategyContext } from '../../context/StrategyContext';

import 'react-datepicker/dist/react-datepicker.css';

const DetailForm = ({ initialValues }) => {
  const {
    syncStatus,
    syncName,
    syncBudget,
    syncChannel,
    syncDateStart,
    syncDateEnd,
    syncGoalType,
    syncGoalValue,
    syncPacingMonetaryType,
    syncPacingMonetaryAmount,
    syncPacingMonetaryInterval,
    syncPacingImpressionType,
    syncPacingImpressionAmount,
    syncPacingImpressionInterval,
    syncFrequencyCapType,
    syncFrequencyCapAmount,
    syncFrequencyCapInterval
  } = useContext(StrategyContext);

  const transformTime = date => {
    const output = date * 1000;
    return output;
  };

  const getLabelForValue = (value, arr) => {
    for (let i = 0; i < arr.length; i++) {
      if (value == arr[i].value) {
        return arr[i];
      }
    }
  };

  const SelectedChannel = getLabelForValue(
    initialValues.channel,
    CHANNEL_FIELDS
  );

  const SelectedGoalType = getLabelForValue(
    initialValues.goal_type,
    GOAL_TYPE_FIELDS
  );

  const SelectedGoalValueBidFor = getLabelForValue(
    initialValues.goal_values.bid_for,
    GOAL_VALUE_BID_FOR_FIELDS
  );

  const SelectedPacingMoneratyType = getLabelForValue(
    initialValues.pacing_monetary.type,
    PACING_MONETARY_TYPE_FIELDS
  );

  const SelectedPacingMoneratyInterval = getLabelForValue(
    initialValues.pacing_monetary.interval,
    PACING_MONETARY_INTERVAL_FIELDS
  );

  const SelectedPacingImpressionType = getLabelForValue(
    initialValues.pacing_impression.type,
    PACING_IMPRESSION_TYPE_FIELDS
  );

  const SelectedPacingImpressionInterval = getLabelForValue(
    initialValues.pacing_impression.interval,
    PACING_IMPRESSION_INTERVAL_FIELDS
  );

  const SelectedFrequencyCapType = getLabelForValue(
    initialValues.frequency_cap.type,
    FREQUENCY_CAP_TYPE_FIELDS
  );

  const SelectedFrequencyCapInterval = getLabelForValue(
    initialValues.frequency_cap.interval,
    FREQUENCY_CAP_INTERVAL_FIELDS
  );

  const date_to_start = transformTime(initialValues.date_start);
  const date_to_end = transformTime(initialValues.date_end);
  console.log(date_to_start);
  console.log(date_to_end);
  const [name, setName] = useState(initialValues.name);
  const [status, setStatus] = useState(initialValues.status);
  const [channel, setChannel] = useState(initialValues.channel);
  const [dateStart, setDateStart] = useState(date_to_start);
  const [dateEnd, setDateEnd] = useState(date_to_end);
  const [budget, setBudget] = useState(initialValues.budget);
  const [goal_type, setGoalType] = useState(initialValues.goal_type);
  const [goal_value, setGoalValues] = useState(initialValues.goal_values.value);
  const [goal_bid_for, setGoalBidFor] = useState(
    initialValues.goal_values.bir_for
  );
  const [goal_min_bid, setGoalMinBid] = useState(
    initialValues.goal_values.min_bid
  );
  const [goal_max_bid, setGoalMaxBid] = useState(
    initialValues.goal_values.max_bid
  );

  // Pacing Monetary
  const [pacing_monetary_type, setPacingMonetaryType] = useState(
    initialValues.pacing_monetary_type
  );
  const [pacing_monetary_amount, setPacingMonetaryAmount] = useState(
    initialValues.pacing_monetary_amount
  );
  const [pacing_monetary_interval, setPacingMonetaryInterval] = useState(
    initialValues.pacing_monetary_interval
  );

  // Pacing Impression
  const [pacing_impression_type, setPacingImpressionType] = useState(
    initialValues.pacing_impression_type
  );
  const [pacing_impression_amount, setPacingImpressionAmount] = useState(
    initialValues.pacing_impression_amount
  );
  const [pacing_impression_interval, setPacingImpressionInterval] = useState(
    initialValues.pacing_impression_interval
  );

  // Frequency Cap
  const [frequency_cap_type, setFrequencyCapType] = useState(
    initialValues.frequency_cap_type
  );
  const [frequency_cap_amount, setFrequencyCapAmount] = useState(
    initialValues.frequency_cap_amount
  );
  const [frequency_cap_interval, setFrequencyCapInterval] = useState(
    initialValues.frequency_cap_interval
  );
  const renderStatusButton = state => {
    const active = state == 1;

    return (
      <div>
        {active ? (
          <button
            className="btn btn-primary"
            onClick={() => {
              setStatus(0);
              syncStatus(0);
            }}
          >
            ACTIVE
          </button>
        ) : (
          <button
            className="btn btn-primary"
            style={{ backgroundColor: 'grey' }}
            onClick={() => {
              setStatus(1);
              syncStatus(1);
            }}
          >
            INACTIVE
          </button>
        )}
      </div>
    );
  };

  return (
    <div className="ui grid">
      <div className="sixteen wide column">
        {/* Status */}
        {renderStatusButton(status)}
        <form className="ui form">
          {/* Name */}
          <div className="field">
            <label>Name</label>
            <input
              value={name}
              onChange={event => {
                setName(event.target.value);
                syncName(event.target.value);
              }}
            />
          </div>
          <div className="fields">
            {/* Channel */}
            <div className="four wide field">
              <label>Channel</label>
              <Select
                defaultValue={SelectedChannel}
                options={CHANNEL_FIELDS}
                onChange={event => {
                  setChannel(event);
                  syncChannel(event.value);
                }}
              />
            </div>
            {/* Budget */}
            <div className="eight wide field">
              <label>Budget</label>
              <div className="ui right labeled input">
                <label htmlFor="amount" className="ui label">
                  $
                </label>
                <input
                  value={budget}
                  onChange={event => {
                    setBudget(event.target.value);
                    syncBudget(event.target.value);
                  }}
                />
                <div className="ui basic label">.00</div>
              </div>
            </div>
            {/* Date Start */}
            <div className="field">
              <label>Date Start</label>
              <DatePicker
                selected={dateStart}
                onChange={event => {
                  setDateStart(event.getTime());
                  syncDateStart(event.getTime());
                }}
              />
            </div>
            {/* Date End */}
            <div className="field">
              <label>Date End</label>
              <DatePicker
                selected={dateEnd}
                onChange={event => {
                  setDateEnd(event);
                  syncDateEnd(event);
                }}
              />
            </div>
          </div>

          <div className="field">
            <h4>Goals</h4>
          </div>
          <div className="fields">
            {/* Goal Type */}
            <div className="four wide field">
              <Select
                defaultValue={SelectedGoalType}
                options={GOAL_TYPE_FIELDS}
                onChange={event => {
                  setGoalType(event);
                  syncGoalType(event);
                }}
              />
            </div>
            {/* Goal Values */}
            <div className="four wide field">
              <input
                value={goal_value}
                onChange={event => {
                  setGoalValues(event.target.value);
                  syncGoalValues(event.target.value);
                }}
              />
            </div>
            <div className="four wide field">
              <Select
                defaultValue={SelectedGoalValueBidFor}
                options={GOAL_VALUE_BID_FOR_FIELDS}
                onChange={event => {
                  setGoalBidFor(event.target.value);
                  syncGoalBidFor(event.target.value);
                }}
              />
            </div>
            <div className="four wide field">
              <input
                value={goal_min_bid}
                onChange={event => {
                  setGoalMinBid(event.target.value);
                }}
              />
            </div>
            <div className="four wide field">
              <input
                value={goal_max_bid}
                onChange={event => {
                  setGoalMaxBid(event.target.value);
                }}
              />
            </div>
          </div>
          <div className="three fields">
            {/* Pacing Monetary */}
            <div className="field">
              <label>Pacing Monetary Type</label>
              <Select
                defaultValue={SelectedPacingMoneratyType}
                options={PACING_MONETARY_TYPE_FIELDS}
                onChange={event => {
                  setPacingMonetaryType(event);
                  syncPacingMonetaryType(event.value);
                }}
              />
            </div>
            <div className="field">
              <label>Pacing Monetary Amount</label>
              <input
                value={pacing_monetary_amount}
                onChange={event => {
                  setPacingMonetaryAmount(event.target.value);
                  syncPacingMonetaryAmount(event.target.value);
                }}
              />
            </div>
            <div className="field">
              <label>Pacing Monetary Interval</label>
              <Select
                defaultValue={SelectedPacingMoneratyInterval}
                options={PACING_MONETARY_INTERVAL_FIELDS}
                onChange={event => {
                  setPacingMonetaryInterval(event);
                  syncPacingMonetaryInterval(event.value);
                }}
              />
            </div>
          </div>
          <div className="three fields">
            {/* Pacing Impression */}
            <div className="field">
              <label>Pacing Impression Type</label>
              <Select
                defaultValue={SelectedPacingImpressionType}
                options={PACING_IMPRESSION_TYPE_FIELDS}
                onChange={event => {
                  setPacingImpressionType(event);
                  syncPacingImpressionType(event.value);
                }}
              />
            </div>
            <div className="field">
              <label>Pacing Impression Amount</label>
              <input
                value={pacing_impression_amount}
                onChange={event => {
                  setPacingImpressionAmount(event.target.value);
                  syncPacingImpressionAmount(pacing_impression_amount);
                }}
              />
            </div>
            <div className="field">
              <label>Pacing Impression Interval</label>
              <Select
                defaultValue={SelectedPacingImpressionInterval}
                options={PACING_IMPRESSION_INTERVAL_FIELDS}
                onChange={event => {
                  {
                    setPacingImpressionInterval(event);
                    syncPacingImpressionInterval(event.value);
                  }
                }}
              />
            </div>
          </div>
          <div className="three fields">
            {/* Frequency Cap */}
            <div className="field">
              <label>Frequency Cap Type</label>
              <Select
                defaultValue={SelectedFrequencyCapType}
                options={FREQUENCY_CAP_TYPE_FIELDS}
                onChange={event => {
                  {
                    setFrequencyCapType(event);
                    syncFrequencyCapType(event.value);
                  }
                }}
              />
            </div>
            <div className="field">
              <label>Frequency Cap Amount</label>
              <input
                value={frequency_cap_amount}
                onChange={event => {
                  setFrequencyCapAmount(event.target.value);
                  syncFrequencyCapAmount(frequency_cap_amount);
                }}
              />
            </div>
            <div className="field">
              <label>Frequency Cap Interval</label>
              <Select
                defaultValue={SelectedFrequencyCapInterval}
                options={FREQUENCY_CAP_INTERVAL_FIELDS}
                onChange={event => {
                  {
                    setFrequencyCapInterval(event);
                    syncFrequencyCapInterval(event.value);
                  }
                }}
              />
            </div>
          </div>
        </form>
      </div>
    </div>
  );
};

export default DetailForm;
