<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-2">
            <div class="col-md-2 text-end">
                <input type="month" name="" id="month_partner_program" class="form-control form-control-sm"
                    value="{{ date('Y-m') }}" onchange="checkProgrambyMonth()">
            </div>
        </div>
        <div class="row align-items-stretch">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header text-center">
                        Partner Program
                    </div>
                    <div class="card-body">
                        <div class="partnership-program partner">
                            <canvas id="partner_program"></canvas>
                        </div>
                    </div>
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="m-0 p-0">Total</h6>
                        <h6 class="m-0 p-0">Rp. 123.000.000</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header text-center">
                        School Program
                    </div>
                    <div class="card-body">
                        <div class="partnership-program school">
                            <canvas id="school_program"></canvas>
                        </div>
                    </div>
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="m-0 p-0">Total</h6>
                        <h6 class="m-0 p-0">Rp. 123.000.000</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header text-center">
                        Referral
                    </div>
                    <div class="card-body p-4">
                        <div class="partnership-program referral">
                            <canvas id="referral_program"></canvas>
                        </div>
                    </div>
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="m-0 p-0">Total</h6>
                        <h6 class="m-0 p-0">Rp. 123.000.000</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="" id="partner_type">Partner</div>
                        <div class="" id="partner_status">Pending</div>
                    </div>
                    <div class="card-body p-1 overflow-auto" style="max-height: 300px;">
                        <ul class="list-group" style="font-size: 11px">
                            @for ($i = 0; $i < 30; $i++)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="">
                                        <strong>
                                            Parter/School Name
                                        </strong> <br>
                                        <small>Program Name</small>
                                    </div>
                                </li>
                            @endfor
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // percentage 
    let lbl_partner_prog = [{
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

    function checkProgrambyMonth() {
        let month = $('.month_partner_program').val()

        // Axios here ... 
        let data = {
            'partner': [20, 10, 2, 4],
            'school': [12, 5, 9, 1],
            'referral': [5, 10],
        }
        renderChartProgram(data)
    }

    function renderChartProgram(data = null) {
        $('.partnership-program canvas').remove()
        $('.partnership-program.partner').append('<canvas id="partner_program"></canvas>')

        const partner_program = document.getElementById('partner_program');

        new Chart(partner_program, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Success', 'Denied', 'Refund'],
                datasets: [{
                    label: 'Partner Program',
                    data: data ? data.partner : null,
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                plugins: {
                    datalabels: lbl_partner_prog[0],
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                        }
                    }
                },
                onClick: (e, activeEls) => {
                    let datasetIndex = activeEls[0].datasetIndex;
                    let dataIndex = activeEls[0].index;
                    let label = e.chart.data.labels[dataIndex];

                    $('#partner_type').html('Partner')
                    $('#partner_status').html(label)
                }
            }
        });

        $('.partnership-program.school').append('<canvas id="school_program"></canvas>')
        const school_program = document.getElementById('school_program');

        new Chart(school_program, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Success', 'Denied', 'Refund'],
                datasets: [{
                    label: 'School Program',
                    data: data ? data.school : null,
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                plugins: {
                    datalabels: lbl_partner_prog[0],
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                        }
                    }
                },
                onClick: (e, activeEls) => {
                    let datasetIndex = activeEls[0].datasetIndex;
                    let dataIndex = activeEls[0].index;
                    let label = e.chart.data.labels[dataIndex];

                    $('#partner_type').html('School')
                    $('#partner_status').html(label)
                }
            }
        });


        $('.partnership-program.referral').append('<canvas id="referral_program"></canvas>')
        const referral_program = document.getElementById('referral_program');

        new Chart(referral_program, {
            type: 'doughnut',
            data: {
                labels: ['Referral IN', 'Referral Out'],
                datasets: [{
                    label: '# of Votes',
                    data: data ? data.referral : null,
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                plugins: {
                    datalabels: lbl_partner_prog[0],
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                        }
                    }
                },
                onClick: (e, activeEls) => {
                    let datasetIndex = activeEls[0].datasetIndex;
                    let dataIndex = activeEls[0].index;
                    let label = e.chart.data.labels[dataIndex];

                    $('#partner_type').html('Referral')
                    $('#partner_status').html(label)
                }
            }
        });
    }

    checkProgrambyMonth()
</script>
