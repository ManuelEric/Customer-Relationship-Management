<?php

namespace Database\Seeders;

use App\Models\Stream;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class StreamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $streams = ['Engineering', 'Computer Science', 'Life Sciences', 'Healthcare', 'Business & Economics', 'Art & Design'];
        foreach ($streams as $key => $value) {
            $seeds[] = [
                'stream_name' => $value,
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        Stream::insert($seeds);
    }
}
