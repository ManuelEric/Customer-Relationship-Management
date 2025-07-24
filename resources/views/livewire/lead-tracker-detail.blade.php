<div>
    <div class="row align-items-stretch mb-3">
        <div class="statistics-details d-flex align-items-center justify-content-start">
            @if($percentage_division['Digital'] > 0)
                <div class="card p-2 me-2" style="min-width: 120px">
                    <p class="statistics-title text-center">Digital Leads</p>
                    <h3 class="rate-percentage text-primary text-center">{{ $percentage_division['Digital'] }}%</h3>
                </div>
            @endif
            @if($percentage_division['Sales'] > 0)
                <div class="card p-2 me-2" style="min-width: 120px">
                    <p class="statistics-title text-center">Sales Leads</p>
                    <h3 class="rate-percentage text-warning text-center">{{ $percentage_division['Sales'] }}%</h3>
                </div>
            @endif
            @if($percentage_division['Partnership'] > 0)
                <div class="card p-2 me-2" style="min-width: 100px">
                    <p class="statistics-title text-center">Partnership Leads</p>
                    <h3 class="rate-percentage text-success text-center">{{ $percentage_division['Partnership'] }}%</h3>
                </div>
            @endif
            @if($percentage_division['Other'] > 0)
                <div class="card p-2 me-2" style="min-width: 100px">
                    <p class="statistics-title text-center">Other Leads</p>
                    <h3 class="rate-percentage text-success text-center">{{ $percentage_division['Other'] }}%</h3>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <div class="row g-3 align-items-center">
                    <div class="col-auto me-auto">
                        <h4>Leads Tracker - {{ ucwords(str_replace('_', ' ', $requested_type)) }}</h4>
                    </div>
                    <div class="col-auto">
                        {{-- <div class="row row-cols-md-3 g-1">
                            <div class="col">
                                <select name="lead_source" id="leadSource" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    <option value=""
                                        {{ isset($_GET['lead_source']) && $_GET['lead_source'] == '' ? 'selected' : '' }}>
                                        Select
                                        Lead Source</option>
                                    @foreach ($lead_source as $key => $item)
                                        <option value="{{ $key }}"
                                            {{ isset($_GET['lead_source']) && $_GET['lead_source'] == $key ? 'selected' : '' }}>
                                            {{ $key }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <select name="utm_content" id="utmContent" class="form-select form-select-sm "
                                    placeholder="Test" onchange="this.form.submit()">
                                    <option value=""
                                        {{ isset($_GET['utm_content']) && $_GET['utm_content'] == '' ? 'selected' : '' }}>
                                        Select
                                        UTM Content</option>
                                    @foreach ($utm_content as $key => $item)
                                        <option value="{{ $key == '' ? '-' : $key }}"
                                            {{ isset($_GET['utm_content']) && $_GET['utm_content'] == $key ? 'selected' : '' }}>
                                            {{ $key == '' ? '-' : $key }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <input type="text" id="search" name="search" placeholder="Search"
                                    class="form-control form-control-sm" value="{{ $_GET['search'] ?? '' }}">
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col">
                                <select wire:model="lead_source" class="form-select form-select-sm" wire:change="doSearch">
                                    <option value="">Select Lead Source</option>
                                    @foreach ($lead_sources as $key => $item)
                                        <option value="{{ $key }}">
                                            {{ $key }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <select wire:model="utm_content" class="form-select form-select-sm" wire:change="doSearch">
                                    <option value="">Select UTM Content</option>
                                    @foreach ($utm_content_list as $key => $item)
                                        <option value="{{ $key }}">
                                            {{ $key }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col">
                                <input type="text" placeholder="Search" class="form-control form-control-sm" wire:model="requested_search" wire:keydown.enter="doSearch">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div wire:loading>Loading..</div>
            <div class="table-responsive" wire:loading.remove>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Grade Now</th>
                        <th>School Name</th>
                        <th>Lead Source</th>
                        <th>UTM Content</th>
                        <th>Interest Program</th>
                        <th>Program Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads_tracker as $key => $lead_tracker)
                            <tr>
                                <td>{{ ($leads_tracker->currentPage() - 1) * $leads_tracker->perPage() + $loop->iteration }}</td>
                                <td>{{ $lead_tracker['full_name'] }}</td>
                                <td>{{ $lead_tracker['mail'] }}</td>
                                <td>{{ $lead_tracker['phone'] }}</td>
                                <td>{{ $lead_tracker['grade_now'] }}</td>
                                <td>{{ $lead_tracker['school_name'] }}</td>
                                <td>{{ $lead_tracker['lead_source'] }}</td>
                                <td>{{ $lead_tracker['utm_content'] }}</td>
                                <td>{{ $lead_tracker['interest_program'] }}</td>
                                <td>{{ $lead_tracker['program_name'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">There is not data yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $leads_tracker->links() }}
            </div>
        </div>
    </div>
</div>
