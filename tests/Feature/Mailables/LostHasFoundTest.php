<?php

namespace Tests\Feature\Mailables;

use App\Mail\LostHasFound;
use App\Models\Found;
use App\Models\License;
use App\Models\Lost;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class LostHasFoundTest extends TestCase
{
    use RefreshDatabase;

    public function test_lost_has_found_shows_information()
    {
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
        $mail = new LostHasFound($lost, $found);

        $mail->assertSeeInHtml($lost->user->email);
        $mail->assertSeeInHtml($firstLostProperty->value);
        $mail->assertSeeInHtml($secondLostProperty->value);
    }

}
