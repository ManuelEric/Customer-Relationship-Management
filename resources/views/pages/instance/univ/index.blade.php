@extends('layout.main')

@section('title', 'List of Universitiy')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Universities
            </h5>
            <a href="{{ url('instance/university/create') }}" class="btn btn-sm btn-info"><i
                    class="bi bi-plus-square me-1"></i>
                Add
                University</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="univTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">University ID</th>
                        <th>University Name</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Tag</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
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
                        data: 'univ_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'univ_id',
                    },
                    {
                        data: 'univ_name',
                    },
                    {
                        data: 'univ_address',
                    },
                    {
                        data: 'univ_email',
                    },
                    {
                        data: 'univ_phone',
                    },
                    {
                        data: 'univ_country',
                    },
                    {
                        data: 'tag_name',
                        name: 'tbl_tag.name'
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning editUniv" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="More Detail"><i class="bi bi-eye"></i></button>'
                    }
                ]
            };
            
            var table = initializeDataTable('#univTable', options, 'rt_university');

            $('#univTable tbody').on('click', '.editUniv ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.open("{{ url('instance/university') }}/" + data.univ_id.toLowerCase(), "_blank");
            });

            // Tooltip 
            $('#univTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });
        });
    </script>

@endsection
