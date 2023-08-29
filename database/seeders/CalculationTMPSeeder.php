<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalculationTMPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeds = [
            [
                'divisi' => 'Sales',
                'contribution_in_percent' => 33.33,
            ],
            [
                'divisi' => 'Referral',
                'contribution_in_percent' => 33.33,
            ],
            [
                'divisi' => 'Digital',
                'contribution_in_percent' => 33.33,
            ]
        ];

        DB::table('contribution_calculation_tmp')->insert($seeds);
    }
}
