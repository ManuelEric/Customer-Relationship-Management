 <div class="card">
     <div class="card-header">
         Invoice Detail
     </div>
     <div class="card-body">
         <div class="row">
             <div class="col-md-4 mb-3">
                 <label for="">Price</label>
                 <div class="input-group input-group-sm">
                     <span class="input-group-text" id="basic-addon1">
                         Rp
                     </span>
                     <input type="number" name="invb2b_priceidr" id="invoice_idr_price" class="form-control"
                         oninput="checkInvoiceIDR()" 
                         value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_priceidr : old('invb2b_priceidr') }}"
                         {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                 </div>
                @error('invb2b_priceidr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
             </div>
             <div class="col-md-4 mb-3">
                 <label for="">Participants</label>
                 <div class="input-group input-group-sm">
                     <input type="number" name="invb2b_participants" id="invoice_idr_participants" class="form-control"
                         oninput="checkInvoiceIDR()"
                         value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_participants : old('invb2b_participants') }}"
                         {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                         <span class="input-group-text" id="basic-addon1">
                             Person
                            </span>                      
                 </div>
                    @error('invb2b_participants')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
             </div>
             <div class="col-md-4 mb-3">
                 <label for="">Discount</label>
                 <div class="input-group input-group-sm">
                     <span class="input-group-text" id="basic-addon1">
                         Rp
                     </span>
                     <input type="number" name="invb2b_discidr" id="invoice_idr_discount" class="form-control"
                         oninput="checkInvoiceIDR()"
                         value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_discidr : old('invb2b_discidr') }}"
                         {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                 </div>
                    @error('invb2b_discidr')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
             </div>
             <div class="col-md-4 mb-3">
                 <label for="">Total Price</label>
                 <div class="input-group input-group-sm">
                     <span class="input-group-text" id="basic-addon1">
                         Rp
                     </span>
                     <input type="number" name="invb2b_totpriceidr" id="invoice_idr_total" class="form-control"
                     value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_totpriceidr : old('invb2b_totpriceidr') }}"
                     {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                 </div>
                @error('invb2b_totpriceidr')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
             </div>
             <div class="col-md-8 mb-3">
                 <label for="">Words</label>
                 <input type="text" name="invb2b_wordsidr" id="invoice_idr_word"
                     class="form-control form-control-sm rounded" 
                     value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_wordsidr : old('invb2b_wordsidr') }}" readonly
                     {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                    @error('invb2b_wordsidr')
                        <small class="text-danger fw-light">{{ $message }}</small>
                    @enderror
             </div>
         </div>
     </div>
 </div>

 <script>
     function checkInvoiceIDR() {
         let price = $('#invoice_idr_price').val()
         let participant = $('#invoice_idr_participants').val()
         let discount = $('#invoice_idr_discount').val()
         let total = (price * participant)- discount

         $('#invoice_idr_total').val(total)

         $('#total_idr').val(total)
         $('#total_other').val(0)

         $('#invoice_idr_word').val(wordConverter(total)+ ' Rupiah')
     }
 </script>
