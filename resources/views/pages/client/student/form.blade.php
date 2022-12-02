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
            <form action="" method="post">
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
                                    <input name="st_firstname" type="text" class="form-control form-control-sm"
                                        placeholder="First name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Last Name</label>
                                    <input name="st_lastname" type="text" class="form-control form-control-sm"
                                        placeholder="Last name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>E-mail <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="st_mail" type="text" class="form-control form-control-sm"
                                        placeholder="E-mail">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Phone Number <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="st_phone" type="text" class="form-control form-control-sm"
                                        placeholder="Phone Number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Date of Birth <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="st_dob" type="date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Instagram</label>
                                    <input name="st_insta" type="text" class="form-control form-control-sm"
                                        placeholder="Instagram">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>State / Region <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="st_state" type="text" class="form-control form-control-sm"
                                        placeholder="State / Region" id="state">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>City</label>
                                    <input name="st_city" type="text" class="form-control form-control-sm"
                                        placeholder="City" id="city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label>Postal Code</label>
                                    <input name="st_pc" type="text" class="form-control form-control-sm"
                                        placeholder="Postal Code">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-2">
                            <label>Address</label>
                            <textarea name="st_address" class="form-control form-control-sm" placeholder="Address" rows="5"></textarea>
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
                                                <option value="add-new">Add New Parent</option>
                                            </select>
                                        </div>

                                        {{-- New Parent Field  --}}
                                        <div class="row parent d-none">
                                            <div class="col-md-6 mb-2">
                                                <small>First Name</small>
                                                <input id="pFName" name="pr_firstname" type="text"
                                                    placeholder="First Name" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small>Last Name</small>
                                                <input name="pr_lastname" type="text" placeholder="Last Name"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small>E-mail</small>
                                                <input name="pr_mail" type="text" placeholder="E-mail"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small>Phone Number</small>
                                                <input name="pr_phone" type="text" placeholder="Phone Number"
                                                    class="form-control form-control-sm">
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
                            <label>School Name</label>
                            <select class="select w-100" id="schoolName" name="sch_id" onChange="addSchool();">
                                <option data-placeholder="true"></option>
                                <option value="add-new">Add New School</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8 school d-none">
                        <div class="row row-cols-md-3 row-cols-1">
                            <div class="col">
                                <div class="mb-2">
                                    <label>Other School Name <i class="text-danger font-weight-bold">*</i></label>
                                    <input name="sch_name" type="text" class="form-control form-control-sm"
                                        placeholder="Other School Name" autofocus>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-2">
                                    <label>School Type</label>
                                    <select class="select w-100" name="st_currentsch">
                                        <option data-placeholder="true"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-2">
                                    <label>Curriculum</label>
                                    <select class="select w-100" name="st_currentsch">
                                        <option data-placeholder="true"></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 school d-none">
                        <div class="mb-2">
                            <label>School Market</label>
                            <select class="select w-100" name="st_currentsch">
                                <option data-placeholder="true"></option>
                                <option value="2">Low Market</option>
                                <option value="4">Mid Market</option>
                                <option value="5">Up Market</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4" id="studentYear">
                        <div class="mb-2">
                            <label>Student Grade</label>
                            <select class="select w-100" id="grade" name="st_grade">
                                <option data-placeholder="true"></option>
                                <option value="13">Not High School</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4" id="studentYear">
                        <div class="mb-2">
                            <label>Graduation Year</label>
                            <select class="select w-100" id="graduation_year" name="st_graduatioan_year">
                                <option data-placeholder="true"></option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                {{-- Lead  --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label>Lead Source <i class="text-danger font-weight-bold">*</i></label>
                            <select class="select w-100" id="leadSource" name="lead_id" onchange="leads()">
                                <option data-placeholder="true"></option>
                                <option value="program">ALL-in Program</option>
                                <option value="edufair">Edufair External</option>
                                <option value="kol">KOL</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 program d-none">
                        <div class="mb-2">
                            <label>Program Name<i class="text-danger font-weight-bold">*</i>
                            </label>
                            <select class="select w-100" name="prog_id">
                                <option data-placeholder="true"></option>

                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 edufair d-none">
                        <div class="mb-2">
                            <label>Edufair Name<i class="text-danger font-weight-bold">*</i>
                            </label>
                            <select class="select w-100" name="eduf_id">
                                <option data-placeholder="true"></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 kol d-none">
                        <div class="mb-2">
                            <label>KOL Name<i class="text-danger font-weight-bold">*</i>
                            </label>
                            <select class="select w-100" name="lead_id">
                                <option data-placeholder="true"></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-2">
                            <label>Level of Interest</label>
                            <select class="select w-100" id="levelInterest" name="st_levelinterest">
                                <option data-placeholder="true"></option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-2">
                            <label>Interested Program</label>
                            <select class="select w-100" id="interestedProgram" name="prog_id[]" multiple>
                                <option data-placeholder="true"></option>
                            </select>
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
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Country</label>
                            <select class="select w-100" id="countryStudy" name="st_abrcountry[]" multiple>
                                <option data-placeholder="true"></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Univ Destination</label>
                            <select class="select w-100" id="univDestination" name="st_abruniv[]" multiple>
                                <option data-placeholder="true"></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Major</label>
                            <select class="select w-100" id="major" name="st_abrmajor[]" multiple>
                                <option data-placeholder="true"></option>
                            </select>

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
    </script>

@endsection
