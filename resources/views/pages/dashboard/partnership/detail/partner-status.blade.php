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
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total Partner</h5>
                        <h4 class="m-0 p-0" id="tot_partner">
                            {{ $totalPartner }}<sup class="text-primary">({{$newPartner}} New)</sup>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total School</h5>
                        <h4 class="m-0 p-0" id="tot_school">
                            {{ $totalSchool }}<sup class="text-primary">({{$newSchool}} New)</sup>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total Univeristy</h5>
                        <h4 class="m-0 p-0" id="tot_univ">
                            {{ $totalUniversity }}<sup class="text-primary">({{$newUniversity}} New)</sup>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100">
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

<script>
    function checkPartnerStatusbyMonth() {
        let month = $('#partner_status_month').val()
      
        let data = ({
            'partner': { 'total': {{$totalPartner}}, 'new': {{$newPartner}}},
            'school': { 'total': {{$totalSchool}}, 'new': {{$newSchool}}},       
            'university': { 'total': {{$totalUniversity}}, 'new': {{$newUniversity}}},       
            'agreement': { 'total': {{$totalAgreement}} }       
        });
        Swal.showLoading()

        // Axios here...
        axios.get('{{ url("api/partner/total/") }}/' + month)
            .then((response) => {
                var result = response.data.data
                var html = ""
                var no = 1;

                swal.close()
                data.partner.new = result.newPartner
                data.school.new = result.newSchool
                data.university.new = result.newUniversity
                data.agreement.total = result.totalAgreement

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
