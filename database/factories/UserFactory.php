<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'number' => fake()->randomNumber(),
            'id' => fake()->uuid(),
            'nip' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'address' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->phoneNumber(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'emergency_contact_relation_name' => fake()->name(),
            'datebirth' => fake()->dateTime()->format('Y-m-d'),
            'position_id' => \App\Models\Position::inRandomOrder()->first()->id,
            'password' => Hash::make('password'),
            'hiredate' => Carbon::now(),
            'nik' => fake()->randomNumber(9),
            'idcard' => NULL,
            'cv' => NULL,
            'bank_name' => 'BCA',
            'account_name' => fake()->name(),
            'account_no' => fake()->randomNumber(9),
            'npwp' => fake()->randomNumber(9),
            'tax' => Str::random(30),
            'active' => 1,
            'health_insurance' => Str::random(50),
            'empl_insurance' => Str::random(50),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
