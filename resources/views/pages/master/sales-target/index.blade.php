@extends('layout.main')

@section('title', 'Sales Target - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Sales Target
        </a>
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#salesTargetForm"
            onclick="resetForm()"><i class="bi bi-plus-square me-1"></i> Add
            Sales Target</a>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="salesTargetTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Program Name</th>
                        <th>Participants</th>
                        <th>Total Amount</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="modal fade" id="salesTargetForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Sales Target
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ url('master/sales-target') }}" method="POST" id="formSalesTarget">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Program Name <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="prog_id" id="prog_id" class="modal-select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->prog_id }}" {{ $program->prog_id == old('prog_id') ? 'selected' : ''}}>{{$program->sub_prog ? $program->sub_prog->sub_prog_name.' - ':''}}{{ $program->prog_program }}</option>
                                        @endforeach
                                    </select>
                                    @error('prog_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Participants Target <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="number" name="total_participant" id="total_participant"
                                        class="form-control form-control-sm rounded" required value="{{ old('total_participant') }}">
                                    @error('total_participant')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Total Amount Target <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="number" name="total_target" id="total_target"
                                        class="form-control form-control-sm rounded" required value="{{ old('total_target') }}">
                                    @error('total_target')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Month <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="month" name="month_year" id="month_year"
                                        class="form-control form-control-sm rounded" required value="{{ old('month_year') }}">
                                    @error('month_year')
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

    <script>
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#salesTargetForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });

        $(document).ready(function() {
            var table = $('#salesTargetTable').DataTable({
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
                        data: 'program_name',
                        // name: 'program_name',
                    },
                    {
                        data: 'total_participant',
                        className: 'text-center',
                    },
                    {
                        data: 'total_target',
                        className: 'text-center',
                    },
                    {
                        data: 'month',
                        className: 'text-center',
                    },
                    {
                        data: 'year',
                        className: 'text-center',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" data-bs-toggle="modal" data-bs-target="#salesTargetForm" class="btn btn-sm btn-outline-warning editSalesTarget"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteSalesTarget"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            });

            $('#salesTargetTable tbody').on('click', '.editSalesTarget ', function() {
                var data = table.row($(this).parents('tr')).data();
                editById(data.id)
            });

            $('#salesTargetTable tbody').on('click', '.deleteSalesTarget ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/sales-target', data.id)
            });
        });

        function resetForm() {
            $('#total_participant').val(null)
            $('#total_target').val(null)
            $('#month_year').val(null)
            $('#prog_id').val(null).trigger('change')
            $('.modal-select').select2({
                dropdownParent: $('#salesTargetForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
            $('.put').html('')
            $('#formSalesTarget').attr('action', '{{ url('master/sales-target') }}')
        }


        function editById(id) {
            let link = "{{ url('master/sales-target') }}/" + id + "/edit"
            // console.log(link)

            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data
                    // console.log(data.prog_id)

                    $('#total_participant').val(data.total_participant)
                    $('#total_target').val(data.total_target)
                    $('#month_year').val(data.month_year)
                    $('#prog_id').val(data.prog_id).trigger('change')
                    let html =
                        '@method('put')' +
                        '<input type="hidden" name="id" value="' + data.id + '">'

                    $('.put').html(html)

                    $('#formSalesTarget').attr('action', '{{ url('master/sales-target') }}/' + data.id + '')
                })
                .catch(function(error) {
                    // handle error
                    console.error(error);
                })
        }
    </script>

    @if(
        $errors->has('prog_id') | 
        $errors->has('total_target') | 
        $errors->has('total_participant') | 
        $errors->has('month_year')
        )
                
        <script>
            $(document).ready(function(){
                $('#salesTargetForm').modal('show'); 
            })

        </script>

    @endif

@endsection