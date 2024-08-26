@extends('layout.main')

@section('title', 'University Tags')

@section('content')    
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                University Tags
            </h5>
            <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#universityTagsForm"
                onclick="resetForm()"><i class="bi bi-plus-square me-1"></i> Add
                University Tag</a>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="tagTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Tags</th>
                        <th>Score</th>
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

    <div class="modal fade" id="universityTagsForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        University Tags
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ url('master/university-tags') }}" method="POST" id="formTag">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <div class="mb-2">
                                    <label for="">
                                        Tags Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="name" id="name"
                                        class="form-control form-control-sm rounded" required value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Score <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="number" name="score" id="score"
                                        class="form-control form-control-sm rounded" required value="{{ old('score') }}">
                                    @error('score')
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
                        data: 'score',
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
                        defaultContent: '<button type="button" data-bs-toggle="modal" data-bs-target="#universityTagsForm" class="btn btn-sm btn-outline-warning editTag"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteTag"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#tagTable', options, 'rt_tag');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'University Tag Score')->first();
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

            $('#tagTable tbody').on('click', '.editTag ', function() {
                var data = table.row($(this).parents('tr')).data();
                editById(data.id)
            });

            $('#tagTable tbody').on('click', '.deleteTag ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/university-tags', data.id)
            });
        });

        function resetForm() {
            $('#name').val(null)
            $('#score').val(null)
            $('.put').html('')
            $('#formTag').attr('action', '{{ url('master/university-tags') }}')
        }

        function editById(id) {
            let link = "{{ url('master/university-tags') }}/" + id + '/edit'

            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data.tag
                    console.log(data)

                    $('#name').val(data.name)
                    $('#score').val(data.score)
                    let html =
                        '@method('put')' +
                        '<input type="hidden" name="id" value="' + data.id + '">'

                    $('.put').html(html)

                    $('#formTag').attr('action', '{{ url('master/university-tags') }}/' + data.id + '')
                })
                .catch(function(error) {
                    // handle error
                    console.error(error);
                })
        }
    </script>

    @if ($errors->has('name') || $errors->has('score'))
        <script>
            $(document).ready(function() {
                $('#universityTagsForm').modal('show');
            })
        </script>
    @endif
@endsection
