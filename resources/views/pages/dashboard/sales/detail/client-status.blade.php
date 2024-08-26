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
            <div class="col-md-2 text-md-end text-center">
                <a href="{{ url('api/export/client') }}" class="btn btn-sm btn-outline-info btn-export"
                    style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"><i
                        class="bi bi-download"></i>
                </a>
            </div>
        </div>
        <div class="row g-3 align-items-stretch">
            <div @class([
                'col-md-9' => $isSuperAdmin || $isSalesAdmin,
            ])>
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
                                    <h3 class="p-0 m-0 client-status ms-auto">
                                        {{ $totalClientInformation['potential']['old'] }}
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
                                                    <small>{{ $totalClientInformation['existingMentees']['new'] }}
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
                                                    <small>{{ $totalClientInformation['existingNonMentees']['new'] }}
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
                                                    <small>{{ $totalClientInformation['alumniMentees']['new'] }}
                                                        New</small>
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
                                                    <small>{{ $totalClientInformation['alumniNonMentees']['new'] }}
                                                        New</small>
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
                    @if ($isSuperAdmin || $isSalesAdmin)
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
                                                        <small>{{ $totalClientInformation['parent']['new'] }}
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
                                        <h5 class="p-0 m-0">Teacher <br> Total</h5>
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
                    @endif
                </div>
            </div>
            @if ($isSuperAdmin || $isSalesAdmin)
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center h-100">
                                <h5 class="p-0 m-0">Raw Data Client <br> Total</h5>
                                <h3 class="p-0 client-status ms-auto">
                                    {{ array_sum($totalClientInformation['raw']) }}
                                </h3>
                            </div>
                        </div>
                        <div class="card-footer bg-primary">
                            <div class="row row-cols-3">
                                <div class="col text-center text-white">
                                    <h5 class="m-0">{{ $totalClientInformation['raw']['student'] }}</h5>
                                    <small>Students</small>
                                </div>
                                <div class="col text-center text-white">
                                    <h5 class="m-0">{{ $totalClientInformation['raw']['parent'] }}</h5>
                                    <small>Parents</small>
                                </div>
                                <div class="col text-center text-white">
                                    <h5 class="m-0">{{ $totalClientInformation['raw']['teacher'] }}</h5>
                                    <small>Teachers</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex align-items-center h-100">
                                <h5 class="p-0 m-0">Inactive Client <br> Total</h5>
                                <h3 class="p-0 client-status ms-auto">
                                    {{ array_sum($totalClientInformation['inactive']) }}
                                </h3>
                            </div>
                        </div>
                        <div class="card-footer bg-danger">
                            <div class="row row-cols-3">
                                <div class="col text-center text-white">
                                    <h5 class="m-0">{{ $totalClientInformation['inactive']['student'] }}</h5>
                                    <small>Students</small>
                                </div>
                                <div class="col text-center text-white">
                                    <h5 class="m-0">{{ $totalClientInformation['inactive']['parent'] }}</h5>
                                    <small>Parents</small>
                                </div>
                                <div class="col text-center text-white">
                                    <h5 class="m-0">{{ $totalClientInformation['inactive']['teacher'] }}</h5>
                                    <small>Teachers</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
                                <th class='bg-secondary rounded border border-white'>Client's PIC</th>
                                <th class='bg-secondary rounded border border-white'>Client's Mail</th>
                                <th class='bg-secondary rounded border border-white'>Client's Phone</th>
                                <th class='bg-secondary rounded border border-white'>Graduation Year</th>
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


@push('scripts')
    <script src="{{ asset('js/date.js') }}" type="text/javascript"></script>
    <script>
        $(".card-client").each(function() {
            $(this).click(function() {
                showLoading()

                let f_date = $(this).data('f-date')
                let f_type = $(this).data('f-client-type')

                let url = window.location.origin + '/api/get/client/' + f_date + '/type/' + f_type;
                const bearer_token = `Bearer {{ Session::get('access_token') }}`;

                axios.get(url, {
                    headers: {
                        Authorization: bearer_token
                    }
                })
                    .then(function(response) {


                        var obj = response.data;
                        
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
        }

        $(document).on('click', '.popup-modal-detail-client', function() {
            var clientId = $(this).data('detail');
            var url = window.location.origin + '/client/student/' + clientId;
            window.open(url, '_blank');

        });
    </script>
@endpush
