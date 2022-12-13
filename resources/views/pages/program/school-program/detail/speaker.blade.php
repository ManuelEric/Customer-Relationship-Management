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
                            @switch($speaker->speaker_type)
                                @case('partner')
                                    <div class="">{{ $speaker->partner_pic_name }}</div>
                                @break
                                @case('internal')
                                    <div class="">{{ $speaker->internal_pic }}</div>
                                @break
                                @case('school')
                                    <div class="">{{ $speaker->school_pic_name }}</div>
                                @break
                            @endswitch
                                <small>{{ $speaker->start_time }}</small>
                        </div>
                        <div class="text-end d-flex align-items-center">
                            <select name="status" class="select w-100 status-form" onchange="checkStatusSpeaker('{{ $speaker->agenda_id }}')"
                                id="{{ 'speaker' . $speaker->agenda_id }}" style="width: 120px">
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
                                onclick="confirmDelete('program/school/{{ $school->sch_id }}/detail/{{ $schoolProgram->id }}/speaker', '{{ $speaker->agenda_id }}')">

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
                     action="{{ url('program/school/' . $school->sch_id . '/detail/' . $schoolProgram->id . '/speaker') }}"
                     method="POST">
                     @csrf
                     <div class="put"></div>
                     <div class="row g-2">
                         <div class="col-md-12 mb-2">
                             <label for="">
                                 From  <sup class="text-danger">*</sup>
                             </label>
                             <select name="speaker_type" class="speaker-select w-100" id="speaker_type"
                                 onchange="changeType()">
                                 <option data-placeholder="true"></option>
                                 <option value="internal">ALL-in</option>
                                 <option value="partner">Partner</option>
                                 <option value="school">School</option>
                             </select>
                             @error('speaker_type')
                                 <small class="text-danger fw-light">{{ $message }}</small>
                             @enderror
                         </div>
                         <div class="col-md-12">
                             <div class="speaker mb-2 d-none" id="internal">
                                 <label for="">
                                     Employee Speaker <sup class="text-danger">*</sup>
                                 </label>

                                 <select name="allin_speaker" class="speaker-select w-100">

                                     @if (isset($employees))
                                         <option data-placeholder="true"></option>
                                         @foreach ($employees as $employee)
                                             <option value="{{ $employee->id }}">
                                                 {{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                         @endforeach
                                     @else
                                         <option data-placeholder="true">There's no speaker</option>
                                     @endif
                                 </select>
                                 @error('allin_speaker')
                                     <small class="text-danger fw-light">{{ $message }}</small>
                                 @enderror
                             </div>

                             <div class="speaker mb-2 d-none" id="partner">
                                 <label for="">
                                     Partner Name <sup class="text-danger">*</sup>
                                 </label>

                                 <select name="select_partner" class="speaker-select w-100" id="partner_id"
                                     onchange="changeSpeaker('partner')">
                                     @if (isset($partners))
                                         <option data-placeholder="true"></option>
                                         @foreach ($partners as $partner)
                                             <option value="{{ $partner->corp_id }}">{{ $partner->corp_name }}</option>
                                         @endforeach
                                     @endif
                                 </select>
                                 @error('partner_speaker')
                                     <small class="text-danger fw-light">{{ $message }}</small>
                                 @enderror
                             </div>

                             <div class="speaker mb-2 d-none" id="school">
                                 <label for="">
                                     School Name <sup class="text-danger">*</sup>
                                 </label>
                                 <select name="select_school" class="speaker-select w-100"
                                     onchange="changeSpeaker('school')" id="school_id">
                                     @if (isset($schools))
                                         <option data-placeholder="true"></option>
                                         @foreach ($schools as $school)
                                             <option value="{{ $school->sch_id }}">
                                                 {{ $school->sch_name }}</option>
                                         @endforeach
                                     @endif
                                 </select>
                                 @error('school_speaker')
                                     <small class="text-danger fw-light">{{ $message }}</small>
                                 @enderror
                             </div>

                             <div class="speaker mb-2 d-none speaker-pic">
                                 <label for="">
                                     Speaker Name <sup class="text-danger">*</sup>
                                 </label>
                                 <select name="" class="speaker-select w-100" id="speaker_pic">
                                     <option data-placeholder="true"></option>
                                 </select>
                                 @error('school_speaker')
                                     <small class="text-danger fw-light">{{ $message }}</small>
                                 @enderror
                             </div>
                         </div>


                         <div class="col-md-6 mb-2">
                             <label for="">
                                 Start Time <sup class="text-danger">*</sup>
                             </label>
                             <input type="datetime-local" name="start_time" id=""
                                 class="form-control form-control-sm" value="" min="{{ $schoolProgram->start_program_date . 'T00:00' }}" max="{{ $schoolProgram->end_program_date . 'T00:00' }}">
                             @error('start_time')
                                 <small class="text-danger fw-light">{{ $message }}</small>
                             @enderror
                         </div>
                         <div class="col-md-6 mb-2">
                             <label for="">
                                 End Time <sup class="text-danger">*</sup>
                             </label>
                             <input type="datetime-local" name="end_time" id=""
                                 class="form-control form-control-sm" value="" min="{{ $schoolProgram->start_program_date . 'T00:00' }}" max="{{ $schoolProgram->end_program_date . 'T23:59' }}">
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
                    <input type="hidden" name="status" id="status_id">
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

     function changeType() {
         let type = $('#speaker_type').val()
         let id = '#' + type
         $('.speaker').addClass('d-none')
         $(id).removeClass('d-none')
     }

     function changeSpeaker(type) {
         let id = $('#' + type + '_id').val()
         let link = '{{ url('program/school/' . $school->sch_id . '/detail/' . $schoolProgram->id . '/edit') }}'
         let new_link = link + '?type=' + type + '&id=' + id;

         $('.speaker-pic').removeClass('d-none')
         $('#speaker_pic').attr('name', type + '_speaker')
         Swal.showLoading()
         axios.get(new_link)
             .then((res) => {
                 // handle success
                 let data = res.data
                 $('#speaker_pic').html('<option data-placeholder="true"></option>')

                 if (type == 'partner') {
                     data.forEach(partner => {
                         $('#speaker_pic').append('<option value="' + partner.id + '">' + partner
                             .pic_name + '</option>')
                     });
                 } else {
                     data.forEach(sch => {
                         $('#speaker_pic').append('<option value="' + sch.schdetail_id + '">' + sch
                             .schdetail_fullname + '</option>')
                     });
                 }
                 Swal.close();
             })
             .catch((err) => {
                 // handle error
                 console.error(err)
                 Swal.close()
             })

     }

     function cancelModal() {
        let id = $('#agenda_id').val();
        let status = $('#speaker' + id)
        $('#element').select2('destroy');
        $(status).val(1).select2({
            allowClear: true
        });
        $('#reasonModal').modal('hide')
    }

   
 </script>

    @if($errors->has('speaker_type') || 
        $errors->has('allin_speaker') || 
        $errors->has('partner_speaker') ||
        $errors->has('start_time') ||
        $errors->has('end_time')
        )
            
        <script>
            $(document).ready(function(){
                $('#speaker').modal('show'); 
            })

        </script>

    @endif

