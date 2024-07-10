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
                                <div class="col-md-6 mb-3">
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
                                <div class="col-md-3 mb-3">
                                    <label for="" class="text-muted">Fee Hours</label>
                                    <input class="form-control form-control-sm rounded" type="text" name="fee_hours[]" value="{{ $user_subject->fee_hours ??  old('fee_hours.'.$loop->index) }}">
                                    @error('fee_hours.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3 d-flex justify-content-between align-items-end">
                                    <div style="width:{{$loop->index > 0 ? '85' : '100'}}%">
                                        <label for="" class="text-muted">Fee Session</label>
                                        <input class="form-control form-control-sm rounded" type="text" name="fee_session[]" value="{{ $user_subject->fee_session ??  old('fee_session.'.$loop->index) }}">
                                        @error('fee_session.'.$loop->index)
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
                                <div class="col-md-4 mb-3">
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
                                <div class="col-md-3 mb-3">
                                    <label for="" class="text-muted">Fee Hours</label>
                                    <input class="form-control form-control-sm rounded" type="text" name="fee_hours[]" value="{{ old('fee_hours.'.$i) }}">
                                    @error('fee_hours.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3 d-flex justify-content-between align-items-end">
                                    <div style="width:{{$loop->index > 0 ? '85' : '100'}}%%">                                    <label for="" class="text-muted">Fee Session</label>
                                        <input class="form-control form-control-sm rounded" type="text" name="fee_session[]" value="{{ old('fee_session.'.$i) }}">
                                        @error('fee_session.'.$loop->index)
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
                        <div class="col-md-6 mb-3">
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
                            <label for="" class="text-muted">Fee Hours</label>
                            <input class="form-control form-control-sm rounded" type="text" name="fee_hours[]">
                            @error('fee_hours.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="" class="text-muted">Fee Session</label>
                            <input class="form-control form-control-sm rounded" type="text" name="fee_session[]">
                            @error('fee_session.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
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
                '<div class="col-md-6 mb-3">' +
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
                '<div class="col-md-3 mb-3">' +
                    '<label for="" class="text-muted">Fee Hours <sup class="text-danger">*</sup></label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_hours[]">' +
                    @error('fee_hours')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-3 mb-3 d-flex justify-content-between align-items-end">' +
                    '<div style="width:85%">' +
                        // '<div class="col-md-3 mb-3">' +
                            '<label for="" class="text-muted">Fee Session <sup class="text-danger">*</sup></label>' +
                            '<input class="form-control form-control-sm rounded" type="text" name="fee_session[]">' +
                            @error('fee_session')
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
