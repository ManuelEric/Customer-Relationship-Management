<?php

namespace App\Console\Commands;

use App\Interfaces\LeadTargetRepositoryInterface;
use App\Models\LeadTargetTracking;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InsertTargetTracking_Monthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:target_tracking_monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert target lead for signal alarm every one month';

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
        # info if the cron working or not
        Log::info('Cron Insert target tracking monthly works fine.');

        $now = Carbon::now();

        DB::beginTransaction();
        try {

            # if this month data target has been stored into target tracking
            # then don't allow scheduler to continue the process

            if ($this->leadTargetRepository->findThisMonthTarget($now)->count() > 0) 
                return Command::SUCCESS;
    
            if (!$activeTarget = $this->leadTargetRepository->getThisMonthTarget())
                Log::error('Cron Insert target tracking monthly cannot be processed because there are no data in the target signal view.');
    
    
            foreach ($activeTarget as $target) {
    
                $targetTrackingDetails[] = [
                    'divisi' => $target->divisi,
                    'target_lead' => $target->lead_needed,
                    'achieved_lead' => 0,
                    'target_hotleads' => $target->hot_leads_target,
                    'achieved_hotleads' => 0,
                    'target_initconsult' => $target->initial_consult_target,
                    'achieved_initconsult' => 0,
                    'contribution_target' => $target->contribution_to_target,
                    'contribution_achieved' => 0,
                    'status' => 0,
                    'month_year' => date('Y-m').'-01'
                ];
    
            }

    
            LeadTargetTracking::insert($targetTrackingDetails);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::info('Cron Insert target tracking not working normal. Error : '. $e->getMessage() .' | Line '. $e->getCode());

        }

        return Command::SUCCESS;
    }
}

