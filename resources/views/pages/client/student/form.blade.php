@extends('layout.main')

@section('title', 'Student')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Students</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Student</li>
@endsection
@section('content')
    <div class="card rounded">
        <div class="card-header">
            <h5 class="my-1 p-0">
                <i class="bi bi-info-circle me-1"></i>
                Student Detail
            </h5>
        </div>
        <div class="card-body">
            <form
                action="{{ isset($student) ? route('student.update', ['student' => $student->id]) : route('student.store') }}"
                method="post">
                @csrf
                @if (isset($student))
                    @method('PUT')
                @endif
                <div class="row flex-md-row flex-column align-items-center">
                    <div class="col-md-4 col text-center">
                        <img src="{{ asset('img/mentee.jpg') }}" class="w-50">
                    </div>
                    <div class="col-md-8 col">
                        <div class="row gap-md-0 gap-2">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>First Name <i class="text-danger font-weight-bold">*</i>
                                    </label>
                                    <input name="first_name" type="text" class="form-control form-control-sm"
                                        placeholder="First name"
                                        value="{{ isset($student->first_name) ? $student->first_name : old('first_name') }}">
                                    @error('first_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Last Name</label>
                                    <input name="last_name" type="text" class="form-control form-control-sm"
                                        placeholder="Last name"
                                        value="{{ isset($student->last_name) ? $student->last_name : old('last_name') }}">
                                    @error('last_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>E-mail <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="mail" type="text" class="form-control form-control-sm"
                                        placeholder="E-mail"
                                        value="{{ isset($student->mail) ? $student->mail : old('mail') }}">
                                    @error('mail')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Phone Number <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="phone" type="text" class="form-control form-control-sm"
                                        placeholder="Phone Number"
                                        value="{{ isset($student->phone) ? $student->phone : old('phone') }}">
                                    @error('phone')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Date of Birth </label>
                                    <input name="dob" type="date" class="form-control form-control-sm"
                                        value="{{ isset($student->dob) ? $student->dob : old('dob') }}">
                                    @error('dob')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Instagram</label>
                                    <input name="insta" type="text" class="form-control form-control-sm"
                                        placeholder="Instagram"
                                        value="{{ isset($student->insta) ? $student->insta : old('insta') }}">
                                    @error('insta')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="mb-2">
                                    <label>Country <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="country" type="text" class="form-control form-control-sm"
                                        placeholder="Country" id="country" value="{{ isset($student->state) ? $student->state : old('country') }}">
                                    @error('state')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Is Funding</label>
                                    <select class="select w-100" name="is_funding">
                                        <option data-placeholder="true"></option>
                                        <option value="1"
                                            {{ old('is_funding') == '1' || (isset($student->is_funding) && $student->is_funding == 1) ? 'selected' : null }}>
                                            Yes</option>
                                        <option value="0"
                                            {{ old('is_funding') == '0' || (isset($student->is_funding) && $student->is_funding == 0) ? 'selected' : null }}>
                                            No</option>
                                    </select>
                                    @error('is_funding')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Register As</label>
                                    <select class="select w-100" name="register_as">
                                        <option data-placeholder="true"></option>
                                        <option value="student"
                                            {{ old('register_as') == 'student' || (isset($student->register_as) && $student->register_as == 'student') ? 'selected' : null }}>
                                            Student</option>
                                        <option value="parent"
                                            {{ old('register_as') == 'parent' || (isset($student->register_as) && $student->register_as == 'parent') ? 'selected' : null }}>
                                            Parent</option>
                                    </select>
                                    @error('is_funding')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>State / Region <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="state" type="text" class="form-control form-control-sm"
                                        placeholder="State / Region" id="state"
                                        value="{{ isset($student->state) ? $student->state : old('state') }}">
                                    @error('state')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>City </label>
                                    <input name="city" type="text" class="form-control form-control-sm"
                                        placeholder="City" id="city"
                                        value="{{ isset($student->city) ? $student->city : old('city') }}">
                                    @error('city')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>Postal Code</label>
                                    <input name="postal_code" type="text" class="form-control form-control-sm"
                                        placeholder="Postal Code"
                                        value="{{ isset($student->postal_code) ? $student->postal_code : old('postal_code') }}">
                                    @error('postal_code')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-md-12 mt-md-0 mt-2">
                        <div class="mb-2">
                            <label>Address</label>
                            <textarea name="address" class="form-control form-control-sm" placeholder="Address" rows="5">{{ isset($student->address) ? $student->address : old('address') }}</textarea>
                            @error('address')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- School  --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label>School Name <i class="text-danger font-weight-bold">*</i></label>
                            <select class="select w-100" id="schoolName" name="sch_id" onChange="addSchool();">
                                <option data-placeholder="true"></option>
                                @if (isset($schools) && count($schools) > 0)
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->sch_id }}"
                                            @if (isset($student->school)) {{ $student->sch_id == $school->sch_id ? 'selected' : null }}
                                            @else
                                                {{ old('sch_id') == $school->sch_id ? 'selected' : null }} @endif>
                                            {{ $school->sch_name }}</option>
                                    @endforeach
                                @endif
                                <option value="add-new" {{ old('sch_id') == 'add-new' ? 'selected' : null }}>Add New
                                    School</option>
                            </select>
                            @error('sch_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8 school d-none">
                        <div class="row row-cols-md-3 row-cols-1">
                            <div class="col">
                                <div class="mb-2">
                                    <label>Other School Name <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="sch_name" type="text" class="form-control form-control-sm"
                                        placeholder="Other School Name" autofocus value="{{ old('sch_name') }}">
                                    @error('sch_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col">
                                <div class="mb-2">
                                    <label>School Address <i class="text-danger font-weight-bold">*</i></label>
                                    <input type="text" name="sch_location" class="form-control form-control-sm" placeholder="School Address">
                                </div>
                            </div> --}}
                            <div class="col">
                                <div class="mb-2">
                                    <label>School Type <i class="text-danger font-weight-bold">*</i></label>
                                    <select class="select w-100" name="sch_type">
                                        <option data-placeholder="true"></option>
                                        <option value="International"
                                            {{ old('sch_type') == 'International' ? 'selected' : null }}>International
                                        </option>
                                        <option value="National" {{ old('sch_type') == 'National' ? 'selected' : null }}>
                                            National</option>
                                    </select>
                                    @error('sch_type')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-2">
                                    <label>Curriculum <i class="text-danger font-weight-bold">*</i></label>
                                    <select class="select w-100" name="sch_curriculum[]" multiple id="schCurriculum">
                                        <option data-placeholder="true"></option>
                                        @foreach ($curriculums as $curriculum)
                                            <option value="{{ $curriculum->id }}"
                                                {{ old('sch_curriculum') == $curriculum->name ? 'selected' : null }}>
                                                {{ $curriculum->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sch_curriculum')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 school d-none">
                        <div class="mb-2">
                            <label>School Market <i class="text-danger font-weight-bold">*</i> </label>
                            <select class="select w-100" name="sch_score">
                                <option data-placeholder="true"></option>
                                <option value="2" {{ old('sch_score') == 2 ? 'selected' : null }}>Low Market</option>
                                <option value="4" {{ old('sch_score') == 4 ? 'selected' : null }}>Mid Market</option>
                                <option value="5" {{ old('sch_score') == 5 ? 'selected' : null }}>Up Market</option>
                            </select>
                            @error('sch_score')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4" id="studentYear">
                        <div class="mb-2">
                            <label>Student Grade <i class="text-danger font-weight-bold">*</i></label>
                            @if(isset($student))
                            <small class="text-info ms-2">Input First Time ({{ date('M, dS Y', strtotime($student->created_at)) }})</small>
                            @endif
                            <select class="select w-100" id="grade" name="st_grade">
                                <option data-placeholder="true"></option>
                                @for ($grade = 1; $grade <= 12; $grade++)
                                    <option value="{{ $grade }}"
                                        @if (isset($student->st_grade)) {{ $student->st_grade == $grade ? 'selected' : null }}
                                        @else
                                            {{ old('st_grade') == $grade ? 'selected' : null }} @endif>
                                        {{ $grade }}</option>
                                @endfor
                                <option value="13"
                                    @if (isset($student->st_grade)) {{ $student->st_grade == 13 ? 'selected' : null }}
                                    @else
                                        {{ old('st_grade') == 13 ? 'selected' : null }} @endif>
                                    Not High School</option>

                            </select>
                            @error('st_grade')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2" id="studentYear">
                        <div class="mb-2">
                            <label>Graduation Year</label>
                            <input type="text" class="form-control form-control-sm" id="auto_grad_year" name="graduation_year" readonly value="{{isset($student) ? $student->graduation_year : old('graduation_year') }}">
                            @error('graduation_year')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-2">
                            <label>Gap Year</label>
                            <input type="text" class="form-control form-control-sm" name="gap_year" value="{{isset($student) ? $student->gap_year : old('gap_year') }}">
                            @error('gap_year')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <hr>
                {{-- Lead  --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label>Lead Source <i class="text-danger font-weight-bold">*</i></label>
                            <select class="select w-100" id="leadSource" name="lead_id">
                                <option data-placeholder="true"></option>
                                @if (isset($leads) && count($leads) > 0)
                                    @foreach ($leads as $lead)
                                        <option data-lead="{{ $lead->main_lead }}" value="{{ $lead->lead_id }}"
                                            @selected(old('lead_id') == $lead->lead_id)>{{ $lead->main_lead }}</option>
                                    @endforeach
                                    {{-- <option value="program">ALL-in Event</option>
                                <option value="edufair">Edufair External</option> --}}
                                    <option data-lead="KOL" value="kol" @selected(old('lead_id') == 'kol')>KOL</option>
                                @endif
                            </select>
                            @error('lead_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4 referral d-none">
                        <div class="mb-2">
                            <label>Referral Name <i class="text-danger font-weight-bold">*</i></label>
                            <input type="hidden" name="old_refname" id="old_refname" value="{{ isset($student->referral_code) ? $student->referral_name : null }}">
                            <select name="referral_code" id="referral_code" class="select w-100 select-referral">
                                @if(isset($student->referral_code))
                                    <option value="{{ $student->referral_code }}" selected="selected">{{ $student->referral_name }}</option>
                                @endif
                                {{-- <option data-placeholder="true"></option> --}}

                            </select>
                            @error('referral_code')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 program d-none">
                        <div class="mb-2">
                            <label>Event Name<i class="text-danger font-weight-bold">*</i>
                            </label>
                            <select class="select w-100" name="event_id">
                                <option data-placeholder="true"></option>
                                @if (isset($events) && count($events) > 0)
                                    @foreach ($events as $event)
                                        <option value="{{ $event->event_id }}"
                                            @if (isset($student->event->event_id)) {{ $student->event->event_id == $event->event_id ? 'selected' : null }}
                                            @else
                                                {{ old('event_id') == $event->event_id ? 'selected' : null }} @endif>
                                            {{ $event->event_title }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('event_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 edufair d-none">
                        <div class="mb-2">
                            <label>Edufair Name<i class="text-danger font-weight-bold">*</i>
                            </label>
                            <select class="select w-100" name="eduf_id">
                                <option data-placeholder="true"></option>
                                @if (isset($ext_edufair) && count($ext_edufair) > 0)
                                    @foreach ($ext_edufair as $edufair)
                                        <option value="{{ $edufair->id }}"
                                            @if (isset($student->external_edufair->id)) {{ $student->external_edufair->id == $edufair->id ? 'selected' : null }}
                                            @else
                                                {{ old('eduf_id') == $edufair->id ? 'selected' : null }} @endif>
                                            {{ $edufair->organizer_name . ' - ' . date('d M Y', strtotime($edufair->event_start)) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('eduf_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 kol d-none">
                        <div class="mb-2">
                            <label>KOL Name<i class="text-danger font-weight-bold">*</i>
                            </label>
                            <select class="select w-100" name="kol_lead_id">
                                <option data-placeholder="true"></option>
                                @if (isset($kols) && count($kols) > 0)
                                    @foreach ($kols as $kol)
                                        <option value="{{ $kol->lead_id }}"
                                            @if (isset($student->lead_id) && $student->lead->main_lead == 'KOL') {{ $student->lead_id == $kol->lead_id ? 'selected' : null }}
                                            @else
                                                {{ old('kol_lead_id') == $kol->lead_id ? 'selected' : null }} @endif>
                                            {{ $kol->sub_lead }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('kol_lead_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-2">
                            <label>Level of Interest <i class="text-danger font-weight-bold">*</i></label>
                            <select class="select w-100" id="levelInterest" name="st_levelinterest">
                                <option data-placeholder="true"></option>
                                <option value="High"
                                    @if (isset($student->st_levelinterest)) {{ $student->st_levelinterest == 'High' ? 'selected' : null }}
                                    @else
                                        {{ old('st_levelinterest') == 'High' ? 'selected' : null }} @endif>
                                    High</option>
                                <option value="Medium"
                                    @if (isset($student->st_levelinterest)) {{ $student->st_levelinterest == 'Medium' ? 'selected' : null }}
                                    @else
                                        {{ old('st_levelinterest') == 'Medium' ? 'selected' : null }} @endif>
                                    Medium</option>
                                <option value="Low"
                                    @if (isset($student->st_levelinterest)) {{ $student->st_levelinterest == 'Low' ? 'selected' : null }}
                                    @else
                                    {{ old('st_levelinterest') == 'Low' ? 'selected' : null }} @endif>
                                    Low</option>
                            </select>
                            @error('st_levelinterest')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    {{-- <div class="col-md-12">
                        <div class="mb-2">
                            <label>Interested Program</label>
                            <select class="select w-100" id="interestedProgram" name="prog_id[]" multiple>
                                <option data-placeholder="true"></option>
                                @if (isset($programs))
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->prog_id }}"
                                            {{ old('prog_id') == $program->prog_id ? 'selected' : null }}>
                                            {{ $program->program_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('prog_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div> --}}


                </div>

                <div class="line" style="margin-top:15px; margin-bottom:0px;"></div>
                <small class="text-info font-weight-bold">Study Aboard</small>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Year of Going Study Abroad</label>
                            <select class="select w-100" id="year" name="st_abryear">
                                <option data-placeholder="true"></option>
                                @for ($year = date('Y'); $year <= date('Y') + 5; $year++)
                                    <option value="{{ $year }}"
                                        {{ old('st_abryear') == $year ? 'selected' : null }}>{{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('st_abryear')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Country</label>
                            <select class="select w-100" id="countryStudy" name="st_abrcountry[]" multiple>
                                <option data-placeholder="true"></option>
                                @if (isset($countries))
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('st_abrcountry')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>University Destination</label>
                            <label for="" id=test></label>
                            <select class="select w-100" id="univDestination" name="st_abruniv[]" multiple>
                                <option data-placeholder="true"></option>
                            </select>
                            @error('st_abruniv')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Major</label>
                            <select class="select w-100" id="major" name="st_abrmajor[]" multiple>
                                <option data-placeholder="true"></option>
                                @if (isset($majors))
                                    @foreach ($majors as $major)
                                        <option value="{{ $major->id }}" @selected(
    isset($student->interestMajor) &&
        in_array(
            $major->id,
            $student
                ->interestMajor()
                ->pluck('tbl_major.id')
                ->toArray(),
        )
)
                                            @selected(old('st_abrmajor') == $major->id)>{{ $major->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('st_abrmajor')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <div class="mb-2">
                            <label>Notes</label>
                            <textarea name="st_note" cols="30" rows="10">{{ isset($student->st_note) ? $student->st_note : old('st_note') }}</textarea>
                            @error('st_note')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="line" style="margin-top:15px; margin-bottom:15px;"></div>
                <div class="row">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save2"></i>&nbsp;
                            Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>

    function addSchool() {
        var s = $('#schoolName').val();

        if (s == 'add-new') {
            $(".school").removeClass("d-none");
        } else {
            $(".school").addClass("d-none");
        }
    }

    $("#leadSource").on('change', function() {
        var lead = $(this).select2().find(":selected").data('lead')
        if (lead.includes('All-In Event')) {

            $(".program").removeClass("d-none")
            $(".edufair").addClass("d-none")
            $(".kol").addClass("d-none")
            $(".referral").addClass("d-none")

        } else if (lead.includes('External Edufair')) {

            $(".program").addClass("d-none")
            $(".edufair").removeClass("d-none")
            $(".kol").addClass("d-none")
            $(".referral").addClass("d-none")

        } else if (lead.includes('KOL')) {

            $(".program").addClass("d-none")
            $(".edufair").addClass("d-none")
            $(".kol").removeClass("d-none")
            $(".referral").addClass("d-none")

        } else if (lead.includes('Referral')) {

            $(".program").addClass("d-none")
            $(".edufair").addClass("d-none")
            $(".kol").addClass("d-none")
            $(".referral").removeClass("d-none")

        } else {

            $(".program").addClass("d-none")
            $(".edufair").addClass("d-none")
            $(".kol").addClass("d-none")
            $(".referral").addClass("d-none")


        }
    })

    $("#grade").on('change', async function() {
        var grade = $(this).val()
        var year = '{{isset($student) ? date("Y", strtotime($student->created_at)) : date("Y") }}'
        var month = '{{isset($student) ? date("m", strtotime($student->created_at)) : date("m") }}'
        
        var graduation_year = parseInt(month) > 7 ? (12 - parseInt(grade)) + parseInt(year) + 1 : (12 - parseInt(grade)) + parseInt(year)
        $('#auto_grad_year').val(graduation_year);
    })

    $('#referral_code').on('change', function(){
        $('#old_refname').val($("option:selected", this).text())
    })


    const anotherDocument = () => {
        @if (isset($student->interestUniversities))
            var st_abruniv = new Array();
            @foreach ($student->interestUniversities as $university)
                st_abruniv.push("{{ $university->univ_id }}")
            @endforeach

            $("#univDestination").select2().val(st_abruniv).trigger('change')
        @elseif (old('st_abruniv'))

            var st_abruniv = new Array();
            @foreach (old('st_abruniv') as $key => $val)
                st_abruniv.push("{{ $val }}")
            @endforeach

            $("#univDestination").select2().val(st_abruniv).trigger('change')
        @endif
    }

    $("#countryStudy").on('change', async function() {
        var countries = $(this).val()
        if (countries.length == 0) {
            $("#univDestination").html('')
            return
        }

        var get = ""

        countries.forEach(function(currentValue, index, arr) {
            if (index == 0)
                get += "?country[]=" + currentValue
            else
                get += "&country[]=" + currentValue
        })

        // reset univ Destination html
        $("#univDestination").html('');

        var html = ""
        var link = "{{ route('student.create') }}" + get
        showLoading()
        await axios.get(link)
            .then(function(response) {

                // handle success
                let data = response.data
                data.forEach(function(currentValue, index, arr) {
                    html += "<option value='" + arr[index].univ_id + "'>" + arr[index]
                        .univ_name + " - " + arr[index].univ_country + "</option>"
                })

                $("#univDestination").append(html)
                initSelect2("#univDestination")
                Swal.close()
            })
            .catch(function(error) {
                // handle error
                Swal.close()
                notification(error.response.data.success, error.response.data.message)
            })

        anotherDocument()

    })

    $(document).ready(function() {

        var baseUrl = "{{ url('/') }}/api/get/referral/list";

        $(".select-referral").select2({
            placeholder: 'Referral Name...',
            // width: '350px',
            allowClear: true,
            ajax: {
                url: baseUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    }
                },
                cache: true
            }
        });

        const documentReady = () => {


            @if (old('sch_id') !== null && old('sch_id') == 'add-new')
                $("#schoolName").select2().val("{{ old('sch_id') }}").trigger('change')

                @if (!empty(old('sch_curriculum')) && count(old('sch_curriculum')) > 0)
                    var sch_curriculum = new Array();
                    @foreach (old('sch_curriculum') as $key => $val)
                        sch_curriculum.push("{{ $val }}")
                    @endforeach

                    $("#schCurriculum").val(sch_curriculum).trigger('change')
                @endif
            @endif

            @if (isset($student->st_abryear))
                $("#year").select2().val("{{ $student->st_abryear }}").trigger('change')
            @elseif (old('st_abryear') !== null)
                $("#year").select2().val("{{ old('st_abryear') }}").trigger('change')
            @endif

            @if (isset($student->destinationCountries))
                var st_abrcountry = new Array();
                @foreach ($student->destinationCountries as $country)
                    st_abrcountry.push("{{ $country->id }}")
                @endforeach

                $("#countryStudy").val(st_abrcountry).trigger('change')
            @elseif (!empty(old('st_abrcountry')) && count(old('st_abrcountry')) > 0)
                var st_abrcountry = new Array();
                @foreach (old('st_abrcountry') as $key => $val)
                    st_abrcountry.push("{{ $val }}")
                @endforeach

                $("#countryStudy").val(st_abrcountry).trigger('change')
            @endif

            @if (old('st_abrmajor'))
                var st_abrmajor = new Array();
                @foreach (old('st_abrmajor') as $key => $val)
                    st_abrmajor.push("{{ $val }}")
                @endforeach

                $("#major").val(st_abrmajor).trigger('change')
            @endif

            @if (old('referral_code'))
                // Set the value, creating a new option if necessary
                if ($('#referral_code').find("option[value= {{ old('referral_code') }} ]").length) {
                    $('#referral_code').val('{{ old("referral_code") }}').trigger('change');
                } else { 
                    // Create a DOM Option and pre-select by default
                    var newOption = new Option('{{ old("old_refname") }}', '{{ old("referral_code") }}', true, true);
                    // Append it to the select
                    $('#referral_code').append(newOption).trigger('change');
                } 
            @endif

            // @if (isset($student->interestPrograms))
            //     var prog_id = new Array();
            //     @foreach ($student->interestPrograms as $program)
            //         prog_id.push("{{ $program->prog_id }}")
            //     @endforeach

            //     $("#interestedProgram").val(prog_id).trigger('change')
            // @elseif (old('prog_id') !== null && count(old('prog_id')) > 0)
            //     var prog_id = new Array();
            //     @foreach (old('prog_id') as $key => $val)
            //         prog_id.push("{{ $val }}")
            //     @endforeach

            //     $("#interestedProgram").val(prog_id).trigger('change')
            //     anotherDocument()
            // @endif

            @if (isset($student->lead_id))
                @if ($student->lead->main_lead == 'KOL')
                    $("#leadSource").select2().val("kol").trigger('change')
                @else
                    $("#leadSource").select2().val("{{ $student->lead_id }}").trigger('change')
                @endif
            @elseif (old('lead_id') !== null)
                $("#leadSource").select2().val("{{ old('lead_id') }}").trigger('change')
            @endif
        }

        documentReady()
    })
</script>
@endpush
