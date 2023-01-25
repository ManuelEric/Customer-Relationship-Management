<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-between g-1 mb-2">
            <div class="col-md-4">
                <div class="row g-1">
                    <div class="col-md-6">
                        <select name="" id="period" class="select w-100" onchange="checkPeriod()">
                            <option value="all">All</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-end d-none" id="monthly" onchange="checkMonthly()">
                        <input type="month" name="" id="client_status_month"
                            class="form-control form-control-sm" value="{{ date('Y-m') }}">
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-sm btn-info position-relative pe-3" style="font-size: 11px"
                    data-bs-toggle="modal" data-bs-target="#follow_up">
                    Follow Up Reminder
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 11px">
                        30
                    </span>
                </button>

                <button type="button" class="btn btn-sm btn-info position-relative ms-3 pe-3" style="font-size: 11px"
                    data-bs-toggle="modal" data-bs-target="#birthday">
                    Mentee's Birthday
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 11px">
                        30
                    </span>
                </button>
            </div>

        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center text-warning">
                        <h5 class="p-0 m-0">Prospective <br> Client</h5>
                        <h3 class="p-0 m-0">123</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center text-info">
                        <h5 class="p-0 m-0">Potential <br> Client</h5>
                        <h3 class="p-0 m-0">123</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center text-success">
                        <h5 class="p-0 m-0">Current <br> Client</h5>
                        <h3 class="p-0 m-0">123</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center text-primary">
                        <h5 class="p-0 m-0">Completed <br> Client</h5>
                        <h3 class="p-0 m-0">123</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="p-0 m-0">Mentee <br> Total</h5>
                        <h3 class="p-0 m-0">123</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="p-0 m-0">Alumni <br> Total</h5>
                        <h3 class="p-0 m-0">123</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Birthday -->
<div class="modal modal-lg fade" id="birthday" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Mentee's Birthday</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-3">
                        <input type="month" name="" id="" class="form-control form-control-sm"
                            value="{{ date('Y-m') }}">
                    </div>
                </div>
                <div class="overflow-auto" style="height: 400px">
                    <table class="table table-bordered table-hover">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Mentee's Name</th>
                                <th>Birthday</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Follow Up -->
<div class="modal modal-lg fade" id="follow_up" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Follow Up Reminder</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <h6>Today ({{ date('D, d M Y') }})</h6>
                <div class="overflow-auto mb-3" style="height: 150px">
                    <ul class="list-group">
                        @for ($i = 0; $i < 3; $i++)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="">
                                    <p class="m-0 p-0 lh-1">Mentees Name</p>
                                    <small class="m-0">Program Name</small>
                                </div>
                                <div class="">
                                    <input class="form-check-input me-1" type="checkbox" value="1"
                                        id="mark_{{ $i }}" onchange="marked({{ $i }})">
                                    <label class="form-check-label" for="mark_{{ $i }}">Done</label>
                                </div>
                            </li>
                        @endfor
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="">
                                <p class="m-0 p-0 lh-1">Mentees Name</p>
                                <small class="m-0">Program Name</small>
                            </div>
                            <div class="">
                                <input class="form-check-input me-1" type="checkbox" value="1"
                                    id="mark_{{ $i }}" onchange="marked({{ $i }})" checked>
                                <label class="form-check-label" for="mark_{{ $i }}">Done</label>
                            </div>
                        </li>
                    </ul>
                </div>
                <hr>
                <h6>Tommorow ({{ date('D, d M Y', strtotime('+1 day')) }})</h6>
                <div class="overflow-auto" style="height:150px">
                    <ul class="list-group">
                        @for ($i = 0; $i < 2; $i++)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="">
                                    <p class="m-0 p-0 lh-1">Mentees Name</p>
                                    <small class="m-0">Program Name</small>
                                </div>
                                <div class="">
                                    <input class="form-check-input me-1" type="checkbox" value="1"
                                        id="mark_{{ $i }}" onchange="marked({{ $i }})">
                                    <label class="form-check-label" for="mark_{{ $i }}">Done</label>
                                </div>
                            </li>
                        @endfor
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="">
                                <p class="m-0 p-0 lh-1">Mentees Name</p>
                                <small class="m-0">Program Name</small>
                            </div>
                            <div class="">
                                <input class="form-check-input me-1" type="checkbox" value="1"
                                    id="mark_{{ $i }}" onchange="marked({{ $i }})" checked>
                                <label class="form-check-label" for="mark_{{ $i }}">Done</label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Follow Up Notes  --}}
<div class="modal modal-md fade" id="follow_up_notes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Follow Up Notes</h5>
            </div>
            <div class="modal-body ">
                <form action="">
                    <input type="hidden" class="marked_id">
                    <textarea name="" id="" cols="30" rows="10"></textarea>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="cancelMarked()">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Follow Up  --}}
<div class="modal modal-md fade" id="cancel_follow_up_notes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Cancel Follow Up Mark</h5>
            </div>
            <div class="modal-body ">
                <form action="">
                    <input type="hidden" class="marked_id">
                    Are you sure, you want to cancel this follow up?
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="backMarked()">No</button>
                        <button type="submit" class="btn btn-sm btn-primary">Yes, Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    function checkPeriod() {
        let period = $('#period').val()
        if (period == 'monthly') {
            $('#monthly').removeClass('d-none')
            checkMonthly()
        } else {
            $('#monthly').addClass('d-none')
            checkClientStatus()
        }
    }

    function checkMonthly() {
        let month = $('#client_status_month').val()
        checkClientStatus(month)
    }

    function checkClientStatus(month = null) {
        // Axios here... 
        alert(month)
    }

    function marked(i) {
        $('.marked_id').val(i)
        if ($('#mark_' + i).is(':checked')) {
            $('#follow_up_notes').modal('show')
        } else {
            $('#cancel_follow_up_notes').modal('show')
        }
    }

    function cancelMarked() {
        let i = $('.marked_id').val()
        $('#mark_' + i).prop('checked', false)
        $('#follow_up_notes').modal('hide')
    }

    function backMarked() {
        let i = $('.marked_id').val()
        $('#mark_' + i).prop('checked', true)
        $('#cancel_follow_up_notes').modal('hide')
    }
</script>
