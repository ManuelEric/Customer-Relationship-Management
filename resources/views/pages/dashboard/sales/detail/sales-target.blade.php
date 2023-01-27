<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end mb-2">
            <div class="col-md-2">
                <input type="month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <canvas id="student_target"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body overflow-auto" style="height: 400px">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th rowspan="2">No</th>
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
                                    <tr class="text-center">
                                        <td>No</td>
                                        <td class="text-start">Program Name</td>
                                        <td>10</td>
                                        <td>100.000.000</td>
                                        <td>5</td>
                                        <td>60.000.000</td>
                                        <td>50%</td>
                                        <td>60%</td>
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

<script>
    const st = document.getElementById('student_target');

    new Chart(st, {
        data: {
            labels: ['Student', 'Amount'],
            datasets: [{
                    type: 'line',
                    label: 'Target',
                    data: [12, 40],
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
                    data: [20, 30],
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
