@extends('layout.main')

@section('title', 'Edufair')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Edufair
            </h5>
            <a href="{{ url('master/edufair/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
                Add
                Edufair</a>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="edufairTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Organizer Name</th>
                        <th>Event Name</th>
                        <th>PIC Name</th>
                        <th>PIC Contact</th>
                        <th>PIC Email</th>
                        <th>First Discuss</th>
                        <th>Last Discuss</th>
                        <th>Start Event</th>
                        <th>End Event</th>
                        <th>Status</th>
                        <th>ALL-in PIC</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="12"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var options = {
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                fixedColumns: {
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 1,
                    right: 1
                },
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'organizer_name',
                    },
                    {
                        data: 'title',
                    },
                    {
                        data: 'ext_pic_name',
                    },
                    {
                        data: 'ext_pic_phone',
                    },
                    {
                        data: 'ext_pic_mail',
                    },
                    {
                        data: 'first_discussion_date',
                        render: function(data, type, row) {
                            let date = row.first_discussion_date !=
                                '0000-00-00' && row.first_discussion_date != null ? moment(row
                                    .first_discussion_date).format("MMMM Do YYYY") : '-'
                            return date
                        }
                    },
                    {
                        data: 'last_discussion_date',
                        render: function(data, type, row) {
                            let date = row.last_discussion_date !=
                                '0000-00-00' && row.last_discussion_date != null ? moment(row
                                    .last_discussion_date).format("MMMM Do YYYY") : '-'
                            return date
                        }
                    },
                    {
                        data: 'event_start',
                        render: function(data, type, row) {
                            let date = row.event_start !=
                                '0000-00-00' && row.event_start != null ? moment(row
                                    .event_start).format("MMMM Do YYYY") : '-'
                            return date
                        }
                    },
                    {
                        data: 'event_end',
                        render: function(data, type, row) {
                            let date = row.event_end !=
                                '0000-00-00' && row.event_end != null ? moment(row
                                    .event_end).format("MMMM Do YYYY") : '-'
                            return date
                        }
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            if (row.status == 0)
                                return "<div class='badge badge-outline-info'>Pending</div>"
                            else if (row.status == 1)
                                return "<div class='badge badge-outline-success'>Success</div>"
                            else
                                return "<div class='badge badge-outline-danger'>Denied</div>"
                        }
                    },
                    {
                        data: 'fullname',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning viewEdufair"><i class="bi bi-eye"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteEdufair"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#edufairTable', options, 'rt_eduf_lead');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'External Edufair')->first();
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

            
            $('#edufairTable tbody').on('click', '.viewEdufair ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/edufair') }}/" + data.id;
            });

            $('#edufairTable tbody').on('click', '.deleteEdufair ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/edufair', data.id)
            });
        });
    </script>
@endsection
