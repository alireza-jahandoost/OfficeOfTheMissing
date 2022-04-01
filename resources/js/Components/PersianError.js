import React from 'react';

const PersianError = ({message}) => {
    return <div className={"text-red-700"}>
        <span>خطا: </span>
        <span>{message}</span>
    </div>
}

export default PersianError;
