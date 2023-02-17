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
                            <tbody id="tbl_revenue">

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <h6 class="m-0 p-0">Total Paid</h6>
                        <h6 class="m-0 p-0" id="tot_paid">Rp. 0</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    renderChart(null)

    function checkRevenueMode() {
        let mode = $('#revenue_mode').val()
        $('.revenue-period').addClass('d-none')
        if (mode == 'annual') {
            $('#annual').removeClass('d-none')
        } else {
            $('#monthly').removeClass('d-none')
        }
    }
   

    function checkRevenueByYear(){
        let year = $('#revenue_year').val()

        let revenue = [0,0,0,0,0,0,0,0,0,0,0,0]


        axios.get('{{ url("api/finance/revenue") }}/' + year)
            .then((response) => {

                var result = response.data.data

                var index =0;
                Object.entries(result.totalRevenue).forEach(entry => {
                    const [key, value] = entry;
                    revenue[index] = value;
                    index++;
                });
                console.log(revenue)

                }, (error) => {
                    console.log(error)
                    swal.close()
                })

       renderChart(revenue);

    }

    
    function renderChart(revenue = null){
        $('#revenue_chart').remove()
        $('.revenue_chart').append('<canvas id="revenue_chart"></canvas>')

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

        const revenue_chart = document.getElementById('revenue_chart');
    
        new Chart(revenue_chart, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                datasets: [{
                    label: 'Revenue',
                    data: revenue ? revenue : null,
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
                    let month = new Date(`${label} 1, 2023`).getMonth() + 1;
                    let year = $('#revenue_year').val()

                    const rupiah = (number)=>{
                        return new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR"
                        }).format(number);
                    }

                    axios.get('{{ url("api/finance/revenue/detail") }}/' + year + '/' +  month)
                        .then((response) => {
                            var result = response.data.data
                            
                            console.log(result)
                            
                            var html = '';     
                            var no = 1;   
                            var total_paid = 0;
                            var total_paid_diff = 0;

                            $('#tbl_revenue').empty();
                            
                            result.revenueDetail.forEach(function (item, index) {
                                var diff = (item.total > item.total_price_inv ? item.total - item.total_price_inv : 0);
                                html = "<tr>";
                                html += "<td>" + no + "</td>"
                                html += "<td>" + item.full_name + "</td>"
                                html += "<td>" + item.program_name + "</td>"
                                html += "<td>" + rupiah(item.total) + (diff > 0 ? " ("+ rupiah(diff) +")" : '') + "</td>"
                                total_paid += item.total_price_inv;
                                total_paid_diff += diff;
                                $('#tbl_revenue').append(html);
                                no++;
                            })

                            
                            $('#tot_paid').empty().append(rupiah(total_paid) + (total_paid_diff > 0 ? " (" +(rupiah(total_paid_diff)+")") : ''));

                        }, (error) => {
                            console.log(error)
                        })


                    $("#revenue_month").html(label)
                }
            }
        });
    }
    
    checkRevenueByYear()
    
</script>
