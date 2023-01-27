<div class="card mb-3">
    <div class="card-header d-flex justify-content-between  align-items-center">
        <div class="">
            Installment
        </div>
        @if(empty($invoicePartner->inv_detail) || $status == 'edit')
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInstallment()">
                <i class="bi bi-plus"></i>
            </button>
        @endif
    </div>
    <div class="card-body " id="installment_content">
        @if ((old('invdtl_installment') || isset($invoicePartner->inv_detail)) && isset($invoicePartner) && $invoicePartner->invb2b_pm == 'Installment')
        @php
            $limit = isset($invoicePartner->inv_detail) ? count($invoicePartner->inv_detail) : count(old('invdtl_installment'))
        @endphp
            @for ($i = 0; $i < $limit ; $i++)
                <div class="row g-2 installment mb-3">
                    <div class="col-md-3">
                        <label for="">Name</label>
                        <input type="text" name="invdtl_installment[]" class="form-control form-control-sm installment-name" 
                            value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_installment : old('invdtl_installment')[$i] }}" 
                                    {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                         @error('invdtl_installment.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="">Due Date</label>
                        <input type="date" name="invdtl_duedate[]" class="form-control form-control-sm" 
                            value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_duedate : old('invdtl_duedate')[$i] }}" 
                                    {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                        @error('invdtl_duedate.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="">Percentage (%)</label>
                        <input type="text" name="invdtl_percentage[]" id="percentage_{{ $i }}" class="form-control form-control-sm percentage"
                            onchange="checkPercentage('{{ $i }}')" value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_percentage : old('invdtl_percentage')[$i] }}" 
                                    {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                        @error('invdtl_percentage.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between">
                            <div class="">
                                Amount
                            </div>
                            @if(empty($invoicePartner->inv_detail) || $status == 'edit')
                                <div class="cursor-pointer" onclick="removeInstallment({{$i}})">
                                    <i class="bi bi-trash2 text-danger"></i>
                                </div>
                            @endif
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" id="basic-addon1">
                                Rp
                            </span>
                            <input type="number" name="invdtl_amountidr[]" class="form-control amount" 
                                id="amount_{{ $i }}" onchange="checkAmount('{{ $i }}')" value="{{ isset($invoicePartner->inv_detail) ? $invoicePartner->inv_detail[$i]->invdtl_amountidr : old('invdtl_amountidr')[$i] }}" {{ empty($invoicePartner->inv_detail) || $status == 'edit' ? '' : 'disabled' }}>
                            @error('invdtl_amountidr.'.$i)
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            @endfor
        @else
            <div class="row g-2 installment mb-3">
                <div class="col-md-3">
                    <label for="">Name</label>
                    <input type="text" name="invdtl_installment[]" class="form-control form-control-sm installment-name" value="Installment 1">
                    @error('invdtl_installment.0')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="">Due Date</label>
                    <input type="date" name="invdtl_duedate[]" class="form-control form-control-sm ">
                    @error('invdtl_duedate.0')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label for="">Percentage (%)</label>
                    <input type="text" name="invdtl_percentage[]" id="percentage_0" class="form-control form-control-sm percentage"
                        onchange="checkPercentage('0')">
                    @error('invdtl_percentage.0')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
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
                        <input type="number" name="invdtl_amountidr[]" class="form-control amount" id="amount_0" onchange="checkAmount('0')">
                        @error('invdtl_amountidr.0')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        @endif
        {{-- <div class="row g-2 installment mb-3">
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
        </div> --}}
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
    
    function triggerInstallment()
    {
        $(".percentage").each(function() {
            
            var each_element = $(this)
            var element_id = each_element.attr('id')
            const arrayElement = element_id.split("_")
            var id = arrayElement[1]

            let percent = $("#" + element_id).val()
            let kurs = $("#current_rate").val()
            let tot_idr = $("#total_idr").val()
            let currency = $("#currency").val()
            let total = (percent / 100) * tot_idr
            // console.log(total)

            $("#amount_" + id).val(total)
        })
    }

    function checkPercentage(id) {
        var sum = 0
        $('.percentage').each(function() {
            sum += parseInt($(this).val())
        })

        if (isNaN(sum))
            sum = 0

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
        var sum = 0
        $('.amount').each(function() {
            sum += parseInt($(this).val())
        })

        if (isNaN(sum))
            sum = 0

        let tot_idr = $('#total_idr').val()

        console.log(tot_idr)

        if (sum <= tot_idr) {
            let amount = $('#amount_' + id).val()
            let total = Math.round((amount / tot_idr) * 100)
            $("#percentage_" + id).val(total)
        } else {
            $('#percentage_' + id).val(null)
            $('#amount_' + id).val(null)
            notification('error', 'Installment amount should be less than total invoice')
        }
    }
</script>
