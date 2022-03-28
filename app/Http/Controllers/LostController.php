<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLostRequest;
use App\Http\Requests\UpdateLostRequest;
use App\Models\License;
use App\Models\Lost;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia;

class LostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Lost::class, 'lost');
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
    public function create(License $license): \Inertia\Response
    {
        $propertyTypes = $license->propertyTypes()->exceptShowToFinder()
            ->get()->map(function($propertyType){
                return collect($propertyType)->forget(['show_to_loser', 'show_to_finder']);
        });

        return Inertia::render('Losts/Create', [
            'license' => $license,
            'property_types' => $propertyTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLostRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreLostRequest $request, License $license)
    {
        $data = $request->validated();

        $lost = new Lost;
        $lost->user_id = auth()->id();
        $lost->license_id = $license->id;
        $lost->save();

        foreach($license->propertyTypes()->exceptShowToFinder()->get() as $propertyType){
            switch ($propertyType->value_type){
                case 'text':
                    $property = $lost->properties()->create([
                        'property_type_id' => $propertyType->id,
                        'value' => $data["property_type$propertyType->id"]['value'],
                    ]);
                    break;
                case 'image':
                    $path = $request->file("property_type$propertyType->id.value")->store('licenses');
                    $property = $lost->properties()->create([
                        'property_type_id' => $propertyType->id,
                        'value' => $path,
                    ]);
            }
        }

        return redirect()->route('losts.show', [$license, $lost]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lost  $lost
     * @return \Illuminate\Http\Response
     */
    public function show(Lost $lost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lost  $lost
     * @return \Illuminate\Http\Response
     */
    public function edit(Lost $lost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLostRequest  $request
     * @param  \App\Models\Lost  $lost
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLostRequest $request, Lost $lost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lost  $lost
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lost $lost)
    {
        //
    }
}
