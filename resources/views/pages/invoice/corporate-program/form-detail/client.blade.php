<div class="accordion accordion-flush shadow-sm">
    <div class="accordion-item rounded">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#clientInfo" aria-expanded="true" aria-controls="clientInfo">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    Partner Information
                </h6>
            </button>
        </h2>
        <div id="clientInfo" class="accordion-collapse show" aria-labelledby="clientInfo">
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
                                {{ $partnerProgram->corp->corp_mail ? $partnerProgram->corp->corp_mail : 'Not Available'}}
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
                                {{ $partnerProgram->corp->corp_phone ? $partnerProgram->corp->corp_phone : 'Not Available' }}

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
                                {!! $partnerProgram->corp->corp_address ? $partnerProgram->corp->corp_address : 'Not Available' !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="accordion accordion-flush shadow-sm">
    <div class="accordion-item rounded">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#picInfo" aria-expanded="true" aria-controls="picInfo">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    PIC Information
            </button>
        </h2>
        <div id="picInfo" class="accordion-collapse show" aria-labelledby="picInfo">
            <div class="accordion-body p-2">
                @foreach ($partnerProgram->corp->pic as $pic)
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $pic->pic_name ?? 'Not Available'}}
                            </div>
                        </div>
                        <div class="row mb-2 g-1">
                            <div class="col-md-4 d-flex justify-content-between">
                                <label>
                                    E-mail
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-8">
                                {{ $pic->pic_mail ?? 'Not Available'}}
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
                                {{ $pic->pic_phone ?? 'Not Available' }}

                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
