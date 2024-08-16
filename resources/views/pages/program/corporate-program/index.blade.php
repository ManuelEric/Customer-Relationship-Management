@extends('layout.main')

@section('title', 'Partner Program ')
@push('styles')
    <style>
        @media only screen and (max-width: 600px) {
            .filter-partnerprog {
                width: 300px !important;
            }
        }
    </style>
@endpush
@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Partner Program
            </h5>

            <div class="dropdown">
                <button href="#" class="btn btn-sm btn-light text-dark dropdown-toggle" data-bs-toggle="dropdown"
                    data-bs-auto-close="false" id="filter">
                    <i class="bi bi-funnel me-2"></i> Filter
                </button>
                <form action="{{ route('program.corporate.index') }}" class="dropdown-menu dropdown-menu-end pt-0 shadow filter-partnerprog"
                    style="width: 400px" method="GET">
                    <div class="dropdown-header bg-info text-dark py-2 d-flex justify-content-between">
                        Advanced Filter
                        <i class="bi bi-search"></i>
                    </div>
                    <div class="row p-3">
                        <div class="col-md-12 mb-2">
                            <label for="">Partner Name</label>
                            <select name="partner_name[]" id="" class="select form-select form-select-sm w-100"
                                multiple>
                                @foreach ($partners as $partner)
                                    <option value="{{ $partner->corp_name }}">{{ $partner->corp_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Program Name</label>
                            <select name="program_name[]" id="" class="select form-select form-select-sm w-100"
                                multiple>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->program_name }}">{{ $program->program_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="row g-2">
                                <div class="col-md-6 mb-2">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Approach Status</label>
                            <select name="status[]" id="" class="select form-select form-select-sm w-100" multiple>
                                <option value="0">Pending</option>
                                <option value="4">Accepted</option>
                                <option value="5">Cancel</option>
                                <option value="2">Rejected</option>
                                <option value="1">Success</option>
                                <option value="3">Refund</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">PIC</label>
                            <select name="pic[]" id="" class="select form-select form-select-sm w-100" multiple>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->first_name }}
                                        {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-sm btn-outline-danger" id="cancel">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-outline-success">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="partnerProgTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Partner Name</th>
                        <th>Program Name</th>
                        <th>First Discuss</th>
                        <th>Participants</th>
                        <th>Total</th>
                        <th>Approach Status</th>
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
            $('#cancel').click(function() {
                $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
            });

            var options = {
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                fixedColumns: {
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
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
                        data: 'corp_name',
                        name: 'tbl_corp.corp_name'
                    },
                    {
                        data: 'program_name',
                        name: 'program.program_name'
                    },
                    {
                        data: 'first_discuss',
                        className: 'text-center',
                    },
                    {
                        data: 'participants',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(row.status)) {
                                case 1:
                                    return '<i class="bi bi-person me-2"></i>' + data
                                    break;

                                default:
                                    return "-"
                                    break;
                            }
                        }
                    },
                    {
                        data: 'total_fee',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(row.status)) {
                                case 1:
                                    return new Intl.NumberFormat("id-ID", {
                                        style: "currency",
                                        currency: "IDR"
                                    }).format(data);
                                    break;

                                default:
                                    return "-"
                                    break;
                            }
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(row.status)) {
                                case 0:
                                    return "<i class='bi bi-clock me-2 text-warning'></i> Pending"
                                    break;

                                case 1:
                                    return "<i class='bi bi-check me-2 text-success'></i> Success"
                                    break;

                                case 2:
                                    return "<i class='bi bi-x me-2 text-danger'></i> Rejected"
                                    break;

                                case 3:
                                    return "<i class='bi bi-arrow-counterclockwise me-2 text-info'></i> Refund"
                                    break;

                                case 4:
                                    return "<i class='bi bi-check-circle me-2 text-success'></i> Accepted"
                                    break;

                                case 5:
                                    return "<i class='bi bi-x-circle me-2 text-danger'></i> Cancel"
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
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning editSchProg"><i class="bi bi-eye"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#partnerProgTable', options, 'rt_partner_program');

            @php
                $privilage = $menus['Program']->where('submenu_name', 'Partner Program')->first();
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

            realtimeData(table)

            $('#partnerProgTable tbody').on('click', '.editSchProg', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('program/corporate') }}/" + data.corp_id.toLowerCase() +
                    "/detail/" + data.id;
            });


        });
    </script>
@endsection
