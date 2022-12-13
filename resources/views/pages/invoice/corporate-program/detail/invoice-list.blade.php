            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Partner Name</th>
                        <th>Program Name</th>
                        <th>Invoice ID</th>
                        <th>Payment Method</th>
                        <th>Created At</th>
                        <th>Due Date</th>
                        <th>Total Price</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td>#</td>
                            <td>Partner Name</td>
                            <td>Program Name</td>
                            <td>Invoice ID</td>
                            <td>Payment Method</td>
                            <td>Created At</td>
                            <td>Due Date</td>
                            <td>Total Price</td>
                            <td class="text-center">
                                <a href="{{ url('invoice/corporate-program/1') }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-eye"></i>
                                </a>
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
