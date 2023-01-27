<table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
    <thead class="bg-dark text-white">
        <tr>
            <th class="bg-info text-white">#</th>
            <th class="bg-info text-white">Student/Partner/School Name</th>
            <th>Program Name</th>
            <th>Refund Date</th>
            <th>Total Price</th>
            <th>Total Paid</th>
            <th>Refund Reason</th>
            <th>PIC</th>
            <th class="bg-info text-white">Action</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 0; $i < 5; $i++)
            <tr>
                <td>#</td>
                <td>Student/Partner/School Name</td>
                <td>Program Name</td>
                <td>Refund Date</td>
                <td>Total Price</td>
                <td>Total Paid</td>
                <td>Refund Reason</td>
                <td>PIC</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="refund({{ $i }})">
                        <i class="bi bi-plus"></i> Refund
                    </button>
                </td>
            </tr>
        @endfor
    </tbody>
    <tfoot class="bg-light text-white">
        <tr>
            <td colspan="7"></td>
        </tr>
    </tfoot>
</table>

<!-- Modal -->
<div class="modal fade" id="refund">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Refund</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="">
                    @method('post')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Total Price</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Total Paid</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Refund</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Refund Nominal</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="">Percentage Tax</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="">Tax Nominal</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="">Total Refund</label>
                            <input type="number" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>
                        <hr>
                        <div class="text-center d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-sm btn-primary">Save changes</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- Need Changing --}}
<script>
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
            }
        })
        // var table = $('#programTable').DataTable({
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
        //         left: 2,
        //         right: 1
        //     },
        //     processing: true,
        //     serverSide: true,
        //     ajax: '',
        //     columns: [{
        //             data: 'event_id',
        //             className: 'text-center',
        //             render: function(data, type, row, meta) {
        //                 return meta.row + meta.settings._iDisplayStart + 1;
        //             }
        //         },
        //         {
        //             data: 'event_title',
        //         },
        //         {
        //             data: 'event_location',
        //         },
        //         {
        //             data: 'event_startdate',
        //             render: function(data, type, row) {
        //                 let event_startdate = row.event_startdate ? moment(row
        //                     .event_startdate).format("MMMM Do YYYY HH:mm:ss") : '-'
        //                 return event_startdate
        //             }
        //         },
        //         {
        //             data: 'event_enddate',
        //             render: function(data, type, row) {
        //                 let event_enddate = row.event_enddate ? moment(row
        //                     .event_enddate).format("MMMM Do YYYY HH:mm:ss") : '-'
        //                 return event_enddate
        //             }
        //         },
        //         {
        //             data: '',
        //             className: 'text-center',
        //             defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning showEvent"><i class="bi bi-eye"></i></button>' +
        //                 '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteEvent"><i class="bi bi-trash2"></i></button>'
        //         }
        //     ]
        // });

        // realtimeData(table)

        // $('#programTable tbody').on('click', '.showEvent ', function() {
        //     var data = table.row($(this).parents('tr')).data();
        //     window.location.href = "{{ url('master/event') }}/" + data.event_id;
        // });

        // $('#programTable tbody').on('click', '.deleteEvent ', function() {
        //     var data = table.row($(this).parents('tr')).data();
        //     confirmDelete('master/event', data.event_id)
        // });

    });
</script>

{{-- Refund  --}}
<script>
    function refund(i) {
        $('#refund').modal('show')
    }
</script>
