<?php

namespace Tests\Feature\Endpoints\Found;

use App\Models\Found;
use App\Models\License;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    protected const FOUND_EDIT = 'licenses.founds.edit';

    public function test_user_can_see_edit_page_for_their_founds()
    {
        $user = User::factory()->create();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $found = Found::factory()->for($license)->for($user)->create();

        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->get(route(self::FOUND_EDIT, [$license, $found]));

        $response->assertOk();

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Founds/Edit')
            ->where('license.name', $license->name)
            ->has('property_types',2,fn(AssertableInertia $page) => $page
                ->where('name', $firstPropertyType->name)
                ->where('value_type', $firstPropertyType->value_type)
                ->where('hint', $firstPropertyType->hint)
                ->where('license_id', $firstPropertyType->license->id)
                ->missing('show_to_loser')
                ->missing('show_to_finder')
                ->etc()
            )
            ->has('properties',2)
            ->where('properties.0.value', $value)
            ->where('properties.1.value', $path)
        );
    }

    public function test_user_can_not_see_other_users_found_edit_page()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $found = Found::factory()->for($license)->for($user)->create();

        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($anotherUser)->get(route(self::FOUND_EDIT, [$license, $found]));

        $response->assertForbidden();
    }

    public function test_guest_user_can_not_see_found_edit_page()
    {
        $user = User::factory()->create();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $found = Found::factory()->for($license)->for($user)->create();

        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->get(route(self::FOUND_EDIT, [$license, $found]));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_not_edit_other_users_found_models()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $found = Found::factory()->for($license)->for($user)->create();

        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($admin)->get(route(self::FOUND_EDIT, [$license, $found]));

        $response->assertForbidden();
    }

    public function test_found_model_must_belongs_to_the_license()
    {
        $user = User::factory()->create();

        $license = License::factory()->create();
        $anotherLicense = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1, 3))->create([
            'show_to_loser' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $found = Found::factory()->for($license)->for($user)->create();

        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->get(route(self::FOUND_EDIT, [$anotherLicense, $found]));

        $response->assertForbidden();
    }
}
