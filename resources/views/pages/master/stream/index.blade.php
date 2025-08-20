@extends('layout.main')

@section('title', 'Subject')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Stream
            </h5>
            <a href="#" class="btn btn-sm btn-info" onclick="resetForm()" data-bs-toggle="modal"
                data-bs-target="#streamForm"><i class="bi bi-plus-square me-1"></i> Add
                Stream</a>
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
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="streamTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center">
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Stream Name</th>
                        <th>Active</th>
                        <th>Created At</th>
                        <th>Updated At</th>
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

    <div class="modal fade" id="streamForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Stream
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ route('stream.store') }}" method="POST" id="formStream">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Stream Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="stream_name" class="form-control form-control-sm rounded"
                                        required value="" id="stream_name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Active
                                    </label>
                                    <select name="is_active" class="form-select form-select-sm rounded">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
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
                order: [[2, 'desc']],
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
                        data: 'stream_name',
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
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
                        defaultContent: '<button data-bs-toggle="modal" data-bs-target="#streamForm" type="button" class="btn btn-sm btn-outline-warning editStream"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteStream"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#streamTable', options, 'rt_stream');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Stream')->first();
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

            $('#streamTable tbody').on('click', '.editStream ', function() {
                var data = table.row($(this).parents('tr')).data();

                editById(data.id)
            });

            $('#streamTable tbody').on('click', '.deleteStream ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/stream', data.id)
            });
        });

        function resetForm() {
            $('#stream_name').val(null)
            $('.put').html('')
            $('#formStream').attr('action', '{{ url('master/stream') }}')
        }

        function editById(id) {
            let link = "{{ url('master/stream') }}/" + id
            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data.stream

                    $('#stream_name').val(data.stream_name);

                    let html =
                        '@method('put')' +
                        '<input type="hidden" name="id" value="' + data.id + '">'

                    $('.put').html(html)

                    $('#formStream').attr('action', '{{ url('master/stream') }}/' + data.id + '')
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }
    </script>
@endsection
