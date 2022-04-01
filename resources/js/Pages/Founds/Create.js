import Authenticated from '@/Layouts/Authenticated';
import Button from '../../Components/Button';
import PersianError from '../../Components/PersianError';
import Input from '../../Components/Input';
import {useForm, Head, Link} from '@inertiajs/inertia-react';
import React from 'react';

const CreateFounds = ({auth, errors: authenticatedErrors, license, property_types}) => {
    const initialValues = property_types.reduce((carry, propertyType) => {
        carry[`property_type${propertyType.id}`] = {
            value: ""
        }
        return carry;
    }, {});
    const {data, setData, post, processing, errors} = useForm(initialValues);
    console.log(data);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('licenses.founds.store', [license.id]));
    }

    return (
        <Authenticated
            auth={auth}
            errors={authenticatedErrors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">
                <span>اضافه کردن</span>
                <span> {license.name} </span>
                <span>پیدا شده</span>
            </h2>}
        >
            <Head title="Create Found" />

            <div className="m-10">
                <form onSubmit={handleSubmit}>
                    {property_types.map(propertyType => {
                        const propertyName = `property_type${propertyType.id}`;
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
                                    </div>
                                )
                            default:
                                throw new Error('Invalid property type in Founds/Create.js');
                        }
                    })}
                    <Button processing={processing} className={"my-4"}>ایجاد مدرک</Button>
                </form>
            </div>
        </Authenticated>
    );
}

export default CreateFounds;
