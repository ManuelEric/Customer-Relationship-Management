<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path
            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
    </symbol>
    <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
        <path
            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
    </symbol>
    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path
            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
    </symbol>
</svg>
<div class="card mb-3">
    <div class="card-body">
        <div class="row px-3 alert-container">

        </div>
        <div class="row justify-content-end mb-2">
            <div class="col-md-2">
                <input type="month" class="form-control form-control-sm qdate"
                    value="{{ Request::get('qdate') ?? date('Y-m') }}">
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
                                    <tr class="text-center text-white">
                                        <th rowspan="2" class="bg-secondary rounded border-1 border-white">No</th>
                                        <th rowspan="2" class="bg-secondary rounded border-1 border-white">ID</th>
                                        <th rowspan="2" class="bg-secondary rounded border-1 border-white">Program Name</th>
                                        <th colspan="2" class="bg-secondary rounded border-1 border-white">Target</th>
                                        <th colspan="2" class="bg-secondary rounded border-1 border-white">Actual Sales</th>
                                        <th colspan="2" class="bg-secondary rounded border-1 border-white">Sales Percentage</th>
                                    </tr>
                                    <tr class="text-center text-white">
                                        <th class="bg-secondary rounded border-1 border-white">Students</th>
                                        <th class="bg-secondary rounded border-1 border-white">Total Amount</th>
                                        <th class="bg-secondary rounded border-1 border-white">Students</th>
                                        <th class="bg-secondary rounded border-1 border-white">Total Amount</th>
                                        <th class="bg-secondary rounded border-1 border-white">Students</th>
                                        <th class="bg-secondary rounded border-1 border-white">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesDetail as $detail)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detail->prog_id }}</td>
                                            <td class="text-start">{{ $detail->program_name_sales }}</td>
                                            <td>{{ $detail->total_target_participant ??= 0 }}</td>
                                            <td>{{ number_format($detail->total_target_amount, '2', ',', '.') }}</td>
                                            <td>{{ $detail->total_actual_participant }}</td>
                                            <td>{{ number_format($detail->total_actual_amount, '2', ',', '.') }}</td>
                                            <td>{{ $detail->total_target_participant != 0 ? round(($detail->total_actual_participant / $detail->total_target_participant) * 100, 2) : 0 }}%
                                            </td>
                                            <td>{{ $detail->total_target_amount != 0 ? ($detail->total_actual_amount / $detail->total_target_amount) * 100 : 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="text-center">
                                        <th colspan="3">Total</th>
                                        <td><b>{{ $salesDetail->sum('total_target_participant') ?? 0 }}</b></td>
                                        <td><b>{{ number_format($salesDetail->sum('total_target_amount'), '2', ',', '.') }}</b>
                                        </td>
                                        <td><b>{{ $salesDetail->sum('total_actual_participant') ?? 0 }}</b></td>
                                        <td><b>{{ number_format($salesDetail->sum('total_actual_amount'), '2', ',', '.') }}</b>
                                        </td>
                                        <td><b>{{ $salesDetail->sum('total_target_participant') != 0 ? round(($salesDetail->sum('total_actual_participant') / $salesDetail->sum('total_target_participant')) * 100, 2) : 0 }}%</b>
                                        </td>
                                        <td><b>{{ $salesDetail->sum('total_target_amount') != 0 ? ($salesDetail->sum('total_actual_amount') / $salesDetail->sum('total_target_amount')) * 100 : 0 }}%</b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    var target_revenue_chart, target_people_chart = null;

    // sales target context
    function get_all_program(month = null, user = null) {
        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/all-program/target/' + month + '/' + user

        axios.get(url)
            .then(function(response) {

                var obj = response.data.data
                target_people_chart.data.datasets[0].data = obj.dataset.participant // target
                target_people_chart.data.datasets[1].data = obj.dataset.participant // actual 
                target_people_chart.update();

                target_revenue_chart.data.datasets[0].data = obj.dataset.revenue // target
                target_revenue_chart.data.datasets[1].data = obj.dataset.revenue // actual
                target_revenue_chart.update();

                $("#sales-target-detail tbody").html(obj.html_txt)

                showAlert({
                    target_of_participant: obj.dataset.participant[0],
                    actual_of_participant: obj.dataset.participant[1],
                    target_of_amount: obj.dataset.revenue[0],
                    actual_of_amount: obj.dataset.revenue[1],
                })

                swal.close();

            }).catch(function(error) {
                notification('error', 'Ooops! Something went wrong. Please try again.')
                swal.close();
            })
    }
</script>
<script>
    function showAlert(data) {
        const target_of_participant = parseInt(data.target_of_participant);
        const actual_of_participant = parseInt(data.actual_of_participant);
        const achieved_percentage_of_participant = target_of_participant == 0 ? actual_of_participant * 100 : (
            actual_of_participant / target_of_participant) * 100;
        round_percentage_participant = achieved_percentage_of_participant.toFixed(3);

        const target_of_amount = parseInt(data.target_of_amount);
        const actual_of_amount = parseInt(data.actual_of_amount);
        const achieved_percentage_of_amount = target_of_amount == 0 ? actual_of_amount * 100 : (actual_of_amount /
            target_of_amount) * 100;
        round_percentage_amount = achieved_percentage_of_amount.toFixed(3);

        if (target_of_participant < actual_of_participant && target_of_amount < actual_of_amount) {
            var alert_type = 'alert-success';
            var symbol =
                '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>';
            var text = 'Congratulations! You\'ve achieved the target by ' + achieved_percentage_of_participant +
                '% on client and ' + achieved_percentage_of_amount + '% on amount';
        } else if (target_of_participant < actual_of_participant || target_of_amount < actual_of_amount) {
            var alert_type = 'alert-warning';
            var symbol =
                '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>';
            var text =
                '<p>You\'ve achieved the target but please remember that the other target is not achieved yet. Good Luck!</p>';
        } else {
            var alert_type = 'alert-danger';
            var symbol =
                '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>';
            var text = 'You haven\'t achieved the target.';
        }

        var html = '<div class="alert ' + alert_type + ' row justify-content-between align-items-center p-2" role="alert">' +
            '<div class="col-md-6">' + symbol + text + '</div>' +
            '<div class="col-md-6 text-md-end">' +
            '<hr class="my-1 d-block d-md-none"><p class="my-0"> Target of client : <b>' + round_percentage_participant +
            '%</b> & target of amount : <b>' + round_percentage_amount + '%</b></p></div>' +
            '</div>';
        $(".alert-container").html(html)
    }

    const st = document.getElementById('student_target');
    const sa = document.getElementById('amount_target');

    const dataset_sales_target = new Array();
    const dataset_sales_actual = new Array();

    const target_of_participant = {{ $salesTarget->total_participant ?? 0 }};
    const actual_of_participant = {{ $salesActual->total_participant ?? 0 }};
    const achieved_percentage_of_participant = target_of_participant == 0 ? actual_of_participant * 100 : (
        actual_of_participant / target_of_participant) * 100;

    const target_of_amount = {{ $salesTarget->total_target ?? 0 }};
    const actual_of_amount = {{ $salesActual->total_target ?? 0 }};
    const achieved_percentage_of_amount = target_of_amount == 0 ? actual_of_amount * 100 : (actual_of_amount /
        target_of_amount) * 100;

    showAlert({
        target_of_participant: target_of_participant,
        actual_of_participant: actual_of_participant,
        target_of_amount: target_of_amount,
        actual_of_amount: actual_of_amount,
    })

    dataset_sales_target.push({{ $salesTarget->total_participant ?? 0 }})
    dataset_sales_target.push({{ $salesActual->total_participant ?? 0 }})

    dataset_sales_actual.push({{ $salesTarget->total_target ?? 0 }})
    dataset_sales_actual.push({{ $salesActual->total_target ?? 0 }})

    var target_people_chart = new Chart(st, {
        data: {
            labels: ['Target', 'Actual'],
            datasets: [{
                    type: 'bar',
                    label: 'Actual Sales',
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
                },
                {
                    type: 'line',
                    label: 'Target',
                    data: dataset_sales_target,
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
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
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
                    type: 'bar',
                    label: 'Actual Sales',
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
                },
                {
                    type: 'line',
                    label: 'Target',
                    data: dataset_sales_actual,
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
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
                },
                title: {
                    display: true,
                    text: 'ALL Program'
                }
            }
        },
    });
</script>
@endpush