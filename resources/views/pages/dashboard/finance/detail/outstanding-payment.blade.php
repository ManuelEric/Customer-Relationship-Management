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
                <input type="month" name="" id="month_outstanding" class="form-control form-control-sm" onchange="checkFinancebyMonth()"
                    value="{{ date('Y-m') }}">
            </div>
            <div class="col-md-3 finance-period d-none" id="custom">
                <div class="row g-1">
                    <div class="col">
                        <label for="">Start Date</label>
                        <input type="date" name="" id="start_outstanding" onchange="checkFinancebyPeriode()" class="form-control form-control-sm" 
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col">
                        <label for="">End Date</label>
                        <input type="date" name="" id="end_outstanding" onchange="checkFinancebyPeriode()" class="form-control form-control-sm"
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
                    <div class="card-body payment_chart">
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
                                        <tbody id="tbl_paid_payment">
                                            @foreach ($paidPayments as $paidPayment)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $paidPayment->full_name }}</td>
                                                    <td>{{ $paidPayment->program_name }}</td>
                                                    <td>Rp. {{ number_format($paidPayment->total, '2', ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <h6 class="m-0 p-0">Total Paid</h6>
                                <h6 class="m-0 p-0" id="tot_paid">Rp. {{ number_format($paidPayments->sum('total'), '2', ',', '.') }}</h6>
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
                                    <tbody id="tbl_unpaid_payment">
                                        @foreach ($unpaidPayments as $unpaidPayment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $unpaidPayment->full_name }}</td>
                                                <td>{{ $unpaidPayment->program_name }}</td>
                                                <td>Rp. {{ number_format($unpaidPayment->total, '2', ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <h6 class="m-0 p-0">Total Unpaid</h6>
                                <h6 class="m-0 p-0" id="tot_unpaid">Rp. {{ number_format($unpaidPayments->sum('total'), '2', ',', '.') }}</h6>
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
                let percentage = Math.round((value / sum) * 100);
                if(isNaN(percentage))
                    return 0;
                else 
                    return percentage + "%";
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

    function checkFinancebyMonth()
    {
        let month = $('#month_outstanding').val()
        
        const rupiah = (number)=>{
            return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
            }).format(number);
        }

        let data = {
            'total': [0, 0],
        }

        // Axios here ...
            axios.get('{{ url("api/finance/outstanding/") }}/' + month)
            .then((response) => {

                var result = response.data.data

                console.log(result)

                data['total'][0] = result.paidPayments.length
                data['total'][1] = result.unpaidPayments.length

                var html;
                var no = 1;
                var total_paid = 0;
                var total_paid_diff = 0;
                var total_unpaid = 0;
                
                $('#tbl_paid_payment').empty();

                result.paidPayments.forEach(function (item, index) {
                    var diff = (item.total > item.total_price_inv ? item.total - item.total_price_inv : 0);
                    html = "<tr>";
                    html += "<td>" + no + "</td>"
                    html += "<td>" + item.full_name + "</td>"
                    html += "<td>" + item.program_name + "</td>"
                    html += "<td>" + rupiah(item.total) + (diff > 0 ? " ("+ rupiah(diff) +")" : '') + "</td>"
                    total_paid += item.total_price_inv;
                    total_paid_diff += diff;
                    $('#tbl_paid_payment').append(html);
                    no++;
                })

                console.log(data)
                
                $('#tot_paid').html(rupiah(total_paid) + (total_paid_diff > 0 ? " (" +(rupiah(total_paid_diff)+")") : ''));
                
                html = '';     
                no = 1;   
                $('#tbl_unpaid_payment').empty();
                
                result.unpaidPayments.forEach(function (item, index) {
                    html = "<tr>";
                    html += "<td>" + no + "</td>"
                    html += "<td>" + item.full_name + "</td>"
                    html += "<td>" + item.program_name + "</td>"
                    html += "<td>" + rupiah(item.total) + "</td>"
                    total_unpaid += item.total;
                    $('#tbl_unpaid_payment').append(html);
                    no++;
                })
                
                $('#tot_unpaid').html(rupiah(total_unpaid))

                }, (error) => {
                    console.log(error)
                    swal.close()
                })

          
            renderChart(data)

    }

    function checkFinancebyPeriode(){
        let start_date = $('#start_outstanding').val()
        let end_date = $('#end_outstanding').val()
        
        console.log(start_date)
        console.log(end_date)


         const rupiah = (number)=>{
            return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
            }).format(number);
        }

        let data = {
            'total': [0, 0],
        }

        // Axios here ...
            axios.get('{{ url("api/finance/outstanding/period") }}/' + start_date + '/' + end_date)
            .then((response) => {

                var result = response.data.data

                
                data['total'][0] = result.paidPayments.length
                data['total'][1] = result.unpaidPayments.length
                console.log(data)
                
                var html;
                var no = 1;
                var total_paid = 0;
                var total_paid_diff = 0;
                var total_unpaid = 0;
                
                $('#tbl_paid_payment').empty();

                result.paidPayments.forEach(function (item, index) {
                    var diff = (item.total > item.total_price_inv ? item.total - item.total_price_inv : 0);
                    html = "<tr>";
                    html += "<td>" + no + "</td>"
                    html += "<td>" + item.full_name + "</td>"
                    html += "<td>" + item.program_name + "</td>"
                    html += "<td>" + rupiah(item.total) + (diff > 0 ? " ("+ rupiah(diff) +")" : '') + "</td>"
                    total_paid += item.total_price_inv;
                    total_paid_diff += diff;
                    $('#tbl_paid_payment').append(html);
                    no++;
                })

                // console.log(total_paid_diff)
                
                $('#tot_paid').html(rupiah(total_paid) + (total_paid_diff > 0 ? " (" +(rupiah(total_paid_diff)+")") : ''));
                
                html = '';     
                no = 1;   
                $('#tbl_unpaid_payment').empty();
                
                result.unpaidPayments.forEach(function (item, index) {
                    html = "<tr>";
                    html += "<td>" + no + "</td>"
                    html += "<td>" + item.full_name + "</td>"
                    html += "<td>" + item.program_name + "</td>"
                    html += "<td>" + rupiah(item.total) + "</td>"
                    total_unpaid += item.total;
                    $('#tbl_unpaid_payment').append(html);
                    no++;
                })
                
                $('#tot_unpaid').html(rupiah(total_unpaid))
                
                renderChart(data)
                }, (error) => {
                    console.log(error)
                    swal.close()
                })

          
    }

   function renderChart(data = null){
        $('#payment_chart').remove()
        $('.payment_chart').append('<canvas id="payment_chart"></canvas>')
        const payment_chart = document.getElementById('payment_chart');

        new Chart(payment_chart, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Unpaid'],
                datasets: [{
                    label: 'Invoice',
                    data: data.total ? data.total : null,
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

    }

    checkFinancebyMonth()
  $( document ).ready(function() {
    // console.log( "ready!" );
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
