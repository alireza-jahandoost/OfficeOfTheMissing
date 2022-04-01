<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLostRequest;
use App\Http\Requests\UpdateLostRequest;
use App\Models\License;
use App\Models\Lost;
use Exception;
use Faker\Calculator\Inn;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia;

class LostController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     * @throws AuthorizationException
     */
    public function index(License $license): \Inertia\Response
    {
        $this->authorize('viewAny', Lost::class);
        $propertyTypes = $license->propertyTypes()->exceptShowToFinder()
            ->get()->map(function($propertyType){
                return collect($propertyType)->forget(['show_to_finder','show_to_loser']);
            });

        $losts = $license->losts()->where('user_id', auth()->id())->get()->reduce(function($carry, $lost){
            $carry[] = [
                'id' => $lost->id,
                'properties' => $lost->properties
            ];
            return $carry;
        }, []);

        return Inertia::render('Losts/Index', [
            'license' => $license,
            'property_types' => $propertyTypes,
            'losts' => $losts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(License $license): \Inertia\Response
    {
        $this->authorize('create', Lost::class);
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
     * @param StoreLostRequest $request
     * @return RedirectResponse
     */
    public function store(StoreLostRequest $request, License $license)
    {
        $this->authorize('create', Lost::class);
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

        return redirect()->route('licenses.losts.show', [$license, $lost]);
    }

    /**
     * Display the specified resource.
     *
     * @param Lost $lost
     * @return \Inertia\Response
     * @throws AuthorizationException
     */
    public function show(License $license, Lost $lost): \Inertia\Response
    {
        $this->authorize('view', [$lost, $license]);
        $propertyTypes = $license->propertyTypes()->exceptShowToFinder()
            ->get()->map(function($propertyType){
                return collect($propertyType)->forget(['show_to_loser', 'show_to_finder']);
            });
        return Inertia::render('Losts/Show', [
            'license' => $license,
            'property_types' => $propertyTypes,
            'lost' => [
                'properties' => $lost->properties,
                'id' => $lost->id,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Lost $lost
     * @return \Inertia\Response
     * @throws Exception
     */
    public function edit(License $license, Lost $lost): \Inertia\Response
    {
        $this->authorize('update', [$lost, $license]);
        $propertyTypes = $license->propertyTypes()->exceptShowToFinder()
            ->get()->map(function($propertyType){
                return collect($propertyType)->forget(['show_to_loser', 'show_to_finder']);
            });

        return Inertia::render('Losts/Edit',[
            'license' => $license,
            'property_types' => $propertyTypes,
            'lost' => [
                'properties' => $lost->properties,
                'id' => $lost->id,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLostRequest $request
     * @param Lost $lost
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function update(UpdateLostRequest $request, License $license, Lost $lost): RedirectResponse
    {
        $this->authorize('update', [$lost, $license]);
        $data = $request->validated();

        foreach ($license->propertyTypes()->exceptShowToFinder()->get() as $propertyType){
            if(isset($data["property_type$propertyType->id"])){
                $property = $lost->properties()
                    ->where('property_type_id', $propertyType->id)->first();
                switch ($propertyType->value_type){
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
                        throw new Exception('Invalid property type in LostController/update');
                }
            }
        }

        return redirect()->route('licenses.losts.edit', [$license, $lost]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Lost $lost
     * @return RedirectResponse
     */
    public function destroy(License $license, Lost $lost): RedirectResponse
    {
        $this->authorize('delete', [$lost, $license]);
        foreach($lost->properties()->with('propertyType')->get() as $property){
            if($property->propertyType->value_type === 'image'){
                Storage::delete($property->value);
            }
        }

        $lost->properties()->delete();
        $lost->delete();

        return redirect()->route('licenses.losts.index', $license);
    }

    /**
     * Display list of licenses with link to index specific lost models route
     */
    public function indexLicenses(){
        $this->authorize('indexLicenses', Lost::class);
        return Inertia::render('Losts/IndexLicenses', [
            'licenses' => License::all()
        ]);
    }
}
