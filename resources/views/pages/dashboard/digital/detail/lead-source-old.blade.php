<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="text-success">
                  <i class="bi bi-check me-1"></i>  Successful Program
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <b class="text-center border-1">Lead Source</b>
                        <hr>
                        <ul class="list-group list-group-flush overflow-auto pe-3" style="height: 300px">
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
                      <ul class="list-group list-group-flush overflow-auto pe-3" style="height: 300px">
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
        </div>
    </div>
    <div class="col-md-5">
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
                            <th style="font-size: 13px">Conversion Time</th>
                            <th style="font-size: 13px">Follow Up Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataLead as $data)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$data->fullname}}</td>
                                <td>{{$data->lead_source}}</td>
                                <td>{{$data->conversion_lead}}</td>
                                <td>{{$data->program_name}}</td>
                                <td>{{$data->conversion_time}} Days</td>
                                <td>{{$data->followup_time}} Days</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-between align-items-center w-100">
                <div class="w-50">
                    <small>Average of Follow Up</small>
                    <h5><i class="bi bi-calendar me-1"></i> {{ $dataLead->count() > 0 ? $dataLead->avg('followup_time') : '-' }} Days</h5>
                </div>
                <div class="w-50 text-end">
                    <small>Average of Conversion</small>
                    <h5><i class="bi bi-calendar me-1"></i> {{ $dataLead->count() > 0 ? $dataLead->avg('conversion_time') : '-' }} Days</h5>
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

<script>
    // const digitalLeadSource = document.getElementById('digitalLeadSource');
    // const theadLead = "<th>No</th>" +
    //                 "<th>Full Name</th>" +
    //                 "<th>Parents Name</th>" +
    //                 "<th>School Name</th>" +
    //                 "<th>Graduation Year</th>" +
    //                 "<th>Lead Source</th>";

    // new Chart(digitalLeadSource, {
    //     type: 'bar',
    //     data: {
    //         labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    //         datasets: [{
    //             label: '# of Votes',
    //             data: [12, 19, 3, 5, 2, 3],
    //             borderWidth: 1
    //         }]
    //     },
    //     options: {
    //         scales: {
    //             y: {
    //                 beginAtZero: true
    //             }
    //         }
    //     }
    // });

    function checkLeadSourceDetail(lead){
        Swal.showLoading()
        axios.get('{{ url('api/digital/detail/') }}/' + lead + '/lead-source')
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
        Swal.showLoading()
        axios.get('{{ url('api/digital/detail/') }}/' + lead + '/conversion-lead')
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

</script>
