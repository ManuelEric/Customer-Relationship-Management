<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-3">
            <div class="col-md-2 text-end">
                <input type="month" name="" id="digital_lead_month" class="form-control form-control-sm"
                    onchange="checkDataLead()" value="{{ date('Y-m') }}">
            </div>
        </div>
        <div class="row d-flex align-items-stretch">
            <div class="col-md-5">
                <div class="row row-cols-md-2 row-cols-1 g-2">
                    <div class="col">
                        <div id="status_achieved_lead_needed"
                            class="card border {{ $dataLeads['total_achieved_lead_needed'] >= $dataLeads['number_of_leads'] && $dataLeads['total_achieved_lead_needed'] != 0 ? 'border-info' : 'border-danger' }} border-2 shadow">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1">Number of Leads</p>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end fw-semibold lead-detail">
                                        <div class="d-flex align-items-end">
                                            <div class="fs-3" id="tot_achieved_lead_needed">
                                                {{ $dataLeads['total_achieved_lead_needed'] }}</div>
                                            <div class="fs-6" id="tot_target_lead_needed">/
                                                {{ $dataLeads['number_of_leads'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Sales</label>
                                        <label
                                            id="lead_needed_sales">{{ $actualLeadsSales['lead_needed'] }}/{{ $leadSalesTarget['lead_needed'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Sales', 'Lead')" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <div class="progress-bar" id="lead_needed_percentage_sales"
                                            style="width: {{ $leadSalesTarget['percentage_lead_needed'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Referral</label>
                                        <label
                                            id="lead_needed_referral">{{ $actualLeadsReferral['lead_needed'] }}/{{ $leadReferralTarget['lead_needed'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Referral', 'Lead')" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <div class="progress-bar" id="lead_needed_percentage_referral"
                                            style="width: {{ $leadReferralTarget['percentage_lead_needed'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Digital</label>
                                        <label id="lead_needed_digital">{{ $actualLeadsDigital['lead_needed'] }}/{{ $leadDigitalTarget['lead_needed'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Digital', 'lead')" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <div class="progress-bar" id="lead_needed_percentage_digital"
                                            style="width: {{ $leadDigitalTarget['percentage_lead_needed'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div id="status_achieved_hot_lead"
                            class="card border {{ $dataLeads['total_achieved_hot_lead'] >= $dataLeads['number_of_hot_leads'] && $dataLeads['total_achieved_hot_lead'] != 0 ? 'border-info' : 'border-danger' }} border-2 shadow">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1">Number of Hot Leads</p>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end fw-semibold">
                                        <div class="d-flex align-items-end">
                                            <div class="fs-3" id="tot_achieved_hot_lead">
                                                {{ $dataLeads['total_achieved_hot_lead'] }}</div>
                                            <div class="fs-6" id="tot_target_hot_lead">/
                                                {{ $dataLeads['number_of_hot_leads'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Sales</label>
                                        <label
                                            id="hot_lead_sales">{{ $actualLeadsSales['hot_lead'] }}/{{ $leadSalesTarget['hot_lead'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Sales', 'HotLead')" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <div class="progress-bar" id="hot_lead_percentage_sales"
                                            style="width: {{ $leadSalesTarget['percentage_hot_lead'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Referral</label>
                                        <label
                                            id="hot_lead_referral">{{ $actualLeadsReferral['hot_lead'] }}/{{ $leadReferralTarget['hot_lead'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Referral', 'HotLead')" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <div class="progress-bar" id="hot_lead_percentage_referral"
                                            style="width: {{ $leadReferralTarget['percentage_hot_lead'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Digital</label>
                                        <label
                                            id="hot_lead_digital">{{ $actualLeadsDigital['hot_lead'] }}/{{ $leadDigitalTarget['hot_lead'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Digital', 'HotLead')" aria-valuenow="25"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" id="hot_lead_percentage_digital"
                                            style="width: {{ $leadDigitalTarget['percentage_hot_lead'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div id="status_achieved_ic"
                            class="card border {{ $dataLeads['total_achieved_ic'] >= $dataLeads['number_of_ic'] && $dataLeads['total_achieved_ic'] != 0 ? 'border-info' : 'border-danger' }} border-2 shadow">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1">Number of Initial
                                            Consultation
                                        </p>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end fw-semibold">
                                        <div class="d-flex align-items-end">
                                            <div class="fs-3" id="tot_achieved_ic">
                                                {{ $dataLeads['total_achieved_ic'] }}</div>
                                            <div class="fs-6" id="tot_target_ic">/ {{ $dataLeads['number_of_ic'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Sales</label>
                                        <label
                                            id="ic_sales">{{ $actualLeadsSales['ic'] }}/{{ $leadSalesTarget['ic'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Sales', 'InitConsult')" aria-valuenow="25"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" id="ic_percentage_sales"
                                            style="width: {{ $leadSalesTarget['percentage_ic'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Referral</label>
                                        <label
                                            id="ic_referral">{{ $actualLeadsReferral['ic'] }}/{{ $leadReferralTarget['ic'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Referral', 'InitConsult')" aria-valuenow="25"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" id="ic_percentage_referral"
                                            style="width: {{ $leadReferralTarget['percentage_ic'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Digital</label>
                                        <label
                                            id="ic_digital">{{ $actualLeadsDigital['ic'] }}/{{ $leadDigitalTarget['ic'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Digital', 'InitConsult')" aria-valuenow="25"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" id="ic_percentage_digital"
                                            style="width: {{ $leadDigitalTarget['percentage_ic'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div id="status_achieved_contribution"
                            class="card border {{ $dataLeads['total_achieved_contribution'] >= $dataLeads['number_of_contribution'] && $dataLeads['total_achieved_contribution'] != 0 ? 'border-info' : 'border-danger' }} border-2 shadow">
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-lg mb-0 fw-bolder text-muted lh-1">Number of Contribution</p>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end fw-semibold">
                                        <div class="d-flex align-items-end">
                                            <div class="fs-3" id="tot_achieved_contribution">
                                                {{ $dataLeads['total_achieved_contribution'] }}</div>
                                            <div class="fs-6" id="tot_target_contribution">/
                                                {{ $dataLeads['number_of_contribution'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Sales</label>
                                        <label
                                            id="contribution_sales">{{ $actualLeadsSales['contribution'] }}/{{ $leadSalesTarget['contribution'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Sales', 'Contribution')" aria-valuenow="25"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" id="contribution_percentage_sales"
                                            style="width: {{ $leadSalesTarget['percentage_contribution'] }}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Referral</label>
                                        <label
                                            id="contribution_referral">{{ $actualLeadsReferral['contribution'] }}/{{ $leadReferralTarget['contribution'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Referral', 'Contribution')" aria-valuenow="25"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" id="contribution_percentage_referral"
                                            style="width: {{ $leadReferralTarget['percentage_contribution'] }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label>Digital</label>
                                        <label
                                            id="contribution_digital">{{ $actualLeadsDigital['contribution'] }}/{{ $leadDigitalTarget['contribution'] }}</label>
                                    </div>
                                    <div class="progress cursor-pointer " role="progressbar"
                                        onclick="checkLeadDetail('Digital', 'Contribution')" aria-valuenow="25"
                                        aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" id="contribution_percentage_digital"
                                            style="width: {{ $leadDigitalTarget['percentage_contribution'] }}%"></div>
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

<!-- Modal -->
<div class="modal modal-lg fade" id="leadsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-8">
                            <h1 class="modal-title fs-5" id="leadTitleModal">Modal title</h1>
                        </div>
                        <div class="col-2 ms-auto">
                            <button class="btn btn-sm btn-outline-info" onclick="ExportToExcel()">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export
                            </button>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left:0 !important"></button>
                    </div>
                </div>
            </div>
            <div class="modal-body overflow-auto" style="max-height: 300px" id="leadContentModal">
                <table class="table table-hover table-striped" id="listDataLeads">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Full Name</th>
                            <th>Parents Name</th>
                            <th>School Name</th>
                            <th>Graduation Year</th>
                            <th>Lead Source</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-lead-detail">
                        {{-- @for ($i = 0; $i < 20; $i++)
                            <tr>
                                <td>No</td>
                                <td>Full Name</td>
                                <td>Parents Name</td>
                                <td>School Name</td>
                                <td>Graduation Year</td>
                                <td>Lead Source</td>
                            </tr>
                        @endfor --}}
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const digitalLeadsTarget = document.getElementById('leadsTarget');

    // const options = 
    let dataset_leadsTarget = new Array();
    let dataset_leadsActual = new Array();
    let lbl_dataLeads = new Array();

    dataset_leadsTarget = {{ json_encode($dataLeadChart['target']) }}
    dataset_leadsActual = {{ json_encode($dataLeadChart['actual']) }}
    lbl_dataLeads = {!! json_encode($dataLeadChart['label']) !!}

    var chart_dataleads = new Chart(digitalLeadsTarget, {
        type: 'line',
        data: {
            labels: lbl_dataLeads,
            datasets: [{
                    label: 'Actual Sales',
                    data: dataset_leadsActual,
                    borderWidth: 1
                },
                {
                    label: 'Target',
                    data: dataset_leadsTarget,
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
        }
    });


    const idrTarget = document.getElementById('idrTarget');

    let dataset_revenueTarget = new Array();
    let dataset_revenueActual = new Array();
    let lbl_dataRevenue = new Array();

    dataset_revenueTarget = {{ json_encode($dataRevenueChart['target']) }}
    dataset_revenueActual = {{ json_encode($dataRevenueChart['actual']) }}
    lbl_dataRevenue = {!! json_encode($dataRevenueChart['label']) !!}

    var chart_datarevenue = new Chart(idrTarget, {
        type: 'line',
        data: {
            labels: lbl_dataRevenue,
            datasets: [{
                    label: 'Actual Sales',
                    data: dataset_revenueActual,
                    borderWidth: 1
                },
                {
                    label: 'Target',
                    data: dataset_revenueTarget,
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

<script>
    function ucwords(str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function($1) {
            return $1.toUpperCase();
        });
    }

    function updateProgressBarSalesTarget(result)
    {
        // console.log(result)
        var html = '';
        var no = 1;
        
        var divisi = ['sales', 'referral', 'digital'];
        var typeLead = ['lead_needed', 'hot_lead', 'ic', 'contribution']

        // Actual
        chart_dataleads.data.datasets[0].data = [];
        chart_dataleads.data.datasets[0].data = result.dataLeadChart.actual;

        // Target
        chart_dataleads.data.datasets[1].data = [];
        chart_dataleads.data.datasets[1].data = result.dataLeadChart.target;

        // Label
        chart_dataleads.data.labels = [];
        chart_dataleads.data.labels = result.dataLeadChart.label;

        // Actual
        chart_datarevenue.data.datasets[0].data = [];
        chart_datarevenue.data.datasets[0].data = result.dataRevenueChart.actual;

        // Target
        chart_datarevenue.data.datasets[1].data = [];
        chart_datarevenue.data.datasets[1].data = result.dataRevenueChart.target;

        // Label
        chart_datarevenue.data.labels = [];
        chart_datarevenue.data.labels = result.dataRevenueChart.label;

        // Lead Needed

        typeLead.forEach(function(itemType, indexType) {
            var dataKey = '';
            switch (itemType) {
                case 'lead_needed':
                    dataKey = 'number_of_leads';
                    break;
                case 'hot_lead':
                    dataKey = 'number_of_hot_leads';
                    break;
                case 'ic':
                    dataKey = 'number_of_ic';
                    break;
                case 'contribution':
                    dataKey = 'number_of_contribution';
                    break;
            }

            // Total Lead
            $('#status_achieved_' + itemType).removeClass('border-info')
            $('#status_achieved_' + itemType).removeClass('border-danger')
            $('#status_achieved_' + itemType).addClass(result.dataLeads['total_achieved_' +
                itemType] >= result.dataLeads[dataKey] && result.dataLeads[
                'total_achieved_' + itemType] != 0 ? 'border-info' : 'border-danger')
            $('#tot_achieved_' + itemType).html(result.dataLeads['total_achieved_' + itemType])
            $('#tot_target_' + itemType).html('/ ' + result.dataLeads[dataKey])
            divisi.forEach(function(itemDivisi, indexDivisi) {

                // Lead by divisi
                $('#' + itemType + '_' + itemDivisi).html(result['actualLeads' + ucwords(
                    itemDivisi)][itemType] + '/' + result['lead' + ucwords(
                    itemDivisi) + 'Target'][itemType]);
                $('#' + itemType + '_percentage_' + itemDivisi).css('width', result['lead' +
                    ucwords(itemDivisi) + 'Target']['percentage_' + itemType]+"%")
            })
        })

        chart_dataleads.update()
        chart_datarevenue.update()
    }

    function checkDataLead() {

        let month = $('#digital_lead_month').val()

        let today = moment().format('YYYY-MM')

        if (month != today) {
            $('.today').addClass('d-none')
        } else {
            $('.today').removeClass('d-none')
        }

        const rupiah = (number) => {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }).format(number);
        }

        function total(arr) {
            if (!Array.isArray(arr)) return;
            return arr.reduce((a, v) => a + v);
        }

        Swal.showLoading()
        axios.get('{{ url('api/digital/all-leads') }}/' + month)
            .then((response) => {
                var result = response.data

                updateProgressBarSalesTarget(result)

                swal.close()
            }, (error) => {
                console.log(error)
                swal.close()
            })

    }

</script>
<script>
    function checkLeadDetail(depart, type) {

        let month = $('#digital_lead_month').val()

        Swal.showLoading()
        axios.get('{{ url('api/digital/detail/') }}/' + month + '/type-lead/' + type + '/division/' + depart)
            .then((response) => {
                var result = response.data

                $('#tbody-lead-detail').html(result.html_ctx)

                $("#listDataLeads .detail").each(function(){
                    var link = "{{url('/')}}/client/student/" + $(this).data('clientid')
                    
                    $(this).click(function() {
                        window.open(link, '_blank')
                    })
                })

                swal.close()
            }, (error) => {
                console.log(error)
                swal.close()
            })

        $('#leadTitleModal').html(depart + ' ' + type)
        $('#leadsModal').modal('show')
    }

    function ExportToExcel() {

    var title = $('#leadTitleModal').text();

    var sheetName = [title];

    var tableName = ['listDataLeads'];

    var ws = new Array();

    var workbook = XLSX.utils.book_new();
    tableName.forEach(function(d, i) {
        ws[i] = XLSX.utils.table_to_sheet(document.getElementById(tableName[i]));
        XLSX.utils.book_append_sheet(workbook, ws[i], sheetName[i]);
    })

   

    XLSX.writeFile(workbook, "data-leads.xlsx");

    }
</script>
@endpush
