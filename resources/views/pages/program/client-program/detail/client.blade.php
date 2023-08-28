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
                                    {{$viewStudent->grade_now != null ? $viewStudent->grade_now : ''}}
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
                    <div class="card-body">
                        @if ($student->parents()->count() > 0)
                            <div class="row mb-2 g-1">
                                <div class="col d-flex justify-content-between">
                                    <label>
                                        Parents Name
                                    </label>
                                    <label>:</label>
                                </div>
                                <div class="col-md-8 col-8">
                                    {{ $student->parents()->first()->fullname }}
                                </div>
                            </div>
                            <div class="row mb-2 g-1">
                                <div class="col d-flex justify-content-between">
                                    <label>
                                        Parents Email
                                    </label>
                                    <label>:</label>
                                </div>
                                <div class="col-md-8 col-8">
                                    {{ $student->parents()->first()->mail }}
                                </div>
                            </div>
                            <div class="row mb-2 g-1">
                                <div class="col d-flex justify-content-between">
                                    <label>
                                        Parents Phone
                                    </label>
                                    <label>:</label>
                                </div>
                                <div class="col-md-8 col-8">
                                    {{ $student->parents()->first()->phone }}
                                </div>
                            </div>
                        @else
                            <div class="row mb-2 g-1">
                                <div class="col-md justify-content-between">
                                    <label for="">There's no parent information.</label><br>
                                    <span>Input parent information <a href="
                                        @php
                                            $link = '';
                                        @endphp
                                        @if (isset($student))
                                            @php
                                                $link = route('parent.create').'?child='.$student->id;
                                            @endphp
                                        @endif
                                        
                                        @if (isset($clientProgram))
                                            @php
                                                $link .= "&client_prog=".$clientProgram->clientprog_id;
                                            @endphp
                                        @endif
                                        {{ $link }}">here</a></span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
