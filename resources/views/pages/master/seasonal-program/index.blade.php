@extends('layout.main')

@section('title', 'Seasonal Program')

@section('content')    
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Seasonal Program
            </h5>
            <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#seasonalProgram"
                onclick="resetForm()"><i class="bi bi-plus-square me-1"></i> Add
                Seasonal Program</a>
        </div>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="seasonalProgramTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Program Name</th>
                        <th>Start Program Date</th>
                        <th>End Program Date</th>
                        <th>Sales Date</th>
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

    <div class="modal fade" id="seasonalProgram" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Seasonal Program
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
                                        {{-- @foreach ($programs as $program)
                                            <option value="{{ $program->prog_id }}"
                                                {{ $program->prog_id == old('prog_id') ? 'selected' : '' }}>
                                                {{ $program->program_name }}</option>
                                        @endforeach --}}
                                    </select>
                                    @error('prog_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Start Program Date <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="date" name="start_program_date" id="start_program_date"
                                        class="form-control form-control-sm rounded" required
                                        value="{{ old('start_program_date') }}">
                                    @error('start_program_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        End Program Date <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="date" name="end_program_date" id="end_program_date"
                                        class="form-control form-control-sm rounded" required
                                        value="{{ old('end_program_date') }}">
                                    @error('end_program_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Sales Date <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="date" name="sales_date" id="sales_date"
                                        class="form-control form-control-sm rounded" required
                                        value="{{ old('sales_date') }}">
                                    @error('sales_date')
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
                dropdownParent: $('#seasonalProgram .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });

        $(document).ready(function() {
            // var table = $('#seasonalProgramTable').DataTable({
            //     dom: 'Bfrtip',
            //     lengthMenu: [
            //         [10, 25, 50, 100, -1],
            //         ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
            //     ],
            //     buttons: [
            //         'pageLength', {
            //             extend: 'excel',
            //             text: 'Export to Excel',
            //         }
            //     ],
            //     scrollX: true,
            //     fixedColumns: {
            //         left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
            //         right: 1
            //     },
            //     processing: true,
            //     serverSide: true,
            //     ajax: '',
            //     pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
            //     columns: [{
            //             data: 'id',
            //             className: 'text-center',
            //             render: function(data, type, row, meta) {
            //                 return meta.row + meta.settings._iDisplayStart + 1;
            //             }
            //         },
            //         {
            //             data: 'program_name',
            //             // name: 'program_name',
            //             name: 'program.program_name'

            //         },
            //         {
            //             data: 'total_participant',
            //             className: 'text-center',
            //         },
            //         {
            //             data: 'total_target',
            //             className: 'text-center',
            //             render: function(data, type) {
            //                 var number = $.fn.dataTable.render
            //                     .number(',', '.', 2, 'Rp. ')
            //                     .display(data);

            //                 return number;
            //             },
            //         },
            //         {
            //             data: 'month',
            //             className: 'text-center',
            //         },
            //         {
            //             data: 'year',
            //             className: 'text-center',
            //         },
            //         {
            //             data: '',
            //             className: 'text-center',
            //             defaultContent: '<button type="button" data-bs-toggle="modal" data-bs-target="#seasonalProgram" class="btn btn-sm btn-outline-warning editSeasonalProgram"><i class="bi bi-pencil"></i></button>' +
            //                 '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteSeasonalProgram"><i class="bi bi-trash2"></i></button>'
            //         }
            //     ]
            // });

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Seasonal Program')->first();
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

            $('#seasonalProgramTable tbody').on('click', '.editSeasonalProgram ', function() {
                var data = table.row($(this).parents('tr')).data();
                editById(data.id)
            });

            $('#seasonalProgramTable tbody').on('click', '.deleteSeasonalProgram ', function() {
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
                dropdownParent: $('#seasonalProgram .modal-content'),
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
@endsection
