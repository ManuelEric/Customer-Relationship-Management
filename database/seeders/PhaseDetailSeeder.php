<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PhaseDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = [
            # Interests & Career Exploration
            // [
            //     'phase_id' => 1,
            //     'phase_detail_name' => 'Test Minat Bakat',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 1,
            //     'phase_detail_name' => 'Guidance to choose and referral to externals (choose): Extracurricular, Clubs, Community Service, Online Course',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            [
                'phase_id' => 1,
                'phase_detail_name' => 'Hands-on Activities (Workshop / Site Visit)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 1,
                'phase_detail_name' => 'Talk to Professionals',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // [
            //     'phase_id' => 1,
            //     'phase_detail_name' => 'Guided Research & Reflection',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],

            # Profile Building
            // [
            //     'phase_id' => 2,
            //     'phase_detail_name' => 'CV Building & Writing (document, LinkedIn)',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 2,
            //     'phase_detail_name' => 'Academic Planning: Curriculum and Subject Selection, Standardized Test Selection',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 2,
            //     'phase_detail_name' => 'Activities Planning & Guidance to choose: Summer school abroad, Extracurricular, Clubs, Community Service, Online Course, etc',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            [
                'phase_id' => 2,
                'phase_detail_name' => 'Competition Mentoring',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // [
            //     'phase_id' => 2,
            //     'phase_detail_name' => 'Personal Project (choose): Research, Internship, Other Projects (podcast, web, campaign)',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            [
                'phase_id' => 2,
                'phase_detail_name' => 'Passion Project(15 hours)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 2,
                'phase_detail_name' => 'Research Project(10 hours)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 2,
                'phase_detail_name' => 'Community Service Trip (domestic - summer only by batch)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            # University Application Strategy
            // [
            //     'phase_id' => 3,
            //     'phase_detail_name' => 'University Research',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 3,
            //     'phase_detail_name' => 'University Shortlisting Strategy',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 3,
            //     'phase_detail_name' => 'Major Selection Strategy',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            [
                'phase_id' => 3,
                'phase_detail_name' => 'Complete Univ Essay Guidance & Editing (5/10 univs)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phase_id' => 3,
                'phase_detail_name' => 'Former Admission Officer Consultation (Add-on)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            # Writing
            // [
            //     'phase_id' => 4,
            //     'phase_detail_name' => 'Research Writing',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 4,
            //     'phase_detail_name' => 'Narrative Writing Practice',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            [
                'phase_id' => 4,
                'phase_detail_name' => 'Essay Bootcamp',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            # BONUS
            // [
            //     'phase_id' => 5,
            //     'phase_detail_name' => 'Interview Guidance',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 5,
            //     'phase_detail_name' => 'Student Starter Kit',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 5,
            //     'phase_detail_name' => 'Mentee Graduation Event',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 5,
            //     'phase_detail_name' => 'Subject Tutoring 10 hours',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 5,
            //     'phase_detail_name' => 'Competition Tutoring x hours',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 5,
            //     'phase_detail_name' => 'Emotional Resilience Class for Mentees',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'phase_id' => 5,
            //     'phase_detail_name' => 'First Air for Struggling Teens for Parents',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
        ];

        DB::table('phase_details')->insert($seeds);
    }
}
