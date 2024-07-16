<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TokenLibFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $headers = [
            [
                'name' => 'Header-ET'
            ]
        ];
        $randomize_token = base64_encode(Str::uuid());
        
        /* calculate gap date */
        $now = Carbon::now();
        $maxTime = $now->setTime(23, 59, 59);
        $gap = $now->diff($maxTime);

        $expires_at = $now->addHours($gap);

        return [
            'header_name' => $headers[array_rand($headers, 1)]['name'],
            'value' => $randomize_token,
            'expires_at' => $expires_at,
        ];
    }
}
