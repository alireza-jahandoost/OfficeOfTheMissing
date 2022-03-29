<?php

namespace Tests\Feature\Endpoints\Lost;

use App\Models\License;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CreateTest extends TestCase
{
     use RefreshDatabase;

     protected const LOST_CREATE = 'licenses.losts.create';

    public function test_user_can_see_lost_creation_page()
    {
        $user = User::factory()->create();
        $license = License::factory()->create();

        $response = $this->actingAs($user)->get(route(self::LOST_CREATE, $license));

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Losts/Create')
        );
    }

    public function test_the_information_of_license_and_property_types_must_be_sent_to_ui()
    {
        $user = User::factory()->create();
        $license = License::factory()->create();
        $lostPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => false,
        ]);
        $foundPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => false,
            'show_to_finder' => true,
        ]);
        $globalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_loser' => true,
            'show_to_finder' => true,
        ]);

        $response = $this->actingAs($user)->get(route(self::LOST_CREATE, $license));

        $response->assertInertia(fn(AssertableInertia $page) => $page
            ->where('license.name', $license->name)
            ->has('property_types', 2, fn(AssertableInertia $page) => $page
                ->where('value_type', $lostPropertyType->value_type)
                ->where('hint', $lostPropertyType->hint)
                ->where('license_id', $license->id)
                ->missing('show_to_finder')
                ->missing('show_to_loser')
                ->etc()
            )
            ->where('property_types.0.name', $lostPropertyType->name)
            ->where('property_types.1.name', $globalPropertyType->name)
        );
    }


    public function test_guest_user_can_not_see_create_lost_page()
    {
        $license = License::factory()->create();

        $response = $this->get(route(self::LOST_CREATE, $license));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_user_can_not_see_lost_page()
    {
        $admin = User::factory()->create(['is_admin'=>true]);
        $license = License::factory()->create();

        $response = $this->actingAs($admin)->get(route(self::LOST_CREATE, $license));

        $response->assertForbidden();
    }


}
