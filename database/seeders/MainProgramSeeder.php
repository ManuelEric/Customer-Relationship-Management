<?php

namespace Database\Seeders;

use App\Models\MainProg;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MainProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seed = [
            [
                'prog_name' => 'Admissions Mentoring',
                'prog_status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'prog_name' => 'Career Exploration',
                'prog_status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'prog_name' => 'Application Bootcamp',
                'prog_status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'prog_name' => 'Academic & Test Preparation',
                'prog_status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'prog_name' => 'Events & Info Sessions',
                'prog_status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        MainProg::insert($seed);
    }
}
