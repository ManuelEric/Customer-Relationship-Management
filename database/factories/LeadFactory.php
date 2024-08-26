<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $maxLead = Lead::max('lead_id');
        $subStr = substr($maxLead, 3);
        $leadId = "LS". $subStr + 1;
        $department = Department::inRandomOrder()->first();

        return [
            'lead_id' => $leadId,
            'main_lead' => fake()->name(),
            'sub_lead' => null,
            'score' => 0,
            'department_id' => $department->id,
            'color_code' => fake()->hexColor(),
            'note' => null,
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            
        ];
    }
}
