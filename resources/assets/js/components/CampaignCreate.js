import React, { useContext, useEffect } from 'react';
import CampaignForm from './CampaignForm';

const CampaignCreate = () => {
  const renderForm = () => {
    if (id === '') {
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

export default CampaignCreate;
