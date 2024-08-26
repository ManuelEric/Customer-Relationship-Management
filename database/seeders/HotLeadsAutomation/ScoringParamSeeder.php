<?php

namespace Database\Seeders\HotLeadsAutomation;

use App\Models\ScoringParam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ScoringParamSeeder extends Seeder
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
                'category' => 'School',
                'max_score' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'category' => 'Lead',
                'max_score' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
                
            ],
            [
                'category' => 'Graduation Year',
                'max_score' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'category' => 'Destination',
                'max_score' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'category' => 'Type of Client',
                'max_score' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        ScoringParam::insert($seeds);
    }
}
