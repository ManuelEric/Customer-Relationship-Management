@extends('layout.main')

@section('title', 'Major')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Major
            </h5>
            <a href="#" class="btn btn-sm btn-info" onclick="resetForm()" data-bs-toggle="modal"
                data-bs-target="#majorForm"><i class="bi bi-plus-square me-1"></i> Add
                Major</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="majorTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Major Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
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

    <div class="modal fade" id="majorForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Major
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ route('major.store') }}" method="POST" id="formMajor">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Major Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="name" class="form-control form-control-sm rounded"
                                        required value="" id="major_name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="">Status</label>
                                <select name="active" id="major_status" class="form-select form-select-sm rounded">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                                <i class="bi bi-x-square me-1"></i>
                                Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save2 me-1"></i>
                                Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Need Changing --}}
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
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        render: function(data, type, row, meta) {
                            var badge = '';
                            if (row.active === 0)
                                badge =
                                '<span class="badge text-bg-danger" style="font-size:8px";>Inactive</span>';

                            if (row.active == 1 && row.created_at == Date.now())
                                badge =
                                '<span class="badge text-bg-success" style="font-size: 8px;">New</span>'

                            return data + ' ' + badge;
                        }
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
                        defaultContent: '<button data-bs-toggle="modal" data-bs-target="#majorForm" type="button" class="btn btn-sm btn-outline-warning editMajor"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteMajor"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#majorTable', options, 'rt_major');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Major')->first();
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

            $('#majorTable tbody').on('click', '.editMajor ', function() {
                var data = table.row($(this).parents('tr')).data();

                editById(data.id)
            });

            $('#majorTable tbody').on('click', '.deleteMajor ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/major', data.id)
            });
        });

        function resetForm() {
            $('#major_name').val(null)
            $('.put').html('')
            $('#formMajor').attr('action', '{{ url('master/major') }}')
        }

        function editById(id) {
            let link = "{{ url('master/major') }}/" + id
            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data.major

                    $('#major_name').val(data.name);
                    $("#major_status").val(data.active).change();

                    let html =
                        '@method('put')' +
                        '<input type="hidden" name="id" value="' + data.id + '">'

                    $('.put').html(html)

                    $('#formMajor').attr('action', '{{ url('master/major') }}/' + data.id + '')
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }
    </script>
@endsection
