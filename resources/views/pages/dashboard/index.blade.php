@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('content')

    {{-- Sales --}}
    @if ($isSales || $isAdmin)
        <h3 class="my-3">
            <i class="bi bi-person me-2"></i>
            Sales Dashboard</h3>
        @include('pages.dashboard.sales.index')
    @endif
    {{-- Partnership --}}
    @if ($isPartnership || $isAdmin)
        <h3 class="my-3">
            <i class="bi bi-building me-2"></i>
            Partnership Dashboard
        </h3>
        @include('pages.dashboard.partnership.index')
    @endif
    {{-- Finance  --}}
    @if ($isFinance || $isAdmin)
        <h3 class="my-3">
            <i class="bi bi-currency-dollar me-2"></i>
            Finance Dashboard
        </h3>
        @include('pages.dashboard.finance.index')
    @endif


    <script>
        $(".btn-compare").on('click', function() {

            let prog = $("select[name='q-program']").val();
            var first_year = $("select[name='q-first-year']").val();
            var second_year = $("select[name='q-second-year']").val();
            var user = $("#cp_employee").val();

            var link = window.location.origin + '/api/get/program-comparison'

            var url = new URL(link);
            url.searchParams.append('prog', prog)
            url.searchParams.append('first_year', first_year)
            url.searchParams.append('second_year', second_year)
            url.searchParams.append('u', user)

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

                    $("#comparison-table tbody").html(html)

                }).catch(function(error) {
                    console.log(error)
                })

        })

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

            var month = $(this).val()
            $(".qdate").val(month)
            var uuid = $('#cp_employee').val() == "all" ? null : $('#cp_employee').val()
            var year = $("#qclient-event-year").val()

            reloadChart(month, uuid, year)

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
            get_client_event(year, uuid)
        }
    </script>
@endsection
