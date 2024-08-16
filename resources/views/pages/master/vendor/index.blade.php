@extends('layout.main')

@section('title', 'Vendors')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Vendors
            </h5>
            <a href="{{ url('master/vendor/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i> Add
                Vendor</a>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="vendor-table">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Vendor ID</th>
                        <th>Vendor Name</th>
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
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editVendor"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteVendor"><i class="bi bi-trash"></i></button>'
                    }
                ]
            }

            var table = initializeDataTable('#vendor-table', options, 'rt_vendor');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Vendors')->first();
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

            $('#vendor-table tbody').on('click', '.editVendor ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/vendor') }}/" + data.vendor_id.toLowerCase() +
                    '/edit';
            });

            $('#vendor-table tbody').on('click', '.deleteVendor ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/vendor', data.vendor_id)
            });
        });
    </script>
@endsection
