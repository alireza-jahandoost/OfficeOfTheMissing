<?php

namespace Tests\Feature\Endpoints\Lost;

use App\Models\License;
use App\Models\Lost;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected const LOST_DELETE = 'losts.destroy';
    protected const LOST_INDEX = 'losts.index';

    public function test_user_can_delete_their_lost()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'string',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser'=> true,
            'value_type' => 'image'
        ]);

        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => Str::random(30),
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->delete(route(self::LOST_DELETE,[$license, $lost]));

        $response->assertRedirect(route(self::LOST_INDEX, $license));

        $this->assertModelMissing($lost);
        $this->assertModelMissing($firstProperty);
        $this->assertModelMissing($secondProperty);

        Storage::assertMissing($path);
    }

    public function test_just_user_can_delete_their_lost_models()
    {
        Storage::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'string',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser'=> true,
            'value_type' => 'image'
        ]);

        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => Str::random(30),
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($anotherUser)->delete(route(self::LOST_DELETE,[$license, $lost]));

        $response->assertForbidden();

        $this->assertModelExists($lost);
        $this->assertModelExists($firstProperty);
        $this->assertModelExists($secondProperty);

        Storage::assertExists($path);
    }

    public function test_license_and_lost_models_must_match_for_deletation()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();
        $anotherLicense = License::factory()->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'string',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser'=> true,
            'value_type' => 'image'
        ]);

        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => Str::random(30),
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->delete(route(self::LOST_DELETE,[$anotherLicense, $lost]));

        $response->assertForbidden();

        $this->assertModelExists($lost);
        $this->assertModelExists($firstProperty);
        $this->assertModelExists($secondProperty);

        Storage::assertExists($path);
    }

    public function test_guest_user_can_not_delete_any_lost_model()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'string',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser'=> true,
            'value_type' => 'image'
        ]);

        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => Str::random(30),
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->delete(route(self::LOST_DELETE,[$license, $lost]));

        $response->assertRedirect(route('login'));

        $this->assertModelExists($lost);
        $this->assertModelExists($firstProperty);
        $this->assertModelExists($secondProperty);

        Storage::assertExists($path);
    }

    public function test_admin_user_can_not_delete_any_model()
    {
        Storage::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $license = License::factory()->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
            'value_type' => 'string',
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser'=> true,
            'value_type' => 'image'
        ]);

        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => Str::random(30),
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($admin)->delete(route(self::LOST_DELETE,[$license, $lost]));

        $response->assertForbidden();

        $this->assertModelExists($lost);
        $this->assertModelExists($firstProperty);
        $this->assertModelExists($secondProperty);

        Storage::assertExists($path);
    }
}
