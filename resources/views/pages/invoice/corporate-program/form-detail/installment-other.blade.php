<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="">
            Installment
        </div>
        @if(empty($invoicePartner->inv_detail) || $status == 'edit')
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInstallmentOther()">
                <i class="bi bi-plus"></i>
            </button>
        @endif
    </div>
    <div class="card-body " id="installment_content_other">
        @if (old('invdtl_installment') || isset($invoicePartner->inv_detail)  && isset($invoicePartner) && $invoicePartner->invb2b_pm == 'Installment')
        @php
            $limit = isset($invoicePartner->inv_detail) ? count($invoicePartner->inv_detail) : count(old('invdtl_installment'))
        @endphp
            @for ($i = 0; $i < $limit ; $i++)
                <div class="row g-2 installment-others mb-3">
                    <div class="col-md-3">
                        <label for="">Name</label>
                        <input type="text" name="invdtl_installment_other[]" class="form-control form-control-sm installment-name" 
                            value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_installment : old('invdtl_installment_other')[$i] }}" 
                                    {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                        @error('invdtl_installment_other.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="">Due Date</label>
                        <input type="date" name="invdtl_duedate_other[]" class="form-control form-control-sm"
                           value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_duedate : old('invdtl_duedate_other')[$i] }}" 
                                    {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                        @error('invdtl_duedate_other.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="">Percentage (%)</label>
                        <input type="text" name="invdtl_percentage_other[]" id="percentage_other_0"
                            class="form-control form-control-sm percentage-other" onchange="checkPercentageOther('0')"
                            onchange="checkPercentage('{{$i}}')" value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_percentage : old('invdtl_percentage_other')[$i] }}" 
                                    {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                        @error('invdtl_percentage_other.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between">
                            <div class="">
                                Amount
                            </div>
                            @if(empty($invoicePartner->inv_detail) || $status == 'edit')
                                <div class="cursor-pointer" onclick="removeInstallmentOther({{$i}})">
                                    <i class="bi bi-trash2 text-danger"></i>
                                </div>
                            @endif
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text currency-icon" id="basic-addon1">
                                $
                            </span>
                            <input type="number" name="invdtl_amount[]" class="form-control amount-other" id="amount_other_{{$i}}" onchange="checkAmountOther('{{ $i }}')"
                                value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_amount : old('invdtl_amount')[$i] }}" 
                                {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                             @error('invdtl_amount.'.$i)
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" id="basic-addon1">
                                Rp
                            </span>
                            <input type="number" name="invdtl_amountidr_other[]" class="form-control amount-other-idr" 
                                id="amount_other_idr_{{$i}}"
                               value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_amountidr : old('invdtl_amountidr_other')[$i] }}" 
                               {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                             @error('invdtl_amountidr_other.'.$i)
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            @endfor
        @else
            <div class="row g-2 installment-others mb-3">
                <div class="col-md-3">
                    <label for="">Name</label>
                    <input type="text" name="invdtl_installment_other[]" class="form-control form-control-sm installment-name" value="Installment 1">
                </div>
                <div class="col-md-3">
                    <label for="">Due Date</label>
                    <input type="date" name="invdtl_duedate_other[]" class="form-control form-control-sm ">
                </div>
                <div class="col-md-2">
                    <label for="">Percentage (%)</label>
                    <input type="text" name="invdtl_percentage_other[]" id="percentage_other_0"
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
                        <input type="number" name="invdtl_amount[]" class="form-control amount-other" id="amount_other_0">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" id="basic-addon1">
                            Rp
                        </span>
                        <input type="number" name="invdtl_amountidr_other[]" class="form-control amount-other-idr" id="amount_other_idr_0">
                    </div>
                </div>
            </div>
        @endif
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

        $('#installment_other_' + id).find('.installment-name').val('Installment ' + parseInt(id+1))
        $('#installment_other_' + id).find('.percentage-other').attr('id', 'percentage_other_' + id)
        $('#installment_other_' + id).find('.percentage-other').attr('onchange', 'checkPercentageOther(' + id + ')')
        $('#installment_other_' + id).find('.amount-other').attr('id', 'amount_other_' + id)
        $('#installment_other_' + id).find('.amount-other').attr('onchange', 'checkAmountOther(' + id + ')')
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

        if (isNaN(sum))
            sum = 0


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

    function checkAmountOther(id) {
        var sum = 0
        $('.amount-other').each(function() {
            sum += parseInt($(this).val())
        })

        if (isNaN(sum))
            sum = 0

        let tot_other = $('#total_other').val()

        if (sum <= tot_other) {
            let amount = $('#amount_other_' + id).val()
            let total = Math.round((amount / tot_idr) * 100)
            $("#percentage_other_" + id).val(total)
        } else {
            $('#percentage_other_' + id).val(null)
            $('#amount_other_' + id).val(null)
            notification('error', 'Installment amount should be less than total invoice')
        }
    }
</script>
