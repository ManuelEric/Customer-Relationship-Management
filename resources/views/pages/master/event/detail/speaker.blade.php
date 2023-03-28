<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Speaker
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal" data-bs-target="#speaker">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Speaker Name</th>
                        <th>From</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th width="130px">Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($eventSpeakers))
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($eventSpeakers as $eventSpeaker)
                            <tr>
                                <td>{{ $no++ }}</td>
                                @switch($eventSpeaker->speaker_type)
                                    @case('partner')
                                        <td>{{ $eventSpeaker->partner_pic_name }}</td>
                                        <td>{{ $eventSpeaker->corp_name }}</td>
                                    @break

                                    @case('school')
                                        <td>{{ $eventSpeaker->school_pic_name }}</td>
                                        <td>{{ $eventSpeaker->school_name }}</td>
                                    @break

                                    @case('university')
                                        <td>{{ $eventSpeaker->university_pic_name }}</td>
                                        <td>{{ $eventSpeaker->university_name }}</td>
                                    @break

                                    @case('internal')
                                        <td>{{ $eventSpeaker->internal_pic }}</td>
                                        <td>ALL-In Eduspace</td>
                                    @break

                                    @default
                                @endswitch
                                <td>{{ $eventSpeaker->start_time }}</td>
                                <td>{{ $eventSpeaker->end_time }}</td>
                                <td nowrap>
                                    <select name="status" class="select w-100 status-form"
                                        onchange="checkStatusSpeaker('{{ $eventSpeaker->agenda_id }}')"
                                        id="{{ 'speaker' . $eventSpeaker->agenda_id }}">
                                        <option data-placeholder="true"></option>
                                        <option value="1" {{ $eventSpeaker->status == 1 ? 'selected' : null }}>
                                            Active</option>
                                        <option value="2" {{ $eventSpeaker->status == 2 ? 'selected' : null }}>
                                            Cancel</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)">
                                        <div
                                            onclick="confirmDelete('master/event/{{ $event->event_id }}/speaker', '{{ $eventSpeaker->agenda_id }}')">
                                            <i class="bi bi-trash2 text-danger"></i>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="speaker" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Speaker
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="{{ route('event.speaker.store', ['event' => $event->event_id]) }}" method="POST"
                    id="formPosition">
                    @csrf
                    <div class="put"></div>
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label for="">
                                From <sup class="text-danger">*</sup>
                            </label>
                            <select name="speaker_type" class="modal-select w-100" id="speaker_type"
                                onchange="changeSpeaker()">
                                <option data-placeholder="true"></option>
                                <option value="internal">ALL-in</option>
                                <option value="partner">Partner</option>
                                <option value="school">School</option>
                                <option value="university">University</option>
                            </select>
                            @error('speaker_type')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <div class="speaker d-none mb-2" id="internalPIC">
                                <label for="">
                                    Employee Speaker <sup class="text-danger">*</sup>
                                </label>

                                <select name="allin_speaker" class="modal-select w-100">

                                    @if (isset($employees))
                                        <option data-placeholder="true"></option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                        @endforeach
                                    @else
                                        <option data-placeholder="true">There's no speaker</option>
                                    @endif
                                </select>
                                @error('allin_speaker')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="speaker d-none mb-2" id="partnerPIC">
                                <label for="">
                                    Partner Speaker <sup class="text-danger">*</sup>
                                </label>

                                <select name="partner_speaker" class="modal-select w-100">
                                    @if (isset($partnerEvent))
                                        @foreach ($partnerEvent as $partnerJoined)
                                            @if (isset($partnerJoined->pic) && count($partnerJoined->pic) > 0)
                                                <option data-placeholder="true"></option>
                                                @foreach ($partnerJoined->pic as $pic)
                                                    <option value="{{ $pic->id }}">{{ $pic->pic_name }} from
                                                        {{ $partnerJoined->corp_name }}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('partner_speaker')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="speaker d-none mb-2" id="schoolPIC">
                                <label for="">
                                    School Speaker <sup class="text-danger">*</sup>
                                </label>
                                <select name="school_speaker" class="modal-select w-100">
                                    @if (isset($schoolEvent))
                                        @foreach ($schoolEvent as $schoolJoined)
                                            @if (isset($schoolJoined->detail) && count($schoolJoined->detail) > 0)
                                                <option data-placeholder="true"></option>
                                                @foreach ($schoolJoined->detail as $pic)
                                                    <option value="{{ $pic->schdetail_id }}">
                                                        {{ $pic->schdetail_fullname }} as
                                                        {{ $pic->schdetail_position }}
                                                        from {{ $schoolJoined->sch_name }}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('school_speaker')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="speaker d-none mb-2" id="universityPIC">
                                <label for="">
                                    University Speaker <sup class="text-danger">*</sup>
                                </label>
                                <select name="university_speaker" class="modal-select w-100">
                                    @if (isset($universityEvent))
                                        @foreach ($universityEvent as $universityJoined)
                                            @if (isset($universityJoined->pic) && count($universityJoined->pic) > 0)
                                                <option data-placeholder="true"></option>
                                                @foreach ($universityJoined->pic as $pic)
                                                    <option value="{{ $pic->id }}">{{ $pic->name }} as
                                                        {{ $pic->title }} from {{ $universityJoined->univ_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('university_speaker')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="">
                                Start Time <sup class="text-danger">*</sup>
                            </label>
                            <input type="datetime-local" name="start_time" id=""
                                class="form-control form-control-sm" value="{{ $event->event_startdate }}"
                                min="{{ $event->event_startdate }}" max="{{ $event->event_enddate }}">
                            @error('start_time')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">
                                End Time <sup class="text-danger">*</sup>
                            </label>
                            <input type="datetime-local" name="end_time" id=""
                                class="form-control form-control-sm" value="{{ $event->event_enddate }}"
                                min="{{ $event->event_startdate }}" max="{{ $event->event_enddate }}">
                            @error('end_time')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
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

<div class="modal fade" id="reasonModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Reason
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="#" method="POST" id="reasonForm">
                    @csrf
                    @method('put')
                    <input type="hidden" name="agendaId" id="agenda_id">
                    <input type="hidden" name="status" id="status_id">
                    <label for="">Notes</label>
                    <textarea name="notes" id="notes"></textarea>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                            onclick="cancelModal()">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if (
        $errors->has('speaker_type') ||
        $errors->has('allin_speaker') ||
        $errors->has('partner_speaker') ||
        $errors->has('school_speaker') ||
        $errors->has('university_speaker') ||
        $errors->has('start_time') ||
        $errors->has('end_time') 
        )
        <script>
            $(document).ready(function() {
                $('#speaker').modal('show');

            })
        </script>
    @endif

<script>
    $(document).ready(function() {
        $('.modal-select').select2({
            dropdownParent: $('#speaker .modal-body'),
            placeholder: "Select value",
            allowClear: true
        });
    })

    function changeSpeaker() {
        let type = $('#speaker_type').val()
        let id = '#' + type + 'PIC'
        $('.speaker').addClass('d-none')
        $(id).removeClass('d-none')
    }

    function cancelModal() {
        let id = $('#agenda_id').val();
        let status = $('#speaker' + id)
        $('#element').select2('destroy');
        $(status).val(1).select2({
            allowClear: true
        });
        $('#reasonModal').modal('hide')
    }

    function checkStatusSpeaker(agendaId) {
        let status = $('#speaker' + agendaId).val()

        let link =
            '{{ url('') }}/master/event/{{ $event->event_id }}/speaker/' +
            agendaId
        let data = new Array()

        $('#reasonForm').attr('action', link)

        if (status == 2) {
            $('#reasonModal').modal('show')
            $('#agenda_id').val(agendaId)
            $('#status_id').val(status)
        } else {
            $('#agenda_id').val(agendaId)
            $('#status_id').val(status)
            $('#notes').val('')
            $('#reasonForm').submit()
        }
    }
</script>
