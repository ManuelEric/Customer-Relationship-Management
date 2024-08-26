            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Client Name</th>
                        <th>Program Name</th>
                        <th>Program Success Date</th>
                        <th>Conversion Lead</th>
                        <th>PIC</th>
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
                        order: [[3, 'desc']],
                        buttons: [
                            'pageLength', {
                                extend: 'excel',
                                text: 'Export to Excel',
                                exportOptions: {
                                    format: {
                                        body: function (data, row, column, node){
                                            var clearHtml = '';
                                            var result = '';
                                            if(column === 2){
                                                clearHtml = data.replace(/<[^>]*>?/gm, '');
                                                if (clearHtml.indexOf('{}') === -1) {
                                                    result = clearHtml.replace(/{.*}/, '');
                                                }
                                            }else if(column === 6){
                                                result = data.replace(/<[^>]*>?/gm, '');
                                            }else{
                                                result = data;
                                            }
                                            return result;
                                        }
                                    }
                                },
                            }
                        ],
                        fixedColumns: {
                            left: (widthView < 768) ? 1 : 2,
                            right: 1
                        },
                        ajax: '',
                        columns: [{
                                data: 'clientprog_id',
                                className: 'text-center',
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'fullname',
                            },
                            {
                                data: 'program_name',
                                render: function(data, type, row, meta) {
                                    var bundling_id = invoice_alert = null;
                                    if(row.bundling_id !== null){
                                        bundling_id = row.bundling_id.substring(0, 3).toUpperCase();
                                    }

                                    if(row.count_invoice > 0){
                                        invoice_alert = 'animated-gradient';
                                    }
                                    return row.is_bundle > 0 ? data + ' <span class="badge text-bg-success '+ invoice_alert +'" style="font-size:8px";>{Bundle '+ bundling_id +'}</span>' : data;
                                }
                            },
                            {
                                data: 'success_date',
                                className:'text-center',
                                render: function(data, type, row) {
                                    let success_date = data ? moment(data).format("MMMM Do YYYY") : '-'
                                    return success_date
                                }
                            },
                            {
                                data: 'conversion_lead',
                                className:'text-center',
                            },
                            {
                                data: 'pic_name',
                                className:'text-center',
                            },
                            {
                                data: 'clientprog_id',
                                className: 'text-center',
                                render: function(data, type, row) {
                                    var link = "{{ url('invoice/client-program/create') }}?prog=" + row.clientprog_id

                                    return '<a href="' + link + '" class="btn btn-sm btn-outline-warning">' +
                                    '<i class="bi bi-plus"></i> Invoice</a>'
                                }
                            }
                        ]
                    };

                    var table = initializeDataTable('#programTable', options, 'rt_client_program');

                });
            </script>
