@extends('layout.main')

@section('title', 'Employee - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Employee
        </a>
        {{-- @if(Request::route('user_role') != 'editor') --}}
            <a href="{{ url('user/' . Request::route('user_role') . '/create') }}" class="btn btn-sm btn-primary"><i
                    class="bi bi-plus-square me-1"></i>
                Add
                {{ ucfirst(Request::route('user_role')) }}</a>
        {{-- @endif --}}
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ Request::route('user_role') == 'employee' ? 'active' : null }}"
                        aria-current="page" href="{{ url('user/employee') }}">Employee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::route('user_role') == 'mentor' ? 'active' : null }}" aria-current="page"
                        href="{{ url('user/mentor') }}">Mentor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::route('user_role') == 'editor' ? 'active' : null }}" aria-current="page"
                        href="{{ url('user/editor') }}">Editor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::route('user_role') == 'tutor' ? 'active' : null }}" aria-current="page"
                        href="{{ url('user/tutor') }}">Tutor</a>
                </li>
            </ul>

            <table class="table table-bordered table-hover nowrap align-middle w-100" id="userTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-white text-dark">#</th>
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
            
            var options = {
                order: [[1, 'asc']],
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
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'extended_id',
                    },
                    {
                        data: 'full_name',
                    },
                    {
                        data: 'email',
                    },
                    {
                        data: 'phone',
                    },
                    {
                        data: 'position_name',
                        name: 'tbl_position.position_name',
                    },
                    {
                        data: 'graduation_date_group',
                    },
                    {
                        data: 'major_group',
                    },
                    {
                        data: 'datebirth',
                    },
                    {
                        data: 'nik',
                    },
                    {
                        data: 'npwp',
                    },
                    {
                        data: 'bankacc',
                    },
                    {
                        data: 'emergency_contact',
                    },
                    {
                        data: 'address',
                    },
                    {
                        data: 'active',
                        render: function(data, type, row, meta) {
                            if (data == 1)
                                return "Active"
                            else
                                return "Not Active"
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-warning editUser"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-danger ms-1 deleteUser"><i class="bi bi-trash"></i></button>'
                    }
                ],
                createdRow: function(row, data, index) {
                    // temporary condition
                    // while change soon
                    if (data['active'] == 0) {
                        $('td', row).addClass('text-danger');
                        $('td:nth-last-child(1) .deleteUser', row).addClass('d-none');
                        // $('td:nth-last-child(2)', row).addClass('bg-danger rounded text-white my-2');
                    }
                }
            };

            var table = initializeDataTable('#userTable', options, 'rt_user');

            @php
                $privilage = $menus['Users']->where('submenu_name', 'Employee')->first();
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

            $('#userTable tbody').on('click', '.editUser ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('user/' . Request::route('user_role')) }}/" + data.id +
                    '/edit';
            });

            $('#userTable tbody').on('click', '.deleteUser ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('user/{{ Request::route('user_role') }}', data.id)
            });
        });
    </script>
@endsection
