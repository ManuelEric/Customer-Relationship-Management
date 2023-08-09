@extends('layout.main')

@section('title', 'Request Sign')

@section('content')    
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Request Sign
            </h5>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('type') == 'invoice' ? 'active' : '' }}"
                        href="{{ url('request-sign?type=invoice') }}">Invoice <div class="badge bg-info p-1 px-2">4</div></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('type') == 'receipt' ? 'active' : '' }}"
                        href="{{ url('request-sign?type=receipt') }}">Receipt <div class="badge bg-info p-1 px-2">4</div></a>
                </li>
            </ul>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="requestSignTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">{{ Request::get('type') == 'receipt' ? 'Receipt ID' : 'Invoice ID' }}</th>
                        <th>Full Name</th>
                        <th>Program Name</th>
                        <th>Payment Method</th>
                        <th>Due Date</th>
                        <th>Total Other</th>
                        <th>Total IDR</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="9"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // var table = $('#requestSignTable').DataTable({
            //     dom: 'Bfrtip',
            //     lengthMenu: [
            //         [10, 25, 50, 100, -1],
            //         ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
            //     ],
            //     buttons: [
            //         'pageLength', {
            //             extend: 'excel',
            //             text: 'Export to Excel',
            //         }
            //     ],
            //     scrollX: true,
            //     fixedColumns: {
            //         left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
            //         right: 1
            //     },
            //     processing: true,
            //     serverSide: true,
            //     ajax: '',
            //     pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
            //     columns: [{
            //             data: 'id',
            //             className: 'text-center',
            //             render: function(data, type, row, meta) {
            //                 return meta.row + meta.settings._iDisplayStart + 1;
            //             }
            //         },
            //         {
            //             data: 'program_name',
            //             // name: 'program_name',
            //             name: 'program.program_name'

            //         },
            //         {
            //             data: 'total_participant',
            //             className: 'text-center',
            //         },
            //         {
            //             data: 'total_target',
            //             className: 'text-center',
            //             render: function(data, type) {
            //                 var number = $.fn.dataTable.render
            //                     .number(',', '.', 2, 'Rp. ')
            //                     .display(data);

            //                 return number;
            //             },
            //         },
            //         {
            //             data: 'month',
            //             className: 'text-center',
            //         },
            //         {
            //             data: 'year',
            //             className: 'text-center',
            //         },
            //         {
            //             data: '',
            //             className: 'text-center',
            //             defaultContent: '<button type="button" data-bs-toggle="modal" data-bs-target="#requestSign" class="btn btn-sm btn-outline-warning editSeasonalProgram"><i class="bi bi-pencil"></i></button>' +
            //                 '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteSeasonalProgram"><i class="bi bi-trash2"></i></button>'
            //         }
            //     ]
            // });
        });
    </script>
@endsection