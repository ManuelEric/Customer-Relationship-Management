@extends('layout.main')

@section('title', 'Volunteer - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Volunteer
        </a>
        <a href="{{ url('user/volunteer/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i>
            Add
            Volunteer</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-white text-dark">#</th>
                        <th class="bg-info text-white">Volunteer ID</th>
                        <th class="bg-info text-white">Full Name</th>
                        <th>E-mail</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


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
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [{
                        data: 'volunt_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'volunt_id',
                    },
                    {
                        data: 'volunt_firstname',
                        render: function(data, type, row) {
                            let firstname = row.volunt_firstname != null ? row.volunt_firstname : ''
                            let lastname = row.volunt_lastname != null ? row.volunt_lastname : ''
                            return firstname + ' ' + lastname
                        }
                    },
                    {
                        data: 'volunt_mail',
                    },
                    {
                        data: 'volunt_phone',
                    },
                    {
                        data: 'volunt_address',
                    },
                    {
                        data: 'volunt_status',
                        render: function(data, type, row) {
                            let status = row.volunt_status == 1 ? 'success' : 'danger'
                            let title = row.volunt_status == 1 ? 'Active' : 'Inactive'
                            return '<div class="badge badge-' + status + '">' + title + '</badge>'
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-warning editVolunt"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-danger ms-1 deleteVolunt"><i class="bi bi-trash"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#volunteerTable', options, 'rt_user');

            @php
                $privilage = $menus['Users']->where('submenu_name', 'Volunteer')->first();
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

            $('#volunteerTable tbody').on('click', '.editVolunt ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('user/volunteer') }}/" + data.volunt_id + '/edit';
            });

            $('#volunteerTable tbody').on('click', '.deleteVolunt ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('user/volunteer', data.volunt_id)
            });
        });
    </script>
@endsection
