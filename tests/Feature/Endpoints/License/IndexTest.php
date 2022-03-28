<?php

namespace Tests\Feature\Endpoints\License;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class IndexTest extends TestCase
{
     use RefreshDatabase;

     protected const LICENSE_INDEX = 'licenses.index';

    public function test_admin_can_index_licenses()
    {
        $licenses = License::factory()->count(4)->create();

        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route(self::LICENSE_INDEX));

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Licenses/Index')
            ->has('licenses', 4)
            ->where('licenses.0.name', $licenses[0]->name)
            ->where('licenses.1.name', $licenses[1]->name)
            ->where('licenses.2.name', $licenses[2]->name)
            ->where('licenses.3.name', $licenses[3]->name)
        );
    }

    public function test_guest_user_can_not_index_licenses()
    {
        License::factory()->count(4)->create();

        $response = $this->get(route(self::LICENSE_INDEX));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_users_can_not_index_licenses()
    {
        License::factory()->count(4)->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route(self::LICENSE_INDEX));

        $response->assertForbidden();
    }


}
