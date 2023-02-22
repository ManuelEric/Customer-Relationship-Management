 <div class="card">
     <div class="card-header">
         Invoice Detail
     </div>
     <div class="card-body">
         <div class="row">
             <div class="col-md-4 mb-3">
                 <label for="">Total Price</label>
                 <div class="input-group input-group-sm">
                     <span class="input-group-text" id="basic-addon1">
                         Rp
                     </span>
                     <input type="number" name="invb2b_totpriceidr" id="invoice_idr_total" class="form-control" oninput="checkInvoiceIDR()"
                        value="{{ (isset($invoiceRef)) ? $invoiceRef->invb2b_totpriceidr : old('invb2b_totpriceidr') }}"
                        {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                 </div>
                @error('invb2b_totpriceidr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
             </div>
             <div class="col-md-8 mb-3">
                 <label for="">Words</label>
                 <input type="text" name="invb2b_wordsidr" id="invoice_idr_word"
                     class="form-control form-control-sm rounded" 
                     value="{{ (isset($invoiceRef)) ? $invoiceRef->invb2b_wordsidr : old('invb2b_wordsidr') }}" readonly
                     {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                    @error('invb2b_wordsidr')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
             </div>
         </div>
     </div>
 </div>

 <script>
     function checkInvoiceIDR() {
         let total = $('#invoice_idr_total').val()
         $('#invoice_idr_total').val(total)
         $('#invoice_idr_word').val(wordConverter(total) +' Rupiah')
     }
 </script>
