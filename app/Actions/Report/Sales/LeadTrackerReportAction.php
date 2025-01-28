<?php

namespace App\Actions\Report\Sales;

use App\Services\LeadTrackerService;
use Illuminate\Support\Carbon;

class LeadTrackerReportAction
{
    private LeadTrackerService $leadTrackerService;

    public function __construct(LeadTrackerService $leadTrackerService)
    {
        $this->leadTrackerService = $leadTrackerService;
    }

    public function execute($date_range)
    {
        [$start_date, $end_date] = ($date_range) ? array_map([$this, "castToCarbon"], explode('-', $date_range)) : $this->selectCurrentWeek();
        $end_date = $end_date->endOfDay();

        $lead_summary = $this->leadTrackerService->summary($start_date, $end_date);
        dd($lead_summary);
        $lead_by_product = [
            'mentoring' => $this->leadTrackerService->leadMentoring($start_date, $end_date),
            'tutoring' => $this->leadTrackerService->leadTutoring($start_date, $end_date),
            'gip' => $this->leadTrackerService->leadGIP($start_date, $end_date),
        ];
        $lead_by_sales = [
            'mentoring' => $this->leadTrackerService->leadMentoringOnSales($start_date, $end_date),
            'tutoring' => $this->leadTrackerService->leadTutoringOnSales($start_date, $end_date),
            'gip' => $this->leadTrackerService->leadGIPOnSales($start_date, $end_date),
        ];
        $sales = \App\Models\User::whereIn('number', [4, 172, 188])->select('id', 'first_name', 'last_name')->get();
        $mapped_sales = $sales->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->full_name
            ];
        });

        return compact('start_date', 'end_date', 'lead_summary', 'lead_by_product', 'lead_by_sales', 'mapped_sales');
    }

    private function castToCarbon(String $item): Carbon
    {
        return Carbon::parse($item);
    }

    private function selectCurrentWeek(): Array
    {
        $week_start_date = Carbon::now()->startOfWeek();
        $week_end_date = Carbon::now()->endOfWeek();
        return [$week_start_date, $week_end_date];
    }
}