<div class="card">
    <div class="card-header">
        Invoice Detail
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="">Price <sup class="text-danger">*</sup></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_price_idr" id="not_session_idr_price" class="form-control" {{ $disabled }} 
                        oninput="checkNotSessionIDR()" value="{{ isset($invoice->inv_price_idr) ? $invoice->inv_price_idr : old('inv_price_idr') }}">
                </div>
                @error('inv_price_idr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Early Bird <sup class="text-danger">*</sup></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_earlybird_idr" id="not_session_idr_early" class="form-control" {{ $disabled }}
                        oninput="checkNotSessionIDR()" value="{{ isset($invoice->inv_earlybird_idr) ? $invoice->inv_earlybird_idr : old('inv_earlybird_idr') }}">
                </div>
                @error('inv_earlybird_idr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount <sup class="text-danger">*</sup></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_discount_idr" id="not_session_idr_discount" class="form-control" {{ $disabled }}
                        oninput="checkNotSessionIDR()" value="{{ isset($invoice->inv_discount_idr) ? $invoice->inv_discount_idr : old('inv_discount_idr') }}">
                </div>
                @error('inv_discount_idr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price <sup class="text-danger">*</sup></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_totalprice_idr" id="not_session_idr_total" class="form-control" value="{{ isset($invoice->inv_totalprice_idr) ? $invoice->inv_totalprice_idr : old('inv_totalnumber_idr') }}" {{ $disabled }}>
                </div>
                @error('inv_totalprice_idr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words <sup class="text-danger">*</sup></label>
                <input type="text" name="inv_words_idr" id="not_session_idr_word" {{ $disabled }}
                    class="form-control form-control-sm rounded" readonly value="{{ isset($invoice->inv_words_idr) ? $invoice->inv_words_idr : old('inv_words_idr') }}">
                @error('inv_words_idr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
    function checkNotSessionIDR() {
        let price = $('#not_session_idr_price').val()
        let early = $('#not_session_idr_early').val()
        let discount = $('#not_session_idr_discount').val()
        let total = price - early - discount

        $('#not_session_idr_total').val(total)

        $('#total_idr').val(total)
        $('#total_other').val(0)

        $('#not_session_idr_word').val(wordConverter(total) +' Rupiah')

        triggerInstallment()
    }
</script>
