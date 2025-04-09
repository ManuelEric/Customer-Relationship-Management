<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PhaseDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = [
            # ALL
            [
                'phase_id' => 1,
                'phase_detail_name' => '1:1 Mentoring Hours',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            # Interests & Career Exploration
            [
                'phase_id' => 2,
                'phase_detail_name' => 'Hands-on Activities (Workshop / Site Visit)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 2,
                'phase_detail_name' => 'Talk to Professionals',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 2,
                'phase_detail_name' => 'Test Minat Bakat',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            # Profile Building
            [
                'phase_id' => 3,
                'phase_detail_name' => 'Competition Mentoring',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 3,
                'phase_detail_name' => 'Research Project Mentoring',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 3,
                'phase_detail_name' => 'Passion Project Mentoring',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 3,
                'phase_detail_name' => 'Community Service Trip',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 3,
                'phase_detail_name' => 'Internship/Shadowing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 3,
                'phase_detail_name' => 'Digital Coverage',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            # University Application Strategy
            [
                'phase_id' => 4,
                'phase_detail_name' => 'Essay Editing Hours',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            # Writing
            [
                'phase_id' => 5,
                'phase_detail_name' => 'Essay Bootcamp',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('phase_details')->insert($seeds);
    }
}
