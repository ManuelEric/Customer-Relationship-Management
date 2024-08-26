<?php

namespace App\Console\Commands;

use App\Interfaces\LeadRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateLeadColorCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:color_code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate color code to leads on the database v2';

    protected LeadRepositoryInterface $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        parent::__construct();

        $this->leadRepository = $leadRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // $progressBar = $this->output->createProgressBar();
        // $progressBar->start();

        // DB::beginTransaction();
        // try {

        $leads = $this->leadRepository->getAllLead();
        foreach ($leads as $lead) {
            // echo $lead->color_code;
            // exit;
            // if ($lead->color_code != NULL)
            //     continue;

            $leadId = $lead->lead_id;
            $leadDetails = [
                'color_code' => $this->generateColorCode(),
            ];
            // echo json_encode($leadDetails);
            // exit;

            $this->leadRepository->updateLead($leadId, $leadDetails);
            // $progressBar->advance();
        }
        //     $progressBar->finish();
        //     DB::commit();
        //     Log::info('Generate lead color code works fine');

        // } catch (Exception $e) {

        //     DB::rollBack();
        //     Log::warning('Failed to generate lead color code : '. $e->getMessage() .' | Line : '. $e->getLine());

        // }


        return Command::SUCCESS;
    }

    private function generateColorCode()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
