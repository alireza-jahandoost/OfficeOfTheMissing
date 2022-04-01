import Authenticated from '@/Layouts/Authenticated';
import Button from '../../Components/Button';
import {Head, Link} from '@inertiajs/inertia-react';
import React from 'react';

const IndexLosts = ({auth, errors: authenticatedErrors, license, property_types, losts}) => {
    return (
        <Authenticated
            auth={auth}
            errors={authenticatedErrors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">
                <span>{license.name}</span>
                <span> های گم شده</span>
            </h2>}
        >
            <Head title="Losts" />

            <div className="m-10">
                <Link href={route('licenses.losts.create', [license.id])}>
                    <Button type="button">اضافه کردن مدرک گم شده</Button>
                </Link>
                {losts.map((lost) => (
                    <Link key={lost.id} href={route('licenses.losts.show', [license.id, lost.id])}>
                        <div className={"bg-white shadow my-4 p-3 rounded hover:bg-gray-200"}>
                            {
                                lost.properties.map(property => {
                                    const propertyType = property_types.find(propertyType =>
                                        propertyType.id === property.property_type_id
                                    );
                                    if(propertyType.value_type !== "text"){
                                        return null;
                                    }
                                    return (
                                        <div key={property.id}>
                                            <span>{propertyType.name}</span>
                                            <span> : </span>
                                            <span>{property.value}</span>
                                        </div>
                                    )
                                })
                            }
                        </div>
                    </Link>
                ))}
            </div>
        </Authenticated>
    );
}

export default IndexLosts;
