<?php

namespace Database\Seeders\HotLeadsAutomation;

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
                'bucket_id' => 'L-1',
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
                'bucket_id' => 'L-2',
                'initialprogram_id' => 1,
                'param_id' => 2,
                'weight_new' => 25,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country
            [
                'bucket_id' => 'L-3',
                'initialprogram_id' => 1,
                'param_id' => 3,
                'weight_new' => 10,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Status
            [
                'bucket_id' => 'L-4',
                'initialprogram_id' => 1,
                'param_id' => 4,
                'weight_new' => 25,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School
            [
                'bucket_id' => 'L-5',
                'initialprogram_id' => 2,
                'param_id' => 1,
                'weight_new' => 40,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade
            [
                'bucket_id' => 'L-6',
                'initialprogram_id' => 2,
                'param_id' => 2,
                'weight_new' => 30,
                'weight_existing_mentee' => 50,
                'weight_existing_non_mentee' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Status
            [
                'bucket_id' => 'L-7',
                'initialprogram_id' => 2,
                'param_id' => 4,
                'weight_new' => 25,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Major
            [
                'bucket_id' => 'L-8',
                'initialprogram_id' => 2,
                'param_id' => 5,
                'weight_new' => null,
                'weight_existing_mentee' => 50,
                'weight_existing_non_mentee' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School
            [
                'bucket_id' => 'L-9',
                'initialprogram_id' => 3,
                'param_id' => 1,
                'weight_new' => 60,
                'weight_existing_mentee' => 35,
                'weight_existing_non_mentee' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade
            [
                'bucket_id' => 'L-10',
                'initialprogram_id' => 3,
                'param_id' => 2,
                'weight_new' => 40,
                'weight_existing_mentee' => 65,
                'weight_existing_non_mentee' => 60,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | School
            [
                'bucket_id' => 'L-11',
                'initialprogram_id' => 4,
                'param_id' => 1,
                'weight_new' => 70,
                'weight_existing_mentee' => 30,
                'weight_existing_non_mentee' => 50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | Grade
            [
                'bucket_id' => 'L-12',
                'initialprogram_id' => 4,
                'param_id' => 2,
                'weight_new' => 30,
                'weight_existing_mentee' => 70,
                'weight_existing_non_mentee' => 50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

        ];

        DB::table('tbl_lead_bucket_params')->insert($seeds);
    }
}
