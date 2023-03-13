<?php

namespace App\Console\Commands;

use App\Interfaces\ProgramRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportProg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import program data from crm big data';

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
        $programDetails = $this->programRepository->getProgramFromV1();
        
        DB::beginTransaction();
        try {

            foreach ($programDetails as $detail)
            {
    
                # if prog_id does not exists on tbl_prog v2
                if (!$this->programRepository->getProgramById($detail['prog_id'])) {
    
                    # insert into program v2
                    $this->programRepository->createProgramFromV1($detail);
    
                }
    
            }
            DB::commit();
            Log::info('Import Program works fine');

        } catch (Exception $e) {
         
            DB::rollBack();
            Log::warning('There\'s something wrong with import program : '.$e->getMessage());

        }

        return Command::SUCCESS;
    }
}
