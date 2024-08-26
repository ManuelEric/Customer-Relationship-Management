<div class="card mb-3">
    <div class="card-body">
        <table class="table table-bordered table-hover nowrap align-middle w-100" id="outstandingTable">
            <thead class="bg-secondary text-white">
                <tr>
                    <th class="bg-info text-white">#</th>
                    <th class="bg-info text-white">Client Name</th>
                    <th>Reminder</th>
                    <th>Invoice ID</th>
                    <th>Type</th>
                    <th>Program Name</th>
                    <th>Installment</th>
                    <th>Invoice Duedate</th>
                    <th>Amount</th>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            const bearer_token = `Bearer {{ Session::get('access_token') }}`;

            var widthView = $(window).width();
            var table = $('#outstandingTable').DataTable({
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
                scrollX: (widthView < 768) ? true : false,
                fixedColumns: {
                    left: (widthView < 768) ? 1 : 2,
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url("/") }}/api/get/outstanding-payment',
                    // beforeSend : function( xhr ) {
                    //     xhr.setRequestHeader( 'Authorization', bearer_token);
                    // },
                },
                columns: [{
                        data: 'id',
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'full_name',
                    },
                    {
                        data: 'typeprog',
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data == "client_prog") {
                                return '<button data-bs-toggle="modal" data-bs-target="#reminderModal" class="mx-1 btn btn-sm btn-outline-success reminder">' +
                                    '<i class="bi bi-whatsapp"></i>' +
                                    '</button>';
                            }

                            return null;
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
                        render: function(data, type, row, meta) {
                            return moment().format('MMMM D, YYYY');
                        }
                    },
                    {
                        data: 'total',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR"
                            }).format(data);
                        }
                    }
                ],
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
            });

            $("#outstandingTable tbody").on('click', '.reminder', function() {
                var data = table.row($(this).parents('tr')).data();

                $("#phone").val(data.parent_phone);
                $('#client_id').val(data.client_id);
                $('#fullname').val(data.full_name);
                $('#program_name').val(data.program_name);
                $('#invoice_duedate').val(data.invoice_duedate);
                $('#total_payment').val(data.total);
                $('#clientprog_id').val(data.client_prog_id);
                $('#payment_method').val(data.installment_name);
                $('#parent_id').val(data.parent_id);
            });
        });
    </script>
@endpush
