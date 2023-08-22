@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
    
    {{-- Alert  --}}

    {{-- General --}}
    @if ($alarmLeads['general']['mid']['event'])
        <div class="row  g-3 mb-1">
            <div class="col-12">
                <div class="alert bg-danger text-white d-flex align-items-center mb-0 py-2" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <small class="">
                        There are no events this month.
                    </small>
                </div>
            </div>
        </div>
    @endif 

     <div class="row {{$isAdmin ? 'row-cols-md-2' : 'row-cols-md-1'}} row-cols-1 g-3 mb-3">
        {{-- Sales --}}
        @if ($isAdmin || $isSales)
            @include('pages.dashboard.sales.detail.alarm')
        @endif 

        {{-- Digital --}}
         @if ($isDigital || $isAdmin)
            @include('pages.dashboard.digital.detail.alarm')
        @endif
    </div> 

    {{-- End Alert --}}

    {{-- Sales --}}
    @if ($isSales || $isAdmin)
        <div class="card bg-secondary mb-1">
            <div class="d-flex justify-content-between align-items-center px-3 text-white">
                <h3 class="mb-0">Sales Dashboard</h3>
                <h1><i class="bi bi-person me-2 opacity-50"></i></h1>
            </div>
        </div>
        @include('pages.dashboard.sales.index')
    @endif
    {{-- Partnership --}}
    @if ($isPartnership || $isAdmin)
        <div class="card bg-secondary mb-1">
            <div class="d-flex justify-content-between align-items-center px-3 text-white">
                <h3 class="mb-0"> Partnership Dashboard</h3>
                <h1><i class="bi bi-building me-2 opacity-50"></i></h1>
            </div>
        </div>
        @include('pages.dashboard.partnership.index')
    @endif
    {{-- Digital  --}}
    @if ($isDigital || $isAdmin)
        <div class="card bg-secondary mb-1">
            <div class="d-flex justify-content-between align-items-center px-3 text-white">
                <h3 class="mb-0">Digital Dashboard</h3>
                <h1><i class="bi bi-bar-chart-line me-2 opacity-50"></i></h1>
            </div>
        </div>
        @include('pages.dashboard.digital.index')
    @endif
    {{-- Finance  --}}
    @if ($isFinance || $isAdmin)
        <div class="card bg-secondary mb-1">
            <div class="d-flex justify-content-between align-items-center px-3 text-white">
                <h3 class="mb-0">Finance Dashboard</h3>
                <h1><i class="bi bi-currency-dollar me-2 opacity-50"></i></h1>
            </div>
        </div>
        @include('pages.dashboard.finance.index')
    @endif


    <script>
        $(document).ready(function() {

            $(".btn-compare").on('click', function() {

                showLoading()
                get_program_comparison()

            })
        })

        function get_program_comparison() {
            let prog = $("select[name='q-program']").val();
            var first_year = $("select[name='q-first-year']").val();
            var second_year = $("select[name='q-second-year']").val();
            var user = $("#cp_employee").val();

            var first_monthyear = $("#q-first-monthyear").val();
            var second_monthyear = $("#q-second-monthyear").val();

            var use_filter_by_month = $("#use-filter-by-month").prop('checked');

            var link = window.location.origin + '/api/get/program-comparison'

            var url = new URL(link);
            url.searchParams.append('prog', prog)

            // if (use_filter_by_month === true) {
            url.searchParams.append('first_monthyear', first_monthyear)
            url.searchParams.append('second_monthyear', second_monthyear)
            // } else {
            url.searchParams.append('first_year', first_year)
            url.searchParams.append('second_year', second_year)
            // }
            url.searchParams.append('u', user)
            url.searchParams.append('query_month', use_filter_by_month);

            console.log(url)

            axios.get(url)
                .then(function(response) {

                    let html = null
                    const obj = response.data.data

                    var no = 1;
                    obj.forEach(function(item, index) {
                        html += "<tr>" +
                            "<td class='text-center'>" + no++ + "</td>" +
                            "<td>" + item.prog_name + ": " + item.prog_program + "</td>" +
                            "<td class='text-center'>" + formatRupiah(item.revenue_year1).replace('Rp',
                                '') + "</td>" +
                            "<td class='text-center'>" + formatRupiah(item.revenue_year2).replace('Rp',
                                '') + "</td>" +
                            "</tr>"
                    })

                    $(".dashboard-pc--year_1").html(first_year)
                    $(".dashboard-pc--year_2").html(second_year)
                    $("#comparison-table tbody").html(html)

                    swal.close();

                }).catch(function(error) {
                    notification('error', error.message);
                })
        }

        function dashboardTab(type, tab) {

            $('.dashboard-' + type).addClass('d-none')
            $('#' + tab + '.dashboard-' + type).removeClass('d-none')
            $('.nav-link.' + type).removeClass('active')
            $('.nav-link.' + type + '.' + tab).addClass('active')
            return

            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('type', type)
            urlParams.set('tab', tab)
            window.location.search = urlParams
            return
        }

        const formatRupiah = (money) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(money);
        }
    </script>
    <script type="text/javascript">
        $("#cp_employee").on('change', function() {
            showLoading()

            var month = $(".qdate").val()
            var uuid = $(this).val() == "all" ? null : $(this).val()
            var year = $("#qclient-event-year").val()

            reloadChart(month, uuid, year)

        })

        $('.select-pc').select2({
            placeholder: "Select value",
            allowClear: true
        });

        $(".qdate").on('change', function() {
            showLoading()
            var month = $(this).val()
            $(".qdate").val(month)
            var uuid = $('#cp_employee').val() == "all" ? null : $('#cp_employee').val()
            var year = $("#qclient-event-year").val()

            reloadChart(month, uuid, year)

        })

        $("#use-filter-by-month").on('change', function() {
            var val = $(this).prop('checked');

            if (val === true) {
                $("#filter-withmonth-container").removeClass('d-none');
                $("#filter-year-container").addClass('d-none')
            } else {
                $("#filter-withmonth-container").addClass('d-none');
                $("#filter-year-container").removeClass('d-none')
            }
        })

        function reloadChart(month, uuid, year) {

            get_client_program_status(month, uuid)
            get_successful_program(month, uuid)
            get_admission_program(month, uuid)
            get_initial_consultation(month, uuid)
            get_academic_prep(month, uuid)
            get_career_exploration(month, uuid)
            get_conversion_leads(month, uuid)
            get_admission_mentoring_lead(month, uuid)
            get_academic_prep_lead(month, uuid)
            get_career_exp_lead(month, uuid)
            get_all_program(month, uuid)
            get_program_comparison()
            // get_client_event(year, uuid)
        }
    </script>
@endsection
