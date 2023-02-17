<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end mb-2">
            <div class="col-md-2">
                <select name="" id="qclient-event-year" class="select w-100">
                    <option value="{{ date('Y') }}">Current Year</option>
                    <option value="{{ date('Y') - 3 }}">The Last 3 Year</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Client Event Percentage
                    </div>
                    <div class="card-body overflow-auto" style="height: 300px">
                        <table class="table table-hover">
                            <tr>
                                <th>Event Name</th>
                                <th class="text-end">Percentage</th>
                            </tr>
                            @forelse ($events as $event)
                                <tr>
                                    <td>{{ $event->event_title }}</td>
                                    <td class="text-end">{{ $event->participants != 0 && $event->event_target != null ? ($event->participants/$event->event_target)*100 : 0 }}%</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2">No Data</td>
                                </tr>

                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <canvas id="client_event"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="" id="event_title">{{ count($events) > 0 ? $events[0]->event_title : null }}</div>
                        <div class="">Conversion Lead</div>
                    </div>
                    <div class="card-body client-event-lead">
                        <canvas id="client_event_lead"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

    new Chart(event, {
        data: {
            labels: dataset_events,
            datasets: [{
                type: 'line',
                label: 'Target Participants',
                data: dataset_target,
                borderWidth: 6,
                datalabels: {
                    color: '#fff',
                    backgroundColor: '#2f6ba8',
                    borderRadius: 40,
                    padding: 5,
                }
            }, {
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
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            indexAxis: 'y',
            onClick: (e, activeEls) => {
                let datasetIndex = activeEls[0].datasetIndex;
                let dataIndex = activeEls[0].index;
                let datasetLabel = e.chart.data.datasets[datasetIndex].label;
                let value = e.chart.data.datasets[datasetIndex].data[dataIndex];
                let label = e.chart.data.labels[dataIndex];
                $('#event_title').html(label)
                get_event_lead(label)
            }
        }
    });


    // Concersion Lead 
    function get_event_lead(event_name) {
        $('.client-event-lead canvas').remove()
        $('.client-event-lead').append('<canvas id="client_event_lead"></canvas>')

        const dataset_labels = new Array();
        const dataset_info = new Array();
        @if (isset($conversion_lead_of_event))
        @foreach ($conversion_lead_of_event->pluck('conversion_lead')->toArray() as $key => $value)
            dataset_labels.push('{{ $value }}')
        @endforeach

        @foreach ($conversion_lead_of_event->pluck('count_conversionLead')->toArray() as $key => $value)
            dataset_info.push('{{ $value == null || $value == '' ? 0 : $value }}')
        @endforeach
        @endif

        const event_lead = document.getElementById('client_event_lead');
        new Chart(event_lead, {
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
    }

    get_event_lead('event_a');

    $("#qclient-event-year").on('change', function () {
        if ($(this).val() != "all" && $(this).val() != null) {

                let link = window.location.origin + "/dashboard"
                var queryString = window.location.search
                
                const urlParams = new URLSearchParams(queryString);
                if (urlParams.has('qyear')) {
                    urlParams.set('qyear', $(this).val())
                }
                urlParams.append('qyear', $(this).val())
                location.href = link + "?" + urlParams
                return
            }

            let url = window.location.href
            let urlObj = new URL(url)
            urlObj.search = ''
            const result = urlObj.toString()
            window.location = result
    })
</script>