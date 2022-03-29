<?php

namespace Tests\Feature\Endpoints\Lost;

use App\Models\License;
use App\Models\Lost;
use App\Models\Property;
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

    protected const LOST_EDIT = 'losts.edit';

    public function test_user_can_see_edit_page_for_their_losts()
    {
        $user = User::factory()->create();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $lost = Lost::factory()->for($license)->for($user)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->get(route(self::LOST_EDIT, [$license, $lost]));

        $response->assertOk();

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Losts/Edit')
            ->where('license.name', $license->name)
            ->has('property_types',2,fn(AssertableInertia $page) => $page
                ->where('name', $firstPropertyType->name)
                ->where('value_type', $firstPropertyType->value_type)
                ->where('hint', $firstPropertyType->hint)
                ->where('license_id', $firstPropertyType->license->id)
                ->missing('show_to_finder')
                ->missing('show_to_loser')
                ->etc()
            )
            ->has('properties',2)
            ->where('properties.0.value', $value)
            ->where('properties.1.value', $path)
        );
    }

    public function test_user_can_not_see_other_users_lost_edit_page()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $lost = Lost::factory()->for($license)->for($user)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($anotherUser)->get(route(self::LOST_EDIT, [$license, $lost]));

        $response->assertForbidden();
    }

    public function test_guest_user_can_not_see_lost_edit_page()
    {
        $user = User::factory()->create();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $lost = Lost::factory()->for($license)->for($user)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->get(route(self::LOST_EDIT, [$license, $lost]));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_not_edit_other_users_lost_models()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $lost = Lost::factory()->for($license)->for($user)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($admin)->get(route(self::LOST_EDIT, [$license, $lost]));

        $response->assertForbidden();
    }

    public function test_lost_model_must_belongs_to_the_license()
    {
        $user = User::factory()->create();

        $license = License::factory()->create();
        $anotherLicense = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);
        PropertyType::factory()->for($license)->count(rand(1, 3))->create([
            'show_to_finder' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        Storage::fake();

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);

        $value = Str::random(30);

        $lost = Lost::factory()->for($license)->for($user)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value
        ]);

        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->get(route(self::LOST_EDIT, [$anotherLicense, $lost]));

        $response->assertForbidden();
    }
}
