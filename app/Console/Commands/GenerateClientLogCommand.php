<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateClientLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:client-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generating a record for each client';

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        $skipped_users = \App\Models\UserClient::withTrashed()->whereNull('category')->where('is_verified', 'Y')->pluck('id')->toArray();

        $user_clients = \App\Models\UserClient::with([
            'clientProgram' => function ($query) {
                $query->select('clientprog_id', 'client_id', 'updated_at')->orderBy('updated_at', 'DESC')->limit(1);
            }
        ])->withTrashed()->whereNotIn('id', $skipped_users)->get();

        $mapped_user_clients = $user_clients->map(function ($value) {

            $category = $value->category == NULL && $value->is_verified == 'N' ? 'raw' : $value->category;
            [$created_at, $updated_at] = $this->getDateBasedOnTypeClient($category, $value);

            return [
                'client_id' => $value->id,
                'first_name' => $value->first_name,
                'last_name' => $value->last_name,
                'category' => $category,
                'lead_source' => $value->lead_id,
                'inputted_from' => 'manual',
                'unique_key' => \Illuminate\Support\Str::ulid(),
                'clientprog_id' => $this->getClientProg($value),
                'created_at' => $created_at,
                'updated_at' => $updated_at
            ];
        });

        \App\Models\ClientLog::insert($mapped_user_clients->toArray());
        return Command::SUCCESS;
    }

    private function getDateBasedOnTypeClient(String $category, \App\Models\UserClient $user_client)
    {
        # kalau raw dari updated_at dari tbl_client
        # kalau new leads dari updated_at dari tbl_client
        # kalau potential dari penawaran program latest created_at dari tbl_client_program 
        # kalau mentee dari mentee berdasarkan latest program (program admission) dan dari success_date tbl_client_prog
        # kalau non-mentee dari mentee berdasarkan latest program dan dari success_date tbl_client_prog
        # kalau alumni mentee dari updated_at dari tbl_client_prog yg programnya admission
        # kalau alumni nonmentee dari updated_at dari tbl_client_prog yg programnya non-admission
        # kalau trash pakai deleted_at dari tbl_client 
        switch ($category)
        {
            case "raw":
                $created_at = $updated_at = $user_client->updated_at;
                break;

            case "new-lead":
                $created_at = $updated_at = $user_client->updated_at;
                break;

            case "potential":
                # get the latest client program of his/her by status 0
                //! there is case when latestOfferedProgram is null resulting fetching created_at goes error
                //! in this case, we need to put extra condition 
                $created_at = $updated_at = $user_client->latestOfferedProgram->created_at ?? null;
                break;

            case "mentee":
                $created_at = $updated_at = $user_client->latestAdmissionProgram->success_date ?? $user_client->latestAdmissionProgram->updated_at;
                break;

            case "non-mentee":
                $created_at = $updated_at = $user_client->latestNonAdmissionProgram->success_date ?? $user_client->latestAdmissionProgram->updated_at;
                break;

            case "alumni-mentee":
                $created_at = $updated_at = $user_client->latestAdmissionProgram->updated_at;
                break;

            case "alumni-non-mentee":
                $created_at = $updated_at = $user_client->latestNonAdmissionProgram->updated_at;
                break;

            case "trash":
                $created_at = $updated_at = $user_client->deleted_at;
                break;
        }

        return [$created_at, $updated_at];
    }

    private function getClientProg(\App\Models\UserClient $client)
    {
        if ( $client->category != 'potential' && $client->category != 'mentee' && $client->category != 'non-mentee' )
            return null;

        return $client->clientProgram[0]->clientprog_id;
    }
}
