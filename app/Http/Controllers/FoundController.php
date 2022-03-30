<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFoundRequest;
use App\Http\Requests\UpdateFoundRequest;
use App\Models\Found;
use App\Models\License;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FoundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     * @throws AuthorizationException
     */
    public function create(License $license): \Inertia\Response
    {
        $this->authorize('create', Found::class);
        $propertyTypes = $license->propertyTypes()->exceptShowToLoser()
            ->get()->map(function($propertyType){
                return collect($propertyType)->forget(['show_to_loser', 'show_to_finder']);
            });

        return Inertia::render('Founds/Create', [
            'license' => $license,
            'property_types' => $propertyTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFoundRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws AuthorizationException
     */
    public function store(StoreFoundRequest $request, License $license)
    {
        $this->authorize('create', Found::class);
        $data = $request->validated();

        $found = new Found;
        $found->user_id = auth()->id();
        $found->license_id = $license->id;
        $found->save();

        foreach($license->propertyTypes()->exceptShowToLoser()->get() as $propertyType){
            switch ($propertyType->value_type){
                case 'text':
                    $found->properties()->create([
                        'property_type_id' => $propertyType->id,
                        'value' => $data["property_type$propertyType->id"]['value'],
                    ]);
                    break;
                case 'image':
                    $path = $request->file("property_type$propertyType->id.value")
                        ->store('licenses');
                    $found->properties()->create([
                        'property_type_id' => $propertyType->id,
                        'value' => $path,
                    ]);
            }
        }

        return redirect()->route('licenses.founds.show', [$license, $found]);
    }

    /**
     * Display the specified resource.
     *
     * @param Found $found
     * @return Response
     */
    public function show(Found $found)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Found $found
     * @return \Inertia\Response
     * @throws AuthorizationException
     */
    public function edit(License $license, Found $found)
    {
        $this->authorize('update', [$found, $license]);
        $propertyTypes = $license->propertyTypes()->exceptShowToLoser()
            ->get()->map(function($propertyType){
                return collect($propertyType)->forget(['show_to_loser', 'show_to_finder']);
            });

        return Inertia::render('Founds/Edit',[
            'license' => $license,
            'property_types' => $propertyTypes,
            'properties' => $found->properties,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateFoundRequest $request
     * @param Found $found
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function update(UpdateFoundRequest $request, License $license, Found $found)
    {
        $this->authorize('update', [$found, $license]);
        $data = $request->validated();

        foreach ($license->propertyTypes()->exceptShowToLoser()->get() as $propertyType) {
            if (isset($data["property_type$propertyType->id"])) {
                $property = $found->properties()
                    ->where('property_type_id', $propertyType->id)->first();
                switch ($propertyType->value_type) {
                    case 'text':
                        $property->value = $data["property_type$property->property_type_id"]['value'];
                        $property->save();
                        break;
                    case 'image':
                        Storage::delete($property->value);
                        $property->value = $request->file("property_type$property->property_type_id.value")
                            ->store('licenses');
                        $property->save();
                        break;
                    default:
                        throw new Exception('Invalid property type in FoundController/update');
                }
            }
        }

        return redirect()->route('licenses.founds.edit', [$license, $found]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Found $found
     * @return Response
     */
    public function destroy(Found $found)
    {
        //
    }
}
