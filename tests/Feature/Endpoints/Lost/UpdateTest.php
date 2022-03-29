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
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected const LOST_UPDATE = 'losts.update';
    protected const LOST_EDIT = 'losts.edit';

    public function test_user_can_send_update_request_without_any_property()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$license, $lost]),[

        ]);

        $response->assertRedirect(route(self::LOST_EDIT, [$license, $lost]));

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);

        $this->assertDatabaseCount(Lost::class, 1);
    }

    public function test_user_can_update_text_property()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newValue = Str::random(30);

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$firstPropertyType->id" => [
                "value" => $newValue,
            ]
        ]);

        $response->assertRedirect(route(self::LOST_EDIT, [$license, $lost]));

        $this->assertDatabaseMissing(Property::class,[
            'value' => $firstProperty->value,
        ]);
        $this->assertDatabaseHas(Property::class,[
            'value' => $newValue,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);

        $this->assertDatabaseCount(Lost::class, 1);
    }

    public function test_for_updating_text_properties_the_value_must_be_string()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newValue = ['value' => Str::random(30)];

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$firstPropertyType->id" => [
                "value" => $newValue,
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);

        $this->assertDatabaseCount(Lost::class, 1);
    }

    public function test_for_updating_text_properties_value_length_must_be_lower_than_100_characters()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newValue = Str::random(101);

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$firstPropertyType->id" => [
                "value" => $newValue,
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);

        $this->assertDatabaseCount(Lost::class, 1);
    }

    public function test_user_can_update_image_property()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newFile = UploadedFile::fake()->image('test2.jpg');

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$secondPropertyType->id" => [
                "value" => $newFile,
            ]
        ]);

        $response->assertRedirect(route(self::LOST_EDIT, [$license, $lost]));

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseMissing(Property::class,[
            'value' => $secondProperty->value,
        ]);
        $this->assertDatabaseHas(Property::class, [
            'value' => "licenses/".$newFile->hashName(),
        ]);

        $this->assertDatabaseCount(Lost::class, 1);

        Storage::assertMissing($path);
        Storage::assertExists('licenses/'.$newFile->hashName());
    }

    public function test_if_property_type_is_image_the_value_must_be_type_of_image()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newFile = UploadedFile::fake()
            ->create('test2.pdf', 100 , 'application/pdf');

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$secondPropertyType->id" => [
                "value" => $newFile,
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);
        $this->assertDatabaseMissing(Property::class, [
            'value' => "licenses/".$newFile->hashName(),
        ]);

        $this->assertDatabaseCount(Lost::class, 1);

        Storage::assertExists($path);
        Storage::assertMissing('licenses/'.$newFile->hashName());
    }

    public function test_if_property_type_is_image_the_size_must_be_lower_than_2m()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newFile = UploadedFile::fake()
            ->create('test2.jpg', 2001 , 'image/jpeg');

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$secondPropertyType->id" => [
                "value" => $newFile,
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);
        $this->assertDatabaseMissing(Property::class, [
            'value' => "licenses/".$newFile->hashName(),
        ]);

        $this->assertDatabaseCount(Lost::class, 1);

        Storage::assertExists($path);
        Storage::assertMissing('licenses/'.$newFile->hashName());
    }

    public function test_user_can_update_some_properties_together()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);

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

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);

        $thirdPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'image'
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $secondFile = UploadedFile::fake()->image('text.jpg');
        $secondPath = Storage::putFile('licenses', $secondFile);

        $secondProperty = $lost->properties()->create([
            'value' => $secondPath,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $thirdFile = UploadedFile::fake()->image('text.jpg');
        $thirdPath = Storage::putFile('licenses', $thirdFile);

        $thirdProperty = $lost->properties()->create([
            'value' => $thirdPath,
            'property_type_id' => $thirdPropertyType->id,
        ]);

        $newValue = Str::random(30);
        $newSecondFile = UploadedFile::fake()->image('test2.jpg');
        $newThirdFile = UploadedFile::fake()->image('test3.jpg');

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$firstPropertyType->id" => [
                "value" => $newValue,
            ],
            "property_type$secondPropertyType->id" => [
                "value" => $newSecondFile,
            ],
            "property_type$thirdPropertyType->id" => [
                "value" => $newThirdFile,
            ]
        ]);

        $response->assertRedirect(route(self::LOST_EDIT, [$license, $lost]));

        $this->assertDatabaseMissing(Property::class,[
            'value' => $firstProperty->value,
        ]);
        $this->assertDatabaseHas(Property::class, [
            'value' => $newValue,
        ]);

        $this->assertDatabaseMissing(Property::class,[
            'value' => $secondProperty->value,
        ]);
        $this->assertDatabaseHas(Property::class, [
            'value' => "licenses/".$newSecondFile->hashName(),
        ]);

        $this->assertDatabaseMissing(Property::class,[
            'value' => $thirdProperty->value,
        ]);
        $this->assertDatabaseHas(Property::class, [
            'value' => "licenses/".$newThirdFile->hashName(),
        ]);

        $this->assertDatabaseCount(Lost::class, 1);

        Storage::assertMissing($secondPath);
        Storage::assertExists('licenses/'.$newSecondFile->hashName());

        Storage::assertMissing($thirdPath);
        Storage::assertExists('licenses/'.$newThirdFile->hashName());
    }

    public function test_users_can_update_lost_models_that_belongs_to_themselves()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newValue = Str::random(30);

        $response = $this->actingAs($anotherUser)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$firstPropertyType->id" => [
                "value" => $newValue,
            ]
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);

        $this->assertDatabaseCount(Lost::class, 1);
    }

    public function test_lost_and_license_models_must_match_for_updating()
    {
        Storage::fake();
        $license = License::factory()->create();
        $anotherLicense = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newValue = Str::random(30);

        $response = $this->actingAs($user)->put(route(self::LOST_UPDATE, [$anotherLicense, $lost]),[
            "property_type$firstPropertyType->id" => [
                "value" => $newValue,
            ]
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);

        $this->assertDatabaseCount(Lost::class, 1);
    }

    public function test_guest_user_can_not_update_any_lost_model()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newValue = Str::random(30);

        $response = $this->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$firstPropertyType->id" => [
                "value" => $newValue,
            ]
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);

        $this->assertDatabaseCount(Lost::class, 1);
    }

    public function test_admin_user_can_not_update_any_lost_model()
    {
        Storage::fake();
        $license = License::factory()->create();

        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin'=>true]);
        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'value_type' => 'text',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstProperty = $lost->properties()->create([
            'value' => Str::random(30),
            'property_type_id' => $firstPropertyType->id,
        ]);

        $file = UploadedFile::fake()->image('text.jpg');
        $path = Storage::putFile('licenses', $file);

        $secondProperty = $lost->properties()->create([
            'value' => $path,
            'property_type_id' => $secondPropertyType->id,
        ]);

        $newValue = Str::random(30);

        $response = $this->actingAs($admin)->put(route(self::LOST_UPDATE, [$license, $lost]),[
            "property_type$firstPropertyType->id" => [
                "value" => $newValue,
            ]
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas(Property::class,[
            'value' => $firstProperty->value,
        ]);

        $this->assertDatabaseHas(Property::class,[
            'value' => $secondProperty->value,
        ]);

        $this->assertDatabaseCount(Lost::class, 1);
    }
}
