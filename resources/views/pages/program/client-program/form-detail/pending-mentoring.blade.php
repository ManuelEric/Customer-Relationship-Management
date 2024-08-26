<div class="row mb-3">
    <div class="col-md-3">
        <label for="">
            Program Detail <sup class="text-danger">*</sup>
        </label>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                Admissions Program
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <small>Initial Consult Date</small>
                        <input type="date" name="pend_initconsult_date" {{ $disabled }}
                            class="form-control form-control-sm rounded" value="{{ isset($clientProgram->initconsult_date) ? $clientProgram->initconsult_date : old('pend_initconsult_date') }}">
                        @error('pend_initconsult_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <small>Initial Assessment Sent</small>
                        <input type="date" name="pend_assessmentsent_date" {{ $disabled }} 
                            class="form-control form-control-sm rounded" value="{{ isset($clientProgram->assessmentsent_date) ? $clientProgram->assessmentsent_date : old('pend_assessmentsent_date') }}">
                        @error('pend_assessmentsent_date')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row mb-2 mentor-ic">
                    <div class="col-md-12 mb-2">
                        <small>Mentor IC</small>
                        <select name="pend_mentor_ic" id="" class="select w-100" {{ $disabled }}>
                            <option data-placeholder="true"></option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}"
                                    @if (old('pend_mentor_ic') == $mentor->id) {{ 'selected' }}
                                    @elseif (isset($clientProgram->mentorIC) &&
                                            $clientProgram->mentorIC()->orderBy('tbl_mentor_ic.id', 'asc')->count() > 0)
                                        @if ($clientProgram->mentorIC()->orderBy('tbl_mentor_ic.id', 'asc')->first()->id == $mentor->id)
                                        {{ 'selected' }} @endif
                                    @endif
                                    >{{ $mentor->first_name . ' ' . $mentor->last_name }}</option>
                            @endforeach
                        </select>
                        @error('pend_mentor_ic')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
