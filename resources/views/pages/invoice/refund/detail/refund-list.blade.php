<table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
    <thead class="bg-secondary text-white">
        <tr>
            <th class="bg-info text-white">#</th>
            <th class="bg-info text-white">Student/Partner/School Name</th>
            <th>Program Name</th>
            <th>Invoice ID</th>
            <th>Total Price</th>
            <th>Refund Amount</th>
            <th>Tax Amount</th>
            <th>Total Refund</th>
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



<div class="modal fade" id="cancel_refund">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Refund</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="">
                    @method('post')
                    <div class="text-center">
                        <p>
                            Are you sure you want to cancel the refund?
                        </p>
                        <hr>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary">Yes, Cancel</button>
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
                    data: 'invoiceId',
                    className: 'text-center',
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
                    data: 'refund_amount',
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
                    data: 'tax_amount',
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
                    data: 'total_refunded',
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
                    data: 'id',
                    className: 'text-center',
                    render: function(data, type, row) {
                        switch (row.receipt_cat) {

                            case 'student':
                                var link = '{{ url('receipt/client-program/') }}/' + data
                                break;

                            case 'school':
                                var link = '{{ url('receipt/school-program/') }}/' + data
                                break;

                            case 'partner':
                                var link = '{{ url('receipt/corporate-program/') }}/' + data
                                break;

                            case 'referral':
                                var link = '{{ url('receipt/referral/') }}/' + data
                                break;

                        }
                        return '<a href="' + link +
                            '" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>'
                    }
                }
            ]
        };

        var table = initializeDataTable('#programTable', options, 'rt_refund');

    });
</script>

{{-- Cancel Refund  --}}
<script>
    function cancel_refund(i) {
        $('#cancel_refund').modal('show')
    }
</script>
