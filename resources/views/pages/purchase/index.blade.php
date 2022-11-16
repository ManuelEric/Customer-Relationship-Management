@extends('layout.main')

@section('title', 'Purchase Request - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Purchase Request
        </a>
        <a href="{{ url('master/purchase/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i> Add
            Purchase Request</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="purchasereqTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="bg-info text-white">Purchase ID</th>
                        <th class="bg-info text-white">Requested By</th>
                        <th class="bg-info text-white">Department</th>
                        <th>Request Status</th>
                        <th>Last Update</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var table = $('#purchasereqTable').DataTable({
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
                        data: 'purchase_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'purchase_id',
                    },
                    {
                        data: 'fullname',
                    },
                    {
                        data: 'dept_name',
                    },
                    {
                        data: 'purchase_statusrequest',
                    },
                    {
                        data: 'updated_at',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editRequest"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteRequest"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            });

            $('#purchasereqTable tbody').on('click', '.editRequest ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/purchase') }}/" + data.purchase_id;
            });

            // $('#vendor-table tbody').on('click', '.deleteRequest ', function() {
            //     var data = table.row($(this).parents('tr')).data();
            //     confirmDelete('master/purchase', data.purchase_id)
            // });
        });
    </script>
    <script type="text/javascript" async defer></script>
@endsection
