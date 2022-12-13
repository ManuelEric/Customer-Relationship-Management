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
                    <input type="number" name="" id="session_idr_price" class="form-control"
                        oninput="checkSessionIDR()">
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <label for="">Session</label>
                <input type="number" name="" id="session_idr_session"
                    class="form-control form-control-sm rounded" oninput="checkSessionIDR()">
            </div>
            <div class="col-md-2 mb-3">
                <label for="">Duration/Minutes</label>
                <input type="number" name="" id="session_idr_duration"
                    class="form-control form-control-sm rounded" oninput="checkSessionIDR()">
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Discount</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="session_idr_discount" class="form-control"
                        oninput="checkSessionIDR()">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Total Price</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" id="session_idr_total" class="form-control"
                        oninput="checkSessionIDR()">
                </div>
            </div>
            <div class="col-md-8 mb-3">
                <label for="">Words</label>
                <input type="text" name="" id="session_idr_word" class="form-control form-control-sm rounded"
                    readonly>
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
