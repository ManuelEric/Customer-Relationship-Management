@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
<style>
    .iti {
        display: block !important;
    }
</style>
@endpush

<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-3">
            <div class="col-md-2 text-end">
                <input type="month" name="" id="finance_status_month" class="form-control form-control-sm"
                    onchange="checkInvoiceStatusbyMonth()" value="{{ date('Y-m') }}">
            </div>
        </div>

        <div class="row align-items-stretch g-3">
            <div class="col-md-2">
                <div class="card rounded border h-100 card-finance cursor-pointer" data-finance-type="invoice-needed">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        @if($invoiceNeededToday > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger today"
                                style="font-size: 11px">
                                {{$invoiceNeededToday}}
                            </span>
                        @endif
                        <h5 class="m-0 p-0">Invoice Needed</h5>
                        <div id="invoice_needed" class="text-end">
                            <h3 class="m-0 p-0 text-warning">
                                {{ $totalInvoiceNeeded }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total <br> Invoice</h5>
                        <div id="tot_invoice" class="text-end">
                            <h4 class="m-0 p-0">
                                {{ $totalInvoice[0]['count_invoice'] + $totalInvoice[1]['count_invoice'] }}
                            </h4>
                            <h6 class="m-0">Rp. {{ number_format($totalInvoice[0]['total'] +  $totalInvoice[1]['total']) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="m-0 p-0">Total <br> Receipt</h5>
                        <div id="tot_receipt" class="text-end">
                            <h4 class="m-0 p-0 text-info">
                                {{ $totalReceipt->sum('count_receipt') }}
                            </h4>
                            <h6 class="m-0">Rp. {{ number_format($totalReceipt->sum('total')) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card rounded border h-100 card-finance cursor-pointer" data-finance-type="outstanding">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        @if($outstandingToday > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger today"
                                style="font-size: 11px">
                                {{$outstandingToday}}
                            </span>
                        @endif
                        <h5 class="m-0 p-0">Outstanding Payment</h5>
                        <div id="reminder_need" class="text-end">
                            <h3 class="m-0 p-0 text-danger" id="tot_outstanding">
                                {{ $unpaidPayments->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card rounded border h-100 card-finance cursor-pointer" data-finance-type="refund-request">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        @if($refundRequestToday > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger today"
                                style="font-size: 11px">
                                {{$refundRequestToday}}
                            </span>
                        @endif
                        <h5 class="m-0 p-0">Refund Request</h5>
                        <div id="refund_request" class="text-end">
                            <h3 class="m-0 p-0 text-danger" id="tot_refund">
                                {{ $totalRefundRequest }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail -->
<div class="modal modal-lg fade" id="list-detail-finance" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><!-- title here --></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
                <div class="row justify-content-end mb-2">
                    <div class="col-md-3">
                    </div>
                </div>
                <div class="overflow-auto" style="height: 400px">
                    <table class="table table-striped table-hover" id="listFinanceTable">
                        <thead class="text-center" id="thead-finance">
                            {{-- Head table --}}
                        </thead>
                        <tbody id="tbody-finance">
                            <!-- content here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reminderModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Reminder
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                {{-- <form action="" method="POST" id="reminderForm"> --}}
                    @csrf
                    {{-- @method('put') --}}
                    <div class="form-phone">

                        {{-- <label for="">Phone Number Parent</label>
                        <input type="text" name="phone" id="phone" class="form-control w-100"> --}}
                    </div>
                    <input type="hidden" name="client_id" id="client_id">
                    <input type="hidden" name="clientprog_id" id="clientprog_id">
                    <input type="hidden" name="program_name" id="program_name">
                    <input type="hidden" name="invoice_duedate" id="invoice_duedate">
                    <input type="hidden" name="total_payment" id="total_payment">
                    <input type="hidden" name="payment_method" id="payment_method">
                    <input type="hidden" name="parent_id" id="parent_id">
                    <input type="hidden" name="send_to">
                    <input type="hidden" name="target_phone">
                    {{-- <hr> --}}
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                          data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="submit" onclick="sendWhatsapp()" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Send</button>
                    </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
 </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script>
    function initiate()
    {
        var parent = document.querySelector("#prPhone");
        var child = document.querySelector("#chPhone");
    
        const phoneInput1 = window.intlTelInput(parent, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            initialCountry: 'id',
            onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
        });
    
        const phoneInput2 = window.intlTelInput(child, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            initialCountry: 'id',
            onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
        });

        $("#prPhone").on('keyup', function(e) {
            var number1 = phoneInput1.getNumber();
            $("#prPhoneR").val(number1);
            $("input[name=target_phone]").val(number1);
        });

        $("#chPhone").on('keyup', function(e) {
            var number2 = phoneInput2.getNumber();
            $("#chPhoneR").val(number2);
            $("input[name=target_phone]").val(number2);
        });
    }
</script>
<script>

    $(document).on('click', 'input[id=prPhoneInput]', function () {
        sendTo('parent');
    });

    $(document).on('click', 'input[id=chPhoneInput]', function () {
        sendTo('child');
    });

    function sendTo(recipient)
    {
        var prPhoneStatus = chPhoneStatus = false;
        switch (recipient) {
            case "parent":
                var phone = $("#prPhoneR").val();
                prPhoneStatus = true;
                break;

            case "child":
                var phone = $("#chPhoneR").val();
                chPhoneStatus = true;
                break;
        }

        $('#prPhoneInput').prop('checked', prPhoneStatus);
        $('#chPhoneInput').prop('checked', chPhoneStatus);
        $("input[name=send_to]").val(recipient);
        $("input[name=target_phone]").val(phone);
    }

    $(".card-finance").each(function() {
        $(this).click(function() {
            // showLoading()

            let type = $(this).data('finance-type')
            let month = $('#finance_status_month').val()
            
            let url = window.location.origin + '/api/finance/detail/'+ month +'/'+ type;
            var html;

            switch (type) {
                case 'invoice-needed':
                        html = "<tr class='text-white'>"
                        html += "<th class='bg-secondary rounded border border-white'>No</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Client Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Program Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Success Date</th>"
                        html += "<th class='bg-secondary rounded border border-white'>PIC</th>"
                        html += "</tr>"
                    break;
                
                case 'outstanding':
                        html = "<tr class='text-white'>"
                        html += "<th class='bg-secondary rounded border border-white'>No</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Client Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Reminder</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Invoice ID</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Type</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Program Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Installment</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Invoice Duedate</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Amount</th>"
                        html += "</tr>"
                    break;

                case 'refund-request':
                        html = "<tr class='text-white'>"
                        html += "<th class='bg-secondary rounded border border-white'>No</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Client Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Receipt ID</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Program Name</th>"
                        html += "<th class='bg-secondary rounded border border-white'>Refund Date</th>"
                        html += "<th class='bg-secondary rounded border border-white'>PIC</th>"
                        html += "</tr>"
                    break;          
            }
            $('#thead-finance').html(html)

            axios.get(url)
                .then(function(response) {
                    var result = response.data;
                    $('#list-detail-finance .modal-title').html(result.title)
                    $('#listFinanceTable tbody').html(result.html_ctx)

                    $('#phone').val('');
                    $("#listFinanceTable .reminder").each(function() {
                         $(this).click(function() {
                            var client_id = null;
                            if($(this).data('clientid') != undefined){
                                client_id = $(this).data('clientid');
                                var parent_phone = result.reminder[$(this).data('clientid')].parent_phone;
                                var child_phone = result.reminder[$(this).data('clientid')].child_phone;
                                var parent_id = result.reminder[$(this).data('clientid')].parent_id;
                                $('input[name=target_phone]').val(parent_id == null ? child_phone : parent_phone);
                                $('input[name=send_to]').val(parent_id == null ? 'child' : 'parent');
                                
                                $('#client_id').val($(this).data('clientid'))
                                // $('#fullname').val(result.reminder[$(this).data('clientid')].parent_fullname) //no longer in use
                                $('#program_name').val(result.reminder[$(this).data('clientid')].program_name)
                                $('#invoice_duedate').val(result.reminder[$(this).data('clientid')].invoice_duedate)
                                $('#total_payment').val(result.reminder[$(this).data('clientid')].total_payment)
                                $('#clientprog_id').val(result.reminder[$(this).data('clientid')].clientprog_id)
                                $('#payment_method').val(result.reminder[$(this).data('clientid')].payment_method)
                                $('#parent_id').val(result.reminder[$(this).data('clientid')].parent_id)
                                $('#client_id').val(result.reminder[$(this).data('clientid')].client_id)

                                var form_phone = checked = '';
                                if (parent_id != null) {

                                    
                                    form_phone += '<h5>Select a recipient</h5>' +
                                        '<div class="form-check form-check-inline ms-4">' +
                                            '<input type="radio" class="form-check-input" name="recipients" value="parent" checked id="prPhoneInput">' +
                                            '<label class="class="form-check-label" for="prPhoneInput">' +
                                                '<div class="form-group">' +
                                                    '<label for="">Parent</label>' +
                                                    '<input type="text" name="pr_phone" class="form-control w-100" value="'+ parent_phone +'" id="prPhone">' +
                                                    '<input type="hidden" id="prPhoneR" name="pr_phone_r" value="'+ parent_phone +'">' +
                                                '</div>' +
                                            '</label>' +
                                        '</div>';
                                } else {
                                    checked = 'checked';
                                }

                                form_phone += '<div class="form-check form-check-inline ms-4">' +
                                            '<input type="radio" class="form-check-input" name="recipients" value="children" '+ checked +' id="chPhoneInput">' +
                                            '<label class="class="form-check-label" for="chPhoneInput">' +
                                                '<div class="form-group">' +
                                                    '<label for="">Child</label>' +
                                                    '<input type="text" name="ch_phone" class="form-control w-100" value="'+ child_phone +'" id="chPhone">' +
                                                    '<input type="hidden" id="chPhoneR" name="ch_phone_r" value="'+ child_phone +'">' +
                                                '</div>' +
                                            '</label>' +
                                        '</div>';

                                $(".form-phone").html(form_phone)

                                initiate();
                            }
                            

                            // $("#reminderForm").attr("action", '{{ url("/") }}/invoice/client-program/'+result.reminder[$(this).data('clientid')].clientprog_id+'/remind/by/whatsapp');
                        })
                    })
                    
                    $("#listFinanceTable .detail").each(function() {
                        var link = '';

                        switch ($(this).data('type')) {
                                case 'invoice-needed':
                                    switch ($(this).data('typeprog')) {
                                        case 'sch_prog':
                                            link = "{{ url('/') }}/invoice/school-program/" + $(this).data('clientprog') + "/detail/create"
                                            break;
                                        case 'partner_prog':
                                            link = "{{ url('/') }}/invoice/corporate-program/" + $(this).data('clientprog') + "/detail/create"
                                            break;
                                        case 'referral':
                                            link = "{{ url('/') }}/invoice/referral/" + $(this).data('clientprog') + "/detail/create"
                                            break;
                                        case 'client_prog':
                                            link = "{{ url('/') }}/invoice/client-program/create?prog=" + $(this).data('clientprog')
                                            break;
                                    }
                                    break;

                                case 'outstanding':
                                    switch ($(this).data('typeprog')) {
                                        case 'sch_prog':
                                            link = "{{ url('/') }}/invoice/school-program/" + $(this).data('clientprog') + "/detail/" + $(this).data('invid')
                                            break;
                                        case 'partner_prog':
                                            link = "{{ url('/') }}/invoice/corporate-program/" + $(this).data('clientprog') + "/detail/" + $(this).data('invid')
                                            break;
                                        case 'referral':
                                            link = "{{ url('/') }}/invoice/referral/" + $(this).data('clientprog') + "/detail/" + $(this).data('invid')
                                            break;
                                        case 'client_prog':
                                            link = "{{ url('/') }}/invoice/client-program/" + $(this).data('clientprog')
                                            break;
                                    }
                                    break;
                                break;

                                case 'refund-request':
                                    switch ($(this).data('typeprog')) {
                                        case 'sch_prog':
                                            link = "{{ url('/') }}/invoice/school-program/" + $(this).data('clientprog') + "/detail/" + $(this).data('invid')
                                            break;
                                        case 'partner_prog':
                                            link = "{{ url('/') }}/invoice/corporate-program/" + $(this).data('clientprog') + "/detail/" + $(this).data('invid')
                                            break;
                                        case 'referral':
                                            link = "{{ url('/') }}/invoice/referral/" + $(this).data('clientprog') + "/detail/" + $(this).data('invid')
                                            break;
                                        case 'client_prog':
                                            link = "{{ url('/') }}/invoice/client-program/" + $(this).data('clientprog')
                                            break;
                                    }
                                    break;
                                break;
                            
                                
                            }
                            $(this).click(function() {
                                window.open(link, '_blank')
                            })
                        })
                    
                        swal.close()

                        $('#list-detail-finance').modal('show')

                    }).catch(function(error) {
                        
                        notification('error', 'There was an error while processing your request. Please try again or contact your administrator.');

                    })
        })
    })

    function checkInvoiceStatusbyMonth() {
        
        let month = $('#finance_status_month').val()

        let today = moment().format('YYYY-MM')
       
        if(month != today){
            $('.today').addClass('d-none')
        }else{
            $('.today').removeClass('d-none')
        }

        const rupiah = (number)=>{
            return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
            }).format(number);
        }

        // Axios here...
        let data = {
            'invoiceNeeded':{
                'total': 0,
            },
            'invoice': {
                'total': 0,
                'amount': 0
            },
            'receipt': {
                'total': 0,
                'amount': 0
            },
            'outstanding': {
                'total': 0,
            },
            'refund':{
                'total': 0
            }
        }

        function total(arr) {
            if(!Array.isArray(arr)) return;
            return arr.reduce((a, v)=>a + v);
        }

        axios.get('{{ url("api/finance/total/") }}/' + month)
            .then((response) => {
                var result = response.data.data
                var html = '';
                var no = 1;

                data['invoiceNeeded']['total'] = result.totalInvoiceNeeded
                data['refund']['total'] = result.totalRefundRequest
                data['outstanding']['total'] = parseInt(result.totalOutstanding)

                result.totalInvoice.forEach(function (item, index) {
                    data['invoice']['total'] += parseInt(item.count_invoice)
                    data['invoice']['amount'] += parseInt(item.total)
                })

                result.totalReceipt.forEach(function (item, index) {
                    data['receipt']['total'] += parseInt(item.count_receipt)
                    data['receipt']['amount'] += parseInt(item.total)
                })

                $('#invoice_needed').html(
                    '<h3 class="m-0 p-0 text-warning">' +
                    data.invoiceNeeded.total +
                    '</h3>' 
                )

                $('#tot_invoice').html(
                    '<h4 class="m-0 mb-1 p-0 text-info">' +
                    data.invoice.total +
                    '</h4>' +
                    '<h6 class = "m-0">' +
                    rupiah(data.invoice.amount) +
                    '</h6>'
                )

                $('#tot_receipt').html(
                    '<h4 class="m-0 mb-1 p-0 text-info">' +
                    data.receipt.total +
                    '</h4>' +
                    '<h6 class = "m-0">' +
                    rupiah(data.receipt.amount) +
                    '</h6>'
                )

                $('#tot_outstanding').html(data.outstanding.total)
                $('#tot_refund').html(data.refund.total)

                swal.close()
            }, (error) => {
                console.log(error)
                swal.close()
            })
            
    }

    function sendWhatsapp()
    {
        var clientprog = $('#clientprog_id').val();
        var link = '{{ url("/") }}/invoice/client-program/'+clientprog+'/remind/by/whatsapp';
            axios.post(link, {
                parent_fullname : $('#fullname').val(),
                phone : $('input[name=target_phone]').val(),
                program_name : $('#program_name').val(),
                invoice_duedate : $('#invoice_duedate').val(),
                total_payment : $('#total_payment').val(),
                payment_method : $('#payment_method').val(),
                parent_id : $('#parent_id').val(),
                client_id : $('#client_id').val(),
                sendTo : $('input[name=send_to]').val(),
            })
            .then(function(response) {
                swal.close();
                            
                let obj = response.data;
                var link = obj.link;
                window.open(link)
            })
            .catch(function(error) {
                swal.close();
                notification('error', error)
            })
    }
    // checkInvoiceStatusbyMonth()

</script>
@endpush
