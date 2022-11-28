 <div class="card rounded mb-3">
     <div class="card-header d-flex align-items-center justify-content-between">
         <div class="">
             <h6 class="m-0 p-0">
                 <i class="bi bi-person me-2"></i>
                 Speaker
             </h6>
         </div>
         <div class="">
             <button class="btn btn-sm btn-outline-primary rounded" data-bs-toggle="modal" data-bs-target="#speaker">
                 <i class="bi bi-plus"></i>
             </button>
         </div>
     </div>
     <div class="card-body">
         <div class="list-group">
             <div class="list-group-item d-flex justify-content-between align-items-center">
                 <div class="">
                     <div class="">Speaker Name</div>
                     <small>20-12-2022 15.00-17.00</small>
                 </div>
                 <div class="text-end">
                     <i class="bi bi-trash2 text-danger cursor-pointer"></i>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <div class="modal fade" id="speaker" data-bs-backdrop="static" data-bs-keyboard="false"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <span>
                     Speaker
                 </span>
                 <i class="bi bi-pencil-square"></i>
             </div>
             <div class="modal-body w-100 text-start">
                 <form action="" method="POST" id="formPosition">
                     @csrf
                     <div class="put"></div>
                     <div class="row g-2">
                         <div class="col-md-6 mb-2">
                             <label for="">
                                 From <sup class="text-danger">*</sup>
                             </label>
                             <select name="" class="modal-select w-100">
                                 <option data-placeholder="true"></option>
                                 <option value="Partner">School</option>
                                 <option value="Partner">Partner</option>
                                 <option value="ALL-in">ALL-in</option>
                             </select>
                         </div>
                         <div class="col-md-6 mb-2">
                             <label for="">
                                 Partner Name <sup class="text-danger">*</sup>
                             </label>
                             <select name="" class="modal-select w-100">
                                 <option data-placeholder="true"></option>
                             </select>
                         </div>
                         <div class="col-md-6 mb-2">
                             <label for="">
                                 Start Time <sup class="text-danger">*</sup>
                             </label>
                             <input type="datetime-local" name="" id=""
                                 class="form-control form-control-sm">
                         </div>
                         <div class="col-md-6 mb-2">
                             <label for="">
                                 End Time <sup class="text-danger">*</sup>
                             </label>
                             <input type="datetime-local" name="" id=""
                                 class="form-control form-control-sm">
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
