@extends('layout.main')

@section('title', 'Alumni Acceptance')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Alumni Acceptance
            </h5>

            <a href="{{ url('client/alumni-acceptance/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
                Add</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th rowspan="2" class="bg-info text-white">No</th>
                        <th rowspan="2" class="bg-info text-white">Mentee Name</th>
                        <th rowspan="2">Graduation Year</th>
                        <th colspan="4">Status</th>
                        <th rowspan="2" class="bg-info text-white"># Action</th>
                    </tr>
                    <tr class="text-center">
                        <th>Waitlisted</th>
                        <th>Accepted</th>
                        <th>Denied</th>
                        <th>Decided</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Need Changing --}}
    <script></script>
@endsection
