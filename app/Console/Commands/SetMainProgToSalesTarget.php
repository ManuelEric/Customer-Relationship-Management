<?php

namespace App\Console\Commands;

use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetMainProgToSalesTarget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:main_prog_to_sales_target';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically set main prog to sales target if the main prog field is null';


    private SalesTargetRepositoryInterface $salesTargetRepository;
    private ProgramRepositoryInterface $programRepository;

    public function __construct(SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository)
    {
        parent::__construct();
        $this->salesTargetRepository = $salesTargetRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $salesTarget = $this->salesTargetRepository->getAllSalesTarget();
        $progressBar = $this->output->createProgressBar($salesTarget->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($salesTarget as $st) {
                $progressBar->advance();
                
                if ($st->main_prog == NULL) {

                    $program = $this->programRepository->getProgramById($st->prog_id);
                    $this->salesTargetRepository->updateSalesTarget($st->id, ['main_prog_id' => $program->main_prog_id]);
                }
            }
            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to set main prog to sales target : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}
