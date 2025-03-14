@extends('layout.main')

@section('title', 'List Lead Tracking')

@section('content')

<div class="card">
    <div class="card-body">
      <div class="card-title">
        <div class="row g-3 align-items-center">
            <div class="col-auto me-auto">
                <h4>Leads Tracker</h4>
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

