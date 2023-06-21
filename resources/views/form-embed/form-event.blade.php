@extends('app')
@section('title', 'Registration Form')
@section('css')
    <style>
        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }
    </style>
@endsection
@section('body')
    <div class="container">
        <div class="row">
            <div class="col-12 p-3">
                <div class="card">
                    <div class="card-header text-white" style="background: #233872;">
                        <h5 class="my-1">
                            Let us know you better by filling out this form!
                        </h6>
                    </div>
                    <div class="card-body text-white" style="background: #233872;">
                        <form action="{{ url('form/event') }}" method="POST">
                            @csrf
                            <input type="hidden" name="event" value="">
                            <input type="hidden" name="user_type" value="Student">
                            <div class="row row-cols-lg-2 row-cols-1 align-items-start g-3 mb-3">
                                <div class="col">
                                    <label for="i_name">Name</label>
                                    <input type="text" name="name" id="i_name" value="{{ old('name') }}" class="form-control">
                                    @error('name')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="i_childname">Child's Name</label>
                                    <input type="text" name="child_name" id="i_childname" value="{{ old('child_name') }}" class="form-control">
                                    <small class="text-warning">* if you are a student, then fill in this column with your
                                        name</small>
                                    @error('child_name')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row row-cols-lg-2 row-cols-1 align-items-start g-3 mb-3">
                                <div class="col">
                                    <label for="i_email">Email</label>
                                    <input type="email" name="email" id="i_email" value="{{ old('email') }}" class="form-control">
                                    @error('email')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="i_phone">Phone Number</label>
                                    <input type="text" name="phone" id="i_phone" value="{{ old('phone') }}" class="form-control">
                                    @error('phone')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row row-cols-lg-2 row-cols-1 align-items-start g-3 mb-3">
                                <div class="col">
                                    <label for="i_email">Email Child</label>
                                    <input type="email" name="email_child" id="i_email" value="{{ old('email_child') }}" class="form-control">
                                    @error('email_child')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="i_phone">Phone Number Child</label>
                                    <input type="text" name="phone_child" id="i_phone" value="{{ old('phone_child') }}" class="form-control">
                                    @error('phone_child')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row row-cols-lg-2 row-cols-1 align-items-start g-3 mb-3">
                                <div class="col">
                                    {{-- <select type="school" name="school" id="i_school" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($schools as $school)
                                        <option value="{{ $school->sch_id }}">
                                                {{$school->sch_name }}
                                            </option>
                                            @endforeach
                                    </select> --}}
                                    
                                    <div class="exist-school">
                                        <label for="i_school">School</label>
                                        <select name="school" id="i_school" class="select w-100" onChange="addSchool();">
                                        <option data-placeholder="true"></option>
                                            @foreach ($schools as $school)
                                                <option value="{{ $school->sch_id }}"  {{ old('school') == $school->sch_id ? "selected" : null }}>{{ $school->sch_name }}</option>
                                            @endforeach
                                        <option value="add-new" {{ old('school') == "add-new" ? "selected" : null }}>Add New School</option>
                                    </select>
                                    </div>
                                    <div class="other-school d-none">
                                        <label>Other School Name <sup class="text-danger">*</sup></label>
                                        <input name="other_school" type="text" class="form-control form-control-sm"
                                        placeholder="Other School Name" autofocus  value="{{ old('other_school') }}">
                                    </div>
                                    @error('school')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                    @error('other_school')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="i_grade">Expected Graduation Year</label>
                                    <select type="text" name="grade" id="i_grade" class="select w-100">
                                        <option value="{{old('grade')}}"></option>
                                        @for ($i = date('Y'); $i < date('Y') + 5; $i++)
                                            <option value="{{ $i }}" {{old('grade') == $i ? 'selected' : null}}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('grade')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row row-cols-1">
                                <div class="col mb-3">
                                    <label for="i_leadsource">I know this event from</label>
                                    <select type="text" name="leadsource" id="i_leadsource" class="select w-100">
                                        <option data-placeholder="true"></option>
                                            @foreach ($leads as $lead)
                                                <option value="{{ $lead->lead_id }}" {{old('leadsource') == $lead->lead_id ? 'selected' : null}}>{{ $lead->main_lead == 'KOL' ? $lead->sub_lead : $lead->main_lead }}</option>
                                            @endforeach
                                    </select>
                                    @error('leadsource')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-light text-primary">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        @if (old('school') !== NULL && old('school') == "add-new")
            $("#i_school").select2().val("{{ old('school') }}").trigger('change')
        @endif
    

        function addSchool() {
            var s = $('#i_school').val();

            if (s == 'add-new') {
                $(".other-school").removeClass("d-none");
                $(".exist-school").addClass("d-none");
            } else {
                $(".exist-school").removeClass("d-none");
                $(".other-school").addClass("d-none");
            }
        }
    </script>
@endsection

