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
                    <input type="number" name="inv_price__nso" id="not_session_other_price" class="form-control" {{ $disabled }}
                        oninput="checkNotSessionOther()" value="{{ isset($invoice->inv_price) ? $invoice->inv_price : old('inv_price__nso') }}">
                </div>
                @error('inv_price__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_price_idr__nso" id="not_session_other_price_idr" class="form-control" readonly value="{{ isset($invoice->inv_price_idr) ? $invoice->inv_price_idr : old('inv_price_idr__nso') }}" {{ $disabled }}>
                </div>
                @error('inv_price_idr__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Early Bird</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="inv_earlybird__nso" id="not_session_other_early" class="form-control" value="{{ isset($invoice->inv_earlybird) ? $invoice->inv_earlybird : old('inv_earlybird__nso') }}" {{ $disabled }}
                        oninput="checkNotSessionOther()">
                </div>
                @error('inv_earlybird__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_earlybird_idr__nso" id="not_session_other_early_idr" class="form-control" readonly value="{{ isset($invoice->inv_earlybird_idr) ? $invoice->inv_earlybird_idr : old('inv_earlybird_idr__nso') }}" {{ $disabled }}>
                </div>
                @error('inv_earlybird_idr__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="inv_discount__nso" id="not_session_other_discount" class="form-control" value="{{ isset($invoice->inv_discount) ? $invoice->inv_discount : old('inv_discount__nso') }}" {{ $disabled }}
                        oninput="checkNotSessionOther()">
                </div>
                @error('inv_discount__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_discount_idr__nso" id="not_session_other_discount_idr" class="form-control" value="{{ isset($invoice->inv_discount_idr) ? $invoice->inv_discount_idr : old('inv_discount_idr__nso') }}" {{ $disabled }}
                        readonly>
                </div>
                @error('inv_discount_idr__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="inv_totalprice__nso" id="not_session_other_total" class="form-control" value="{{ isset($invoice->inv_totalprice) ? $invoice->inv_totalprice : old('inv_totalprice__nso') }}" {{ $disabled }}>
                </div>
                @error('inv_totalprice__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_totalprice_idr__nso" id="not_session_other_total_idr" class="form-control" readonly value="{{ isset($invoice->inv_totalprice_idr) ? $invoice->inv_totalprice_idr : old('inv_totalprice_idr__nso') }}" {{ $disabled }}>
                </div>
                @error('inv_totalprice_idr__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="inv_words__nso" id="not_session_other_word" value="{{ isset($invoice->inv_words) ? $invoice->inv_words : old('inv_words__nso') }}" {{ $disabled }}
                    class="form-control form-control-sm rounded mb-1" readonly>
                @error('inv_words__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <input type="text" name="inv_words_idr__nso" id="not_session_other_word_idr" value="{{ isset($invoice->inv_words_idr) ? $invoice->inv_words_idr : old('inv_words_idr') }}" {{ $disabled }}
                    class="form-control form-control-sm rounded" readonly>
                @error('inv_words_idr__nso')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
    function checkNotSessionOther() {
        let detail = $('#currency_detail').val()
        let kurs = $('#current_rate').val()
        let price = $('#not_session_other_price').val()
        let early = $('#not_session_other_early').val()
        let discount = $('#not_session_other_discount').val()
        let total = price - early - discount

        $('#not_session_other_price_idr').val(price * kurs)
        $('#not_session_other_early_idr').val(early * kurs)
        $('#not_session_other_discount_idr').val(discount * kurs)
        $('#not_session_other_total_idr').val(total * kurs)

        $('#not_session_other_early').val(early * kurs)
        $('#not_session_other_discount').val(discount * kurs)
        $('#not_session_other_total').val(total * kurs)

        $('#not_session_other_total').val(total)

        $('#total_idr').val(total * kurs)
        $('#total_other').val(total)

        $('#not_session_other_word').val(wordConverter(total) + ' ' + currencyText(detail))
        $('#not_session_other_word_idr').val(wordConverter(total * kurs) + ' Rupiah')
    }
</script>
