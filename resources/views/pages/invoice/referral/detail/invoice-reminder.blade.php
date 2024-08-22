<table class="table table-bordered table-hover nowrap align-middle w-100" id="invoiceListReferral">
    <thead class="bg-secondary text-white">
        <tr>
            <th class="bg-info text-white">#</th>
            <th class="bg-info text-white">Partner Name</th>
            <th>Program Name</th>
            <th>Invoice ID</th>
            <th>Payment Method</th>
            <th>Created At</th>
            <th>Due Date</th>
            <th>Total Price Other</th>
            <th>Total Price IDR</th>
            <th class="bg-info text-white">Action</th>
        </tr>
    </thead>
    <tfoot class="bg-light text-white">
        <tr>
            <td colspan="9"></td>
        </tr>
    </tfoot>
</table>


{{-- Need Changing --}}
<script>
    var widthView = $(window).width();
    $(document).ready(function() {

        var options = {
            order: [[5, 'desc']],
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
                    data: 'invb2b_num',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'partner_name',
                    name: 'tbl_corp.corp_name'
                },
                {
                    data: 'program_name',
                    name: 'tbl_referral.additional_prog_name'
                },
                {
                    data: 'invb2b_id',
                    className: 'text-center',
                },
                {
                    data: 'invb2b_pm',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data == "Full Payment" ?
                            '<i class="bi bi-wallet me-2 text-info"></i>' + data :
                            '<i class="bi bi-card-checklist me-2 text-warning"></i>' + data
                    }
                },
                {
                    data: 'created_at',
                    name: 'tbl_invb2b.created_at',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data ? moment(data).format("MMMM Do YYYY") : '-'
                    },
                },
                {
                    data: 'invb2b_duedate',
                    className: 'text-center',
                },
                {
                    data: 'invb2b_totprice',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        var currency;
                        var totprice = new Intl.NumberFormat().format(row.invb2b_totprice);
                        switch (row.currency) {
                            case 'usd':
                                currency = '$. ';
                                break;
                            case 'sgd':
                                currency = 'S$. ';
                                break;
                            case 'gbp':
                                currency = '£. ';
                                break;
                            default:
                                currency = '';
                                totprice = '-'
                                break;
                        }
                        return currency + totprice;
                    }
                },
                {
                    data: 'invb2b_totpriceidr',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        var currency = 'Rp. ';
                        var totprice = new Intl.NumberFormat().format(row.invb2b_totpriceidr);
                        return currency + ' ' + totprice;
                    }
                },
                {
                    data: '',
                    className: 'text-center',
                    defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning showInvoice"><i class="bi bi-eye"></i></button>'
                }
            ]
        };

        var table = initializeDataTable('#invoiceListReferral', options, 'rt_invoice_b2b');

        $('#invoiceListReferral tbody').on('click', '.showInvoice ', function() {
            var data = table.row($(this).parents('tr')).data();
            window.location.href = "{{ url('invoice/referral') }}/" + data.ref_id + "/detail/" + data
                .invb2b_num;
        });
    });
</script>
