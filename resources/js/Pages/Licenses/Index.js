import React from 'react';
import { Link } from '@inertiajs/inertia-react'

const IndexLicenses = ({licenses}) => {
    return (
        <div className="m-10">
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
    )
}

export default IndexLicenses;
