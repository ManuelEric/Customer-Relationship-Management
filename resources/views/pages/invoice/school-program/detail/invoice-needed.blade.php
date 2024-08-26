            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">School Name</th>
                        <th>Program Name</th>
                        <th>Program Success Date</th>
                        <th>PIC</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
            </table>


            {{-- Need Changing --}}
            <script>
                var widthView = $(window).width();
                $(document).ready(function() {
                    $('#cancel').click(function() {
                        $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
                    });

                    var options = {
                        order: [[3, 'desc']],
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
                                data: 'school_name',
                                name: 'tbl_sch.sch_name'
                            },
                            {
                                data: 'program_name',
                                name: 'program.program_name'
                            },
                            {
                                data: 'success_date',
                                className: 'text-center',
                                render: function(data, type, row) {
                                    let success_date = data ? moment(data).format("MMMM Do YYYY") : '-'
                                    return success_date
                                },
                            },
                            {
                                data: 'pic_name',
                                className: 'text-center',
                            },
                            {
                                data: '',
                                className: 'text-center',
                                defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning createInvSch"><i class="bi bi-plus"></i> Invoice</button>'
                            }
                        ]
                    };

                    var table = initializeDataTable('#programTable', options, 'rt_school_program');

                    $('#programTable tbody').on('click', '.createInvSch ', function() {
                        var data = table.row($(this).parents('tr')).data();
                        window.location.href = "{{ url('invoice/school-program') }}/" + data.id + "/detail/create";

                    });

                });
            </script>
