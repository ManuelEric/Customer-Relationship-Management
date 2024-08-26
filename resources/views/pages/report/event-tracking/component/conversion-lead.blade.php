@if (count($conversionLeads) > 0)
    <div class="card mb-3">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h6 class="p-0 m-0">Lead Source</h6>
        </div>
        <div class="card-body p-2 overflow-auto" style="max-height: 200px;">
            <ul class="list-group">
                @forelse ($conversionLeads as $conversionLead)
                    <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                        <div class="">{{ $conversionLead->conversion_lead }}</div>
                        <span class="badge badge-warning">{{ $conversionLead->count_conversionLead }}</span>
                    </li>
                @empty
                    <li class="text-center">Not lead source yet</li>
                @endforelse
            </ul>
        </div>
    </div>
@endif
