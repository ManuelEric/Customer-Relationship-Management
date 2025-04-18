@extends('layout.main')

@section('title', 'Convert New Student')

@push('styles')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card rounded">
                <div class="card-header">
                    <h5 class="m-0">
                        Confirming Data
                    </h5>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="mb-1">
                                Full Name <sup class="text-danger">*</sup>
                            </div>
                            <div class="mb-2">
                                <input type="text" name="name" id="nameNew" value="{{ $rawClient->fullname }}"
                                    class="form-control form-control-sm" placeholder="Type new full name"
                                    oninput="checkInputText(this, 'name')">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="mb-1">
                                Email <sup class="text-danger">*</sup>
                            </div>
                            <div class="mb-2">
                                <input type="email" name="email" id="emailNew" value="{{ $rawClient->mail }}"
                                    class="form-control form-control-sm" placeholder="Type new email"
                                    oninput="checkInputText(this, 'email')">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="mb-1">
                                Phone Number <sup class="text-danger">*</sup>
                            </div>
                            <div class="mb-2">
                                <input type="tel" name="phone" id="phoneNew" value="{{ $rawClient->phone }}"
                                    class="form-control form-control-sm" placeholder="Type new phone number"
                                    oninput="checkInputText(this, 'phone')">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="mb-1">
                                Graduation Year <sup class="text-danger">*</sup>
                            </div>
                            <div class="mb-2">
                                <input type="text" name="graduation" id="graduationNew"
                                    value="{{ $rawClient->graduation_year_now }}" class="form-control form-control-sm"
                                    placeholder="Type new graduation year" oninput="checkInputText(this, 'graduation')">
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="mb-1">
                                School Name
                                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="If the school data is not verified please use verified school list"></i>
                                <sup class="text-danger">*</sup>
                            </div>
                            <div class="mb-2">
                                <div class="row g-2">
                                    @if ($rawClient->sch_id != null)
                                        <div class="col-5 d-flex gap-2">
                                            <div class="w-100">
                                                <input type="text" name="" id="schoolNew"
                                                    data-id="{{ $rawClient->is_verifiedschool == 'N' ? $rawClient->sch_id : '' }}"
                                                    class="form-control form-control-sm"
                                                    value="{{ $rawClient->is_verifiedschool == 'N' ? $rawClient->school_name : '' }}"
                                                    oninput="checkInputText(this, 'school')">
                                                <small class="text-danger">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                    Not Verified School
                                                </small>
                                            </div>
                                            <div class="mt-2">
                                                OR
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <select class="select w-100 school" name="school" id="schoolExist"
                                                onchange="checkInputText(this, 'school', 'select')">
                                                <option value=""></option>
                                                @foreach ($schools as $school)
                                                    <option data-id="{{ $school->sch_id }}" value="{{ $school->sch_name }}" {{ $rawClient->sch_id == $school->sch_id ? 'selected' : '' }}>{{ $school->sch_name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-success">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Verified School
                                            </small>
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
                                    @else
                                        <div class="col-10">
                                            <select class="select w-100 school" name="school" id="schoolExist"
                                                onchange="checkInputText(this, 'school', 'select')">
                                                <option value=""></option>
                                                @foreach ($schools as $school)
                                                    <option data-id="{{ $school->sch_id }}" value="{{ $school->sch_name }}">{{ $school->sch_name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-success">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Verified School
                                            </small>
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
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-1">
                                    Parents Information
                                </div>
                                <div class="row">
                                    @if ($rawClient->second_client_name != null)
                                        <div class="col-md-4">
                                            <label for="">Parent's Name</label>
                                            <input type="text" name="" id="parentName"
                                                class="form-control form-control-sm" value="{{ $rawClient->second_client_name }}"
                                                oninput="checkInputText(this, 'parent')">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="">Parent's Email</label>
                                            <input type="text" name="" id="parentEmail"
                                                class="form-control form-control-sm" value="{{ $rawClient->second_client_mail }}"
                                                oninput="checkInputText(this, 'parentEmail')">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="">Parent's Phone</label>
                                            <input type="text" name="" id="parentPhone"
                                                class="form-control form-control-sm"
                                                value="{{ $rawClient->second_client_phone }}"
                                                oninput="checkInputText(this, 'parentPhone')">
                                        </div>
                                    @endif
                                    <div class="mb-2">
                                        <div class="mb-1 mt-2">
                                            Exist Parent's 
                                        </div>
                                        <div class="row g-1">
                                            <div class="col-10">
                                                <select class="select w-100 parent" name="parent" id="parentNew"
                                                    onchange="checkInputText(this, 'parent', 'select')">
                                                    <option value=""></option>
                                                    @foreach ($parents as $parent)
                                                        <option data-id="{{ $parent->id }}" data-name="{{ $parent->full_name }}" data-email="{{ $parent->mail }}" data-phone="{{ $parent->phone }}" 
                                                            value="{{ $parent->full_name }}">{{ $parent->full_name }}</option>
                                                    @endforeach
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card rounded position-sticky" style="top:15%;">
                <form action="{{ route('client.convert.student', ['rawclient_id' => $rawClient->id, 'type' => 'new']) }}"
                    method="post">
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
                                    <div id="namePreview">{{ $rawClient->fullname }}</div>
                                    <input type="hidden" name="nameFinal" id="nameInputPreview"
                                        value="{{ $rawClient->fullname }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>:</td>
                                <td>
                                    <div id="emailPreview">{{ $rawClient->mail }}</div>
                                    <input type="hidden" name="emailFinal" id="emailInputPreview"
                                        value="{{ $rawClient->mail }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td>:</td>
                                <td>
                                    <div id="phonePreview">{{ $rawClient->phone }}</div>
                                    <input type="hidden" name="phoneFinal" id="phoneInputPreview"
                                        value="{{ $rawClient->phone }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Graduation Year</td>
                                <td>:</td>
                                <td>
                                    <div id="graduationPreview">{{ $rawClient->graduation_year }}</div>
                                    <input type="hidden" name="graduationFinal" id="graduationInputPreview"
                                        value="{{ $rawClient->graduation_year }}">
                                </td>
                            </tr>
                            <tr>
                                <td>School Name</td>
                                <td>:</td>
                                <td>
                                    <div id="schoolPreview">{{ $rawClient->school_name }}</div>
                                    <input type="hidden" name="schoolFinal" id="schoolInputPreview"
                                        value="{{ $rawClient->sch_id }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Parent Name</td>
                                <td>:</td>
                                <td>
                                    <div id="parentPreview">{{ $rawClient->second_client_name }}</div>
                                    <input type="hidden" name="parentType" id="parentTypeInput"
                                        value="{{ $rawClient->is_verifiedsecond_client != null && $rawClient->is_verifiedsecond_client == 'Y' ? 'exist' : 'new' }}">
                                    <input type="hidden" name="parentFinal" id="parentInputPreview"
                                        value="{{ $rawClient->second_client_id }}">
                                    <input type="hidden" name="parentName" id="parentNameInputPreview"
                                        value="{{ $rawClient->second_client_name }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Parent Email</td>
                                <td>:</td>
                                <td>
                                    <div id="parentEmailPreview">{{ $rawClient->second_client_mail }}</div>
                                    <input type="hidden" name="parentMail" value="{{ $rawClient->second_client_mail }}"
                                        id="parentEmailInputPreview">
                                </td>
                            </tr>
                            <tr>
                                <td>Parent Phone</td>
                                <td>:</td>
                                <td>
                                    <div id="parentPhonePreview">{{ $rawClient->second_client_phone }}</div>
                                    <input type="hidden" name="parentPhone" value="{{ $rawClient->second_client_phone }}"
                                        id="parentPhoneInputPreview">
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
        $('#schoolExist').on('select2:unselect', function(e) {
            $('#schoolNew').prop('disabled', false).val('{{ $rawClient->school_name }}')
            $('#schoolPreview').html('{{ $rawClient->school_name }}')
            $('#schoolInputPreview').val('{{ $rawClient->sch_id }}')
        });

        $('#parentNew').on('select2:unselect', function(e) {
            $('#parentName').prop('disabled', false).val('{{ $rawClient->second_client_name }}')
            $('#parentEmail').prop('disabled', false).val('{{ $rawClient->second_client_mail }}')
            $('#parentPhone').prop('disabled', false).val('{{ $rawClient->second_client_phone }}')


            $('#parentTypeInput').val(
                '{{ $rawClient->is_verifiedsecond_client != null && $rawClient->is_verifiedsecond_client == 'Y' ? 'exist' : 'new' }}'
            )
            $('#parentInputPreview').val('')
            $('#parentPreview').html('{{ $rawClient->second_client_name }}')
            $('#parentEmailPreview').html('{{ $rawClient->second_client_mail }}')
            $('#parentPhonePreview').html('{{ $rawClient->second_client_phone }}')
            $('#parentInputPreview').val('{{ $rawClient->second_client_id }}')
            $('#parentNameInputPreview').val('{{ $rawClient->second_client_name }}')
            $('#parentEmailInputPreview').val('{{ $rawClient->second_client_mail }}')
            $('#parentPhoneInputPreview').val('{{ $rawClient->second_client_phone }}')
        });

        function checkInputText(item, init, type = null) {
            if ($(item).val() != "") {
                $('.' + init).prop('checked', false)
                $('#' + init + 'Preview').html($(item).val())

                if (type == 'select') {
                    if (init == 'school') {
                        $('#' + init + 'New').prop('disabled', true).val('')
                    } else if (init == 'parent') {
                        $('#' + init + 'Name').prop('disabled', true).val('')
                        $('#' + init + 'Email').prop('disabled', true).val('')
                        $('#' + init + 'Phone').prop('disabled', true).val('')

                        $('#' + init + 'Preview').html($(item).find(":selected").data('name'))
                        $('#' + init + 'NameInputPreview').val($(item).find(":selected").data('name'))
                        $('#' + init + 'EmailPreview').html($(item).find(":selected").data('email'))
                        $('#' + init + 'EmailInputPreview').val($(item).find(":selected").data('email'))
                        $('#' + init + 'PhonePreview').html($(item).find(":selected").data('phone'))
                        $('#' + init + 'PhoneInputPreview').val($(item).find(":selected").data('phone'))

                        $('#parentTypeInput').val('exist_select')
                    }
                    $('#' + init + 'InputPreview').val($(item).find(":selected").data('id'))
                } else {
                    if (init == 'parent') {
                        $('#' + init + 'NameInputPreview').val($(item).val())
                    } else if (init == 'school') {
                        $('#' + init + 'InputPreview').val($(item).data('id'))
                    } else {
                        $('#' + init + 'InputPreview').val($(item).val())
                    }
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
                    $('#schoolExist').html('')
                    $('#schoolExist').append('<option value=""></option>')
                    data.forEach(element => {
                        $('#schoolExist').append(
                            '<option data-id="' + element.sch_id + '" value="' + element.sch_name + '">' +
                            element.sch_name + '</option>'
                        )
                    });

                    $('#schoolExist').val("{{ $rawClient->is_verifiedschool == 'Y' ? $rawClient->school_name : '' }}")
                        .trigger('change')
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
                            '<option data-id="' + element.id + '" ' +
                            'data-name="' + fullname + '"' +
                            'data-email="' + element.mail + '"' +
                            'data-phone="' + element.phone + '"' +
                            'value="' + fullname + '">' + fullname +
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

        // syncParent()
        // syncSchool()
    </script>
@endpush
