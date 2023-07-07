<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <style>
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
    </style>
</head>

<body>
    @if($errors->any())
     <div class="alert alert-danger">
          <ul class="list-unstyled">
                 @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                 @endforeach
          </ul>
      </div>
 @endif
    <div class="min-h-screen flex items-center bg-transparent">
        <div class="max-w-screen-lg w-full mx-auto p-4">
            <form action="{{ url('form/event') }}" method="POST">
                @csrf
                <section id="role" class="page">
                    <div
                        class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Let us know you better by filling out this form!
                        </h2>
                        <hr class="my-5">

                        <p class="mb-3 font-normal text-xl text-gray-700 dark:text-gray-400">
                            Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4">
                            <div class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700">
                                <input checked id="role-1" type="radio" value="parent" name="role"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    onchange="checkRole(this)">
                                <label for="role-1"
                                    class="w-full py-4 pr-4 ml-4 text-md font-medium text-gray-900 dark:text-gray-300">
                                    Parent
                                </label>
                            </div>
                            <div class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700">
                                <input id="role-2" type="radio" value="student" name="role"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    onchange="checkRole(this)">
                                <label for="role-2"
                                    class="w-full py-4 pr-4 ml-4 text-md font-medium text-gray-900 dark:text-gray-300">
                                    Student
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
                <input type="hidden" name="event_name" value="{{$_GET['event_name']}}">
                
                <section id="user1" class="page hidden">
                    <div
                        class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Please fill in your information!
                        </h2>
                        <hr class="my-5">
                        <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400">
                                Full Name
                            </label>
                            <input type="text" name="name"
                                class="w-full text-xl border-0 border-b-2 focus:outline-0 focus:ring-0 px-0">
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                        </div>
                        <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400">
                                Email
                            </label>
                            <input type="email" name="email"
                                class="w-full text-xl border-0 border-b-2 focus:outline-0 focus:ring-0 px-0">
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                        </div>
                        <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400 block">
                                Phone Number
                            </label>
                            <input type="text" name="phone"
                                class="w-full md:w-[126vh] text-xl border-0 border-b-2 focus:outline-0 focus:ring-0 px-0 mx-0"
                                id="phoneUser1">
                            <input type="hidden" name="fullnumber" id="phone1">
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                        </div>
                         <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400">
                                Child Name
                            </label>
                            <input type="text" name="parent_child_name"
                                class="w-full text-xl border-0 border-b-2 focus:outline-0 focus:ring-0 px-0">
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
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
                            <button type="button" onclick="step('user1','user2','next')"
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

                <section id="user2" class="page hidden">
                    <div
                        class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Please fill in your child information!
                        </h2>
                        <hr class="my-5">

                        <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400">
                                Full Name
                            </label>
                            <input type="text" name="child_name"
                                class="w-full text-xl border-0 border-b-2 focus:outline-0 focus:ring-0 px-0">
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                        </div>
                        <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400">
                                Email
                            </label>
                            <input type="text" name="child_email"
                                class="w-full text-xl border-0 border-b-2 focus:outline-0 focus:ring-0 px-0">
                                <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                        </div>
                        <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400 block">
                                Phone Number
                            </label>
                            <input type="text" name="child_phone"
                                class="w-full md:w-[126vh] text-xl border-0 border-b-2 focus:outline-0 focus:ring-0 px-0 mx-0"
                                id="phoneUser2">
                            <input type="hidden" name="child_fullnumber" id="phone2">
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                        </div>

                        <div class="flex justify-between mt-10">
                            <button type="button" onclick="step('user2','user1','prev')"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-red-700 bg-white border-2 border-red-700 rounded-lg hover:bg-red-700 hover:text-white ease-in-out duration-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-left mr-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="step('user2','info','next')"
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

                <section id="info" class="page hidden">
                    <div
                        class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Please fill in your information!
                        </h2>
                        <hr class="my-5">

                        <div class="mb-4">
                                <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400">
                                    School
                                </label>
                                <select name="school" id="schoolList"
                                    class="w-full text-xl border-0 border-b-2 border-gray-500 focus:outline-0 focus:ring-0 px-0"
                                    placeholder="Pick one school" onChange="addSchool();">
                                    <option data-placeholder="true"></option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->sch_id }}"  {{ old('school') == $school->sch_id ? "selected" : null }}>{{ $school->sch_name }}</option>
                                    @endforeach
                                </select>
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                        </div>
                        <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400">
                                Expected Graduation Year
                            </label>
                            <select name="grade" id="grade"
                                class="w-full text-xl border-0 border-b-2 border-gray-500 focus:outline-0 focus:ring-0 px-0"
                                placeholder="Pick one school">
                                <option value=""></option>
                                @for ($i = date('Y'); $i < date('Y') + 6; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                        </div>
                        <div class="mb-4">
                            <label class="mb-3 font-normal text-lg text-gray-700 dark:text-gray-400 block">
                                I know this event from
                            </label>
                            <small class="alert text-red-500 text-md hidden">Please fill in above field!</small>
                            <select name="leadsource" id="leadSource"
                                class="w-full text-xl border-0 border-b-2 border-gray-500 focus:outline-0 focus:ring-0 px-0"
                                placeholder="Pick one item">
                                <option data-placeholder="true"></option>
                                @foreach ($leads as $lead)
                                    <option value="{{ $lead->lead_id }}" {{old('leadsource') == $lead->lead_id ? 'selected' : null}}>{{ $lead->main_lead == 'KOL' ? $lead->sub_lead : $lead->main_lead }}</option>
                                @endforeach                           
                            </select>
                        </div>

                        <div class="flex justify-between mt-10">
                            <button type="button" onclick="step('info','user2','prev')"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-red-700 bg-white border-2 border-red-700 rounded-lg hover:bg-red-700 hover:text-white ease-in-out duration-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-left mr-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                                </svg>
                                Previous
                            </button>
                            <button type="submit"
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
        </div>
    </div>
</body>
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
    // window.intlTelInput(user1, {
    //     utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
    //     initialCountry: 'id',
    //     onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
    // });
    // window.intlTelInput(user2, {
    //     utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
    //     initialCountry: 'id',
    //     onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
    // });

    new TomSelect('#schoolList', {
        create: true
    });

    new TomSelect('#grade', {
        create: false
    });

    new TomSelect('#leadSource', {
        create: false
    });

    function checkRole(element) {
        const buttonUser1 = document.querySelectorAll('#user1 button')
        const buttonInfo = document.querySelectorAll('#info button')

        if (element.value == "student") {
            buttonUser1[0].setAttribute("onclick", "step('user1','role', 'prev')")
            buttonUser1[1].setAttribute("onclick", "step('user1','info', 'next')")
            buttonInfo[0].setAttribute("onclick", "step('info','user1', 'prev')")
        } else {
            buttonUser1[0].setAttribute("onclick", "step('user1','role', 'prev')")
            buttonUser1[1].setAttribute("onclick", "step('user1','user2', 'next')")
            buttonInfo[0].setAttribute("onclick", "step('info','user2', 'prev')")
        }
    }

    function step(current, next, type) {
        const input = document.querySelectorAll('#' + current + ' input');
        const alert = document.querySelectorAll('#' + current + ' small.alert');
        const page = document.querySelectorAll('.page');
        const currentPage = document.querySelector("#" + current);
        const nextPage = document.querySelector("#" + next);

        for (var i = 0; i < page.length; ++i) {
            page[i].classList.add('hidden');
        }

        if (type === "prev") {
            nextPage.classList.remove("hidden");
        } else {
            const check_input = [];
            for (var i = 0; i < input.length; ++i) {
                if (input[i].type === "text") {
                    if (input[i].value === "") {
                        alert[i].classList.remove('hidden')
                        alert[i].classList.add('block')
                        check_input.push(false);
                    } else {
                        alert[i].classList.add('hidden')
                    }
                }
            }

            var index = check_input.indexOf(false);
            if (index === 0) {
                currentPage.classList.remove("hidden");
            } else {
                nextPage.classList.remove("hidden");
            }
        }
    }

    
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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

</html>
