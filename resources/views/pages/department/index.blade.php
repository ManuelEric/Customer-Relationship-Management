@extends('layout.main')

@section('title', 'Department - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Department
        </a>
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#departmentForm"><i
                class="bi bi-plus-square me-1"></i> Add
            Department</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="departmentTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="bg-info text-white">Department Name</th>
                        <th class="bg-info text-white">Created At</th>
                        <th class="bg-info text-white">Updated At</th>
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

    @include('pages.department.create')

    @include('pages.department.update')

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var table = $('#departmentTable').DataTable({
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
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'dept_name',
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                    },
                    {
                        data: 'updated_at',
                        className: 'text-center',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editDepartment"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteDepartment"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            });

            $('#departmentTable tbody').on('click', '.editDepartment ', function() {
                var data = table.row($(this).parents('tr')).data();

                var element = "#departmentFormUpdate"

                $(element).modal('show');
                var action = "{{ url('master/department') }}" + "/" + data.id
                $(element).find('form').attr('action', action)
                $(element).find('form input[name=dept_name]').val(data.dept_name)
                $(element).find('form input[name=id]').val(data.id)
            });

            $('#departmentTable tbody').on('click', '.deleteDepartment ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/department', data.id)
            });
        });
    </script>
@endsection
