@extends('layout.main')

@section('title', 'Referral ')

@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Referral
            </h5>
            <a href="{{ url('program/referral/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
                Add
                Referral</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="refTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Partner Name</th>
                        <th>Type</th>
                        <th>Program Name</th>
                        <th>Participants</th>
                        <th>Amount</th>
                        <th>Amount other</th>
                        <th>PIC</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="8"></td>
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
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
                search: {
                    return: true
                },
                processing: true,
                serverSide: true,
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
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
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return data=="Out" ? data + '<i class="bi bi-box-arrow-right text-danger ms-2"></i>' : data + '<i class="bi bi-box-arrow-in-right text-success ms-2"></i>'
                        }
                    },
                    {
                        data: 'program_name',
                        name: 'program.prog_program',
                        render: function(data, type, row, meta) {
                            return row.referral_type == "Out" ? row.additional_prog_name : row
                                .program_name
                        }
                    },
                    {
                        data: 'number_of_student',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<i class="bi bi-person text-success me-2"></i>' + data 
                        }
                    },
                    {
                        data: 'revenue',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return formatRupiah(row.revenue)
                        }
                    },
                    {
                        data: 'revenue_other',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (row.currency) {
                                case "USD":
                                    return formatUsd(row.revenue_other)
                                    break;

                                case "SGD":
                                    return 'S' + formatSingUsd(row.revenue_other)
                                    break;

                                case "GBP":
                                    return formatGbp(row.revenue_other)
                                    break;

                                case "IDR":
                                    return '-'
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

            @php
                $privilage = $menus['Program']->where('submenu_name', 'Referral')->first();
            @endphp

            @if ($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false");

                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif

            @if ($privilage['export'] == 0)
                table.button(1).disable();
            @endif

            $('#refTable tbody').on('click', '.editRef ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('program/referral') }}/" + data.id;
            });

            const formatRupiah = (money) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(money);
            }

            const formatUsd = (money) => {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(money)
            }

            const formatSingUsd = (money) => {
                return new Intl.NumberFormat('en-SG', {
                    style: 'currency',
                    currency: 'SGD'
                }).format(money)
            }

            const formatGbp = (money) => {
                return new Intl.NumberFormat('en-GB', {
                    style: 'currency',
                    currency: 'GBP'
                }).format(money)
            }

        });
    </script>

@endsection
