<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetRegisterAsIfNull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:register_by';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically set register by to client table if the register by field is null';


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
        $students = $this->clientRepository->getAllClientByRole('Student');
        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($students as $student) {
                $progressBar->advance();
                if ($student->register_by == NULL) {

                    $this->clientRepository->updateClient($student->id, ['register_by' => 'student']);
                    // $student->save();
                }
                $progressBar->advance();
            }
            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to set register_by : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}
