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
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    E-mail
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $client_prog->client->mail }}
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
                                {{ $client_prog->client->phone }}
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
                                {!! $client_prog->client->address !!}
                                {{ $client_prog->client->postal_code }} <br>
                                {{ $client_prog->client->city }}
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
                                {{ $client_prog->client->school->sch_name }}
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
                                {{ $client_prog->client->graduation_year }}
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
                                {{ $client_prog->client->st_levelinterest }}
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
                                {{ $client_prog->client->lead->main_lead }}
                                
                                @if (strtolower($client_prog->client->lead->main_lead) == "external edufair")
                                    ( {{ $client_prog->client->external_edufair->title }} )
                                @elseif (strtolower($client_prog->client->lead->main_lead) == "all-in event")
                                    ( {{ $client_prog->client->event->event_title }} )
                                @endif
                                
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
                    @forelse ($client_prog->client->parents as $parent)
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Parents Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $parent->full_name }}
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
                                {{ $parent->mail }}
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
                                {{ $parent->phone }}
                            </div>
                        </div>
                    @empty
                        There's no parents information
                    @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
