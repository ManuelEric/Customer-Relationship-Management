<?php

namespace App\Jobs\Client;

use App\Http\Traits\GetGradeAndGraduationYear;
use App\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProcessUpdateGradeAndGraduationYearNow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;
    use GetGradeAndGraduationYear;

    protected ClientRepositoryInterface $clientRepository;
    protected $client_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */


    public function __construct($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientRepositoryInterface $clientRepository)
    {
        DB::beginTransaction();
        try {

            $client = $clientRepository->getClientById($this->client_id);

            $grade_now = $graduation_year_now = null;

            if($client->st_grade != null){
                if($client->graduation_year == null){
                    $grade_now = $client->st_grade;
                }else{
                    $grade_now = $this->getRealGrade(date('Y'), Carbon::parse($client->created_at)->format('Y'), date('m'), Carbon::parse($client->created_at)->format('m'), $client->st_grade);
                }
                $graduation_year_now = $this->getGraduationYearNow($grade_now);
            }else{
                $grade_now = $this->getGradeByGraduationYear($client->graduation_year);
                if($client->graduation_year != null){
                    $graduation_year_now = $client->graduation_year;
                }else{
                    $graduation_year_now = $this->getGraduationYearNow($grade_now);
                }
            }

            $client->st_grade == null && $client->graduation_year == null ? $grade_now = $graduation_year_now = null : null;

            # Update grade now and graduation year now to tbl_client
            $clientRepository->updateClientWithTrashed($client->id, ['grade_now' => $grade_now, 'graduation_year_now' => $graduation_year_now, 'updated_at' => $client->updated_at]);


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update grade and graduation now : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        Log::notice('Successfully update grade and graduation now  : ', $client->toArray());
    }
}
