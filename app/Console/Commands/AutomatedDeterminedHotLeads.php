<?php

namespace App\Console\Commands;

use App\Models\InitialProgram;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutomatedDeterminedHotLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automate:determine_hot_leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic gave suggestion program for the clients and gave status hot, warm, cold.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        # get raw data by the oldest client
        $rawData = DB::table('client_lead')->orderBy('id', 'asc')->get();

        $initialPrograms = InitialProgram::orderBy('id', 'asc')->get();
        foreach ($initialPrograms as $initialProgram) {
            $initProgramId = $initialProgram->id;

            $programBuckets = DB::table('tbl_program_buckets_params')->
                                    where('initialprogram_id', $initProgramId)->
                                    orderBy('id', 'asc')->get();

            foreach ($programBuckets as $programBucket) {



            }

        }

        return Command::SUCCESS;
    }
}
