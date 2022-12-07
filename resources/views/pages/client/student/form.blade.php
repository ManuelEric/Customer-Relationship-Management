@extends('layout.main')

@section('title', 'Student - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('client/mentee/potential') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Student
        </a>
    </div>


    <div class="card rounded">
        <div class="card-header">
            <h5 class="my-1 p-0">
                <i class="bi bi-info-circle me-1"></i>
                Student Detail
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('student.store') }}" method="post">
                @csrf
                <div class="row align-items-center">
                    <div class="col-4 text-center">
                        <img src="{{ asset('img/mentee.jpg') }}" class="w-50">
                    </div>
                    <div class="col-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>First Name <i class="text-danger font-weight-bold">*</i>
                                    </label>
                                    <input name="first_name" type="text" class="form-control form-control-sm"
                                        placeholder="First name" value="{{ old('first_name') }}">
                                    @error('first_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Last Name</label>
                                    <input name="last_name" type="text" class="form-control form-control-sm"
                                        placeholder="Last name" value="{{ old('last_name') }}">
                                    @error('last_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>E-mail <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="mail" type="text" class="form-control form-control-sm"
                                        placeholder="E-mail" value="{{ old('mail') }}">
                                    @error('mail')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Phone Number <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="phone" type="text" class="form-control form-control-sm"
                                        placeholder="Phone Number" value="{{ old('phone') }}">
                                    @error('phone')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Date of Birth <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="dob" type="date" class="form-control form-control-sm" value="{{ old('dob') }}">
                                    @error('dob')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Instagram</label>
                                    <input name="insta" type="text" class="form-control form-control-sm"
                                        placeholder="Instagram" value="{{ old('insta') }}">
                                    @error('insta')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>State / Region <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="state" type="text" class="form-control form-control-sm"
                                        placeholder="State / Region" id="state" value="{{ old('state') }}">
                                    @error('state')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>City</label>
                                    <input name="city" type="text" class="form-control form-control-sm"
                                        placeholder="City" id="city" value="{{ old('city') }}">
                                    @error('city')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>Postal Code</label>
                                    <input name="postal_code" type="text" class="form-control form-control-sm"
                                        placeholder="Postal Code" value="{{ old('postal_code') }}">
                                    @error('postal_code')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-2">
                            <label>Address</label>
                            <textarea name="address" class="form-control form-control-sm" placeholder="Address" rows="5">{{ old('address') }}</textarea>
                            @error('address')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card my-3">
                    <div class="card-header">
                        <h6 class="my-1 p-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Parent Detail
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- Parent  --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row align-items-start">
                                    <div class="col-md-8">
                                        <div class="mb-2">
                                            <label>Parent's Name</label>
                                            <select class="select w-100" name="pr_id" id="prName"
                                                onchange="addParent()">
                                                <option data-placeholder="true"></option>
                                                @if (isset($parents))
                                                    @foreach ($parents as $parent)
                                                        <option value="{{ $parent->id }}"
                                                            {{ old('pr_id') == $parent->id ? "selected" : null }}
                                                            >{{ $parent->first_name.' '.$parent->last_name }}</option>
                                                    @endforeach
                                                @endif
                                                <option value="add-new" {{ old('pr_id') == "add-new" ? "selected" : null }}>Add New Parent</option>
                                            </select>
                                            @error('pr_id')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        {{-- New Parent Field  --}}
                                        <div class="row parent d-none">
                                            <div class="col-md-6 mb-2">
                                                <small>First Name <i class="text-danger font-weight-bold">*</i></small>
                                                <input id="pFName" name="pr_firstname" type="text"
                                                    placeholder="First Name" class="form-control form-control-sm" value="{{ old('pr_firstname') }}">
                                                @error('pr_firstname')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small>Last Name</small>
                                                <input name="pr_lastname" type="text" placeholder="Last Name"
                                                    class="form-control form-control-sm" value="{{ old('pr_lastname') }}">
                                                @error('pr_lastname')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small>E-mail </small>
                                                <input name="pr_mail" type="text" placeholder="E-mail"
                                                    class="form-control form-control-sm" value="{{ old('pr_mail') }}">
                                                @error('pr_mail')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small>Phone Number <i class="text-danger font-weight-bold">*</i></small>
                                                <input name="pr_phone" type="text" placeholder="Phone Number"
                                                    class="form-control form-control-sm" value="{{ old('pr_phone') }}">
                                                @error('pr_phone')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center parent d-none">
                                        <img src="{{ asset('img/parent.jpeg') }}" alt="" class="w-50">
                                    </div>
                                </div>
                            </div>
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
                                            {{ old('sch_id') == $school->sch_id ? "selected" : null }}
                                            >{{ $school->sch_name }}</option>
                                    @endforeach
                                @endif
                                <option value="add-new" {{ old('sch_id') == "add-new" ? "selected" : null }}>Add New School</option>
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
                                        <option value="International" {{ old('sch_type') == "International" ? "selected" : null }}>International</option>
                                        <option value="National" {{ old('sch_type') == "National" ? "selected" : null }}>National</option>
                                    </select>
                                    @error('sch_type')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-2">
                                    <label>Curriculum <i class="text-danger font-weight-bold">*</i></label>
                                    <select class="select w-100" name="sch_curriculum">
                                        <option data-placeholder="true"></option>
                                        @foreach ($curriculums as $curriculum)
                                            <option value="{{ $curriculum->name }}" 
                                                    {{ old('sch_curriculum') == $curriculum->name ? "selected" : null }}
                                                >{{ $curriculum->name }}</option>
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
                            <label>School Market</label>
                            <select class="select w-100" name="sch_score">
                                <option data-placeholder="true"></option>
                                <option value="2" {{ old('sch_score') == 2 ? "selected" : null }}>Low Market</option>
                                <option value="4" {{ old('sch_score') == 4 ? "selected" : null }}>Mid Market</option>
                                <option value="5" {{ old('sch_score') == 5 ? "selected" : null }}>Up Market</option>
                            </select>
                            @error('sch_score')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4" id="studentYear">
                        <div class="mb-2">
                            <label>Student Grade <i class="text-danger font-weight-bold">*</i></label>
                            <select class="select w-100" id="grade" name="st_grade">
                                <option data-placeholder="true"></option>
                                @for ($grade = 1 ; $grade <= 12 ; $grade++)
                                    <option value="{{ $grade }}"
                                        {{ old('st_grade') == $grade ? "selected" : null }}
                                    >{{ $grade }}</option>
                                @endfor
                                <option value="13" {{ old('st_grade') == 13 ? "selected" : null }}>Not High School</option>
                            </select>
                            @error('st_grade')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4" id="studentYear">
                        <div class="mb-2">
                            <label>Graduation Year</label>
                            <select class="select w-100" id="graduation_year" name="graduation_year">
                                <option data-placeholder="true"></option>
                                @for ($year = date('Y') ; $year >= 1998 ; $year--)
                                    <option value="{{ $year }}"
                                        {{ old('graduation_year') == $year ? "selected" : null }}
                                    >{{ $year }}</option>
                                @endfor
                            </select>
                            @error('graduation_year')
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
                                            {{ old('lead_id') == $lead->lead_id ? "selected" : null }}
                                        >{{ $lead->main_lead }}</option>
                                @endforeach
                                {{-- <option value="program">ALL-in Event</option>
                                <option value="edufair">Edufair External</option> --}}
                                <option data-lead="KOL" value="kol" {{ old('lead_id') == "kol" ? "selected" : null }}>KOL</option>
                                @endif
                            </select>
                            @error('lead_id')
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
                                                {{ old('event_id') == $event->event_id ? "selected" : null }}
                                            >{{ $event->event_title }}</option>
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
                                                {{ old('eduf_id') == $edufair->id ? "selected" : null }}
                                            >{{ $edufair->title }}</option>
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
                                                {{ old('kol_lead_id') == $kol->lead_id ? "selected" : null }}
                                            >{{ $kol->sub_lead }}</option>
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
                                <option value="High" {{ old('st_levelinterest') == "High" ? "selected" : null }}>High</option>
                                <option value="Medium" {{ old('st_levelinterest') == "Medium" ? "selected" : null }}>Medium</option>
                                <option value="Low" {{ old('st_levelinterest') == "Low" ? "selected" : null }}>Low</option>
                            </select>
                            @error('st_levelinterest')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-2">
                            <label>Interested Program</label>
                            <select class="select w-100" id="interestedProgram" name="prog_id[]" multiple>
                                <option data-placeholder="true"></option>
                                @if (isset($programs))
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->prog_id }}"
                                                {{ old('prog_id') == $program->prog_id ? "selected" : null }}
                                            >{{ $program->prog_program }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('prog_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="line" style="margin-top:15px; margin-bottom:0px;"></div>
                <small class="text-info font-weight-bold">Study Aboard</small>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Year of Going Study Abroad</label>
                            <select class="select w-100" id="year" name="st_abryear">
                                <option data-placeholder="true"></option>
                                @for ($year = date('Y') ; $year <= date('Y')+5 ; $year++)
                                    <option value="{{ $year }}"
                                        {{ old('st_abryear') == $year ? "selected" : null }}
                                    >{{ $year }}</option>
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
                                        <option value="{{ $country->univ_country }}">{{ $country->univ_country }}</option>
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
                            <label>Univ Destination</label>
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
                                        <option value="{{ $major->id }}"
                                                {{ old('st_abrmajor') == $major->id ? "selected" : null }}
                                            >{{ $major->name }}</option>
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
                            <textarea name="st_note" cols="30" rows="10">{{ old('st_note') }}</textarea>
                            @error('st_note')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="line" style="margin-top:15px; margin-bottom:15px;"></div>
                <div class="row">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i>&nbsp;
                            Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addParent() {
            var p = $('#prName').val();

            if (p == 'add-new') {
                $(".parent").removeClass("d-none");
            } else {
                $(".parent").addClass("d-none");
            }
        }

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

            } else if (lead.includes('External Edufair')) {

                $(".program").addClass("d-none")
                $(".edufair").removeClass("d-none")
                $(".kol").addClass("d-none")

            } else  if (lead.includes('KOL')) {

                $(".program").addClass("d-none")
                $(".edufair").addClass("d-none")
                $(".kol").removeClass("d-none")

            }
        })

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
            Swal.showLoading()
            await axios.get(link)
                .then(function(response) {
                    // handle success
                    let data = response.data
                    data.forEach(function(currentValue, index, arr) {
                        html += "<option value='"+arr[index].univ_id+"'>"+arr[index].univ_name+"</option>"
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
        })

        $(document).ready(function() {

            @if (old('pr_id') !== NULL && old('pr_id') == "add-new")
                $("#prName").select2().val("{{ old('pr_id') }}").trigger('change')
            @endif

            @if (old('sch_id') !== NULL && old('sch_id') == "add-new")
                $("#schoolName").select2().val("{{ old('pr_id') }}").trigger('change')
            @endif

            @if (old('st_abryear') !== NULL)
                $("#year").select2().val("{{ old('st_abryear') }}").trigger('change')
            @endif
            
            @if (!empty(old('st_abrcountry')) && count(old('st_abrcountry')) > 0)
                var st_abrcountry = new Array();
                @foreach (old('st_abrcountry') as $key => $val)
                    st_abrcountry.push("{{ $val }}")
                @endforeach

                $("#countryStudy").val(st_abrcountry).trigger('change')

            @endif

            @if (old('prog_id') !== NULL && count(old('prog_id')) > 0)
                var prog_id = new Array();
                @foreach (old('prog_id') as $key => $val)
                    prog_id.push("{{ $val }}")
                @endforeach

                $("#interestedProgram").val(prog_id).trigger('change')
            @endif
        })

    </script>

@endsection
