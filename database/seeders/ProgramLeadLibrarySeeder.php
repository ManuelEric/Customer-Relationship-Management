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

            // Admission (new) | School
            [
                'programbucket_id' => 'B-1',
                'value_category' => 1,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-1',
                'value_category' => 2,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-1',
                'value_category' => 3,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-1',
                'value_category' => 4,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-1',
                'value_category' => 5,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-1',
                'value_category' => 6,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-1',
                'value_category' => 7,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-1',
                'value_category' => 8,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission (new) | Grade
            [
                'programbucket_id' => 'B-2',
                'value_category' => 1,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-2',
                'value_category' => 2,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-2',
                'value_category' => 3,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-2',
                'value_category' => 4,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-2',
                'value_category' => 5,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-2',
                'value_category' => 6,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission (new) | Country
            [
                'programbucket_id' => 'B-3',
                'value_category' => 1,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-3',
                'value_category' => 2,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-3',
                'value_category' => 3,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-3',
                'value_category' => 4,
                'value' => 0,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-3',
                'value_category' => 5,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-3',
                'value_category' => 6,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-3',
                'value_category' => 7,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-3',
                'value_category' => 8,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-3',
                'value_category' => 9,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission (new) | Priority
            [
                'programbucket_id' => 'B-4',
                'value_category' => 1,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-4',
                'value_category' => 2,
                'value' => 1,
                'temp' => 0.75,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-4',
                'value_category' => 3,
                'value' => 1,
                'temp' => 0.5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-4',
                'value_category' => 4,
                'value' => 1,
                'temp' => 0.25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning (new) | School
            [
                'programbucket_id' => 'B-5',
                'value_category' => 1,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-5',
                'value_category' => 2,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-5',
                'value_category' => 3,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-5',
                'value_category' => 4,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-5',
                'value_category' => 5,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-5',
                'value_category' => 6,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-5',
                'value_category' => 7,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-5',
                'value_category' => 8,
                'value' => 1,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning (new) | Grade
            [
                'programbucket_id' => 'B-6',
                'value_category' => 1,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-6',
                'value_category' => 2,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-6',
                'value_category' => 3,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-6',
                'value_category' => 4,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-6',
                'value_category' => 5,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-6',
                'value_category' => 6,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning (new) | Country
            [
                'programbucket_id' => 'B-7',
                'value_category' => 1,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-7',
                'value_category' => 2,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-7',
                'value_category' => 3,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-7',
                'value_category' => 4,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-7',
                'value_category' => 5,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-7',
                'value_category' => 6,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-7',
                'value_category' => 7,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-7',
                'value_category' => 8,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-7',
                'value_category' => 9,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning (new) | Priority
            [
                'programbucket_id' => 'B-8',
                'value_category' => 1,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-8',
                'value_category' => 2,
                'value' => 1,
                'temp' => 0.75,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-8',
                'value_category' => 3,
                'value' => 1,
                'temp' => 0.5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-8',
                'value_category' => 4,
                'value' => 1,
                'temp' => 0.25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            //  Academic Tutoring SAT (new) | School
            [
                'programbucket_id' => 'B-9',
                'value_category' => 1,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-9',
                'value_category' => 2,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-9',
                'value_category' => 3,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-9',
                'value_category' => 4,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-9',
                'value_category' => 5,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-9',
                'value_category' => 6,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-9',
                'value_category' => 7,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-9',
                'value_category' => 8,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance SAT (new) | Grade
            [
                'programbucket_id' => 'B-10',
                'value_category' => 1,
                'value' => 0,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-10',
                'value_category' => 2,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-10',
                'value_category' => 3,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-10',
                'value_category' => 4,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-10',
                'value_category' => 5,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-10',
                'value_category' => 6,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Peformance SAT (new) | Country
            [
                'programbucket_id' => 'B-11',
                'value_category' => 1,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-11',
                'value_category' => 2,
                'value' => 0,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-11',
                'value_category' => 3,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-11',
                'value_category' => 4,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-11',
                'value_category' => 5,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-11',
                'value_category' => 6,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-11',
                'value_category' => 7,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-11',
                'value_category' => 8,
                'value' => 0,
                'temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-11',
                'value_category' => 9,
                'value' => 1,
                'temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Tutoring SAT (new) | Priority
            [
                'programbucket_id' => 'B-12',
                'value_category' => 1,
                'value' => 1,
                'temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-12',
                'value_category' => 2,
                'value' => 1,
                'temp' => 0.75,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-12',
                'value_category' => 3,
                'value' => 1,
                'temp' => 0.5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'programbucket_id' => 'B-12',
                'value_category' => 4,
                'value' => 1,
                'temp' => 0.25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic 



        ];

        DB::table('tbl_program_buckets_params')->insert($seeds);
    }
}
