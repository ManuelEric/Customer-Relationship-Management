<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-between g-1 mb-2">
            <div class="col-md-2">
                    <select id="period-partnership" class="select w-100" onchange="checkPeriodPartnership()">
                    <option value="all">All</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>
            <div class="col-md-2 text-end d-none" id="monthly-partnership">
                    <input type="month" name="" id="partner_status_month" class="form-control form-control-sm"
                    onchange="checkPartnerStatusbyMonth()" value="{{ date('Y-m') }}">
            </div>
        </div>

        <div class="row align-items-stretch">
            <div class="col-md-3">
                <div class="card rounded border h-100 card-partner cursor-pointer" data-partner-type="Partner">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total Partner</h5>
                        <h4 class="m-0 p-0" id="tot_partner">
                            {{ $totalPartner }}<sup></sup><span class="badge bg-primary text-white p-1 px-2 ms-2"><small>{{$newPartner}} New</small></span></sup>
                        </h4>
                    </div>
                    <div class="mb-2 ms-3 border-top pt-3" id="parent_percentage_partner">
                        <p class="mb-0 text-muted partner-status-detail">
                            <span @class([
                                'me-2',
                                'text-success'
                            ])>
                                <i @class([
                                    'bi',
                                    'bi-arrow-up-short'
                                ])></i>
                                0%
                            </span>
                            <span>Since before</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100 card-partner cursor-pointer" data-partner-type="School">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        @if($totalUncompleteSchool > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger today"
                                style="font-size: 11px">
                                {{$totalUncompleteSchool}}
                            </span>
                        @endif                        
                        <h5 class="m-0 p-0">Total School</h5>
                        <h4 class="m-0 p-0" id="tot_school">
                            {{ $totalSchool }}<sup><span class="badge bg-primary text-white p-1 px-2 ms-2"><small>{{$newSchool}} New</small></span></sup>
                        </h4>
                    </div>
                    <div class="mb-2 ms-3 border-top pt-3" id="parent_percentage_partner">
                        <p class="mb-0 text-muted partner-status-detail">
                            <span @class([
                                'me-2',
                                'text-success'
                            ])>
                                <i @class([
                                    'bi',
                                    'bi-arrow-up-short'
                                ])></i>
                                0%
                            </span>
                            <span>Since before</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100 card-partner cursor-pointer" data-partner-type="University">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total University</h5>
                        <h4 class="m-0 p-0" id="tot_univ">
                            {{ $totalUniversity }}<sup><span class="badge bg-primary text-white p-1 px-2 ms-2"><small>{{$newUniversity}} New</small></span></sup>
                        </h4>
                    </div>
                    <div class="mb-2 ms-3 border-top pt-3" id="parent_percentage_partner">
                        <p class="mb-0 text-muted partner-status-detail">
                            <span @class([
                                'me-2',
                                'text-success'
                            ])>
                                <i @class([
                                    'bi',
                                    'bi-arrow-up-short'
                                ])></i>
                                0%
                            </span>
                            <span>Since before</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100 card-partner cursor-pointer" data-partner-type="Agreement">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="">
                            <h6 class="m-0 p-0">Partner Agreement</h6>
                            <small>Need to be extended</small>
                        </div>
                        <h4 class="m-0 p-0 text-danger" id="tot_agreement">
                            {{ $totalAgreement }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail -->
<div class="modal modal-lg fade" id="list-detail-partner" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><!-- title here --></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-3">
                        {{-- <input type="month" name="" id="menteesBirthdayMonth" class="form-control form-control-sm"
                            value="{{ date('Y-m') }}"> --}}
                    </div>
                </div>
                <div class="overflow-auto d-none" style="max-height:400px; margin-bottom:25px" id="additionalTable">
                    <table class="table table-striped table-hover" id="listAdditionalTable">
                        <thead class="text-center" id="thead-additional">
                            {{-- Head table --}}
                        </thead>
                        <tbody id="tbody-additional">
                            <!-- content here -->
                        </tbody>
                    </table>
                </div>
    
                <div class="overflow-auto" style="height: 400px">
                    <table class="table table-striped table-hover" id="listPartnerTable">
                        <thead class="text-center" id="thead-partner">
                            {{-- Head table --}}
                        </thead>
                        <tbody id="tbody-partner">
                            <!-- content here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function checkPeriodPartnership() {
        let period = $('#period-partnership').val()
        if (period == 'monthly') {
            $('#monthly-partnership').removeClass('d-none')
        }else{
            $('#monthly-partnership').addClass('d-none')
        }   
        checkPartnerStatusbyMonth()
     
    }

    $(".card-partner").each(function() {
        $(this).click(function() {
            showLoading()
           
            let period = $('#period-partnership').val()
            let type = $(this).data('partner-type')
            let month;

            if (period == 'all'){    
                month = {{date('Y-m')}}
            }else{
                month = $('#partner_status_month').val()
            }
            
            let url = window.location.origin + '/api/partner/detail/'+ month +'/'+ type;
            var html;

            switch (type) {
                case 'Partner':
                        html = "<tr class='text-white'>"
                        html += "<th class='bg-secondary rounded border border-white'>No</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Corporate Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Email</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Office Number</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Type</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Country Type</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Created At</th>"
                        html += "</tr>"
                    break;
                
                case 'School':
                        html = "<tr class='text-white'>"
                        html += "<th class='bg-secondary rounded border border-white'>No</th>"
                        html += "<th class='bg-secondary rounded border border-white'>School Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Type</th>"
                        html += "<th class='bg-secondary rounded border border-white'>City</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Location</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Created At</th>"
                        html += "</tr>"
                    break;

                case 'University':
                        html = "<tr class='text-white'>"
                        html += "<th class='bg-secondary rounded border border-white'>No</th>"
                        html += "<th class='bg-secondary rounded border border-white'>University ID</th>"
                        html += "<th class='bg-secondary rounded border border-white'>University Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Address</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Email</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Phone</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Country</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Created At</th>"
                        html += "</tr>"
                    break;

                case 'Agreement':
                        html = "<tr class='text-white'>"
                        html += "<th class='bg-secondary rounded border border-white'>No</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Partner Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Agreement Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Agreement Type</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Start Date</th>"
                        html += "<th class='bg-secondary rounded border border-white'>End Date</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Partner PIC</th>"
                        html += "<th class='bg-secondary rounded border border-white'>ALL In PIC</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Created At</th>"
                        html += "</tr>"
                    break;
          
            }
            $('#thead-partner').html(html)

            axios.get(url)
                .then(function(response) {
                    var result = response.data;
                    $('#list-detail-partner .modal-title').html(result.title)
                    $('#listPartnerTable tbody').html(result.html_ctx)
                    
                    $('#listPartnerTable .detail').each(function(){
                        var link = '';
                        switch ($(this).data('type')) {
                            case 'partner':
                                link = "{{ url('/') }}/instance/corporate/" + $(this).data('corpid')
                                break;
                            case 'school':
                                link = "{{ url('/') }}/instance/school/" + $(this).data('schid')
                                break;
                            case 'university':
                                link = "{{ url('/') }}/instance/university/" + $(this).data('univid')
                                break;
                            case 'agreement':
                                link = "{{ url('/') }}/instance/corporate/" + $(this).data('corpid') + "/agreement/" + $(this).data('agreementid')
                                break;
                        
                        }

                           $(this).click(function() {
                                window.open(link, '_blank')
                            })
                    })

                    if(type == 'School'){
                        if(result.additional_content != '' || result.additional_content != null){
                                $('#additionalTable').removeClass('d-none')
                                $('#thead-additional').html(result.additional_header)
                                $('#listAdditionalTable tbody').html(result.additional_content)
                                
                                $("#listAdditionalTable .detail").each(function() {
                                    $(this).click(function() {
                                        var link = "{{ url('/') }}/instance/school/" + $(this).data('schid')
                                        window.open(link, '_blank')
                                    })
                                })
                        }else{
                            
                            $('#additionalTable').addClass('d-none')
                        }
                    }else{
                        $('#additionalTable').addClass('d-none')

                    }
                    swal.close()

                    $('#list-detail-partner').modal('show')

                }).catch(function(error) {
                    
                    notification('error', 'There was an error while processing your request. Please try again or contact your administrator.');

                })
        })
    })

    function checkPartnerStatusbyMonth() {
        let month = $('#partner_status_month').val()
        let type = $('#period-partnership').val()
        
        if(type == 'all'){
            month = null;
        }
        // console.log(type)
      
        let data = ({
            'partner': { 'total': {{$totalPartner}}, 'new': {{$newPartner}}, 'percentage': '0,00'},
            'school': { 'total': {{$totalSchool}}, 'new': {{$newSchool}}, 'percentage': '0,00'},       
            'university': { 'total': {{$totalUniversity}}, 'new': {{$newUniversity}}, 'percentage': '0,00'},       
            'agreement': { 'total': {{$totalAgreement}}, 'percentage': '0,00'}       
        });
        showLoading()

        // Axios here...
        axios.get('{{ url("api/partner/total/") }}/' + month + '/' + type)
            .then((response) => {

                var result = response.data.data
                var html = ""
                var no = 1;
                var percentage;
                var old;
                var news;

                data.partner.new = result.newPartner
                data.partner.total = result.totalPartner
                data.partner.percentage = result.percentagePartner
                data.school.new = result.newSchool
                data.school.total = result.totalSchool
                data.school.percentage = result.percentageSchool
                data.university.new = result.newUniversity
                data.university.total = result.totalUniversity
                data.university.percentage = result.percentageUniversity
                data.agreement.total = result.totalAgreement
                data.agreement.percentage = result.percentageAgreement

                $(".partner-status-detail").each(function(index) {
                    switch (index) {
                        case 0:
                            percentage = data.partner.percentage
                            old = data.partner.total
                            news = data.partner.new
                            break;
                        case 1:
                            percentage = data.school.percentage
                            old = data.school.total
                            news = data.school.new
                            break;
                        case 2:
                            percentage = data.university.percentage
                            old = data.university.total
                            news = data.university.new
                            break;
                  
                    }

                        if (news > old && news != 0 && old != 0) {
    
                            var textStyling = 'text-success'
                            var icon = "bi-arrow-up-short"
    
                        } else if (old == 0 && news != 0) {
    
                            var textStyling = 'text-success'
                            var icon = "bi-arrow-up-short"
                        
                        }else if (old == 0 && news == 0) {
    
                            var textStyling = ''
                            var icon = ""
    
                        } else {
    
                            var textStyling = 'text-danger'
                            var icon = "bi-arrow-down-short"
    
                        }

                        if(type == 'all'){
                            var html = '<span class="me-2 '+ textStyling +'">'+
                                            '<i class="bi '+ icon +'"></i>' +
                                            percentage + '%' +
                                        '</span><span>Since before</span>'
                        }else{
                            var html = '<span class="me-2 '+ textStyling +'">'+
                                            '<i class="bi '+ icon +'"></i>' +
                                            percentage + '%' +
                                        '</span><span>Since last month</span>'

                        }
    
                    
                    $(this).html(html)
                })

                $('#tot_partner').html(data.partner.total + ('<sup><span class="badge bg-primary text-white p-1 px-2 ms-2"><small>' + data.partner.new + ' New</small></span></sup>'))
                $('#tot_school').html(data.school.total + ('<sup><span class="badge bg-primary text-white p-1 px-2 ms-2"><small>' + data.school.new + ' New</small></span></sup>'))
                $('#tot_univ').html(data.university.total + ('<sup><span class="badge bg-primary text-white p-1 px-2 ms-2"><small>' + data.university.new + ' New</small></span></sup>'))
                $('#tot_agreement').html(data.agreement.total)

                swal.close();
                
            }, (error) => {
                
                notification('error', error);
            })
    }
    
    $(function() {

        checkPartnerStatusbyMonth()
    })


        
</script>
@endpush
