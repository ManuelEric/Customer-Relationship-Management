@extends('layout.main')

@section('title', 'List of Partner')

@section('content')

    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Partners
            </h5>
            <a href="{{ url('instance/corporate/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
                Add
                Partner</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="corporateTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Corporate Name</th>
                        <th>Industry</th>
                        <th>Email</th>
                        <th>Office Number</th>
                        <th>Type</th>
                        <th>Country Type</th>
                        <th>Partnership Type</th>
                        <th>Region</th>
                        <th>Address</th>
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

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var options = {
                order: [[1, 'asc']],
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
                        data: 'corp_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'corp_name',
                    },
                    {
                        data: 'corp_industry',
                        render: function(data, type, row, meta) {
                            return data == null ? '-' : data
                        }
                    },
                    {
                        data: 'corp_mail',
                    },
                    {
                        data: 'corp_phone',
                    },
                    {
                        data: 'type',
                    },
                    {
                        data: 'country_type',
                    },
                    {
                        data: 'partnership_type',
                        render: function(data, type, row, meta) {
                            return data == null ? '-' : data
                        }
                    },
                    {
                        data: 'corp_region',
                        render: function(data, type, row, meta) {
                            return data == null ? '-' : data
                        }
                    },
                    {
                        data: 'corp_address',
                        render: function(data, type, row, meta) {
                            return data == null ? '-' : data
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editCorporate" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="More Detail"><i class="bi bi-eye"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#corporateTable', options, 'rt_partner');

            $('#corporateTable tbody').on('click', '.editCorporate ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.open("{{ url('instance/corporate') }}/" + data.corp_id.toLowerCase(), "_blank");
            });

            // Tooltip 
            $('#corporateTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });
        });
    </script>
@endsection
