            <table class="table table-bordered table-hover nowrap align-middle w-100" id="invoiceRef">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Partner Name</th>
                        <th>Program Name</th>
                        <th>Participant</th>
                        <th>Referral Date</th>
                        {{-- <th>Conversion Lead</th> --}}
                        <th>PIC</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                {{-- <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td>#</td>
                            <td>Partner Name</td>
                            <td>Program Name</td>
                            <td>Participant</td>
                            <td>Referral Date</td>
                            <td>Conversion Lead</td>
                            <td>PIC</td>
                            <td class="text-center">
                                <a href="{{ url('invoice/referral/create') }}"
                                    class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-plus"></i> Invoice
                                </a>
                            </td>
                        </tr>
                    @endfor
                </tbody> --}}
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
                    $('#cancel').click(function() {
                        $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
                    });

                    var options = {
                        order: [[4, 'desc']],
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
                                data: 'partner_name',
                                name: 'tbl_corp.corp_name'
                            },
                            {
                                data: 'program_name',
                                name: 'tbl_referral.additional_prog_name'
                            },
                            {
                                data: 'number_of_student',
                                className: 'text-center',
                                name: 'tbl_referral.number_of_student',
                            },
                            {
                                data: 'ref_date',
                                name: 'tbl_referral.ref_date',
                                className: 'text-center',
                                render: function(data, type, row) {
                                    let ref_date = data ? moment(data).format("MMMM Do YYYY") : '-'
                                    return ref_date
                                },
                            },
                            {
                                data: 'pic_name',
                                className: 'text-center',
                            },
                            {
                                data: '',
                                className: 'text-center',
                                defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning createInvRef"><i class="bi bi-plus"></i> Invoice</button>'
                            }
                        ]
                    };

                    var table = initializeDataTable('#invoiceRef', options, 'rt_referral');

                    $('#invoiceRef tbody').on('click', '.createInvRef ', function() {
                        var data = table.row($(this).parents('tr')).data();
                        console.log(data);
                        window.location.href = "{{ url('invoice/referral') }}/" + data.id + "/detail/create";

                    });

                });
            </script>
