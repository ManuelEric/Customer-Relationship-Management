@extends('layout.main')

@section('title', 'Parents Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Parents</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')


    <div class="row">
        <div class="col-md-5">
            <div class="card rounded mb-2">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="">
                            <h3 class="m-0 mb-2 p-0">{{ $parent->full_name }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar-day me-1"></i> Join Date: {{ date('d M Y', strtotime($parent->created_at)) }} |
                                <i class="bi bi-calendar-date mx-1"></i> Last Update: {{ date('d M Y', strtotime($parent->updated_at)) }}
                            </small>
                        </div>
                        <div class="col-2 text-end">
                            <a href="{{ url('client/parent/'.$parent->id.'/edit') }}" class="btn btn-warning btn-sm rounded"><i class="bi bi-pencil"></i></a>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                E-mail
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $parent->mail }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Phone Number
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $parent->phone }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Address
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {!! $parent->address !!} 
                            {!! $parent->postal_code ? $parent->postal_code."<br>" : null !!} 
                            {{ $parent->city }} {{ $parent->state }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Date of Birth
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ isset($parent->dob) ? date('d M Y', strtotime($parent->dob)) : null }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Lead
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $parent->lead_source }} {{ $parent->referral_code != null && $parent->lead_source == "Referral" ? '(' . $parent->referral_name . ')' : null }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.client.parent.component.childrens-info')

        <div class="col-md-12 mt-2">
            <div class="card rounded">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h5 class="m-0 py-2">Events</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
                        <thead class="bg-secondary text-white">
                            <tr class="text-center" role="row">
                                <th class="text-dark">No</th>
                                <th class="bg-info text-white">Event Name</th>
                                <th>Event Start Date</th>
                                <th>Joined Date</th>
                            </tr>
                        </thead>
                        <tfoot class="bg-light text-white">
                            <tr>
                                <td colspan="4"></td>
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
@endsection

@push('scripts')
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

<script>
    var widthView = $(window).width();
    $(document).ready(function() {
        var table_event = $('#eventTable').DataTable({
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
                left: (widthView < 768) ? 1 : 2,
                right: 0
            },
            processing: true,
            serverSide: true,
            ajax: '',
            columns: [{
                    data: 'clientevent_id',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'event_name',
                    name: 'tbl_events.event_title'
                },
                {
                    data: 'event_startdate',
                    name: 'tbl_events.event_startdate',
                    render: function(data, type, row) {
                        return moment(data).format('DD MMMM YYYY HH:mm:ss')
                    }
                },
                {
                    data: 'joined_date',
                    render: function(data, type, row) {
                        return moment(data).format('DD MMMM YYYY')
                    }
                },
            ]
        });
    });
</script>
@endpush
