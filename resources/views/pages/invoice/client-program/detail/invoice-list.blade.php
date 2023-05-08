            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-dark text-white">
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
                        },
                        processing: true,
                        serverSide: true,
                        ajax: '',
                        columns: [{
                                data: 'clientprog_id',
                                className: 'text-center',
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'fullname'
                            },
                            {
                                data: 'program_name'
                            },
                            {
                                data: 'inv_id',
                                name: 'tbl_inv.inv_id'
                            },
                            {
                                data: 'inv_paymentmethod',
                                name: 'tbl_inv.inv_paymentmethod'
                            },
                            {
                                data: 'created_at',
                                render: function(data, type, row) {
                                    return moment(data).format('MMMM Do YYYY')
                                }
                            },
                            {
                                data: 'inv_duedate',
                                name: 'tbl_inv.inv_duedate',
                                render: function(data, type, row) {
                                    return moment(data).format('MMMM Do YYYY')
                                }
                            },
                            {
                                data: 'inv_totalprice_idr',
                                name: 'tbl_inv.inv_totalprice_idr',
                                render: function(data, type, row) {
                                    return new Intl.NumberFormat("id-ID", {
                                        style: "currency",
                                        currency: "IDR"
                                    }).format(data);

                                }
                            },
                            {
                                data: 'clientprog_id',
                                className: 'text-center',
                                render: function(data, type, row) {
                                    var link = "{{ url('invoice/client-program') }}/" + row.clientprog_id

                                    return '<a href="' + link + '" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>'
                                }
                            }
                        ]
                    })

                });
            </script>
