<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Event</title>
    <link href="https://fastly.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link href="https://fastly.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/form-embed.css') }}">
</head>

<body>
    <div id="app">
        <div class="container">
            <div class="row d-flex align-items-center justify-content-center vh-100">
                <div class="col-md-12">
                    <div class="row align-items-stretch g-0">
                        <div class="col-md-4">
                            <div class="card h-100 bg-form border-0 shadow">
                                <div class="card-body d-flex align-items-end p-0 m-0">
                                    <div class="w-100 bg-indicator">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="indicator">
                                                    <div class="indicator-icon"
                                                        :class="section == 1 ? 'progress' : 'done'">
                                                        1
                                                    </div>
                                                    <div class="indicator-text" :class="section == 1 ? 'active' : ''">
                                                        Your Information
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="indicator">
                                                    <div class="indicator-icon i "
                                                        :class="section == 2 ? 'progress' : ''">
                                                        2
                                                    </div>
                                                    <div class="indicator-text" :class="section == 2 ? 'active' : ''">
                                                        Additional Information
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card h-100 border-0 shadow">
                                <div class="card-body">
                                    <h2 class="mb-4">
                                        Let us know you better!</h2>
                                    <section v-if="section==1">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <form class="form-floating">
                                                    <input type="text" class="form-control" id="nameInput"
                                                        placeholder="Name" v-model="registration.fullname"
                                                        :class="shouldShowError('fullname') ?
                                                            'is-invalid' : ''"
                                                        @input="touchField('fullname')">
                                                    <label for="nameInput">Full name <span
                                                            class="text-danger">*</span></label>
                                                </form>
                                                <small class="text-danger" v-if="shouldShowError('fullname')">
                                                    @{{ validate.fullname.$silentErrors[0]?.$message }}
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <form class="form-floating">
                                                    <input type="email" class="form-control" id="emailInput"
                                                        placeholder="Email" v-model="registration.email"
                                                        :class="shouldShowError('email') ?
                                                            'is-invalid' : ''"
                                                        @input="touchField('email')">
                                                    <label for="emailInput">Email <span
                                                            class="text-danger">*</span></label>
                                                </form>
                                                <small class="text-danger" v-if="shouldShowError('email')">
                                                    @{{ validate.email.$silentErrors[0]?.$message }}
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <form class="form-floating">
                                                    <input type="text" class="form-control" id="phoneInput"
                                                        placeholder="" v-model="registration.phone"
                                                        :class="shouldShowError('phone') ?
                                                            'is-invalid' : ''"
                                                        @input="formatPhone('phone')">
                                                    <label for="phoneInput">Phone number <span
                                                            class="text-danger">*</span></label>
                                                </form>
                                                <small class="text-danger" v-if="shouldShowError('phone')">
                                                    @{{ validate.phone.$silentErrors[0]?.$message }}
                                                </small>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <div class="card border-form">
                                                    <div class="card-body">
                                                        <label for="">You are a </label>
                                                        <div class="d-flex align-items-center gap-3 mt-2">
                                                            <div class="role">
                                                                <img loading="lazy"  src="{{ asset('img/form-embed/student.avif') }}"
                                                                    alt="">
                                                                <input class="role-input" type="radio" name="role"
                                                                    v-model="registration.role" value="student"
                                                                    id="studentRole">
                                                                <label class="role-label" for="studentRole">
                                                                    Student
                                                                </label>
                                                            </div>
                                                            <div class="role">
                                                                <img loading="lazy"  src="{{ asset('img/form-embed/parent.webp') }}">
                                                                <input class="role-input" type="radio" name="role"
                                                                    id="parentRole" v-model="registration.role"
                                                                    value="parent">
                                                                <label class="role-label" for="parentRole">
                                                                    Parent
                                                                </label>
                                                            </div>
                                                            <div class="role">
                                                                <img loading="lazy"  src="{{ asset('img/form-embed/teacher.avif') }}">
                                                                <input class="role-input" type="radio"
                                                                    name="role" id="teacherRole"
                                                                    v-model="registration.role" value="teacher">
                                                                <label class="role-label" for="teacherRole">
                                                                    Teacher
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 text-end">
                                                <hr>
                                                <button type="button" class="btn btn-outline-primary"
                                                    @click="updateSection(2)">
                                                    Next <i class="bi bi-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </section>
                                    <section v-if="section==2">
                                        {{-- Parent  --}}
                                        <div class="col-md-12 mt-3" v-if="registration.role=='parent'">
                                            <div class="card border-form">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <label for="haveChild">Have you already child? </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                role="switch" id="haveChild"
                                                                v-model="registration.have_child"
                                                                :checked="registration.have_child">
                                                            <label class="form-check-label" for="haveChild"></label>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2" v-if="registration.have_child">
                                                        <div class="col-md-12">
                                                            <form class="form-floating">
                                                                <input type="text" class="form-control"
                                                                    id="childNameInput" placeholder=""
                                                                    v-model="registration.secondary_name"
                                                                    :class="shouldShowError('secondary_name') ?
                                                                        'is-invalid' : ''"
                                                                    @input="touchField('secondary_name')">
                                                                <label for="childNameInput">Your child full name <span
                                                                        class="text-danger">*</span></label>
                                                            </form>
                                                            <small class="text-danger"
                                                                v-if="shouldShowError('secondary_name')">
                                                                @{{ validate.secondary_name.$silentErrors[0]?.$message }}
                                                            </small>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <form class="form-floating">
                                                                <input type="text" class="form-control"
                                                                    id="childEmailInput" placeholder=""
                                                                    v-model="registration.secondary_email">
                                                                <label for="childEmailInput">Your child email</label>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <form class="form-floating">
                                                                <input type="text" class="form-control"
                                                                    id="childPhoneInput" placeholder=""
                                                                    v-model="registration.secondary_phone"
                                                                    @input="formatPhone('secondary_phone')">
                                                                <label for="childPhoneInput">Your child phone
                                                                    number</label>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <form class="form-floating">
                                                                <input type="text" class="form-control"
                                                                    id="teacherSchoolName" placeholder=""
                                                                    v-model="registration.school"
                                                                    :class="shouldShowError('school') ?
                                                                        'is-invalid' : ''"
                                                                    @input="touchField('school')">
                                                                <label for="teacherSchoolName">What school does your
                                                                    child go to? <span
                                                                        class="text-danger">*</span></label>
                                                            </form>
                                                            <small class="text-danger"
                                                                v-if="shouldShowError('school')">
                                                                @{{ validate.school.$silentErrors[0]?.$message }}
                                                            </small>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <form class="form-floating">
                                                                <input type="text" class="form-control"
                                                                    id="Graduate" placeholder=""
                                                                    v-model="registration.graduation_year"
                                                                    :class="shouldShowError('graduation_year') ?
                                                                        'is-invalid' : ''"
                                                                    @input="touchField('graduation_year')">
                                                                <label for="Graduate">When do you expect your child to
                                                                    graduate? <span
                                                                        class="text-danger">*</span></label>
                                                            </form>
                                                            <small class="text-danger"
                                                                v-if="shouldShowError('graduation_year')">
                                                                @{{ validate.graduation_year.$silentErrors[0]?.$message }}
                                                            </small>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <form class="form-floating">
                                                                <input type="text" class="form-control"
                                                                    id="Destination" placeholder=""
                                                                    v-model="registration.destination_country"
                                                                    :class="shouldShowError('destination_country') ?
                                                                        'is-invalid' : ''"
                                                                    @input="touchField('destination_country')">
                                                                <label for="Destination">Which country does your child
                                                                    interest in studying abroad?<span
                                                                        class="text-danger">*</span></label>
                                                            </form>
                                                            <small class="text-danger"
                                                                v-if="shouldShowError('destination_country')">
                                                                @{{ validate.destination_country.$silentErrors[0]?.$message }}
                                                            </small>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <form class="form-floating">
                                                                <input type="text" class="form-control"
                                                                    id="Scholarship" placeholder=""
                                                                    v-model="registration.scholarship"
                                                                    :class="shouldShowError('scholarship') ?
                                                                        'is-invalid' : ''"
                                                                    @input="touchField('scholarship')">
                                                                <label for="Scholarship">Are your child eligible for a
                                                                    need-based scholarship?<span
                                                                        class="text-danger">*</span></label>
                                                            </form>
                                                            <small class="text-danger"
                                                                v-if="shouldShowError('scholarship')">
                                                                @{{ validate.scholarship.$silentErrors[0]?.$message }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Student  --}}
                                        <div class="col-md-12 mt-3" v-if="registration.role=='student'">
                                            <div class="row mt-2">
                                                <div class="col-md-12 mt-3">
                                                    <form class="form-floating">
                                                        <input type="text" class="form-control"
                                                            id="teacherSchoolName" placeholder=""
                                                            v-model="registration.school"
                                                            :class="shouldShowError('school') ?
                                                                'is-invalid' : ''"
                                                            @input="touchField('school')" list="suggestions">

                                                        <label for="teacherSchoolName">Which school are you from? <span
                                                                class="text-danger">*</span></label>

                                                        <datalist id="suggestions">
                                                            <option v-for="item in school" :key="item"
                                                                :value="item.sch_name">
                                                        </datalist>
                                                    </form>
                                                    <small class="text-danger" v-if="shouldShowError('school')">
                                                        @{{ validate.school.$silentErrors[0]?.$message }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6 mt-3">
                                                    <form class="form-floating">
                                                        <input type="text" class="form-control" id="Graduate"
                                                            placeholder="" v-model="registration.graduation_year"
                                                            :class="shouldShowError('graduation_year') ?
                                                                'is-invalid' : ''"
                                                            @input="touchField('graduation_year')">
                                                        <label for="Graduate">When do you expect to graduate? <span
                                                                class="text-danger">*</span></label>
                                                    </form>
                                                    <small class="text-danger"
                                                        v-if="shouldShowError('graduation_year')">
                                                        @{{ validate.graduation_year.$silentErrors[0]?.$message }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6 mt-3">
                                                    <form class="form-floating">
                                                        <select class="form-select" v-model="registration.scholarship"
                                                            :class="shouldShowError('scholarship') ?
                                                                'is-invalid' : ''"
                                                            @change="touchField('scholarship')">
                                                            <option value="">Open this select menu</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>

                                                        <label for="Scholarship">Are you eligible for a
                                                            need-based scholarship?<span
                                                                class="text-danger">*</span></label>
                                                    </form>
                                                    <small class="text-danger" v-if="shouldShowError('scholarship')">
                                                        @{{ validate.scholarship.$silentErrors[0]?.$message }}
                                                    </small>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <form class="border p-2">
                                                        <small class="text-muted" for="Destination">Which country are
                                                            you thinking of
                                                            studying in?<span class="text-danger">*</span></small>
                                                        <select class="select w-100"
                                                            :value="registration.destination_country"
                                                            :class="shouldShowError('destination_country') ?
                                                                'is-invalid' : ''"
                                                            multiple>
                                                            <option value="">Open this select menu</option>
                                                            <option value="Item 1">Item 1</option>
                                                            <option value="Item 2">Item 2</option>
                                                        </select>
                                                    </form>
                                                    <small class="text-danger"
                                                        v-if="shouldShowError('destination_country')">
                                                        @{{ validate.destination_country.$silentErrors[0]?.$message }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Teacher  --}}
                                        <div class="col-md-12 mt-3" v-if="registration.role=='teacher'">
                                            <form class="form-floating">
                                                <input type="text" class="form-control" id="teacherSchoolName"
                                                    placeholder="" v-model="registration.school"
                                                    :class="shouldShowError('school') ?
                                                        'is-invalid' : ''"
                                                    @input="touchField('school')" list="suggestions">

                                                <label for="teacherSchoolName">Which school are you from? <span
                                                        class="text-danger">*</span></label>

                                                <datalist id="suggestions">
                                                    <option v-for="item in school" :key="item"
                                                        :value="item.sch_name">
                                                </datalist>
                                            </form>
                                            <small class="text-danger" v-if="shouldShowError('school')">
                                                @{{ validate.school.$silentErrors[0]?.$message }}
                                            </small>
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <form class="form-floating">
                                                <select class="form-select" v-model="registration.leadSource"
                                                    :class="shouldShowError('leadSource') ?
                                                        'is-invalid' : ''"
                                                    @change="touchField('leadSource')">
                                                    <option selected>Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                                <label for="leadSource">I know this event from <span
                                                        class="text-danger">*</span></label>
                                            </form>
                                            <small class="text-danger" v-if="shouldShowError('leadSource')">
                                                @{{ validate.leadSource.$silentErrors[0]?.$message }}
                                            </small>
                                        </div>

                                        <hr>
                                        <div class="col-md-12 d-flex justify-content-between">
                                            <button type="button" class="btn btn-outline-primary"
                                                @click="updateSection(1)">
                                                <i class="bi bi-arrow-left"></i>
                                                Back
                                            </button>
                                            <button type="button" class="btn btn-outline-primary"
                                                @click="submitForm()">
                                                Submit <i class="bi bi-send"></i>
                                            </button>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"
        integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script src="https://fastly.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script src="https://fastly.jsdelivr.net/npm/vue-demi"></script>
    <script src="https://fastly.jsdelivr.net/npm/@vuelidate/core"></script>
    <script src="https://fastly.jsdelivr.net/npm/@vuelidate/validators"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.6/bundle/libphonenumber-max.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select').select2({
                placeholder: "Select a value"
            });
        });

        const endpoint = "{{ url('/api/') }}"

        const {
            createApp,
            ref,
            computed,
            onMounted,
        } = Vue;

        const {
            useVuelidate
        } = Vuelidate;

        const {
            required,
            email,
            numeric,
            minLength,
            alpha
        } = VuelidateValidators;

        createApp({
            setup() {
                let formTouched = true;
                const section = ref(2)
                const loading = ref(false)
                const registration = ref({
                    role: 'student',
                    fullname: '',
                    email: '',
                    phone: '',
                    secondary_name: '',
                    secondary_email: '',
                    secondary_phone: '',
                    school: '',
                    graduation_year: '',
                    destination_country: [],
                    school: '',
                    scholarship: '',
                    leadSource: '',
                    event_id: '',
                    attend_status: '',
                    attend: '',
                    event_type: '',
                    status: 'ots',
                    referral: '',
                    client_type: '',
                    have_child: true
                });

                const rules = computed(() => ({
                    fullname: {
                        required,
                        minLength: minLength(3),
                    },
                    email: {
                        required,
                        email
                    },
                    phone: {
                        required,
                        minLength: minLength(5),
                    },
                    secondary_name: {
                        required,
                    },
                    secondary_email: {
                        email,
                    },
                    secondary_phone: {
                        minLength: minLength(5)
                    },
                    school: {
                        required
                    },
                    graduation_year: {
                        required
                    },
                    destination_country: {
                        required
                    },
                    scholarship: {
                        required
                    },
                    leadSource: {
                        required
                    },
                }))

                const school = ref({})

                const section_1_rule = ['fullname', 'email', 'phone']
                const student_rule = ['school', 'graduation_year', 'destination_country', 'scholarship',
                    'leadSource'
                ]
                const parent_child_rule = ['secondary_name', 'school', 'graduation_year', 'destination_country',
                    'scholarship', 'leadSource'
                ]
                const parent_not_child_rule = ['leadSource']
                const teacher_rule = ['school', 'leadSource']

                const validate = useVuelidate(rules, registration)

                const touchField = (field) => {
                    validate.value[field].$touch();
                    if (field == "school")
                        checkSchool();
                };

                const shouldShowError = (field) => {
                    return validate.value[field].$error;
                };

                const checkingValidation = (array) => {
                    let checking = []
                    array.forEach(element => {
                        validate.value[element].$validate();
                        if (!validate.value[element].$invalid) {
                            checking.push(true)
                        } else {
                            checking.push(false)
                        }
                    });

                    return !checking.includes(false)
                }

                const updateSection = (field) => {
                    // Section 1 
                    if (field == 2) {
                        const check = checkingValidation(section_1_rule);
                        if (check) section.value = 2
                    } else {
                        section.value = 1
                    }
                }

                const formatPhone = (field) => {
                    validate.value[field].$touch();

                    let phone = registration.value[field].toString()
                    let numberObj = libphonenumber.parsePhoneNumber(phone, 'ID')

                    console.log(numberObj.format('INTERNATIONAL'));
                    registration.value[field] = numberObj.number
                }

                const submitForm = () => {
                    let data = []
                    const role = registration.value.role
                    const have_child = registration.value.have_child

                    if (role == 'student')
                        data = student_rule
                    else if (role == 'parent')
                        if (have_child) {
                            data = parent_child_rule
                        } else {
                            data = parent_not_child_rule
                        }
                    else
                        data = teacher_rule


                    // check validation 
                    const check = checkingValidation(data)

                    if (check)
                        console.log(registration.value);
                }

                const checkSchool = () => {
                    axios.get(endpoint + '/school?search=' + registration.value.school)
                        .then(function(response) {
                            // handle success
                            school.value = response.data
                        })
                        .catch(function(error) {
                            // handle error
                            console.log(error);
                        })
                }

                return {
                    loading,
                    section,
                    registration,
                    school,
                    validate,
                    touchField,
                    shouldShowError,
                    updateSection,
                    formatPhone,
                    submitForm,
                    checkSchool,
                }
            }
        }).mount('#app')
    </script>

</body>

</html>
