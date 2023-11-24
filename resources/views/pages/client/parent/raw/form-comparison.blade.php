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
                                            checked onchange="checkInputRadio(this, 'name', 'text')" value="Existing Name">
                                        <label class="form-check-label" for="nameInput1">
                                            Existing Name <span class="text-warning">(Existing Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input name" type="radio" name="name" id="nameInput2"
                                            onchange="checkInputRadio(this, 'name', 'text')" value="New Name">
                                        <label class="form-check-label" for="nameInput2">
                                            New Name <span class="text-info">(New
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
                                            onchange="checkInputRadio(this, 'email', 'text')" value="Existing Email"
                                            checked>
                                        <label class="form-check-label" for="emailInput1">
                                            Existing Email <span class="text-warning">(Existing
                                                Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input email" type="radio" name="email" id="emailInput2"
                                            onchange="checkInputRadio(this, 'email', 'text')" value="New Email">
                                        <label class="form-check-label" for="emailInput2">
                                            New Email <span class="text-info">(New
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
                                            onchange="checkInputRadio(this, 'phone', 'text')" value="Existing Phone"
                                            checked>
                                        <label class="form-check-label" for="phoneInput1">
                                            Exisitng Phone <span class="text-warning">(Existing
                                                Data)</span>
                                        </label>
                                    </div>
                                    <div class="form-check ms-4 my-0">
                                        <input class="form-check-input phone" type="radio" name="phone" id="phoneInput2"
                                            onchange="checkInputRadio(this, 'phone', 'text')" value="New Phone">
                                        <label class="form-check-label" for="phoneInput2">
                                            New Phone <span class="text-info">(New
                                                Data)</span>
                                        </label>
                                    </div>
                                    <input type="tel" name="phone" id="phoneNew"
                                        class="form-control form-control-sm ms-2" placeholder="Type new phone number"
                                        oninput="checkInputText(this, 'phone')">
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
                <form action="#" method="post">
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
                                    <div id="namePreview">Existing Name</div>
                                    <input type="hidden" name="nameFinal" id="nameInputPreview" value="Existing Name">
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>:</td>
                                <td>
                                    <div id="emailPreview">Existing Email</div>
                                    <input type="hidden" name="emailFinal" id="emailInputPreview"
                                        value="Existing Email">
                                </td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td>:</td>
                                <td>
                                    <div id="phonePreview">Existing Phone</div>
                                    <input type="hidden" name="phoneFinal" id="phoneInputPreview"
                                        value="Existing Phone">
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
            $('#' + init + 'New').val('')

            // Sumarize 
            $('#' + init + 'InputPreview').val($(item).val())
            $('#' + init + 'Preview').html($(item).val())
        }

        function checkInputText(item, init, type = null) {
            if ($(item).val() != "") {
                $('.' + init).prop('checked', false)
                $('#' + init + 'Preview').html($(item).val())
                $('#' + init + 'InputPreview').val($(item).val())
            }

        }
    </script>
@endpush
