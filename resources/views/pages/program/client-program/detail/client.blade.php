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
                                    Register As
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8 col-8">
                                {{ $student->register_as }}
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
</div>
