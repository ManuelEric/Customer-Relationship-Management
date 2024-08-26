<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovePicClient extends Command
{    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:pic_client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move PIC client from tbl client to tbl pic client';


    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
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
        $details = [];
        $students = $this->clientRepository->getAllClientByRole('Student');
        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($students as $student) {
                $progressBar->advance();

                if($student->pic != null)
                {
                    $details[] = [
                        'client_id' => $student->id,
                        'user_id' => $student->pic,
                        'created_at' => $student->updated_at,
                        'updated_at' => $student->updated_at
                    ];
                }
               
                $progressBar->advance();
            }
            
            if(count($details) > 0){
                $this->clientRepository->insertPicClient($details);
            }
            
            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            echo $e->getMessage();
            DB::rollBack();
            Log::debug('Failed to move pic client : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}