<?php

namespace Database\Factories;

use App\Models\MainProg;
use App\Models\SubProg;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $main_prog = MainProg::inRandomOrder()->first();
        $sub_prog = SubProg::where('main_prog_id', $main_prog->id)->inRandomOrder()->first();
        $prog_type = ['B2C', 'B2B', 'B2B/B2C'];
        $prog_mentor = ['Mentor', 'No'];
        $prog_payment = ['usd', 'idr', 'session'];
        $prog_scope = ['mentee', 'public', null];

        return [
            'prog_id' => substr(fake()->name(), 0, 5),
            'main_prog_id' => $main_prog->id,
            'sub_prog_id' => $sub_prog->id ?? null,
            'prog_main' => $main_prog->prog_name, # unnecessary
            'prog_sub' => $sub_prog->sub_prog_name, # unnecessary
            'main_number' => $main_prog->id, # unnecessary
            'prog_program' => fake()->name(),
            'prog_type' => $prog_type[rand(0,2)],
            'prog_mentor' => $prog_mentor[rand(0,1)],
            'prog_payment' => $prog_payment[rand(0, 2)],
            'prog_scope' => $prog_scope[rand(0, 2)],
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
    }
}
