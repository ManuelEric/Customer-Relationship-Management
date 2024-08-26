<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst(request()->get('event_name')) }} Form</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.webp') }}" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
    <link href="https://fastly.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.js"></script>
    <script src="https://fastly.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <style>
        .text-danger {
            color: red;
        }

        .iti {
            width: 100% !important;
        }

        .ts-control {
            border: none !important;
            padding: 8px 0 !important;
        }

        .ts-wrapper.single .ts-control,
        .ts-wrapper.single .ts-control input {
            font-size: 1.25rem !important;
        }

        .ts-wrapper.multi .ts-control>div,
        .item {
            cursor: pointer;
            margin: 3px !important;
            padding: 3px 6px;
            background: #a39f9f;
            color: #ffffff;
            border: 0px solid #d0d0d0;
            font-size: 1.25rem !important;
            border-radius: 3px;
        }

        .ts-dropdown {
            font-size: 1.25rem !important;
        }

        .step-active {
            position: relative;
            opacity: 1;
            z-index: 1;
            transition: all 0.4s ease-in-out;
        }

        .step-inactive {
            position: absolute;
            width: 100%;
            z-index: -1;
            opacity: 0;
            transition: all 0.4s ease-in-out;
        }

        .bg-form {
            background-image: url('{{ asset('img/form-embed/bg-form.webp') }}');
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat
        }

        .banner img {
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        @media only screen and (max-width: 600px) {
            .bg-form {
                background: white !important;
            }
        }
    </style>
</head>

<body>
    @php
        $image = isset($event->event_banner) ? asset('storage/uploaded_file/events/' . $event->event_banner) : 'https://picsum.photos/900/200';
    @endphp
    <div
        class="min-h-screen flex items-center {{ request()->get('form_type') == 'cta' ? 'bg-form' : 'bg-transparent' }}">
        <div class="max-w-screen-lg w-full mx-auto p-4 relative overflow-hidden">
            @if (request()->get('form_type') == 'cta')
                <div class="w-full flex justify-center my-4">
                    <img loading="lazy"  src="{{ asset('img/logo.webp') }}" alt="Form ALL-in Event" class="w-[150px]">
                </div>

                <div class="h-[200px] overflow-hidden mb-2 rounded-lg shadow banner">
                    <img loading="lazy"  src="{{ $image }}" alt="Form ALL-in Event"
                        class="w-full object-cover hover:scale-[1.05] ease-in-out duration-500">
                </div>
            @endif

            @if ($errors->any())
                <div class="fixed bottom-5 right-5 w-[350px] z-[999]" id="notif">
                    <ul class="grid grid-cols-1 gap-2">
                        <li class="p-2 border-2 border-red-800 rounded-lg text-red-800 bg-white">Registration failed.
                            Please fill your data</li>
                    </ul>
                </div>
            @endif

            <form action="{{ url('form/events') }}" method="POST" id="form-events">
                @csrf
                {{-- Event Name  --}}
                <input type="hidden" name="event_name" value="{{ $_GET['event_name'] }}">
                {{-- Category  --}}
                <input type="hidden" name="category" value="{{ $event->category ?? null }}">
                {{-- Attend Status  --}}
                <input type="hidden" name="attend_status" id=""
                    value="{{ request()->get('attend_status') == 'attend' ? 'attend' : 'join' }}">
                {{-- Event Type  --}}
                <input type="hidden" name="event_type" id=""
                    value="{{ request()->get('event_type') == 'offline' ? 'offline' : '' }}">
                {{-- Status  --}}
                <input type="hidden" name="status" id=""
                    value="{{ request()->get('status') == 'ots' ? 'ots' : '' }}">
                {{-- Referral  --}}
                <input type="hidden" name="referral" id=""
                    value="{{ request()->get('ref') ? request()->get('ref') : '' }}">
                {{-- Notes VIP / VVIP --}}
                <input type="hidden" name="client_type" value="{{ request()->get('type') ?? '' }}">

                <section id="role" class="page step-active">
                    <div
                        class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 md:text-3xl text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Let us know you better by filling out this form!
                        </h2>
                        <hr class="my-5">

                        <p class="mb-3 font-normal md:text-xl text-md text-gray-700 dark:text-gray-400">
                            You are a
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-4">
                            <div class="flex select-box">
                                <input checked id="role-1" type="radio" value="parent" name="role"
                                    class="hidden peer" onchange="checkRole(this)">
                                <label for="role-1"
                                    class="flex items-center justify-center w-full md:py-4 py-2 border rounded-lg border-1 border-[#bbbbbb] text-md font-medium text-gray-900 cursor-pointer dark:text-gray-300 transition-all duration-700 peer-checked:bg-[#cccccc] dark:peer-checked:text-[#999]">
                                    <div class="text-center">
                                        <div class="flex justify-center">

                                            <img loading="lazy"  src="{{ asset('img/form-embed/parent.webp') }}" alt="Student"
                                                class="md:w-[70px] w-[40px]">
                                        </div>
                                        Parent
                                    </div>
                                </label>
                            </div>
                            <div class="flex select-box">
                                <input id="role-2" type="radio" value="student" name="role" class="hidden peer"
                                    onchange="checkRole(this)">
                                <label for="role-2"
                                    class="flex items-center justify-center w-full md:py-4 py-2 border rounded-lg border-1 border-[#bbbbbb] text-md font-medium text-gray-900 cursor-pointer dark:text-gray-300 transition-all duration-700 peer-checked:bg-[#cccccc] dark:peer-checked:text-[#999]">
                                    <div class="text-center">
                                        <div class="flex justify-center">

                                            <img loading="lazy"  src="{{ asset('img/form-embed/student.webp') }}" alt="Parent"
                                                class="md:w-[70px] w-[40px]">

                                        </div>
                                        Student
                                    </div>
                                </label>
                            </div>
                            <div class="flex select-box">
                                <input id="role-3" type="radio" value="teacher/counsellor" name="role"
                                    class="hidden peer" onchange="checkRole(this)">
                                <label for="role-3"
                                    class="flex items-center justify-center w-full md:py-4 py-2 border rounded-lg border-1 border-[#bbbbbb] text-md font-medium text-gray-900 cursor-pointer dark:text-gray-300 transition-all duration-700 peer-checked:bg-[#cccccc] dark:peer-checked:text-[#999]">
                                    <div class="flex flex-col items-center">
                                        <div class="flex justify-center">

                                            <img loading="lazy"  src="{{ asset('img/form-embed/teacher.webp') }}" alt="Parent"
                                                class="md:w-[70px] w-[40px]">

                                        </div>
                                        Teacher/Counsellor
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end mt-10">
                            <button type="button" onclick="step('role', 'user1','next')"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-blue-700 bg-white border-2 border-blue-700 rounded-lg hover:bg-blue-800 hover:text-white ease-in-out duration-500">
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-right ml-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>


                <section id="user1" class="page step-inactive">
                    <div
                        class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 md:text-2xl text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Please fill in your information
                        </h2>
                        <hr class="my-5">
                        <div class="grid md:grid-cols-3 grid-cols-1 gap-4">
                            <div class="col md:mb-4 mb-2 main-user">
                                <label
                                    class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400">
                                    Full Name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="fullname[]" value="{{ old('fullname.0') }}"
                                    class="w-full md:text-xl text-md border-0 border-b-2 focus:outline-0 focus:ring-0 px-0 required"
                                    id="name_input">
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                                @error('fullname.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col md:mb-4 mb-2 main-user">
                                <label
                                    class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400">
                                    Email <span class="text-red-400">*</span>
                                </label>
                                <input type="email" name="email[]" value="{{ old('email.0') }}"
                                    class="w-full md:text-xl text-md border-0 border-b-2 focus:outline-0 focus:ring-0 px-0 required"
                                    id="email_input">
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                                @error('email.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col md:mb-4 mb-2 main-user">
                                <label class="font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400 block">
                                    Phone Number <span class="text-red-400">*</span>
                                </label>
                                <input type="tel" name="phone[]" value="{{ old('phone.0') }}"
                                    class="w-full md:text-xl text-md border-0 border-b-2 focus:outline-0 focus:ring-0 px-0 mx-0 required"
                                    id="phoneUser1">
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                                @error('fullnumber.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                                <input type="hidden" name="fullnumber[]" id="phone1"
                                    value="{{ old('fullnumber.0') }}">
                            </div>
                            <div class="col md:mb-4 mb-2 user-other">
                                <label
                                    class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400">
                                    Your <span class="role">Child's</span> Name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="fullname[]" id="other_name"
                                    value="{{ old('fullname.1') }}"
                                    class="w-full md:text-xl text-md border-0 border-b-2 focus:outline-0 focus:ring-0 px-0 child_info required">
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                                @error('fullname.1')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col md:mb-4 mb-2 user-other">
                                <label
                                    class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400">
                                    Your <span class="role">Child's</span> Email
                                    @if (request()->get('status') || request()->get('status') == 'ots')
                                        <span class="text-red-400">*</span>
                                    @endif
                                </label>
                                <input type="email" name="email[]" id="other_email" value="{{ old('email.1') }}"
                                    class="w-full md:text-xl text-md border-0 border-b-2 focus:outline-0 focus:ring-0 px-0 child_info">
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                                @error('email.1')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col md:mb-4 mb-2 user-other">
                                <label class="font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400 block">
                                    <span class="role">Child's</span> Number
                                    @if (request()->get('status') || request()->get('status') == 'ots')
                                        <span class="text-red-400">*</span>
                                    @endif
                                </label>
                                <input type="tel" name="phone[]" value="{{ old('phone.1') }}"
                                    class="w-full md:text-xl text-md border-0 border-b-2 focus:outline-0 focus:ring-0 px-0 mx-0"
                                    id="phoneUser2">
                                <input type="hidden" name="fullnumber[]" value="{{ old('fullnumber.1') }}"
                                    id="phone2" class="child_info">
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                                @error('fullnumber.1')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-between mt-10">
                            <button type="button" onclick="step('user1','role','prev')"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-red-700 bg-white border-2 border-red-700 rounded-lg hover:bg-red-700 hover:text-white ease-in-out duration-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-left mr-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="step('user1','info','next')"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-blue-700 bg-white border-2 border-blue-700 rounded-lg hover:bg-blue-800 hover:text-white ease-in-out duration-500">
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-right ml-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>

                <section id="info" class="page step-inactive">
                    <div
                        class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 md:text-3xl text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Please fill in your information
                        </h2>
                        <hr class="my-5">

                        <div class="mb-4" id="school_input">
                            <label
                                class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400">
                                School <span class="text-red-400">*</span>
                            </label>
                            <select name="school" id="schoolList"
                                class="w-full md:text-xl text-md border-0 border-b-2 border-gray-500 focus:outline-0 focus:ring-0 px-0"
                                placeholder="Type your school name if your school is not on the list">
                                <option data-placeholder="true"></option>
                                @foreach ($schools as $school)
                                    <option value="{{ $school->sch_id }}"
                                        {{ old('school') == $school->sch_id ? 'selected' : null }}>
                                        {{ $school->sch_name }}</option>
                                @endforeach
                            </select>
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                            @error('school')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-4" id="graduation_input">
                            <label
                                class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400">
                                When do you expect to graduate? <span class="text-red-400">*</span>
                            </label>
                            <select name="graduation_year" id="graduation_year"
                                class="w-full md:text-xl text-md border-0 border-b-2 border-gray-500 focus:outline-0 focus:ring-0 px-0"
                                placeholder="">
                                <option value=""></option>
                                @for ($i = date('Y'); $i < date('Y') + 6; $i++)
                                    <option value="{{ $i }}"
                                        {{ old('graduation_year') == $i ? 'selected' : null }}>{{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                            @error('graduation_year')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-4" id="country_input">
                            <label
                                class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400">
                                Destination Country
                            </label>
                            <select name="destination_country[]" multiple="multiple" id="destination_country"
                                class="w-full md:text-xl text-md border-0 border-b-2 border-gray-500 focus:outline-0 focus:ring-0 px-0"
                                placeholder="">
                                <option value=""></option>
                                @foreach ($tags as $tag)
                                    @if ($tag->name != 'Other')
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                            @error('destination_country')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-4" id="scholarship_input">
                            <label class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400">
                                Are you eligible for a need-based scholarship?
                            </label>
                            <select name="scholarship_eligibility" id="scholarship_eligibility" class="w-full form-select md:text-xl text-md border-0 border-b-2 border-gray-500 focus:outline-0 focus:ring-0 px-0">
                                <option value=""></option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                        @if (request()->get('status') || request()->get('status') == 'ots')
                            <div class="mb-4">
                                <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400">
                                    Number of Party <span class="text-red-400">*</span>
                                </label>
                                <input type="number" name="attend"
                                    class="required w-full md:text-xl text-md border-0 border-b-2 focus:outline-0 focus:ring-0 px-0">
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                                @error('attend')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        @endif

                        @if (!request()->get('ref') && request()->get('ref') === null)
                            <div class="mb-4">
                                <label
                                    class="md:mb-3 mb-1 font-normal md:text-lg text-sm text-gray-700 dark:text-gray-400 block">
                                    I know this event from <span class="text-red-400">*</span>
                                </label>
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                                <select name="leadsource" id="leadSource"
                                    class="w-full md:text-xl text-md border-0 border-b-2 border-gray-500 focus:outline-0 focus:ring-0 px-0"
                                    placeholder="Pick one item">
                                    <option data-placeholder="true"></option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->lead_id }}"
                                            {{ old('leadsource') == $lead->lead_id ? 'selected' : null }}>
                                            {{ $lead->main_lead == 'KOL' ? $lead->sub_lead : $lead->main_lead }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('leadsource')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        @endif

                        <div class="flex justify-between mt-10">
                            <button type="button" onclick="step('info','user1','prev')"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-red-700 bg-white border-2 border-red-700 rounded-lg hover:bg-red-700 hover:text-white ease-in-out duration-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-left mr-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                                </svg>
                                Previous
                            </button>
                            <button type="submit" id="btn-submit"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-blue-700 bg-white border-2 border-blue-700 rounded-lg hover:bg-blue-800 hover:text-white ease-in-out duration-500">
                                Submit
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-right ml-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>
            </form>

            {{-- Footer  --}}
            @if (request()->get('form_type') == 'cta')
                <div class="w-full flex justify-center md:my-4 mt-[30px] text-sm text-center text-gray-400">
                    Copyright © 2023. ALL-in Eduspace. <br> All rights reserved
                </div>
            @endif
        </div>
    </div>
</body>

<script src="https://fastly.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
<link href="https://fastly.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
    integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
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

    new TomSelect('#schoolList', {
        create: true,
    });

    
    // $("select[name=choosen_school]").select2({
    //     placeholder: "Write school name",
    //     ajax: {
    //         delay: 250, // wait 250 milliseconds before triggering the request
    //         url: '{{ url('/') }}/api/school',
    //         dataType: 'json',
    //         data: function (params) {
    //             var query = {
    //                 search: params.term
    //             }

    //             // Query parameters will be ?search=[term]
    //             return query;
    //         }, 
    //         processResults: function (data) {
    //             return {
    //                 results: $.map(data, function (obj) {
    //                     return { id: obj.sch_id, text: obj.sch_name}
    //                 })
    //             }
    //         }

    //     },
    // })

    new TomSelect('#graduation_year', {
        create: false
    });

    new TomSelect('#destination_country', {
        create: false
    });

    new TomSelect('#leadSource', {
        create: false
    });

    new TomSelect('#scholarship_eligibility', {
        create: false
    });

    $("#btn-submit").on('click', function(e) {
        e.preventDefault();

        Swal.fire({
            width: 100,
            didOpen: () => {
                Swal.showLoading()
            },
            allowOutsideClick: () => !Swal.isLoading()
        })

        $('#form-events').submit()
    })

    $(function() {

        $("#role-1").prop('checked', true).trigger('change');
    })

    function checkRole(element) {


        const input_child_name = $('#input_child_name')
        const graduation_input = $('#graduation_input')
        const country_input = $('#country_input')
        const scholarship_input = $("#scholarship_input")
        const role = $('.role')
        const main_user = $(".main-user")
        const user_other = $('.user-other')
        const other_name = $('#other_name')

        if (element.value == "student") {

            role.html('Parent\'s')
            user_other.removeClass('hidden')
            other_name.addClass('required')
            graduation_input.removeClass('hidden')
            country_input.removeClass('hidden')
            scholarship_input.removeClass('hidden')

            setAdditionalInputLabel({
                "school": "Which school are you from? <span class='text-red-400'>*</span>",
                "graduation": "When do you expect to graduate? <span class='text-red-400'>*</span>",
                "country": "Which country are you thinking of studying in?",
            });

        } else if (element.value == "parent") {

            role.html('Child\'s')
            user_other.removeClass('hidden')
            other_name.addClass('required')
            graduation_input.removeClass('hidden')
            country_input.removeClass('hidden')
            scholarship_input.removeClass('hidden')

            setAdditionalInputLabel({
                "school": "What school does your child go to? <span class='text-red-400'>*</span>",
                "graduation": "When do you expect your child to graduate? <span class='text-red-400'>*</span>",
                "country": "Which country does your child interest in studying abroad?",
            });

        } else {
            user_other.addClass('hidden')
            other_name.removeClass('required')
            graduation_input.addClass('hidden')
            country_input.addClass('hidden')
            scholarship_input.addClass('hidden')

            setAdditionalInputLabel({
                "school": "Which school are you from? <span class='text-red-400'>*</span>",
                "graduation": null,

            })
        }
    }

    function setAdditionalInputLabel(messages) {
        $("#school_input label").html(messages['school']);
        $("#graduation_input label").html(messages['graduation']);
        $("#country_input label").html(messages['country'])
    }

    function step(current, next, type) {
        const input = $('#' + current + ' input.required')
        const alert = input.siblings('small.alert')
        const page = $('.page')
        const currentPage = $("#" + current)
        const nextPage = $("#" + next)

        for (var i = 0; i < page.length; ++i) {
            page.eq(i).addClass('step-inactive');
        }

        if (type === "prev") {
            nextPage.removeClass("step-inactive").addClass("step-active")
        } else {
            const check_input = [];
            for (var i = 0; i < input.length; ++i) {
                if (input.eq(i).attr('type') === "text" || input.eq(i).attr('type') === "email" || input.eq(i).attr(
                        'type') === "tel") {
                    const id = input.eq(i).attr('id')
                    if (input.eq(i).val() === "" || input.eq(i).val() === null) {
                        if (input.eq(i).attr('type') === "tel") {
                            $('#' + id).parents().siblings('small.alert').removeClass('hidden').addClass('block')
                        } else {
                            $('#' + id).siblings('small.alert').removeClass('hidden').addClass('block')
                        }
                        check_input.push(false);
                    } else {
                        if (input.eq(i).attr('type') === "tel") {
                            $('#' + id).parents().siblings('small.alert').removeClass('block').addClass('hidden')
                        } else {
                            $('#' + id).siblings('small.alert').removeClass('block').addClass('hidden')
                        }
                    }
                }
            }

            var index = check_input.indexOf(false);
            if (index === 0) {
                currentPage.removeClass("step-inactive").addClass("step-active");
            } else {
                nextPage.removeClass("step-inactive").addClass("step-active");
            }
        }
    }
</script>

<script>

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
    @if ($errors->any())
        setTimeout(function() {
            $("#notif").fadeOut();
        }, 4000)
    @endif
</script>

</html>
