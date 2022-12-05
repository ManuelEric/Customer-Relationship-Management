<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="">
            Installment
        </div>
        <button class="btn btn-sm btn-outline-primary" onclick="addInstallmentOther()">
            <i class="bi bi-plus"></i>
        </button>
    </div>
    <div class="card-body " id="installment_content_other">
        <div class="row g-2 installment-others mb-3">
            <div class="col-md-3">
                <label for="">Name</label>
                <input type="text" name="" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label for="">Due Date</label>
                <input type="date" name="" class="form-control form-control-sm ">
            </div>
            <div class="col-md-2">
                <label for="">Percentage (%)</label>
                <input type="text" name="" id="percentage_other_0"
                    class="form-control form-control-sm percentage-other" onchange="checkPercentageOther('0')">
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <div class="">
                        Amount
                    </div>
                    <div class="cursor-pointer" onclick="removeInstallmentOther(0)">
                        <i class="bi bi-trash2 text-danger"></i>
                    </div>
                </div>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text currency-icon" id="basic-addon1">
                        $
                    </span>
                    <input type="number" name="" class="form-control amount-other" id="amount_other_0">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" class="form-control amount-other-idr" id="amount_other_idr_0">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function addInstallmentOther() {
        let length = $('.installment-others').length
        let id = ''

        for (let i = 1; i <= length; i++) {
            let check_id = $('#installment_other_' + i).attr('id')
            if (!check_id) {
                id = i
            } else {
                id = $('.installment-others').length + 1
            }
        }

        $(".installment-others").first().clone().attr('id', 'installment_other_' + id).appendTo(
            "#installment_content_other");

        // value 
        $('#installment_other_' + id).find('input').val('')

        $('#installment_other_' + id).find('.percentage-other').attr('id', 'percentage_other_' + id)
        $('#installment_other_' + id).find('.percentage-other').attr('onchange', 'checkPercentageOther(' + id + ')')
        $('#installment_other_' + id).find('.amount-other').attr('id', 'amount_other_' + id)
        $('#installment_other_' + id).find('.amount-other-idr').attr('id', 'amount_other_idr_' + id)
        $('#installment_other_' + id).find('.cursor-pointer').attr('onclick', 'removeInstallmentOther(' + id + ')')
    }

    function removeInstallmentOther(id) {
        if (id == 0) {
            notification('error', 'You can\'t remove first element')
        }
        $('#installment_other_' + id).remove()
    }

    function checkPercentageOther(id) {
        var sum = 0
        $('.percentage-other').each(function() {
            sum += parseInt($(this).val())
        })

        if (sum <= 100) {
            let percent = $('#percentage_other_' + id).val()
            let kurs = $('#current_rate').val()
            let tot_other = $('#total_other').val()
            let total = (percent / 100) * tot_other

            $('#amount_other_' + id).val(total)
            $('#amount_other_idr_' + id).val(total * kurs)

        } else {
            $('#percentage_other_' + id).val(null)
            $('#amount_other_' + id).val(null)
            $('#amount_other_idr_' + id).val(null)

            notification('error', 'Percentage is more than 100')
        }
    }
</script>
