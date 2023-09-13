
<section id="client-event" class="dashboard-partnership d-none">
    <div class="card mb-3">
        <div class="card-body">
            <div class="row justify-content-end mb-2">
                <div class="col-md-2">
                    <select name="" id="qclient-event-year" class="select w-100">
                        <option value="current">Current Year</option>
                        <option value="last-3-year">The Last 3 Year</option>
                    </select>
                </div>
            </div>
            <div class="row d-flex align-items-stretch">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            Client Event Percentage
                        </div>
                        <div class="card-body overflow-auto" style="height: 300px">
                            <table class="table table-hover" id="client-event-percentage-tbl">
                                <thead>
                                    <tr>
                                        <th>Event Name</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($events as $event)
                                        <tr>
                                            <td wrap>{{ $event->event_title }}</td>
                                            <td class="text-end">
                                                {{ $event->participants != 0 && $event->event_target != 0 ? ($event->participants / $event->event_target) * 100 : 0 }}%
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">No Data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center">
                            <canvas id="client_event"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between">
                            <div class="" id="event_title">{{ count($events) > 0 ? $events[0]->event_title : null }}
                            </div>
                            <div class="text-end">Conversion Lead</div>
                        </div>
                        <div class="card-body client-event-lead">
                            <canvas id="client_event_lead" class="w-100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
    var client_event_chart_pct, event_lead_chart = null;

    $("#qclient-event-year").on('change', function() {
        var selected_year = $(this).val()
        var uuid = $('#cp_employee').val() == "all" ? null : $('#cp_employee').val()

        get_client_event(selected_year, uuid)

    });

    function get_client_event(year = null, user = null) {
        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/client-event/' + year + '/' + user

        axios.get(url)
            .then(function(response) {

                var obj = response.data.data

                $("#client-event-percentage-tbl tbody").html(obj.html_txt)

                client_event_chart_pct.data.labels = obj.ctx.labels
                client_event_chart_pct.data.datasets[0].data = obj.ctx.target
                client_event_chart_pct.data.datasets[1].data = obj.ctx.participants
                client_event_chart_pct.update();

                var lead_array = obj.lead.total.map(function (x) {
                    return parseInt(x);
                })

                event_lead_chart.data.labels = obj.lead.labels
                event_lead_chart.data.datasets[0].data = obj.lead.total
                event_lead_chart.update();
                swal.close()

            }).catch(function (error) {
                notification('error', error.message);
                swal.close()
            })
    }
</script>
<script>
    // Percentage 
    let lbl_client_event = [{
        formatter: (value, ctx) => {
            let datasets = ctx.chart.data.datasets;
            if (datasets.indexOf(ctx.dataset) === datasets.length - 1) {
                let sum = datasets[0].data.reduce((a, b) => a + b, 0);
                let percentage = Math.round((value / sum) * 100) + '%';
                return percentage;
            } else {
                return percentage;
            }
        },
        color: '#000',
        font: {
            size: 11
        },
        padding: {
            left: 8,
            right: 8,
            top: 3,
            bottom: 1
        },
        anchor: 'center',
        borderRadius: 10,
        backgroundColor: '#fff',
    }]

    // Client Event 
    const event = document.getElementById('client_event');

    const dataset_participants = new Array()
    const dataset_target = new Array()
    const dataset_events = new Array()
    @if (isset($events))
        @foreach ($events as $event)
            dataset_participants.push('{{ $event->participants }}')
            dataset_target.push('{{ $event->event_target == null ? 0 : $event->event_target }}')
            dataset_events.push('{{ $event->event_title }}')
        @endforeach
    @endif

    var client_event_chart_pct = new Chart(event, {
        data: {
            labels: dataset_events,
            datasets: [{
                    type: 'bar',
                    label: 'Join Event',
                    data: dataset_participants,
                    borderWidth: 1,
                    datalabels: {
                        color: '#000',
                        backgroundColor: '#fff',
                        borderRadius: 40,
                        padding: 5,
                    }
                },
                {
                    type: 'line',
                    label: 'Target Participants',
                    data: dataset_target,
                    borderWidth: 1,
                    datalabels: {
                        color: '#fff',
                        backgroundColor: '#2f6ba8',
                        borderRadius: 40,
                        padding: 5,
                    }
                }
            ]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
                },
            },
            indexAxis: 'y',
            onClick: (e, activeEls) => {
                let datasetIndex = activeEls[0].datasetIndex;
                let dataIndex = activeEls[0].index;
                let datasetLabel = e.chart.data.datasets[datasetIndex].label;
                let value = e.chart.data.datasets[datasetIndex].data[dataIndex];
                let label = e.chart.data.labels[dataIndex];
                $('#event_title').html(label)
                // get_event_lead(label)
            }
        }
    });


    // Concersion Lead 

    $('.client-event-lead canvas').remove()
    $('.client-event-lead').append('<canvas id="client_event_lead"></canvas>')

    const dataset_labels = new Array();
    const dataset_info = new Array();
    @if (isset($conversion_lead_of_event))
        @foreach ($conversion_lead_of_event->pluck('conversion_lead')->toArray() as $key => $value)
            dataset_labels.push('{{ $value }}')
        @endforeach

    @foreach ($conversion_lead_of_event->pluck('count_conversionLead')->toArray() as $key => $value)
        dataset_info.push({{ $value == null || $value == '' ? 0 : $value }})
    @endforeach
    @endif

    const event_lead = document.getElementById('client_event_lead');
    var event_lead_chart = new Chart(event_lead, {
        type: 'pie',
        data: {
            labels: dataset_labels,
            datasets: [{
                label: 'Participants',
                data: dataset_info,
                borderWidth: 1,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: lbl_client_event[0],
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 15,
                    }
                }
            },
            onClick: (e, activeEls) => {

            }
        }
    });

</script>
@endpush
