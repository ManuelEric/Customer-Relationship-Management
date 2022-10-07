@extends('layout.main')

@section('title', 'Vendor - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Vendor List
        </a>
        <a href="{{ url('vendor/create') }}" class="btn btn-sm btn-primary">Add Vendor</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="vendor-table">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>#</th>
                        <th class="bg-info text-white">Vendor ID</th>
                        <th class="bg-info text-white">Vendor Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Material</th>
                        <th>Size</th>
                        <th>Processing Time</th>
                        <th>Unit Price</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="10"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#vendor-table').DataTable({
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
                ajax: '{!! route('vendor.datatables') !!}',
                columns: [{
                        data: 'vendor_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'vendor_id',
                    },
                    {
                        data: 'vendor_name',
                    },
                    {
                        data: 'vendor_address',
                    },
                    {
                        data: 'vendor_phone',
                    },
                    {
                        data: 'vendor_type',
                    },
                    {
                        data: 'vendor_material',
                    },
                    {
                        data: 'vendor_size',
                    },
                    {
                        data: 'vendor_processingtime',
                    },
                    {
                        data: 'vendor_unitprice',
                        render: function(data, type) {
                            var number = $.fn.dataTable.render
                                .number(',', '.', 2, 'Rp. ')
                                .display(data);

                            return number;
                        },
                    },
                    {
                        targets: -1,
                        data: 'null',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-warning editVendor"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-danger ms-1 deleteVendor"><i class="bi bi-trash"></i></button>'
                    }
                ]
            });

            $('#vendor-table tbody').on('click', '.editVendor ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('vendor') }}/" + data.vendor_id.toLowerCase() + '/edit';
            });

            $('#vendor-table tbody').on('click', '.deleteVendor ', function() {
                var data = table.row($(this).parents('tr')).data();
                var link = "{{ url('vendor') }}/destroy/" + data.vendor_id.toLowerCase()
                var callback = "{{ url('vendor') }}"


                confirmDelete('vendor', data.vendor_id)
            });
        });
    </script>
@endsection
