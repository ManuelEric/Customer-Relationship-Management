@extends('layout.main')

@section('title', 'Major - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Major
        </a>
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#leadForm"><i
                class="bi bi-plus-square me-1"></i> Add
            Major</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="majorTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="bg-info text-white">Major Name</th>
                        <th class="bg-info text-white">Created At</th>
                        <th class="bg-info text-white">Updated At</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @include('pages.major.create')

    @include('pages.major.update')

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var table = $('#majorTable').DataTable({
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
                        data: 'name'
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                    },
                    {
                        data: 'updated_at',
                        className: 'text-center',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-warning editMajor"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-danger ms-1 deleteMajor"><i class="bi bi-trash"></i></button>'
                    }
                ]
            });

            $('#majorTable tbody').on('click', '.editMajor ', function() {
                var data = table.row($(this).parents('tr')).data();

                var element = "#majorFormUpdate"

                $(element).modal('show');
                var action = "{{ url('master/major') }}" + "/" + data.id
                $(element).find('form').attr('action', action)
                $(element).find('form input[name=name]').val(data.name)
                $(element).find('form input[name=id]').val(data.id)
            });

            $('#majorTable tbody').on('click', '.deleteMajor ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/major', data.id)
            });
        });
    </script>
@endsection
