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
                    <input type="number" name="invb2b_totprice" id="invoice_other_total" class="form-control" oninput="checkInvoiceOther()"
                        value="{{ (isset($invoiceRef)) ? $invoiceRef->invb2b_totprice : old('invb2b_totprice') }}"
                        {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                </div>
                    @error('invb2b_totprice')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="invb2b_totpriceidr_other" id="invoice_other_total_idr" class="form-control" 
                        value="{{ (isset($invoiceRef)) ? $invoiceRef->invb2b_totpriceidr : old('invb2b_totpriceidr_other') }}"
                        readonly
                        {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                </div>
                    @error('invb2b_totpriceidr_other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="invb2b_words" id="invoice_other_word"
                    class="form-control form-control-sm rounded mb-1" 
                    value="{{ (isset($invoiceRef)) ? $invoiceRef->invb2b_words : old('invb2b_words') }}"
                    readonly {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                @error('invb2b_words')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <input type="text" name="invb2b_wordsidr_other" id="invoice_other_word_idr"
                    class="form-control form-control-sm rounded" 
                    value="{{ (isset($invoiceRef)) ? $invoiceRef->invb2b_wordsidr : old('invb2b_wordsidr_other') }}"
                    readonly {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                @error('invb2b_wordsidr_other')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
        </div>
    </div>
</div>
@push('scripts')
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
@endpush
