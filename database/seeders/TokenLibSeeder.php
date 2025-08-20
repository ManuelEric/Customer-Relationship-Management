<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TokenLibSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\TokenLib::factory(3)->create();
    }
}
