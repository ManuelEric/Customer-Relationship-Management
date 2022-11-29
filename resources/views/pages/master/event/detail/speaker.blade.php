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
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($eventSpeakers))
                    @php
                        $no = 1
                    @endphp
                        @foreach ($eventSpeakers as $eventSpeaker)
                        <tr>
                            <td>{{ $no++ }}</td>
                            @switch($eventSpeaker->speaker_type)
                                @case("partner")
                                    <td>{{ $eventSpeaker->partner_pic_name }}</td>
                                    <td>{{ $eventSpeaker->corp_name }}</td>
                                    @break

                                @case("school")
                                    <td>{{ $eventSpeaker->school_pic_name }}</td>
                                    <td>{{ $eventSpeaker->school_name }}</td>
                                    @break

                                @case("university")
                                    <td>{{ $eventSpeaker->university_pic_name }}</td>
                                    <td>{{ $eventSpeaker->university_name }}</td>
                                    @break

                                @case("internal")
                                    <td>{{ $eventSpeaker->internal_pic }}</td>
                                    <td>ALL-In Eduspace</td>
                                    @break
                            
                                @default
                                    
                            @endswitch
                            <td>{{ $eventSpeaker->start_time }}</td>
                            <td>{{ $eventSpeaker->end_time }}</td>
                            <td nowrap>
                                <select name="status" class="select w-100 status-form" data-row-id="{{ $eventSpeaker->agenda_id }}">
                                    <option data-placeholder="true"></option>
                                    <option value="1" {{ $eventSpeaker->status == 1 ? "selected" : null }}>Active</option>
                                    <option value="2" {{ $eventSpeaker->status == 2 ? "selected" : null }}>Cancel</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0)">
                                    <div onclick="confirmDelete('master/event/{{ $event->event_id }}/speaker', '{{ $eventSpeaker->agenda_id }}')">
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
                <form action="{{ route('event.speaker.store', ['event' => $event->event_id]) }}" method="POST" id="formPosition">
                    @csrf
                    <div class="put"></div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label for="">
                                From <sup class="text-danger">*</sup>
                            </label>
                            <select name="speaker_type" class="modal-select w-100">
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
                        <div class="col-md-6 mb-2">
                            <label for="">
                                Partner Name <sup class="text-danger">*</sup>
                            </label>

                            <select name="allin_speaker" class="d-none partner-name">
                                <option data-placeholder="true"></option>
                                @if (isset($employees))
                                    @foreach ($employees as $employee) 
                                        <option value="{{ $employee->id }}">{{ $employee->first_name.' '.$employee->last_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('allin_speaker')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror

                            <select name="partner_speaker" class="d-none partner-name">
                                <option data-placeholder="true"></option>
                                @if (isset($partnerEvent))
                                    @foreach ($partnerEvent as $partnerJoined)
                                        @if (isset($partnerJoined->pic))
                                            @foreach ($partnerJoined->pic as $pic)
                                                <option value="{{ $pic->id }}">{{ $pic->pic_name }} from {{ $partnerJoined->corp_name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('partner_speaker')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror

                            <select name="school_speaker" class="d-none partner-name">
                                <option data-placeholder="true"></option>
                                @if (isset($schoolEvent))
                                    @foreach ($schoolEvent as $schoolJoined)
                                        @if (isset($schoolJoined->detail))
                                            @foreach ($schoolJoined->detail as $pic)
                                                <option value="{{ $pic->schdetail_id }}">{{ $pic->schdetail_fullname }} as {{ $pic->schdetail_position }} from {{ $schoolJoined->sch_name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('school_speaker')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror

                            <select name="university_speaker" class="d-none partner-name">
                                <option data-placeholder="true"></option>
                                @if (isset($universityEvent))
                                    @foreach ($universityEvent as $universityJoined)
                                        @if (isset($universityJoined->pic))
                                            @foreach ($universityJoined->pic as $pic)
                                                <option value="{{ $pic->id }}">{{ $pic->name }} as {{ $pic->title }} from {{ $universityJoined->univ_name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('university_speaker')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="">
                                Start Time <sup class="text-danger">*</sup>
                            </label>
                            <input type="datetime-local" name="start_time" id=""
                                class="form-control form-control-sm" value="{{ $event->event_startdate }}" min="{{ $event->event_startdate }}" max="{{ $event->event_enddate }}">
                            @error('start_time')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">
                                End Time <sup class="text-danger">*</sup>
                            </label>
                            <input type="datetime-local" name="end_time" id=""
                                class="form-control form-control-sm" value="{{ $event->event_enddate }}" min="{{ $event->event_startdate }}" max="{{ $event->event_enddate }}">
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

<script>
    $(document).ready(function() {
        $('.modal-select').select2({
            dropdownParent: $('#speaker .modal-body'),
            placeholder: "Select value",
            allowClear: true
        });

        function init() {
            $('.modal-select').select2({
                dropdownParent: $('#speaker .modal-body'),
                placeholder: "Select value",
                allowClear: true
            });
        }

        $("select[name=speaker_type]").change(function () {
            var speaker_type = $(this).val()

            switch (speaker_type) {
                case "internal":
                    var element_name = 'allin_speaker'
                    break;

                case "partner":
                    var element_name = 'partner_speaker'
                    break;

                case "school":
                    var element_name = 'school_speaker'
                    break;

                case "university":
                    var element_name = 'university_speaker'
                    break;
            }

            $('select[name='+element_name+']').removeClass('d-none')
            $('.partner-name').each(function (e, index) {
                        
                if (!index.classList.contains('d-none') && ($(index).attr('name') != element_name))
                    $(index).addClass('d-none')

            })
        })

        $(".status-form").each(function() {
            var _this = $(this)
            _this.change(function() {
                var status = _this.val()
                var agendaId = _this.data('row-id')

                var link = '{{ url('') }}/master/event/{{ $event->event_id }}/speaker/' + agendaId
                // var link = "?status=" + status + "&xsrf=" + "{{ csrf_token() }}" + "&id=" + agendaId
                const data = {
                    'agendaId' : agendaId,
                    'status' : status
                }

                axios.put(link, data)
                    .then(function(response) {
                        console.log(response)
                        notification(response.data.status, response.data.message)
                        
                    })
                    .catch(function(error) {
                        
                        notification(false, error.response.statusText)
                    })
            })
        })
    });
</script>
