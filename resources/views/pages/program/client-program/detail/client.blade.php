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
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    E-mail
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $student->mail }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Phone Number
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $student->phone }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Address
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {!! $student->address !!} 
                                {!! $student->postal_code ? $student->postal_code."<br>" : null !!} 
                                {{ $student->city }} {{ $student->state }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    School Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $student->school->sch_name }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Graduation Year
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $student->graduation_year }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Follow-up Priority
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $student->st_levelinterest }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Lead
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
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
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#parentInfo">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    Parents Information
                </h6>
            </button>
        </h2>
        <div id="parentInfo" class="accordion-collapse collapse">
            <div class="accordion-body p-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Parents Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $student->parents()->first()->fullname }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Parents Email
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $student->parents()->first()->mail }}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Parents Phone
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $student->parents()->first()->phone }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
