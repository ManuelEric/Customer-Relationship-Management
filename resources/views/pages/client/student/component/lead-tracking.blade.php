@if (isset($historyLeads) && $historyLeads->count() > 0)
    <div class="card rounded mb-2">
        <div class="card-header">
            <div class="">
                <h5 class="m-0 p-0">Lead Status Tracking</h5>
            </div>
        </div>
        <div class="card-body">
            @foreach ($historyLeads as $initprog => $historyLead)
                <div class="row align-items-center border-b-2">
                    @php
                        $currentLead = $historyLead
                            ->where('status', 1)
                            ->where('initprog', $initprog)
                            ->first();
                        $oldLeads = $historyLead->where('status', 0)->where('initprog', $initprog);
                    @endphp
                    <div class="col-md-5 col-12">
                        {{ $initprog }}
                    </div>
                    @if (isset($currentLead))
                        <div class="col-md-3 col-4 text-center d-flex align-items-center">
                            <i
                                class="fs-3"></i>
                            <small class="text-muted">({{ $currentLead['total_result_program'] }}/1)</small>
                        </div>
                        <div class="col-md-3 col-4 text-center d-flex align-items-center">
                            @if ($currentLead['lead_status'] == 'Hot')
                                <i class="bi bi-fire text-danger fs-5 me-2"></i> {{ $currentLead['lead_status'] }}
                                <small class="text-muted">({{ $currentLead['total_result_lead'] }}/1)</small>
                            @elseif($currentLead['lead_status'] == 'Warm')
                                <i class="bi bi-fire text-warning fs-5 me-2"></i> {{ $currentLead['lead_status'] }}
                                <small class="text-muted">({{ $currentLead['total_result_lead'] }}/1)</small>
                            @elseif($currentLead['lead_status'] == 'Cold')
                                <i class="bi bi-snow3 text-info fs-5 me-2"></i> {{ $currentLead['lead_status'] }}
                                <small class="text-muted">({{ $currentLead['total_result_lead'] }}/1)</small>
                            @endif
                        </div>
                    @else
                        <div class="col-md-3 col-4  text-center d-flex align-items-center">
                            -
                        </div>
                    @endif
                    <div class="col-md-1 col-4 text-end">
                        <div class="dropdown">
                            <i class="bi bi-info-circle cursor-pointer" title="History" data-bs-toggle="dropdown"></i>
                            <div class="dropdown-menu">
                                <div class="dropdown-header">
                                    History of Lead Status Tracking
                                </div>
                                <div class="px-3 overflow-auto" style="max-height: 150px">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped" style="font-size: 10px">
                                            <tr>
                                                <th>No</th>
                                                <th>Main Program</th>
                                                <th>Status</th>
                                                <th>Last Date</th>
                                                <th>Reason</th>
                                            </tr>
                                            @forelse ($oldLeads->sortByDesc('updated_at') as $oldLead)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $initprog }}</td>
                                                    <td>{{ $oldLead['lead_status'] }}</td>
                                                    <td>{{ $oldLead['updated_at'] }}</td>
                                                    <td>{{ $oldLead['reason'] != null ? $oldLead['reason'] : '-' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr class="text-center">
                                                    <td colspan="5">No data</td>
                                                </tr>
                                            @endforelse
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
