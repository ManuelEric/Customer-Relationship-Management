@extends('layout.main')

@section('title', 'Subject')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Subject
            </h5>
            <a href="#" class="btn btn-sm btn-info" onclick="resetForm()" data-bs-toggle="modal"
                data-bs-target="#subjectForm"><i class="bi bi-plus-square me-1"></i> Add
                Subject</a>
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
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="subjectTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Subject Name</th>
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

    <div class="modal fade" id="subjectForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Subject
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ route('subject.store') }}" method="POST" id="formSubject">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Subject Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="name" class="form-control form-control-sm rounded"
                                        required value="" id="subject_name">
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
                        defaultContent: '<button data-bs-toggle="modal" data-bs-target="#subjectForm" type="button" class="btn btn-sm btn-outline-warning editSubject"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteSubject"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#subjectTable', options, 'rt_subject');
            
            @php
                $privilage = $menus['Master']->where('submenu_name', 'Subject')->first();
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

            $('#subjectTable tbody').on('click', '.editSubject ', function() {
                var data = table.row($(this).parents('tr')).data();

                editById(data.id)
            });

            $('#subjectTable tbody').on('click', '.deleteSubject ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/subject', data.id)
            });
        });

        function resetForm() {
            $('#subject_name').val(null)
            $('.put').html('')
            $('#formSubject').attr('action', '{{ url('master/subject') }}')
        }

        function editById(id) {
            let link = "{{ url('master/subject') }}/" + id
            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data.subject

                    $('#subject_name').val(data.name);

                    let html =
                        '@method('put')' +
                        '<input type="hidden" name="id" value="' + data.id + '">'

                    $('.put').html(html)

                    $('#formSubject').attr('action', '{{ url('master/subject') }}/' + data.id + '')
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }
    </script>
@endsection
