@extends('layout.main')

@section('title', 'List Lead Tracking')

@section('content')

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
                <h4>Leads Tracker - {{ ucwords(str_replace('_', ' ', Request::get('type'))) }}</h4>
            </div>
            <div class="col-auto">
                <form action="">
                    <input type="text" id="search" name="search" placeholder="Search" class="form-control">
                    <input type="hidden" id="daterange" name="daterange" value="{{Request::get('daterange')}}" class="form-control">
                    <input type="hidden" id="type" name="type" value="{{Request::get('type')}}" class="form-control">
                </form>
            </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Grade Now</th>
                <th>Lead Source</th>
                <th>Program Name</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads_tracker as $key => $lead_tracker)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $lead_tracker['full_name'] }}</td>
                        <td>{{ $lead_tracker['mail'] }}</td>
                        <td>{{ $lead_tracker['phone'] }}</td>
                        <td>{{ $lead_tracker['grade_now'] }}</td>
                        <td>{{ $lead_tracker['lead_source'] }}</td>
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

@endsection

