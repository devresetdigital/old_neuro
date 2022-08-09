import React, { useState } from 'react';
import Select from 'react-select';

const CreativesForm = ({ onSubmit, initialValues }) => {
  const renderArr = () => {
    console.log(initialValues.loaded);
    arr.map(item => <h4>{item}</h4>);
  };
  const [conceptData, setConceptData] = useState(initialValues.concepts);

  const renderConcepts = () => {
    ADVERTISER_CONCEPTS.map(item => {
      return <div key={item.id}>{item.label}</div>;
    });
  };

  return (
    <div>
      <Select
        defaultValue={conceptData}
        isMulti
        options={ADVERTISER_CONCEPTS}
        onChange={event => setConceptData(event)}
      />
    </div>
  );
};

export default CreativesForm;
