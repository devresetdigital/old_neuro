import React, { useContext, useEffect } from 'react';
import { Context as CampaignContext } from '../context/CampaignContext';
import CampaignForm from './CampaignForm';

const CampaignEdit = () => {
  const {
    state: { id, name },
    getCampaign
  } = useContext(CampaignContext);

  useEffect(() => {
    getCampaign();
  }, []);

  const renderForm = () => {
    if (name === '') {
      return <div>Loading...</div>;
    } else {
      return (
        <CampaignForm
          initialValues={{
            id,
            name: `${name}`
          }}
        />
      );
    }
  };
  return <div>{renderForm()}</div>;
};

export default CampaignEdit;
