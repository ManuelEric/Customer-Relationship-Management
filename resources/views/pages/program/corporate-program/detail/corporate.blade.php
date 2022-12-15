<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Partner Information
            </h6>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-2 g-1">
            <div class="col-md-4 d-flex justify-content-between">
                <label>
                    E-mail
                </label>
                <label>:</label>
            </div>
            <div class="col-md-8">
                {{ $partner->corp_mail ? $partner->corp_mail : 'Not Available'}}
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
               {{ $partner->corp_phone ? $partner->corp_phone : 'Not Available' }}
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
                {!! $partner->corp_address ? $partner->corp_address : 'Not Available' !!}
            </div>
        </div>
    </div>
</div>
