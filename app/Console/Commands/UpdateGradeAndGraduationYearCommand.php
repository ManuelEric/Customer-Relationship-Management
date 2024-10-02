<?php

namespace App\Console\Commands;

use App\Http\Traits\GetGradeAndGraduationYear;
use App\Repositories\ClientRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateGradeAndGraduationYearCommand extends Command
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

    # Purpose:
    # Update grade and graduation year student every year
    public function handle()
    {
        # info if the cron working or not
        Log::info('Cron Update grade and graduation year works fine.');


        DB::beginTransaction();
        try {

            $clients = $this->clientRepository->getAllClientByRole('Student');
            $progress_bar = $this->output->createProgressBar($clients->count());
            $progress_bar->start();

            foreach($clients as $client){
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
                $this->clientRepository->updateClient($client->id, ['grade_now' => $grade_now, 'graduation_year_now' => $graduation_year_now, 'updated_at' => $client->updated_at]);

                $progress_bar->advance();
            }

            DB::commit();
            $progress_bar->finish();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::info('Cron Update grade and graduation year not working normal. Error : '. $e->getMessage() .' | Line '. $e->getLine());

        }

        return Command::SUCCESS;
    }
}

