@extends('layout.main')

@section('title', 'Curriculum')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Curriculum
            </h5>
            <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#curriculumForm"
            onclick="resetForm()"><i class="bi bi-plus-square me-1"></i> Add
            Curriculum</a>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="curriculumTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Curriculum Name</th>
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

    <div class="modal fade" id="curriculumForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Curriculum
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ url('master/curriculum') }}" method="POST" id="formCurriculum">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Curriculum Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="name" id="name"
                                        class="form-control form-control-sm rounded" required value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
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
                order: [[3, 'desc']],
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
                        defaultContent: '<button type="button" data-bs-toggle="modal" data-bs-target="#curriculumForm" class="btn btn-sm btn-outline-warning editcurriculum"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deletecurriculum"><i class="bi bi-trash2"></i></button>'
                    }
                ],
            };

            var table = initializeDataTable('#curriculumTable', options, 'rt_curriculum');

            $('#curriculumTable tbody').on('click', '.editcurriculum ', function() {
                var data = table.row($(this).parents('tr')).data();
                editById(data.id)
            });

            $('#curriculumTable tbody').on('click', '.deletecurriculum ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/curriculum', data.id)
            });

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Curriculum')->first();
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
        });

        function resetForm() {
            $('#name').val(null)
            $('.put').html('')
            $('#formCurriculum').attr('action', '{{ url('master/curriculum') }}')
        }

        function editById(id) {
            let link = "{{ url('master/curriculum') }}/" + id + '/edit'

            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data.curriculum

                    $('#name').val(data.name)
                    let html =
                        '@method('put')' +
                        '<input type="hidden" name="id" value="' + data.id + '">'

                    $('.put').html(html)

                    $('#formCurriculum').attr('action', '{{ url('master/curriculum') }}/' + data.id + '')
                })
                .catch(function(error) {
                    // handle error
                    console.error(error);
                })
        }
    </script>

    @if ($errors->has('name'))
        <script>
            $(document).ready(function() {
                $('#curriculumForm').modal('show');
            })
        </script>
    @endif
@endsection
