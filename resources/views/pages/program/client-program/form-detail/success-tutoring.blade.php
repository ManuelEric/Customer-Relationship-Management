<div class="row mb-3">
    <div class="col-md-3">
        <label for="">
            Program Detail <sup class="text-danger">*</sup>
        </label>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                Academic Tutoring Program
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-12 mb-2">
                        <small>Trial Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="trial_date" {{ $disabled }} value="{{ isset($clientProgram->trial_date) ? $clientProgram->trial_date : old('trial_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('trial_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>Start Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="prog_start_date" {{ $disabled }} value="{{ isset($clientProgram->prog_start_date) ? $clientProgram->prog_start_date : old('prog_start_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('prog_start_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <small>End Date <sup class="text-danger">*</sup></small>
                        <input type="date" name="prog_end_date" {{ $disabled }} value="{{ isset($clientProgram->prog_end_date) ? $clientProgram->prog_end_date : old('prog_end_date') }}"
                            class="form-control form-control-sm rounded">
                        @error('prog_end_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <small>Timesheet Link <sup class="text-danger">*</sup></small>
                        <input type="url" name="timesheet_link" {{ $disabled }} value="{{ isset($clientProgram->timesheet_link) ? $clientProgram->timesheet_link : old('timesheet_link') }}"
                            class="form-control form-control-sm rounded">
                        @error('timesheet_link')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mt-3"></div>
                    <hr>
                    <label for="">
                        @if (isset($clientProgram->session_tutor))
                        Detail of academic tutoring programs
                        @else
                        Fill the detail of academic tutoring program for his/her
                        @endif
                    </label>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <small>Session <sup class="text-danger">*</sup></small>
                            <input type="number" name="session" class="form-control form-control-sm rounded" {{ $disabled }} value="{{ isset($clientProgram->session_tutor) ? $clientProgram->session_tutor : old('session') }}">
                        </div>
                    </div>
                    <div class="row mt-4 pt-2" id="section-session">
                        <!-- session detail here -->

                        @if (isset($clientProgram->acadTutorDetail))
                            @foreach ($clientProgram->acadTutorDetail as $key => $value)

                            <div class="row mb-3 schedule-{{ $loop->iteration }}">
                                <div class="col-md-3">
                                    <label>Session {{ $loop->iteration }}.<sup class="text-danger">*</sup></label>
                                </div>
                                <div class="col-md-5">
                                    <small>Schedule</small>
                                    <input type="datetime-local" required class="form-control form-control-sm rounded" min="{{ $clientProgram->prog_start_date.'T00:00' }}" max="{{ $clientProgram->prog_end_date.'T23:59' }}" name="sessionDetail[]" {{$disabled}} value="{{ $value->date.' '.$value->time }}">
                                </div>
                                <div class="col-md-4">
                                    <small>Zoom link</small>
                                    <input type="url" required class="form-control form-control-sm rounded" {{$disabled}} name="sessionLinkMeet[]" value="{{ $value->link }}">
                                </div>
                            </div>

                            @endforeach
                        @endif

                        @if (old('sessionDetail.*'))
                            @foreach (old('sessionDetail.*') as $key => $value)

                            <div class="row mb-3 schedule-{{ $key }}">
                                <div class="col-md-3">
                                    <label>Session {{ $key }}.<sup class="text-danger">*</sup></label>
                                </div>
                                <div class="col-md-5">
                                    <small>Schedule</small>
                                    <input type="datetime-local" required class="form-control form-control-sm rounded" min="{{ old('prog_start_date').'T00:00' }}" max="{{ old('prog_end_date').'T23:59' }}" name="sessionDetail[]" value="{{ old('sessionDetail')[$key] }}">
                                </div>
                                <div class="col-md-4">
                                    <small>Zoom link</small>
                                    <input type="url" required class="form-control form-control-sm rounded" name="sessionLinkMeet[]" value="{{ old('sessionLinkMeet')[$key] }}">
                                </div>
                            </div>

                            @endforeach
                        @endif
                    </div>
                    {{-- <div class="col-md-12 mb-2">
                        <small>Tutor Name <sup class="text-danger">*</sup></small>
                        <select name="tutor_id" id="" class="select w-100" {{ $disabled }}>
                            <option data-placeholder="true"></option>
                            @foreach ($tutors as $tutor)
                                <option value="{{ $tutor->id }}"
                                    @if (isset($clientProgram->clientMentor) && $clientProgram->clientMentor()->count() > 0)
                                        @if ($clientProgram->clientMentor()->first()->id == $tutor->id)
                                            {{ "selected" }}
                                        @endif
                                    @endif
                                    >{{ $tutor->first_name.' '.$tutor->last_name.' - '.json_encode($tutor->roles()->where('role_name', 'Tutor')->pluck('tutor_subject')->toArray()) }}</option>
                            @endforeach
                        </select>
                        @error('tutor_id')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
