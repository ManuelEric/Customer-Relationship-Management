<?php

namespace Database\Seeders\HotLeadsAutomation;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProgramLeadLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeds = [

            // Program | Admission | School | International
            [
                'programbucket_id' => 'B-1',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | School | National +
            [
                'programbucket_id' => 'B-1',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | School | Homeschool
            [
                'programbucket_id' => 'B-1',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | School | Homeschool $
            [
                'programbucket_id' => 'B-1',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | School | National - Private
            [
                'programbucket_id' => 'B-1',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | School | National - Private $
            [
                'programbucket_id' => 'B-1',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | School | National - Negeri
            [
                'programbucket_id' => 'B-1',
                'leadbucket_id' => null,
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | School | National - Negeri $
            [
                'programbucket_id' => 'B-1',
                'leadbucket_id' => null,
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Grade | 0
            [
                'programbucket_id' => 'B-2',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Grade | -1
            [
                'programbucket_id' => 'B-2',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Grade | -2
            [
                'programbucket_id' => 'B-2',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Grade | -3
            [
                'programbucket_id' => 'B-2',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Grade | -4
            [
                'programbucket_id' => 'B-2',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Grade | -5
            [
                'programbucket_id' => 'B-2',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | US
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | UK
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | Canada
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | Australia
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | Hongkong
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | Singapore
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | Other
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 7,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | Undecided
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Admission | Country | Undecided $
            [
                'programbucket_id' => 'B-3',
                'leadbucket_id' => null,
                'value_category' => 9,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | School | International
            [
                'programbucket_id' => 'B-6',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | School | National +
            [
                'programbucket_id' => 'B-6',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | School | Homeschool
            [
                'programbucket_id' => 'B-6',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | School | Homeschool $
            [
                'programbucket_id' => 'B-6',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | School | National - Private
            [
                'programbucket_id' => 'B-6',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | School | National - Private $
            [
                'programbucket_id' => 'B-6',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | School | National - Negeri
            [
                'programbucket_id' => 'B-6',
                'leadbucket_id' => null,
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | School | National - Negeri $
            [
                'programbucket_id' => 'B-6',
                'leadbucket_id' => null,
                'value_category' => 8,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Grade | 0
            [
                'programbucket_id' => 'B-7',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Grade | -1
            [
                'programbucket_id' => 'B-7',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Grade | -2
            [
                'programbucket_id' => 'B-7',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Grade | -3
            [
                'programbucket_id' => 'B-7',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Grade | -4
            [
                'programbucket_id' => 'B-7',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Grade | -5
            [
                'programbucket_id' => 'B-7',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | US
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | UK
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | Canada
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | Australia
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | Hongkong
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | Singapore
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | Other
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | Undecided
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Experiential Learning | Country | Undecided $
            [
                'programbucket_id' => 'B-8',
                'leadbucket_id' => null,
                'value_category' => 9,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | School | International
            [
                'programbucket_id' => 'B-12',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | School | National +
            [
                'programbucket_id' => 'B-12',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | School | Homeschool
            [
                'programbucket_id' => 'B-12',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | School | Homeschool $
            [
                'programbucket_id' => 'B-12',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | School | National - Private
            [
                'programbucket_id' => 'B-12',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | School | National - Private $
            [
                'programbucket_id' => 'B-12',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | School | National - Negeri
            [
                'programbucket_id' => 'B-12',
                'leadbucket_id' => null,
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | School | National - Negeri $
            [
                'programbucket_id' => 'B-12',
                'leadbucket_id' => null,
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Grade | 0
            [
                'programbucket_id' => 'B-13',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Grade | -1
            [
                'programbucket_id' => 'B-13',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Grade | -2
            [
                'programbucket_id' => 'B-13',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Grade | -3
            [
                'programbucket_id' => 'B-13',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Grade | -4
            [
                'programbucket_id' => 'B-13',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Grade | -5
            [
                'programbucket_id' => 'B-13',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | US
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | UK
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | Canada
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | Australia
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | Hongkong
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | Singapore
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | Other
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 7,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | Undecided
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (SAT) | Country | Undecided $
            [
                'programbucket_id' => 'B-14',
                'leadbucket_id' => null,
                'value_category' => 9,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | School | International
            [
                'programbucket_id' => 'B-18',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | School | National +
            [
                'programbucket_id' => 'B-18',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | School | Homeschool
            [
                'programbucket_id' => 'B-18',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | School | Homeschool $
            [
                'programbucket_id' => 'B-18',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | School | National - Private
            [
                'programbucket_id' => 'B-18',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | School | National - Private $
            [
                'programbucket_id' => 'B-18',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | School | National - Negeri
            [
                'programbucket_id' => 'B-18',
                'leadbucket_id' => null,
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | School | National - Negeri $
            [
                'programbucket_id' => 'B-18',
                'leadbucket_id' => null,
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Grade | 0
            [
                'programbucket_id' => 'B-19',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Grade | -1
            [
                'programbucket_id' => 'B-19',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Grade | -2
            [
                'programbucket_id' => 'B-19',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Grade | -3
            [
                'programbucket_id' => 'B-19',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Grade | -4
            [
                'programbucket_id' => 'B-19',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Grade | -5
            [
                'programbucket_id' => 'B-19',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | US
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | UK
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | Canada
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | Australia
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | Hongkong
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | Singapore
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | Other
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 7,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | Undecided
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Program | Academic Performance (Academic Tutoring) | Country | Undecided $
            [
                'programbucket_id' => 'B-20',
                'leadbucket_id' => null,
                'value_category' => 9,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | School | International
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-1',
                'value_category' => 1,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | School | National +
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-1',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | School | Homeschool
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-1',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | School | Homeschool $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-1',
                'value_category' => 4,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | School | National - Private
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-1',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | School | National - Private $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-1',
                'value_category' => 6,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | School | National - Negeri
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-1',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | School | National - Negeri $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-1',
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Grade | 0
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-2',
                'value_category' => 1,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Grade | -1
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-2',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Grade | -2
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-2',
                'value_category' => 3,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Grade | -3
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-2',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Grade | -4
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-2',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Grade | -5
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-2',
                'value_category' => 6,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | US
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | UK
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | Canada
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 3,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | Australia
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 4,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | Hongkong
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 5,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | Singapore
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 6,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | Other
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 7,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | Undecided
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 8,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Country | Undecided $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-3',
                'value_category' => 9,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Status | Student
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-4',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 0,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Admission | Status | Parent
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-4',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 0,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | School | International
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-5',
                'value_category' => 1,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | School | National +
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-5',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | School | Homeschool
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-5',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | School | Homeschool $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-5',
                'value_category' => 4,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | School | National - Private
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-5',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | School | National - Private $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-5',
                'value_category' => 6,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | School | National - Negeri
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-5',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | School | National - Negeri $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-5',
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Grade | 0
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-6',
                'value_category' => 1,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Grade | -1
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-6',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Grade | -2
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-6',
                'value_category' => 3,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Grade | -3
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-6',
                'value_category' => 4,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Grade | -4
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-6',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Grade | -5
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-6',
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Status | Student
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-7',
                'value_category' => 1,
                'new' => 1,
                'existing_mentee' => 2,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Status | Parent
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-7',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Major | Decided
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-8',
                'value_category' => 1,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Experiential Learning | Major | Undecided
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-8',
                'value_category' => 2,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | School | International
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-9',
                'value_category' => 1,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | School | National +
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-9',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | School | Homeschool
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-9',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | School | Homeschool $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-9',
                'value_category' => 4,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | School | National - Private
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-9',
                'value_category' => 5,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | School | National - Private $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-9',
                'value_category' => 6,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | School | National - Negeri
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-9',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | School | National - Negeri $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-9',
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | Grade | 0
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-10',
                'value_category' => 1,
                'new' => 2,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | Grade | -1
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-10',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | Grade | -2
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-10',
                'value_category' => 3,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | Grade | -3
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-10',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | Grade | -4
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-10',
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (SAT) | Grade | -5
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-10',
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | School | International
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-11',
                'value_category' => 1,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | School | National +
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-11',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | School | Homeschool
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-11',
                'value_category' => 3,
                'new' => 1,
                'existing_mentee' => 2,
                'existing_non_mentee' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | School | Homeschool $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-11',
                'value_category' => 4,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | School | National - Private
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-11',
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | School | National - Private $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-11',
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | School | National - Negeri
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-11',
                'value_category' => 7,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | School | National - Negeri $
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-11',
                'value_category' => 8,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | Grade | 0
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-12',
                'value_category' => 1,
                'new' => 2,
                'existing_mentee' => 1,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | Grade | -1
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-12',
                'value_category' => 2,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | Grade | -2
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-12',
                'value_category' => 3,
                'new' => 2,
                'existing_mentee' => 2,
                'existing_non_mentee' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | Grade | -3
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-12',
                'value_category' => 4,
                'new' => 1,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | Grade | -4
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-12',
                'value_category' => 5,
                'new' => 0,
                'existing_mentee' => 1,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            // Lead | Academic Performance (Academic Tutoring) | Grade | -5
            [
                'programbucket_id' => null,
                'leadbucket_id' => 'L-12',
                'value_category' => 6,
                'new' => 0,
                'existing_mentee' => 0,
                'existing_non_mentee' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],



        ];

        DB::table('tbl_program_lead_library')->insert($seeds);
    }
}
