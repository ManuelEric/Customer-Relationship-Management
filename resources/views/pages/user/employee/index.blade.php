@extends('layout.main')

@section('title', 'Employee - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Employee
        </a>
        <a href="{{ url('user/employee/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i>
            Add
            Employee</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ url('user/employee') }}">Employee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ url('user/mentor') }}">Mentor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ url('user/editor') }}">Editor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ url('user/tutor') }}">Tutor</a>
                </li>
            </ul>

            <table class="table table-bordered table-hover nowrap align-middle w-100" id="volunteerTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="bg-info text-white">Employee ID</th>
                        <th class="bg-info text-white">Full Name</th>
                        <th>E-mail</th>
                        <th>Phone</th>
                        <th>Position</th>
                        <th>Graduated Form</th>
                        <th>Major</th>
                        <th>Date of Birth</th>
                        <th>NIK</th>
                        <th>NPWP</th>
                        <th>Bank Account</th>
                        <th>Emergency Contact</th>
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
            var table = $('#volunteerTable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
                ],
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                scrollX: true,
                fixedColumns: {
                    left: 2,
                    right: 1
                },
                // processing: true,
                // serverSide: true,
                // ajax: '',
                // columns: [{
                //         data: 'volunt_id',
                //         className: 'text-center',
                //         render: function(data, type, row, meta) {
                //             return meta.row + meta.settings._iDisplayStart + 1;
                //         }
                //     },
                //     {
                //         data: 'volunt_id',
                //     },
                //     {
                //         data: 'volunt_firstname',
                //         render: function(data, type, row) {
                //             let firstname = row.volunt_firstname != null ? row.volunt_firstname : ''
                //             let lastname = row.volunt_lastname != null ? row.volunt_lastname : ''
                //             return firstname + ' ' + lastname
                //         }
                //     },
                //     {
                //         data: 'volunt_mail',
                //     },
                //     {
                //         data: 'volunt_phone',
                //     },
                //     {
                //         data: 'volunt_graduatedfr',
                //     },
                //     {
                //         data: 'volunt_major',
                //     },
                //     {
                //         data: 'volunt_position',
                //     },
                //     {
                //         data: 'volunt_address',
                //     },
                //     {
                //         data: 'volunt_status',
                //         render: function(data, type, row) {
                //             let status = row.volunt_status == 1 ? 'success' : 'danger'
                //             let title = row.volunt_status == 1 ? 'Active' : 'Inactive'
                //             return '<div class="badge badge-' + status + '">' + title + '</badge>'
                //         }
                //     },
                //     {
                //         data: '',
                //         className: 'text-center',
                //         defaultContent: '<button type="button" class="btn btn-sm btn-warning editVolunt"><i class="bi bi-pencil"></i></button>' +
                //             '<button type="button" class="btn btn-sm btn-danger ms-1 deleteVolunt"><i class="bi bi-trash"></i></button>'
                //     }
                // ]
            });

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
