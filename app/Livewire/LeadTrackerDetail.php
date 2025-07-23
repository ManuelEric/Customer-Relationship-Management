<?php

namespace App\Livewire;

use App\Services\LeadTrackerService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title("Lead Tracker Detail")]
class LeadTrackerDetail extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $query_lead_tracker;

    #[Url(as: 'type')]
    public ?string $requested_type = null; 
    #[Url(as: 'daterange')]
    public ?string $requested_daterange = null; 
    #[Url(as: 'search')]
    public ?string $requested_search = null;


    public function mount(LeadTrackerService $lead_tracker_service) 
    {
        $this->query_lead_tracker = $query = $lead_tracker_service->detailLead($this->requested_type, $this->requested_daterange, $this->requested_search);
    }

    public function render()
    {
        $total_leads_tracker = $this->query_lead_tracker->count();
        return view('livewire.lead-tracker-detail')->with([
            'leads_tracker' => (clone $this->query_lead_tracker)->paginate(10),
            'percentage_division' => [
                'Digital' => toPercentage($total_leads_tracker, (clone $this->query_lead_tracker)->where('lead_from_division', 'Digital')->count()),
                'Sales' => toPercentage($total_leads_tracker, (clone $this->query_lead_tracker)->where('lead_from_division', 'Sales')->count()),
                'Partnership' => toPercentage($total_leads_tracker, (clone $this->query_lead_tracker)->where('lead_from_division', 'Partnership')->count()),
                'Other' => toPercentage($total_leads_tracker, (clone $this->query_lead_tracker)->where('lead_from_division', null)->count()),
            ]
        ]);
    }
}
