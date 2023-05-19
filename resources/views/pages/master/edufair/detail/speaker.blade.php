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
                @forelse ($speakers as $speaker)   
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="">  
                                <div class="">{{ $speaker->internal_pic }}</div>
                                <small>{{ date("M d, Y H.i", strtotime($speaker->start_time)) }} - {{ date("M d, Y H.i", strtotime($speaker->end_time)) }}</small>
                        </div>
                        <div class="text-end d-flex align-items-center">
                            <select name="status_speaker" class="select w-100 status-form" onchange="checkStatusSpeaker('{{ $speaker->agenda_id }}')"
                                id="{{ 'status_speaker' . $speaker->agenda_id }}" style="width: 120px">
                                <option data-placeholder="true"></option>
                                @if(isset($speaker->status))
                                    <option value="1" {{ $speaker->status == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="2" {{ $speaker->status == 2 ? 'selected' : '' }}>
                                        Cancel
                                    </option>
                                @endif
                            </select>

                            <i class="bi bi-trash2 text-danger cursor-pointer ms-2"
                                onclick="confirmDelete('master/edufair/{{ $edufair->id }}/speaker', '{{ $speaker->agenda_id }}')"
                                >

                            </i>
                        </div>
                    </div>
                @empty
                    No speaker yet
                @endforelse
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
                 <form
                     action="{{ url('master/edufair/' . $edufair->id . '/speaker') }}"
                     method="POST" id="formPosition">
                     @csrf
                     <div class="put"></div>
                     <div class="row g-2">
                         <div class="col-md-12">
                             <div class="speaker mb-2" id="internal">
                                 <label for="">
                                     All In Speaker <sup class="text-danger">*</sup>
                                 </label>

                                 <select name="speaker" class="speaker-select w-100">

                                     @if (isset($employees))
                                         <option data-placeholder="true"></option>
                                         @foreach ($employees as $employee)
                                             <option value="{{ $employee->id }}" {{$employee->id == old('speaker') ? 'selected' : ''}}>
                                                 {{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                         @endforeach
                                     @else
                                         <option data-placeholder="true">There's no speaker</option>
                                     @endif
                                 </select>
                                 @error('speaker')
                                     <small class="text-danger fw-light">{{ $message }}</small>
                                 @enderror
                             </div>


                             {{-- <div class="speaker mb-2 d-none speaker-pic">
                                 <label for="">
                                     Speaker Name <sup class="text-danger">*</sup>
                                 </label>
                                 <select name="" class="speaker-select w-100" id="speaker_pic">
                                     <option data-placeholder="true"></option>
                                 </select>
                             
                             </div> --}}
                         </div>


                         <div class="col-md-6 mb-2">
                             <label for="">
                                 Start Time <sup class="text-danger">*</sup>
                             </label>
                             <input type="datetime-local" name="start_time" id=""
                                 class="form-control form-control-sm" value="{{old('start_time')}}">
                             @error('start_time')
                                 <small class="text-danger fw-light">{{ $message }}</small>
                             @enderror
                         </div>
                         <div class="col-md-6 mb-2">
                             <label for="">
                                 End Time <sup class="text-danger">*</sup>
                             </label>
                             <input type="datetime-local" name="end_time" id=""
                                 class="form-control form-control-sm" value="{{old('end_time')}}">
                             @error('end_time')
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

 <div class="modal fade" id="reasonModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Reason
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                <form action="#" method="POST" id="reasonForm">
                    @csrf
                    @method('put')
                    <input type="hidden" name="agendaId" id="agenda_id">
                    <input type="hidden" name="status_speaker" id="status_id">
                    <label for="">Notes</label>
                    <textarea name="notes_reason" id="notes"></textarea>
                        @error('notes_reason')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    <hr>
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                            onclick="cancelModal()">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
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
         $('.speaker-select').select2({
             dropdownParent: $('#speaker .modal-content'),
             placeholder: "Select value",
             allowClear: true
         });
     });

     function checkStatusSpeaker(agendaId) {
                let status = $('#status_speaker' + agendaId).val()
                @if(isset($edufair))
                    let link =
                        '{{ url('') }}/master/edufair/{{ $edufair->id }}/speaker/' +
                        agendaId
                    console.log(link)
                @endif 
                let data = new Array()

                $('#reasonForm').attr('action', link)

                if (status == 2) {
                    $('#reasonModal').modal('show')
                    $('#agenda_id').val(agendaId)
                    $('#status_id').val(status)
                } else {
                    $('#agenda_id').val(agendaId)
                    $('#status_id').val(status)
                    $('#notes').val('')
                    $('#reasonForm').submit()
                }
            }

     

     function cancelModal() {
        let id = $('#agenda_id').val();
        let status = $('#status_speaker' + id)
        $('#element').select2('destroy');
        $(status).val(1).select2({
            allowClear: true
        });
        $('#reasonModal').modal('hide')
    }

   
   
 </script>

    @if($errors->has('speaker') || 
        $errors->has('start_time') ||
        $errors->has('end_time')
        )
            
    
        <script>
            $(document).ready(function(){
                $('#speaker').modal('show'); 
            })

        </script>
    @endif



