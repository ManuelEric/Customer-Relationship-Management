<div class="card shadow">
    <div class="card-header">
        Session Detail
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="">Price/Hours</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="inv_price__so" id="session_other_price" class="form-control" {{ $disabled }}
                        oninput="checkSessionOther()" value="{{ isset($invoice->inv_price) ? $invoice->inv_price : old('inv_price__so') }}">
                </div>
                @error('inv_price__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror

                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_price_idr__so" id="session_other_price_idr" class="form-control" readonly {{ $disabled }} value="{{ isset($invoice->inv_price_idr) ? $invoice->inv_price_idr : old('inv_price_idr__so') }}">
                </div>
                @error('inv_price_idr__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-2 mb-3">
                <label for="">Session</label>
                <input type="number" name="session__so" id="session_other_session" {{ $disabled }} value="{{  i }}sset($invoice->session) ? $invoice->session : old('session__so') }}"
                    class="form-control form-control-sm rounded" oninput="checkSessionOther()">
                @error('session__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-2 mb-3">
                <label for="">Duration/Minutes</label>
                <input type="number" name="duration__so" id="session_other_duration" {{ $disabled }} value="{{  i }}sset($invoice->duration) ? $invoice->duration : old('duration__so') }}"
                    class="form-control form-control-sm rounded" oninput="checkSessionOther()">
                @error('duration__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="inv_discount__so" id="session_other_discount" class="form-control" {{ $disabled }} value="{{  i }}sset($invoice->inv_discount) ? $invoice->inv_discount : old('inv_discount__so') }}"
                        oninput="checkSessionOther()">
                </div>
                @error('inv_discount__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_discount_idr__so" id="session_other_discount_idr" class="form-control" readonly {{ $disabled }} value="{{ isset($invoice->inv_discount_idr) ? $invoice->inv_discount_idr : old('inv_discount_idr__so') }}">
                </div>
                @error('inv_discount_idr__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="inv_totalprice__so" id="session_other_total" class="form-control" {{ $disabled }} value="{{  i }}sset($invoice->inv_totalprice) ? $invoice->inv_totalprice : old('inv_totalprice__so') }}">
                </div>
                @error('inv_totalprice__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_totalprice_idr__so" id="session_other_total_idr" class="form-control" readonly {{ $disabled }} value="{{ isset($invoice->inv_totalprice_idr) ? $invoice->inv_totalprice_idr : old('inv_totalprice_idr__so') }}">
                </div>
                @error('inv_totalprice_idr__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="inv_words__so" id="session_other_word" value="{{  isset($invoice->inv_words) ? $invoice->inv_words : old('inv_words__so') }}"
                    class="form-control form-control-sm rounded mb-1" readonly {{ $disabled }}>
                @error('inv_words__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                <input type="text" name="inv_words_idr__so" id="session_other_word_idr" value="{{ isset($invoice->inv_words_idr) ? $invoice->inv_words_idr : old('inv_words_idr__so') }}"
                    class="form-control form-control-sm rounded" readonly {{ $disabled }}>
                @error('inv_words_idr__so')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
    function checkSessionOther() {
        let detail = $('#currency_detail').val()
        let kurs = $('#current_rate').val()
        let price = $('#session_other_price').val()
        let session = $('#session_other_session').val()
        let duration = $('#session_other_duration').val()
        let discount = $('#session_other_discount').val()
        let total = (price * session * (duration / 60)) - discount

        $('#session_other_price_idr').val(price * kurs)
        $('#session_other_discount_idr').val(discount * kurs)
        $('#session_other_total_idr').val(total * kurs)
        $('#session_other_total').val(total)

        $('#total_idr').val(total * kurs)
        $('#total_other').val(total)

        $('#session_other_word').val(wordConverter(total) + currencyText(detail))
        $('#session_other_word_idr').val(wordConverter(total * kurs) + 'Rupiah')
    }
</script>
