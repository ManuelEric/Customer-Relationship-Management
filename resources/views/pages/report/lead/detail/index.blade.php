@extends('layout.main')

@section('title', 'Lead Tracking')

@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Lead Tracking
            </h5>
            <a href="{{ url('master/asset/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
                Add
                Asset</a>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="leadTrackingTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Grade</th>
                        <th>Lead Source</th>
                        <th>Program Name</th>
                        <th class="bg-info text-white">Action</th>
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

    <script>
        $(document).ready(function() {

            var options = {
                order: [[2, 'asc']],
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                fixedColumns: {
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
                ajax: '',
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'full_name',
                    },
                    {
                        data: 'mail',
                    },
                    {
                        data: 'phone',
                    },
                    {
                        data: 'grade_now',
                    },
                    {
                        data: 'lead_source',
                    },
                    {
                        data: 'program_name',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning ms-1 showDetail"><i class="bi bi-eye"></i></button>'
                    }
                ],
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
            };

            var table = initializeDataTable('#leadTrackingTable', options, 'rt_client');

            @php
                $privilage = $menus['Report']->where('submenu_name', 'Lead Tracker')->first();
            @endphp

            @if ($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false");

                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif

            @if ($privilage['export'] == 0)
                table.button(1).disable();
            @endif


            $('#leadTrackingTable tbody').on('click', '.showDetail', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('client/student') }}/" + data.client_id.toLowerCase();
            });

        });
    </script>
@endsection
