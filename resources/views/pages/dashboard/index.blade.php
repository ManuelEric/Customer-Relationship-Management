@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('content')

    {{-- Sales --}}
    @include('pages.dashboard.sales.index')
    {{-- Partnership --}}
    {{-- @include('pages.dashboard.partnership.index') --}}
    {{-- Finance  --}}
    {{-- @include('pages.dashboard.finance.index') --}}


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
            .then(function (response) {
                
                let html = null
                const obj = response.data.data

                var no = 1;
                obj.forEach(function(item, index) {
                    html += "<tr>" +
                            "<td class='text-center'>" + no++ + "</td>" +
                            "<td>" + item.prog_name + ": " + item.prog_program + "</td>" +
                            "<td class='text-center'>" + formatRupiah(item.revenue_year1).replace('Rp', '') + "</td>" +
                            "<td class='text-center'>" + formatRupiah(item.revenue_year2).replace('Rp', '')  + "</td>" +
                        "</tr>"
                })

                $("#comparison-table tbody").html(html)

            }).catch(function (error) {
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
        
        // $(document).ready(function() {
        //     var type = '{{ Request::get('type') }}'
        //     var tab = '{{ Request::get('tab') }}'

        //     $('.dashboard-' + type).addClass('d-none')
        //     $('#' + tab + '.dashboard-' + type).removeClass('d-none')
        //     $('.nav-link.' + type).removeClass('active')
        //     $('.nav-link.' + type + '.' + tab).addClass('active')
        // })
        
        const formatRupiah = (money) => {
            return new Intl.NumberFormat('id-ID',
                { style: 'currency', currency: 'IDR' }
            ).format(money);
        }
    </script>
    <script type="text/javascript">
        $("#cp_employee").on('change', function() {
            if ($(this).val() != "all" && $(this).val() != null) {

                const searchParams = new URLSearchParams({'quser': $(this).val()})
                location.href = "?" + searchParams
                return
            }

            let url = window.location.href
            let urlObj = new URL(url)
            urlObj.search = ''
            const result = urlObj.toString()
            window.location = result
            
        })

        $('.select-pc').select2({
            placeholder: "Select value",
            allowClear: true
        });
    </script>
@endsection
