<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end mb-2">
            <div class="col-md-2">
                <select name="" id="" class="select w-100">
                    <option value="Current Year">Current Year</option>
                    <option value="The Last 3 Year">The Last 3 Year</option>
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
                            @for ($i = 0; $i < 20; $i++)
                                <tr>
                                    <td>Event Name ADADDADA {{ $i }}</td>
                                    <td class="text-end">20%</td>
                                </tr>
                            @endfor
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
                        <div class="" id="event_title">Event A</div>
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

    new Chart(event, {
        data: {
            labels: ['Event A', 'Event B', 'Event C', 'Event D'],
            datasets: [{
                type: 'line',
                label: 'Target Participants',
                data: [12, 19, 20, 35],
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
                data: [15, 25, 31, 20],
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

        const event_lead = document.getElementById('client_event_lead');
        new Chart(event_lead, {
            type: 'pie',
            data: {
                labels: ['Whatsapp Blass', 'Website', 'Newsletter', 'Instagram'],
                datasets: [{
                    label: 'Target Participants',
                    data: [12, 19, 20, 35],
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
</script>
