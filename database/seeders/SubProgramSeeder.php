<?php

namespace Database\Seeders;

use App\Models\MainProg;
use App\Models\SubProg;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SubProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mainProgs = MainProg::all();
        foreach ($mainProgs as $mainProg) {
            $seeds = array();

            switch ($mainProg->prog_name) {

                case "Admissions Mentoring":                    
                    $seeds = [
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'Admissions Mentoring',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'Essay Clinic',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'Interview Preparation',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                    ];
                    
                    break;

                case "Career Exploration":
                    $seeds = [
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'JuniorXplorer',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'PassionXplorer',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'Summer Science Research Program',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                    ];
                    break;

                case "Academic & Test Preparation":
                    $seeds = [
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'Academic Tutoring',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'ACT Prep',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'SAT Last Minute',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'SAT Last Minute Subject',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'SAT Prep',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'SAT Subject',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                        [
                            'main_prog_id' => $mainProg->id,
                            'sub_prog_name' => 'Subject Tutoring',
                            'sub_prog_status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ],
                    ];
                    break;
            }

            SubProg::insert($seeds);

        }
    }
}
