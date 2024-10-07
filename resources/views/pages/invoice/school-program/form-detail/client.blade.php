<div class="accordion accordion-flush shadow-sm mb-3 mb-md-0">
    <div class="accordion-item rounded">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#clientInfo" aria-expanded="true" aria-controls="clientInfo">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    School Information
                </h6>
            </button>
        </h2>
        <div id="clientInfo" class="accordion-collapse show" aria-labelledby="clientInfo">
            <div class="accordion-body p-2">
                <div class="card">
                    <div class="card-body">
                        <form action="">
                            <div class="row mb-2 g-1">
                                <div class="col-md-4 d-flex justify-content-between">
                                    <label>
                                        E-mail
                                    </label>
                                    <label>:</label>
                                </div>
                                <div class="col-md-8">
                                    {{ $school->sch_mail ? $school->sch_mail : 'Not Available' }}
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
                                    {{ $school->sch_phone ? $school->sch_phone : 'Not Available'}}
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
                                    {!! $school->sch_location !!}
                                    {{ $school->sch_city }}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
