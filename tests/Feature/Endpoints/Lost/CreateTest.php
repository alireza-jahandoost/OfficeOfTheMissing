<?php

namespace Tests\Feature\Endpoints\Lost;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CreateTest extends TestCase
{
     use RefreshDatabase;

     protected const LOST_CREATE = 'losts.create';

    public function test_user_can_see_lost_creation_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route(self::LOST_CREATE));

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Losts/Create')
        );
    }

    public function test_guest_user_can_not_see_create_lost_page()
    {
        $response = $this->get(route(self::LOST_CREATE));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_user_can_not_see_lost_page()
    {
        $admin = User::factory()->create(['is_admin'=>true]);

        $response = $this->actingAs($admin)->get(route(self::LOST_CREATE));

        $response->assertForbidden();
    }


}
