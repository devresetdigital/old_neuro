import React, { useState, useContext } from 'react';
import { Context as CampaignContext } from '../context/CampaignContext';

const CampaignForm = ({ initialValues }) => {
  const { updateCampaign } = useContext(CampaignContext);
  const [name, setName] = useState(initialValues.name);
  const id = initialValues.id;

  const handleSubmit = event => {
    event.preventDefault();
    updateCampaign(id, name);
  };

  return (
    <div>
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Name</label>
          <input
            className="form-control"
            value={name}
            onChange={event => setName(event.target.value)}
          />
        </div>
        <button className="btn btn-primary">Save</button>
      </form>
    </div>
  );
};

CampaignForm.defaultProps = {
  initialValues: {
    id: '',
    name: ''
  }
};

export default CampaignForm;
