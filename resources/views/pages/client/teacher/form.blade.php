@extends('layout.main')

@section('title', 'Teacher/Counselor ')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Teacher/Counselor</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Teacher/Counselor</li>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="my-1 p-0">
                <i class="bi bi-info-circle me-1"></i>
                Teachers Detail
            </h5>
        </div>
        <div class="card-body">

            @if($errors->any())
                {!! implode('', $errors->all('<div>:message</div>')) !!}
            @endif

            <form action="{{ isset($teacher_counselor) ? route('teacher-counselor.update', ['teacher_counselor' => $teacher_counselor->id]) : route('teacher-counselor.store') }}" method="post">
                @csrf
                @if (isset($teacher_counselor))
                    @method('PUT')
                @endif
                <div class="row flex-md-row flex-column align-items-center">
                    <div class="col-md-4 col text-center mb-md-0 mb-2">
                        <img src="{{ asset('img/teacher.jpg') }}" class="w-50">
                    </div>
                    <div class="col-md-8 col">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>First Name <i class="text-danger font-weight-bold">*</i>
                                    </label>
                                    <input name="first_name" type="text" class="form-control form-control-sm"
                                        placeholder="First name" value="{{ isset($teacher_counselor->first_name) ? $teacher_counselor->first_name : old('first_name') }}">
                                    @error('first_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Last Name</label>
                                    <input name="last_name" type="text" class="form-control form-control-sm"
                                        placeholder="Last name" value="{{ isset($teacher_counselor->last_name) ? $teacher_counselor->last_name : old('last_name') }}">
                                    @error('last_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>E-mail <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="mail" type="text" class="form-control form-control-sm"
                                        placeholder="E-mail" value="{{ isset($teacher_counselor->mail) ? $teacher_counselor->mail : old('mail') }}">
                                    @error('mail')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Phone Number <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="phone" type="text" class="form-control form-control-sm"
                                        placeholder="Phone Number" value="{{ isset($teacher_counselor->phone) ? $teacher_counselor->phone : old('phone') }}">
                                    @error('phone')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Date of Birth </label>
                                    <input name="dob" type="date" class="form-control form-control-sm" value="{{ isset($teacher_counselor->dob) ? $teacher_counselor->dob : old('dob') }}">
                                    @error('dob')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Instagram</label>
                                    <input name="insta" type="text" class="form-control form-control-sm"
                                        placeholder="Instagram" value="{{ isset($teacher_counselor->insta) ? $teacher_counselor->insta : old('insta') }}">
                                    @error('insta')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>State / Region <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="state" type="text" class="form-control form-control-sm"
                                        placeholder="State / Region" id="state" value="{{ isset($teacher_counselor->state) ? $teacher_counselor->state : old('state') }}">
                                    @error('state')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>City</label>
                                    <input name="city" type="text" class="form-control form-control-sm"
                                        placeholder="City" id="city" value="{{ isset($teacher_counselor->city) ? $teacher_counselor->city : old('city') }}">
                                    @error('city')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>Postal Code</label>
                                    <input name="postal_code" type="text" class="form-control form-control-sm"
                                        placeholder="Postal Code" value="{{ isset($teacher_counselor->postal_code) ? $teacher_counselor->postal_code : old('postal_code') }}">
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
                            <textarea name="address" class="form-control form-control-sm" placeholder="Address" rows="5">{{ isset($teacher_counselor->address) ? $teacher_counselor->address : old('address') }}</textarea>
                            @error('address')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- School  --}}
                <div class="row my-3">
                    <div class="col-md-3">
                        <div class="mb-2">
                            <label>School Name <i class="text-danger font-weight-bold">*</i></label>
                            <select class="select w-100" id="schoolName" name="sch_id" onChange="addSchool();">
                                <option data-placeholder="true"></option>
                                @if (isset($schools) && count($schools) > 0)
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->sch_id }}"
                                            @if (isset($teacher_counselor->school))
                                                {{ $teacher_counselor->sch_id == $school->sch_id ? "selected" : null }}
                                            @else
                                                {{ old('sch_id') == $school->sch_id ? "selected" : null }}
                                            @endif
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
                                    <select class="select w-100" name="sch_curriculum[]" multiple id="schCurriculum">
                                        <option data-placeholder="true"></option>
                                        @foreach ($curriculums as $curriculum)
                                            <option value="{{ $curriculum->id }}" 
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
                            <label>School Market <i class="text-danger font-weight-bold">*</i>                             </label>
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
                </div>

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
                    <div class="col-md-4 referral d-none">
                        <div class="mb-2">
                            <label>Referral Name <i class="text-danger font-weight-bold">*</i></label>
                            <input type="hidden" name="old_refname" id="old_refname" value="{{ isset($teacher_counselor->referral_code) ? $teacher_counselor->referral_name : null }}">
                            <select name="referral_code" id="referral_code" class="select w-100 select-referral">
                                @if(isset($teacher_counselor->referral_code))
                                    <option value="{{ $teacher_counselor->referral_code }}" selected="selected">{{ $teacher_counselor->referral_name }}</option>
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
                                            @if (isset($teacher_counselor->event->event_id))
                                                {{ $teacher_counselor->event->event_id == $event->event_id ? "selected" : null }}
                                            @else
                                                {{ old('event_id') == $event->event_id ? "selected" : null }}
                                            @endif
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
                                            @selected(isset($teacher_counselor->external_edufair->id) && $teacher_counselor->external_edufair->id == $edufair->id)
                                            @selected(old('eduf_id') == $edufair->id)
                                            >{{ $edufair->organizer_name }}</option>
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
                                            @if (isset($teacher_counselor->lead_id) && $teacher_counselor->lead_id == "LS017")
                                                {{ $teacher_counselor->lead_id == $kol->lead_id ? "selected" : null }}
                                            @else
                                                {{ old('kol_lead_id') == $kol->lead_id ? "selected" : null }}
                                            @endif
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
                                <option value="High" 
                                    @if (isset($teacher_counselor->st_levelinterest))
                                        {{ $teacher_counselor->st_levelinterest == "High" ? "selected" : null }}
                                    @else
                                        {{ old('st_levelinterest') == "High" ? "selected" : null }}
                                    @endif
                                    >High</option>
                                <option value="Medium" 
                                    @if (isset($teacher_counselor->st_levelinterest))
                                        {{ $teacher_counselor->st_levelinterest == "Medium" ? "selected" : null }}
                                    @else
                                        {{ old('st_levelinterest') == "Medium" ? "selected" : null }}
                                    @endif
                                    >Medium</option>
                                <option value="Low" 
                                    @if (isset($teacher_counselor->st_levelinterest))
                                        {{ $teacher_counselor->st_levelinterest == "Low" ? "selected" : null }}
                                    @else
                                    {{ old('st_levelinterest') == "Low" ? "selected" : null }}
                                    @endif
                                    >Low</option>
                            </select>
                            @error('st_levelinterest')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="line" style="margin-top:15px; margin-bottom:15px;"></div>
                <div class="row">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save2"></i></i>&nbsp;
                            Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>

    $(document).ready(function(){
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

        @if (old('referral_code') !== NULL)
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
    })

    $('#referral_code').on('change', function(){
        $('#old_refname').val($("option:selected", this).text())
    })

    function addSchool() {
        var s = $('#schoolName').val();

        if (s == 'add-new') {
            $(".school").removeClass("d-none");
        } else {
            $(".school").addClass("d-none");
        }
    }

    function leads() {
        let l = $("#leadSource").val();
        if (l == "program") {
            $(".program").removeClass("d-none");
            $(".edufair").addClass("d-none");
            $(".kol").addClass("d-none");
        } else
        if (l == "edufair") {
            $(".program").addClass("d-none");
            $(".edufair").removeClass("d-none");
            $(".kol").addClass("d-none");
        } else
        if (l == "kol") {
            $(".program").addClass("d-none");
            $(".edufair").addClass("d-none");
            $(".kol").removeClass("d-none");
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

        } else  if (lead.includes('KOL')) {

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

    @if (old('sch_id') !== NULL && old('sch_id') == "add-new")
        $("#schoolName").select2().val("{{ old('sch_id') }}").trigger('change')

        @if (!empty(old('sch_curriculum')) && count(old('sch_curriculum')) > 0)
            var sch_curriculum = new Array();
            @foreach (old('sch_curriculum') as $key => $val)
                sch_curriculum.push("{{ $val }}")
            @endforeach

            $("#schCurriculum").val(sch_curriculum).trigger('change')
        @endif
    @endif

    @if (isset($teacher_counselor->lead_id))
        @if ($teacher_counselor->lead_id == "LS017")
            $("#leadSource").select2().val("kol").trigger('change')
        @else
            $("#leadSource").select2().val("{{ $teacher_counselor->lead_id }}").trigger('change')
        @endif
    @elseif (old('lead_id') !== NULL)
        $("#leadSource").select2().val("{{ old('lead_id') }}").trigger('change')
    @endif

</script>
@endpush
