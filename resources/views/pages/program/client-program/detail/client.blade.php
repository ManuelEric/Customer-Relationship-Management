<div class="accordion accordion-flush shadow-sm mb-3">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#clientInfo">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    Clients Information
                </h6>
            </button>
        </h2>
        <div id="clientInfo" class="accordion-collapse collapse show" aria-labelledby="clientInfo">
            <div class="accordion-body p-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    E-mail
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $student->mail }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Phone Number
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $student->phone }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Address
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ strip_tags($student->address) }} 
                                {{ $student->postal_code ? $student->postal_code."<br>" : null }} 
                                {{ $student->city }} {{ $student->state }}
                            </div>
                        </div>
                        
                        @if ($student->school)
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    School Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $student->school->sch_name }}
                            </div>
                        </div>
                        @endif
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Grade
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                    {{ $student->grade_now }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Graduation Year
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $student->graduation_year }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Follow-up Priority
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $student->st_levelinterest }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Lead
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $student->leadSource }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Register By
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $student->register_by }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#parentInfo">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    Parents Information
                </h6>
            </button>
        </h2>
        <div id="parentInfo" class="accordion-collapse collapse show">
            <div class="accordion-body p-2">
                <div class="card">
                    <div class="card-body" style="overflow: auto;">
                        @if($student->parents()->count() > 0)
                            <table class="table table-bordered" id="list-parent">
                                <thead>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($student->parents as $parent)
                                        <tr align="center">
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $parent->fullname }}</td>
                                            <td>{{ $parent->mail }}</td>
                                            <td>{{ $parent->phone }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            There's no parent information yet    
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if (isset($clientProgram) && $clientProgram->status == 4 && preg_match("/Admission/i", $clientProgram->program->prog_main) )
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#admissionInfo">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    Admission Information
                </h6>
            </button>
        </h2>
        <div id="admissionInfo" class="accordion-collapse collapse show" aria-labelledby="admissionInfo">
            <div class="accordion-body p-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Initial Consult Date
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->initconsult_date }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Initial Assessment Sent
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->assessmentsent_date }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Success Program
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->success_date }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Mentor IC
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->mentorIC()->orderBy('tbl_mentor_ic.id', 'asc')->first()->full_name }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Program End
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->prog_end_date }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Supervising Mentor
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->clientMentor()->where('type', 1)->first()->full_name }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Profile Building Mentor
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->clientMentor()->where('type', 2)->first()->full_name }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Subject Specialist Mentor
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->clientMentor()->where('type', 6)->first()->full_name }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Aplication Strategy Mentor
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->clientMentor()->where('type', 3)->first()->full_name }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Writing Mentor
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $clientProgram->clientMentor()->where('type', 4)->first()->full_name }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    Agreement
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                <a target="_blank" href="{{ url('/') }}/storage/uploaded_file/agreement/{{ $clientProgram->agreement }}">{{ $clientProgram->agreement }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
