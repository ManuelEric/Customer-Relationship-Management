<?php

namespace Database\Factories;

use App\Http\Traits\StandardizePhoneNumberTrait;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserClient>
 */
class UserClientFactory extends Factory
{
    use StandardizePhoneNumberTrait;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'mail' => fake()->unique()->safeEmail(),
            'phone' => $this->setPhoneNumber(Str::random(11)),
            'st_grade' => rand(10, 12),
            'st_levelinterest' => array_rand(['Low', 'Medium', 'High'], 1),
            'graduation_year' => rand(2022, 2023),
            'st_abryear' => rand(2022, 2023),
            'st_password' => Hash::make('12345678'),
        ];
    }
}
