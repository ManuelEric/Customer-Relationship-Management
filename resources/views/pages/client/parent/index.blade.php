@extends('layout.main')

@section('title', 'Parent - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Parent
        </a>
        <div>
            <a href="{{ url('client/parent/export_excel') }}" class="btn btn-sm btn-warning"><i class="bi bi-download me-1"></i> Download Template
            </a>
            <a href="{{ url('client/parent/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i> Add
                Parent</a>
        </div>
    </div>
       


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-dark text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Parents Name</th>
                        <th>Parents Email</th>
                        <th>Parents Number</th>
                        <th>Birthday</th>
                        <th>Childs Name</th>
                        <th class="bg-info text-white">Priority</th>
                        <th class="bg-info text-white">#</th>
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

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var table = $('#clientTable').DataTable({
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
                    right: 2
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
                        data: 'full_name',
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        data: 'mail',
                    },
                    {
                        data: 'phone',
                    },
                    {
                        data: 'dob',
                    },
                    {
                        data: 'children_name',
                        name: 'children_name',
                        defaultContent: '-',
                        orderable:true,
                        searchable:true,
                    },
                    {
                        data: 'total_score',
                        className: 'text-primary',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editClient"><i class="bi bi-eye"></i></button>'
                    }
                ],
            });

            $('#clientTable tbody').on('click', '.editClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('client/parent') }}/" + data.id
            });

            $('#clientTable tbody').on('click', '.deleteClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('asset', data.asset_id)
            });
        });
    </script>
@endsection
