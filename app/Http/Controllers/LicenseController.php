<?php

namespace App\Http\Controllers;

use App\Actions\ShowToLoserAndFinderAreValid;
use App\Http\Requests\StoreLicenseRequest;
use App\Http\Requests\UpdateLicenseRequest;
use App\Models\License;
use App\Models\PropertyType;
use Inertia\Inertia;

class LicenseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(License::class, 'license');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Licenses/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLicenseRequest  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ShowToLoserAndFinderAreValid $action, StoreLicenseRequest $request): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $data = $request->validated();

        $error = $action->check($data['property_types']);

        if($error){
            return redirect()->back()->withErrors([
                'property_types' => $error,
            ])->withInput();
        }

        $license = License::create([
            'name' => $data['name']
        ]);

        foreach($data['property_types'] as $propertyType){
            $license->propertyTypes()->create($propertyType);
        }

        return redirect()->route('licenses.show', $license);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\License  $license
     * @return \Illuminate\Http\Response
     */
    public function show(License $license)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\License  $license
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(License $license)
    {
        foreach ($license->founds as $model){
            $model->properties()->delete();
        }

        foreach ($license->losts as $model){
            $model->properties()->delete();
        }
        $license->delete();
        return redirect()->route('licenses.index');
    }
}
