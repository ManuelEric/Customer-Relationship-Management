@extends('layout.main')

@section('title', 'Lead')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Lead Source
            </h5>
            <button type="button" href="#" class="btn btn-sm btn-info" onclick="resetForm()" data-bs-toggle="modal"
                data-bs-target="#leadForm"><i class="bi bi-plus-square me-1"></i> Add
                Lead Source</button>
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
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="leadTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Lead ID</th>
                        <th>Lead Name</th>
                        <th>Lead Detail</th>
                        <th>Score</th>
                        <th>Department</th>
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
    <div class="modal fade" id="leadForm" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Lead Source
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ url('master/lead') }}" method="POST" id="formLead">
                        @csrf
                        <div class="put"></div>

                        <div class="row g-2">
                            @if ($isSuperAdmin)
                                <div class="col-md-12">
                                    <div class="mb-0">
                                        <label for="">
                                            Department <sup class="text-danger">*</sup>
                                        </label>
                                        <select name="department_id" class="form-select form-select-sm">
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="department_id" value="{{ $deptId }}">
                            @endif
                            <div class="col-md-10">
                                <div class="mb-0">
                                    <label for="">
                                        Lead Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="lead_name" id="lead_name"
                                        class="form-control form-control-sm rounded" required value="{{ old('main_lead') }}"
                                        id="lead_name">
                                </div>
                                <div class="mb-2">
                                    <input class="form-check-input" name="kol" id="kol" type="checkbox"
                                        value="true">
                                    <label class="form-check-label ms-1 text-secondary" for="kol">
                                        Select to KOL
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-2">
                                    <label for="">
                                        Score <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="number" name="score" id="score"
                                        class="form-control form-control-sm rounded" required
                                        value="{{ isset($lead->score) ? $lead->score : old('score') }}">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ url('master/lead') }}" class="btn btn-outline-danger btn-sm"
                                data-bs-dismiss="{{ isset($lead) ? '' : 'modal' }}">
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
                order: [[1, 'desc']],
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
                ajax: '{!! url('master/lead') !!}',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'lead_id',
                        className: 'text-center',
                    },
                    {
                        data: 'main_lead',
                        className: 'text-center',
                    },
                    {
                        data: 'sub_lead',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return row.sub_lead ? row.sub_lead : '-'
                        }
                    },
                    {
                        data: 'score',
                        className: 'text-center'
                    },
                    {
                        data: 'dept_name',
                        name: 'dept_name',
                        className: 'text-center',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button data-bs-toggle="modal" data-bs-target="#leadForm" type="button" class="btn btn-sm btn-outline-warning editLead"><i class="bi bi-pencil"></i></button>' +
                            '<button  type="button" class="btn btn-sm btn-outline-danger ms-1 deleteLead"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            };

            var table = initializeDataTable('#leadTable', options, 'rt_lead');

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Lead Source')->first();
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

            $('#leadTable tbody').on('click', '.editLead ', function() {
                var data = table.row($(this).parents('tr')).data();
                editById(data.lead_id)
            });

            $('#leadTable tbody').on('click', '.deleteLead ', function() {
                var data = table.row($(this).parents('tr')).data();
                //App Blade
                confirmDelete('master/lead', data.lead_id)
            });
        });


        function resetForm() {
            $('#lead_name').val(null)
            $('#kol').prop('checked', false)
            $('#score').val(null)
            $('.put').html('')
            $('#formLead').attr('action', '{{ url('master/lead') }}')
        }

        function editById(lead_id) {
            let link = "{{ url('master/lead') }}/" + lead_id.toLowerCase()

            axios.get(link)
                .then(function(response) {

                    // handle success
                    let data = response.data.lead
                    // console.log(data)

                    if (data.main_lead == "KOL") {
                        $('#lead_name').val(data.sub_lead)
                        $('#kol').prop('checked', true);
                    } else {
                        $('#lead_name').val(data.main_lead)
                        $('#kol').prop('checked', false);
                    }
                    $('#score').val(data.score)
                    $('select[name=department_id]').val(data.department_id).change();


                    let html =
                        '@method('put')' +
                        '<input type="hidden" name="id" value="' + data.lead_id + '">'

                    $('.put').html(html)


                    $('#formLead').attr('action', '{{ url('master/lead') }}/' + data.lead_id.toLowerCase() + '')
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }
    </script>
@endsection
