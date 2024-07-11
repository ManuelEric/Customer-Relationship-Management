<div class="card {{ $is_tutor || Request::route('user_role') == 'tutor' ? null : 'd-none' }}" id="subject">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 p-0">Subject Detail</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSubject()"><i
                class="bi bi-plus"></i></button>
    </div>
    <div class="card-body" id="subjectContent">
        <div class="row" id="subjectField">
            {{-- <div class="col-md-12 education"> --}}
                @if (isset($user) && count($user->user_subjects) > 0 )
                    @foreach ($user->user_subjects as $user_subject)
                        <div class="col-md-12 subject subject-{{ $loop->index }}">
                            <div class="row g-2">
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Subject Name</label>
                                    <select name="subject_id[]" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                @if ($subject->id == $user_subject->subject->id){{ "selected" }}
                                                @endif
                                            >{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject_id.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Grade</label>
                                    <select name="grade[]" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="[9-10]" {{ $user_subject->grade == '[9-10]' ? 'selected' : null }}>9-10</option>
                                        <option value="[11-12]" {{ $user_subject->grade == '[11-12]' ? 'selected' : null }}>11-12</option>
                                    </select>                            
                                    @error('grade.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Fee Individual</label>
                                    <input class="form-control form-control-sm rounded" type="text" name="fee_individual[]" value="{{ $user_subject->fee_individual ??  old('fee_individual.'.$loop->index) }}">
                                    @error('fee_individual.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Fee Group</label>
                                    <input class="form-control form-control-sm rounded" type="text" name="fee_group[]" value="{{ $user_subject->fee_group ??  old('fee_group.'.$loop->index) }}">
                                    @error('fee_group.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Additional Fee</label>
                                    <input class="form-control form-control-sm rounded" type="text" name="additional_fee[]" value="{{ $user_subject->additional_fee ??  old('additional_fee.'.$loop->index) }}">
                                    @error('additional_fee.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3 d-flex justify-content-between align-items-end">
                                    <div style="width:{{$loop->index > 0 ? '85' : '100'}}%">
                                        <label for="" class="text-muted">Head</label>
                                        <input class="form-control form-control-sm rounded" type="text" name="head[]" value="{{ $user_subject->head ??  old('head.'.$loop->index) }}">
                                        @error('head.'.$loop->index)
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    @if($loop->index > 0)
                                        <button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteSubject('{{$loop->index}}')"><i class="bi bi-trash2"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @elseif (old('subject_id') !== null)
                    @for($i = 0 ; $i < count(old('subject_id')) ; $i++)
                        <div class="col-md-12 subject">
                            <div class="row g-2">
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Subject Name</label>
                                    <select name="subject_id[]" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                @if ($subject->id == old('subject_id.'.$i)){{ "selected" }}
                                                @endif
                                            >{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject_id.'.$i)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Grade</label>
                                    <select name="grade[]" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="[9-10] {{ old('grade.'.$i) == '[9-10]' ? 'selected' : null }}">9-10</option>
                                        <option value="[11-12]" {{ old('grade.'.$i) == '[11-12]' ? 'selected' : null }}>11-12</option>
                                    </select>                            
                                    @error('grade.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Fee Indiviudal</label>
                                    <input class="form-control form-control-sm rounded" type="text" name="fee_individual[]" value="{{ old('fee_individual.'.$i) }}">
                                    @error('fee_individual.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Fee Group</label>
                                    <input class="form-control form-control-sm rounded" type="text" name="fee_group[]" value="{{ old('fee_group.'.$i) }}">
                                    @error('fee_group.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Additional </label>
                                    <input class="form-control form-control-sm rounded" type="text" name="additional_fee[]" value="{{ old('additional_fee.'.$i) }}">
                                    @error('additional_fee.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3 d-flex justify-content-between align-items-end">
                                    <div style="width:{{$loop->index > 0 ? '85' : '100'}}%">                                    <label for="" class="text-muted">Fee Session</label>
                                        <label for="" class="text-muted">Head</label>
                                        <input class="form-control form-control-sm rounded" type="text" name="head[]">
                                        @error('head.'.$loop->index)
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteSubject('{{$loop->index}}')"><i class="bi bi-trash2"></i></button>
                                </div>
                            </div>
                        </div>
                    @endfor
                @else
                <div class="col-md-12 subject">
                    <div class="row g-2">
                        <div class="col-md-4 mb-3">
                            <label for="" class="text-muted">Agreement</label>
                            <input type="file" name="agreement" class="form-control form-control-sm" id="agreementFile" />                            
                            @error('agreement.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="text-muted">Subject Name</label>
                            <select name="subject_id[]" class="select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="" class="text-muted">Grade</label>
                            <select name="grade[]" class="select w-100">
                                <option data-placeholder="true"></option>
                                <option value="[9-10]">9-10</option>
                                <option value="[11-12]">11-12</option>
                            </select>                            
                            @error('grade.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <h6 style="margin-bottom: -2px;">Fee Detail</h6>
                        <div class="col-md-3 mb-3">
                            <label for="" class="text-muted">Fee Individual</label>
                            <input class="form-control form-control-sm rounded" type="text" name="fee_individual[]">
                            @error('fee_individual.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="" class="text-muted">Fee Group</label>
                            <input class="form-control form-control-sm rounded" type="text" name="fee_group[]">
                            @error('fee_group.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="" class="text-muted">Additional Fee</label>
                            <input class="form-control form-control-sm rounded" type="text" name="additional_fee[]">
                            @error('additional_fee.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="" class="text-muted">Head</label>
                            <input class="form-control form-control-sm rounded" type="text" name="head[]">
                            @error('head.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        {{-- <div class="card" style="margin-top:-10px;">
                            <div class="card-header d-flex justify-content-between align-items-center">
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    
                                </div>
                            </div>
                        </div> --}}
                        
                    </div>
                </div>
                @endif
            {{-- </div> --}}
        </div>
    </div>
</div>

<script>
    function addSubject() {
        let id = Math.floor((Math.random() * 100) + 1);
        $("#subjectField").append(
            '<div class="col-md-12 subject subject-' + id + '">' +
            '<div class="row g-2">' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Subject Name <sup class="text-danger">*</sup></label>' +
                    '<select name="subject_id[]" id="" class="select w-100">' +
                    '<option data-placeholder="true"></option>' +
                    @foreach ($subjects as $subject)
                        '<option value="{{ $subject->id }}">{{ $subject->name }}</option>' +
                    @endforeach
                    '</select>' +
                    @error('subject_id')
                        '<small class="text-danger fw-light">{{ $message }}</small>'
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Grade <sup class="text-danger">*</sup></label>' +
                    '<select name="grade[]" class="select w-100">' +
                        '<option data-placeholder="true"></option>' +
                        '<option value="[9-10]">9-10</option>' +
                        '<option value="[11-12]">11-12</option>' +
                    '</select>' +
                    @error('grade')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Fee Individual <sup class="text-danger">*</sup></label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_individual[]">' +
                    @error('fee_individual')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Fee Group <sup class="text-danger">*</sup></label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_group[]">' +
                    @error('fee_group')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Additional Fee <sup class="text-danger">*</sup></label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="additional_fee[]">' +
                    @error('additional_fee')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3 d-flex justify-content-between align-items-end">' +
                    '<div style="width:85%">' +
                        // '<div class="col-md-3 mb-3">' +
                            '<label for="" class="text-muted">Head <sup class="text-danger">*</sup></label>' +
                            '<input class="form-control form-control-sm rounded" type="text" name="head[]">' +
                            @error('head')
                                '<small class="text-danger fw-light">{{ $message }}</small>' +
                            @enderror
                        // '</div>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteSubject(' + id +
                        ')"><i class="bi bi-trash2"></i></button>' +
                '</div>' +
                
            '</div>' +
            '</div>'
        )

        initSelect2('.subject ')
    }

    function deleteSubject(id) {
        $('.subject-' + id).remove();
    }
</script>
