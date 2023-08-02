<?php

namespace App\Console\Commands;

use App\Interfaces\LeadTargetRepositoryInterface;
use Illuminate\Console\Command;

class UpdateTargetTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:target_tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update data target tracking for this month.';

    private LeadTargetRepositoryInterface $leadTargetRepository;

    public function __construct(LeadTargetRepositoryInterface $leadTargetRepository)
    {
        parent::__construct();
        $this->leadTargetRepository = $leadTargetRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $current_month = date('m');
        
        $achievedLead_thisMonth = $this->leadTargetRepository->getAchievedLeadSalesByMonth($current_month);
        
        $this->info(json_encode($achievedLead_thisMonth));

        return Command::SUCCESS;
    }
}
