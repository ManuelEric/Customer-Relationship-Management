            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Client Name</th>
                        <th>Program Name</th>
                        <th>Invoice ID</th>
                        <th>Payment Method</th>
                        <th>Created At</th>
                        <th>Due Date</th>
                        <th>Total Price</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            </table>


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
                                data: 'uuid',
                                className: 'text-center',
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'fullname'
                            },
                            {
                                data: 'program_name',
                            },
                            {
                                data: 'inv_id',
                                className:'text-center',
                                name: 'tbl_inv.inv_id'
                            },
                            {
                                data: 'inv_paymentmethod',
                                className:'text-center',
                                name: 'tbl_inv.inv_paymentmethod',
                                render: function(data, type, row) {
                                    return data=="Full Payment" ? '<i class="bi bi-wallet me-2 text-info"></i>' + data : '<i class="bi bi-card-checklist me-2 text-warning"></i>' + data
                                }
                            },
                            {
                                data: 'created_at',
                                name: 'created_at',
                                className:'text-center',
                                render: function(data, type, row) {
                                    return moment(data).format('MMMM Do YYYY')
                                }
                            },
                            {
                                data: 'inv_duedate',
                                name: 'tbl_inv.inv_duedate',
                                className:'text-center',
                                render: function(data, type, row) {
                                    return moment(data).format('MMMM Do YYYY')
                                }
                            },
                            {
                                data: 'inv_totalprice_idr',
                                name: 'tbl_inv.inv_totalprice_idr',
                                className:'text-center',
                                render: function(data, type, row) {
                                    return new Intl.NumberFormat("id-ID", {
                                        style: "currency",
                                        currency: "IDR",
                                        minimumFractionDigits: 0
                                    }).format(data);

                                }
                            },
                            {
                                data: 'uuid',
                                className: 'text-center',
                                render: function(data, type, row) {
                                    var link = "{{ url('invoice/client-program/bundle') }}/" + row.uuid

                                    return '<a href="' + link + '" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>'
                                }
                            }
                        ]
                    };

                    var table = initializeDataTable('#programTable', options, 'rt_invoice_b2c');

                });
            </script>
