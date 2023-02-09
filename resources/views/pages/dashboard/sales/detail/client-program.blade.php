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
                                    @forelse ($allSuccessProgramByMonth as $detail)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <div class="">{{ $detail->program_name_st }}</div>
                                            <span class="badge badge-primary">{{ $detail->total_client_per_program }}</span>
                                        </li>
                                    @empty
                                        There's no success program
                                    @endforelse
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
                                                                                <td>Total Initial Consultation</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        {{ $totalInitialConsultation }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Success Program</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        {{ $successProgram }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Initial Assessment Making</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        {{ isset($initialAssessmentMaking) ? (int)$initialAssessmentMaking->initialMaking : 0 }} Days
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Conversion Time Progess</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        {{ isset($conversionTimeProgress) ? (int)$conversionTimeProgress->conversionTime : 0 }} Days
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Success Percentage</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info">
                                                                                        {{ $successPercentage }}%
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
                                                        <div>Rp. {{ number_format($totalRevenueAdmissionMentoring,'2',',','.') }}</div>
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
                                        <div>Rp. {{ number_format($totalRevenueAcadTestPrepByMonth,'2',',','.') }}</div>
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
                                        <div>Rp. {{ number_format($totalRevenueCareerExplorationByMonth,'2',',','.') }}</div>
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
                let percentage = Math.round((value / sum) * 100);
                if (isNaN(percentage))
                    percentage = 0
                return percentage + '%';
            } else {
                return percentage + '%';
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

    // create a dataset for clientprogram by status
    var dataset_programstatus = new Array()
    @foreach ($clientProgramGroupByStatus as $key => $val)
        dataset_programstatus.push({{ (int)$val }})
    @endforeach

    new Chart(all, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: '',
                data: dataset_programstatus,
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

    // create a dataset for admission mentoring by status
    var dataset_admentoring = new Array()
    @foreach ($admissionsMentoring as $key => $val)
        dataset_admentoring.push({{ (int)$val }})
    @endforeach

    new Chart(adm, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: 'Client Program',
                data: dataset_admentoring,
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

    // create a dataset for admission mentoring by status
    var dataset_acadtestprep = new Array()
    @foreach ($academicTestPrep as $key => $val)
        dataset_acadtestprep.push({{ (int)$val }})
    @endforeach

    new Chart(acad, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: '',
                data: dataset_acadtestprep,
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

    // create a dataset for admission mentoring by status
    var dataset_careerexploration = new Array()
    @foreach ($careerExploration as $key => $val)
        dataset_careerexploration.push({{ (int)$val }})
    @endforeach

    new Chart(career, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: '',
                data: dataset_careerexploration,
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

    // create a dataset for initial consultation
    var dataset_initconsult = new Array()
    @foreach ($initialConsultation as $key => $val)
        dataset_initconsult.push({{ (int)$val }})
    @endforeach

    new Chart(ic, {
        type: 'pie',
        data: {
            labels: ['Soon', 'Already', 'Success'],
            datasets: [{
                label: '',
                data: dataset_initconsult,
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
