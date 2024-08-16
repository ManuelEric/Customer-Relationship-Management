@extends('layout.main')

@section('title', 'Purchase Request')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Purchase Request
            </h5>
            <a href="{{ url('master/purchase/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i> Add</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="purchasereqTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Purchase ID</th>
                        <th>Requested By</th>
                        <th>Department</th>
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
                        name: 'tbl_department.dept_name',
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
            };

            var table = initializeDataTable('#purchasereqTable', options, 'rt_purchase_request');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Purchase Request')->first();
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

            $('#purchasereqTable tbody').on('click', '.editRequest ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/purchase') }}/" + data.purchase_id;
            });

            $('#purchasereqTable tbody').on('click', '.deleteRequest ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/purchase', data.purchase_id)
            });
        });
    </script>
    <script type="text/javascript" async defer></script>
@endsection
