<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ProgramPhaseRepositoryInterface;
use App\Models\pivot\ClientProgramDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddedPackageBoughtForAdmissionMentee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:package-bought';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will generate a package for everyone that joined the admission program';


    protected ClientRepositoryInterface $clientRepository;
    protected ProgramPhaseRepositoryInterface $programPhaseRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        ProgramPhaseRepositoryInterface $programPhaseRepository
        )
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
        $this->programPhaseRepository = $programPhaseRepository;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $packages = $this->programPhaseRepository->rnGetPhaseDetails();

        /* get active mentees should be temporarily update by removing the getMentoredStudents to select all active mentees in database */
        $active_mentees = $this->clientRepository->rnGetActiveMentees([]);
        $bar = $this->output->createProgressBar($active_mentees->count());

        DB::beginTransaction();
        foreach ( $active_mentees as $mentee )
        {
            $full_name = $mentee->full_name;
            $grade = $mentee->grade_now;
            
            foreach ($mentee->clientProgram as $client_program)
            {
                $clientprog_id = $client_program->clientprog_id;
                $client_program_details = [];
                foreach ( $packages as $package )
                {
                    // check if exists
                    if (ClientProgramDetail::where('clientprog_id', $clientprog_id)->where('phase_detail_id', $package->id)->exists())
                        continue;

                    $client_program_details[] = [
                        'clientprog_id' => $clientprog_id,
                        'phase_detail_id' => $package->id,
                        'phase_lib_id' => NULL, //! change this into phase_lib_id whenever team sales want to track how many client goes to US, UK, etc.
                        'quota' => 1,
                        'use' => 0,
                        'grade' => $grade,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
            $bar->advance();

            try {

                $this->programPhaseRepository->rnStoreBulkProgramPhase($client_program_details);
                $this->newLine();
                $this->info('Client program details stored successfully for ' . $full_name . ' with clientprog_id: ' . $clientprog_id);
                DB::commit();
            } catch (\Exception $err) {
                DB::rollBack();
                $this->error('Failed to store client program details ' . $err->getMessage());
                break;
            }
        }

        $this->info('Process done.');
        $bar->finish();
    }
}
