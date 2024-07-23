<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h5 class="text-success">
                  <i class="bi bi-check me-1"></i>  Successful Program
                </h5>
            </div>
            <div class="col-md-8 justify-content-end">
                <div class="row justify-content-end g-1">
                    <div class="col-md-6">
                        <select name="program-name" onchange="checkLeadDigital()" id="prog_id_digital" class="select w-100">
                            <option value=""></option>
                            @foreach ($programsDigital as $program)
                                <option value="{{$program->prog_id}}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="month" onchange="checkLeadDigital()" value="{{ date('Y-m') }}" name="month-year" id="month_year_digital" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>
        <div class="row align-items-stretch">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body overflow-auto" style="height: 340px">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th style="font-size: 13px">No</th>
                                    <th style="font-size: 13px">Full Name</th>
                                    <th style="font-size: 13px">Lead Source</th>
                                    <th style="font-size: 13px">Conversion Lead</th>
                                    <th style="font-size: 13px">Program Name</th>
                                    {{-- <th style="font-size: 13px">Follow Up Time</th> --}}
                                    <th style="font-size: 13px">Conversion Time</th>
                                </tr>
                            </thead>
                            <tbody id="t-body-leads-digital">
                                @foreach ($dataLead as $data)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$data->client->full_name}}</td>
                                        <td>{{(isset($data->client->lead_source) ? $data->client->lead_source : '-')}}</td>
                                        <td>{{(isset($data->conversion_lead) ? $data->conversion_lead : '-')}}</td>
                                        <td>{{$data->program->program_name}}</td>
                                        <td>{{$data->conversion_time}} Days</td>
                                        {{-- <td>{{$data->followup_time}} Days</td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-between align-items-center w-100">
                        {{-- <div class="w-50">
                            <small>Average of Follow Up</small>
                            <h5 id="avg-follow-up"><i class="bi bi-calendar me-1"></i> {{ $dataLead->count() > 0 ? $dataLead->avg('followup_time') : '-' }} Days</h5>
                        </div> --}}
                        <div class="w-100 text-end">
                            <small>Average of Conversion</small>
                            <h5 id="avg-conversion"><i class="bi bi-calendar me-1"></i> {{ $dataLead->count() > 0 ? round($dataLead->avg('conversion_time')) : '-' }} Days</h5>
                        </div>
                    </div>
                </div>
                {{-- <div class="card">
                    <div class="card-body">
                        <canvas id="digitalLeadSource"></canvas>
                    </div>
                </div> --}}
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <b class="text-center border-1">Lead Source</b>
                                <hr>
                                <ul class="list-group list-group-flush overflow-auto pe-3" style="height: 300px" id="list-lead-source">
                                    @foreach ($leadsDigital->sortByDesc('count') as $lead)
                                    @php
                                        $lead_id = $lead['lead_id'];
                                    @endphp
                                    <li
                                        class="d-flex align-items-center justify-content-between pb-1 mb-1 border-bottom border-secondary cursor-pointer"  onclick="checkLeadSourceDetail('{{$lead_id}}')">
                                        <small>
                                            {{ $lead['lead_name'] }}
                                        </small>
                                        <div class="d-flex justify-content-center align-items-center rounded-circle border border-primary"
                                            style="width: 25px; height:25px;">
                                            {{  $lead['count'] }}
                                        </div>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6 text-center">
                                <b class="text-center border-1">Conversion Lead</b>
                                <hr>
                                <ul class="list-group list-group-flush overflow-auto pe-3" style="height: 300px" id="list-conversion-lead">
                                    @foreach ($leadsAllDepart->sortByDesc('count') as $lead)
                                            @php
                                                $lead_id = $lead['lead_id'];
                                            @endphp
                                        <li
                                            class="d-flex align-items-center justify-content-between pb-1 mb-1 border-bottom border-secondary cursor-pointer" onclick="checkConversionLeadDetail('{{$lead_id}}')">
                                            <small>
                                                {{ $lead['lead_name'] }}
                                            </small>
                                                <div class="d-flex justify-content-center align-items-center rounded-circle border border-primary"
                                                    style="width: 25px; height:25px;">
                                                    {{  $lead['count'] }}
                                                </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal modal-lg fade" id="modalLeadDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="titleLeadDetail">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body overflow-auto" style="max-height: 300px" id="leadContentModal">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr id="thead-digital-lead">
                            
                        </tr>
                    </thead>
                    <tbody id="tbody-digital-lead">
                        {{-- @for ($i = 0; $i < 20; $i++)
                            <tr>
                                <td>No</td>
                                <td>Full Name</td>
                                <td>Parents Name</td>
                                <td>School Name</td>
                                <td>Graduation Year</td>
                                <td>Lead Source</td>
                            </tr>
                        @endfor --}}
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const digitalLeadSource = document.getElementById('digitalLeadSource');
    const theadLead = "<th>No</th>" +
                    "<th>Full Name</th>" +
                    "<th>Parents Name</th>" +
                    "<th>School Name</th>" +
                    "<th>Graduation Year</th>" +
                    "<th>Lead Source</th>";

    new Chart(digitalLeadSource, {
        type: 'bar',
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function checkLeadSourceDetail(lead){
        var month = $('#month_year_digital').val()
        var prog_id = $('#prog_id_digital').val()

        Swal.showLoading()
        axios.get('{{ url('api/digital/detail') }}/' + month + '/lead-source/' + lead + '/' + prog_id) 
            .then((response) => {
                var result = response.data

                $('#tbody-digital-lead').html(result.html_ctx)

                swal.close()
            }, (error) => {
                console.log(error)
                swal.close()
            })


        $('#modalLeadDetail').modal('show') 
        $('#titleLeadDetail').html('Lead Source') 
        var thead = $("#thead-digital-lead");
        thead.empty()
        thead.html(theadLead)
        // thead.append('<th>Test</th>')
    }

    function checkConversionLeadDetail(lead){
        var month = $('#month_year_digital').val()
        var prog_id = $('#prog_id_digital').val()

        Swal.showLoading()
        axios.get('{{ url('api/digital/detail') }}/' + month + '/conversion-lead/' + lead + '/' + prog_id)
            .then((response) => {
                var result = response.data
                console.log(result)
                $('#tbody-digital-lead').html(result.html_ctx)

                swal.close()
            }, (error) => {
                console.log(error)
                swal.close()
            })

        $('#modalLeadDetail').modal('show') 
        $('#titleLeadDetail').html('Conversion Lead') 
        var thead = $("#thead-digital-lead");
        thead.empty()
        thead.html(theadLead)
        thead.append('<th>Conversion Lead</th>')
        thead.append('<th>Program Name</th>')
    }

    function checkLeadDigital(){
        var month = $('#month_year_digital').val()
        var prog_id = $('#prog_id_digital').val()
        Swal.showLoading()
        axios.get('{{ url('api/digital/leads') }}/' + month + '/' + prog_id)
            .then((response) => {
                var result = response.data.data
                // console.log(result);
                var avgFollowUpTime = 0;
                var totalFollowUpTime = 0;
                var avgConversionTime = 0;
                var totalConversionTime = 0;
                var html = '';
                var icon = '<i class="bi bi-calendar me-1"></i>';
                var i = 1;
                var count = 0;

                $('#t-body-leads-digital').empty()
                html = result.htmlDataLead;
                $('#t-body-leads-digital').append(html)

                avgConversionTime = result.totalConversionTime;

                // $('#avg-follow-up').html(icon + Math.round(avgFollowUpTime) + ' Days');
                $('#avg-conversion').html(icon + Math.round(avgConversionTime) + ' Days');
    
                // Lead Source
                html = '';
                $('#list-lead-source').empty()
                result.leadsDigital.forEach(function (item, index) {
                    html = `<li class="d-flex align-items-center justify-content-between pb-1 mb-1 border-bottom border-secondary cursor-pointer" onclick="checkLeadSourceDetail('${item.lead_id}')">`;
                    html += '<small>';
                    html += item.lead_name;
                    html += '</small>';
                    html += ' <div class="d-flex justify-content-center align-items-center rounded-circle border border-primary" style="width: 25px; height:25px;">';
                    html += item.count;
                    html += '</div>';
                    html += '</li>';

                    $('#list-lead-source').append(html)
                })

                // Conversion Lead
                html = '';
                $('#list-conversion-lead').empty()
                result.leadsAllDepart.forEach(function (item, index) {
                    html = `<li class="d-flex align-items-center justify-content-between pb-1 mb-1 border-bottom border-secondary cursor-pointer" onclick="checkConversionLeadDetail('${item.lead_id}')">`;
                    html += '<small>';
                    html += item.lead_name;
                    html += '</small>';
                    html += ' <div class="d-flex justify-content-center align-items-center rounded-circle border border-primary" style="width: 25px; height:25px;">';
                    html += item.count;
                    html += '</div>';
                    html += '</li>';

                    $('#list-conversion-lead').append(html)
                })

                swal.close()
            }, (error) => {
                console.log(error)
                swal.close()
            })

    }
</script>
@endpush