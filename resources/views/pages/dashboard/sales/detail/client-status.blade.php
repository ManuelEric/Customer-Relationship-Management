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
                @if (isset($followUpReminder) && count($followUpReminder) > 0)
                <button type="button" class="btn btn-sm btn-info position-relative pe-3" style="font-size: 11px"
                    data-bs-toggle="modal" data-bs-target="#follow_up">
                    Follow Up Reminder
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 11px">
                        {{ count($followUpReminder) }}
                    </span>
                </button>
                @endif

                <button type="button" class="btn btn-sm btn-info position-relative ms-3 pe-3" style="font-size: 11px"
                    data-bs-toggle="modal" data-bs-target="#birthday">
                    Mentee's Birthday
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 11px">
                        {{ $menteesBirthday->count() }}
                    </span>
                </button>
            </div>

        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center text-warning">
                        <h5 class="p-0 m-0">Prospective <br> Client</h5>
                        <h3 class="p-0 m-0">{{ $totalClientInformation['prospective'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center text-info">
                        <h5 class="p-0 m-0">Potential <br> Client</h5>
                        <h3 class="p-0 m-0">{{ $totalClientInformation['potential'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center text-success">
                        <h5 class="p-0 m-0">Current <br> Client</h5>
                        <h3 class="p-0 m-0">{{ $totalClientInformation['current'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center text-primary">
                        <h5 class="p-0 m-0">Completed <br> Client</h5>
                        <h3 class="p-0 m-0">{{ $totalClientInformation['completed'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="p-0 m-0">Mentee <br> Total</h5>
                        <h3 class="p-0 m-0">{{ $totalClientInformation['mentee'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="p-0 m-0">Alumni <br> Total</h5>
                        <h3 class="p-0 m-0">{{ $totalClientInformation['alumni'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="p-0 m-0">Parents <br> Total</h5>
                        <h3 class="p-0 m-0">{{ $totalClientInformation['parent'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="p-0 m-0">Teacher/Counselor <br> Total</h5>
                        <h3 class="p-0 m-0">{{ $totalClientInformation['teacher_counselor'] }}</h3>
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
                        <input type="month" name="" id="menteesBirthdayMonth" class="form-control form-control-sm"
                            value="{{ date('Y-m') }}">
                    </div>
                </div>
                <div class="overflow-auto" style="height: 400px">
                    <table class="table table-bordered table-hover" id="menteesBirthdayTable">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Mentee's Name</th>
                                <th>Birthday</th>
                                <th>Address</th>
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
<div class="modal modal-lg fade" id="follow_up" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Follow Up Reminder</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                @foreach ($followUpReminder as $key => $detail)
                <h6>
                    @php
                        $opener = "(";
                        $closer = ")";
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
                    {{ $opener. date('D, d M Y', strtotime($key)). $closer }}
                </h6>
                <div class="overflow-auto mb-3" style="height: 150px">
                    <ul class="list-group">
                        @foreach($detail as $info)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="">
                                    <p class="m-0 p-0 lh-1">{{ $info->clientProgram->client->full_name }}</p>
                                    <small class="m-0">{{ $info->clientProgram->program_name }}</small>
                                </div>
                                <div class="">
                                    <input class="form-check-input me-1" type="checkbox" value="1" @checked($info->status == 1)
                                        id="mark_{{ $loop->index }}" 
                                            data-student="{{ $info->clientProgram->client->id }}" 
                                            data-program="{{ $info->clientProgram->clientprog_id }}"
                                            data-followup="{{ $info->id }}"
                                            onchange="marked({{ $loop->index }})">
                                    <label class="form-check-label" for="mark_{{ $loop->index }}">Done</label>
                                </div>
                            </li>
                        @endforeach
                        {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="">
                                <p class="m-0 p-0 lh-1">Mentees Name</p>
                                <small class="m-0">Program Name</small>
                            </div>
                            <div class="">
                                <input class="form-check-input me-1" type="checkbox" value="1"
                                    id="mark_{{ $i }}" onchange="marked({{ $i }})" checked>
                                <label class="form-check-label" for="mark_{{ $i }}">Done</label>
                            </div>
                        </li> --}}
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
                        <button type="submit" class="btn btn-sm btn-primary" onclick="tinyMCE.triggerSave(true,true);">Submit</button>
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
                        <button type="submit" class="btn btn-sm btn-primary">Yes, Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="http://www.datejs.com/build/date.js" type="text/javascript"></script>
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
        var mark = $("#mark_" + i)

        if (mark.is(':checked')) {
            $('#follow_up_notes').modal('show')

            var student = mark.data('student')
            var program = mark.data('program')
            var followup = mark.data('followup')
            var link = 'client/student/'+ student +'/program/'+ program +'/followup/'+ followup;

            $("#followUpForm").attr('action', link)

        } else {
            $('#cancel_follow_up_notes').modal('show')

            var student = mark.data('student')
            var program = mark.data('program')
            var followup = mark.data('followup')
            var link = 'client/student/'+ student +'/program/'+ program +'/followup/'+ followup;

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

    $("#followUpForm").submit(function(e) {
        e.preventDefault()
        e.stopPropagation()

        var link = $(this).attr('action')
        var data = $(this).serialize()

        var obj = [{ "mark" : true }]

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

    $("#cancelFollowUpForm").submit(function(e) {
        e.preventDefault()
        e.stopPropagation()

        var link = $(this).attr('action')
        var data = $(this).serialize()

        var obj = [{ "mark" : false }]

        axios.post(link, data + '&' + $.param(obj[0]))
            .then((response) => {
                Swal.close()
                notification('success', 'Follow-up has been marked as waiting')
                
                $('#follow_up_notes').modal('hide')

            }, (error) => {
                Swal.close()
                notification('error', 'Failed to mark follow-up')
            });
    })

    $("#menteesBirthdayMonth").on('change', function() {

        var date = $(this).val()
        var month = date.split('-')[1]
        
        axios.get('{{ url("api/mentee/birthday/") }}/' + month)
            .then((response) => {
                var data = response.data.data
                var html = ""
                var no = 1;
                data.forEach(function(item, index, arr) {
                    var dob = new Date(item['dob']);
                    var dob_str = dob.toString("ddd, dd MMMM yyyy")

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
                console.log(error)
            })

    })
</script>
