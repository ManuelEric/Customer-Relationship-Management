<div class="accordion accordion-flush shadow-sm">
    <div class="accordion-item rounded">
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
                        @foreach ($bundle->details->unique('client_program.client.id') as $detail)
                            <div class="accordion" id="accordion-{{ $detail->client_program->client->id }}">
                                <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $detail->client_program->client->id }}" style="font-size: 13px;">
                                    {{ $detail->client_program->client->full_name }}
                                    </button>
                                </h2>
                                <div id="collapse-{{ $detail->client_program->client->id }}" class="accordion-collapse collapse" aria-labelledby="collapse-{{ $detail->client_program->client->id }}">
                                    <div class="accordion-body">
                                        <div class="row mb-2 g-1">
                                            <div class="col-md-4 d-flex justify-content-between">
                                                <label>
                                                    E-mail
                                                </label>
                                                <label>:</label>
                                            </div>
                                            <div class="col-md-8">
                                                {{ $detail->client_program->client->mail }}
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
                                                {{ $detail->client_program->client->phone }}
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
                                                {{ strip_tags($detail->client_program->client->address)  }}
                                                {{ $detail->client_program->client->postal_code }} <br>
                                                {{ $detail->client_program->client->city }}
                                            </div>
                                        </div>
                                        @if (isset($detail->client_program->client->school->sch_name))
                                        <div class="row mb-2 g-1">
                                            <div class="col-md-4 d-flex justify-content-between">
                                                <label>
                                                    School Name
                                                </label>
                                                <label>:</label>
                                            </div>
                                            <div class="col-md-8">
                                                {{ $detail->client_program->client->school->sch_name }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="row mb-2 g-1">
                                            <div class="col-md-4 d-flex justify-content-between">
                                                <label>
                                                    Grade
                                                </label>
                                                <label>:</label>
                                            </div>
                                            <div class="col-md-8">
                                                {{ $detail->client_program->viewClient->grade_now != null && $detail->client_program->viewClient->grade_now > 12 ? 'Not high school' : $detail->client_program->viewClient->grade_now }}
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
                                                {{ $detail->client_program->client->graduation_year }}
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
                                                {{ $detail->client_program->client->st_levelinterest }}
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
                                                {{ $detail->client_program->client->lead_source }}
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    
                                        <div class="card">
                                            <p class="text-center mt-3"><b>Parent Information</b></p>
                                            <div class="card-body" style="overflow: auto;">
                                                @if ($detail->client_program->client->parents()->count() > 0)
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
                                                            @foreach ($detail->client_program->client->parents as $parent)
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
                                                    No parents data
                                                @endif
                                            </div>
                                        </div>
                                </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- <div class="accordion-item">
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
                        @if($client_prog->client->parents()->count() > 0)
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
                                    @foreach ($client_prog->client->parents as $parent)
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
                            There's no parents information
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>
