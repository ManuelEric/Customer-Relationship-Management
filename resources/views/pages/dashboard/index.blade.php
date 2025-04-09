@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
    
    {{-- Alert  --}}

    {{-- General --}}
    @includeWhen($alarmLeads['general']['mid']['target'] || $alarmLeads['general']['mid']['event'], 'pages.dashboard.alarm')

     <div @class([
        'row',
        'row-cols-md-2' => $isSuperAdmin,
        'row-cols-md-1' => !$isSuperAdmin,
        'row-cols-1',
        'g-3',
        'mb-3'
     ])>

        {{-- Sales --}}
        @includeWhen($isSuperAdmin || $isSalesAdmin || $isSales, 'pages.dashboard.sales.detail.alarm')

        {{-- Digital --}}
        @includeWhen($isSuperAdmin || $isSalesAdmin || $isDigital, 'pages.dashboard.digital.detail.alarm')

    </div> 

    {{-- End Alert --}}

    @if($isSuperAdmin || $isSalesAdmin)
        <x-dashboard.nav />
    @endif

    {{-- Sales --}}
    @includeWhen(Request::segment(2) == 'sales' && ($isSuperAdmin || $isSalesAdmin || $isSales), 'pages.dashboard.sales.index')

    {{-- Partnership --}}
    @includeWhen(Request::segment(2) == 'partnership' && ($isSuperAdmin || $isPartnership), 'pages.dashboard.partnership.index')

    {{-- Digital  --}}
    @includeWhen(Request::segment(2) == 'digital' && ($isSuperAdmin || $isSalesAdmin || $isDigital), 'pages.dashboard.digital.index')

    {{-- Finance  --}}
    @includeWhen(Request::segment(2) == 'finance' && ($isSuperAdmin || $isFinance), 'pages.dashboard.finance.index')

@endsection

@push('scripts')
<script type="text/javascript" async>

        $(".btn-compare").on('click', function() {

            showLoading()
            get_program_comparison()

        })

        function get_program_comparison() {
            let prog = $("select[name='q-program']").val();
            var first_year = $("select[name='q-first-year']").val();
            var second_year = $("select[name='q-second-year']").val();
            var user = $("#cp_employee").val();
    
            var first_monthyear = $("#q-first-monthyear").val();
            var second_monthyear = $("#q-second-monthyear").val();
    
            var use_filter_by_month = $("#use-filter-by-month").prop('checked');
    
            var url = window.location.origin + '/api/v1/dashboard/program-comparison'
    
            axios.get(url, {
                    params: {
                        prog: prog,
                        first_monthyear: first_monthyear,
                        second_monthyear: second_monthyear,
                        first_year: first_year,
                        second_year: second_year,
                        uuid: user,
                        query_month: use_filter_by_month
                    }
                })
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
        var daterange = $('#daterange').val();

        reloadChart(month, uuid, year, daterange)

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

    function reloadChart(month, uuid, year, daterange) {

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
        get_domicile(uuid, daterange)

        // get_client_event(year, uuid)
    }
</script>
@endpush
