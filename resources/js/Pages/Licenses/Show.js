import React from 'react';
import Button from '../../Components/Button';
import { Inertia } from '@inertiajs/inertia';
import {Head} from '@inertiajs/inertia-react';
import Authenticated from '@/Layouts/Authenticated';

const ShowLicense = ({license, property_types: propertyTypes, auth, errors: authenticatedErrors}) => {
    const handleClick = () => {
        if(confirm('همه اطلاعات مدرک حذف خواهد شد. آیا اطمینان دارید؟')){
            Inertia.delete(route('licenses.destroy', [license.id]));
        }
    }
    return (
        <Authenticated
            auth={auth}
            errors={authenticatedErrors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">مشخصات مدرک</h2>}
        >
            <Head title="Show License" />

            <div className="m-10 bg-white p-10 border-2 shadow">
                <h3 className="text-3xl my-5 font-extralight">
                    <span>نام مدرک: </span>
                    <span>{license.name}</span>
                </h3>
                <div>
                    <h4 className="text-lg mt-5 font-bold">ویژگی ها</h4>
                    {
                        propertyTypes.map((propertyType, idx) => (
                            <div key={propertyType.id} className={`my-3 pt-4${idx!==0&&" border-t"}`}>
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
                <Button
                    type="button"
                    handleClick={handleClick}
                >
                    حذف کردن مدرک
                </Button>
            </div>
        </Authenticated>
    )
}

export default ShowLicense;
