<?php

namespace App\Console\Commands;

use App\Models\ClientProgram;
use App\Models\Program;
use App\Repositories\ClientEventRepository;
use App\Repositories\ClientProgramRepository;
use App\Repositories\ClientRepository;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateTypeClientMentor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:type_client_mentor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update value type client mentor';

    private ClientProgramRepository $clientProgramRepository;

    public function __construct(ClientProgramRepository $clientProgramRepository)
    {
        parent::__construct();
        $this->clientProgramRepository = $clientProgramRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        # info if the cron working or not
        Log::info('Cron Update Client mentor works fine.');


        DB::beginTransaction();
        try {
            
            $clientPrograms = ClientProgram::all();

            $admission_prog_list = Program::whereHas('main_prog', function ($query) {
                $query->where('prog_name', 'Admissions Mentoring');
            })->orWhereHas('sub_prog', function ($query) {
                $query->where('sub_prog_name', 'Admissions Mentoring');
            })->pluck('prog_id')->toArray();

            $satact_prog_list = Program::whereHas('sub_prog', function ($query) {
                $query->where('sub_prog_name', 'like', '%SAT%')->orWhere('sub_prog_name', 'like', '%ACT%');
            })->pluck('prog_id')->toArray();

            foreach ($clientPrograms as $clientProg) {
                $this->info('=====================');
                $this->info('clientprog_id: '. $clientProg->clientprog_id);
                
       

                if($clientProg->clientMentor()->count() > 0){
                    $clientMentors = $clientProg->clientMentor()->orderBy('tbl_client_mentor.id', 'desc')->get();

                   if(in_array($clientProg->prog_id, $admission_prog_list)){ # Mentor
                        if($clientMentors->count() > 1){
                            $clientProg->clientMentor()->updateExistingPivot($clientMentors->first()->id, ['type' => 1]); # Supervising mentor / main mentor
                            $this->info('Main Mentor:' . $clientMentors->first()->id);
                            $clientProg->clientMentor()->updateExistingPivot($clientMentors->last()->id, ['type' => 2]); # profile building mentor / backup mentor
                            $this->info('Backup Mentor:' . $clientMentors->last()->id);
                            
                        }else{
                            $clientProg->clientMentor()->updateExistingPivot($clientMentors->first()->id, ['type' => 1]); # Supervising mentor / main mentor
                            $this->info('Main Mentor:' . $clientMentors->first()->id);
                        }

                   }

                   if(in_array($clientProg->prog_id, $satact_prog_list)){ # Tutor
                        if($clientMentors->count() > 1){
                            $clientProg->clientMentor()->updateExistingPivot($clientMentors->first()->id, ['type' => 5]); # Tutor / tutor 1
                            $this->info('Main Tutor:' . $clientMentors->first()->id);
                            $clientProg->clientMentor()->updateExistingPivot($clientMentors->last()->id, ['type' => 5]); # Tutor / tutor 2
                            $this->info('backup Tutor:' . $clientMentors->last()->id);
                        }else{
                            $clientProg->clientMentor()->updateExistingPivot($clientMentors->first()->id, ['type' => 5]); # Tutor / tutor 1
                            $this->info('main Tutor:' . $clientMentors->first()->id);
                        }
                   }
                }
            }
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::info('Cron Insert target tracking not working normal. Error : '. $e->getMessage() .' | Line '. $e->getCode());

        }

        return Command::SUCCESS;
    }
}

