@extends('layout.main')

@section('title', 'Alumni Acceptance')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Alumni Acceptance
            </h5>

            <a href="{{ route('acceptance.create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
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
                        <th colspan="4" class="text-center">Status</th>
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
                        <td colspan="8"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection
@push('scripts')
<script>
    var widthView = $(window).width();
    $(document).ready(function() {

        var options = {
            buttons: [
                'pageLength', {
                    extend: 'excel',
                    text: 'Export to Excel',
                }
            ],
            fixedColumns: {
                left: (widthView < 768) ? 1 : 2,
                right: 1
            },
            ajax: '',
            columns: [
                {
                    data: 'id',
                    className: 'text-center',
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'full_name',
                },
                {
                    data: 'graduation_year',
                },
                {
                    data: 'waitlisted_groups',
                },
                {
                    data: 'accepted_groups',
                },
                {
                    data: 'denied_groups',
                },
                {
                    data: 'chosen_groups',
                },
                {
                    data: '',
                    className: 'text-center',
                    defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editAcceptance"><i class="bi bi-eye"></i></button>'
                }
            ]
        };

        var table = initializeDataTable('#clientTable', options, 'rt_client');

        $('#clientTable tbody').on('click', '.editAcceptance ', function() {
            var data = table.row($(this).parents('tr')).data();
            window.location.href = "{{ url('client/acceptance/') }}/" + data.client_id + '/edit';
        });

    });
</script>
@endpush
