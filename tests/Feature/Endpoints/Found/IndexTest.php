<?php

namespace Tests\Feature\Endpoints\Found;

use App\Models\Found;
use App\Models\License;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected const FOUND_INDEX = 'licenses.founds.index';

    public function test_every_user_can_index_their_own_found_models()
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $license = License::factory()->create();
        $propertyTypes = PropertyType::factory()->for($license)->count(4)->create([
            'show_to_finder' => true,
        ]);

        $firstFounds = Found::factory()->for($firstUser)->for($license)->count(3)->create();

        foreach ($firstFounds as $firstLost){
            foreach($propertyTypes as $propertyType){
                $firstLost->properties()->create([
                    'property_type_id' => $propertyType->id,
                    'value' => Str::random(30),
                ]);
            }
        }

        $secondFounds = Found::factory()->for($secondUser)->for($license)->count(5)->create();

        foreach ($secondFounds as $secondLost){
            foreach($propertyTypes as $propertyType){
                $secondLost->properties()->create([
                    'property_type_id' => $propertyType->id,
                    'value' => Str::random(30),
                ]);
            }
        }

        $firstResponse = $this->actingAs($firstUser)->get(route(self::FOUND_INDEX, $license));

        $firstResponse->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Founds/Index')
            ->where('license.name', $license->name)
            ->has('founds', 3, fn(AssertableInertia $page) => $page
                ->where('id', $firstFounds[0]->id)
                ->has('properties', 4, fn(AssertableInertia $page) => $page
                    ->where('value', $firstFounds[0]->properties[0]->value)
                    ->etc()
                )
            )
            ->has('property_types', 4, fn(AssertableInertia $page) => $page
                ->where('name', $propertyTypes[0]->name)
                ->where('value_type', $propertyTypes[0]->value_type)
                ->where('hint', $propertyTypes[0]->hint)
                ->missingAll(['show_to_loser', 'show_to_finder'])
                ->etc()
            )
        );

        $secondResponse = $this->actingAs($secondUser)->get(route(self::FOUND_INDEX, $license));

        $secondResponse->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Founds/Index')
            ->where('license.name', $license->name)
            ->has('founds', 5, fn(AssertableInertia $page) => $page
                ->where('id', $secondFounds[0]->id)
                ->has('properties', 4, fn(AssertableInertia $page) => $page
                    ->where('value', $secondFounds[0]->properties[0]->value)
                    ->etc()
                )
            )
            ->has('property_types', 4, fn(AssertableInertia $page) => $page
                ->where('name', $propertyTypes[0]->name)
                ->where('value_type', $propertyTypes[0]->value_type)
                ->where('hint', $propertyTypes[0]->hint)
                ->missingAll(['show_to_loser', 'show_to_finder'])
                ->etc()
            )
        );
    }

    public function test_just_founds_related_to_specific_license_must_be_listed()
    {
        $user = User::factory()->create();

        $firstLicense = License::factory()->create();
        $secondLicense = License::factory()->create();
        $firstPropertyTypes = PropertyType::factory()->for($firstLicense)->count(4)->create([
            'show_to_finder' => true,
        ]);
        $secondPropertyTypes = PropertyType::factory()->for($secondLicense)->count(3)->create([
            'show_to_finder' => true,
        ]);

        $firstFounds = Found::factory()->for($user)->for($firstLicense)->count(3)->create();

        foreach ($firstFounds as $firstLost){
            foreach($firstPropertyTypes as $propertyType){
                $firstLost->properties()->create([
                    'property_type_id' => $propertyType->id,
                    'value' => Str::random(30),
                ]);
            }
        }

        $secondFounds = Found::factory()->for($user)->for($secondLicense)->count(5)->create();

        foreach ($secondFounds as $secondLost){
            foreach($secondPropertyTypes as $propertyType){
                $secondLost->properties()->create([
                    'property_type_id' => $propertyType->id,
                    'value' => Str::random(30),
                ]);
            }
        }

        $firstResponse = $this->actingAs($user)->get(route(self::FOUND_INDEX, $firstLicense));

        $firstResponse->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Founds/Index')
            ->where('license.name', $firstLicense->name)
            ->has('founds', 3, fn(AssertableInertia $page) => $page
                ->where('id', $firstFounds[0]->id)
                ->has('properties', 4, fn(AssertableInertia $page) => $page
                    ->where('value', $firstFounds[0]->properties[0]->value)
                    ->etc()
                )
            )
            ->has('property_types', 4, fn(AssertableInertia $page) => $page
                ->where('name', $firstPropertyTypes[0]->name)
                ->where('value_type', $firstPropertyTypes[0]->value_type)
                ->where('hint', $firstPropertyTypes[0]->hint)
                ->missingAll(['show_to_loser', 'show_to_finder'])
                ->etc()
            )
        );

        $secondResponse = $this->actingAs($user)->get(route(self::FOUND_INDEX, $secondLicense));

        $secondResponse->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Founds/Index')
            ->where('license.name', $secondLicense->name)
            ->has('founds', 5, fn(AssertableInertia $page) => $page
                ->where('id', $secondFounds[0]->id)
                ->has('properties', 3, fn(AssertableInertia $page) => $page
                    ->where('value', $secondFounds[0]->properties[0]->value)
                    ->etc()
                )
            )
            ->has('property_types', 3, fn(AssertableInertia $page) => $page
                ->where('name', $secondPropertyTypes[0]->name)
                ->where('value_type', $secondPropertyTypes[0]->value_type)
                ->where('hint', $secondPropertyTypes[0]->hint)
                ->missingAll(['show_to_loser', 'show_to_finder'])
                ->etc()
            )
        );
    }

    public function test_guest_user_can_not_index_found_models()
    {
        $license = License::factory()->create();

        $response = $this->get(route(self::FOUND_INDEX, $license));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_user_can_not_index_founds_models()
    {
        $license = License::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route(self::FOUND_INDEX, $license));

        $response->assertForbidden();
    }
}
