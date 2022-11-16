<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::truncate();

        $seeds = [
            [
                'dept_name' => 'Client Management',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'dept_name' => 'Business Development',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'dept_name' => 'Finance & Operation',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'dept_name' => 'Product Development',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'dept_name' => 'HR',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dept_name' => 'IT',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        Department::insert($seeds);
    }
}
