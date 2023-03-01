<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeds = [
            [
                'menu_name' => 'Master',
                'order_no' => 1,
                'icon' => 'bi bi-bookmark',
            ],
            [
                'menu_name' => 'Client',
                'order_no' => 2,
                'icon' => 'bi bi-people-fill',
            ],
            [
                'menu_name' => 'Instance',
                'order_no' => 3,
                'icon' => 'bi bi-building',
            ],
            [
                'menu_name' => 'Program',
                'order_no' => 4,
                'icon' => 'bi bi-calendar2-event',
            ],
            [
                'menu_name' => 'Invoice',
                'order_no' => 5,
                'icon' => 'bi bi-receipt',
            ],
            [
                'menu_name' => 'Receipt',
                'order_no' => 6,
                'icon' => 'bi bi-receipt-cutoff',
            ],
            [
                'menu_name' => 'Users',
                'order_no' => 7,
                'icon' => 'bi bi-person-workspace',
            ],
            [
                'menu_name' => 'Report',
                'order_no' => 8,
                'icon' => 'bi bi-printer',
            ]
        ];

        DB::table('tbl_main_menu')->insert($seeds);
    }
}
