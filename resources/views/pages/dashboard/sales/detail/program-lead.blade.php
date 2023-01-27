<div class="row mt-3">
    <div class="col-md-12">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h5>Conversion Leads</h5>
                        <canvas id="leadSource" class="mb-2"></canvas>
                        <canvas id="programLead"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row justify-content-center align-items-center g-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Admissions Mentoring</h6>
                                <canvas id="admissionsLead"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Academic & Test Preparation</h6>
                                <canvas id="academicLead"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Career Exploration</h6>
                                <canvas id="careerLead"></canvas>
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
    let lbl_prog_lead = [{
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
        color: '#000',
        font: {
            size: 11
        },
        padding: {
            left: 4,
            right: 4,
            top: 3,
            bottom: 1
        },
        anchor: 'center',
        borderRadius: 10,
        backgroundColor: '#fff',
    }]


    // Overall Lead 
    const lead = document.getElementById('leadSource');

    new Chart(lead, {
        type: 'bar',
        data: {
            labels: ['Whatshapp', 'Instagram', 'KOL - @ads', 'Edufair: Lorem'],
            datasets: [{
                label: '',
                data: [12, 19, 3, 5],
                borderWidth: 1,
                borderRadius: 20,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            indexAxis: 'y',
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                datalabels: lbl_prog_lead[0],
                title: {
                    display: true,
                    text: 'Lead Source',
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

    const progLead = document.getElementById('programLead');

    new Chart(progLead, {
        type: 'bar',
        data: {
            labels: ['Whatshapp', 'Instagram', 'KOL - @ads', 'Edufair: Lorem'],
            datasets: [{
                label: '',
                data: [12, 19, 3, 5],
                borderWidth: 1,
                borderRadius: 20,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            indexAxis: 'y',
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                datalabels: lbl_prog_lead[0],
                title: {
                    display: true,
                    text: 'Conversion Leads',
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
    const admLead = document.getElementById('admissionsLead');

    new Chart(admLead, {
        type: 'pie',
        data: {
            labels: ['Whatshapp', 'Instagram', 'KOL - @ads', 'Edufair: Lorem'],
            datasets: [{
                label: '',
                data: [12, 19, 3, 5],
                borderWidth: 1,
                borderRadius: 3,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: lbl_prog_lead[0],
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
    const acadLead = document.getElementById('academicLead');

    new Chart(acadLead, {
        type: 'pie',
        data: {
            labels: ['Whatshapp', 'Instagram', 'KOL - @ads', 'Edufair: Lorem'],
            datasets: [{
                label: '',
                data: [12, 19, 3, 5],
                borderWidth: 1,
                borderRadius: 3,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: lbl_prog_lead[0],
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
    const careerLead = document.getElementById('careerLead');

    new Chart(careerLead, {
        type: 'pie',
        data: {
            labels: ['Whatshapp', 'Instagram', 'KOL - @ads', 'Edufair: Lorem'],
            datasets: [{
                label: '',
                data: [12, 19, 3, 5],
                borderWidth: 1,
                borderRadius: 3,
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            plugins: {
                datalabels: lbl_prog_lead[0],
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
</script>
