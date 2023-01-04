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
