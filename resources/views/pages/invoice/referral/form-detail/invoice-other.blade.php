<div class="card">
    <div class="card-header">
        Invoice Detail
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="invoice_other_total" class="form-control" oninput="checkInvoiceOther()">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="invoice_other_total_idr" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="" id="invoice_other_word"
                    class="form-control form-control-sm rounded mb-1" readonly>
                <input type="text" name="" id="invoice_other_word_idr"
                    class="form-control form-control-sm rounded" readonly>
            </div>
        </div>
    </div>
</div>

<script>
    function checkInvoiceOther() {
        let detail = $('#currency_detail').val()
        let kurs = $('#current_rate').val()
        let total = $('#invoice_other_total').val()

        $('#invoice_other_total_idr').val(total * kurs)
        $('#invoice_other_total').val(total)

        $('#invoice_other_word').val(wordConverter(total) + ' ' + currencyText(detail))
        $('#invoice_other_word_idr').val(wordConverter(total * kurs) +' Rupiah')
    }
</script>
