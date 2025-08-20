<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UniversityApplicationDeadlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $universities = University::inRandomOrder()->limit(10)->get();
        foreach ($universities as $university) {
            $university->early_action = Carbon::now()->addDays(rand(0, 12))->subDays(rand(0, 30));
            $university->early_decision = Carbon::now()->addDays(rand(0, 12))->subDays(rand(0, 30));
            $university->regular_deadline = Carbon::now()->addDays(rand(0, 12))->subDays(rand(0, 30));
            $university->save();
        }
    }
}
