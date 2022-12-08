@extends('layout.main')

@section('title', 'School Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> School Program
        </a>

        <div class="dropdown">
            <button href="#" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                data-bs-auto-close="false" id="filter">
                <i class="bi bi-funnel me-2"></i> Filter
            </button>
            <form class="dropdown-menu dropdown-menu-end pt-0 shadow" style="width: 300px">
                <h6 class="dropdown-header bg-secondary text-white rounded-top">Advanced Filter</h6>
                <div class="row p-3">
                    <div class="col-md-12 mb-2">
                        <label for="">School Name</label>
                        <select name="" id="" class="select form-select form-select-sm w-100" multiple>
                            @for ($i = 0; $i < 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="">Program Name</label>
                        <select name="" id="" class="select form-select form-select-sm w-100" multiple>
                            @for ($i = 0; $i < 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label>Start Date</label>
                                <input type="date" name="" id=""
                                    class="form-control form-control-sm rounded">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>End Date</label>
                                <input type="date" name="" id=""
                                    class="form-control form-control-sm rounded">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="">Approach Status</label>
                        <select name="" id="" class="select form-select form-select-sm w-100" multiple>
                            @for ($i = 0; $i < 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="">PIC</label>
                        <select name="" id="" class="select form-select form-select-sm w-100" multiple>
                            @for ($i = 0; $i < 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-outline-danger" id="cancel">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-outline-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="schProgTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">School Name</th>
                        <th>Program Name</th>
                        <th>First Discuss</th>
                        <th>Participants</th>
                        <th>Total</th>
                        <th>Approach Status</th>
                        <th>PIC</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="8"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#cancel').click(function() {
                $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
            });

            var table = $('#schProgTable').DataTable({
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
                        data: 'school_name',
                        name: 'tbl_sch.sch_name' 
                    },
                    {
                        data: 'program_name',
                        name: 'tbl_prog.prog_program'
                    },
                    {
                        data: 'first_discuss',
                    },
                    {
                        data: 'participants',
                        render: function(data, type, row, meta) {
                            switch (row.status) {
                                case 0:
                                    return "-"
                                    break;

                                case 1:
                                    return data
                                    break;
                                
                                case 2:
                                    return "-"
                                    break;
                            }
                        }
                    },
                    {
                        data: 'total_fee',
                        render: function(data, type, row, meta) {
                            switch (row.status) {
                                case 0:
                                    return "-"
                                    break;

                                case 1:
                                    return data
                                    break;
                                
                                case 2:
                                    return "-"
                                    break;
                            }
                        }
                    },
                    {
                        data: 'status',
                        render: function(data, type, row, meta) {
                            switch (row.status) {
                                case 0:
                                    return "Pending"
                                    break;

                                case 1:
                                    return "Success"
                                    break;
                                
                                case 2:
                                    return "Denied"
                                    break;
                            }
                        }
                    },
                    {
                        data: 'pic_name',
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning editSchProg"><i class="bi bi-eye"></i></button>'
                    }
                ]
            });
            realtimeData(table)

            $('#schProgTable tbody').on('click', '.editSchProg', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('program/school') }}/" + data.sch_id.toLowerCase() + "/detail/" + data.id;    
            });

        
        });
    </script>

  
@endsection
