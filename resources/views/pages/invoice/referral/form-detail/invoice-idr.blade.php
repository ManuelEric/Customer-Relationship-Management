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
                     <input type="number" name="" id="invoice_idr_total" class="form-control"  oninput="checkInvoiceIDR()">
                 </div>
             </div>
             <div class="col-md-8 mb-3">
                 <label for="">Words</label>
                 <input type="text" name="" id="invoice_idr_word"
                     class="form-control form-control-sm rounded" readonly>
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
