import Authenticated from '@/Layouts/Authenticated';
import {Head, Link} from '@inertiajs/inertia-react';
import React from 'react';

const IndexLicenses = ({auth, errors: authenticatedErrors, licenses}) => {
    return (
        <Authenticated
            auth={auth}
            errors={authenticatedErrors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">مدارک گم شده</h2>}
        >
            <Head title="Licenses" />

            <div className="m-10">
                {licenses.map(license => (
                    <div key={license.id}>
                        <Link className="text-blue-800" href={route('licenses.losts.index', [license])}>
                            {license.name}
                        </Link>
                    </div>
                ))}
            </div>
        </Authenticated>
    );
}

export default IndexLicenses;
