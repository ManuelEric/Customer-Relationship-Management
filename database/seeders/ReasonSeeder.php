<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ReasonSeeder extends Seeder
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
                'reason_name' => 'Ended by system for the reason that clients has been graduated',
                'type' => 'Program',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        Reason::insert($seeds);
    }
}
