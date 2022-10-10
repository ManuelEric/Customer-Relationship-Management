@extends('layout.main')

@section('title', 'University - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> University
        </a>
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#univForm"><i
                class="bi bi-plus-square me-1"></i> Add
            University</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="univTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>#</th>
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
    <div class="modal fade" id="univForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <form
                        action="@if (isset($university)) {{ '/university/' . strtolower($university->univ_id) }}@else{{ '/university' }} @endif"
                        method="POST">
                        @csrf
                        @if (isset($university))
                            @method('put')
                        @endif
                        <div class="mb-2">
                            <label for="">
                                University Name
                            </label>
                            <input type="text" name="univ_name" class="form-control form-control-sm rounded" required
                                value="{{ isset($university->univ_name) ? $university->univ_name : old('univ_name') }}">
                        </div>
                        <div class="mb-2">
                            <label for="">
                                Country
                            </label>
                            <select name="univ_country" class="form-select form-select-sm rounded" required>
                                @foreach ($countries as $item)
                                    <option value="{{ $item->name }}"
                                        {{ (isset($university->univ_country) && $item->name == $university->univ_country) ||
                                        old('univ_country') == $item->name
                                            ? 'selected'
                                            : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="">
                                Address
                            </label>
                            <textarea name="univ_address" class="form-control form-control-sm rounded">{{ isset($university->univ_address) ? $university->univ_address : old('univ_address') }}</textarea>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ url('university') }}" class="btn btn-outline-danger btn-sm"
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

    @if (isset($university))
        <script>
            $(document).ready(function() {
                // show modal 
                var myModal = new bootstrap.Modal(document.getElementById('univForm'))
                myModal.show()
            });
        </script>
    @endif

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
                ajax: '{!! route('university.datatables') !!}',
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
                        defaultContent: '<button type="button" class="btn btn-sm btn-warning editUniv"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-danger ms-1 deleteUniv"><i class="bi bi-trash"></i></button>'
                    }
                ]
            });

            $('#univTable tbody').on('click', '.editUniv ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('university') }}/" + data.id + '/edit';
            });

            $('#univTable tbody').on('click', '.deleteUniv ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('university', data.id)
            });
        });
    </script>
@endsection
