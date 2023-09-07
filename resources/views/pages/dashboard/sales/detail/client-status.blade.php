

<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-md-between align-items-center g-1 mb-md-2 mb-3">
            <div class="col-md-4 mb-md-0 mb-3">
                <div class="row g-1">
                    <div class="col-md-6">
                        <select id="period" class="select w-100" onchange="checkPeriod()">
                            <option value="all">All</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-end d-none" id="monthly" onchange="checkMonthly()">
                        <input type="month" id="client_status_month" class="form-control form-control-sm"
                            value="{{ isset($filter_bymonth) ? $filter_bymonth : date('Y-m') }}">
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end text-center">
                        <a href="{{ url('api/export/client') }}"
                   class="btn btn-sm btn-outline-info btn-export" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"><i class="bi bi-download"></i></a>

                <button type="button" id="btn-follow-up" class="btn btn-sm btn-info position-relative ms-2 pe-3"
                    style="font-size: 11px" data-bs-toggle="modal" data-bs-target="#follow_up">
                    Follow Up Reminder
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 11px">
                        {{ isset($followUpReminder) ? count($followUpReminder) : 0 }}
                    </span>
                </button>

                <button type="button" id="btn-mentees-birthday" class="btn btn-sm btn-info position-relative ms-3 pe-3"
                    style="font-size: 11px" data-bs-toggle="modal" data-bs-target="#birthday">
                    Mentee's Birthday
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 11px">
                        {{ isset($menteesBirthday) ? $menteesBirthday->count() : 0 }}
                    </span>
                </button>
            </div>

        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between text-warning w-full d-flex flex-column cursor-pointer card-client"
                        data-f-date="all" data-f-client-type="new-leads">
                        <div class="d-flex">
                            {{-- <h5 class="p-0 m-0">Prospective <br> Client</h5> --}}
                            <h5 class="p-0 m-0">New <br> Leads</h5>
                            <h3 class="p-0 client-status ms-auto">
                                {{ $totalClientInformation['newLeads']['old'] }}
                                @if ($totalClientInformation['newLeads']['new'] != 0)
                                    <sup>
                                        <span class="badge bg-primary text-white p-1 px-2">
                                            <small>{{ $totalClientInformation['newLeads']['new'] }} New</small>
                                        </span>
                                    </sup>
                                @endif
                            </h3>
                        </div>
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-0 text-muted client-status-detail">
                                <span @class(['me-2', 'text-success'])>
                                    <i @class(['bi', 'bi-arrow-up-short'])></i>
                                    {{ $totalClientInformation['newLeads']['percentage'] }}%
                                </span>
                                <span>Since before</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between text-info w-full d-flex flex-column cursor-pointer card-client"
                        data-f-date="all" data-f-client-type="potential">
                        <div class="d-flex">
                            <h5 class="p-0 m-0">Potential <br> Client</h5>
                            <h3 class="p-0 m-0 client-status ms-auto">{{ $totalClientInformation['potential']['old'] }}
                                @if ($totalClientInformation['potential']['new'] != 0)
                                    <sup>
                                        <span class="badge bg-primary text-white p-1 px-2">
                                            <small>{{ $totalClientInformation['potential']['new'] }} New</small>
                                        </span>
                                    </sup>
                                @endif
                            </h3>
                        </div>
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-0 text-muted client-status-detail">
                                <span @class(['me-2', 'text-success'])>
                                    <i @class(['bi', 'bi-arrow-up-short'])></i>
                                    {{ $totalClientInformation['potential']['percentage'] }}%
                                </span>
                                <span>Since before</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between text-success w-full d-flex flex-column cursor-pointer card-client"
                        data-f-date="all" data-f-client-type="existing-mentees">
                        <div class="d-flex">
                            <h5 class="p-0 m-0">Existing <br> Mentee</h5>
                            <h3 class="p-0 client-status ms-auto">
                                {{ $totalClientInformation['existingMentees']['old'] }}
                                @if ($totalClientInformation['existingMentees']['new'] != 0)
                                    <sup>
                                        <span class="badge bg-primary text-white p-1 px-2">
                                            <small>{{ $totalClientInformation['existingMentees']['new'] }} New</small>
                                        </span>
                                    </sup>
                                @endif
                            </h3>
                        </div>
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-0 text-muted client-status-detail">
                                <span @class(['me-2', 'text-success'])>
                                    <i @class(['bi', 'bi-arrow-up-short'])></i>
                                    {{ $totalClientInformation['existingMentees']['percentage'] }}%
                                </span>
                                <span>Since before</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between text-primary w-full d-flex flex-column cursor-pointer card-client"
                        data-f-date="all" data-f-client-type="existing-non-mentees">
                        <div class="d-flex">
                            <h5 class="p-0 m-0">Existing <br> Non-Mentee</h5>
                            <h3 class="p-0 client-status ms-auto">
                                {{ $totalClientInformation['existingNonMentees']['old'] }}
                                @if ($totalClientInformation['existingNonMentees']['new'] != 0)
                                    <sup>
                                        <span class="badge bg-primary text-white p-1 px-2">
                                            <small>{{ $totalClientInformation['existingNonMentees']['new'] }} New</small>
                                        </span>
                                    </sup>
                                @endif
                            </h3>
                        </div>
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-0 text-muted client-status-detail">
                                <span @class(['me-2', 'text-success'])>
                                    <i @class(['bi', 'bi-arrow-up-short'])></i>
                                    {{ $totalClientInformation['existingNonMentees']['percentage'] }}%
                                </span>
                                <span>Since before</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between w-full d-flex flex-column cursor-pointer card-client"
                        data-f-date="all" data-f-client-type="alumni-mentee">
                        <div class="d-flex">
                            <h5 class="p-0 m-0">Alumni <br> Mentee</h5>
                            <h3 class="p-0 ms-auto">
                                {{ $totalClientInformation['alumniMentees']['old'] }}
                                @if ($totalClientInformation['alumniMentees']['new'] != 0)
                                    <sup class="d-none">
                                        <span class="badge bg-primary text-white p-1 px-2">
                                            <small>{{ $totalClientInformation['alumniMentees']['new'] }} New</small>
                                        </span>
                                    </sup>
                                @endif
                            </h3>
                        </div>
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-0 text-muted">
                                <span @class(['invisible', 'me-2', 'text-success'])>
                                    <i @class(['bi', 'bi-arrow-up-short'])></i>
                                    {{ $totalClientInformation['alumniMentees']['percentage'] }}%
                                </span>
                                <span class="invisible">Since before</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between w-full d-flex flex-column cursor-pointer card-client"
                        data-f-date="all" data-f-client-type="alumni-non-mentee">
                        <div class="d-flex">
                            <h5 class="p-0 m-0">Alumni <br> Non-Mentee</h5>
                            <h3 class="p-0 ms-auto">
                                {{ $totalClientInformation['alumniNonMentees']['old'] }}
                                @if ($totalClientInformation['alumniNonMentees']['new'] != 0)
                                    <sup>
                                        <span class="badge bg-primary text-white p-1 px-2">
                                            <small>{{ $totalClientInformation['alumniNonMentees']['new'] }} New</small>
                                        </span>
                                    </sup>
                                @endif
                            </h3>
                        </div>
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-0 text-muted">
                                <span @class(['invisible', 'me-2', 'text-success'])>
                                    <i @class(['bi', 'bi-arrow-up-short'])></i>
                                    {{ $totalClientInformation['alumniNonMentees']['percentage'] }}%
                                </span>
                                <span class="invisible">Since before</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between w-full d-flex flex-column cursor-pointer card-client"
                        data-f-date="all" data-f-client-type="parent">
                        <div class="d-flex">
                            <h5 class="p-0 m-0">Parents <br> Total</h5>
                            <h3 class="p-0 client-status ms-auto">
                                {{ $totalClientInformation['parent']['old'] }}
                                @if ($totalClientInformation['parent']['new'] != 0)
                                    <sup>
                                        <span class="badge bg-primary text-white p-1 px-2">
                                            <small>{{ $totalClientInformation['parent']['new'] }} New</small>
                                        </span>
                                    </sup>
                                @endif
                            </h3>
                        </div>
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-0 text-muted client-status-detail">
                                <span @class(['me-2', 'text-success'])>
                                    <i @class(['bi', 'bi-arrow-up-short'])></i>
                                    {{ $totalClientInformation['parent']['percentage'] }}%
                                </span>
                                <span>Since before</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between w-full d-flex flex-column cursor-pointer card-client"
                        data-f-date="all" data-f-client-type="teacher-counselor">
                        <div class="d-flex">
                            <h5 class="p-0 m-0">Teacher/Counselor <br> Total</h5>
                            <h3 class="p-0 client-status ms-auto">
                                {{ $totalClientInformation['teacher_counselor']['old'] }}
                                @if ($totalClientInformation['teacher_counselor']['new'] != 0)
                                    <sup>
                                        <span class="badge bg-primary text-white p-1 px-2">
                                            <small>{{ $totalClientInformation['teacher_counselor']['new'] }}
                                                New</small>
                                        </span>
                                    </sup>
                                @endif
                            </h3>
                        </div>
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-0 text-muted client-status-detail">
                                <span @class(['me-2', 'text-success'])>
                                    <i @class(['bi', 'bi-arrow-up-short'])></i>
                                    {{ $totalClientInformation['teacher_counselor']['percentage'] }}%
                                </span>
                                <span>Since before</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail -->
