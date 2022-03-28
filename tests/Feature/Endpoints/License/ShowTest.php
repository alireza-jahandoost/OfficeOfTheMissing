<?php

namespace Tests\Feature\Endpoints\License;

use App\Models\License;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected const LICENSE_SHOW = 'licenses.show';

    public function test_admin_can_show_a_license()
    {
        $admin = User::factory()->create(['is_admin'=>true]);

        $licenses = License::factory()->has(PropertyType::factory()->count(4))->count(3)->create();

        $response = $this->actingAs($admin)->get(route(self::LICENSE_SHOW, $licenses[1]));

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Licenses/Show')
            ->where('license.name', $licenses[1]->name)
            ->has('property_types', 4, fn(AssertableInertia $page) => $page
                ->where('name', $licenses[1]->propertyTypes[0]->name)
                ->where('value_type', $licenses[1]->propertyTypes[0]->value_type)
                ->where('hint', $licenses[1]->propertyTypes[0]->hint)
                ->where('show_to_finder', $licenses[1]->propertyTypes[0]->show_to_finder)
                ->where('show_to_loser', $licenses[1]->propertyTypes[0]->show_to_loser)
                ->etc()
            )
        );
    }

    public function test_guest_user_can_not_see_any_license()
    {
        $license = License::factory()->create();
        $response = $this->get(route(self::LICENSE_SHOW, $license));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_users_can_not_see_any_license()
    {
        $license = License::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route(self::LICENSE_SHOW, $license));

        $response->assertForbidden();
    }

}
