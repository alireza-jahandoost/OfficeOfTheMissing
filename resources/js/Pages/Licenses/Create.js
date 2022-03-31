import React from 'react';
import {v4 as uuidv4} from 'uuid';
import Authenticated from '@/Layouts/Authenticated';
import {useForm, Head} from '@inertiajs/inertia-react';
import Input from '../../Components/Input';
import Button from '../../Components/Button';
import Checkbox from '../../Components/Checkbox';

const CreateLicense = ({auth, errors: authenticatedErrors}) => {
    const {data, setData, post, processing, errors} = useForm({
        name: '',
        property_types: [],
    });
    console.log(data, errors);

    const addPropertyType = () => {
        setData(
            'property_types',
            [
                ...data.property_types,
                {
                    id: uuidv4(),
                    name: '',
                    value_type: 'text',
                    hint: '',
                    show_to_loser: false,
                    show_to_finder: false,
                }
            ]
        );
    }

    const changePropertyTypeById = (id, obj) => {
        setData(
            'property_types',
            data.property_types.map((propertyType) => {
                return propertyType.id === id ? {...propertyType, ...obj} : propertyType;
            })
        );
    }

    const handleSubmit = (e) => {
        console.log('here');
        e.preventDefault();
        console.log('want to submit');
        post(route('licenses.store'));
    }

    const deletePropertyTypeById = (id) => {
        setData(
            'property_types',
            data.property_types.filter((propertyType) => {
                return propertyType.id !== id;
            })
        );
    }

    return (
        <Authenticated
            auth={auth}
            errors={authenticatedErrors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">ایجاد مدرک جدید</h2>}
        >
            <Head title="Create License" />
            <div className="m-10">
                <Button type="button" handleClick={addPropertyType}>ایجاد ویژگی جدید</Button>

                <form className="my-4" onSubmit={handleSubmit}>
                    <h1>ایجاد مدرک جدید</h1>
                    <div>
                        <label>نام مدرک</label>
                        {errors.name &&
                            <div className="text-red-700">
                                <span>خطا: </span>
                                <span>{errors.name}</span>
                            </div>
                        }
                        <Input
                            type="text"
                            name="license_name"
                            required="required"
                            value={data.name}
                            handleChange={e => setData('name', e.target.value)}
                        />
                    </div>
                    {errors.property_types &&
                        <div className="text-red-700">
                            <span>خطا: </span>
                            <span>{errors.property_types}</span>
                        </div>
                    }
                    {
                        data.property_types.map((propertyType) => (
                            <div className="mt-14" key={propertyType.id}>
                                <div>
                                    <label>نام ویژگی</label>
                                    <Input
                                        type="text"
                                        name={`property_type_${propertyType.id}_name`}
                                        value={propertyType.name}
                                        required="required"
                                        handleChange={(e) =>
                                            changePropertyTypeById(propertyType.id, {name: e.target.value})
                                        }
                                    />
                                </div>
                                <div>
                                    <label>نوع ویژگی</label>
                                    <select
                                        name={`property_type_${propertyType.id}_value_type`}
                                        value={propertyType.value_type}
                                        onChange={(e) => {
                                            changePropertyTypeById(propertyType.id, {value_type: e.target.value});
                                        }}
                                    >
                                        <option value="text">متن</option>
                                        <option value="image">تصویر</option>
                                    </select>
                                </div>
                                <div>
                                    <label>توضیح ویژگی برای کاربران</label>
                                    <Input
                                        type="text"
                                        name={`property_type_${propertyType.id}_hint`}
                                        value={propertyType.hint}
                                        handleChange={(e) =>
                                            changePropertyTypeById(propertyType.id, {hint: e.target.value})
                                        }
                                    />
                                </div>
                                <div>
                                    <label>نشان دادن به پیدا کننده</label>
                                    <Checkbox
                                        name={`property_type_${propertyType.id}_show_to_finder`}
                                        value={propertyType.show_to_finder}
                                        handleChange={(e) => {
                                            changePropertyTypeById(propertyType.id, {show_to_finder: e.target.checked});
                                        }}
                                    />
                                </div>
                                <div>
                                    <label>نشان دادن به گم کننده</label>
                                    <Checkbox
                                        name={`property_type_${propertyType.id}_show_to_loser`}
                                        value={propertyType.show_to_loser}
                                        handleChange={(e) => {
                                            changePropertyTypeById(propertyType.id, {show_to_loser: e.target.checked});
                                        }}
                                    />
                                </div>
                                <Button
                                    type="button"
                                    handleClick={() => deletePropertyTypeById(propertyType.id)}
                                >
                                    حذف ویژگی
                                </Button>
                            </div>
                        ))
                    }
                    <Button className="my-5" type="submit">ایجاد</Button>
                </form>
            </div>
        </Authenticated>
    )
}

export default CreateLicense;
