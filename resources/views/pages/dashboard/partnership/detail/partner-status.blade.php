<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-2">
            <div class="col-md-2 text-end">
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
                            {{ $totalPartner }}<sup class="text-primary">({{$newPartner}} New)</sup>
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
                        <h5 class="m-0 p-0">Total School</h5>
                        <h4 class="m-0 p-0" id="tot_school">
                            {{ $totalSchool }}<sup class="text-primary">({{$newSchool}} New)</sup>
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
                        <h5 class="m-0 p-0">Total Univeristy</h5>
                        <h4 class="m-0 p-0" id="tot_univ">
                            {{ $totalUniversity }}<sup class="text-primary">({{$newUniversity}} New)</sup>
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
                <div class="overflow-auto" style="height: 400px">
                    <table class="table table-bordered table-hover" id="listPartnerTable">
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

<script>

    $(".card-partner").each(function() {
        $(this).click(function() {
            showLoading()

            let type = $(this).data('partner-type')
            let month = $('#partner_status_month').val()
            
            let url = window.location.origin + '/api/partner/detail/'+ month +'/'+ type;
            var html;

            switch (type) {
                case 'Partner':
                        html = "<tr>"
                        html += "<th>No</th>"
                        html += "<th>Corporate Name</th>"
                        html += "<th>Email</th>"
                        html += "<th>Office Number</th>"
                        html += "<th>Type</th>"
                        html += "<th>Country Type</th>"
                        html += "<th>Created At</th>"
                        html += "</tr>"
                    break;
                
                case 'School':
                        html = "<tr>"
                        html += "<th>No</th>"
                        html += "<th>School Name</th>"
                        html += "<th>Type</th>"
                        html += "<th>City</th>"
                        html += "<th>Location</th>"
                        html += "<th>Created At</th>"
                        html += "</tr>"
                    break;

                case 'University':
                        html = "<tr>"
                        html += "<th>No</th>"
                        html += "<th>University ID</th>"
                        html += "<th>University Name</th>"
                        html += "<th>Address</th>"
                        html += "<th>Email</th>"
                        html += "<th>Phone</th>"
                        html += "<th>Country</th>"
                        html += "<th>Created At</th>"
                        html += "</tr>"
                    break;

                case 'Agreement':
                        html = "<tr>"
                        html += "<th>No</th>"
                        html += "<th>Partner Name</th>"
                        html += "<th>Agreement Name</th>"
                        html += "<th>Agreement Type</th>"
                        html += "<th>Start Date</th>"
                        html += "<th>End Date</th>"
                        html += "<th>Partner PIC</th>"
                        html += "<th>ALL In PIC</th>"
                        html += "<th>Created At</th>"
                        html += "</tr>"
                    break;
          
            }
            $('#thead-partner').html(html)

            axios.get(url)
                .then(function(response) {
                    var result = response.data;
                    console.log(result)
                    $('#list-detail-partner .modal-title').html(result.title)
                    $('#listPartnerTable tbody').html(result.html_ctx)
                    swal.close()

                    $('#list-detail-partner').modal('show')

                }).catch(function(error) {
                    
                    notification('error', 'There was an error while processing your request. Please try again or contact your administrator.');

                })
        })
    })

    function checkPartnerStatusbyMonth() {
        let month = $('#partner_status_month').val()
      
        let data = ({
            'partner': { 'total': {{$totalPartner}}, 'new': {{$newPartner}}, 'percentage': '0,00'},
            'school': { 'total': {{$totalSchool}}, 'new': {{$newSchool}}, 'percentage': '0,00'},       
            'university': { 'total': {{$totalUniversity}}, 'new': {{$newUniversity}}, 'percentage': '0,00'},       
            'agreement': { 'total': {{$totalAgreement}}, 'percentage': '0,00'}       
        });
        Swal.showLoading()

        // Axios here...
        axios.get('{{ url("api/partner/total/") }}/' + month)
            .then((response) => {
                var result = response.data.data
                var html = ""
                var no = 1;
                var percentage;

                console.log(result)
                swal.close()

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
                            break;
                        case 1:
                            percentage = data.school.percentage
                            break;
                        case 2:
                            percentage = data.university.percentage
                            break;
                        case 3:
                            percentage = data.agreement.percentage
                            break;
                  
                    }
                        var textStyling = 'text-success'
                        var icon = "bi-arrow-up-short"
                        var html = '<span class="me-2 '+ textStyling +'">'+
                                        '<i class="bi '+ icon +'"></i>' +
                                        percentage + '%' +
                                    '</span><span>Since before</span>'
                    
                    $(this).html(html)
                })

                $('#tot_partner').html(data.partner.total + (!!data.partner.new ? '<sup class="text-primary">(' + data.partner.new + ' New)</sup>' : ''))
                $('#tot_school').html(data.school.total + (!!data.school.new ? '<sup class="text-primary">(' + data.school.new + ' New)</sup>' : ''))
                $('#tot_univ').html(data.university.total + (!!data.university.new ? '<sup class="text-primary">(' + data.university.new + ' New)</sup>' : ''))
                $('#tot_agreement').html(data.agreement.total)
            }, (error) => {
                console.log(error)
                swal.close()
            })
            
    

    }

    checkPartnerStatusbyMonth()


        
</script>
