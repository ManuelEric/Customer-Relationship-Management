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
                    <input type="number" name="" id="not_session_other_price" class="form-control"
                        oninput="checkNotSessionOther()">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="not_session_other_price_idr" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Early Bird</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="not_session_other_early" class="form-control"
                        oninput="checkNotSessionOther()">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="not_session_other_early_idr" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="not_session_other_discount" class="form-control"
                        oninput="checkNotSessionOther()">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="not_session_other_discount_idr" class="form-control"
                        readonly>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="not_session_other_total" class="form-control">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="not_session_other_total_idr" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="" id="not_session_other_word"
                    class="form-control form-control-sm rounded mb-1" readonly>
                <input type="text" name="" id="not_session_other_word_idr"
                    class="form-control form-control-sm rounded" readonly>
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
        $('#not_session_other_total').val(total)

        $('#total_idr').val(total * kurs)
        $('#total_other').val(total)

        $('#not_session_other_word').val(wordConverter(total) + ' Rupiah')
            $('#not_session_other_word_idr').val(wordConverter(total * kurs) + ' ' + currencyText(detail))
        }
</script>
