<div class="card mb-3">
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover" id="outstandingTable">
            <thead class="text-center" id="thead-finance">
                <tr>
                    <th>No</th>
                    <th>Client Name</th>
                    <th>Reminder</th>
                    <th>Invoice ID</th>
                    <th>Type</th>
                    <th>Program Name</th>
                    <th>Installment</th>
                    <th>Invoice Duedate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <td colspan="9"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#assetTable').DataTable({
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
                left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                right: 1
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
                },
                {
                    data: '',
                    render: function(data, type, row, meta) {
                        if (row.typeprog == "client_prog") {
                            return '<button data-bs-toggle="modal" data-bs-target="#reminderModal" class="mx-1 btn btn-sm btn-outline-success reminder">' +
                                        '<i class="bi bi-whatsapp"></i>' +
                                    '</button>';
                        }
                    }
                },
                {
                    data: 'invoice_id',
                },
                {
                    data: 'type',
                    className: 'text-center'
                },
                {
                    data: 'program_name',
                },
                {
                    data: 'installment_name',
                    className: 'text-center',
                },
                {
                    data: 'invoice_duedate',
                    render: function (data, type, row, meta) {
                        return moment().format('MM dd, YYYY');
                    }
                },
                {
                    data: 'total',
                    render: function (data, type, row, meta) {
                        return new Intl.NumberFormat("id-ID", {style: "currency", currency: "IDR"}).format(data);
                    }
                }
            ],
            pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
        });

        realtimeData(table)

        $("#outstandingTable tbody").on('click', '.reminder', function() {
            var data = table.row($(this).parents('tr')).data();
            
            console.log(data);
            return;

            $('#client_id').val()
            $('#fullname').val()
            $('#program_name').val()
            $('#invoice_duedate').val()
            $('#total_payment').val()
            $('#clientprog_id').val()
            $('#payment_method').val()
            $('#parent_id').val()
            $('#client_id').val()
        });
    });
</script>
@endpush