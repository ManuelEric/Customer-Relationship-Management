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

            // Admission | School | International
            [
                'programbucket_id' => 'B-1',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | School | National +
            [
                'programbucket_id' => 'B-1',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | School | Homeschool
            [
                'programbucket_id' => 'B-1',
                'value_category' => 3,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | School | Homeschool $
            [
                'programbucket_id' => 'B-1',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | School | National - Private
            [
                'programbucket_id' => 'B-1',
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 1,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | School | National - Private $
            [
                'programbucket_id' => 'B-1',
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | School | National - Negeri
            [
                'programbucket_id' => 'B-1',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | School | National - Negeri $
            [
                'programbucket_id' => 'B-1',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Grade | 0
            [
                'programbucket_id' => 'B-2',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Grade | -1
            [
                'programbucket_id' => 'B-2',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Grade | -2
            [
                'programbucket_id' => 'B-2',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Grade | -3
            [
                'programbucket_id' => 'B-2',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 1,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Grade | -4
            [
                'programbucket_id' => 'B-2',
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Grade | -5
            [
                'programbucket_id' => 'B-2',
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | US
            [
                'programbucket_id' => 'B-3',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | UK
            [
                'programbucket_id' => 'B-3',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | Canada
            [
                'programbucket_id' => 'B-3',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | Australia
            [
                'programbucket_id' => 'B-3',
                'value_category' => 4,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | Hongkong
            [
                'programbucket_id' => 'B-3',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | Singapore
            [
                'programbucket_id' => 'B-3',
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | Other
            [
                'programbucket_id' => 'B-3',
                'value_category' => 7,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | Undecided
            [
                'programbucket_id' => 'B-3',
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Admission | Country | Undecided $
            [
                'programbucket_id' => 'B-3',
                'value_category' => 9,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School | International
            [
                'programbucket_id' => 'B-6',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School | National +
            [
                'programbucket_id' => 'B-6',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School | Homeschool
            [
                'programbucket_id' => 'B-6',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School | Homeschool $
            [
                'programbucket_id' => 'B-6',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School | National - Private
            [
                'programbucket_id' => 'B-6',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School | National - Private $
            [
                'programbucket_id' => 'B-6',
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School | National - Negeri
            [
                'programbucket_id' => 'B-6',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | School | National - Negeri $
            [
                'programbucket_id' => 'B-6',
                'value_category' => 8,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade | 0
            [
                'programbucket_id' => 'B-7',
                'value_category' => 1,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade | -1
            [
                'programbucket_id' => 'B-7',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade | -2
            [
                'programbucket_id' => 'B-7',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade | -3
            [
                'programbucket_id' => 'B-7',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade | -4
            [
                'programbucket_id' => 'B-7',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Grade | -5
            [
                'programbucket_id' => 'B-7',
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | US
            [
                'programbucket_id' => 'B-8',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | UK
            [
                'programbucket_id' => 'B-8',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | Canada
            [
                'programbucket_id' => 'B-8',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | Australia
            [
                'programbucket_id' => 'B-8',
                'value_category' => 4,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | Hongkong
            [
                'programbucket_id' => 'B-8',
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | Singapore
            [
                'programbucket_id' => 'B-8',
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | Other
            [
                'programbucket_id' => 'B-8',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | Undecided
            [
                'programbucket_id' => 'B-8',
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Experiential Learning | Country | Undecided $
            [
                'programbucket_id' => 'B-8',
                'value_category' => 9,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School | International
            [
                'programbucket_id' => 'B-12',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School | National +
            [
                'programbucket_id' => 'B-12',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School | Homeschool
            [
                'programbucket_id' => 'B-12',
                'value_category' => 3,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School | Homeschool $
            [
                'programbucket_id' => 'B-12',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School | National - Private
            [
                'programbucket_id' => 'B-12',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School | National - Private $
            [
                'programbucket_id' => 'B-12',
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School | National - Negeri
            [
                'programbucket_id' => 'B-12',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | School | National - Negeri $
            [
                'programbucket_id' => 'B-12',
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade | 0
            [
                'programbucket_id' => 'B-13',
                'value_category' => 1,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 2,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade | -1
            [
                'programbucket_id' => 'B-13',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade | -2
            [
                'programbucket_id' => 'B-13',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade | -3
            [
                'programbucket_id' => 'B-13',
                'value_category' => 4,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade | -4
            [
                'programbucket_id' => 'B-13',
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Grade | -5
            [
                'programbucket_id' => 'B-13',
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | US
            [
                'programbucket_id' => 'B-14',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | UK
            [
                'programbucket_id' => 'B-14',
                'value_category' => 2,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | Canada
            [
                'programbucket_id' => 'B-14',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | Australia
            [
                'programbucket_id' => 'B-14',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | Hongkong
            [
                'programbucket_id' => 'B-14',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | Singapore
            [
                'programbucket_id' => 'B-14',
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | Other
            [
                'programbucket_id' => 'B-14',
                'value_category' => 7,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 1,
                'existing_mentee_temp' => 1,
                'existing_non_mentee_temp' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | Undecided
            [
                'programbucket_id' => 'B-14',
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'new_temp' => 0,
                'existing_mentee_temp' => 0,
                'existing_non_mentee_temp' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Academic Performance (SAT) | Country | Undecided $
            [
                'programbucket_id' => 'B-14',
                'value_category' => 9,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'new_temp' => 2,
                'existing_mentee_temp' => 2,
                'existing_non_mentee_temp' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],



        ];

        DB::table('tbl_program_buckets_params')->insert($seeds);
    }
}
