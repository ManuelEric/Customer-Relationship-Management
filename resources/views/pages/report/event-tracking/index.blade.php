@extends('layout.main')

@section('title', 'Sales Tracking - Bigdata Platform')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="p-0 m-0">Client Event</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('report.client.event') }}" method="GET">
                        {{-- @csrf --}}
                        <div class="mb-3">
                            <label>Event Name</label>
                            <select name="event_id" id="" class="select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->event_id }}">{{ $event->event_title }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Client  --}}
            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Client</h6>
                    <span class="badge bg-primary">{{ count($clientEvents) }}</span>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        @forelse ($clients as $client)
                            <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                                <div class="">{{ $client->role_name }}</div>
                                <span class="badge badge-info">{{ $client->count_role }}</span>
                            </li>
                        @empty
                            <li class="text-center">Not yet Client</li> 
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Conversion Lead  --}}
            <div class="card mb-3">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Conversion Lead</h6>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 150px ">
                    <ul class="list-group">
                        @forelse ($conversionLeads as $conversionLead)
                            <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                                <div class="">{{ $conversionLead->conversion_lead }}</div>
                                <span class="badge badge-warning">{{ $conversionLead->count_conversionLead }}</span>
                            </li>
                        @empty
                            <li class="text-center">Not yet conversion lead</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="p-0 m-0">Client Event</h6>
                    <div class="">
                        <button class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-excel me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="height: 500px">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover nowrap align-middle w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>School Name</th>
                                    <th>Grade</th>
                                    <th>Conversion Lead</th>
                                    <th>Joined At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientEvents as $clientEvent)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $clientEvent->client->first_name }} {{ $clientEvent->client->last_name }}</td>
                                        <td>{{ $clientEvent->client->mail }}</td>
                                        <td>{{ $clientEvent->client->phone }}</td>
                                        <td>{{ isset($clientEvent->client->sch_id) ? $clientEvent->client->school->sch_name : '-' }}</td>
                                        <td>{{ isset($clientEvent->client->st_grade) ? $clientEvent->client->st_grade : '-' }}</td>
                                        <td> 
                                            @switch($clientEvent->lead->main_lead)
                                                @case('KOL')
                                                    {{ $clientEvent->lead->main_lead }}: {{ $clientEvent->lead->sub_lead }}
                                                    @break
                                                @case('External Edufair')
                                                    {{ $clientEvent->lead->main_lead }}: {{ $clientEvent->edufLead->title }}
                                                    @break
                                                @case('All-In Partners')
                                                    {{ $clientEvent->lead->main_lead }}: {{ $clientEvent->partner->corp_name }}
                                                    @break
                                                @default
                                                    {{ $clientEvent->lead->main_lead }}
                                                    
                                            @endswitch

                                        </td>
                                        <td>{{ $clientEvent->joined_date }}</td>
                                @empty
                                        <td colspan="8" class="text-center">
                                            Not yet event
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
