<?php

namespace Database\Seeders\HotLeadsAutomation;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CategoryLeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = [
            [
                'value' => 'International',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'National+',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Homeschool',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Homeschool$',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'National - Private',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'National - Private $',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'National - Negeri',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'National - Negeri $',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('tbl_school_categorization_lead')->insert($school);

        $grade = [
            [
                'value' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => -1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => -2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => -3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => -4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => -5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('tbl_grade_categorization_lead')->insert($grade);

        $country = [
            [
                'value' => 'US',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'UK',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Canada',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Australia',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Hongkong',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Singapore',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Other',
                'description' => 'The value in interested country not in country categorization',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Undecided',
                'description' => 'The value is null',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Undecided $',
                'description' => 'The value in interested country is null but funding is true',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('tbl_country_categorization_lead')->insert($country);

        $status = [
            [
                'value' => 'Parent',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Student',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('tbl_status_categorization_lead')->insert($status);

        $major = [
            [
                'value' => 'Decided',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'value' => 'Undecided',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('tbl_major_categorization_lead')->insert($major);

        $priority = [
            [
                'name' => 'Admission Mentoring',
                'weight' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Experiential Learning',
                'weight' => 0.75,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'SAT',
                'weight' => 0.5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'ACAD',
                'weight' => 0.25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('tbl_priority_lead')->insert($priority);

        // $seasonal = [
        //     [
        //         'prog_id' => 'BCSP',
        //         'start' => null,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     ],
        //     [
        //         'prog_id' => 'OBY',
        //         'start' => null,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     ],
        // ];

        // DB::table('tbl_seasonal_lead')->insert($seasonal);

        // $joined = [
        //     [
        //         'initialprogram_id' => '1',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     ],
        //     [
        //         'initialprogram_id' => '2',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     ],
        //     [
        //         'initialprogram_id' => '3',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     ],
        //     [
        //         'initialprogram_id' => '4',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     ],
        // ];

        // DB::table('tbl_already_joined')->insert($joined);
    }
}
