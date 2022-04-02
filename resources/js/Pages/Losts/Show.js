import React from 'react';
import Authenticated from '@/Layouts/Authenticated';
import {Head, Link} from '@inertiajs/inertia-react';
import { Inertia } from '@inertiajs/inertia'
import Button from '../../Components/Button';

const ShowLost = ({auth, errors: authenticatedErrors, license, property_types, lost, founds}) => {
    const handleLicenseDeletation = () => {
        if(confirm('مدرک به طور دایم حذف خواهد شد. از انجام این کار اطمینان دارید؟')){
            Inertia.delete(route('licenses.losts.destroy', [license.id, lost.id]));
        }
    }

    const confirmFoundLicense = (foundId) => {
        if(confirm('آیا اطمینان دارید که این مدرک متعلق به شماست؟ ')){
            Inertia.post(route('licenses.losts.match', [lost.id, foundId]));
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
            <Head title="Show Lost" />

            <div className="m-10">
                <h3 className={"text-xl"}>
                    <span>نوع مدرک: </span>
                    <span>{license.name}</span>
                </h3>

                <Button handleClick={handleLicenseDeletation} className={"my-4 ml-4"}>حذف مدرک</Button>

                <Link href={route('licenses.losts.edit', [license.id, lost.id])}>
                    <Button>ویرایش مدرک</Button>
                </Link>

                {
                    lost.properties.map(property => {
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

                <div className={"mt-14"}>
                    <h4 className={"text-2xl"}>مدارک مطابقت داشته پیدا شده</h4>
                    <div>
                        {
                            founds.map(found => (
                                <div key={found.id} className={"border-2 p-4 my-8 border-gray-800"}>
                                    {
                                        found.properties.map(property => {
                                            const propertyType = property_types.find(
                                                propertyType => propertyType.id === property.property_type_id
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
                                    <Button
                                        type={"button"}
                                        handleClick={() => confirmFoundLicense(found.id)}
                                    >
                                        تایید مدرک
                                    </Button>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}

export default ShowLost;
