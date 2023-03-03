@extends('layout.main')

@section('title', 'Referral - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Referral
        </a>
        <a href="{{ url('program/referral/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i>
            Add
            Referral</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="refTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Partner Name</th>
                        <th>Type</th>
                        <th>Program Name</th>
                        <th>Participants</th>
                        <th>Amount</th>
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
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#refTable').DataTable({
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
                        data: 'referral_type',
                    },
                    {
                        data: 'program_name',
                        name: 'tbl_prog.prog_program',
                        render: function(data, type, row, meta) {
                            return row.referral_type == "Out" ? row.additional_prog_name : row.program_name
                        }
                    },
                    {
                        data: 'number_of_student',
                    },
                    {
                        data: 'revenue',
                        render: function(data, type, row, meta) {
                            switch (row.currency) {
                                case "USD":
                                    return formatUsd(row.revenue)
                                    break;

                                case "IDR":
                                    return formatRupiah(row.revenue)
                                    break;
                                
                                case "SGD":
                                    return 'S' + formatSingUsd(row.revenue)
                                    break;
                            }
                        }
                    },
                    {
                        data: 'pic_name',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning editRef"><i class="bi bi-eye"></i></button>'
                    }
                ]
            });

            $('#refTable tbody').on('click', '.editRef ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('program/referral') }}/" + data.id;
            });

            const formatRupiah = (money) => {
                return new Intl.NumberFormat('id-ID',
                    { style: 'currency', currency: 'IDR' }
                ).format(money);
            }

            const formatUsd = (money) => {
                return new Intl.NumberFormat('en-US',
                    { style: 'currency', currency: 'USD' }
                ).format(money)
            }

            const formatSingUsd = (money) => {
                return new Intl.NumberFormat('en-SG',
                    { style: 'currency', currency: 'SGD' }
                ).format(money)
            }
        });
    </script>

@endsection
