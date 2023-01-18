<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-2">
                        <input type="month" class="form-control form-control-sm">
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
                                                                                <td class="text-end">50</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Success Program</td>
                                                                                <td class="text-end">14</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Initial Assessment Making</td>
                                                                                <td class="text-end">3 Days</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Conversion Time Progess</td>
                                                                                <td class="text-end">14 Days</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Success Percentage</td>
                                                                                <td class="text-end">20%</td>
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
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
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
                }
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
        options: {
            plugins: {
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
        options: {
            plugins: {
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
                borderRadius: 5,
            }]
        },
        options: {
            plugins: {
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
                    labels: {
                        boxWidth: 15,
                    }
                }
            }
        }
    });
</script>
