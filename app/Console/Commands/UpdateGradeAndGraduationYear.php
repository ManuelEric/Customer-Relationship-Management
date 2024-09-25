<?php

namespace App\Console\Commands;

use App\Http\Traits\GetGradeAndGraduationYear;
use App\Repositories\ClientRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateGradeAndGraduationYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:grade_and_graduation_year';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update grade and graduation year student every year';

    private ClientRepository $clientRepository;
    use GetGradeAndGraduationYear;

    public function __construct(ClientRepository $clientRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        # info if the cron working or not
        Log::info('Cron Update grade and graduation year works fine.');


        DB::beginTransaction();
        try {

            $clients = $this->clientRepository->getAllClientByRole('Student');
            $progressBar = $this->output->createProgressBar($clients->count());
            $progressBar->start();

            foreach($clients as $client){
                $gradeNow = $graduationYearNow = null;

                if($client->st_grade != null){
                    if($client->graduation_year == null){
                        $gradeNow = $client->st_grade;
                    }else{
                        $gradeNow = $this->getRealGrade(date('Y'), Carbon::parse($client->created_at)->format('Y'), date('m'), Carbon::parse($client->created_at)->format('m'), $client->st_grade);
                    }
                    $graduationYearNow = $this->getGraduationYearNow($gradeNow);
                }else{
                    $gradeNow = $this->getGradeByGraduationYear($client->graduation_year);
                    if($client->graduation_year != null){
                        $graduationYearNow = $client->graduation_year;
                    }else{
                        $graduationYearNow = $this->getGraduationYearNow($gradeNow);
                    }
                }

                $client->st_grade == null && $client->graduation_year == null ? $gradeNow = $graduationYearNow = null : null;
            
                # Update grade now and graduation year now to tbl_client
                $this->clientRepository->updateClient($client->id, ['grade_now' => $gradeNow, 'graduation_year_now' => $graduationYearNow, 'updated_at' => $client->updated_at]);

                $progressBar->advance();
            }

            DB::commit();
            $progressBar->finish();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::info('Cron Update grade and graduation year not working normal. Error : '. $e->getMessage() .' | Line '. $e->getLine());

        }

        return Command::SUCCESS;
    }
}

