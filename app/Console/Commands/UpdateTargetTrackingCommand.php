<?php

namespace App\Console\Commands;

use App\Interfaces\LeadTargetRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateTargetTrackingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:target_tracking {date}';

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
        $now = $this->argument('date');

        $timer_start = Carbon::now();

        $division = ['Sales', 'Referral', 'Digital'];
        $progressBar = $this->output->createProgressBar(count($division));
        $progressBar->start();

        DB::beginTransaction();
        try {

            // all of the update target tracking should be running after command "insert:target_tracking_monthly"

            $achievedRevenue = $this->leadTargetRepository->getAchievedRevenue($now);

            foreach ($division as $key => $division_name) {

                // checking active target from target tracking
                // won't continue the process before the active target is inserted
                if (! $activeTarget = $this->leadTargetRepository->findThisMonthTargetByDivision($now, $division_name)) {
                    continue;
                }

                $achievedLeadMethodName = 'getAchievedLead'.$division_name.'ByMonth';
                $achievedHotLeadMethodName = 'getAchievedHotLead'.$division_name.'ByMonth';
                $achievedInitConsultMethodName = 'getAchievedInitConsult'.$division_name.'ByMonth';
                $achievedContributionMethodName = 'getAchievedContribution'.$division_name.'ByMonth';

                $achievedLead = $this->leadTargetRepository->{$achievedLeadMethodName}($now)->count();
                $achievedHotLead = $this->leadTargetRepository->{$achievedHotLeadMethodName}($now)->count();
                $achievedInitConsult = $this->leadTargetRepository->{$achievedInitConsultMethodName}($now)->count();
                $achievedContribution = $this->leadTargetRepository->{$achievedContributionMethodName}($now)->count();

                $contribution_target = $activeTarget->contribution_target;

                // if the contribution target has achieved then put status into 1 which is complete
                $status = $contribution_target <= $achievedContribution ? 1 : 0;

                $details = [
                    'achieved_lead' => $achievedLead,
                    'achieved_hotleads' => $achievedHotLead,
                    'achieved_initconsult' => $achievedInitConsult,
                    'contribution_achieved' => $achievedContribution,
                    'revenue_achieved' => $achievedRevenue,
                    'status' => $status,
                    'updated_at' => Carbon::now(),
                ];

                $this->leadTargetRepository->updateActualLead($details, $now, $division_name);

                $progressBar->advance();
            }

            DB::commit();
            $progressBar->finish();

        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Cron update target tracking not working normal. Error : '.$e->getMessage().' | Line '.$e->getCode());

        }

        $timer_end = Carbon::now();
        $progress_time = $timer_start->diffInSeconds($timer_end);

        Log::info("[CRON - UPDATE TARGET TRACKING] has been run. It tooks {$progress_time} seconds");

        return Command::SUCCESS;
    }
}
