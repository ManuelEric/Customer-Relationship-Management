<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MajorGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $major_groups = [
            'Business Administration',
            'Business Management',
            'Marketing',
            'Finance',
            'Economics',
            'Entrepreneurship',
            'International Business',
            'Commerce',
            'Management and Marketing',
            'Business Analytics',
            'Social Sciences',
            'Public Policy',
            'Public Administration',
            'Political Science',
            'Law',
            'Psychology',
            'Communications',
            'International Relations',
            'Graphic Design',
            'Music Production',
            'Film and Television',
            'Illustration',
            'Fine Arts',
            'Philosophy',
            'English Literature',
            'Education',
            'Mechanical Engineering',
            'Industrial Engineering',
            'Electrical Engineering',
            'Civil Engineering',
            'Computer Engineering',
            'Chemical Engineering',
            'Material Science Engineering',
            'Aerospace Engineering',
            'Computer Science',
            'Information Systems',
            'Data Science',
            'Software Engineering',
            'Cybersecurity',
            'Physics',
            'Chemistry',
            'Mathematics',
            'Statistics',
            'Science',
            'Environmental Science',
            'Biological Sciences/Biology',
            'Biotechnology',
            'Biomedical Engineering',
            'Biomedical Science',
            'Neuroscience',
            'Molecular and Cell Biology',
            'Public Health',
            'Health and Human Sciences',
            'Veterinary Medicine',
            'Food Science'
        ];

        foreach ($major_groups as $key => $value)
        {
            $seeds[] = [
                'mg_name' => $value,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        DB::table('major_groups')->insert($seeds);
    }
}
