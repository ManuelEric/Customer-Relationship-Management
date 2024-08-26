<div class="card mb-3">
    <div class="card-header d-flex justify-content-between  align-items-center">
        <div class="">
            Installment
        </div>
        @if ($status != "view")
        <button class="btn btn-sm btn-outline-primary" type="button" onclick="addInstallment()">
            <i class="bi bi-plus"></i>
        </button>
        @endif
    </div>
    <div class="card-body " id="installment_content">
        @if ((old('invdtl_installment') || isset($invoice) && $invoice->invoiceDetail()->count() > 0))
        @php
            $limit = isset($invoice->invoiceDetail) ? count($invoice->invoiceDetail) : count(old('invdtl_installment'))
        @endphp
            @for ($i = 0; $i < $limit ; $i++)
            <div class="row g-2 installment mb-3">
                <div class="col-md-3">
                    <label for="">Name <sup class="text-danger">*</sup></label>
                    <input type="text" name="invdtl_installment[]" class="form-control form-control-sm installment-name" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_installment : old('invdtl_installment')[$i] }}" {{ $disabled }}>
                    @error('invdtl_installment.'.$i)
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="">Due Date <sup class="text-danger">*</sup></label>
                    <input type="date" name="invdtl_duedate[]" class="form-control form-control-sm " value="{{ isset($invoice->invoiceDetail) ? date('Y-m-d', strtotime($invoice->invoiceDetail[$i]->invdtl_duedate)) : old('invdtl_duedate')[$i] }}" {{ $disabled }}>
                    @error('invdtl_duedate.'.$i)
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label for="">Percentage (%) <sup class="text-danger">*</sup></label>
                    <input type="text" name="invdtl_percentage[]" id="percentage_{{ $i }}" class="form-control form-control-sm percentage"
                        onchange="checkPercentage('{{ $i }}')" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_percentage : old('invdtl_percentage')[$i] }}" {{ $disabled }}>
                    @error('invdtl_percentage.'.$i)
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between">
                        <div class="">
                            Amount <sup class="text-danger">*</sup>
                        </div>
                        <div class="cursor-pointer" onclick="removeInstallment({{ $i }})">
                            <i class="bi bi-trash2 text-danger"></i>
                        </div>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" id="basic-addon1">
                            Rp
                        </span>
                        <input type="number" name="invdtl_amountidr[]" class="form-control amount" id="amount_{{ $i }}" onchange="checkAmount('{{ $i }}')" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_amountidr : old('invdtl_amountidr')[$i] }}" {{ $disabled }}>
                        @error('invdtl_amountidr.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            @endfor
        @elseif (isset($invoice->receipt) && $invoice->receipt()->count() > 0 && !isset($invoice->invoiceDetail))
        @php
            $limit = $invoice->receipt->count();
        @endphp
            @for ($i = 0; $i < $limit ; $i++)
                <div class="row g-2 installment mb-3">
                    <div class="col-md-3">
                        <label for="">Name <sup class="text-danger">*</sup></label>
                        <input type="text" name="invdtl_installment[]" class="form-control form-control-sm installment-name" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_installment : old('invdtl_installment')[$i] }}" {{ $disabled }}>
                        @error('invdtl_installment.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="">Due Date <sup class="text-danger">*</sup></label>
                        <input type="date" name="invdtl_duedate[]" class="form-control form-control-sm " value="{{ isset($invoice->invoiceDetail) ? date('Y-m-d', strtotime($invoice->invoiceDetail[$i]->invdtl_duedate)) : old('invdtl_duedate')[$i] }}" {{ $disabled }}>
                        @error('invdtl_duedate.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="">Percentage (%) <sup class="text-danger">*</sup></label>
                        <input type="text" name="invdtl_percentage[]" id="percentage_{{ $i }}" class="form-control form-control-sm percentage"
                            onchange="checkPercentage('{{ $i }}')" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_percentage : old('invdtl_percentage')[$i] }}" {{ $disabled }}>
                        @error('invdtl_percentage.'.$i)
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between">
                            <div class="">
                                Amount <sup class="text-danger">*</sup>
                            </div>
                            <div class="cursor-pointer" onclick="removeInstallment({{ $i }})">
                                <i class="bi bi-trash2 text-danger"></i>
                            </div>
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" id="basic-addon1">
                                Rp
                            </span>
                            <input type="number" name="invdtl_amountidr[]" class="form-control amount" id="amount_{{ $i }}" onchange="checkAmount('{{ $i }}')" value="{{ isset($invoice->invoiceDetail) ? $invoice->invoiceDetail[$i]->invdtl_amountidr : old('invdtl_amountidr')[$i] }}" {{ $disabled }}>
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
                <label for="">Name <sup class="text-danger">*</sup></label>
                <input type="text" name="invdtl_installment[]" class="form-control form-control-sm installment-name" value="Installment 1">
                @error('invdtl_installment')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-3">
                <label for="">Due Date <sup class="text-danger">*</sup></label>
                <input type="date" name="invdtl_duedate[]" class="form-control form-control-sm ">
                @error('invdtl_duedate')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-2">
                <label for="">Percentage (%) <sup class="text-danger">*</sup></label>
                <input type="text" name="invdtl_percentage[]" id="percentage_0" class="form-control form-control-sm percentage"
                    onchange="checkPercentage('0')">
                @error('invdtl_percentage')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <div class="">
                        Amount <sup class="text-danger">*</sup>
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
                </div>
                @error('invdtl_amountidr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
        @endif
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
