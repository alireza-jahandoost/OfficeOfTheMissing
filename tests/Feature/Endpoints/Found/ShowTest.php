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

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected const FOUND_SHOW = 'licenses.founds.show';

    public function test_user_can_see_show_found_models_page()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();

        $found = Found::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $value = Str::random();
        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->get(route(self::FOUND_SHOW, [$license, $found]));

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Founds/Show')
            ->where('license.name', $license->name)
            ->where('found.id', $found->id)
            ->has('property_types', 2, fn(AssertableInertia $page) => $page
                ->where('name', $firstPropertyType->name)
                ->where('value_type', $firstPropertyType->value_type)
                ->where('hint', $firstPropertyType->hint)
                ->missing('show_to_loser')
                ->missing('show_to_finder')
                ->etc()
            )
            ->has('found.properties', 2)
            ->where('found.properties.0.value', $firstProperty->value)
            ->where('found.properties.1.value', $secondProperty->value)
        );
    }

    public function test_another_user_can_not_see_show_found_models_page_of_user()
    {
        Storage::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $found = Found::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $value = Str::random();
        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($anotherUser)->get(route(self::FOUND_SHOW, [$license, $found]));

        $response->assertForbidden();
    }

    public function test_to_show_found_model_found_and_license_models_must_match()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();
        $anotherLicense = License::factory()->create();

        $found = Found::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $value = Str::random();
        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->get(route(self::FOUND_SHOW, [$anotherLicense, $found]));

        $response->assertForbidden();
    }

    public function test_guest_user_can_not_see_show_found_models_page()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();

        $found = Found::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $value = Str::random();
        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->get(route(self::FOUND_SHOW, [$license, $found]));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_user_can_not_see_show_found_models_page()
    {
        Storage::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $license = License::factory()->create();

        $found = Found::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_loser' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $value = Str::random();
        $firstProperty = $found->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $found->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($admin)->get(route(self::FOUND_SHOW, [$license, $found]));

        $response->assertForbidden();
    }
}
