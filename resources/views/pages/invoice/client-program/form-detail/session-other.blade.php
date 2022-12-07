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
                    <input type="number" name="" id="session_other_price" class="form-control"
                        oninput="checkSessionOther()">
                </div>

                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="session_other_price_idr" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <label for="">Session</label>
                <input type="number" name="" id="session_other_session"
                    class="form-control form-control-sm rounded" oninput="checkSessionOther()">
            </div>
            <div class="col-md-2 mb-3">
                <label for="">Duration/Minutes</label>
                <input type="number" name="" id="session_other_duration"
                    class="form-control form-control-sm rounded" oninput="checkSessionOther()">
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="session_other_discount" class="form-control"
                        oninput="checkSessionOther()">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="session_other_discount_idr" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" id="session_other_total" class="form-control">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="session_other_total_idr" class="form-control" readonly>
                </div>
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="" id="session_other_word"
                    class="form-control form-control-sm rounded mb-1" readonly>
                <input type="text" name="" id="session_other_word_idr"
                    class="form-control form-control-sm rounded" readonly>
            </div>
        </div>
    </div>
</div>

<script>
    function checkSessionOther() {
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

        $('#session_other_word').val(wordConverter(total))
        $('#session_other_word_idr').val(wordConverter(total * kurs))
    }
</script>
