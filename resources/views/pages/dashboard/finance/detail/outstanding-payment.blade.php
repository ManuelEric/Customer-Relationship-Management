<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-3 align-items-end">
            <div class="col-md-2">
                <select name="" id="filter_mode" class="select w-100" onchange="checkFinanceMode()">
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom Period</option>
                </select>
            </div>
            <div class="col-md-2 text-end finance-period" id="monthly">
                <input type="month" name="" id="" class="form-control form-control-sm"
                    value="{{ date('Y-m') }}">
            </div>
            <div class="col-md-3 finance-period d-none" id="custom">
                <div class="row g-1">
                    <div class="col">
                        <label for="">Start Date</label>
                        <input type="date" name="" id="" class="form-control form-control-sm"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col">
                        <label for="">End Date</label>
                        <input type="date" name="" id="" class="form-control form-control-sm"
                            value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header text-center">
                        Overall Invoice
                    </div>
                    <div class="card-body">
                        <canvas id="payment_chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row row-cols-md-2">
                    <div class="col">
                        <div class="card">
                            <div class="card-header text-center">
                                Paid Payments
                            </div>
                            <div class="card-body overflow-auto" style="height: 300px">
                                <table class="table table-hover">
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Program Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    @for ($i = 0; $i < 20; $i++)
                                        <tr>
                                            <td>ID</td>
                                            <td>Full Name</td>
                                            <td>Program Name</td>
                                            <td>Amount</td>
                                        </tr>
                                    @endfor
                                </table>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <h6 class="m-0 p-0">Total Paid</h6>
                                <h6 class="m-0 p-0">Rp. 123.000.000</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header text-center">
                                Unpaid Payments
                            </div>
                            <div class="card-body overflow-auto" style="height: 300px">
                                <table class="table table-hover">
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Program Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    @for ($i = 0; $i < 20; $i++)
                                        <tr>
                                            <td>ID</td>
                                            <td>Full Name</td>
                                            <td>Program Name</td>
                                            <td>Amount</td>
                                        </tr>
                                    @endfor
                                </table>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <h6 class="m-0 p-0">Total Paid</h6>
                                <h6 class="m-0 p-0">Rp. 123.000.000</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // percentage 
    let lbl_outstanding_payment = [{
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
        color: '#fff',
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
        backgroundColor: '#192e54',
    }]

    const payment_chart = document.getElementById('payment_chart');

    new Chart(payment_chart, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Unpaid'],
            datasets: [{
                label: 'Invoice',
                data: [12, 19],
                borderWidth: 4
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: lbl_outstanding_payment[0],
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
                }
            },
        }
    });

    function checkFinanceMode() {
        let mode = $('#filter_mode').val()
        $('.finance-period').addClass('d-none')
        if (mode == 'custom') {
            $('#custom').removeClass('d-none')
        } else {
            $('#monthly').removeClass('d-none')
        }
    }
</script>
