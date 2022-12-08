 <div class="card rounded mb-3">
     <div class="card-header d-flex align-items-center justify-content-between">
         <div class="">
             <h6 class="m-0 p-0">
                 <i class="bi bi-person me-2"></i>
                 Attachment
             </h6>
         </div>
           
         <div class="">
             <button class="btn btn-sm btn-outline-primary rounded" data-bs-toggle="modal" data-bs-target="#attachment">
                 <i class="bi bi-plus"></i>
             </button>
         </div>
        
     </div>
     <div class="card-body">
         <div class="list-group">
             <div class="list-group-item d-flex flex-wrap gap-2 align-items-center">
                @if( isset($schoolProgram))
                    
                    @forelse ($schoolProgramAttachs as $schoolProgramAttach)
                    
                        <div class="d-flex me-2 border px-2 py-1 rounded">
                            <a href="{{ url($schoolProgramAttach->schprog_attach) }}" class="text-muted text-decoration-none">
                                    <i class="bi bi-download me-1"></i> {{ $schoolProgramAttach->schprog_file }}
                                </a>
                                <div class="text-end cursor-pointer ms-4">
                                   
                                    <i class="bi bi-x text-danger" onclick="confirmDelete('{{ 'program/school/' . $school->sch_id . '/detail/' . $schoolProgram->id . '/attach' }}', {{$schoolProgramAttach->id}})"></i>
                            </div>
                        </div>
                        
                     @empty
                        No Have Data  
                     @endforelse
                @else
                No Have Data
                @endif
             </div>
         </div>
     </div>
 </div>

 <div class="modal fade" id="attachment" data-bs-backdrop="static" data-bs-keyboard="false"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <span>
                     Attachment
                 </span>
                 <i class="bi bi-pencil-square"></i>
             </div>
             <div class="modal-body w-100 text-start">
                @error('schprog_id')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
                 <form action="{{ url(
                    'program/school/' . $school->sch_id . '/detail/' . $schoolProgram->id . '/attach' 
                        )}}" method="POST" id="formPosition" enctype="multipart/form-data">
                     @csrf
                     <div class="put"></div>
                     <div class="row g-2">
                        <div class="col-md-12 mb-2">
                             <label for="">
                                 File Name <sup class="text-danger">*</sup>
                             </label>
                             <input type="text" name="schprog_file" id=""
                                 class="form-control form-control-sm rounded" value="{{ $errors->has('schprog_file') || $errors->has('schprog_attach')  ? old('schprog_file') : '' }}">

                            @error('schprog_file')
                                 <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror 
                        </div>
                         <div class="col-md-12 mb-2">
                             <label for="">
                                 Attachment <sup class="text-danger">*</sup>
                             </label>
                             <input type="file" name="schprog_attach" id=""
                                 class="form-control form-control-sm rounded" value="{{ $errors->has('schprog_file') || $errors->has('schprog_attach') ? old('schprog_attach') : '' }}">

                            @error('schprog_attach')
                                 <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror 
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
             dropdownParent: $('#attachment .modal-content'),
             placeholder: "Select value",
             allowClear: true
         });
     });

    </script>
     
     @if($errors->has('schprog_file') || $errors->has('schprog_attach') || $errors->has('schprog_id'))
        
            <script>
                $(document).ready(function(){
                    $('#attachment').modal('show'); 
                })
            
            </script>
        
    @endif
