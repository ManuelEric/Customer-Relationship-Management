<table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
    <thead class="bg-secondary text-white">
        <tr>
            <th class="bg-info text-white">#</th>
            <th class="bg-info text-white">Student/Partner/School Name</th>
            <th>Program Name</th>
            <th>Refund Date</th>
            <th>Total Price</th>
            <th>Total Paid</th>
            <th>Refund Reason</th>
            <th>Refund Notes</th>
            <th>PIC</th>
            <th class="bg-info text-white">Action</th>
        </tr>
    </thead>
    <tbody></tbody>
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
    var widthView = $(window).width();
    $(document).ready(function() {

        var options = {
            buttons: [
                'pageLength', {
                    extend: 'excel',
                    text: 'Export to Excel',
                }
            ],
            fixedColumns: {
                left: (widthView < 768) ? 1 : 2,
                right: 1
            },
            ajax: '',
            columns: [{
                    data: 'id',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'client_fullname',
                },
                {
                    data: 'program_name',
                },
                {
                    data: 'refund_date',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return moment(data).format('DD MMMM YYYY')
                    }
                },
                {
                    data: 'total_price',
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 0
                        }).format(data);

                    }
                },
                {
                    data: 'total_paid',
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 0
                        }).format(data);

                    }
                },
                {
                    data: 'refund_reason',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'refund_notes',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'pic_name',
                    className: 'text-center',
                },
                {
                    data: 'id',
                    className: 'text-center',
                    render: function(data, type, row) {
                        switch (row.receipt_cat) {

                            case "student":
                                return '<a href="{{ url('invoice/client-program/') }}/' + row
                                    .b2prog_id +
                                    '" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>'
                                break;

                            case "school":
                                return '<a href="{{ url('invoice/school-program/') }}/' + row
                                    .b2prog_id + '/detail/' + row.invoiceNum +
                                    '" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>'
                                break;

                            case "partner":
                                return '<a href="{{ url('invoice/corporate-program/') }}/' + row
                                    .b2prog_id + '/detail/' + row.invoiceNum +
                                    '" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>'
                                break;

                        }
                    }
                }
            ]
        };

        var table = initializeDataTable('#programTable', options, 'rt_receipt');

    });
</script>

{{-- Refund  --}}
<script>
    function refund(i) {
        $('#refund').modal('show')
    }
</script>
