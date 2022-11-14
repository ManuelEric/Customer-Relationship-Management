<?php

namespace App\Console\Commands;

use App\Interfaces\ProgramRepositoryInterface;
use App\Models\Program;
use Illuminate\Console\Command;

class CleaningProg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleaning:prog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Input main_prog_id & sub_prog_id depends on prog_main & prog_sub';

    protected ProgramRepositoryInterface $programRepository;

    public function __construct(ProgramRepositoryInterface $programRepository)
    {
        parent::__construct();

        $this->programRepository = $programRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->programRepository->cleaningProgram();
        return Command::SUCCESS;
    }
}
