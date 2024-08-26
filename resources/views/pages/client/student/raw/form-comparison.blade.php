@extends('layout.main')

@section('title', 'Comparison Students Data')

@push('styles')
@endpush

@section('content')
    @php
        $existParent = null;
        if ($client->parents()->count() > 0) {
            $existParent = $client->parents()->first();
        }
    @endphp
    <div class="row">
        <div class="col-md-7">
            <div class="card rounded">
                <div class="card-header">
                    <h5 class="m-0">
                        Comparison with Existing Data
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
                        <div class="col-md-12 pe-5">
                            <div class="mb-3">
                                <div class="text-danger mb-1 f">
                                    * Select the option to use or update new full name.
                                </div>
                                <div class="mb-2">
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input name" type="radio" name="name" id="nameInput1"
                                            checked onchange="checkInputRadio(this, 'name', 'text')"
                                            value="{{ $client->full_name }}">
                                        <label class="form-check-label" for="nameInput1">
                                            {{ $client->full_name }} <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input name" type="radio" name="name" id="nameInput2"
                                            onchange="checkInputRadio(this, 'name', 'text')"
                                            value="{{ $rawClient->fullname }}">
                                        <label class="form-check-label" for="nameInput2">
                                            {{ $rawClient->fullname ? $rawClient->fullname : '-' }} <span
                                                class="text-info">(New
                                                Data)</span>
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
                                            onchange="checkInputRadio(this, 'email', 'text')" value="{{ $client->mail }}"
                                            checked>
                                        <label class="form-check-label" for="emailInput1">
                                            {{ $client->mail ? $client->mail : '-' }} <span class="text-warning">(Existing
                                                Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input email" type="radio" name="email" id="emailInput2"
                                            onchange="checkInputRadio(this, 'email', 'text')"
                                            value="{{ $rawClient->mail }}">
                                        <label class="form-check-label" for="emailInput2">
                                            {{ $rawClient->mail ? $rawClient->mail : '-' }} <span class="text-info">(New
                                                Data)</span>
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
                                            onchange="checkInputRadio(this, 'phone', 'text')" value="{{ $client->phone }}"
                                            checked>
                                        <label class="form-check-label" for="phoneInput1">
                                            {{ $client->phone ? $client->phone : '-' }} <span
                                                class="text-warning">(Existing
                                                Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input phone" type="radio" name="phone" id="phoneInput2"
                                            onchange="checkInputRadio(this, 'phone', 'text')"
                                            value="{{ $rawClient->phone }}">
                                        <label class="form-check-label" for="phoneInput2">
                                            {{ $rawClient->phone ? $rawClient->phone : '-' }} <span class="text-info">(New
                                                Data)</span>
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
                                            value="{{ $client->graduation_year_real }}" checked>
                                        <label class="form-check-label" for="graduationInput1">
                                            {{ $client->graduation_year_real ? $client->graduation_year_real : '-' }} <span
                                                class="text-warning">(Existing
                                                Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input graduation" type="radio" name="graduation"
                                            id="graduationInput2" onchange="checkInputRadio(this, 'graduation', 'text')"
                                            value="{{ $rawClient->graduation_year_real }}">
                                        <label class="form-check-label" for="graduationInput2">
                                            {{ $rawClient->graduation_year_real ? $rawClient->graduation_year_real : '-' }} <span
                                                class="text-info">(New Data)</span>
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
                                            id="schoolInput1" onchange="checkInputRadio(this, 'school', 'select')"
                                            data-name="{{ $client->school_name }}" value="{{ $client->sch_id }}"
                                            checked>
                                        <label class="form-check-label" for="schoolInput1">
                                            {{ $client->school_name ? $client->school_name : '-' }} <span
                                                class="text-warning">(Existing Data)</span>
                                        </label>
                                        @if ($client->school_name != null)
                                            @if ($client->school->is_verified == 'Y')
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Verified School
                                                </small>
                                            @else
                                                <small class="text-danger">
                                                    <i class="bi bi-info-circle-fill"></i>
                                                    Not Verified School
                                                </small>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input school" type="radio" name="school"
                                            data-name="{{ $rawClient->school_name }}" id="schoolInput2"
                                            onchange="checkInputRadio(this, 'school', 'select')"
                                            value="{{ $rawClient->sch_id }}">
                                        <label class="form-check-label" for="schoolInput2">
                                            {{ $rawClient->school_name ? $rawClient->school_name : '-' }} <span
                                                class="text-info">(New
                                                Data)</span>
                                        </label>
                                        @if ($rawClient->sch_id != null)
                                            @if ($rawClient->is_verifiedschool == 'Y')
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Verified School
                                                </small>
                                            @else
                                                <small class="text-danger">
                                                    <i class="bi bi-info-circle-fill"></i>
                                                    Not Verified School
                                                </small>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="row g-1">
                                        <div class="col-10">
                                            <select class="select w-100 school" name="school" id="schoolNew"
                                                onchange="checkInputText(this, 'school', 'select')">
                                                <option value=""></option>
                                                @foreach ($schools as $school)
                                                    <option data-id="{{ $school->sch_id }}" value="{{ $school->sch_name }}">{{ $school->sch_name }}</option>
                                                @endforeach
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
                                            id="parentInput1"
                                            data-name="{{ $existParent != null ? $existParent->first_name . ' ' . $existParent->last_name : null }}"
                                            data-email="{{ $existParent != null ? $existParent->mail : null }}"
                                            data-phone="{{ $existParent != null ? $existParent->phone : null }}"
                                            onchange="checkInputRadio(this, 'parent', 'select', 'exist')"
                                            value="{{ $existParent != null ? $existParent->id : null }}" checked>
                                        <label class="form-check-label" for="parentInput1">
                                            {{ $existParent != null ? $existParent->first_name . ' ' . $existParent->last_name : null }}
                                            <span class="text-warning">(Existing Data)</span>
                                        </label>
                                        @if ($existParent != null)
                                            @if ($existParent->is_verified == 'Y')
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Verified
                                                </small>
                                            @else
                                                <small class="text-danger">
                                                    <i class="bi bi-info-circle-fill"></i>
                                                    Not Verified
                                                </small>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input parent" type="radio"
                                            data-name="{{ $rawClient->second_client_name }}"
                                            data-email="{{ $rawClient->second_client_mail }}"
                                            data-phone="{{ $rawClient->second_client_phone }}" name="parent" id="parentInput2"
                                            onchange="checkInputRadio(this, 'parent', 'select', 'new')"
                                            value="{{ $rawClient->second_client_id }}">
                                        <label class="form-check-label" for="parentInput2">
                                            {{ $rawClient->second_client_name ? $rawClient->second_client_name : '-' }} <span
                                                class="text-info">(New Data)</span>
                                        </label>
                                        @if ($rawClient->is_verifiedparent != null)
                                            @if ($rawClient->is_verifiedparent == 'Y')
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Verified
                                                </small>
                                            @else
                                                <small class="text-danger">
                                                    <i class="bi bi-info-circle-fill"></i>
                                                    Not Verified
                                                </small>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="row my-2" id="parentDetail">
                                        <div class="col-md-4">
                                            <label for="parents_email">Parent's Name</label>
                                            <input type="email" name="" id="parent_name"
                                                class="form-control form-control-sm parentInput"
                                                value="{{ $existParent != null ? $existParent->first_name . ' ' . $existParent->last_name : null }}"
                                                oninput="checkInputText(this, 'parentName')">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="parents_email">Parent's Email</label>
                                            <input type="email" name="" id="parent_email"
                                                class="form-control form-control-sm parentInput"
                                                value="{{ $existParent != null ? $existParent->mail : null }}"
                                                oninput="checkInputText(this, 'parentEmail')">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="parents_email">Parent's Phone Number</label>
                                            <input type="tel" name="" id="parent_phone"
                                                class="form-control form-control-sm parentInput" value="{{ $existParent != null ? $existParent->phone : null }}"
                                                oninput="checkInputText(this, 'parentPhone')">
                                        </div>
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
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            @if($client->clientProgram->where('status', 1)->count() > 0)
                <div class="card rounded mb-3">
                    <div class="card-header">
                        <strong>
                            Success Program from {{ $client->full_name }}
                        </strong>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 300px">
                        <ul class="list-group" style="font-size: 11px;">
                            @foreach ($client->clientProgram->where('status', 1) as $clientProg)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-tags-fill me-1"></i>
                                        {{ $clientProg->program->program_name }}
                                    </div>
                                <span class="badge bg-primary rounded-pill py-1 px-2" style="font-size: 11px">{{ isset($clientProg->internalPic) ? $clientProg->internalPic->full_name : '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <div class="card rounded">
                <form
                    action="{{ route('client.convert.student', ['client_id' => $client->id, 'type' => 'merge', 'rawclient_id' => $rawClient->id]) }}"
                    method="post">
                    @csrf
                    <div class="card-header">
                        <h5 class="m-0">Summarize</h5>
                    </div>
                    <div class="card-body">
                        Preview first before convert this data
                        <hr class="my-1">
                        <input type="hidden" name="id" id="existing_id" value="{{ $client->id }}">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%">Full Name</td>
                                <td width="1%">:</td>
                                <td>
                                    <div id="namePreview">{{ $client->full_name }}</div>
                                    <input type="hidden" name="nameFinal" id="nameInputPreview"
                                        value="{{ $client->full_name }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>:</td>
                                <td>
                                    <div id="emailPreview">{{ $client->mail }}</div>
                                    <input type="hidden" name="emailFinal" id="emailInputPreview"
                                        value="{{ $client->mail }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td>:</td>
                                <td>
                                    <div id="phonePreview">{{ $client->phone }}</div>
                                    <input type="hidden" name="phoneFinal" id="phoneInputPreview"
                                        value="{{ $client->phone }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Graduation Year</td>
                                <td>:</td>
                                <td>
                                    <div id="graduationPreview">{{ $client->graduation_year_real }}</div>
                                    <input type="hidden" name="graduationFinal" id="graduationInputPreview"
                                        value="{{ $client->graduation_year_real }}">
                                </td>
                            </tr>
                            <tr>
                                <td>School Name</td>
                                <td>:</td>
                                <td>
                                    <div id="schoolPreview">{{ $client->school_name }}</div>
                                    <input type="hidden" name="schoolFinal" id="schoolInputPreview"
                                        value="{{ $client->sch_id }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Parent Name</td>
                                <td>:</td>
                                <td>
                                    <div id="parentNamePreview">
                                        {{ $existParent != null ? $existParent->first_name . ' ' . $existParent->last_name : null }}
                                    </div>
                                    <input type="hidden" name="parentType" id="parentTypeInput" value="exist">
                                    <input type="hidden" name="parentName" id="parentNameInputPreview"
                                        value="{{ $existParent != null ? $existParent->first_name . ' ' . $existParent->last_name : null }}">
                                    <input type="hidden" name="parentFinal" id="parentInputPreview"
                                        value="{{ $existParent != null ? $existParent->id : null }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Parent Mail</td>
                                <td>:</td>
                                <td>
                                    <div id="parentEmailPreview">{{ $existParent != null ? $existParent->mail : null }}</div>
                                    <input type="hidden" name="parentMail" id="parentEmailInputPreview"
                                        value="{{ $existParent != null ? $existParent->mail : null }}">
                                </td>
                            </tr>
                            <tr>
                                <td>Parent Phone</td>
                                <td>:</td>
                                <td>
                                    <div id="parentPhonePreview">{{ $existParent != null ? $existParent->phone : null }}</div>
                                    <input type="hidden" name="parentPhone" id="parentPhoneInputPreview"
                                        value="{{ $existParent != null ? $existParent->phone : null }}">
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
        // Parameter itemType for type parent (exist or new)
        function checkInputRadio(item, init, type, itemType = null) {
            if (type == 'text') {
                $('#' + init + 'New').val('')

                // Sumarize 
                $('#' + init + 'InputPreview').val($(item).val())
                $('#' + init + 'Preview').html($(item).val())
            } else if (type == 'select') {
                $('#' + init + 'New').val('').trigger('change')

                if (init == 'parent') {
                    if (!$(item).data('name')) {
                        $('#' + init + 'Detail').prop('hidden', true)
                    } else {
                        $('#' + init + 'Detail').prop('hidden', false)
                    }
                    $('#' + init + 'NamePreview').html($(item).data('name'))

                    // for parents detail 
                    $('#' + init + '_name').val($(item).data('name'))
                    $('#' + init + '_email').val($(item).data('email'))
                    $('#' + init + '_phone').val($(item).data('phone'))

                    // for parent detail summarize 
                    $('#' + init + 'NameInputPreview').val($(item).data('name'))
                    $('#' + init + 'EmailInputPreview').val($(item).data('email'))
                    $('#' + init + 'PhoneInputPreview').val($(item).data('phone'))
                    $('#' + init + 'EmailPreview').html($(item).data('email'))
                    $('#' + init + 'PhonePreview').html($(item).data('phone'))
                } else {
                    if ($(item).data('name')) {
                        $('#' + init + 'Preview').html($(item).data('name'))
                    } else {
                        $('#' + init + 'Preview').html($(item).val())
                    }
                }

                // Sumarize 
                $('#' + init + 'InputPreview').val($(item).val())
            }

            if (itemType == 'new') {
                $('#parentTypeInput').val('new')
            } else {
                $('#parentTypeInput').val('exist')
            }
        }

        function checkInputText(item, init, type = null) {
            if ($(item).val() != "") {
                $('.' + init).prop('checked', false)
                $('#' + init + 'Preview').html($(item).val())

                if (type == 'select') {
                    if (init == 'parent') {
                        $('#' + init + 'Detail').prop('hidden', true)
                        $('#' + init + 'InputPreview').val($(item).find(":selected").data('id'))
                        $('#' + init + 'NamePreview').html($(item).val())
                        $('#' + init + 'EmailPreview').html($(item).find(":selected").data('email'))
                        $('#' + init + 'PhonePreview').html($(item).find(":selected").data('phone'))
                        $('#' + init + 'NameInputPreview').val($(item).val())
                        $('#' + init + 'EmailInputPreview').val($(item).find(":selected").data('email'))
                        $('#' + init + 'PhoneInputPreview').val($(item).find(":selected").data('phone'))
                        $('#parentTypeInput').val('exist_select')
                    } else {
                        $('#' + init + 'InputPreview').val($(item).find(":selected").data('id'))
                    }
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
                            '<option data-id="' + element.id + '" ' +
                            'data-name="' + element.fullname + '"' +
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
