<section id="available-mentor" class="mentor-tutor" v-if="main_prog==1 && status==1">
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="">
                Supervising Mentor <sup class="text-danger">*</sup>
            </label>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <select name="supervising_mentor" id="" class="select w-100" {{ $disabled }}>
                        <option data-placeholder="true"></option>
                        @foreach ($mentors as $mentor)
                            <option value="{{ $mentor->id }}" @selected(old('supervising_mentor') == $mentor->id)
                                @selected(isset($clientProgram->clientMentor) && optional($clientProgram->clientMentor()->wherePivot('status', 1)->where('type', 1)->latest()->first())->id == $mentor->id)>{{ $mentor->first_name . ' ' . $mentor->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supervising_mentor')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="">
                Profile Building Mentor <sup class="text-danger">*</sup>
            </label>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <select name="profile_building_mentor" id="" class="select w-100" {{ $disabled }}>
                        <option data-placeholder="true"></option>
                        @foreach ($mentors as $mentor)
                            <option value="{{ $mentor->id }}" @selected(old('profile_building_mentor') == $mentor->id)
                                @selected(isset($clientProgram->clientMentor) && optional($clientProgram->clientMentor()->wherePivot('status', 1)->where('type', 2)->latest()->first())->id == $mentor->id)>{{ $mentor->first_name . ' ' . $mentor->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('profile_building_mentor')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="">
                Subject Specialist Mentor
            </label>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <select name="subject_specialist_mentor" id="" class="select w-100" {{ $disabled }}>
                        <option data-placeholder="true"></option>
                        @foreach ($externalMentors as $mentor)
                            <option value="{{ $mentor->id }}" @selected(old('subject_specialist_mentor') == $mentor->id)
                                @selected(isset($clientProgram->clientMentor) && optional($clientProgram->clientMentor()->wherePivot('status', 1)->where('type', 6)->latest()->first())->id == $mentor->id)>{{ $mentor->first_name . ' ' . $mentor->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_specialist_mentor')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="">
                Aplication Strategy Mentor
            </label>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <select name="aplication_strategy_mentor" id="" class="select w-100" {{ $disabled }}>
                        <option data-placeholder="true"></option>
                        @foreach ($mentors as $mentor)
                            <option value="{{ $mentor->id }}" @selected(old('subject_specialist_mentor') == $mentor->id)
                                @selected(isset($clientProgram->clientMentor) && optional($clientProgram->clientMentor()->wherePivot('status', 1)->where('type', 3)->latest()->first())->id == $mentor->id)>{{ $mentor->first_name . ' ' . $mentor->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('aplication_strategy_mentor')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="">
                Writing Mentor
            </label>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <select name="writing_mentor" id="" class="select w-100" {{ $disabled }}>
                        <option data-placeholder="true"></option>
                        @foreach ($mentors as $mentor)
                            <option value="{{ $mentor->id }}" @selected(old('subject_specialist_mentor') == $mentor->id)
                                @selected(isset($clientProgram->clientMentor) && optional($clientProgram->clientMentor()->wherePivot('status', 1)->where('type', 4)->latest()->first())->id == $mentor->id)>{{ $mentor->first_name . ' ' . $mentor->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('writing_mentor')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</section>
<section id="available-tutor" class="mentor-tutor">
    <div id="tutoring"
        v-if="(main_prog==4 || main_prog==7 || main_prog==8 || main_prog==9) && status==1 && prog_id!='SATPREP'">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="">
                    Tutor <sup class="text-danger">*</sup>
                </label>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-12">
                        <select name="tutor_id" id="" class="select w-100" {{ $disabled }}>
                            <option data-placeholder="true"></option>
                            @foreach ($tutors as $tutor)
                                @php
                                    $subjects = [];
                                    if ($tutor->user_subjects()->count() > 0) {
                                        foreach ($tutor->user_subjects as $user_subject) {
                                            $subjects[] = $user_subject->subject->name;
                                        }
                                    }
                                @endphp
                                <option value="{{ $tutor->id }}"
                                    @if (isset($clientProgram->clientMentor) && $clientProgram->clientMentor()->count() > 0) @if ($clientProgram->clientMentor()->first()->id == $tutor->id)
                                                                    {{ 'selected' }} @endif
                                    @endif
                                    @selected(old('tutor_id') == $tutor->id)
                                    >{{ $tutor->first_name . ' ' . $tutor->last_name . (count($subjects) > 0 ? ' - ' . json_encode($subjects) : '') }}
                                </option>
                            @endforeach
                        </select>
                        @error('tutor_id')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="sat-act" v-if="main_prog==4 && prog_id=='SATPREP' && status==1">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="">
                    Tutor 1<sup class="text-danger">*</sup>
                </label>
            </div>
            <div class="col-md">
                <div class="row">
                    <div class="col-md-6">
                        <select name="tutor_1" class="select w-100" {{ $disabled }}>
                            <option data-placeholder="true"></option>
                            @foreach ($tutors as $tutor)
                                @php
                                    $subjects = [];
                                    if ($tutor->user_subjects()->count() > 0) {
                                        foreach ($tutor->user_subjects as $user_subject) {
                                            $subjects[] = $user_subject->subject->name;
                                        }
                                    }
                                @endphp
                                <option value="{{ $tutor->id }}" @selected(old('tutor_1') == $tutor->id)
                                    @selected(isset($clientProgram->clientMentor) && optional($clientProgram->clientMentor()->wherePivot('status', 1)->where('type', 5)->first())->id == $tutor->id)>
                                    {{ $tutor->first_name . ' ' . $tutor->last_name . (count($subjects) > 0 ? ' - ' . json_encode($subjects) : '') }}
                                </option>
                            @endforeach
                        </select>
                        @error('tutor_1')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="timesheet_1" id="" {{ $disabled }}
                            class="form-control form-control-sm rounded" placeholder="Timesheet 1"
                            value="{{ isset($clientProgram->clientMentor[0]->pivot->timesheet_link) ? $clientProgram->clientMentor[0]->pivot->timesheet_link : old('timesheet_1') }}">
                        @error('timesheet_1')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="">
                    Tutor 2
                </label>
            </div>
            <div class="col-md">
                <div class="row">
                    <div class="col-md-6">
                        {{ $clientProgram->clientMentor()->wherePivot('status', 1)->where('type', 5)->get() }}
                        <select name="tutor_2" class="select w-100" {{ $disabled }}>
                            <option data-placeholder="true"></option>
                            @foreach ($tutors as $tutor)
                                @php
                                    $subjects = [];
                                    if ($tutor->user_subjects()->count() > 0) {
                                        foreach ($tutor->user_subjects as $user_subject) {
                                            $subjects[] = $user_subject->subject->name;
                                        }
                                    }
                                @endphp
                                <option value="{{ $tutor->id }}" @selected(old('tutor_2') == $tutor->id)
                                    @selected(isset($clientProgram->clientMentor) && optional($clientProgram->clientMentor()->wherePivot('status', 1)->where('type', 5)->latest()->first())->id == $tutor->id && $clientProgram->clientMentor()->count() > 1)>
                                    {{ $tutor->first_name . ' ' . $tutor->last_name . (count($subjects) > 0 ? ' - ' . json_encode($subjects) : '') }}
                                </option>
                            @endforeach
                        </select>
                        @error('tutor_2')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="timesheet_2" id="" {{ $disabled }}
                            class="form-control form-control-sm rounded" placeholder="Timesheet 2"
                            value="{{ isset($clientProgram->clientMentor[1]->pivot->timesheet_link) ? $clientProgram->clientMentor[1]->pivot->timesheet_link : old('timesheet_2') }}">
                        @error('timesheet_2')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
