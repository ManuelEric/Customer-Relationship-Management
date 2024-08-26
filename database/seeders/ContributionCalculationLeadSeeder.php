<?php

namespace Database\Seeders;

use App\Models\ContributionCalculation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContributionCalculationLeadSeeder extends Seeder
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
                'contribution_in_percent' => '33.33',
                'contribution_to_target' => NULL,
                'initial_consult_target' => NULL,
                'hot_leads_target' => NULL,
                'leads_needed' => NULL,
            ],
            [
                'divisi' => 'Referral',
                'contribution_in_percent' => '33.33',
                'contribution_to_target' => NULL,
                'initial_consult_target' => NULL,
                'hot_leads_target' => NULL,
                'leads_needed' => NULL,
            ],
            [
                'divisi' => 'Digital',
                'contribution_in_percent' => '33.33',
                'contribution_to_target' => NULL,
                'initial_consult_target' => NULL,
                'hot_leads_target' => NULL,
                'leads_needed' => NULL,
            ]
        ];

        ContributionCalculation::insert($seeds);
    }
}
