import React from 'react';
import Authenticated from '@/Layouts/Authenticated';
import Button from '../../Components/Button';
import {Head} from '@inertiajs/inertia-react';
import { Link } from '@inertiajs/inertia-react';

const IndexLicenses = ({licenses, auth, errors: authenticatedErrors}) => {
    return (
        <Authenticated
            auth={auth}
            errors={authenticatedErrors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">مدارک ایجاد شده</h2>}
        >
            <Head title="Index Licenses" />
            <div className="m-10">
                <Link href={route('licenses.create')}>
                    <Button type="button">ایجاد مدرک جدید</Button>
                </Link>

                <h3 className="text-xl mt-14">مدارک موجود: </h3>

                {licenses.map(license => (
                    <div key={license.id} className="my-5">
                        <div>
                            <span>نام مدرک: </span>
                            <span>{license.name}</span>
                        </div>
                        <Link
                            href={route('licenses.show', [license.id])}
                            className="text-blue-800"
                        >
                            جزییات بیشتر
                        </Link>
                    </div>
                ))}
            </div>
        </Authenticated>
    )
}

export default IndexLicenses;
