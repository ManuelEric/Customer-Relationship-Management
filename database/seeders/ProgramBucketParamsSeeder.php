<?php

namespace Database\Seeders;

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

            // Admission | Client New
            [
                'bucket_id' => 'B-1',
                'initialprogram_id' => 1,
                'param_id' => 1,
                'weight' => 40,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-2',
                'initialprogram_id' => 1,
                'param_id' => 2,
                'weight' => 30,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-3',
                'initialprogram_id' => 1,
                'param_id' => 3,
                'weight' => 20,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-4',
                'initialprogram_id' => 1,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Client New
            [
                'bucket_id' => 'B-5',
                'initialprogram_id' => 2,
                'param_id' => 1,
                'weight' => 40,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-6',
                'initialprogram_id' => 2,
                'param_id' => 2,
                'weight' => 40,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-7',
                'initialprogram_id' => 2,
                'param_id' => 3,
                'weight' => 10,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-8',
                'initialprogram_id' => 2,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Client New
            [
                'bucket_id' => 'B-9',
                'initialprogram_id' => 3,
                'param_id' => 1,
                'weight' => 30,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-10',
                'initialprogram_id' => 3,
                'param_id' => 2,
                'weight' => 30,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-11',
                'initialprogram_id' => 3,
                'param_id' => 3,
                'weight' => 30,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-12',
                'initialprogram_id' => 3,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | client New
            [
                'bucket_id' => 'B-13',
                'initialprogram_id' => 4,
                'param_id' => 1,
                'weight' => 40,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-14',
                'initialprogram_id' => 4,
                'param_id' => 2,
                'weight' => 30,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-15',
                'initialprogram_id' => 4,
                'param_id' => 3,
                'weight' => 20,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-16',
                'initialprogram_id' => 4,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | client Existing Mentee
            [
                'bucket_id' => 'B-17',
                'initialprogram_id' => 2,
                'param_id' => 2,
                'weight' => 70,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-18',
                'initialprogram_id' => 2,
                'param_id' => 8,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-19',
                'initialprogram_id' => 2,
                'param_id' => 7,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-20',
                'initialprogram_id' => 2,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | client Existing Mentee
            [
                'bucket_id' => 'B-21',
                'initialprogram_id' => 3,
                'param_id' => 2,
                'weight' => 70,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-22',
                'initialprogram_id' => 3,
                'param_id' => 7,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-23',
                'initialprogram_id' => 3,
                'param_id' => 8,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-24',
                'initialprogram_id' => 3,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | client Existing Mentee
            [
                'bucket_id' => 'B-25',
                'initialprogram_id' => 4,
                'param_id' => 2,
                'weight' => 70,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-26',
                'initialprogram_id' => 4,
                'param_id' => 7,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-27',
                'initialprogram_id' => 4,
                'param_id' => 8,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-28',
                'initialprogram_id' => 4,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'Existing Mentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission Mentoring | client Existing Non Mentee
            [
                'bucket_id' => 'B-29',
                'initialprogram_id' => 1,
                'param_id' => 2,
                'weight' => 30,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-30',
                'initialprogram_id' => 1,
                'param_id' => 1,
                'weight' => 40,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-31',
                'initialprogram_id' => 1,
                'param_id' => 3,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-32',
                'initialprogram_id' => 1,
                'param_id' => 7,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-33',
                'initialprogram_id' => 1,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | client Existing Non Mentee
            [
                'bucket_id' => 'B-34',
                'initialprogram_id' => 2,
                'param_id' => 2,
                'weight' => 40,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-35',
                'initialprogram_id' => 2,
                'param_id' => 1,
                'weight' => 20,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-36',
                'initialprogram_id' => 2,
                'param_id' => 3,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-37',
                'initialprogram_id' => 2,
                'param_id' => 7,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-38',
                'initialprogram_id' => 2,
                'param_id' => 8,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-39',
                'initialprogram_id' => 2,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | client Existing Non Mentee
            [
                'bucket_id' => 'B-40',
                'initialprogram_id' => 3,
                'param_id' => 2,
                'weight' => 40,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-41',
                'initialprogram_id' => 3,
                'param_id' => 1,
                'weight' => 20,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-42',
                'initialprogram_id' => 3,
                'param_id' => 3,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-43',
                'initialprogram_id' => 3,
                'param_id' => 7,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-44',
                'initialprogram_id' => 3,
                'param_id' => 8,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-45',
                'initialprogram_id' => 3,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (Academic Tutoring) | client Existing Non Mentee
            [
                'bucket_id' => 'B-46',
                'initialprogram_id' => 4,
                'param_id' => 2,
                'weight' => 40,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-47',
                'initialprogram_id' => 4,
                'param_id' => 1,
                'weight' => 20,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-48',
                'initialprogram_id' => 4,
                'param_id' => 3,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-49',
                'initialprogram_id' => 4,
                'param_id' => 7,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-50',
                'initialprogram_id' => 4,
                'param_id' => 8,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'bucket_id' => 'B-51',
                'initialprogram_id' => 4,
                'param_id' => 6,
                'weight' => 10,
                'client' => 'Existing NonMentee',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('tbl_program_buckets_params')->insert($seeds);
    }
}
