@extends('layout.main')

@section('title', 'Teacher')

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
                                            checked onchange="checkInputRadio(this, 'name', 'text')" value="{{ $client->full_name }}">
                                        <label class="form-check-label" for="nameInput1">
                                            {{ $client->full_name }} <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input name" type="radio" name="name" id="nameInput2"
                                            onchange="checkInputRadio(this, 'name', 'text')" value="{{ $rawClient->fullname }}">
                                        <label class="form-check-label" for="nameInput2">
                                            {{ $rawClient->fullname ? $rawClient->fullname : '-' }} <span class="text-info">(New
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
                                            onchange="checkInputRadio(this, 'email', 'text')" value="{{ $rawClient->mail }}">
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
                                            {{ $client->phone ? $client->phone : '-' }} <span class="text-warning">(Existing
                                                Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input phone" type="radio" name="phone" id="phoneInput2"
                                            onchange="checkInputRadio(this, 'phone', 'text')" value="{{ $rawClient->phone }}">
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
                                <div class="text-danger mb-1 f">
                                    * Select the option to use or update new school.
                                </div>
                                <div class="mb-2">
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input school" type="radio" name="school"
                                            id="schoolInput1" onchange="checkInputRadio(this, 'school', 'select')"
                                            data-name="{{ $client->school_name }}" value="{{ $client->sch_id }}" checked>
                                        <label class="form-check-label" for="schoolInput1">
                                            {{ $client->school_name ? $client->school_name : '-' }} <span class="text-warning">(Existing Data)</span>
                                        </label>
                                        @if($client->school_name != null)
                                            @if($client->school->is_verified == 'Y')
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
                                            onchange="checkInputRadio(this, 'school', 'select')" value="{{ $rawClient->sch_id }}">
                                        <label class="form-check-label" for="schoolInput2">
                                            {{ $rawClient->school_name ? $rawClient->school_name : '-' }} <span class="text-info">(New
                                                Data)</span>
                                        </label>
                                        @if($rawClient->sch_id != null)
                                            @if($rawClient->is_verifiedschool == 'Y')
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card rounded position-sticky" style="top:15%;">
                <form action="{{ route('client.convert.teacher', ['client_id' => $client->id, 'type' => 'merge', 'rawclient_id' => $rawClient->id]) }}" method="post">
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
                                    <div id="namePreview">{{ $client->full_name }}</div>
                                    <input type="hidden" name="nameFinal" id="nameInputPreview" value="{{ $client->full_name }}">
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
                                <td>School Name</td>
                                <td>:</td>
                                <td>
                                    <div id="schoolPreview">{{ $client->school_name }}</div>
                                    <input type="hidden" name="schoolFinal" id="schoolInputPreview" value="{{ $client->sch_id }}">
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
            if (type == "text") {
                $('#' + init + 'New').val('')
            } else if (type == 'select') {
                $('#' + init + 'New').val('').trigger('change')

            }

            // Sumarize 
            $('#' + init + 'InputPreview').val($(item).val())
            if ($(item).data('name')) {
                $('#' + init + 'Preview').html($(item).data('name'))
            } else {
                $('#' + init + 'Preview').html($(item).val())
            }
        }

        function checkInputText(item, init, type = null) {
            if ($(item).val() != "") {
                $('.' + init).prop('checked', false)

                if (type == 'select') {
                    $('#' + init + 'InputPreview').val($(item).find(":selected").data('id'))
                } else {
                    $('#' + init + 'InputPreview').val($(item).val())
                }

                $('#' + init + 'Preview').html($(item).val())
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

        syncSchool()
    </script>
@endpush
