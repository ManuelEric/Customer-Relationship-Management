<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end mb-2">
            <div class="col-md-2">
                <input type="month" class="form-control form-control-sm qdate" value="{{ Request::get('qdate') ?? date('Y-m') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <canvas id="student_target"></canvas>
                    </div>
                </div>
                <canvas id="amount_target"></canvas>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body overflow-auto" style="height: 400px">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="sales-target-detail">
                                <thead>
                                    <tr class="text-center">
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2">Program Name</th>
                                        <th colspan="2">Target</th>
                                        <th colspan="2">Actual Sales</th>
                                        <th colspan="2">Sales Percentage</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>Students</th>
                                        <th>Total Amount</th>
                                        <th>Students</th>
                                        <th>Total Amount</th>
                                        <th>Students</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesDetail as $detail)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->prog_id }}</td>
                                        <td class="text-start">{{ $detail->program_name_sales }}</td>
                                        <td>{{ $detail->total_target_participant ??= 0 }}</td>
                                        <td>{{ number_format($detail->total_target_amount,'2',',','.') }}</td>
                                        <td>{{ $detail->total_actual_participant }}</td>
                                        <td>{{ number_format($detail->total_actual_amount,'2',',','.') }}</td>
                                        <td>{{ $detail->total_target_participant != 0 ? round(($detail->total_actual_participant/$detail->total_target_participant) * 100, 2) : 0 }}%</td>
                                        <td>{{ $detail->total_target_amount != 0 ? ($detail->total_actual_amount/$detail->total_target_amount) * 100 : 0 }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    var target_revenue_chart, target_people_chart = null;

    // sales target context
    function get_all_program(month = null, user = null)
    {
        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/all-program/target/' + month + '/' + user

        axios.get(url)
           .then(function (response) {
            
                var obj = response.data.data
                target_people_chart.data.datasets[0].data = obj.dataset.participant
                target_people_chart.data.datasets[1].data = obj.dataset.participant
                target_people_chart.update();
                
                target_revenue_chart.data.datasets[0].data = obj.dataset.revenue
                target_revenue_chart.data.datasets[1].data = obj.dataset.revenue
                target_revenue_chart.update();

                $("#sales-target-detail tbody").html(obj.html_txt)

            }).catch(function (error) {
                notification('error', 'Ooops! Something went wrong. Please try again.')
            })
    }
</script>
<script>
    const st = document.getElementById('student_target');
    const sa = document.getElementById('amount_target');

    const dataset_sales_target = new Array();
    const dataset_sales_actual = new Array();

    dataset_sales_target.push({{ $salesTarget->total_participant ?? 0 }})
    dataset_sales_target.push({{ $salesActual->total_participant ?? 0 }})
    
    dataset_sales_actual.push({{ $salesTarget->total_target ?? 0 }})
    dataset_sales_actual.push({{ $salesActual->total_target ?? 0 }})

    var target_people_chart = new Chart(st, {
        data: {
            labels: ['Target', 'Actual'],
            datasets: [{
                    type: 'line',
                    label: 'Target',
                    data: dataset_sales_target,
                    borderWidth: 4,
                    datalabels: {
                        color: '#fff',
                        backgroundColor: '#2f6ba8',
                        borderRadius: 40,
                        padding: 5,
                    }
                },
                {
                    type: 'bar',
                    label: 'Actual',
                    data: dataset_sales_target,
                    borderWidth: 1,
                    borderRadius: 5,
                    datalabels: {
                        color: '#fff',
                        backgroundColor: '#192e54',
                        borderRadius: 40,
                        padding: 5,
                        anchor: 'end',
                    }
                }
            ]
        },
        plugins: [ChartDataLabels],
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'ALL Program'
                }
            }
        },
    });

    var target_revenue_chart = new Chart(sa, {
        data: {
            labels: ['Target', 'Actual'],
            datasets: [{
                    type: 'line',
                    label: 'Target',
                    data: dataset_sales_actual,
                    borderWidth: 4,
                    datalabels: {
                        color: '#fff',
                        backgroundColor: '#2f6ba8',
                        borderRadius: 40,
                        padding: 5,
                    }
                },
                {
                    type: 'bar',
                    label: 'Actual',
                    data: dataset_sales_actual,
                    borderWidth: 1,
                    borderRadius: 5,
                    datalabels: {
                        color: '#fff',
                        backgroundColor: '#192e54',
                        borderRadius: 40,
                        padding: 5,
                        anchor: 'end',
                    }
                }
            ]
        },
        plugins: [ChartDataLabels],
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'ALL Program'
                }
            }
        },
    });
</script>
