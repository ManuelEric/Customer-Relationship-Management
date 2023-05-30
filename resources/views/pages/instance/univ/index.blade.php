@extends('layout.main')

@section('title', 'University - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> University
        </a>
        <a href="{{ url('instance/university/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i>
            Add
            University</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="univTable">
                <thead class="bg-dark text-white">
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
            var table = $('#univTable').DataTable({
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
                processing: true,
                serverSide: true,
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
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning editUniv"><i class="bi bi-eye"></i></button>'
                    }
                ]
            });

            $('#univTable tbody').on('click', '.editUniv ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('instance/university') }}/" + data.univ_id.toLowerCase();
            });
        });
    </script>

@endsection
