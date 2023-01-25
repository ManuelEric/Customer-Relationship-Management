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
                            130<sup class="text-primary">(4 New)</sup>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total School</h5>
                        <h4 class="m-0 p-0">
                            130<sup class="text-primary">(4 New)</sup>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total Univeristy</h5>
                        <h4 class="m-0 p-0">
                            130<sup class="text-primary">(4 New)</sup>
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
                        <h4 class="m-0 p-0 text-danger">
                            10
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

        // Axios here...
        let data = {
            'partner': {
                'total': 110,
                'new': 5
            }
        }

        $('#tot_partner').html(data.partner.total + '<sup class="text-primary">(' + data.partner.new + ' New)</sup>')
    }

    checkPartnerStatusbyMonth()
</script>
