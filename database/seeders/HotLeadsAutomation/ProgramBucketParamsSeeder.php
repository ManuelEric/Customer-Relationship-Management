<?php

namespace Database\Seeders\HotLeadsAutomation;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProgramBucketParamsSeeder extends Seeder
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
                'bucket_id' => 'B-1',
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
                'bucket_id' => 'B-2',
                'initialprogram_id' => 1,
                'param_id' => 2,
                'weight_new' => 30,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country
            [
                'bucket_id' => 'B-3',
                'initialprogram_id' => 1,
                'param_id' => 3,
                'weight_new' => 20,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Priority
            [
                'bucket_id' => 'B-4',
                'initialprogram_id' => 1,
                'param_id' => 6,
                'weight_new' => 10,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Seasonal
            [
                'bucket_id' => 'B-5',
                'initialprogram_id' => 1,
                'param_id' => 7,
                'weight_new' => null,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School
            [
                'bucket_id' => 'B-6',
                'initialprogram_id' => 2,
                'param_id' => 1,
                'weight_new' => 40,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade
            [
                'bucket_id' => 'B-7',
                'initialprogram_id' => 2,
                'param_id' => 2,
                'weight_new' => 40,
                'weight_existing_mentee' => 70,
                'weight_existing_non_mentee' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country
            [
                'bucket_id' => 'B-8',
                'initialprogram_id' => 2,
                'param_id' => 3,
                'weight_new' => 10,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Priority
            [
                'bucket_id' => 'B-9',
                'initialprogram_id' => 2,
                'param_id' => 6,
                'weight_new' => 10,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Already Joined
            [
                'bucket_id' => 'B-10',
                'initialprogram_id' => 2,
                'param_id' => 8,
                'weight_new' => null,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Seasonal
            [
                'bucket_id' => 'B-11',
                'initialprogram_id' => 2,
                'param_id' => 7,
                'weight_new' => null,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School
            [
                'bucket_id' => 'B-12',
                'initialprogram_id' => 3,
                'param_id' => 1,
                'weight_new' => 30,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade
            [
                'bucket_id' => 'B-13',
                'initialprogram_id' => 3,
                'param_id' => 2,
                'weight_new' => 30,
                'weight_existing_mentee' => 70,
                'weight_existing_non_mentee' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country
            [
                'bucket_id' => 'B-14',
                'initialprogram_id' => 3,
                'param_id' => 3,
                'weight_new' => 30,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Priority
            [
                'bucket_id' => 'B-15',
                'initialprogram_id' => 3,
                'param_id' => 6,
                'weight_new' => 10,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Seasonal
            [
                'bucket_id' => 'B-16',
                'initialprogram_id' => 3,
                'param_id' => 7,
                'weight_new' => null,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Already Joined
            [
                'bucket_id' => 'B-17',
                'initialprogram_id' => 3,
                'param_id' => 8,
                'weight_new' => null,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | School
            [
                'bucket_id' => 'B-18',
                'initialprogram_id' => 4,
                'param_id' => 1,
                'weight_new' => 40,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | Grade
            [
                'bucket_id' => 'B-19',
                'initialprogram_id' => 4,
                'param_id' => 2,
                'weight_new' => 30,
                'weight_existing_mentee' => 70,
                'weight_existing_non_mentee' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | Country
            [
                'bucket_id' => 'B-20',
                'initialprogram_id' => 4,
                'param_id' => 3,
                'weight_new' => 20,
                'weight_existing_mentee' => null,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | Priority
            [
                'bucket_id' => 'B-21',
                'initialprogram_id' => 4,
                'param_id' => 6,
                'weight_new' => 10,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | Seasonal
            [
                'bucket_id' => 'B-22',
                'initialprogram_id' => 4,
                'param_id' => 7,
                'weight_new' => null,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | Already Joined
            [
                'bucket_id' => 'B-23',
                'initialprogram_id' => 4,
                'param_id' => 8,
                'weight_new' => null,
                'weight_existing_mentee' => 10,
                'weight_existing_non_mentee' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

        ];

        DB::table('tbl_program_buckets_params')->insert($seeds);
    }
}
