<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @test.
     */
    public function test_store(): void
    {
        $user = \App\Models\User::where('email', 'manuel.eric@edu-all.com')->first();
        $response = $this->actingAs($user)

        # hit post method store user
        ->post(route('user.store', ['user_role' => 'tutor']), [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'emergency_contact_phone' => fake()->unique()->phoneNumber(),
            'datebirth' => '1985-04-21',
            'address' => fake()->unique()->address(),
            'hiredate' => Carbon::now(),
            'nik' => fake()->numerify('################'),
            'bank_name' => 'BCA',
            'account_name' => fake()->name(),
            'account_no' => fake()->numerify('#########'),
            'npwp' => fake()->numerify('#########'),
            'password' => Hash::make('password'),
            'position_id' => \App\Models\Position::inRandomOrder()->first()->id,
            'graduated_from' => [\App\Models\University::inRandomOrder()->first()->univ_id],
            'major' => [\App\Models\Major::inRandomOrder()->first()->id],
            'degree' => ['Bachelor'],
            'graduation_date' => NULL,
            'role' => [4], # tutor
            'type' => 1, # full time
            'department' => 4, # client management
            'start_period' => Carbon::now(),
            'end_period' => null,
            // 'agreement' => [],
            // 'subject_id' => [],
            // 'year' => [],
            // 'grade' => [][],
            // 'fee_individual' => [][],
            // 'fee_group' => [][],
            // 'additional_fee' => [][],
            // 'head' => [][]
        ]);

        $response->assertStatus(200);

        $response->assertRedirect(route('user.index', ['user_role' => 'tutor']));
    }
}
