<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-3 align-items-end">
            <div class="col-md-2 revenue-period">
                <select name="" id="" class="select w-100">
                    @for ($i = 2020; $i <= date('Y'); $i++)
                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="revenue_chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="">Revenue</div>
                        <div class="" id="revenue_month">Month</div>
                    </div>
                    <div class="card-body overflow-auto" style="height: 280px">
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

<script>
    let SI_SYMBOL = ["", "k", "M", "G", "T", "P", "E"];

    function number_format(number) {

        // what tier? (determines SI symbol)
        var tier = Math.log10(Math.abs(number)) / 3 | 0;

        // if zero, we don't need a suffix
        if (tier == 0) return number;

        // get suffix and determine scale
        var suffix = SI_SYMBOL[tier];
        var scale = Math.pow(10, tier * 3);

        // scale the number
        var scaled = number / scale;

        // format number and add suffix
        return scaled.toFixed(0) + ' ' + suffix;
    }

    function checkRevenueMode() {
        let mode = $('#revenue_mode').val()
        $('.revenue-period').addClass('d-none')
        if (mode == 'annual') {
            $('#annual').removeClass('d-none')
        } else {
            $('#monthly').removeClass('d-none')
        }
    }

    const revenue_chart = document.getElementById('revenue_chart');

    new Chart(revenue_chart, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
                label: 'Revenue',
                data: [120000000, 190000000, 300000000, 5000000, 200000000, 300000000],
                borderWidth: 4,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: {
                    formatter: function(value, context) {
                        return 'Rp. ' + number_format(value);
                    },
                    color: '#fff',
                    padding: 5,
                    borderRadius: 5,
                    backgroundColor: '#000'
                },
                tooltip: {
                    callbacks: {
                        label: function(value, context) {
                            let revenue = value.raw
                            return ' Rp. ' + number_format(revenue);
                        }
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'Rp. ' + number_format(value);
                        }
                    }
                },
            },
            onClick: (e, activeEls) => {
                let datasetIndex = activeEls[0].datasetIndex;
                let dataIndex = activeEls[0].index;
                let datasetLabel = e.chart.data.datasets[datasetIndex].label;
                let value = e.chart.data.datasets[datasetIndex].data[dataIndex];
                let label = e.chart.data.labels[dataIndex];
                $("#revenue_month").html(label)
            }
        }
    });
</script>
