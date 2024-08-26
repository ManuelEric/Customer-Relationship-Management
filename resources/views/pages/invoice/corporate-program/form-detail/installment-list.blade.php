<div class="card rounded my-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Installment List
            </h6>
        </div>
    </div>
    <div class="card-body">
        <div class="list-group">
            @foreach ($invoicePartner->inv_detail as $inv_dtl)
                <div class="list-group-item">
                    <div class="">
                        <div class="ps-1 fs-6">
                            {{ $inv_dtl->invdtl_installment }}
                        </div>
                        <table class="table">
                            <tr>
                                <td>Invoice ID: {{isset($inv_dtl->receipt)}}</td>
                                <td class="text-end">{{ $inv_dtl->invb2b_id }}</td>
                            </tr>
                            <tr>
                                <td> Due Date:</td>
                                <td class="text-end">{{ $inv_dtl->invdtl_duedate }}</td>
                            </tr>
                        </table>
                        <div class="ps-1 mt-1">
                            {{ $inv_dtl->invdtl_amount > 0 ? $inv_dtl->invoicedtlAmount . ' | ' : '' }} 
                             {{ $inv_dtl->invoicedtlAmountidr }}
                        </div>
                    </div> 
                    <div class="mt-2 text-end">
                        @if(empty($inv_dtl->receipt) && $invoicePartner->invb2b_pm == 'Installment' && $status != 'edit')
                            <button class="btn btn-sm btn-outline-primary py-1" style="font-size: 11px" onclick="checkReceipt('{{isset($inv_dtl->invdtl_amount) ? $inv_dtl->invdtl_amount : $inv_dtl->invdtl_amountidr}}', '{{$invoicePartner->currency != 'idr' ? 'other' : 'idr'}}', '{{isset($inv_dtl->invdtl_amount) ? $inv_dtl->invdtl_amountidr : null}}');setIdentifier('{{ $inv_dtl->invdtl_id }}')">
                                <i class="bi bi-plus"></i> Receipt
                            </button>
                        @endif
                        @if(isset($inv_dtl->receipt)  && $status != 'edit' && $invoicePartner->invb2b_pm == 'Installment')
                            <a href="{{ url('receipt/corporate-program/'.$inv_dtl->receipt->id) }}" class="btn btn-sm btn-outline-warning py-1"  style="font-size: 11px">
                                <i class="bi bi-eye"></i> View Receipt
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Add Installment  --}}
<div class="modal fade" id="plan" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Plan Follow-Up
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="" method="POST" id="formPosition">
                    @csrf
                    <div class="put"></div>
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label for="">
                                Follw-Up Date <sup class="text-danger">*</sup>
                            </label>
                            <input type="date" name="" id=""
                                class="form-control form-control-sm rounded">
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.modal-select').select2({
            dropdownParent: $('#speaker .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });
    });
</script>
