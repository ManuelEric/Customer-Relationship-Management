<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExternalMentorDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {

            # create users
            $dummy_external_mentors = User::factory()->count(3)->create();
    
            foreach ($dummy_external_mentors as $dummy_mentor)
            {
                # attach to external-mentor roles
                $dummy_mentor->roles()->attach(20); #20: external-mentor
            }
            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();
            $this->command->outputComponents()->error($e->getMessage());

        }


    }
}
