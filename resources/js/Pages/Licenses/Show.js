import React from 'react';

const ShowLicense = ({license, property_types: propertyTypes}) => {
    return (
        <div className="m-10">
            <h3 className="text-xl my-5">
                <span>نام مدرک: </span>
                <span>{license.name}</span>
            </h3>
            <div>
                <h4 className="text-lg mt-5">ویژگی ها</h4>
                {
                    propertyTypes.map(propertyType => (
                        <div key={propertyType.id} className="my-3">
                            <div>
                                <span>نام ویژگی: </span>
                                <span>{propertyType.name}</span>
                            </div>
                            <div>
                                <span>نوع ویژگی: </span>
                                <span>{propertyType.value_type}</span>
                            </div>
                            <div>
                                <span>راهنمای ویژگی: </span>
                                <span>{propertyType.hint}</span>
                            </div>
                            <div>
                                <span>نمایش به پیدا کننده: </span>
                                <span>{propertyType.show_to_finder ? 'بله' : 'خیر'}</span>
                            </div>
                            <div>
                                <span>نمایش به گم کننده: </span>
                                <span>{propertyType.show_to_loser ? 'بله' : 'خیر'}</span>
                            </div>
                        </div>
                    ))
                }
            </div>
        </div>
    )
}

export default ShowLicense;
