<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeadBucketParamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeds = [

            // Admission | School
            [
                'initialprogram_id' => 1,
                'param_id' => 1,
                'weight_new' => 40,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Grade
            [
                'initialprogram_id' => 1,
                'param_id' => 2,
                'weight_new' => 25,
                'weight_existing_mentee' => null,
                'weight_exisiting_non_mentee' => 25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country
            [
                'initialprogram_id' => 1,
                'param_id' => 3,
                'weight_new' => 10,
                'weight_existing_mentee' => null,
                'weight_exisiting_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Status
            [
                'initialprogram_id' => 1,
                'param_id' => 4,
                'weight_new' => 25,
                'weight_existing_mentee' => null,
                'weight_exisiting_non_mentee' => 25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School
            [
                'initialprogram_id' => 2,
                'param_id' => 1,
                'weight_new' => 40,
                'weight_existing_mentee' => null,
                'weight_exisiting_non_mentee' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade
            [
                'initialprogram_id' => 2,
                'param_id' => 2,
                'weight_new' => 30,
                'weight_existing_mentee' => 50,
                'weight_exisiting_non_mentee' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Status
            [
                'initialprogram_id' => 2,
                'param_id' => 4,
                'weight_new' => 25,
                'weight_existing_mentee' => null,
                'weight_exisiting_non_mentee' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Major
            [
                'initialprogram_id' => 2,
                'param_id' => 5,
                'weight_new' => null,
                'weight_existing_mentee' => 50,
                'weight_exisiting_non_mentee' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School
            [
                'initialprogram_id' => 3,
                'param_id' => 1,
                'weight_new' => 60,
                'weight_existing_mentee' => 35,
                'weight_exisiting_non_mentee' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade
            [
                'initialprogram_id' => 3,
                'param_id' => 2,
                'weight_new' => 40,
                'weight_existing_mentee' => 65,
                'weight_exisiting_non_mentee' => 60,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | School
            [
                'initialprogram_id' => 4,
                'param_id' => 1,
                'weight_new' => 70,
                'weight_existing_mentee' => 30,
                'weight_exisiting_non_mentee' => 50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | Grade
            [
                'initialprogram_id' => 4,
                'param_id' => 2,
                'weight_new' => 30,
                'weight_existing_mentee' => 70,
                'weight_exisiting_non_mentee' => 50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

        ];

        DB::table('tbl_lead_bucket_params')->insert($seeds);
    }
}
