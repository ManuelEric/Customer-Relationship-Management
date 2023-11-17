@extends('layout.main')

@section('title', 'Student')

@push('styles')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card rounded">
                <div class="card-header">
                    <h5 class="m-0">
                        Comparison with Existing Data
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 pe-5">
                            <div class="mb-3">
                                <div class="text-danger mb-1 f">
                                    * Select the full name to use or update new full name.
                                </div>
                                <div class="mb-2">
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input name" type="radio" name="name" id="nameInput1"
                                            onchange="checkInputRadio(this, 'name', 'text')" value="name 1">
                                        <label class="form-check-label" for="nameInput1">
                                            Full Name <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input name" type="radio" name="name" id="nameInput2"
                                            onchange="checkInputRadio(this, 'name', 'text')" value="name 2">
                                        <label class="form-check-label" for="nameInput2">
                                            Full Name <span class="text-info">(New Data)</span>
                                        </label>
                                    </div>
                                    <input type="text" name="name" id="nameNew"
                                        class="form-control form-control-sm ms-2" placeholder="Type new full name"
                                        oninput="checkInputText(this, 'name')">
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <div class="text-danger mb-1 f">
                                    * Select the option to use or update new email.
                                </div>
                                <div class="mb-2">
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input email" type="radio" name="email" id="emailInput1"
                                            onchange="checkInputRadio(this, 'email', 'text')" value="email 1">
                                        <label class="form-check-label" for="emailInput1">
                                            hafidz@gmail.com <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input email" type="radio" name="email" id="emailInput2"
                                            onchange="checkInputRadio(this, 'email', 'text')" value="email 2">
                                        <label class="form-check-label" for="emailInput2">
                                            hafidz@test.com <span class="text-info">(New Data)</span>
                                        </label>
                                    </div>
                                    <input type="email" name="email" id="emailNew"
                                        class="form-control form-control-sm ms-2" placeholder="Type new email"
                                        oninput="checkInputText(this, 'email')">
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <div class="text-danger mb-1 f">
                                    * Select the option to use or update new phone.
                                </div>
                                <div class="mb-2">
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input phone" type="radio" name="phone" id="phoneInput1"
                                            onchange="checkInputRadio(this, 'phone', 'text')" value="phone 1">
                                        <label class="form-check-label" for="phoneInput1">
                                            +628121240124 <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input phone" type="radio" name="phone" id="phoneInput2"
                                            onchange="checkInputRadio(this, 'phone', 'text')" value="phone 2">
                                        <label class="form-check-label" for="phoneInput2">
                                            +62839535343 <span class="text-info">(New Data)</span>
                                        </label>
                                    </div>
                                    <input type="tel" name="phone" id="phoneNew"
                                        class="form-control form-control-sm ms-2" placeholder="Type new phone number"
                                        oninput="checkInputText(this, 'phone')">
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <div class="text-danger mb-1">
                                    * Select the option to use or update new graduation year.
                                </div>
                                <div class="mb-2">
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input graduation" type="radio" name="graduation"
                                            id="graduationInput1" onchange="checkInputRadio(this, 'graduation', 'text')"
                                            value="graduation 1">
                                        <label class="form-check-label" for="graduationInput1">
                                            2024 <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input graduation" type="radio" name="graduation"
                                            id="graduationInput2" onchange="checkInputRadio(this, 'graduation', 'text')"
                                            value="graduation 2">
                                        <label class="form-check-label" for="graduationInput2">
                                            2023 <span class="text-info">(New Data)</span>
                                        </label>
                                    </div>
                                    <input type="text" name="graduation" id="graduationNew"
                                        class="form-control form-control-sm ms-2" placeholder="Type new graduation year"
                                        oninput="checkInputText(this, 'graduation')">
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <div class="text-danger mb-1 f">
                                    * Select the option to use or update new school.
                                </div>
                                <div class="mb-2">
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input school" type="radio" name="school"
                                            id="schoolInput1" onchange="checkInputRadio(this, 'school', 'text')"
                                            value="school 1">
                                        <label class="form-check-label" for="schoolInput1">
                                            School 1 <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input school" type="radio" name="school"
                                            id="schoolInput2" onchange="checkInputRadio(this, 'school', 'text')"
                                            value="school 2">
                                        <label class="form-check-label" for="schoolInput2">
                                            School 2 <span class="text-info">(New Data)</span>
                                        </label>
                                    </div>
                                    <div class="row g-1">
                                        <div class="col-10">
                                            <select class="select w-100 school" name="school" id="schoolNew"
                                                onchange="checkInputText(this, 'school', 'select')">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                        <div class="col-1">
                                            <button class="btn btn-sm btn-outline-dark w-100" onclick="syncSchool()">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </div>
                                        <div class="col-1">
                                            <button class="btn btn-sm btn-outline-dark w-100" type="button"
                                                onclick="addNewData('school')">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <div class="text-danger mb-1 f">
                                    * Select the option to use or update new parent.
                                </div>
                                <div class="mb-2">
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input parent" type="radio" name="parent"
                                            id="parentInput1" onchange="checkInputRadio(this, 'parent', 'select')"
                                            value="parent 1">
                                        <label class="form-check-label" for="parentInput1">
                                            parent 1 <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input parent" type="radio" name="parent"
                                            id="parentInput2" onchange="checkInputRadio(this, 'parent', 'select')"
                                            value="parent 2">
                                        <label class="form-check-label" for="parentInput2">
                                            parent 2 <span class="text-info">(New Data)</span>
                                        </label>
                                    </div>
                                    <div class="row g-1">
                                        <div class="col-10">
                                            <select class="select w-100 parent" name="parent" id="parentNew"
                                                onchange="checkInputText(this, 'parent', 'select')">
                                                <option value=""></option>
                                                <option value="Add">Add Parent</option>
                                            </select>
                                        </div>
                                        <div class="col-1">
                                            <button class="btn btn-sm btn-outline-dark w-100" type="button"
                                                onclick="syncParent()">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </div>
                                        <div class="col-1">
                                            <button class="btn btn-sm btn-outline-dark w-100" type="button"
                                                onclick="addNewData('parent')">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card rounded position-sticky" style="top:15%;">
                <form action="">
                    @csrf
                    <div class="card-header">
                        <h5>Summarize</h5>
                    </div>
                    <div class="card-body">
                        Preview first before convert this data
                        <hr class="my-1">
                        <input type="hidden" name="id" id="existing_id">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%">Full Name</td>
                                <td width="1%">:</td>
                                <td>
                                    <div id="namePreview"></div>
                                    <input type="hidden" name="nameFinal" id="nameInputPreview">
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>:</td>
                                <td>
                                    <div id="emailPreview"></div>
                                    <input type="hidden" name="emailFinal" id="emailInputPreview">
                                </td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td>:</td>
                                <td>
                                    <div id="phonePreview"></div>
                                    <input type="hidden" name="phoneFinal" id="phoneInputPreview">
                                </td>
                            </tr>
                            <tr>
                                <td>Graduation Year</td>
                                <td>:</td>
                                <td>
                                    <div id="graduationPreview"></div>
                                    <input type="hidden" name="graduationFinal" id="graduationInputPreview">
                                </td>
                            </tr>
                            <tr>
                                <td>School Name</td>
                                <td>:</td>
                                <td>
                                    <div id="schoolPreview"></div>
                                    <input type="hidden" name="schoolFinal" id="schoolInputPreview">
                                </td>
                            </tr>
                            <tr>
                                <td>Parent Name</td>
                                <td>:</td>
                                <td>
                                    <div id="parentPreview"></div>
                                    <input type="hidden" name="parentFinal" id="parentInputPreview">
                                </td>
                            </tr>
                        </table>
                        <hr>
                        <div class="text-center">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Convert
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function checkInputRadio(item, init, type) {
            if (type == 'text') {
                $('#' + init + 'New').val('')
                $('#' + init + 'InputPreview').val($(item).val())
                $('#' + init + 'Preview').html($(item).val())
            } else if (type == 'select') {
                $('#' + init + 'New').val('').trigger('change')
                $('#' + init + 'Preview').html($(item).val())
                $('#' + init + 'InputPreview').val($(item).val())
            }
        }

        function checkInputText(item, init, type = null) {
            if ($(item).val() != "") {
                $('.' + init).prop('checked', false)
                $('#' + init + 'Preview').html($(item).val())

                if (type == 'select') {
                    $('#' + init + 'InputPreview').val($(item).find(":selected").data('id'))
                } else {
                    $('#' + init + 'InputPreview').val($(item).val())
                }
            }



        }

        function addNewData(type) {
            if (type == "school") {
                window.open("{{ url('instance/school/create') }}", "_blank");
            } else {
                window.open("{{ url('client/parent/create') }}", "_blank");
            }
        }

        function syncSchool() {
            showLoading();
            axios.get("{{ url('api/instance/school') }}")
                .then(function(response) {
                    const data = response.data.data
                    $('#schoolNew').html('')
                    $('#schoolNew').append('<option value=""></option>')
                    data.forEach(element => {
                        $('#schoolNew').append(
                            '<option data-id="' + element.sch_id + '" value="' + element.sch_name + '">' +
                            element.sch_name + '</option>'
                        )
                    });
                    swal.close()
                })
                .catch(function(error) {
                    swal.close()
                    console.log(error);
                })
        }

        function syncParent() {
            showLoading()
            axios.get("{{ url('api/client/parent') }}")
                .then(function(response) {
                    const data = response.data.data
                    $('#parentNew').html('')
                    $('#parentNew').append('<option value=""></option>')
                    data.forEach(element => {
                        const last_name = element.last_name == null ? '' : ' ' + element.last_name
                        const fullname = element.first_name + last_name
                        $('#parentNew').append(
                            '<option data-id="' + element.id + '" value="' + fullname + '">' + fullname +
                            '</option>'
                        )
                    });
                    swal.close()
                })
                .catch(function(error) {
                    swal.close()
                    console.log(error);
                })
        }

        syncSchool()
        syncParent()
    </script>
@endpush
