<div class="card">
    <div class="card-header">
        Invoice Detail
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="">Price</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="invoice_other_price" class="form-control"
                        oninput="checkInvoiceOther()">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="invoice_other_price_idr" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Participants</label>
                <div class="input-group input-group-sm mb-1">
                    <input type="number" name="" id="invoice_other_participant" class="form-control"
                        oninput="checkInvoiceOther()">
                        <span class="input-group-text" id="basic-addon1">
                            Person
                        </span>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="invoice_other_discount" class="form-control"
                        oninput="checkInvoiceOther()">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="invoice_other_discount_idr" class="form-control"
                        readonly>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="invoice_other_total" class="form-control">
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
        let kurs = $('#current_rate').val()
        let price = $('#invoice_other_price').val()
        let participant = $('#invoice_other_participant').val()
        let discount = $('#invoice_other_discount').val()
        let total = (price * participant) - discount

        $('#invoice_other_price_idr').val(price * kurs)
        $('#invoice_other_discount_idr').val(discount * kurs)
        $('#invoice_other_total_idr').val(total * kurs)
        $('#invoice_other_total').val(total)
        $('#total_idr').val(total * kurs)
        $('#total_other').val(total)

        $('#invoice_other_word').val(wordConverter(total))
        $('#invoice_other_word_idr').val(wordConverter(total * kurs))
    }
</script>
