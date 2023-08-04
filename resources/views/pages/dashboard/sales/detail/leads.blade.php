<div class="card mb-3">
    <div class="card-body">
        <div class="row d-flex align-items-stretch">
            <div class="col-md-5">
                <div class="row row-cols-md-2 row-cols-1 g-2">
                    <div class="col">
                        <div class="card border {{ $dataLeads['total_achieved_lead_needed'] >= $dataLeads['number_of_leads'] ? 'border-info' : 'border-danger' }} border-2 shadow">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1">Number of Leads</p>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end fw-semibold">
                                        <div class="d-flex align-items-end">
                                            <div class="fs-3">{{ $dataLeads['total_achieved_lead_needed'] }}</div>
                                            <div class="fs-6">/ {{ $dataLeads['number_of_leads'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Sales</label>
                                        <label id="salesLabel">{{$actualLeadsSales['lead_needed']}}/{{ $leadSalesTarget['lead_needed'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadSalesTarget['percentage_lead_needed'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Referral</label>
                                        <label id="referralLabel">{{$actualLeadsReferral['lead_needed']}}/{{ $leadReferralTarget['lead_needed'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadReferralTarget['percentage_lead_needed'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Digital</label>
                                        <label id="digitalLabel">{{$actualLeadsDigital['lead_needed']}}/{{ $leadDigitalTarget['lead_needed'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadDigitalTarget['percentage_lead_needed'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border {{ $dataLeads['total_achieved_hot_lead'] >= $dataLeads['number_of_leads'] ? 'border-info' : 'border-danger' }} border-2 shadow">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1">Number of Hot Leads</p>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end fw-semibold">
                                        <div class="d-flex align-items-end">
                                            <div class="fs-3">{{ $dataLeads['total_achieved_hot_lead'] }}</div>
                                            <div class="fs-6">/ {{ $dataLeads['number_of_hot_leads'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Sales</label>
                                        <label id="salesLabel">{{$actualLeadsSales['hot_lead']}}/{{ $leadSalesTarget['hot_lead'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadSalesTarget['percentage_hot_lead'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Referral</label>
                                        <label id="referralLabel">{{$actualLeadsReferral['hot_lead']}}/{{ $leadReferralTarget['hot_lead'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadReferralTarget['percentage_hot_lead'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Digital</label>
                                        <label id="digitalLabel">{{$actualLeadsDigital['hot_lead']}}/{{ $leadDigitalTarget['hot_lead'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadDigitalTarget['percentage_hot_lead'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border {{ $dataLeads['total_achieved_ic'] >= $dataLeads['number_of_leads'] ? 'border-info' : 'border-danger' }} border-2 shadow">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1">Number of Initial Consultation
                                        </p>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end fw-semibold">
                                        <div class="d-flex align-items-end">
                                            <div class="fs-3">{{ $dataLeads['total_achieved_ic'] }}</div>
                                            <div class="fs-6">/ {{ $dataLeads['number_of_ic'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Sales</label>
                                        <label id="salesLabel">{{$actualLeadsSales['IC']}}/{{ $leadSalesTarget['ic'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadSalesTarget['percentage_ic'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Referral</label>
                                        <label id="referralLabel">{{$actualLeadsReferral['IC']}}/{{ $leadReferralTarget['ic'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadReferralTarget['percentage_ic'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Digital</label>
                                        <label id="digitalLabel">{{$actualLeadsDigital['IC']}}/{{ $leadDigitalTarget['ic'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadDigitalTarget['percentage_ic'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border {{ $dataLeads['total_achieved_contribution'] >= $dataLeads['number_of_leads'] ? 'border-info' : 'border-danger' }} border-2 shadow">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1">Number of Contribution</p>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end fw-semibold">
                                        <div class="d-flex align-items-end">
                                            <div class="fs-3">{{ $dataLeads['total_achieved_contribution'] }}</div>
                                            <div class="fs-6">/ {{ $dataLeads['number_of_contribution'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Sales</label>
                                        <label id="salesLabel">{{$actualLeadsSales['contribution']}}/{{ $leadSalesTarget['contribution'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadSalesTarget['percentage_contribution'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Referral</label>
                                        <label id="referralLabel">{{$actualLeadsReferral['contribution']}}/{{ $leadReferralTarget['contribution'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadReferralTarget['percentage_contribution'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Digital</label>
                                        <label id="digitalLabel">{{$actualLeadsDigital['contribution']}}/{{ $leadDigitalTarget['contribution'] }}</label>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Basic example"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $leadDigitalTarget['percentage_contribution'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="h-100">
                    <div class="row row-cols-md-2 row-cols-1 d-flex align-items-stretch h-100">
                        <div class="col">
                            <div class="h-100 card shadow bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="text-center w-100">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1 mb-2">Leads Target</p>
                                        <canvas id="leadsTarget" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="h-100 card shadow bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="text-center w-100">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1 mb-2">IDR Target</p>
                                        <canvas id="idrTarget" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    console.log('{{json_encode($dataLeadChart["label"])}}')
    const leadsTarget = document.getElementById('leadsTarget');

    const options = 

    new Chart(leadsTarget, {
        type: 'line',
        data: {
            labels: {{json_encode($dataLeadChart["label"])}},
            datasets: [{
                label: 'Actual Sales',
                data: {{json_encode($dataLeadChart["actual"])}},
                borderWidth: 1
            },
            {
                label: 'Target',
                data: {{json_encode($dataLeadChart["target"])}},
                borderWidth: 1
            },
        ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
                },
            }
        }
    });

    const idrTarget = document.getElementById('idrTarget');

    new Chart(idrTarget, {
        type: 'line',
        data: {
            labels: ['June', 'May', 'April'],
            datasets: [{
                label: 'Actual Sales',
                data: [12, 19, 7],
                borderWidth: 1
            },
            {
                label: 'Target',
                data: [30, 10, 8],
                borderWidth: 1
            },
        ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                    }
                },
            }
        }
    });
</script>
