<?php

namespace Tests\Feature\Endpoints\Lost;

use App\Models\Found;
use App\Models\License;
use App\Models\Lost;
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

    protected const LOST_SHOW = 'licenses.losts.show';

    public function test_user_can_see_show_lost_models_page()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();

        $lost = Lost::factory()->for($user)->for($license)->create();

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_loser' => true,
            'show_to_finder' => true,
        ]);

        $firstPropertyTypeOfFound = PropertyType::factory()->for($license)->create([
            'show_to_finder'=>true,
        ]);

        $secondPropertyTypeOfFound = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_loser' => true,
        ]);

        $value = Str::random();
        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->get(route(self::LOST_SHOW, [$license, $lost]));

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Losts/Show')
            ->where('license.name', $license->name)
            ->has('property_types', 4, fn(AssertableInertia $page) => $page
                ->where('name', $firstPropertyType->name)
                ->where('value_type', $firstPropertyType->value_type)
                ->where('hint', $firstPropertyType->hint)
                ->missing('show_to_finder')
                ->missing('show_to_loser')
                ->etc()
            )
            ->has('founds', 0)
            ->has('lost.properties', 2)
            ->where('lost.id', $lost->id )
            ->where('lost.properties.0.value', $firstProperty->value)
            ->where('lost.properties.1.value', $secondProperty->value)
        );
    }

    public function test_if_there_is_any_matching_found_model_it_must_be_listed()
    {
        Storage::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $firstGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
        ]);
        $secondGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
        ]);

        $lostPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => false,
        ]);

        $textFoundPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => false,
            'show_to_finder' => true,
        ]);

        $imageFoundPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => false,
            'show_to_finder' => true,
            'value_type' => 'image',
        ]);

        $firstSameValue = Str::random(30);
        $secondSameValue = Str::random(40);

        $lost = Lost::factory()->for($license)->for($user)->create();
        $firstGlobalPropertyOfLost = $lost->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstSameValue,
        ]);
        $secondGlobalPropertyOfLost = $lost->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondSameValue,
        ]);
        $lostPropertyOfLost = $lost->properties()->create([
            'property_type_id' => $lostPropertyType->id,
            'value' => Str::random()
        ]);

        $firstMatchFound = Found::factory()->for($license)->for($anotherUser)->create();
        $firstGlobalPropertyOfFirstMatchFound = $firstMatchFound->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstSameValue,
        ]);
        $secondGlobalPropertyOfFirstMatchFound = $firstMatchFound->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondSameValue,
        ]);
        $textFoundPropertyOfFirstMatchFound = $firstMatchFound->properties()->create([
            'property_type_id' => $textFoundPropertyType->id,
            'value' => Str::random()
        ]);
        $firstMatchFoundFile = UploadedFile::fake()->image('test.jpg');
        $firstMatchFoundPath = Storage::putFile('licenses', $firstMatchFoundFile);
        $imageFoundPropertyOfFirstMatchFound = $firstMatchFound->properties()->create([
            'property_type_id' => $imageFoundPropertyType->id,
            'value' => $firstMatchFoundPath,
        ]);

        $firstNotMatchFound = Found::factory()->for($license)->for($anotherUser)->create();
        $firstGlobalPropertyOfFirstNotMatchFound = $firstNotMatchFound->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstSameValue.'aaa',
        ]);
        $secondGlobalPropertyOfFirstNotMatchFound = $firstNotMatchFound->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondSameValue,
        ]);
        $textFoundPropertyOfFirstNotMatchFound = $firstNotMatchFound->properties()->create([
            'property_type_id' => $textFoundPropertyType->id,
            'value' => Str::random()
        ]);
        $firstNotMatchFoundFile = UploadedFile::fake()->image('test.jpg');
        $firstNotMatchFoundPath = Storage::putFile('licenses', $firstNotMatchFoundFile);
        $imageFoundPropertyOfFirstNotMatchFound = $firstNotMatchFound->properties()->create([
            'property_type_id' => $imageFoundPropertyType->id,
            'value' => $firstNotMatchFoundPath,
        ]);

        $secondNotMatchFound = Found::factory()->for($license)->for($anotherUser)->create();
        $firstGlobalPropertyOfSecondNotMatchFound = $secondNotMatchFound->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstSameValue,
        ]);
        $secondGlobalPropertyOfSecondNotMatchFound = $secondNotMatchFound->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondSameValue.'aaa',
        ]);
        $textFoundPropertyOfSecondNotMatchFound = $secondNotMatchFound->properties()->create([
            'property_type_id' => $textFoundPropertyType->id,
            'value' => Str::random()
        ]);
        $secondNotMatchFoundFile = UploadedFile::fake()->image('test.jpg');
        $secondNotMatchFoundPath = Storage::putFile('licenses', $secondNotMatchFoundFile);
        $imageFoundPropertyOfSecondNotMatchFound = $secondNotMatchFound->properties()->create([
            'property_type_id' => $imageFoundPropertyType->id,
            'value' => $secondNotMatchFoundPath,
        ]);

        $secondMatchFound = Found::factory()->for($license)->for($anotherUser)->create();
        $firstGlobalPropertyOfSecondMatchFound = $secondMatchFound->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstSameValue,
        ]);
        $secondGlobalPropertyOfSecondMatchFound = $secondMatchFound->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondSameValue,
        ]);
        $textFoundPropertyOfSecondMatchFound = $secondMatchFound->properties()->create([
            'property_type_id' => $textFoundPropertyType->id,
            'value' => Str::random()
        ]);
        $secondMatchFoundFile = UploadedFile::fake()->image('test.jpg');
        $secondMatchFoundPath = Storage::putFile('licenses', $secondMatchFoundFile);
        $imageFoundPropertyOfSecondMatchFound = $secondMatchFound->properties()->create([
            'property_type_id' => $imageFoundPropertyType->id,
            'value' => $secondMatchFoundPath,
        ]);

        $response = $this->actingAs($user)->get(route(self::LOST_SHOW, [$license, $lost]));

        $response->assertOk();

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->has('founds', 2, fn(AssertableInertia $page) => $page
                ->has('properties', 4, fn(AssertableInertia $page) => $page
                    ->where('value', $firstGlobalPropertyOfFirstMatchFound->value)
                    ->etc()
                )
                ->where('properties.0.id', $firstGlobalPropertyOfFirstMatchFound->id)
                ->where('properties.1.id', $secondGlobalPropertyOfFirstMatchFound->id)
                ->where('properties.2.id', $textFoundPropertyOfFirstMatchFound->id)
                ->where('properties.3.id', $imageFoundPropertyOfFirstMatchFound->id)
                ->etc()
            )
            ->where('founds.0.id', $firstMatchFound->id)
            ->where('founds.1.id', $secondMatchFound->id)
        );
    }

    public function test_another_user_can_not_see_show_lost_models_page_of_user()
    {
        Storage::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $lost = Lost::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_loser' => true,
            'show_to_finder' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_loser' => true,
        ]);

        $value = Str::random();
        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($anotherUser)->get(route(self::LOST_SHOW, [$license, $lost]));

        $response->assertForbidden();
    }

    public function test_to_show_lost_model_lost_and_license_models_must_match()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();
        $anotherLicense = License::factory()->create();

        $lost = Lost::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_loser' => true,
            'show_to_finder' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_loser' => true,
        ]);

        $value = Str::random();
        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($user)->get(route(self::LOST_SHOW, [$anotherLicense, $lost]));

        $response->assertForbidden();
    }

    public function test_guest_user_can_not_see_show_lost_models_page()
    {
        Storage::fake();

        $user = User::factory()->create();
        $license = License::factory()->create();

        $lost = Lost::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_loser' => true,
            'show_to_finder' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_loser' => true,
        ]);

        $value = Str::random();
        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->get(route(self::LOST_SHOW, [$license, $lost]));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_user_can_not_see_show_lost_models_page()
    {
        Storage::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $license = License::factory()->create();

        $lost = Lost::factory()->for($user)->for($license)->create();

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder'=>true,
        ]);

        $firstPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'text',
            'show_to_loser' => true,
            'show_to_finder' => true,
        ]);

        PropertyType::factory()->for($license)->count(rand(1,3))->create([
            'show_to_finder' => true,
        ]);

        $secondPropertyType = PropertyType::factory()->for($license)->create([
            'value_type' => 'image',
            'show_to_loser' => true,
        ]);

        $value = Str::random();
        $firstProperty = $lost->properties()->create([
            'property_type_id' => $firstPropertyType->id,
            'value' => $value,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');
        $path = Storage::putFile('licenses', $file);
        $secondProperty = $lost->properties()->create([
            'property_type_id' => $secondPropertyType->id,
            'value' => $path,
        ]);

        $response = $this->actingAs($admin)->get(route(self::LOST_SHOW, [$license, $lost]));

        $response->assertForbidden();
    }
}
