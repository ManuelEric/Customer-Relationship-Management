<?php

namespace Database\Seeders;

use App\Models\ContributionCalculation;
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
                'contribution_to_target' => null,
                'initial_consult_target' => null,
                'hot_leads_target' => null,
                'leads_needed' => null,
            ],
            [
                'divisi' => 'Referral',
                'contribution_in_percent' => '33.33',
                'contribution_to_target' => null,
                'initial_consult_target' => null,
                'hot_leads_target' => null,
                'leads_needed' => null,
            ],
            [
                'divisi' => 'Digital',
                'contribution_in_percent' => '33.33',
                'contribution_to_target' => null,
                'initial_consult_target' => null,
                'hot_leads_target' => null,
                'leads_needed' => null,
            ],
        ];

        ContributionCalculation::insert($seeds);
    }
}
