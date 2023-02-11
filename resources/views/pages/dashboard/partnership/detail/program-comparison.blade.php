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
                            <tbody id="tbl_comparison">
                                @php
                                    $startYear = date('Y') - 1;
                                    $endYear = date('Y');
                                @endphp
                                @foreach ($programComparisons as $key => $programComparison)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $programComparison[0]['program_name'] }}</td>
                                        <td>{{ $programComparison[0]['type'] }}</td>
                                        @if(count($programComparison) > 1)
                                            @foreach ($programComparison as $yearData)
                                                <td>{{$yearData['year'] == $startYear ? $yearData[$startYear]['participants'] .' (Rp. '. number_format($yearData[$startYear]['total'], '2', ',', '.') .')' : $yearData['2023']['participants']  .' (Rp. '. number_format($yearData['2023']['total'], '2', ',', '.') .')'}}</td>
                                            @endforeach  
                                        @else
                                            <td>{{ isset($programComparison[0][$startYear]) ? $programComparison[0][$startYear]['participants'] .' (Rp. '. number_format($programComparison[0][$startYear]['total'], '2', ',', '.') .')' : '-' }}</td>
                                            <td>{{isset($programComparison[0][$endYear]) ? $programComparison[0][$endYear]['participants'] .' (Rp. '.number_format($programComparison[0][$endYear]['total'], '2', ',', '.').')' : '-'}}</td>
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

<script>
    // percentage 
    let lbl_program_comparison = [{
        formatter: (value, ctx) => {
            let datasets = ctx.chart.data.datasets;
            if (datasets.indexOf(ctx.dataset) === datasets.length - 1) {
                let sum = datasets[0].data.reduce((a, b) => a + b, 0);
                let percentage = Math.round((value / sum) * 100);
                if(isNaN(percentage))
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


        const rupiah = (number)=>{
            return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
            }).format(number);
        }

        if (start != end) {
            $('.comparison_start').html(start)
            $('.comparison_end').html(end)
            
            let data = {
                'label': {
                    'start': start,
                    'end': end
                },
                'total': {
                    'start': 'Rp. 120.000.000',
                    'end': 'Rp. 120.000.000'
                },
                'partner': [0, 0],
                'school': [0, 0],
                'referral': [6, 5],
            }
            // Axios here ...
            axios.get('{{ url("api/partner/partnership-program/program-comparison") }}/' + start + '/' +  end)
            .then((response) => {

                var result = response.data.data

                result.totalPartner.forEach(function (item, index) {
                    switch (item['type']) {
                        case 'start':
                            data['partner'][0] = item['count'];
                            $('#tot_start_partner').html(rupiah(item['total_fee']));
                            break;
                        case 'end':
                            data['partner'][1] = item['count'];
                            $('#tot_end_partner').html(rupiah(item['total_fee']));
                            break;
                        default:
                            break;
                    }
                })

                result.totalSch.forEach(function (item, index) {
                    switch (item['type']) {
                        case 'start':
                            data['school'][0] = item['count'];
                            $('#tot_start_school').html(rupiah(item['total_fee']));
                            break;
                        case 'end':
                            data['school'][1] = item['count'];
                            $('#tot_end_school').html(rupiah(item['total_fee']));
                            break;
                        default:
                            break;
                    }
                })
                
                var html;
                var no = 1;
                
                $('#tbl_comparison').empty()

                Object.entries(result.programComparisons).forEach(entry => {
                    const [key, value] = entry;
                    html = "<tr class='text-center'>";
                    html += "<td>" + no + "</td>";
                    html += "<td>" + value[0]['program_name'] + "</td>";
                    html += "<td>" + value[0]['type'] + "</td>";
                        if(value.length > 1){
                            Object.entries(value).forEach(entry => {
                                const [key, value] = entry;
                                html += "<td>" + (value['year'] === start ? value[start]['participants'] + ' (' + rupiah(value[start]['total']) + ')' : value[end]['participants'] + ' (' + rupiah(value[end]['total']) + ')') + "</td>"
                            })
                        }else{
                            console.log(value[0])
                            html += "<td>" + (value[0]['year'] === start ? value[0][start]['participants'] + ' (' + rupiah(value[0][start]['total']) + ')' : '-' ) + "</td>";
                            html += "<td>" + (value[0]['year'] === end ? value[0][end]['participants'] + ' (' + rupiah(value[0][end]['total']) + ')' : '-' ) + "</td>";
                        }
                    html += '</tr>'
                    $('#tbl_comparison').append(html);
                    no++;
                });

                }, (error) => {
                    console.log(error)
                    swal.close()
                })

          
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
