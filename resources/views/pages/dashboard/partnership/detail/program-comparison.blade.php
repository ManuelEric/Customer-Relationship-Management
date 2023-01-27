<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-2">
            <div class="col-md-3 d-flex align-items-center">
                <select name="" id="start_comparison" class="select w-100" style="width: 45%"
                    onchange="checkComparison()">
                    @for ($i = 2020; $i <= date('Y'); $i++)
                        <option value="{{ $i }}" {{ $i == date('Y') - 1 ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                <div class="mx-2">
                    VS
                </div>
                <select name="" id="end_comparison" class="select w-100" style="width: 45%"
                    onchange="checkComparison()">
                    @for ($i = 2020; $i <= date('Y'); $i++)
                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        Partner Program
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="comparison_partner">
                                    <canvas id="comparison_partner"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="row row-cols-md-1 row-cols-2 g-3">
                                            <div class="col text-center">
                                                <div class="comparison_start">
                                                </div>
                                                <span class="badge badge-primary">
                                                    Rp. 200.000.000
                                                </span>
                                            </div>
                                            <div class="col text-center">
                                                <div class="comparison_end">
                                                </div>
                                                <span class="badge badge-primary">
                                                    Rp. 200.000.000
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        School Program
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="comparison_school">
                                    <canvas id="comparison_school"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="row row-cols-md-1 row-cols-2 g-3">
                                            <div class="col text-center">
                                                <div class="comparison_start">
                                                </div>
                                                <span class="badge badge-primary">
                                                    Rp. 200.000.000
                                                </span>
                                            </div>
                                            <div class="col text-center">
                                                <div class="comparison_end">
                                                </div>
                                                <span class="badge badge-primary">
                                                    Rp. 200.000.000
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        Referral
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="comparison_referral">
                                    <canvas id="comparison_referral"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="row row-cols-md-1 row-cols-2 g-3">
                                            <div class="col text-center">
                                                <div class="comparison_start">
                                                </div>
                                                <span class="badge badge-primary">
                                                    Rp. 200.000.000
                                                </span>
                                            </div>
                                            <div class="col text-center">
                                                <div class="comparison_end">
                                                </div>
                                                <span class="badge badge-primary">
                                                    Rp. 200.000.000
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-body overflow-auto" style="height: 300px">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-dark text-white">
                                <tr class="text-center">
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Program Name</th>
                                    <th rowspan="2">Type</th>
                                    <th colspan="2">Year</th>
                                </tr>
                                <tr class="text-center">
                                    <th class="comparison_start">2022</th>
                                    <th class="comparison_end">2023</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>1</td>
                                    <td>Program Name</td>
                                    <td>Partner Program</td>
                                    <td>10 (Rp. 123.333.122)</td>
                                    <td>10 (Rp. 123.333.122)</td>
                                </tr>
                                <tr class="text-center">
                                    <td>1</td>
                                    <td>Program Name</td>
                                    <td>School Program</td>
                                    <td>10 (Rp. 123.333.122)</td>
                                    <td>10 (Rp. 123.333.122)</td>
                                </tr>
                                <tr class="text-center">
                                    <td>1</td>
                                    <td>Program Name</td>
                                    <td>Referral In</td>
                                    <td>10 (Rp. 123.333.122)</td>
                                    <td>10 (Rp. 123.333.122)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // percentage 
    let lbl_program_comparison = [{
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
        anchor: 'end',
        borderRadius: 10,
        backgroundColor: '#192e54',
    }]

    function checkComparison() {
        let start = $('#start_comparison').val()
        let end = $('#end_comparison').val()

        if (start != end) {
            $('.comparison_start').html(start)
            $('.comparison_end').html(end)

            // Axios here ...
            let data = {
                'label': {
                    'start': start,
                    'end': end
                },
                'total': {
                    'start': 'Rp. 120.000.000',
                    'end': 'Rp. 120.000.000'
                },
                'partner': [12, 5],
                'school': [4, 5],
                'referral': [6, 5],
            }
            renderChart(data)
        } else {
            $('#end_comparison').val(parseInt(start) + 1).trigger('change')
        }


    }

    function renderChart(data = null) {
        $('#comparison_partner').remove()
        $('.comparison_partner').append('<canvas id="comparison_partner"></canvas>')
        const comparison_partner = document.getElementById('comparison_partner');

        new Chart(comparison_partner, {
            type: 'doughnut',
            data: {
                labels: [data.label.start, data.label.end],
                datasets: [{
                    label: 'Success Program',
                    data: data ? data.partner : null,
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                plugins: {
                    datalabels: lbl_program_comparison[0],
                }

            }
        });


        $('#comparison_school').remove()
        $('.comparison_school').append('<canvas id="comparison_school"></canvas>')
        const comparison_school = document.getElementById('comparison_school');

        new Chart(comparison_school, {
            type: 'doughnut',
            data: {
                labels: [data.label.start, data.label.end],
                datasets: [{
                    label: 'Success Program',
                    data: data ? data.school : null,
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                plugins: {
                    datalabels: lbl_program_comparison[0],
                }
            }
        });

        $('#comparison_referral').remove()
        $('.comparison_referral').append('<canvas id="comparison_referral"></canvas>')
        const comparison_referral = document.getElementById('comparison_referral');

        new Chart(comparison_referral, {
            type: 'doughnut',
            data: {
                labels: [data.label.start, data.label.end],
                datasets: [{
                    label: 'Success Program',
                    data: data ? data.referral : null,
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                plugins: {
                    datalabels: lbl_program_comparison[0],
                }
            }
        });
    }

    function renderTable() {

    }

    checkComparison()
</script>
