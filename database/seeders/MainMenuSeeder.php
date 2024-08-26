<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
                'mainmenu_name' => 'Master',
                'order_no' => 1,
                'icon' => 'bi bi-bookmark',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'mainmenu_name' => 'Client',
                'order_no' => 2,
                'icon' => 'bi bi-people-fill',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'mainmenu_name' => 'Instance',
                'order_no' => 3,
                'icon' => 'bi bi-building',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'mainmenu_name' => 'Program',
                'order_no' => 4,
                'icon' => 'bi bi-calendar2-event',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'mainmenu_name' => 'Invoice',
                'order_no' => 5,
                'icon' => 'bi bi-receipt',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'mainmenu_name' => 'Receipt',
                'order_no' => 6,
                'icon' => 'bi bi-receipt-cutoff',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'mainmenu_name' => 'Users',
                'order_no' => 7,
                'icon' => 'bi bi-person-workspace',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'mainmenu_name' => 'Report',
                'order_no' => 8,
                'icon' => 'bi bi-printer',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'mainmenu_name' => 'Recycle Bin',
                'order_no' => 9,
                'icon' => 'bi bi-trash',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        DB::table('tbl_main_menus')->insert($seeds);
    }
}