<div class="modal modal-lg fade" id="list-detail-client" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">
                    <!-- title here -->
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-3">
                        {{-- <input type="month" name="" id="menteesBirthdayMonth" class="form-control form-control-sm"
                            value="{{ date('Y-m') }}"> --}}
                    </div>
                </div>
                <div class="overflow-auto" style="height: 400px">
                    <table class="table table-hover" id="listClientTable">
                        <thead class="text-center">
                            <tr class="text-white">
                                <th class='bg-secondary rounded border border-white'>No</th>
                                <th class='bg-secondary rounded border border-white'>Client's Name</th>
                                <th class='bg-secondary rounded border border-white'>Client's Mail</th>
                                <th class='bg-secondary rounded border border-white'>Client's Phone</th>
                                <th class='bg-secondary rounded border border-white'>Register Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- content here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Birthday -->
<div class="modal modal-lg fade" id="birthday" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Mentee's Birthday</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-3">
                        {{-- <input type="month" name="" id="menteesBirthdayMonth" class="form-control form-control-sm"
                            value="{{ date('Y-m') }}"> --}}
                    </div>
                </div>
                <div class="overflow-auto" style="height: 400px">
                    <table class="table table-striped table-hover" id="menteesBirthdayTable">
                        <thead class="text-center">
                            <tr class="text-white">
                                <th class='bg-secondary rounded border border-white'>No</th>
                                <th class='bg-secondary rounded border border-white'>Mentee's Name</th>
                                <th class='bg-secondary rounded border border-white'>Birthday</th>
                                <th class='bg-secondary rounded border border-white'>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menteesBirthday as $mentee)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mentee->full_name }}</td>
                                    <td>{{ date('D, d M Y', strtotime($mentee->dob)) }}</td>
                                    <td>{{ strip_tags($mentee->address) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Follow Up -->
<div class="modal modal-lg fade" id="follow_up" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Follow Up Reminder</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body">
                @foreach ($followUpReminder as $key => $detail)
                    <h6>
                        @php
                            $opener = '(';
                            $closer = ')';
                        @endphp
                        @switch(date('d', strtotime($key))-date('d'))
                            @case(0)
                                Today
                            @break

                            @case(1)
                                Tomorrow
                            @break

                            @case(2)
                                The day after tomorrow
                            @break

                            @default
                                @php
                                    $opener = null;
                                    $closer = null;
                                @endphp
                        @endswitch
                        {{ $opener . date('D, d M Y', strtotime($key)) . $closer }}
                    </h6>
                    <div class="overflow-auto mb-3" style="height: 150px">
                        <ul class="list-group">
                            @foreach ($detail as $info)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="">
                                        <p class="m-0 p-0 lh-1">{{ $info->clientProgram->client->full_name }}</p>
                                        <small
                                            class="m-0">{{ $info->clientProgram->program->program_name }}</small>
                                    </div>
                                    <div class="">
                                        <input class="form-check-input me-1" type="checkbox" value="1"
                                            @checked($info->status == 1) id="mark_{{ $loop->index }}"
                                            data-student="{{ $info->clientProgram->client->id }}"
                                            data-program="{{ $info->clientProgram->clientprog_id }}"
                                            data-followup="{{ $info->id }}"
                                            onchange="marked({{ $loop->index }})">
                                        <label class="form-check-label" for="mark_{{ $loop->index }}">Done</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <hr>
                @endforeach
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
                <form action="" method="POST" id="followUpForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" class="marked_id">
                    <textarea name="new_notes" id="" cols="30" rows="10"></textarea>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="cancelMarked()">Cancel</button>
                        <button type="submit" id="btn-submit-followup" class="btn btn-sm btn-primary"
                            onclick="tinyMCE.triggerSave(true,true);">Submit</button>
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
                <form action="" method="POST" id="cancelFollowUpForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" class="marked_id">
                    Are you sure, you want to cancel this follow up?
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="backMarked()">No</button>
                        <button type="submit" id="btn-cancel-followup" class="btn btn-sm btn-primary">Yes,
                            Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="//www.datejs.com/build/date.js" type="text/javascript"></script>
<script>
    $(".card-client").each(function() {
        $(this).click(function() {
            showLoading()

            let f_date = $(this).data('f-date')
            let f_type = $(this).data('f-client-type')

            let url = window.location.origin + '/api/get/client/' + f_date + '/type/' + f_type;

            axios.get(url)
                .then(function(response) {

                    
                    var obj = response.data;
                    console.log(obj)
                    $('#list-detail-client .modal-title').html(obj.title)
                    $('#listClientTable tbody').html(obj.html_ctx)
                    swal.close()

                    $('#list-detail-client').modal('show')

                }).catch(function(error) {
                    notification('error',
                        'There was an error while processing your request. Please try again or contact your administrator.'
                        );
                })
        })
    })

    function checkPeriod() {
        let period = $('#period').val()
        if (period == 'monthly') {
            $('#monthly').removeClass('d-none')
            checkMonthly()

            // new
            let month = $('#client_status_month').val()
            $(".card-client").data('f-date', month);
        } else {

            $(".card-client").data('f-date', 'all');
            $('#monthly').addClass('d-none')
            checkClientStatus()
        }
    }

    function checkMonthly() {
        let month = $('#client_status_month').val()
        checkClientStatus(month)
    }

    function checkClientStatus(month = false) {
        // Axios here... 
        showLoading()

        var today = new Date()

        if (!month)
            month = 'all'
        // month = moment(today).format('YYYY-MM')

        var url = window.location.origin + '/api/get/client-status/' + month
        $(".card-client").data('f-date', month);

        axios.get(url)
            .then(function(response) {
                console.log(response)
                var obj = response.data.data

                $(".client-status").each(function(index, value) {
                    var title = obj[index]['old']
                    if (obj[index]['new'] != 0) {
                        title += '<sup>' +
                            '<span class="badge bg-primary text-white p-1 px-2">' +
                            '<small>' + obj[index]['new'] + ' New</small>' +
                            '</span>' +
                            '</sup>'
                    }
                    $(this).html(title)

                })

                $(".client-status-detail").each(function(index) {
                    if (response.data.type == "all") {

                        var textStyling = 'text-success'
                        var icon = "bi-arrow-up-short"
                        var html = '<span class="me-2 ' + textStyling + '">' +
                            '<i class="bi ' + icon + '"></i>' +
                            obj[index]['percentage'] + '%' +
                            '</span><span>Since before</span>'

                    } else {

                        if (obj[index]['new'] > obj[index]['old'] && obj[index]['new'] != 0 && obj[index][
                                'old'
                            ] != 0) {

                            var textStyling = 'text-success'
                            var icon = "bi-arrow-up-short"

                        } else if (obj[index]['old'] == 0 && obj[index]['new'] != 0) {

                            var textStyling = 'text-success'
                            var icon = "bi-arrow-up-short"

                        } else if (obj[index]['old'] == 0 && obj[index]['new'] == 0) {

                            var textStyling = ''
                            var icon = ""

                        } else {

                            var textStyling = 'text-danger'
                            var icon = "bi-arrow-down-short"

                        }

                        var html = '<span class="me-2 ' + textStyling + '">' +
                            '<i class="bi ' + icon + '"></i>' +
                            obj[index]['percentage'] + '%' +
                            '</span><span>Since last month</span>'


                    }

                    $(this).html(html)
                })
                swal.close()

            }).catch(function(error) {
                swal.close()
                notification('error', 'Ooops! Something went wrong. Please try again.')
            })

        getFollowUpReminder(month)
        getMenteesBirthday(month)
    }

    function getFollowUpReminder(month) {

        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        var url = window.location.origin + '/api/get/follow-up-reminder/' + month

        axios.get(url)
            .then(function(response) {

                var obj = response.data.data
                if (obj.length == 0) {

                    $("#btn-follow-up").addClass('d-none');

                } else {

                    var total_followupReminder = Object.keys(obj.followUpReminder).length

                    $("#btn-follow-up").removeClass('d-none')
                    $("#btn-follow-up span").html(total_followupReminder)

                }
                $("#modal-body").html(obj.html_txt)

            }).catch(function(error) {

                notification('error', 'Ooops! Something went wrong. Please try again.')
            })
    }

    function getMenteesBirthday(month) {
        var today = new Date()

        if (!month || month == 'all')
            month = moment(today).format('YYYY-MM')

        var url = window.location.origin + '/api/get/mentee-birthday/' + month

        axios.get(url)
            .then(function(response) {

                var obj = response.data.data
                var total_menteesbirthday = Object.keys(obj).length

                $("#btn-mentees-birthday").removeClass('d-none')
                $("#btn-mentees-birthday span").html(total_menteesbirthday)

                var html = ""
                var no = 1;
                obj.forEach(function(item, index, arr) {
                    var dob_str = moment(item['dob']).format('ddd, DD MMM yyyy')
                    var address = item['address'] === null ? '' : item['address']

                    html += "<tr class='text-center'>" +
                        "<td>" + no++ + "</td>" +
                        "<td>" + item['full_name'] + "</td>" +
                        "<td>" + dob_str + "</td>" +
                        "<td>" + address + "</td>" +
                        "</tr>"
                })

                $("#menteesBirthdayTable tbody").html('');
                $("#menteesBirthdayTable tbody").append(html);

            }).catch(function(error) {

                notification('error', 'Ooops! Something went wrong. Please try again.')

            })

    }

    function marked(i) {
        $('.marked_id').val(i)
        var mark = $("#mark_" + i)

        if (mark.is(':checked')) {
            $('#follow_up_notes').modal('show')

            var student = mark.data('student')
            var program = mark.data('program')
            var followup = mark.data('followup')
            var link = 'client/student/' + student + '/program/' + program + '/followup/' + followup;

            $("#followUpForm").attr('action', link)

        } else {
            $('#cancel_follow_up_notes').modal('show')

            var student = mark.data('student')
            var program = mark.data('program')
            var followup = mark.data('followup')
            var link = 'client/student/' + student + '/program/' + program + '/followup/' + followup;

            $("#cancelFollowUpForm").attr('action', link)
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

    // function that change followup status to 1 
    $("#btn-submit-followup").click(function(e) {
        e.preventDefault()
        e.stopPropagation()

        var link = $('#followUpForm').attr('action')
        var data = $('#followUpForm').serialize()

        var obj = [{
            "mark": true
        }]

        axios.post(link, data + '&' + $.param(obj[0]))
            .then((response) => {
                Swal.close()
                notification('success', 'Follow-up has been marked as done')

                $('#follow_up_notes').modal('hide')

            }, (error) => {
                Swal.close()
                notification('error', 'Failed to mark follow-up')
            });
    })

    // function that change followup status to 0
    $("#btn-cancel-followup").click(function(e) {
        e.preventDefault()
        e.stopPropagation()

        var link = $('#cancelFollowUpForm').attr('action')
        var data = $('#cancelFollowUpForm').serialize()

        var obj = [{
            "mark": false
        }]

        axios.post(link, data + '&' + $.param(obj[0]))
            .then((response) => {
                Swal.close()
                notification('success', 'Follow-up has been marked as waiting')

                $('#cancel_follow_up_notes').modal('hide')

            }, (error) => {
                Swal.close()
                notification('error', 'Failed to mark follow-up')
            });
    })

    $("#menteesBirthdayMonth").on('change', function() {

        var date = $(this).val()

        axios.get('{{ url('api/mentee/birthday/') }}/' + date)
            .then((response) => {

                var data = response.data.data
                var html = ""
                var no = 1;
                data.forEach(function(item, index, arr) {
                    var dob_str = moment(item['dob']).format('ddd, DD MMM yyyy')

                    html += "<tr class='text-center'>" +
                        "<td>" + no++ + "</td>" +
                        "<td>" + item['full_name'] + "</td>" +
                        "<td>" + dob_str + "</td>" +
                        "<td>" + item['address'] + "</td>" +
                        "</tr>"
                })

                $("#menteesBirthdayTable tbody").html('');
                $("#menteesBirthdayTable tbody").append(html);

            }, (error) => {
                notification('error', 'Ooops! Something went wrong. Please try again.')
            })

    })

    $(document).on('click', '.popup-modal-detail-client', function () {
        var clientId = $(this).data('detail');
        var url = window.location.origin + '/client/student/' + clientId;
        window.open(url, '_blank');

    });

    function showLoading() {
        Swal.fire({
            width: 100,
            backdrop: '#4e4e4e7d',
            allowOutsideClick: false,
        })
        Swal.showLoading();
    }
</script>
