<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        # acces per department
        $client_management_access = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,21,22,36,37];
        $finance_access = [1,3,5,9,10,25,26,27,28,29,30,31,32,33,34,35,39,40];

        $policy = [
            'copy' => 0,
            'export' => 0
        ];
        
        $departments = Department::all();
        foreach ($departments as $department)
        {
            $department_name = $department->dept_name;
            switch ($department_name) {

                case "Client Management":
                    $department->access_menus()->attach($client_management_access, $policy);
                    break;

                case "Finance & Operation":
                    $department->access_menus()->attach($finance_access, $policy);
                    break;

            }
        }
    }
}
