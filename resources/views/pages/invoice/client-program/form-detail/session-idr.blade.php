<div class="card shadow">
    <div class="card-header">
        Session Detail
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="">Price/Hours</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_price_idr__si" id="session_idr_price" class="form-control" {{ $disabled }}
                        oninput="checkSessionIDR()" value="{{ isset($invoice->inv_price_idr) ? $invoice->inv_price_idr : old('inv_price_idr__si') }}">
                </div>
                @error('inv_price_idr__si')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-2 mb-3">
                <label for="">Session</label>
                <input type="number" name="session__si" id="session_idr_session" {{ $disabled }} value="{{ isset($invoice->session) ? $invoice->session : old('session__si') }}"
                    class="form-control form-control-sm rounded" oninput="checkSessionIDR()">
                @error('session__si')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-2 mb-3">
                <label for="">Duration/Minutes</label>
                <input type="number" name="duration__si" id="session_idr_duration" {{ $disabled }} value="{{ isset($invoice->duration) ? $invoice->duration : old('duration__si') }}"
                    class="form-control form-control-sm rounded" oninput="checkSessionIDR()">
                @error('duration__si')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_discount_idr__si" id="session_idr_discount" class="form-control" {{ $disabled }} value="{{ isset($invoice->inv_discount_idr) ? $invoice->inv_discount_idr : old('inv_discount_idr__si') }}"
                        oninput="checkSessionIDR()">
                </div>
                @error('inv_discount_idr__si')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="inv_totalprice_idr__si" id="session_idr_total" class="form-control" {{ $disabled }}
                        oninput="checkSessionIDR()" value="{{ isset($invoice->inv_totalprice_idr) ? $invoice->inv_totalprice_idr : old('inv_totalprice_idr__si') }}">
                </div>
                @error('inv_totalprice_idr__si')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="inv_words_idr__si" id="session_idr_word" class="form-control form-control-sm rounded {{ $disabled }}" value="{{ isset($invoice->inv_words_idr) ? $invoice->inv_words_idr : old('inv_words_idr__si') }}"
                    readonly>
                @error('inv_words_idr__si')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
    function checkSessionIDR() {
        let price = $('#session_idr_price').val()
        let session = $('#session_idr_session').val()
        let duration = $('#session_idr_duration').val()
        let discount = $('#session_idr_discount').val()
        let total = (price * session * (duration / 60)) - discount

        $('#session_idr_total').val(total)
        $('#total_idr').val(total)
        $('#total_other').val(0)

        $('#session_idr_word').val(wordConverter(total) +' Rupiah')
    }
</script>
