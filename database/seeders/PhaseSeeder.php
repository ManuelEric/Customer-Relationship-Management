<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = [
            [
                'phase_name' => 'ALL',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'phase_name' => 'Interests & Career Exploration',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'phase_name' => 'Profile Building',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'phase_name' => 'University Application Strategy',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'phase_name' => 'Writing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // [
            //     'phase_name' => 'BONUS',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],
        ];

        DB::table('phases')->insert($seeds);
    }
}
