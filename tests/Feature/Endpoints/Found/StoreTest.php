<?php

namespace Tests\Feature\Endpoints\Found;

use App\Models\Found;
use App\Models\License;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected const FOUND_STORE = 'licenses.founds.store';
    protected const FOUND_SHOW = 'licenses.founds.show';

    public function test_user_can_store_found_model_with_type_text()
    {
        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $value = Str::random(20);

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
                'value' => $value,
            ]
        ]);

        $response->assertRedirect(route(self::FOUND_SHOW, [$license, 1]));

        $this->assertDatabaseCount(Found::class, 1);
        $this->assertDatabaseCount(Property::class, 1);
        $this->assertDatabaseHas(Property::class, [
            'value' => $value
        ]);
    }

    public function test_if_the_type_is_text_value_is_required()
    {
        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
    }

    public function test_if_the_type_is_text_value_type_must_be_string()
    {
        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
                'value' => [
                    'type' => 'string',
                ]
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
    }

    public function test_if_type_is_text_the_value_must_not_be_longer_than_100_characters()
    {
        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
                'value' => Str::random(101),
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
    }

    public function test_user_can_store_a_found_model_with_property_type_image()
    {
        Storage::fake();

        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
                'value' => $file,
            ]
        ]);

        $response->assertRedirect(route(self::FOUND_SHOW, [$license, 1]));

        $this->assertDatabaseCount(Found::class, 1);
        $this->assertDatabaseCount(Property::class, 1);
        $this->assertDatabaseHas(Property::class, [
            'value' => "licenses/".$file->hashName()
        ]);
    }

    public function test_if_the_type_is_image_the_image_must_exists_and_its_required()
    {
        Storage::fake();

        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
        $this->assertDatabaseMissing(Property::class, [
            'value' => "licenses/".$file->hashName()
        ]);
    }

    public function test_if_the_value_type_is_image_the_input_must_be_an_image()
    {
        Storage::fake();

        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
                'value' => $file
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
        $this->assertDatabaseMissing(Property::class, [
            'value' => "licenses/".$file->hashName()
        ]);
    }

    public function test_if_the_value_is_image_the_size_of_image_must_not_be_bigger_than_2m()
    {
        Storage::fake();

        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('test.jpg', 2100,'image/jpeg');

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
                'value' => $file
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
        $this->assertDatabaseMissing(Property::class, [
            'value' => "licenses/".$file->hashName()
        ]);
    }

    public function test_user_can_create_found_model_with_more_than_one_property()
    {
        Storage::fake();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);
        PropertyType::factory()->for($license)->count(rand(1,4))->create([
            'show_to_loser' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);
        PropertyType::factory()->for($license)->count(rand(1,4))->create([
            'show_to_loser' => true,
        ]);
        $thirdPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $firstFile = UploadedFile::fake()->image('test.jpg');
        $secondFile = UploadedFile::fake()->image('test.jpg');
        $value = Str::random(30);

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$firstPropertyType->id" => [
                'value' => $firstFile,
            ],
            "property_type$secondPropertyType->id" => [
                'value' => $value,
            ],
            "property_type$thirdPropertyType->id" => [
                'value' => $secondFile,
            ]
        ]);

        $response->assertRedirect(route(self::FOUND_SHOW, [$license, 1]));

        $this->assertDatabaseCount(Found::class, 1);
        $this->assertDatabaseCount(Property::class, 3);
        $this->assertDatabaseHas(Property::class, [
            'value' => "licenses/".$firstFile->hashName()
        ]);
        $this->assertDatabaseHas(Property::class, [
            'value' => $value
        ]);
        $this->assertDatabaseHas(Property::class, [
            'value' => "licenses/".$secondFile->hashName()
        ]);
    }

    public function test_if_there_is_more_than_one_property_all_of_them_are_required()
    {
        Storage::fake();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);
        PropertyType::factory()->for($license)->count(rand(1,4))->create([
            'show_to_loser' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);
        PropertyType::factory()->for($license)->count(rand(1,4))->create([
            'show_to_loser' => true,
        ]);
        $thirdPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $firstFile = UploadedFile::fake()->image('test.jpg');
        $secondFile = UploadedFile::fake()->image('test.jpg');
        $value = Str::random(30);

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$firstPropertyType->id" => [
                'value' => $firstFile,
            ],
            "property_type$secondPropertyType->id" => [
            ],
            "property_type$thirdPropertyType->id" => [
                'value' => $secondFile,
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
    }

    public function test_if_there_is_more_than_one_property_the_types_must_be_valid()
    {
        Storage::fake();

        $license = License::factory()->create();
        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);
        PropertyType::factory()->for($license)->count(rand(1,4))->create([
            'show_to_loser' => true,
        ]);
        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);
        PropertyType::factory()->for($license)->count(rand(1,4))->create([
            'show_to_loser' => true,
        ]);
        $thirdPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_finder' => true,
        ]);

        $user = User::factory()->create();

        $firstFile = UploadedFile::fake()->image('test.jpg');
        $secondFile = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($user)->post(route(self::FOUND_STORE, $license), [
            "property_type$firstPropertyType->id" => [
                'value' => $firstFile,
            ],
            "property_type$secondPropertyType->id" => [
                'value' => Str::random(30)
            ],
            "property_type$thirdPropertyType->id" => [
                'value' => Str::random(30)
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
    }

    public function test_guest_user_can_not_create_found_model()
    {
        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);

        $value = Str::random(20);

        $response = $this->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
                'value' => $value,
            ]
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
    }

    public function test_admin_can_not_create_any_found_model()
    {
        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_finder' => true,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        $value = Str::random(20);

        $response = $this->actingAs($admin)->post(route(self::FOUND_STORE, $license), [
            "property_type$propertyType->id" => [
                'value' => $value,
            ]
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
    }
}
