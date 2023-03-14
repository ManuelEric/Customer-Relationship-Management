<div class="card mb-3">
    <div class="card-body">
        <div class="row justify-content-end g-1 mb-2">
            <div class="col-md-2 text-end">
                <input type="month" name="" id="finance_status_month" class="form-control form-control-sm"
                    onchange="checkPartnerStatusbyMonth()" value="{{ date('Y-m') }}">
            </div>
        </div>
        <div class="row align-items-stretch g-3">
            <div class="col-md-2">
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
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
                                {{ $totalInvoice->sum('count_invoice') }}

                            </h4>
                            <h6 class="m-0">Rp. {{ number_format($totalInvoice->sum('total'), '2', ',', '.') }}</h6>
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
                            <h6 class="m-0">Rp. {{ number_format($totalReceipt->sum('total'), '2', ',', '.') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
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
                <div class="card rounded border h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
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

<script>
    function checkPartnerStatusbyMonth() {
        let month = $('#finance_status_month').val()

        const rupiah = (number)=>{
            return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
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
                var html = ""
                var no = 1;

                console.log(result)


                data['invoiceNeeded']['total'] = result.totalInvoiceNeeded
                data['refund']['total'] = result.totalRefundRequest
                data['outstanding']['total'] = result.totalOutstanding

                result.totalInvoice.forEach(function (item, index) {
                    data['invoice']['total'] += item.count_invoice
                    data['invoice']['amount'] += item.total
                })

                result.totalReceipt.forEach(function (item, index) {
                    data['receipt']['total'] += item.count_receipt
                    data['receipt']['amount'] += item.total
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

    checkPartnerStatusbyMonth()
</script>
