@extends('layout.main')

@section('title', 'Department - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Department
        </a>
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#leadForm"><i
                class="bi bi-plus-square me-1"></i> Add
            Department</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="departmentTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="bg-info text-white">Department Name</th>
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

    {{-- Create and Updated  --}}
    <!-- Modal -->
    <div class="modal fade" id="leadForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Department
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="" method="POST">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Department Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="dept_name" class="form-control form-control-sm rounded"
                                        required value="">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                                <i class="bi bi-x-square me-1"></i>
                                Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save2 me-1"></i>
                                Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Need Changing --}}
    {{-- <script>
        $(document).ready(function() {
            var table = $('#programTable').DataTable({
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
                ajax: '{!! route('asset.datatables') !!}',
                columns: [{
                        data: '',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: '',
                    },
                    {
                        data: '',
                    },
                    {
                        data: '',
                    },
                    {
                        data: '',
                    },
                    {
                        data: '',
                    },
                    {
                        data: '',
                    },
                    {
                        data: '',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-warning editAsset"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-danger ms-1 deleteAsset"><i class="bi bi-trash"></i></button>'
                    }
                ]
            });

            $('#programTable tbody').on('click', '.editAsset ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('asset') }}/" + data.asset_id.toLowerCase() + '/edit';
            });

            $('#programTable tbody').on('click', '.deleteAsset ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('asset', data.asset_id)
            });
        });
    </script> --}}
@endsection
