<?php

namespace Tests\Feature\Endpoints\Lost;

use App\Mail\LostHasFound;
use App\Models\Found;
use App\Models\License;
use App\Models\Lost;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class MatchTest extends TestCase
{
    use RefreshDatabase;

    protected const LOST_MATCH = 'licenses.losts.match';
    protected const LOST_SHOW = 'licenses.losts.show';

    public function test_lost_has_found_email_type_must_be_sent_to_finder()
    {
        Mail::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $firstGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $secondGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $firstValue = Str::random(30);
        $secondValue = Str::random(40);

        $lost = Lost::factory()->for($license)->for($user)->create();
        $firstLostProperty = $lost->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue,
        ]);
        $secondLostProperty = $lost->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue,
        ]);

        $found = Found::factory()->for($license)->for($anotherUser)->create();
        $firstFoundProperty = $found->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue
        ]);
        $secondFoundProperty = $found->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue
        ]);

        $response = $this->actingAs($user)->post(route(self::LOST_MATCH, [$lost, $found]));
        $response->assertRedirect(route(self::LOST_SHOW, [$license, $lost]));

        Mail::assertQueued(function(LostHasFound $mail) use ($lost, $found){
            return (
                $mail->lost->id === $lost->id &&
                $mail->found->id === $found->id &&
                $mail->hasTo($found->user->email)
            );
        });
    }

    public function test_found_and_lost_must_belong_to_a_same_license()
    {
        Mail::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();
        $anotherLicense = License::factory()->create();

        $firstGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $secondGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $firstValue = Str::random(30);
        $secondValue = Str::random(40);

        $lost = Lost::factory()->for($license)->for($user)->create();
        $firstLostProperty = $lost->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue,
        ]);
        $secondLostProperty = $lost->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue,
        ]);

        $found = Found::factory()->for($anotherLicense)->for($anotherUser)->create();
        $firstFoundProperty = $found->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue
        ]);
        $secondFoundProperty = $found->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue
        ]);

        $response = $this->actingAs($user)->post(route(self::LOST_MATCH, [$lost, $found]));
        $response->assertForbidden();
        Mail::assertNothingQueued();
    }

    public function test_lost_must_belong_to_current_user()
    {
        Mail::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $firstGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $secondGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $firstValue = Str::random(30);
        $secondValue = Str::random(40);

        $lost = Lost::factory()->for($license)->for($user)->create();
        $firstLostProperty = $lost->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue,
        ]);
        $secondLostProperty = $lost->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue,
        ]);

        $found = Found::factory()->for($license)->for($anotherUser)->create();
        $firstFoundProperty = $found->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue
        ]);
        $secondFoundProperty = $found->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue
        ]);

        $response = $this->actingAs($anotherUser)->post(route(self::LOST_MATCH, [$lost, $found]));
        $response->assertForbidden();

        Mail::assertNothingQueued();
    }

    public function test_lost_and_found_models_must_match()
    {
        Mail::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $firstGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $secondGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $firstValue = Str::random(30);
        $secondValue = Str::random(40);

        $lost = Lost::factory()->for($license)->for($user)->create();
        $firstLostProperty = $lost->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue.'a',
        ]);
        $secondLostProperty = $lost->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue,
        ]);

        $found = Found::factory()->for($license)->for($anotherUser)->create();
        $firstFoundProperty = $found->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue
        ]);
        $secondFoundProperty = $found->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue
        ]);

        $response = $this->actingAs($user)->post(route(self::LOST_MATCH, [$lost, $found]));
        $response->assertForbidden();

        Mail::assertNothingQueued();
    }

    public function test_guest_user_can_not_match_any_lost_and_found_models()
    {
        Mail::fake();

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $firstGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $secondGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $firstValue = Str::random(30);
        $secondValue = Str::random(40);

        $lost = Lost::factory()->for($license)->for($user)->create();
        $firstLostProperty = $lost->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue,
        ]);
        $secondLostProperty = $lost->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue,
        ]);

        $found = Found::factory()->for($license)->for($anotherUser)->create();
        $firstFoundProperty = $found->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue
        ]);
        $secondFoundProperty = $found->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue
        ]);

        $response = $this->post(route(self::LOST_MATCH, [$lost, $found]));
        $response->assertRedirect(route('login'));

        Mail::assertNothingQueued();
    }

    public function test_admin_can_not_match_any_lost_and_found_models()
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $license = License::factory()->create();

        $firstGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $secondGlobalPropertyType = PropertyType::factory()->for($license)->create([
            'show_to_finder' => true,
            'show_to_loser' => true,
        ]) ;

        $firstValue = Str::random(30);
        $secondValue = Str::random(40);

        $lost = Lost::factory()->for($license)->for($user)->create();
        $firstLostProperty = $lost->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue,
        ]);
        $secondLostProperty = $lost->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue,
        ]);

        $found = Found::factory()->for($license)->for($anotherUser)->create();
        $firstFoundProperty = $found->properties()->create([
            'property_type_id' => $firstGlobalPropertyType->id,
            'value' => $firstValue
        ]);
        $secondFoundProperty = $found->properties()->create([
            'property_type_id' => $secondGlobalPropertyType->id,
            'value' => $secondValue
        ]);

        $response = $this->actingAs($admin)->post(route(self::LOST_MATCH, [$lost, $found]));
        $response->assertForbidden();

        Mail::assertNothingQueued();
    }
}
