@extends('layout.main')

@section('title', 'School Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/school')}}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> School Program
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h4>{{ $school->sch_name }}</h4>
                    @if(isset($schoolProgram))
                        <h6>{{  $schoolProgram->program->sub_prog ? $schoolProgram->program->sub_prog->sub_prog_name.' - ':''}}{{ $schoolProgram->program->prog_program }}</h6>
                    @endif
                    @if (isset($schoolProgram))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('program/school/' . strtolower($school->sch_id) .'/detail/'. $schoolProgram->id ) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('program/school/' . strtolower($school->sch_id) . '/detail/'. $schoolProgram->id .'/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <button type="button"
                                onclick="confirmDelete('{{'program/school/' . strtolower($school->sch_id) . '/detail'}}', {{$schoolProgram->id}})"
                                class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                   
                </div>
            </div>

            
            @include('pages.program.school-program.detail.school')

            {{-- Refund detail --}}
            @if(isset($schoolProgram))
                @if($schoolProgram->status == 3)
                    @include('pages.program.school-program.detail.refund')
                @endif
            @endif

            @if(isset($schoolProgram) &&  $schoolProgram->status == 1 && empty($edit))
                @include('pages.program.school-program.detail.speaker')
            @endif
        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            School Program Detail
                        </h6>
                    </div>
                </div>
                

                <div class="card-body">

                    <form action="{{ url(isset($edit) ? 'program/school/' . strtolower($school->sch_id) . '/detail/' . $schoolProgram->id : 'program/school/' . strtolower($school->sch_id) . '/detail') }}" method="POST">
                        @csrf
                       @if(isset($edit))
                            @method('put')
                       @endif
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Program Name <sup class="text-danger">*</sup>
                            </label>
                            
                        </div>
                        <div class="col-md-9">
                            <select name="prog_id" id="" class="select w-100" {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                <option data-placeholder="true"></option>
                                @if(isset($schoolProgram->prog_id))
                                    @if(isset($edit))
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->prog_id }}" {{ $schoolProgram->prog_id == $program->prog_id ? 'selected' : ''}}>
                                                {{$program->sub_prog ? $program->sub_prog->sub_prog_name.' - ':''}}{{ $program->prog_program }}
                                            </option>
                                        @endforeach
                                    @else        
                                        <option value="{{ $schoolProgram->prog_id }}" selected>
                                            {{  $schoolProgram->program->sub_prog ? $schoolProgram->program->sub_prog->sub_prog_name.' - ':''}}{{ $schoolProgram->program->prog_program }}
                                        </option>
                                    @endif
                                @elseif(empty($schoolProgram))
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->prog_id }}" {{ old('prog_id') == $program->prog_id ? "selected" : "" }}>
                                            {{$program->sub_prog ? $program->sub_prog->sub_prog_name.' - ':''}}{{ $program->prog_program }}
                                        </option>
                                    @endforeach
                              
                                @endif
                            </select>
                            @error('prog_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Date <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>First Discuss <sup class="text-danger">*</sup> </small>
                                    <input type="date" name="first_discuss" id="" 
                                    value="{{ isset($schoolProgram->first_discuss) ? $schoolProgram->first_discuss :  old('first_discuss') }}"
                                        class="form-control form-control-sm rounded" 
                                        {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>

                                </div>
                                @error('first_discuss')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Notes
                            </label>
                        </div>
                        <div class="col-md-9">
                            <textarea name="notes" id="" class="w-100"  {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                {{ isset($schoolProgram->notes) ? $schoolProgram->notes :  old('notes') }}
                            </textarea>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Approach Status <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>Status <sup class="text-danger">*</sup> </small>
                                    <select name="status" id="approach_status" class="select w-100"
                                        onchange="checkStatus()" {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                        <option data-placeholder="true"></option>
                                            <option value="0">Pending</option>
                                            <option value="1">Success</option>
                                            <option value="2">Denied</option>
                                            @if (isset($schoolProgram->invoice->receipt))
                                            <option value="3">Refund</option>
                                            @endif
                                    </select>
                                    @error('status')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 success_status d-none">
                                    <small>Success Date <sup class="text-danger">*</sup> </small>
                                    <input type="date" name="success_date" id=""
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($schoolProgram->success_date) ? $schoolProgram->success_date :  old('success_date') }}"
                                        {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                    @error('success_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 denied_status d-none">
                                    <small>Denied Date <sup class="text-danger">*</sup> </small>
                                    <input type="date" name="denied_date" id=""
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($schoolProgram->denied_date) ? $schoolProgram->denied_date :  old('denied_date') }}"
                                        {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                    @error('denied_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 refund_status d-none">
                                    <small>Refund Date <sup class="text-danger">*</sup> </small>
                                    <input type="date" name="refund_date" id=""
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($schoolProgram->refund_date) ? $schoolProgram->refund_date :  old('refund_date') }}"
                                        {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                    @error('refund_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Refund --}}
                                <div class="col-md-6 refund_status d-none my-2">
                                
                                        <label>Reason <sup class="text-danger">*</sup> </label>
                                        <div class="classReasonRefund">
                                            <select name="reason_refund_id" class="select w-100"
                                                style="display: none !important; width:100% !important" id="selectReasonRefund"
                                                onchange="otherOption($(this).val())"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                                <option data-placeholder="true"></option>
                                                    @if(isset($schoolProgram->reason_id) || isset($edit))
                                                        @if(isset($edit))    
                                                            @foreach ($reasons as $reason)
                                                                <option value="{{ $reason->reason_id }}" {{ $schoolProgram->reason_id == $reason->reason_id ? 'selected' : ''}}>
                                                                    {{ $reason->reason_name }}
                                                                </option>
                                                            @endforeach
                                                            <option value="other_reason_refund">
                                                                Other option
                                                            </option>
                                                        @else
                                                                <option value="{{ $schoolProgram->reason_id }}" selected>
                                                                    {{ $schoolProgram->reason->reason_name }}
                                                                </option>        
                                                        @endif
                                                    @elseif(empty($schoolProgram))
                                                        @foreach ($reasons as $reason)
                                                            <option value="{{ $reason->reason_id }}" {{ old('reason_refund_id') == $reason->reason_id ? "selected" : "" }}>
                                                                {{ $reason->reason_name }}
                                                            </option>
                                                        @endforeach
                                                        <option value="other_reason_refund">
                                                            Other option
                                                        </option>
                                                    @endif
                                            </select>
                                                @error('reason_refund_id')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                                @error('other_reason_refund')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            
                                        </div>
                                            
                                        <div class="d-flex align-items-center d-none" id="inputReasonRefund">
                                            <input type="text" name="other_reason_refund"
                                                class="form-control form-control-sm rounded">
                                            <div class="float-end cursor-pointer" onclick="resetOption()">
                                                <b>
                                                    <i class="bi bi-x text-danger"></i>
                                                </b>
                                            </div>
                                        </div>
    
                                </div>
                                <div class="col-md-12 refund_status d-none my-2">
                                    <label for="">
                                       Refund  Notes
                                    </label>
                                    <textarea name="refund_notes" id="" class="w-100"  {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                        {{ isset($schoolProgram->refund_notes) ? $schoolProgram->refund_notes :  old('refund_notes') }}
                                    </textarea>
                                </div>        

                                {{-- Denied --}}
                                <div class="col-md-6 denied_status d-none my-2">
                                
                                        <label>Reason <sup class="text-danger">*</sup> </label>
                                        <div class="classReason">
                                            <select name="reason_id" class="select w-100"
                                                style="display: none !important; width:100% !important" id="selectReason"
                                                onchange="otherOption($(this).val())"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                                <option data-placeholder="true"></option>
                                                    @if(isset($schoolProgram->reason_id) || isset($edit))
                                                        @if(isset($edit))    
                                                            @foreach ($reasons as $reason)
                                                                <option value="{{ $reason->reason_id }}" {{ $schoolProgram->reason_id == $reason->reason_id ? 'selected' : ''}}>
                                                                    {{ $reason->reason_name }}
                                                                </option>
                                                            @endforeach
                                                            <option value="other">
                                                                Other option
                                                            </option>
                                                        @else
                                                                <option value="{{ $schoolProgram->reason_id }}" selected>
                                                                    {{ $schoolProgram->reason->reason_name }}
                                                                </option>        
                                                        @endif
                                                    @elseif(empty($schoolProgram))
                                                        @foreach ($reasons as $reason)
                                                            <option value="{{ $reason->reason_id }}" {{ old('reason_id') == $reason->reason_id ? "selected" : "" }}>
                                                                {{ $reason->reason_name }}
                                                            </option>
                                                        @endforeach
                                                        <option value="other">
                                                            Other option
                                                        </option>
                                                    @endif
                                            </select>
                                                @error('reason_id')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                                @error('other_reason')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
    
                                        </div>
                                            
                                        <div class="d-flex align-items-center d-none" id="inputReason">
                                            <input type="text" name="other_reason"
                                                class="form-control form-control-sm rounded">
                                            <div class="float-end cursor-pointer" onclick="resetOption()">
                                                <b>
                                                    <i class="bi bi-x text-danger"></i>
                                                </b>
                                            </div>
                                        </div>              
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Program Detail  --}}
                    <div class="row mb-3 success_status d-none">
                        <div class="col-md-3">
                            <label for="">
                                Program Detail
                            </label>
                        </div>
                        <div class="col-md-9">
                            {{-- Admissions Program  --}}
                            <div class="card">
                                <div class="card-header">
                                    School Program
                                </div>
                                <div class="card-body">
                                    {{-- if success  --}}
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <small>Start Program Date <sup class="text-danger">*</sup> </small>
                                            <input type="date" name="start_program_date" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->start_program_date) ? $schoolProgram->start_program_date :  old('start_program_date') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                            @error('start_program_date')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <small>End Program Date <sup class="text-danger">*</sup> </small>
                                            <input type="date" name="end_program_date" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->end_program_date) ? $schoolProgram->end_program_date :  old('end_program_date') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                            @error('end_program_date')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-12 mb-2">
                                            <small>Place <sup class="text-danger">*</sup> </small>
                                            <input type="text" name="place" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->place) ? $schoolProgram->place :  old('place') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                            @error('place')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Participants <sup class="text-danger">*</sup> </small>
                                            <input type="number" name="participants" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->participants) ? $schoolProgram->participants :  old('participants') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                            @error('participants')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Total Fee <sup class="text-danger">*</sup> </small>
                                            <input type="number" name="total_fee" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->total_fee) ? $schoolProgram->total_fee :  old('total_fee') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                            @error('total_fee')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Total Hours <sup class="text-danger">*</sup> </small>
                                            <input type="number" name="total_hours" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->total_hours) ? $schoolProgram->total_hours :  old('total_hours') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                            @error('total_hours')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Running Status <sup class="text-danger">*</sup> </small>
                                            <select name="running_status" id="" class="select w-100" {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                                <option data-placeholder="true"></option>
                                                @if(isset($schoolProgram))
                                                    <option value="Not yet" {{ $schoolProgram->running_status == 'Not yet' ? 'selected' : ''}}>Not yet</option>
                                                    <option value="On going" {{ $schoolProgram->running_status == 'On going' ? 'selected' : ''}}>On going</option>
                                                    <option value="Done" {{ $schoolProgram->running_status == 'Done' ? 'selected' : ''}}>Done</option>
                                                @elseif(empty($schoolProgram))
                                                    <option value="Not yet" {{ old('running_status') == 'Not yet' ? "selected" : "" }}>Not yet</option>
                                                    <option value="On going" {{ old('running_status') == 'On going' ? "selected" : "" }}>On going</option>
                                                    <option value="Done" {{ old('running_status') == 'Done' ? "selected" : "" }}>Done</option>
                                                @endif
                                            </select>
                                            @error('running_status')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <small>Notes</small>
                                            <textarea name="notes_detail" id=""  {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                                {{ isset($schoolProgram->notes_detail) ? $schoolProgram->notes_detail :  old('notes_detail') }}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                PIC <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    
                                    <select name="empl_id" id="" class="select w-100" {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                        <option data-placeholder="true"></option>
                                         @if(isset($edit))
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}" 
                                                        {{ $schoolProgram->empl_id ==  $employee->id ? 'selected' : ''}}>
                                                        {{ $employee->first_name }} {{ $employee->last_name }}</option>
                                                @endforeach    
                                        @endif
                                        @if(isset($schoolProgram->empl_id))
                                                <option value="{{ $schoolProgram->empl_id }}" selected>
                                                    {{ $schoolProgram->user->first_name }} {{ $schoolProgram->user->last_name }}
                                                </option>
                                        @elseif(empty($schoolProgram))
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ old('empl_id') == $employee->id ? "selected" : "" }}>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                                            @endforeach
                                        @endif
                                        
                                    </select>
                                    @error('empl_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @if (empty($schoolProgram) || isset($edit))
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-sm btn-primary rounded">
                                <i class="bi bi-save2 me-2"></i> Submit
                            </button>
                        </div>
                    @endif
                    </form>
                </div>
            </div>
            @if(!empty($attach) && $schoolProgram->status == 1 )
                @include('pages.program.school-program.detail.attachment')
            @endif
        </div>
    </div>

    <script>
             function checkStatusSpeaker(agendaId) {
                let status = $('#status_speaker' + agendaId).val()
                @if(isset($schoolProgram))
                    let link =
                        '{{ url('') }}/program/school/{{ strtolower($school->sch_id) }}/detail/{{$schoolProgram->id}}/speaker/' +
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
    </script>

    <script>
        function checkStatus() {
            let status = $('#approach_status').val();
            if (status == '0') {
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
                $('.refund_status').addClass('d-none')
            } else if (status == '1') {
                $('.denied_status').addClass('d-none')
                $('.refund_status').addClass('d-none')
                $('.success_status').removeClass('d-none')
            } else if (status == '2') {
                $('.denied_status').removeClass('d-none')
                $('.success_status').addClass('d-none')
                $('.refund_status').addClass('d-none')
            } else if (status == '3'){
                $('.refund_status').removeClass('d-none')
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
            }
        }

        function otherOption(value) {

            if(value == 'other_reason_refund'){
                $('.classReasonRefund').addClass('d-none')
                $('#inputReasonRefund').removeClass('d-none')
                $('#inputReasonRefund input').focus()
            }else{
                $('#inputReasonRefund').addClass('d-none')
                $('.classReasonRefund').removeClass('d-none')
            }
            

            if (value == 'other') {
                $('.classReason').addClass('d-none')
                $('#inputReason').removeClass('d-none')
                $('#inputReason input').focus()
            } else {
                $('#inputReason').addClass('d-none')
                $('.classReason').removeClass('d-none')
            }
        }

        function resetOption() {
            $('.classReason').removeClass('d-none')
            $('#selectReason').val(null).trigger('change')
            $('#inputReason').addClass('d-none')
            $('#inputReason input').val(null)
        }


        function changeSpeaker(type) {
         let id = $('#' + type + '_id').val()
         @if (isset($schoolProgram))    
            let link = '{{ url('program/school/' . strtolower($school->sch_id) . '/detail/' . $schoolProgram->id . '/edit') }}'
            let new_link = link + '?type=' + type + '&id=' + id;
         @endif

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



    </script>
    @if(isset($schoolProgram))
        <script>
            $(document).ready(function(){
                $('#approach_status').val('{{$schoolProgram->status}}').trigger('change')
                // $('#selectReason').select2()
            })

        </script>
    @endif
    
    @if(
        $errors->has('success_date') || 
        $errors->has('start_program_date') || 
        $errors->has('end_program_date') ||
        $errors->has('place') ||
        $errors->has('participants') ||
        $errors->has('total_fee') ||
        $errors->has('total_hours') ||
        $errors->has('running_status')
        )
        
        <script>
            $(document).ready(function(){
                $('#approach_status').val('1').trigger('change')
                // $('#selectReason').select2()
            })

        </script>

    @endif
  
    @if(
        $errors->has('denied_date') || 
        $errors->has('reason_id') ||
        $errors->has('other_reason')
        )
        
        <script>
            $(document).ready(function(){
                $('#approach_status').val('2').trigger('change')
            })

        </script>


    @endif

    @if($errors->has('refund_date') || $errors->has('reason_refund_id') || $errors->has('other_reason_refund'))
        <script>
            $(document).ready(function(){
                $('#approach_status').val('3').trigger('change')
            })

        </script>
    @endif

    @if($errors->has('notes_reason'))
            
        <script>
            $(document).ready(function(){
                $('#reasonModal').modal('show'); 
            })

        </script>

    @endif

@endsection
