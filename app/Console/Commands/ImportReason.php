<?php

namespace App\Console\Commands;

use App\Interfaces\ReasonRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportReason extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:reason';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import reason data from big data v1 into big data v2';

    protected ReasonRepositoryInterface $reasonRepository;

    public function __construct(ReasonRepositoryInterface $reasonRepository)
    {
        parent::__construct();

        $this->reasonRepository = $reasonRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reasons = $this->reasonRepository->getAllReasonFromCRM();
        $reasonDetails = [];
        foreach ($reasons as $reason) {

            if (!$this->reasonRepository->getReasonByName($reason->reason_name) && $reason->reason_name != "" && $reason->reason_name != NULL) {

                $reasonDetails[] = [
                    'reason_id' => $reason->reason_id,
                    'reason_name' => $reason->reason_name,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        $this->reasonRepository->createReasons($reasonDetails);
        if (count($reasonDetails) > 0) {
        }
        return Command::SUCCESS;
    }
}
