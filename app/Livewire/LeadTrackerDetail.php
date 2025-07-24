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

    public $query_lead_tracker, $utm_content_list, $lead_sources;
    protected $updatesQueryString = ['search'];

    #[Url(as: 'type')]
    public ?string $requested_type = null; 
    #[Url(as: 'daterange')]
    public ?string $requested_daterange = null; 

    public $requested_search = null;
    public ?string $utm_content = null;
    public ?string $lead_source = null;

    public $search;


    public function mount(LeadTrackerService $lead_tracker_service) 
    {
        $this->query_lead_tracker = $lead_tracker_service->detailLead($this->requested_type, $this->requested_daterange);
        $this->utm_content_list = $lead_tracker_service->detailLead($this->requested_type, $this->requested_daterange)->whereNotNull('utm_content')->map(function ($item) {
            return [
                'utm_content' => $item['utm_content']
            ];
        })->sortBy('utm_content')->groupBy('utm_content');
        $this->lead_sources = $lead_tracker_service->detailLead($this->requested_type, $this->requested_daterange)->map(function($item){
            return [
                'lead_source' => $item['lead_source']
            ];
        })->sortBy('lead_source')->groupBy('lead_source');
    }

    public function doSearch(LeadTrackerService $lead_tracker_service)
    {
        $this->search =  [
            'search' => $this->requested_search,
            'lead_source' => $this->lead_source,
            'utm_content' => $this->utm_content
        ];
        $this->query_lead_tracker = $lead_tracker_service->detailLead($this->requested_type, $this->requested_daterange, $this->search);
        $this->resetPage();
    }

    public function render()
    {
        $total_leads_tracker = $this->query_lead_tracker->count();
        return view('livewire.lead-tracker-detail')->with([
            'leads_tracker' => $this->query_lead_tracker->paginate(10),
            'percentage_division' => [
                'Digital' => toPercentage($total_leads_tracker, $this->query_lead_tracker->where('lead_from_division', 'Digital')->count()),
                'Sales' => toPercentage($total_leads_tracker, $this->query_lead_tracker->where('lead_from_division', 'Sales')->count()),
                'Partnership' => toPercentage($total_leads_tracker, $this->query_lead_tracker->where('lead_from_division', 'Partnership')->count()),
                'Other' => toPercentage($total_leads_tracker, $this->query_lead_tracker->where('lead_from_division', null)->count()),
            ]
        ]);
    }
}
