<div class="card mb-3">
    <div class="card-header d-flex justify-content-between  align-items-center">
        <div class="">
            Installment
        </div>
        <button class="btn btn-sm btn-outline-primary" onclick="addInstallment()">
            <i class="bi bi-plus"></i>
        </button>
    </div>
    <div class="card-body " id="installment_content">
        <div class="row g-2 installment mb-3">
            <div class="col-md-3">
                <label for="">Name</label>
                <input type="text" name="" class="form-control form-control-sm installment-name" value="Installment 1">
            </div>
            <div class="col-md-3">
                <label for="">Due Date</label>
                <input type="date" name="" class="form-control form-control-sm ">
            </div>
            <div class="col-md-2">
                <label for="">Percentage (%)</label>
                <input type="text" name="" id="percentage_0" class="form-control form-control-sm percentage"
                    onchange="checkPercentage('0')">
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <div class="">
                        Amount
                    </div>
                    <div class="cursor-pointer" onclick="removeInstallment(0)">
                        <i class="bi bi-trash2 text-danger"></i>
                    </div>
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="basic-addon1">
                        Rp
                    </span>
                    <input type="number" name="" class="form-control amount" id="amount_0">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function addInstallment() {
        let length = $('.installment').length
        let id = ''

        for (let i = 1; i <= length; i++) {
            let check_id = $('#installment_' + i).attr('id')
            if (!check_id) {
                id = i
            } else {
                id = $('.installment').length + 1
            }
        }

        $(".installment").first().clone().attr('id', 'installment_' + id).appendTo("#installment_content");

        // value 
        $('#installment_' + id).find('input').val('')

        $('#installment_' + id).find('.installment-name').val('Installment ' + parseInt(id+1))
        $('#installment_' + id).find('.percentage').attr('id', 'percentage_' + id)
        $('#installment_' + id).find('.percentage').attr('onchange', 'checkPercentage(' + id + ')')
        $('#installment_' + id).find('.amount').attr('id', 'amount_' + id)
        $('#installment_' + id).find('.amount').attr('onchange', 'checkAmount(' + id + ')')
        $('#installment_' + id).find('.cursor-pointer').attr('onclick', 'removeInstallment(' + id + ')')
    }

    function removeInstallment(id) {
        if (id == 0) {
            notification('error', 'You can\'t remove first element')
        }
        $('#installment_' + id).remove()
    }

    function checkPercentage(id) {
        var sum = 0
        $('.percentage').each(function() {
            sum += parseInt($(this).val())
        })

        if (sum <= 100) {
            let percent = $('#percentage_' + id).val()
            let kurs = $('#current_rate').val()
            let tot_idr = $('#total_idr').val()
            let currency = $('#currency').val()
            let total = (percent / 100) * tot_idr

            $('#amount_' + id).val(total)
        } else {
            $('#percentage_' + id).val(null)
            $('#amount_' + id).val(null)
            notification('error', 'Percentage is more than 100')
        }
    }

    function checkAmount(id) {

    }
</script>
