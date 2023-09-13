<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-3 align-items-end">
            <div class="col-md-2 revenue-period">
                <select name="" id="revenue_year" onchange="checkRevenueByYear()" class="select w-100">
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
                    <div class="card-body revenue_chart">
                        <canvas id="revenue_chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="">Revenue</div>
                        <div class="" id="revenue_month">{{ date('F') }}</div>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 280px">
                        <table class="table table-striped table-hover">
                            <tr class="text-center">
                                <th class='bg-secondary rounded border border-white text-white'>ID</th>
                                <th class='bg-secondary rounded border border-white text-white'>Full Name</th>
                                <th class='bg-secondary rounded border border-white text-white'>Type</th>
                                <th class='bg-secondary rounded border border-white text-white'>Program Name</th>
                                <th class='bg-secondary rounded border border-white text-white'>Installment</th>
                                <th class='bg-secondary rounded border border-white text-white'>Amount</th>
                            </tr>
                            <tbody id="tbl_revenue">
                                @php
                                    $total_paid_diff = 0;
                                @endphp
                                @foreach ($paidPayments as $paidPayment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $paidPayment->full_name }}</td>
                                        <td>{{ $paidPayment->type }}</td>
                                        <td>{{ $paidPayment->program_name }}</td>
                                        <td class="text-center">
                                            {{ isset($paidPayment->installment_name) ? $paidPayment->installment_name : '-' }}
                                        </td>
                                        <td>Rp. {{ number_format($paidPayment->total, '2', ',', '.') }}</td>
                                    </tr>
                                    @php
                                        $total_paid_diff += $paidPayment->total > $paidPayment->total_price_inv ? $paidPayment->total - $PaidPayment->total_price_inv : 0;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <h6 class="m-0 p-0">Total Paid</h6>
                        <h6 class="m-0 p-0" id="tot_paid_revenue">Rp.
                            {{ number_format($paidPayments->sum('total'), '2', ',', '.') }}
                            {{ $total_paid_diff > 0 ? '(Rp. ' . number_format($total_paid_diff, '2', ',', '.') . ')' : '' }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var revenue_chart = null;

    const rupiah = (number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        }).format(number);
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


    function checkRevenueByYear() {
        let year = $('#revenue_year').val()

        showLoading()
        axios.get('{{ url('api/finance/revenue') }}/' + year)
            .then((response) => {

                $('#tbl_revenue').empty();
                $('#tot_paid_revenue').empty();

                let revenue = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                var result = response.data.data

                revenue_chart.data.datasets[0].data = revenue;

                var index = 0;
                Object.entries(result.totalRevenue).forEach(entry => {
                    const [key, value] = entry;
                    revenue[key - 1] = value;
                    revenue_chart.data.datasets[0].data[key - 1] = revenue[key - 1];
                    index++;
                });

                revenue_chart.update()

                swal.close()
            }, (error) => {
                notification('error', error.message);
                swal.close()
            })

    }



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
        return scaled.toFixed(1) + ' ' + suffix;
    }


    const rc = document.getElementById('revenue_chart');
    const dataset_revenue = new Array();

    let revenue = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    @foreach ($revenue as $key => $tot_revenue)
        revenue[{{ $key - 1 }}] = {{ $tot_revenue ?? 0 }}
    @endforeach

    Object.entries(revenue).forEach(entry => {
        const [key, value] = entry;
        dataset_revenue[key] = value
    });

    var revenue_chart = new Chart(rc, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
                'October', 'November', 'December'
            ],
            datasets: [{
                label: 'Revenue',
                data: dataset_revenue,
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
                legend: {
                    display: true,
                    labels: {
                        boxWidth: 10,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(value, context) {
                            let revenue = value.raw
                            return rupiah(revenue);
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
                let month = new Date(`${label} 1, 2023`).getMonth() + 1;
                let year = $('#revenue_year').val()


                showLoading()
                axios.get('{{ url('api/finance/revenue/detail') }}/' + year + '/' + month)
                    .then((response) => {
                        var result = response.data.data

                        var html = '';
                        var no = 1;
                        var total_paid = 0;
                        var total_paid_diff = 0;

                        $('#tbl_revenue').empty();

                        result.revenueDetail.forEach(function(item, index) {
                            var diff = (parseInt(item.total) > parseInt(item.total_price_inv) ?
                                parseInt(item.total) - parseInt(item.total_price_inv) : 0);
                            html = "<tr>";
                            html += "<td>" + no + "</td>"
                            html += "<td>" + item.full_name + "</td>"
                            html += "<td>" + item.type + "</td>"
                            html += "<td>" + item.program_name + "</td>"
                            html += "<td class='text-center'>" + (item.installment_name !==
                                null ? item.installment_name : "-") + "</td>"
                            html += "<td>" + rupiah(parseInt(item.total)) + (parseInt(diff) >
                                0 ? " (" + rupiah(parseInt(diff)) + ")" : '') + "</td>"
                            total_paid += parseInt(item.total);
                            total_paid_diff += parseInt(diff);
                            $('#tbl_revenue').append(html);
                            no++;
                        })


                        $('#tot_paid_revenue').html(rupiah(total_paid) + (total_paid_diff > 0 ? " (" + (
                            rupiah(total_paid_diff) + ")") : ''));

                        swal.close()
                    }, (error) => {
                        console.log(error)
                        swal.close()

                    })


                $("#revenue_month").html(label)
            }
        }
    });


    // checkRevenueByYear()
</script>
@endpush