<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-2">
                        <input type="month" class="form-control form-control-sm qdate" value="{{ Request::get('qdate') ?? date('Y-m') }}">
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
                                <ul class="list-group" id="successful-program">
                                    @forelse ($allSuccessProgramByMonth as $detail)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <div class="">{{ $detail->program_name_st }}</div>
                                            <span class="badge badge-primary">{{ $detail->total_client_per_program }}</span>
                                        </li>
                                    @empty
                                        There's no success programs
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
                                                                                    <span class="badge badge-info init-consult-details">
                                                                                        {{ $totalInitialConsultation }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Success Program</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info init-consult-details">
                                                                                        {{ $successProgram }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Initial Assessment Making</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info init-consult-details">
                                                                                        {{ isset($initialAssessmentMaking) ? (int)$initialAssessmentMaking->initialMaking : 0 }} Days
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Conversion Time Progess</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info init-consult-details">
                                                                                        {{ isset($conversionTimeProgress) ? (int)$conversionTimeProgress->conversionTime : 0 }} Days
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Success Percentage</td>
                                                                                <td class="text-end">
                                                                                    <span class="badge badge-info init-consult-details">
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
                                                        <div class="init-consult-details">Rp. {{ number_format($totalRevenueAdmissionMentoring,'2',',','.') }}</div>
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
                                        <div class="academic-prep-details">Rp. {{ number_format($totalRevenueAcadTestPrepByMonth,'2',',','.') }}</div>
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
                                        <div class="career-exp-details">Rp. {{ number_format($totalRevenueCareerExplorationByMonth,'2',',','.') }}</div>
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


<script async type="text/javascript">
    var client_program_status_chart, admission_mentoring_chart, initial_consult_chart, academic_prep_chart, career_exploration_chart = null;


    function get_successful_program(month = null, user = null)
    {
        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/successful-program/' + month + '/' + user

        axios.get(url)
            .then(function (response) {
                var obj = response.data.data
                $("#successful-program").html(obj.html_txt);

            }).catch (function (error) {
                notification('error', 'Ooops! Something went wrong. Please try again.')

            })
    }

    function get_client_program_status(month = null, user = null)
    {
        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/client-program/' + month + '/' + user

        axios.get(url)
            .then(function (response) {
                
                var obj = response.data.data
                client_program_status_chart.data.datasets[0].data = obj
                client_program_status_chart.update();
                

            }).catch(function (error) {
                notification('error', 'Ooops! Something went wrong. Please try again.')
            })
        
    }

    function get_admission_program(month = null, user = null)
    {
        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/admissions-mentoring/' + month + '/' + user

        axios.get(url)
           .then(function (response) {
            
                var obj = response.data.data
                admission_mentoring_chart.data.datasets[0].data = obj
                admission_mentoring_chart.update();

            }).catch(function (error) {
                notification('error', 'Ooops! Something went wrong. Please try again.')
            })
    }

    function get_initial_consultation(month = null, user = null)
    {   
        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/initial-consultation/' + month + '/' + user

        axios.get(url)
           .then(function (response) {
            
                var obj = response.data.data
                initial_consult_chart.data.datasets[0].data = obj.ctx
                initial_consult_chart.update();

                $(".init-consult-details").each(function(index) {
                    $(this).html(obj.details[index])
                })

            }).catch(function (error) {
                notification('error', 'Ooops! Something went wrong. Please try again.')
            })
    }

    function get_academic_prep(month = null, user = null)
    {
        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/academic-prep/' + month + '/' + user

        axios.get(url)
           .then(function (response) {

                var obj = response.data.data
                academic_prep_chart_cp.data.datasets[0].data = obj.ctx
                academic_prep_chart_cp.update();

                $(".academic-prep-details").html(obj.total_revenue)

            }).catch(function (error) {
                notification('error', 'Ooops! Something went wrong. Please try again.')
            })
    }

    function get_career_exploration(month = null, user = null)
    {
        var today = new Date()

        if (!month)
            month = moment(today).format('YYYY-MM')

        if (!user)
            user = '';

        var url = window.location.origin + '/api/get/career-exploration/' + month + '/' + user

        axios.get(url)
        .then(function (response) {
            
                var obj = response.data.data
                career_exploration_chart.data.datasets[0].data = obj.ctx
                career_exploration_chart.update();

                $(".career-exp-details").html(obj.total_revenue)

            }).catch(function (error) {
                notification('error', 'Ooops! Something went wrong. Please try again.')
            })
    }
</script>
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

    get_client_program_status()
    // Overall 
    const all = document.getElementById('clientProgram');      
    
    var dataset_program = new Array()
    @foreach ($clientProgramGroupByStatus as $key => $val)
        dataset_program.push({{ (int)$val }})
    @endforeach

    var client_program_status_chart = new Chart(all, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Failed', 'Success', 'Refund'],
            datasets: [{
                label: '',
                data: dataset_program,
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

    var admission_mentoring_chart = new Chart(adm, {
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

    var academic_prep_chart_cp = new Chart(acad, {
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

    var career_exploration_chart = new Chart(career, {
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

    var initial_consult_chart = new Chart(ic, {
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