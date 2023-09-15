@extends('app')
@section('title', 'Confirmation')
@push('styles')
    <style>
        input {
            border: 0 !important;
            border-bottom: 1px solid #16236a !important;
            border-radius: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 3px 0 !important;
        }

        .iti {
            display: block !important;
        }

        #phoneUser1, #phoneUser2 {
            margin-left: 20%;
            width: 80%;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
@endpush
@section('body')
    @php
        $isTeacher = $client->register_as == "teacher/counsellor" ? true : false;
        $isParent = $client->register_as == "parent" ? true : false;
        $isStudent = $client->register_as == "student" ? true : false;
        $secondary_client_role = $client->register_as == "parent" ? "Child's" : "Parent's";
    @endphp
    <section>
        <div class="container-fluid my-3">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-8">
                    <form action="{{ route("link-event-attend", ['clientevent' => request()->route('clientevent')]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div @class([
                                    'col-12' => $isTeacher,
                                    'col-4' => $isParent || $isStudent,
                                    'mb-3'
                                ])>
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" readonly class="form-control" value="{{ $client->full_name }}">
                            </div>
                            <div @class([
                                    'col-12' => $isTeacher,
                                    'col-4' => $isParent || $isStudent,
                                    'mb-3'
                                ])>
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="text" readonly class="form-control" value="{{ $client->mail }}">
                            </div>
                            <div @class([
                                    'col-12' => $isTeacher,
                                    'col-4' => $isParent || $isStudent,
                                    'mb-3'
                                ])>
                                <label>Phone Number <span class="text-danger">*</span></label>
                                <input type="text" id="phoneUser1" readonly class="form-control" value="{{ $client->phone }}">
                                <input type="hidden" id="phone1">
                            </div>
                            <div class="col-12 mb-3">
                                <label>Number of Attend <span class="text-danger">*</span></label>
                                <input type="number" name="how_many_people_attended" class="form-control">
                            </div>
                            @switch ($client->register_as)
                                @case("parent")
                                @case("student")
                                    <div class="col-4 mb-3">
                                        <label>Your {{ $secondary_client_role  }} Name <span class="text-danger">*</span></label>
                                        <input type="text" readonly class="form-control" value="{{ $secondary_client['personal_info']->full_name }}">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>Your {{ $secondary_client_role  }} Email <span class="text-danger">*</span></label>
                                        <input type="text" name="secondary_mail" class="form-control" value="{{ $secondary_client['personal_info']->mail }}">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>{{ $secondary_client_role  }} Number <span class="text-danger">*</span></label>
                                        <input type="text" id="phoneUser2" class="form-control" value="{{ $secondary_client['personal_info']->phone }}">
                                        <input type="hidden" name="secondary_phone" id="phone2" value="{{ $secondary_client['personal_info']->phone }}">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>School Name <span class="text-danger">*</span></label>
                                        <input type="text" readonly class="form-control" value="{{ $secondary_client['school'] }}">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>Graduation Year <span class="text-danger">*</span></label>
                                        <input type="text" readonly class="form-control" value="{{ $secondary_client['graduation_year'] }}">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>Destination Country <span class="text-danger">*</span></label>
                                        <input type="text" readonly class="form-control" value="{{ $secondary_client['abr_country'] }}">
                                    </div>
                                    @break

                                @case("teacher/counsellor")
                                    <div class="col-12 mb-3">
                                        <label>School Name <span class="text-danger">*</span></label>
                                        <input type="text" readonly class="form-control" value="{{ $client->school->sch_name }}">
                                    </div>
                                    @break
                            @endswitch
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-sm btn-primary"><i
                                            class="bi bi-send me-2"></i> Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>

    <script>
        var user1 = document.querySelector("#phoneUser1");
        var user2 = document.querySelector("#phoneUser2");

        const phoneInput1 = window.intlTelInput(user1, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            initialCountry: 'id',
            onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
        });

        const phoneInput2 = window.intlTelInput(user2, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            initialCountry: 'id',
            onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
        });

        $("#phoneUser1").on('keyup', function(e) {
            var number1 = phoneInput1.getNumber();
            $("#phone1").val(number1);
        });

        $("#phoneUser2").on('keyup', function(e) {
            var number2 = phoneInput2.getNumber();
            $("#phone2").val(number2);
        });
    </script>
    <script>
        function test() {
            parent.$('#clientDetail').modal('hide')
        }
    </script>
@endpush
