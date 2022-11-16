@extends('layout.main')

@section('title', 'University - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> University
        </a>
        <a href="#" class="btn btn-sm btn-primary" onclick="resetForm()" data-bs-toggle="modal"
            data-bs-target="#univForm"><i class="bi bi-plus-square me-1"></i> Add
            University</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="univTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="bg-info text-white">University ID</th>
                        <th class="bg-info text-white">University Name</th>
                        <th>Address</th>
                        <th>Country</th>
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

    {{-- Create and Updated  --}}
    <!-- Modal -->
    <div class="modal fade" id="univForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        University
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ url('master/university') }}" method="POST" id="formUniv">
                        @csrf
                        <div class="put"></div>
                        <div class="mb-2">
                            <label for="">
                                University Name
                            </label>
                            <input type="text" name="univ_name" id="univ_name"
                                class="form-control form-control-sm rounded" required value="{{ old('univ_name') }}">
                        </div>
                        <div class="mb-2">
                            <label for="">
                                Country
                            </label>
                            <div class="w-100">
                                <select name="univ_country" id="univ_country" class="w-100">
                                    <option data-placeholder="true"></option>
                                    @foreach ($countries as $item)
                                        <option value="{{ $item->name }}"
                                            {{ old('univ_country') == $item->name ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="">
                                Address
                            </label>
                            <textarea name="univ_address" id="univ_address" class="form-control form-control-sm rounded" style="height: 300px">{{ old('univ_address') }}</textarea>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ url('master/university') }}" class="btn btn-outline-danger btn-sm"
                                data-bs-dismiss="{{ isset($university) ? '' : 'modal' }}">
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
                    left: 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'id',
                        className: 'text-center',
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
                        data: 'univ_country',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" data-bs-toggle="modal" data-bs-target="#univForm" class="btn btn-sm btn-outline-warning editUniv"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteUniv"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            });

            $('#univTable tbody').on('click', '.editUniv ', function() {
                var data = table.row($(this).parents('tr')).data();
                editById(data.univ_id)
            });

            $('#univTable tbody').on('click', '.deleteUniv ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/university', data.id)
            });
        });
    </script>
    <script>
        // Select2 Modal 
        $(document).ready(function() {
            $('#univ_country').select2({
                dropdownParent: $('#univForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });

        function resetForm() {
            $('#univ_name').val(null)
            $('#univ_country').val(null).trigger('change')
            tinyMCE.get('univ_address').setContent('');
            $('.put').html('')
            $('#formUniv').attr('action', '{{ url('master/university') }}')
        }

        function editById(id) {
            let link = "{{ url('master/university') }}/" + id.toLowerCase()
            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data.university
                    // console.log(data)

                    $('#univ_name').val(data.univ_name)
                    $('#univ_country').val(data.univ_country).trigger('change')
                    tinyMCE.get('univ_address').setContent(data.univ_address);

                    let html = '@method('put')'
                    $('.put').html(html)

                    $('#formUniv').attr('action', '{{ url('master/university') }}/' + data.univ_id.toLowerCase() + '')
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }
    </script>

@endsection
