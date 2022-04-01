<?php

namespace Tests\Feature\Endpoints\Lost;

use App\Models\License;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class IndexLicensesTest extends TestCase
{
    use RefreshDatabase;

    protected const LOST_LICENSES_INDEX = 'licenses.losts.index_licenses';

    public function test_user_can_see_index_licenses_page()
    {
        $user = User::factory()->create();
        $licenses = License::factory()->count(5)->create();
        foreach($licenses as $license){
            PropertyType::factory()->for($license)->create([
                'show_to_loser' => true,
                'show_to_finder' => true,
            ]);
        }

        $response = $this->actingAs($user)->get(route(self::LOST_LICENSES_INDEX));

        $response->assertOk();

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Losts/IndexLicenses')
            ->has('licenses', 5)
            ->where('licenses.0.name', $licenses[0]->name)
            ->where('licenses.1.name', $licenses[1]->name)
            ->where('licenses.2.name', $licenses[2]->name)
            ->where('licenses.3.name', $licenses[3]->name)
            ->where('licenses.4.name', $licenses[4]->name)
        );
    }

    public function test_guest_user_can_not_list_licenses()
    {
        $licenses = License::factory()->count(5)->create();
        foreach($licenses as $license){
            PropertyType::factory()->for($license)->create([
                'show_to_loser' => true,
                'show_to_finder' => true,
            ]);
        }

        $response = $this->get(route(self::LOST_LICENSES_INDEX));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_user_can_not_access_losts_index_licenses()
    {
        $licenses = License::factory()->count(5)->create();
        $admin = User::factory()->create(['is_admin' => true]);

        foreach($licenses as $license){
            PropertyType::factory()->for($license)->create([
                'show_to_loser' => true,
                'show_to_finder' => true,
            ]);
        }

        $response = $this->actingAs($admin)->get(route(self::LOST_LICENSES_INDEX));

        $response->assertForbidden();
    }
}
