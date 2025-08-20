<?php

namespace Database\Seeders;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\MajorGroup;
use App\Models\University;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DummyClientAcceptanceSeeder extends Seeder
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {

            $client_acceptance_details = [];
            $graduated_mentees = $this->clientRepository->rnGetGraduatedMentees([]);
            foreach ($graduated_mentees as $mentee) {
                $client_acceptance = DB::table('tbl_client_acceptance')->where('client_id', $mentee['id'])->first();
                if (! $client_acceptance) {
                    // if ( !$client_acceptance || $client_acceptance->status != 'Final Decision' )
                    $major_group = MajorGroup::inRandomOrder()->first();
                    $category = ['Reach', 'Competitive', 'Safety'];
                    $status = ['Submitted', 'Waitlisted', 'Accepted', 'Denied', 'Deferred', 'Final Decision'];

                    $client_acceptance_details[] = [
                        'client_id' => $mentee['id'],
                        'univ_id' => University::inRandomOrder()->first()->univ_id,
                        'major_group_id' => $major_group->id,
                        'major_name' => $major_group->mg_name,
                        'major_id' => null,
                        'category' => $category[array_rand($category)],
                        'status' => $status[array_rand($status)],
                        'status' => 'Final Decision',
                        'requirement_link' => 'https://google.com',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            DB::table('tbl_client_acceptance')->insert($client_acceptance_details);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            DB::commit();
        }
    }
}
