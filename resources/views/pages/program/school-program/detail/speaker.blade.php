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
                        <div class="col-md-12 mb-2">
                            <label for="">
                                From <sup class="text-danger">*</sup>
                            </label>
                            <select name="speaker_type" class="modal-select w-100" id="speaker_type"
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
                            <div class="speaker d-none mb-2" id="internal">
                                <label for="">
                                    Employee Speaker <sup class="text-danger">*</sup>
                                </label>

                                <select name="allin_speaker" class="modal-select w-100">

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

                            <div class="speaker d-none mb-2" id="partner">
                                <label for="">
                                    Partner Name <sup class="text-danger">*</sup>
                                </label>

                                <select name="select_partner" class="modal-select w-100" id="select_speaker" onchange="changeSpeaker(type = 'partner', this)">
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
                            
                            <div class="speakerPic d-none mb-2" id="partnerPic">
                                <label for="">
                                    Partner Name <sup class="text-danger">*</sup>
                                </label>

                                <select name="partner_speaker" id="partner_pic" class="modal-select w-100">
                                    <option data-placeholder="true"></option>
                                       
                                </select>
                                @error('partner_speaker')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="speaker d-none mb-2" id="school">
                                <label for="">
                                    School Name <sup class="text-danger">*</sup>
                                </label>
                                <select name="select_school" class="modal-select w-100" id="select_speaker" onchange="changeSpeaker()">
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

                            <div class="speaker d-none mb-2" id="university">
                                <label for="">
                                    University Name <sup class="text-danger">*</sup>
                                </label>
                                <select name="select_university" class="modal-select w-100" id="select_speaker" onchange="changeSpeaker()">
                                    @if (isset($universities))
                                    <option data-placeholder="true"></option>
                                        @foreach ($universities as $university)
                                            
                                                    <option value="{{ $university->univ_id }}">{{ $university->univ_name }}
                                                    </option>
                                            
                                        @endforeach
                                    @endif
                                </select>
                                @error('university_speaker')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="">
                                Start Time <sup class="text-danger">*</sup>
                            </label>
                            <input type="datetime-local" name="start_time" id=""
                                class="form-control form-control-sm" value=""
                                min="" max="">
                            @error('start_time')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">
                                End Time <sup class="text-danger">*</sup>
                            </label>
                            <input type="datetime-local" name="end_time" id=""
                                class="form-control form-control-sm" value=""
                                min="" max="">
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

 <script>
     $(document).ready(function() {
         $('.modal-select').select2({
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
        
        function changeSpeaker(type, id) {
            let id = '#' + type + 'Pic'
            // alert(id.value)
            $(id).removeClass('d-none')
            let get = '?id=' + id.value + '&type=' + type
            var html = "";
            var link = "{{ url('/program/school/$school->sch_id/detail/$schoolProgram->id/speaker') }}" + get
            Swal.showLoading()
            axios.get(link)
                .then(function(response){
                    // handle success
                    let data = response.data
                    data.forEach(function(currentValue, index, arr) {
                        html += "<option value='"+arr[index].id"'>"+arr[index].pic_name+"</option>" 
                    })

                    $('#partner_pic').append(html)
                    initSelect2("#partnerPic")
                    Swal.close()
                })
                .catch(function(error){
                    // handle error
                    Swal.close
                    notification(error.response.data.success, error.response.data.message)
                })
        }


        

        $(".status-form").each(function() {
            var _this = $(this)
            _this.change(async function() {

                var status = _this.val()
                var agendaId = _this.data('row-id')

                // var link =
                //     '{{ url('') }}/program/school/{{ $school->sch_id }}/detail/{{ $schoolProgram->id }}/speaker/' +
                //     agendaId

                var link = "{{ url('program/school/' . $school->sch_id . '/detail/' . $schoolProgram->id . '/speaker') }}" + get
                    
                var data = new Array()

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
            })
        })
 </script>
