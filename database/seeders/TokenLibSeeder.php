<?php

namespace Database\Seeders;

use Database\Factories\TokenLibFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        \App\Models\TokenLib::factory(1)->create();
    }
}
