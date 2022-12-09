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
                     <input type="number" name="" id="invoice_idr_price" class="form-control"
                         oninput="checkInvoiceIDR()">
                 </div>
             </div>
             <div class="col-md-4 mb-3">
                 <label for="">Participants</label>
                 <div class="input-group input-group-sm">
                     <input type="number" name="" id="invoice_idr_participants" class="form-control"
                         oninput="checkInvoiceIDR()">
                    <span class="input-group-text" id="basic-addon1">
                        Person
                    </span>
                 </div>
             </div>
             <div class="col-md-4 mb-3">
                 <label for="">Discount</label>
                 <div class="input-group input-group-sm">
                     <span class="input-group-text" id="basic-addon1">
                         Rp
                     </span>
                     <input type="number" name="" id="invoice_idr_discount" class="form-control"
                         oninput="checkInvoiceIDR()">
                 </div>
             </div>
             <div class="col-md-4 mb-3">
                 <label for="">Total Price</label>
                 <div class="input-group input-group-sm">
                     <span class="input-group-text" id="basic-addon1">
                         Rp
                     </span>
                     <input type="number" name="" id="invoice_idr_total" class="form-control">
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
         let price = $('#invoice_idr_price').val()
         let participant = $('#invoice_idr_participants').val()
         let discount = $('#invoice_idr_discount').val()
         let total = (price * participant)- discount

         $('#invoice_idr_total').val(total)

         $('#total_idr').val(total)
         $('#total_other').val(0)

         $('#invoice_idr_word').val(wordConverter(total))
     }
 </script>
