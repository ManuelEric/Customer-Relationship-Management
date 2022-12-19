<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                Joined Event
            </h6>
        </div>
    </div>
    <div class="card-body">
        <div class="list-group">
            @for ($i = 0; $i < 3; $i++)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <strong>Event Name</strong> <br>
                            Start Date - End Date
                        </div>
                        <div class="">
                            <a href="#" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
