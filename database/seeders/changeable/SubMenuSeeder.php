<?php

namespace Database\Seeders\changeable;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $new_seeds = [
            'submenu_link' => 'client/student?st=new-leads'
        ];

        DB::table('tbl_menus')->where('mainmenu_id', 2)->where('submenu_name', 'Students')->update($new_seeds);
    }
}
