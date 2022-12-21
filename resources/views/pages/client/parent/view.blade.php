@extends('layout.main')

@section('title', 'Parent - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('client/parent') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Parent
        </a>
    </div>


    <div class="row">
        <div class="col-md-5">
            <div class="card rounded mb-2">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="">
                            <h3 class="m-0 p-0">{{ $parent->fullname }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar-day me-1"></i> Join Date: {{ date('d M Y', strtotime($parent->created_at)) }} |
                                <i class="bi bi-calendar-date mx-1"></i> Last Update: {{ date('d M Y', strtotime($parent->updated_at)) }}
                            </small>
                        </div>
                        <div class="">
                            <a href="{{ url('client/parent/'.$parent->id.'/edit') }}" class="btn btn-warning btn-sm rounded"><i
                                    class="bi bi-pencil"></i></a>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                E-mail
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $parent->mail }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Phone Number
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $parent->phone }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Address
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {!! $parent->address !!} 
                            {!! $parent->postal_code ? $parent->postal_code."<br>" : null !!} 
                            {{ $parent->city }} {{ $parent->state }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Date of Birth
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ isset($parent->dob) ? date('d M Y', strtotime($parent->dob)) : null }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col-md-3 d-flex justify-content-between">
                            <label>
                                Lead
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9">
                            {{ $parent->leadSource }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h6 class="my-1 p-0">
                        <i class="bi bi-info-circle me-1"></i>
                        List of Child
                    </h6>
                </div>
                <div class="card-body">
                    @if (isset($parent->childrens))
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Full Name</th>
                                <th>School Name</th>
                                <th>Graduation Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($parent->childrens as $children)
                                <tr align="center">
                                    <td>{{ $no++ }}</td>    
                                    <td>{{ $children->fullname }}</td>
                                    <td>{{ $children->school->sch_name }}</td>
                                    <td>{{ $children->graduation_year }}</td>
                                    <td>
                                        <a href="{{ url('client/student').'/'.$children->id }}" class="btn btn-outline-warning btn-sm rounded"><i
                                            class="bi bi-eye"></i></a>
                                    </td>
                                </tr> 
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        There's no children
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card rounded">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h5 class="m-0 p-0">Programs</h5>
                    </div>
                    <div class="">
                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#programForm">Add Program</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                        <thead class="bg-dark text-white">
                            <tr class="text-center" role="row">
                                <th class="text-dark">No</th>
                                <th class="bg-info text-white">Program Name</th>
                                <th>Conversion Lead</th>
                                <th>Last Discuss</th>
                                <th>PIC</th>
                                <th>Program Status</th>
                                <th class="text-dark">Status</th>
                                <th class="text-dark">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>Program Name</td>
                                    <td>Instagram</td>
                                    <td>22 Sept 2022</td>
                                    <td>Anggita</td>
                                    <td>Interest</td>
                                    <td class="text-center">Hot</td>
                                    <td class="text-center"><a href="{{ url('client/mentee/1') }}"
                                            class="btn btn-sm btn-warning"><i class="bi bi-info-circle me-2"></i>More</a>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot class="bg-light text-white">
                            <tr>
                                <td colspan="7"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal modal-md fade" id="programForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0 p-0">
                        <i class="bi bi-plus me-2"></i>
                        Add Program
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="">Program Name</label>
                                <select class="modal-select w-100" name="program_id">
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < 5; $i++)
                                        <option value="{{ $i }}">Test {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label for="">Lead Source</label>
                                <select class="modal-select w-100" name="program_id">
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < 5; $i++)
                                        <option value="{{ $i }}">Test {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label for="">PIC</label>
                                <select class="modal-select w-100" name="program_id">
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < 5; $i++)
                                        <option value="{{ $i }}">Test {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label for="">First Discuss</label>
                                <input type="date" name="" class="form-control form-control-sm rounded">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label for="">Planned Follow Up</label>
                                <input type="date" name="" class="form-control form-control-sm rounded">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="">Notes</label>
                                <textarea name="" id="" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-outline-danger rounded-3" data-bs-dismiss="modal">
                                    <i class="bi bi-x me-1"></i>
                                    Cancel
                                </button>
                                <button class="btn btn-sm btn-primary rounded-3">
                                    <i class="bi bi-save2"></i>
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Select2 Modal 
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#programForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });
    </script>

    {{-- Need Changing --}}
    {{-- <script>
        $(document).ready(function() {
            var table = $('#programTable').DataTable({
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
                    right: 2
                },
            });

            $('#programTable tbody').on('click', '.editClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('asset') }}/" + data.asset_id.toLowerCase() + '/edit';
            });

            $('#programTable tbody').on('click', '.deleteClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('asset', data.asset_id)
            });
        });
    </script> --}}




@endsection
