<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-2">
                        <input type="month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-4 text-center">
                        <div class="card mb-3">
                            <div class="card-body">
                                <canvas id="clientProgram"></canvas>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                Successful Program
                            </div>
                            <div class="card-body overflow-auto" style="height: 350px">
                                <ul class="list-group">
                                    @for ($i = 0; $i < 30; $i++)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <div class="">Program Name</div>
                                            <span class="badge badge-primary">34</span>
                                        </li>
                                    @endfor
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <canvas id="admissionsProgram"></canvas>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-5">
                                                                <canvas id="ic_consult"></canvas>
                                                            </div>
                                                            <div class="col-md-7">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <table class="table">
                                                                            <tr>
                                                                                <td>Total Inital Consultation</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        50
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Success Program</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        50
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Initial Assessment Making</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        3 Days
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Conversion Time Progess</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        14 Days
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Success Percentage</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        20%
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-header d-flex justify-content-between">
                                                        <div>Total :</div>
                                                        <div>Rp. 123.231.321</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <canvas id="academicProgram"></canvas>
                                    </div>
                                    <div class="card-header d-flex justify-content-between">
                                        <div>Total :</div>
                                        <div>Rp. 123.231.321</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <canvas id="careerProgram"></canvas>
                                    </div>
                                    <div class="card-header d-flex justify-content-between">
                                        <div>Total :</div>
                                        <div>Rp. 123.231.321</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('pages.dashboard.sales.detail.program-lead')
            </div>
        </div>
    </div>
</div>

<script>
    // percentage 
    let lbl_client_prog = [{
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


    // Overall 
    const all = document.getElementById('clientProgram');

    new Chart(all, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: '',
                data: [12, 19, 3, 5],
                backgroundColor: [
                    '#fd7e14',
                    '#dc3545',
                    '#198754',
                    '#0dcaf0',
                ],
                borderWidth: 1,
                borderRadius: 10,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                datalabels: lbl_client_prog[0],
                title: {
                    display: true,
                    text: 'Client Program Status',
                    font: {
                        size: 12,
                        weight: 'normal'
                    },
                },
                legend: {
                    display: false
                }
            }
        }
    });

    // Admissions Program 
    const adm = document.getElementById('admissionsProgram');

    new Chart(adm, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: 'Client Program',
                data: [12, 19, 3, 5],
                backgroundColor: [
                    '#fd7e14',
                    '#dc3545',
                    '#198754',
                    '#0dcaf0',
                ],
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Admissions Mentoring',
                    font: {
                        size: 15,
                        weight: 'normal'
                    },
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
                },
                datalabels: lbl_client_prog[0]
            }
        }
    });

    // Academic Program 
    const acad = document.getElementById('academicProgram');

    new Chart(acad, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: '',
                data: [12, 19, 3, 5],
                backgroundColor: [
                    '#fd7e14',
                    '#dc3545',
                    '#198754',
                    '#0dcaf0',
                ],
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: lbl_client_prog[0],
                title: {
                    display: true,
                    text: 'Academic & Test Preparation',
                    font: {
                        size: 15,
                        weight: 'normal'
                    },
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
                }
            }
        }
    });

    // Career Program 
    const career = document.getElementById('careerProgram');

    new Chart(career, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: '',
                data: [12, 19, 3, 5],
                backgroundColor: [
                    '#fd7e14',
                    '#dc3545',
                    '#198754',
                    '#0dcaf0',
                ],
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: lbl_client_prog[0],
                title: {
                    display: true,
                    text: 'Career Exploration',
                    font: {
                        size: 15,
                        weight: 'normal'
                    },
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
                }
            }
        }
    });

    const ic = document.getElementById('ic_consult');

    new Chart(ic, {
        type: 'pie',
        data: {
            labels: ['Soon', 'Already', 'Success'],
            datasets: [{
                label: '',
                data: [12, 19, 10],
                borderWidth: 1,
                borderRadius: 0,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: lbl_client_prog[0],
                title: {
                    display: true,
                    text: 'Initial Consultation',
                    font: {
                        size: 15,
                        weight: 'normal'
                    },
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 15,
                        font: {
                            size: 11
                        }
                    },
                }
            }
        }
    });
</script>
