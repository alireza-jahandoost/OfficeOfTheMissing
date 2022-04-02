import React, {useEffect, useState} from 'react';
import Authenticated from '@/Layouts/Authenticated';
import Button from '../../Components/Button';
import PersianError from '../../Components/PersianError';
import Input from '../../Components/Input';
import {useForm, Head, Link} from '@inertiajs/inertia-react';
import {Inertia} from "@inertiajs/inertia";

const EditLost = ({auth, errors, license, property_types, lost}) => {
    const [processing, setProcessing] = useState(false);
    const initialValues = property_types.reduce((carry, propertyType) => {
        const property = lost.properties.find(property =>
            property.property_type_id === propertyType.id
        )
        carry[`property_type${propertyType.id}`] = {
            value: property.value
        }
        return carry;
    }, {});
    const {data, setData} = useForm(initialValues);

    useEffect(() => {
        lost.properties.map(property => {
            const propertyType = property_types.find(
                propertyType => propertyType.id === property.property_type_id
            );
            const propertyName = `property_type${propertyType.id}`;
            if(data[propertyName].value !== property.value){
                setData(
                    propertyName,
                    {
                        value: property.value
                    }
                )
            }
        })
    }, [lost.properties]);

    const handleSubmit = (e) => {
        e.preventDefault();

        const newState = lost.properties.reduce((carry, property) => {
            const propertyType = property_types.find(
                propertyType => propertyType.id === property.property_type_id
            )
            const propertyName = `property_type${propertyType.id}`;
            if (data[propertyName].value !== property.value){
                carry[propertyName] = data[propertyName];
            }
            return carry;
        },{});

        Inertia.post(route('licenses.losts.update', [license.id, lost.id]), {
            ...newState,
            _method: 'PUT'
        }, {
            onStart(){
                setProcessing(true);
            },
            onFinish(){
                setProcessing(false);
            }
        })
    }

    return (
        <Authenticated
            auth={auth}
            errors={errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">
                <span>اصلاح مدرک</span>
            </h2>}
        >
            <Head title="Edit Lost" />

            <div className="m-10">
                <form onSubmit={handleSubmit}>
                    {property_types.map(propertyType => {
                        const propertyName = `property_type${propertyType.id}`;
                        const property = lost.properties.find(
                            property => property.property_type_id === propertyType.id
                        )
                        switch (propertyType.value_type){
                            case 'text':
                                return (
                                    <div key={propertyType.id}>
                                        <label>{propertyType.name}</label>
                                        {errors[propertyName] &&
                                            <PersianError
                                                message={errors[propertyName]}
                                            />
                                        }
                                        {errors[`${propertyName}.value`] &&
                                            <PersianError
                                                message={errors[`${propertyName}.value`]}
                                            />
                                        }
                                        <Input
                                            name={propertyName}
                                            value={data[propertyName].value}
                                            required="required"
                                            handleChange={e => setData(
                                                propertyName,
                                                {
                                                    value: e.target.value
                                                }
                                            )}
                                        />
                                    </div>
                                )
                            case 'image':
                                return (
                                    <div key={propertyType.id}>
                                        <label>{propertyType.name}</label>
                                        {errors[propertyName] &&
                                            <PersianError
                                                message={errors[propertyName]}
                                            />
                                        }
                                        {errors[`${propertyName}.value`] &&
                                            <PersianError
                                                message={errors[`${propertyName}.value`]}
                                            />
                                        }
                                        {
                                            data[propertyName].value === property.value?
                                                <>
                                                    <img
                                                        src={`/${data[propertyName].value}`}
                                                        alt={propertyType.name}
                                                    />
                                                    <Button
                                                        className={"my-4"}
                                                        processing={processing}
                                                        type={"button"}
                                                        handleClick={() => setData(
                                                            propertyName,
                                                            {
                                                                value: ''
                                                            })
                                                        }
                                                    >
                                                        تغییر عکس
                                                    </Button>
                                                </>
                                                :
                                                <Input
                                                    type="file"
                                                    name={propertyName}
                                                    required="required"
                                                    handleChange={(e) => setData(
                                                        propertyName,
                                                        {
                                                            value: e.target.files[0]
                                                        }
                                                    )}
                                                />

                                        }
                                    </div>
                                )
                            default:
                                throw new Error('Invalid property type in Losts/Create.js');
                        }
                    })}
                    <Button processing={processing} className={"my-4"}>به روز رسانی مدرک</Button>
                </form>
            </div>
        </Authenticated>
    );
}

export default EditLost;
