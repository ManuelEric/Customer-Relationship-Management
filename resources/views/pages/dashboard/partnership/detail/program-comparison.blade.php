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
        <div class="row row-cols-md-3 flex-md-row flex-column gap-md-0 gap-1">
            <div class="col">
                <div class="card">
                    <div class="card-header text-center">
                        Partner Program
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center row-cols-1">
                            <div class="col px-md-5 px-0">
                                <div class="comparison_partner px-5">
                                    <canvas id="comparison_partner"></canvas>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="row row-cols-2 g-3">
                                            <div class="col text-center">
                                                <div class="comparison_start">
                                                </div>
                                                <span class="badge badge-primary" id="tot_start_partner">
                                                </span>
                                            </div>
                                            <div class="col text-center">
                                                <div class="comparison_end">
                                                </div>
                                                <span class="badge badge-primary" id="tot_end_partner">
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
            <div class="col">
                <div class="card">
                    <div class="card-header text-center">
                        School Program
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center row-cols-1">
                            <div class="col px-md-5 px-0">
                                <div class="comparison_school px-5">
                                    <canvas id="comparison_school"></canvas>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="row row-cols-2 g-3">
                                            <div class="col text-center">
                                                <div class="comparison_start">
                                                </div>
                                                <span class="badge badge-primary" id="tot_start_school">
                                                </span>
                                            </div>
                                            <div class="col text-center">
                                                <div class="comparison_end">
                                                </div>
                                                <span class="badge badge-primary" id="tot_end_school">
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
            <div class="col">
                <div class="card">
                    <div class="card-header text-center">
                        Referral
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="comparison_referral p-0">
                                    <canvas id="comparison_referral"></canvas>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="row row-cols-2 g-3">
                                            <div class="col text-center">
                                                <div class="comparison_start">
                                                </div>
                                                <span class="badge badge-primary" id="tot_start_ref">
                                                </span>
                                            </div>
                                            <div class="col text-center">
                                                <div class="comparison_end">
                                                </div>
                                                <span class="badge badge-primary" id="tot_end_ref">
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
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead class="bg-dark text-white">
                                <tr class="text-center">
                                    <th rowspan="2" class="bg-secondary rounded border-1 border-white">No</th>
                                    <th rowspan="2" class="bg-secondary rounded border-1 border-white">Program Name</th>
                                    <th rowspan="2" class="bg-secondary rounded border-1 border-white">Type</th>
                                    <th colspan="3" class="bg-secondary rounded border-1 border-white">Year</th>
                                </tr> 
                                <tr class="text-center">
                                    <th class="comparison_start bg-secondary rounded border-1 border-white" id="label-start-table">2022 <br>
                                       
                                    </th>
                                    <th class="comparison_end bg-secondary rounded border-1 border-white" id="label-end-table">2023</th>
                                </tr>
                            </thead>
                            <tbody id="tbl_comparison" class="overflow-auto" style="max-height: 300px">
                                @php
                                    $startYear = date('Y') - 1;
                                    $endYear = date('Y');
                                @endphp
                                @foreach ($programComparisons as $key => $programComparison)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $programComparison[0]['program_name'] }}</td>
                                        <td>{{ $programComparison[0]['type'] }}</td>
                                        @if (count($programComparison) > 1)
                                            @foreach ($programComparison as $yearData)
                                                <td>{{ $yearData['year'] == $startYear ? $yearData[$startYear]['participants'] . ' (Rp. ' . number_format($yearData[$startYear]['total'], '2', ',', '.') . ')' : $yearData[$endYear]['participants'] . ' (Rp. ' . number_format($yearData[$endYear]['total'], '2', ',', '.') . ')' }}
                                                </td>
                                            @endforeach
                                        @else
                                            <td>{{ isset($programComparison[0][$startYear]) ? $programComparison[0][$startYear]['participants'] . ' (Rp. ' . number_format($programComparison[0][$startYear]['total'], '2', ',', '.') . ')' : '-' }}
                                            </td>
                                            <td>{{ isset($programComparison[0][$endYear]) ? $programComparison[0][$endYear]['participants'] . ' (Rp. ' . number_format($programComparison[0][$endYear]['total'], '2', ',', '.') . ')' : '-' }}
                                            </td>
                                        @endif

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var comparison_partner_chart, comparison_school_chart, comparison_referral_chart = null;

    // percentage 
    let lbl_program_comparison = [{
        formatter: (value, ctx) => {
            let datasets = ctx.chart.data.datasets;
            if (datasets.indexOf(ctx.dataset) === datasets.length - 1) {
                let sum = datasets[0].data.reduce((a, b) => a + b, 0);
                let percentage = Math.round((value / sum) * 100);
                if (isNaN(percentage))
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
        anchor: 'end',
        borderRadius: 10,
        backgroundColor: '#192e54',
    }]

    function checkComparison() {
        let start = $('#start_comparison').val()
        let end = $('#end_comparison').val()


        const rupiah = (number) => {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }).format(number);
        }

        if (start != end) {
            $('.comparison_start').html(start)
            $('.comparison_end').html(end)
            
            $('#label-start-table').html(start + '<br><hr>Total Program | Partcipants | Total Fee')
            $('#label-end-table').html(end + '<br><hr>Total Program | Partcipants | Total Fee')

           
            comparison_partner_chart.data.labels = [start, end]
            comparison_school_chart.data.labels = [start, end]
            comparison_referral_chart.data.labels = [start, end]

            // Axios here ...
            axios.get('{{ url('api/partner/partnership-program/program-comparison') }}/' + start + '/' + end)
                .then((response) => {

                    var result = response.data.data

                    result.totalPartner.forEach(function(item, index) {
                        switch (item['type']) {
                            case 'start':
                                comparison_partner_chart.data.datasets[0].data[0] = item['count'];
                                $('#tot_start_partner').html(rupiah(item['total_fee']));
                                break;
                            case 'end':
                                comparison_partner_chart.data.datasets[0].data[1] = item['count'];
                                $('#tot_end_partner').html(rupiah(item['total_fee']));
                                break;
                            default:
                                break;
                        }
                    })
                    comparison_partner_chart.update()

                    result.totalSch.forEach(function(item, index) {
                        switch (item['type']) {
                            case 'start':
                                comparison_school_chart.data.datasets[0].data[0] = item['count'];
                                $('#tot_start_school').html(rupiah(item['total_fee']));
                                break;
                            case 'end':
                                comparison_school_chart.data.datasets[0].data[1] = item['count'];
                                $('#tot_end_school').html(rupiah(item['total_fee']));
                                break;
                            default:
                                break;
                        }
                    })
                    comparison_school_chart.update()
                    
                    var total_ref = 0;
                     $('#tot_start_ref').html(rupiah(0))
                     $('#tot_end_ref').html(rupiah(0))
                    comparison_referral_chart.data.datasets[0].data = [0,0]
                    comparison_referral_chart.data.datasets[1].data = [0,0]
                    result.totalReferral.forEach(function(item, index) {
                        switch (item['type']) {
                            case 'start':
                                comparison_referral_chart.data.datasets[item['referral_type'] == 'In' ? 0 : 1].data[0] = item['count'];
                                $('#tot_start_ref').html(rupiah(item['total_fee']));
                                break;
                            case 'end':
                                comparison_referral_chart.data.datasets[item['referral_type'] == 'In' ? 0 : 1].data[1] = item['count'];
                                total_ref += parseInt(item['total_fee']);
                                $('#tot_end_ref').html(rupiah(total_ref));
                                break;
                        }
                    })
                    comparison_referral_chart.update()

                    var html;
                    var no = 1;

                    $('#tbl_comparison').empty()

                    // console.log(result.programComparisons)

                    Object.entries(result.programComparisons).forEach(entry => {
                        const [key, value] = entry;
                        html = "<tr class='text-center'>";
                        html += "<td>" + no + "</td>";
                        html += "<td>" + value[0]['program_name'] + "</td>";
                        html += "<td>" + value[0]['type'] + "</td>";
                        if (value.length > 1) {
                            Object.entries(value).forEach(entry => {
                                const [key, value] = entry;
                                html += "<td>" + (value['year'] === start ?  value[start]['count_program'] + ' | ' + 
                                    value[start]['participants'
                                ] + ' (' + rupiah(value[start]['total']) + ')' : value[end]['count_program'] + ' | ' +  
                                    value[end]['participants'
                                ] + ' (' + rupiah(value[end]['total']) + ')') + "</td>"
                            })
                        } else {
                            html += "<td>" + (value[0]['year'] === start ? value[0][start]['count_program'] + ' | ' + value[0][start]['participants'] +
                                ' (' + rupiah(value[0][start]['total']) + ')' : '-') + "</td>";
                            html += "<td>" + (value[0]['year'] === end ? value[0][end]['count_program'] + ' | ' + value[0][end]['participants'] +
                                ' (' + rupiah(value[0][end]['total']) + ')' : '-') + "</td>";
                        }
                        html += '</tr>'
                        $('#tbl_comparison').append(html);
                        no++;
                    });

                }, (error) => {
                    console.log(error)
                    swal.close()
                })


            // renderChart(data)
        } else {
            $('#end_comparison').val(parseInt(start) + 1).trigger('change')
        }


    }

        const comparison_partner = document.getElementById('comparison_partner');
        const comparison_school = document.getElementById('comparison_school');
        const comparison_referral = document.getElementById('comparison_referral');

        const dataset_comparison_partner = new Array();
        const dataset_comparison_school = new Array();
        const dataset_comparison_referral = new Array();

        var comparison_partner_chart = new Chart(comparison_partner, {
            type: 'doughnut',
            data: {
                labels: [{{date('Y')}}, {{date('Y')-1}}],
                datasets: [{
                    label: 'Success Program',
                    data: [0,0],
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                plugins: {
                    legend: {
                        labels: {
                            boxWidth: 10,
                        }
                    },
                    datalabels: lbl_program_comparison[0],
                }

            }
        });

        var comparison_school_chart = new Chart(comparison_school, {
            type: 'doughnut',
            data: {
                labels: [{{date('Y')-1}}, {{date('Y')}}],
                datasets: [{
                    label: 'Success Program',
                    data: [0,0],
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                plugins: {
                    legend: {
                        labels: {
                            boxWidth: 10,
                        }
                    },
                    datalabels: lbl_program_comparison[0],
                }
                
            }
        });

        var comparison_referral_chart = new Chart(comparison_referral, {
            type: 'bar',
            data: {
                labels: [{{date('Y')}}, {{date('Y')-1}}],
                datasets: [{
                        label: 'Referral In',
                        data: [0,0],
                        borderWidth: 0,
                    },
                    {
                        label: 'Referral Out',
                        data: [0,0],
                        borderWidth: 0,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        labels: {
                            boxWidth: 10,
                        }
                    }
                }
            }
        });

        // new Chart(comparison_referral, {
        //     type: 'doughnut',
        //     data: {
        //         labels: [data.label.start, data.label.end],
        //         datasets: [{
        //             label: 'Success Program',
        //             data: data ? data.referral : null,
        //             borderWidth: 1
        //         }]
        //     },
        //     plugins: [ChartDataLabels],
        //     options: {
        //         plugins: {
        //             datalabels: lbl_program_comparison[0],
        //         }
        //     }
        // });
    // }

    function renderTable() {

    }

    checkComparison()
</script>
@endpush
