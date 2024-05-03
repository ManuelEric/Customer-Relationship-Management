@extends('app')
@section('title', 'Confirmation')
@push('styles')

    <link href="https://fastly.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>

        @font-face {
            font-family: 'nulshock';
            src: url('/img/makerspace/font/nulshock-bd.otf');
            font-display: swap;
        }

        .notes{
            font-family: 'nulshock' !important;
        }
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

        .ts-control {
            border: none;
            padding-left: 0;
            font-size: .8rem;
            color:rgb(55, 98, 227);
        }

        .ts-control .item {
            color: rgb(55, 98, 227) !important;
        }

        .form-control {
            font-size: .8rem !important;
            color:rgb(55, 98, 227)
        }
        
    </style>
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
@endpush
@section('body')
    @php
        $isTeacher = $client->register_as == "teacher/counsellor" ? true : false;
        $isParent = $client->register_as == "parent" ? true : false;
        $isStudent = $client->register_as == "student" ? true : false;
        $secondary_client_role = $client->register_as == "parent" ? "Child's" : "Parent's";
    @endphp
    <section>
        <div class="container-fluid my-3" style="height: 90vh">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-md-8">
                    @if ($client_event->status == 0)
                    <form action="{{ route("link-event-attend", ['clientevent' => $client_event->clientevent_id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="role" value="{{ $client->register_as }}">
                        <div class="row">
                            <div @class([
                                    'col-12' => $isTeacher,
                                    'col-4' => $isParent || $isStudent,
                                    'mb-3'
                                ])>
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullname[]" class="form-control" value="{{ $client->full_name }}">
                                @error('fullname.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div @class([
                                    'col-12' => $isTeacher,
                                    'col-4' => $isParent || $isStudent,
                                    'mb-3'
                                ])>
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="text" name="email[]" class="form-control" value="{{ $client->mail }}">
                                @error('email.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div @class([
                                    'col-12' => $isTeacher,
                                    'col-4' => $isParent || $isStudent,
                                    'mb-3'
                                ])>
                                <label>Phone Number <span class="text-danger">*</span></label>
                                <input type="text" id="phoneUser1" class="form-control" value="{{ $client->phone }}">
                                @error('fullnumber.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                                <input type="hidden" id="phone1" name="fullnumber[]" value="{{ $client->phone }}">
                                <input type="hidden" name="leadsource" value="{{ $leadsource }}">
                            </div>
                            @switch ($client->register_as)
                                @case("parent")
                                @case("student")
                                    <div class="col-4 mb-3">
                                        <label>Your {{ $secondary_client_role  }} Name <span class="text-danger">*</span></label>
                                        <input type="text" name="fullname[]" class="form-control" value="{{ $secondary_client['personal_info']->full_name ?? null }}">
                                        @error('fullname.1')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>Your {{ $secondary_client_role  }} Email <span class="text-danger">*</span></label>
                                        <input type="text" name="email[]" class="form-control" value="{{ $secondary_client['personal_info']->mail ?? null }}">
                                        @error('email.1')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>{{ $secondary_client_role  }} Number <span class="text-danger">*</span></label>
                                        <input type="text" id="phoneUser2" class="form-control" value="{{ $secondary_client['personal_info']->phone ?? null }}">
                                        <input type="hidden" name="fullnumber[]" id="phone2" value="{{ $secondary_client['personal_info']->phone ?? null}}">
                                        @error('fullnumber.1')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>School Name <span class="text-danger">*</span></label>
                                        {{-- <input type="text" readonly class="form-control" value="{{ $secondary_client['school'] }}"> --}}
                                        <select name="school" id="schoolList"
                                            class="w-full form-control border-top-0 border-end-0 border-start-0 border border-1 border-dark pt-1 ps-0 rounded-0"
                                            placeholder="Type School Name">
                                            <option data-placeholder="true"></option>
                                            @foreach ($schools as $school)
                                                <option value="{{ $school->sch_id }}"
                                                    {{ old('school') == $school->sch_id ? 'selected' : null }}
                                                    @selected($school->sch_id == $secondary_client['sch_id'] ?? null)>
                                                    {{ $school->sch_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('school')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>Graduation Year <span class="text-danger">*</span></label>
                                        {{-- <input type="text" readonly class="form-control" value="{{ $secondary_client['graduation_year'] }}"> --}}
                                        <select name="graduation_year" id="graduation_year"
                                            class="w-full form-control border-top-0 border-end-0 border-start-0 border border-1 border-dark pt-1 ps-0 rounded-0"
                                            placeholder="">
                                            <option value=""></option>
                                            @for ($i = date('Y'); $i < date('Y') + 6; $i++)
                                                <option value="{{ $i }}" @selected($i == $secondary_client['graduation_year'] ?? null)>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('graduation_year')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label>Destination Country <span class="text-danger">*</span></label>
                                        {{-- <input type="text" readonly class="form-control" value="{{ $secondary_client['abr_country'] }}"> --}}
                                        <select name="destination_country[]" multiple="multiple" id="destination_country"
                                            class="w-full form-control border-top-0 border-end-0 border-start-0 border border-1 border-dark pt-1 ps-0 rounded-0"
                                            placeholder="">
                                            <option value=""></option>
                                            @foreach ($tags as $tag)
                                                <option value="{{ $tag->id }}" @selected(isset($secondary_client['abr_country']) && in_array($tag->id, $secondary_client['abr_country']))>{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('destination_country.*')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    @break

                                @case("teacher/counsellor")
                                    <div class="col-12 mb-3">
                                        <label>School Name <span class="text-danger">*</span></label>
                                        <select name="school" id="schoolList"
                                            class="w-full form-control border-top-0 border-end-0 border-start-0 border border-1 border-dark pt-1 ps-0 rounded-0"
                                            placeholder="Type School Name">
                                            <option data-placeholder="true"></option>
                                            @foreach ($schools as $school)
                                                <option value="{{ $school->sch_id }}"
                                                    {{ old('school') == $school->sch_id ? 'selected' : null }}
                                                    @selected($school->sch_id == $secondary_client['sch_id'])>
                                                    {{ $school->sch_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('school')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    @break
                            @endswitch
                            <div class="col-12">
                                <hr>
                                <div class="row justify-content-end align-items-end">
                                    <div class="col">
                                        @if(isset($client_event->notes))
                                            @if($client_event->notes == 'VIP' || $client_event->notes == 'VVIP')
                                                <div class="badge rounded-pill px-3" style="background:#FF7701">
                                                    <h2 class="notes p-0 m-0" style="color:#0C0F38 ">{{ isset($client_event->notes) ? $client_event->notes : 'Regular' }}</h2>
                                                </div>
                                            @endif
                                        @else
                                            @if(isset($client_event->referral_code))
                                                <div class="badge rounded-pill px-3" style="background:#FF7701">
                                                    <h2 class="notes p-0 m-0" style="color:#0C0F38 ">VIP</h2>
                                                </div>
                                            @else
                                                <div class="badge rounded-pill px-3" style="background:#0C0F38">
                                                    <h2 class="notes p-0 m-0" style="color:#ffff; font-size: 22px;">Regular</h2>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-3">
                                        <label>Number of Party <span class="text-danger">*</span></label>
                                        <input type="number" required name="how_many_people_attended" class="form-control" style="border-bottom: 3px solid rgb(55, 98, 227) !important;">
                                        @error('how_many_people_attended')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-sm btn-primary"><i
                                                class="bi bi-send me-2"></i> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="min-h-screen d-flex align-items-center bg-gray-200">
                            <div class="max-w-screen-md w-full mx-auto p-4 text-center">
                                <h2 class="text-3xl mb-4 font-bold">
                                    Thank you for your attendance.
                                </h2>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')

    <script src="https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script src="https://fastly.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        var user1 = document.querySelector("#phoneUser1");
        var user2 = document.querySelector("#phoneUser2");

        const phoneInput1 = window.intlTelInput(user1, {
            utilsScript: "https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            initialCountry: 'id',
            onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
        });

        const phoneInput2 = window.intlTelInput(user2, {
            utilsScript: "https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
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

        new TomSelect('#schoolList', {
            create: true
        });

        new TomSelect('#graduation_year', {
            create: false
        });

        new TomSelect('#destination_country', {
            create: false
        });
    </script>
    <script>
        function test() {
            parent.$('#clientDetail').modal('hide')
        }
    </script>
@endpush
