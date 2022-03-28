<?php

namespace Tests\Feature\Endpoints\License;

use App\Models\Found;
use App\Models\License;
use App\Models\Lost;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTest extends TestCase
{
     use RefreshDatabase;

     protected const LICENSE_DELETE = 'licenses.destroy';
    protected const LICENSE_INDEX = 'licenses.index';

    public function test_admin_can_delete_licenses()
    {
        $licenses = License::factory()->count(4)->create();

        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->delete(route(self::LICENSE_DELETE, $licenses[2]));
        $response->assertRedirect(route(self::LICENSE_INDEX));
        $this->assertModelMissing($licenses[2]);
        $this->assertDatabaseCount(License::class,3);
    }

    public function test_if_a_license_has_some_property_types_they_must_be_removed_after_license_is_deleted()
    {
        $licenses = License::factory()->has(PropertyType::factory()->count(3))->count(4)->create();

        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->delete(route(self::LICENSE_DELETE, $licenses[2]));
        $response->assertRedirect(route(self::LICENSE_INDEX));
        $this->assertModelMissing($licenses[2]);
        $this->assertDatabaseCount(License::class,3);
        $this->assertDatabaseCount(PropertyType::class, 9);
        $this->assertDatabaseMissing(PropertyType::class, [
            'license_id' => 3,
        ]);
    }

    public function test_if_license_has_some_found_or_lost_or_property_models_they_must_be_deleted_after_license_deletation()
    {
        $license = License::factory()->create();
        $propertyType = PropertyType::factory()->for($license)->create();

        $user = User::factory()->create();

        $lost = Lost::factory()->for($user)->for($license)->hasProperties(1, ['property_type_id' => $propertyType->id])->create();
        $lostProperty = $lost->properties[0];

        $found = Found::factory()->for($user)->for($license)->hasProperties(1, ['property_type_id' => $propertyType->id])->create();
        $foundProperty = $found->properties[0];

        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->delete(route(self::LICENSE_DELETE, $license));
        $response->assertRedirect(route(self::LICENSE_INDEX));
        $this->assertModelMissing($license);
        $this->assertModelMissing($propertyType);
        $this->assertModelMissing($found);
        $this->assertModelMissing($lost);
        $this->assertModelMissing($foundProperty);
        $this->assertModelMissing($lostProperty);
        $this->assertDatabaseCount(License::class,0);
        $this->assertDatabaseCount(PropertyType::class, 0);
        $this->assertDatabaseCount(Property::class, 0);
        $this->assertDatabaseCount(Found::class, 0);
        $this->assertDatabaseCount(Lost::class, 0);
        $this->assertDatabaseCount(User::class, 2);
    }

    public function test_guest_user_can_not_delete_any_license()
    {
        $license = License::factory()->create();

        $response = $this->delete(route(self::LICENSE_DELETE, $license));
        $response->assertRedirect(route('login'));
        $this->assertModelExists($license);
        $this->assertDatabaseCount(License::class, 1);
    }

    public function test_non_admin_users_can_not_delete_any_license()
    {
        $user = User::factory()->create();
        $license = License::factory()->create();

        $response = $this->actingAs($user)->delete(route(self::LICENSE_DELETE, $license));
        $response->assertForbidden();
        $this->assertModelExists($license);
        $this->assertDatabaseCount(License::class, 1);
    }


}
