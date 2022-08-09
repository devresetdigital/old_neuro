import React from 'react';

const switchMenu = (state, updateState, updateContext, showOrHide) => {
  const str = ['Include', 'Exclude', 'All'];

  const include = state == 1;
  const exclude = state == 2;
  const all = state == 3;

  const updateAll = number => {
    updateState(number);
    updateContext(number);
    showOrHide(true);
  };

  const updateIncExc = number => {
    updateState(number);
    updateContext(number);
    showOrHide(false);
  };

  return (
    <div className="btn-group btn-group-sm">
      {all ? (
        <button className="btn btn-dark" disabled>
          {str[2]}
        </button>
      ) : (
        <button
          className="btn btn-primary"
          onClick={() => updateAll(3, showOrHide)}
        >
          {str[2]}
        </button>
      )}
      {include ? (
        <button className="btn btn-dark" disabled>
          {str[0]}
        </button>
      ) : (
        <button
          className="btn btn-primary"
          onClick={() => updateIncExc(1, showOrHide)}
        >
          {str[0]}
        </button>
      )}
      {exclude ? (
        <button className="btn btn-dark" disabled>
          {str[1]}
        </button>
      ) : (
        <button
          className="btn btn-primary"
          onClick={() => updateIncExc(2, showOrHide)}
        >
          {str[1]}
        </button>
      )}
    </div>
  );
};

export default switchMenu;
