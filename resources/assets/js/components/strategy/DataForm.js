import React, { useState } from 'react';
import Select from 'react-select';

const DataForm = ({ onSubmit, initialValues }) => {
  const [customData, setCustomData] = useState(initialValues.concepts);
  const [pixelsList, setPixelsListData] = useState(initialValues.concepts);

  return (
    <div>
      <Select
        defaultValue={pixelsList}
        isMulti
        options={PIXELS_LISTS}
        onChange={event => setPixelsListData(event)}
      />
      <Select
        defaultValue={customData}
        isMulti
        options={CUSTOM_DATAS}
        onChange={event => setCustomData(event)}
      />
    </div>
  );
};

export default DataForm;
