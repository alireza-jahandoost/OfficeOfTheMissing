<?php

namespace Tests\Feature\Endpoints\License;

use App\Models\License;
use App\Models\PropertyType;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected const LICENSE_CREATE = 'licenses.create';

    /**
     *
     * @return void
     */
    public function test_create_license_page_should_render_for_admin_successfully()
    {
        $user = User::factory()->create(['is_admin'=>true]);

        $response = $this->actingAs($user)->get(route(self::LICENSE_CREATE));

        $response->assertOk();

        $response->assertInertia(fn(AssertableInertia $page)=>$page
            ->component('Licenses/Create')
        );
    }

    public function test_guest_user_can_not_see_create_license_page()
    {
        $response = $this->get(route(self::LICENSE_CREATE));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_users_must_not_able_to_see_create_license_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route(self::LICENSE_CREATE));

        $response->assertForbidden();
    }


}
