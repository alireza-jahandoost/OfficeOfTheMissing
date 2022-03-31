<?php

namespace Tests\Feature\Endpoints\License;

use App\Models\License;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected const LICENSE_STORE = 'licenses.store';
    protected const LICENSE_SHOW = 'licenses.show';
    protected const LICENSE_CREATE = 'licenses.create';

    public function test_admin_can_create_a_new_license()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'نام پدر',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'text',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect(route(self::LICENSE_SHOW, 1));

        $this->assertDatabaseCount(License::class, 1);
        $this->assertDatabaseCount(PropertyType::class, 3);
    }

    public function test_name_of_license_is_required()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'نام پدر',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'text',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_name_of_license_must_not_be_more_than_30_characters()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => Str::random(31),
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'نام پدر',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'text',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_property_types_is_required()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => Str::random(20),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_property_types_must_be_array()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => Str::random(20),
            'property_types' => Str::random(40),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_property_types_can_not_be_empty()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => Str::random(20),
            'property_types' => [
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_the_name_properties_of_property_types_is_required()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'text',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_the_name_property_of_property_types_must_not_be_more_than_50_characters()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => Str::random(55),
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'text',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_value_type_property_of_property_types_is_required()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_value_type_can_be_text_or_image()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 1);
        $this->assertDatabaseCount(PropertyType::class, 3);
    }

    public function test_value_types_can_not_be_anything_except_text_and_image()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'string',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_hint_is_not_required()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 1);
        $this->assertDatabaseCount(PropertyType::class, 3);
    }

    public function test_hint_must_not_be_longer_than_100_characters()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => Str::random(101),
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_show_to_loser_is_required()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => true,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_show_to_loser_must_be_boolean()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => true,
                    'show_to_loser' => Str::random(10),
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_show_to_finder_is_required()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_loser' => true,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_show_to_finder_must_be_boolean()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_loser' => true,
                    'show_to_finder' => Str::random(10),
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_there_must_be_at_least_a_same_field_for_loser_and_finder()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_the_same_property_between_finder_and_loser_can_not_be_type_of_image()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'نام پدر',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'text',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
                3 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'image',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_there_must_not_be_a_property_type_invisible_to_finder_and_loser()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'image',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_guest_user_can_not_create_any_license()
    {
        $response = $this->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'نام پدر',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'text',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }

    public function test_non_admin_user_can_not_create_any_license()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route(self::LICENSE_STORE), [
            'name' => 'شناسنامه‌',
            'property_types' => [
                0 => [
                    'name' => 'شماره شناسنامه',
                    'value_type' => 'text',
                    'hint' => 'شماره شناسنامه(فقط اعداد)',
                    'show_to_finder' => true,
                    'show_to_loser' => true,
                ],
                1 => [
                    'name' => 'نام پدر',
                    'value_type' => 'text',
                    'hint' => 'نام پدر(به زبان فارسی)',
                    'show_to_finder' => false,
                    'show_to_loser' => true,
                ],
                2 => [
                    'name' => 'نام و نام خانوادگی',
                    'value_type' => 'text',
                    'hint' => 'نام و نام خانوادگی به زبان فارسی',
                    'show_to_finder' => true,
                    'show_to_loser' => false,
                ],
            ]
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount(License::class, 0);
        $this->assertDatabaseCount(PropertyType::class, 0);
    }
}
