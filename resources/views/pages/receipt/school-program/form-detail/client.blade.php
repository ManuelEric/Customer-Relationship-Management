<div class="accordion accordion-flush shadow-sm">
    <div class="accordion-item rounded">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#schoolInfo" aria-expanded="true">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    School Information
                </h6>
            </button>
        </h2>
        <div id="schoolInfo" class="accordion-collapse show" aria-labelledby="schoolInfo">
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
                                  {{ $invoiceSch->sch_prog->school->sch_mail ? $invoiceSch->sch_prog->school->sch_mail : 'Not Available' }}
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
                                {{ $invoiceSch->sch_prog->school->sch_phone ? $invoiceSch->sch_prog->school->sch_phone : 'Not Available' }}
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
                                 {!! $invoiceSch->sch_prog->school->sch_location !!}
                                    {{ $invoiceSch->sch_prog->school->sch_city }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
