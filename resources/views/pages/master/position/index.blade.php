@extends('layout.main')

@section('title', 'Position')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Position
            </h5>
            <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#positionForm"
            onclick="resetForm()"><i class="bi bi-plus-square me-1"></i> Add
            Position</a>
        </div>
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
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="positionTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Position Name</th>
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

    <div class="modal fade" id="positionForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Position
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ route('position.store') }}" method="POST" id="formPosition">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Position Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="position_name" id="position_name"
                                        class="form-control form-control-sm rounded" required value="">
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
                responsive: true,
                ajax: '',
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'position_name',
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
                        defaultContent: '<button type="button" data-bs-toggle="modal" data-bs-target="#positionForm" class="btn btn-sm btn-outline-warning editPosition"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deletePosition"><i class="bi bi-trash2"></i></button>'
                    }
                ],
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
            };
            var table = initializeDataTable('#positionTable', options, 'rt_position');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Position')->first();
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

            $('#positionTable tbody').on('click', '.editPosition ', function() {
                var data = table.row($(this).parents('tr')).data();
                editById(data.id)
            });

            $('#positionTable tbody').on('click', '.deletePosition ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/position', data.id)
            });
        });

        function resetForm() {
            $('#position_name').val(null)
            $('.put').html('')
            $('#formPosition').attr('action', '{{ url('master/position') }}')
        }

        function editById(id) {
            let link = "{{ url('master/position') }}/" + id

            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data.position
                    // console.log(data)

                    $('#position_name').val(data.position_name)
                    let html =
                        '@method('put')' +
                        '<input type="hidden" name="id" value="' + data.id + '">'

                    $('.put').html(html)

                    $('#formPosition').attr('action', '{{ url('master/position') }}/' + data.id + '')
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }
    </script>
@endsection
