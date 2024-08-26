<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="">
            Installment
        </div>
        @if ($status != "view")
        <button class="btn btn-sm btn-outline-primary" type="button" onclick="addInstallmentOther()">
            <i class="bi bi-plus"></i>
        </button>
        @endif
    </div>
    <div class="card-body " id="installment_content_other">
        @if (old('invdtl_installment__other') || (isset($invoice) &&  $invoice->invoiceDetail()->count() > 0))
        @php
            $limit = isset($invoice->invoiceDetail) ? count($invoice->invoiceDetail) : count(old('invdtl_installment__other'))
        @endphp
            @for ($i = 0; $i < $limit ; $i++)
                <div class="row g-2 installment-others mb-3">
                    <div class="col-md-3">
                        <label for="">Name <sup class="text-danger">*</sup></label>
                        <input type="text" name="invdtl_installment__other[]" class="form-control form-control-sm installment-name" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_installment : old('invdtl_installment__other')[$i] }}" {{ $disabled }}>
                        @error('invdtl_installment__other.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label for="">Due Date <sup class="text-danger">*</sup></label>
                        <input type="date" name="invdtl_duedate__other[]" class="form-control form-control-sm " value="{{ isset($invoice->invoiceDetail) ? date('Y-m-d', strtotime($invoice->invoiceDetail[$i]->invdtl_duedate)) : old('invdtl_duedate__other')[$i] }}" {{ $disabled }}>
                        @error('invdtl_duedate__other.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="">Percentage (%) <sup class="text-danger">*</sup></label>
                        <input type="text" name="invdtl_percentage__other[]" id="percentage_other_{{ $i }}" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_percentage : old('invdtl_percentage__other')[$i] }}" {{ $disabled }}
                            class="form-control form-control-sm percentage-other" onchange="checkPercentageOther('{{ $i }}')">
                        @error('invdtl_percentage__other.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between">
                            <div class="">
                                Amount <sup class="text-danger">*</sup>
                            </div>
                            <div class="cursor-pointer" onclick="removeInstallmentOther('{{ $i }}')">
                                <i class="bi bi-trash2 text-danger"></i>
                            </div>
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text currency-icon" id="basic-addon1">
                                $
                            </span>
                            <input type="number" name="invdtl_amount__other[]" class="form-control amount-other" id="amount_other_{{ $i }}" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_amount : old('invdtl_amount__other')[$i] }}" {{ $disabled }} onchange="checkAmountOther('{{ $i }}')">
                        </div>
                        @error('invdtl_amount__other.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" id="basic-addon1">
                                Rp
                            </span>
                            <input type="number" name="invdtl_amountidr__other[]" class="form-control amount-other-idr" id="amount_other_idr_{{ $i }}" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_amountidr : old('invdtl_amountidr__other')[$i] }}" {{ $disabled }}>
                        </div>
                        @error('invdtl_amountidr__other.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            @endfor
        @else
            <div class="row g-2 installment-others mb-3">
                <div class="col-md-3">
                    <label for="">Name <sup class="text-danger">*</sup></label>
                    <input type="text" name="invdtl_installment__other[]" class="form-control form-control-sm installment-name" value="Installment 1">
                    @error('invdtl_installment__other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="">Due Date <sup class="text-danger">*</sup></label>
                    <input type="date" name="invdtl_duedate__other[]" class="form-control form-control-sm ">
                    @error('invdtl_duedate__other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label for="">Percentage (%) <sup class="text-danger">*</sup></label>
                    <input type="text" name="invdtl_percentage__other[]" id="percentage_other_0"
                        class="form-control form-control-sm percentage-other" onchange="checkPercentageOther('0')">
                    @error('invdtl_percentage__other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between">
                        <div class="">
                            Amount <sup class="text-danger">*</sup>
                        </div>
                        <div class="cursor-pointer" onclick="removeInstallmentOther(0)">
                            <i class="bi bi-trash2 text-danger"></i>
                        </div>
                    </div>
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text currency-icon" id="basic-addon1">
                            $
                        </span>
                        <input type="number" name="invdtl_amount__other[]" class="form-control amount-other" id="amount_other_0" onchange="checkAmountOther('0')">
                    </div>
                    @error('invdtl_amount__other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" id="basic-addon1">
                            Rp
                        </span>
                        <input type="number" name="invdtl_amountidr__other[]" class="form-control amount-other-idr" id="amount_other_idr_0">
                    </div>
                    @error('invdtl_amountidr__other')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
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

        $(".installment-others").first().clone().attr('id', 'installment_other_' + id).appendTo("#installment_content_other");

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
