<?php

namespace App\Console\Commands\Temp;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\ClientEvent;
use App\Models\ClientProgram;
use App\Models\UserClient;
use App\Models\Unclean\ClientProgram as ClientProgramUnclean;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixProblemDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:problem_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically fix problem date 0000-00-00 afte cleaning data';


    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        parent::__construct();
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function handle()
    {
        $problem_clientprograms = ClientProgram::where('prog_end_date', '0000-00-00')->get();
        $progress_bar = $this->output->createProgressBar($problem_clientprograms->count());
        $progress_bar->start();

        DB::beginTransaction();
        try {

            foreach ($problem_clientprograms as $problem_clientprogram) {
                $old_clientprogram = ClientProgramUnclean::where('clientprog_id', $problem_clientprogram->clientprog_id)->first();

                ClientProgram::where('clientprog_id', $problem_clientprogram->clientprog_id)->first()->update(['prog_end_date' => $old_clientprogram->prog_end_date]);
            }

            DB::commit();
            $progress_bar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to fix problem date : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }

}
