<div class="card">
    <div class="card-header">
        Invoice Detail
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="">Price<sup class="text-danger">*</sup></label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="invb2b_price" id="invoice_other_price" class="form-control"
                        oninput="checkInvoiceOther()"
                        value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_price : old('invb2b_price') }}"
                        {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                </div>
                    @error('invb2b_price')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="invb2b_priceidr_other" id="invoice_other_price_idr" class="form-control" 
                        value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_priceidr : old('invb2b_priceidr_other') }}"
                        readonly
                        {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                </div>
                    @error('invb2b_priceidr_other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Participants<sup class="text-danger">*</sup></label>
                <div class="input-group input-group-sm mb-1">
                    <input type="number" name="invb2b_participants_other" id="invoice_other_participant" class="form-control"
                        oninput="checkInvoiceOther()"
                        value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_participants : old('invb2b_participants_other') }}"
                        {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                        <span class="input-group-text" id="basic-addon1">
                            Person
                        </span>
                </div>
                    @error('invb2b_participants_other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="invb2b_disc" id="invoice_other_discount" class="form-control"
                        oninput="checkInvoiceOther()"
                        value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_disc : old('invb2b_price') }}"
                        {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                </div>
                    @error('invb2b_disc')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="invb2b_discidr_other" id="invoice_other_discount_idr" class="form-control"
                        value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_discidr : old('invb2b_discidr_other') }}"
                        readonly
                        {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                </div>
                    @error('invb2b_discidr_other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="invb2b_totprice" id="invoice_other_total" class="form-control"
                        value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_totprice : old('invb2b_totprice') }}"
                        {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                </div>
                    @error('invb2b_totprice')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="invb2b_totpriceidr_other" id="invoice_other_total_idr" class="form-control" 
                        value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_totpriceidr : old('invb2b_totpriceidr_other') }}"
                        readonly
                        {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                </div>
                    @error('invb2b_totpriceidr_other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="invb2b_words" id="invoice_other_word"
                    class="form-control form-control-sm rounded mb-1" 
                    value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_words : old('invb2b_words') }}"
                    readonly {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                @error('invb2b_words')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <input type="text" name="invb2b_wordsidr_other" id="invoice_other_word_idr"
                    class="form-control form-control-sm rounded" 
                    value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_wordsidr : old('invb2b_wordsidr_other') }}"
                    readonly {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                @error('invb2b_wordsidr_other')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
    function checkInvoiceOther() {
        let detail = $('#currency_detail').val()
        let kurs = $('#current_rate').val()
        let price = $('#invoice_other_price').val()
        let participant = $('#invoice_other_participant').val()
        let discount = $('#invoice_other_discount').val()
        let total = (price * participant) - discount
        let is_full_amount = $('#is_full_amount').val()

        $('#invoice_other_price_idr').val(price * kurs)
        $('#invoice_other_discount_idr').val(discount * kurs)
        
         if(is_full_amount == 1){
            $('#invoice_other_total_idr').val((price * kurs)-(discount * kurs))
            $('#invoice_other_total').val(price - discount)
            $('#total_idr').val((price * kurs)-(discount * kurs))
            $('#total_other').val(price)

            $('#invoice_other_word').val(wordConverter(price - discount) + ' ' + currencyText(detail))
            $('#invoice_other_word_idr').val(wordConverter((price * kurs)-(discount * kurs))+' Rupiah')
         }else{
            $('#invoice_other_total_idr').val(total * kurs)
            $('#invoice_other_total').val(total)
            $('#total_idr').val(total * kurs)
            $('#total_other').val(total)

            $('#invoice_other_word').val(wordConverter(total) + ' ' + currencyText(detail))
            $('#invoice_other_word_idr').val(wordConverter(total * kurs)+' Rupiah')
         }
    }

    $("#invoice_other_total").on('keyup', function () {
        var val = $(this).val()
        $("#invoice_other_word").val(wordConverter(val) + ' ' + currencyText(detail))
        $('#invoice_other_word_idr').val(wordConverter(val * kurs)+' Rupiah')
    })
</script>
