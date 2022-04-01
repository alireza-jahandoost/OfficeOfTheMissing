import React from 'react';
import Authenticated from '@/Layouts/Authenticated';
import {Head, Link} from '@inertiajs/inertia-react';
import { Inertia } from '@inertiajs/inertia'
import Button from '../../Components/Button';

const ShowFound = ({auth, errors: authenticatedErrors, license, property_types, found}) => {
    const handleLicenseDeletation = () => {
        if(confirm('مدرک به طور دایم حذف خواهد شد. از انجام این کار اطمینان دارید؟')){
            Inertia.delete(route('licenses.founds.destroy', [license.id, found.id]));
        }
    }
    return (
        <Authenticated
            auth={auth}
            errors={authenticatedErrors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">
                <span>مشاهده مشخصات مدرک</span>
            </h2>}
        >
            <Head title="Create Found" />

            <div className="m-10">
                <h3 className={"text-xl"}>
                    <span>نوع مدرک: </span>
                    <span>{license.name}</span>
                </h3>

                <Button handleClick={handleLicenseDeletation} className={"my-4 ml-4"}>حذف مدرک</Button>

                <Link href={route('licenses.founds.edit', [license.id, found.id])}>
                    <Button>ویرایش مدرک</Button>
                </Link>

                {
                    found.properties.map(property => {
                        const propertyType = property_types.find(propertyType =>
                            propertyType.id === property.property_type_id
                        )
                        return (
                            <div key={property.id} className={"my-5"}>
                                <span>{propertyType.name}</span>
                                <span> : </span>
                                {
                                    propertyType.value_type === 'text' ?
                                        <span>{property.value}</span>  :
                                        <img src={`/${property.value}`} alt={propertyType.name}/>
                                }
                            </div>
                        )
                    })
                }
            </div>
        </Authenticated>
    );
}

export default ShowFound;
