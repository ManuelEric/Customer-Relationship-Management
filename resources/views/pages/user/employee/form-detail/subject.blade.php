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
                    @foreach ($user->user_subjects->toQuery()->groupBy(['subject_id', 'year'])->get() as $key1 => $user_subject)
                        <div class="row border py-2 mx-1 mb-1 subject subject-{{ $loop->index }}">
                            <div class="col-md-12">
                                <div class="row g-2">
                                    <div class="col-md-4 mb-3">
                                        @if($user_subject->agreement != null)
                                            <label for="" class="text-muted">Agreement <sup class="text-danger">*</sup></label>
                                            <div class="agreement-container">
                                                <button id="btn-download-{{$user_subject->id}}" type="button" class="btn btn-sm btn-info download" onclick="downloadAgreement('{{$user_subject->id}}')">
                                                    <i class="bi bi-download"></i>
                                                    Download
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger remove" id="btn-remove-{{$user_subject->id}}" onclick="removeAgreement('{{$user_subject->id}}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <div id="agreement-upload-{{$user_subject->id}}" class="upload-file d-flex justify-content-center align-items-center d-none">
                                                    <input type="hidden" name="agreement_text[]" value={{$user_subject->agreement}}>
                                                    <input type="file" name="agreement[]" class="form-control form-control-sm rounded">
                                                    <i id="btn-roolback-{{$user_subject->id}}" class="bi bi-backspace ms-2 cursor-pointer text-danger rollback" onclick="roolbackAgreement('{{$user_subject->id}}')"></i>
                                                </div>
                                                @error('agreement.'. $loop->index)
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        @else
                                            <label for="" class="text-muted">Agreement <sup class="text-danger">*</sup></label>
                                            <input type="file" name="agreement[]" class="form-control form-control-sm" />
                                            @error('agreement.'. $loop->index)
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        @endif
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="" class="text-muted">Subject Name <sup class="text-danger">*</sup></label>
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
                                    <div class="col-md-4 mb-3">
                                        <label for="" class="text-muted">Year <sup class="text-danger">*</sup></label>
                                        <select name="year[]" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @for ($year = date('Y') - 5; $year <= date('Y') + 5; $year++)
                                                <option value="{{$year}}" {{ $user_subject->year == $year ? 'selected' : null }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                        @error('year.'.$loop->index)
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="detail-subject border py-2 px-3 mb-1" id="detail-subject-{{ $key1 }}">
                                        @php
                                            $isub = 0;
                                        @endphp
                                        @foreach ($user->user_subjects->where('subject_id', $user_subject->subject_id)->where('year', $user_subject->year) as $key2 => $sub_user_subject)
                                            <div class="row" id="subjectDetailField-{{$key1 + $key2}}">
                                                <div class="col-md-2 mb-3">
                                                    <label for="" class="text-muted">Grade <sup class="text-danger">*</sup></label>
                                                    <select name="grade[{{$key1}}][]" class="select w-100">
                                                        <option data-placeholder="true"></option>
                                                        <option value="[9,10]" {{ $sub_user_subject->grade == '[9,10]' ? 'selected' : null }}>9-10</option>
                                                        <option value="[11,12]" {{ $sub_user_subject->grade == '[11,12]' ? 'selected' : null }}>11-12</option>
                                                    </select>
                                                    @error('grade.'.$key1.'.'.$isub)
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label for="" class="text-muted">Fee Individual <sup class="text-danger">*</sup></label>
                                                    <input class="form-control form-control-sm rounded" type="text" name="fee_individual[{{$key1}}][]" value="{{ $sub_user_subject->fee_individual ??  old('fee_individual.'.$key1.'.'.$isub) }}">
                                                    @error('fee_individual.'.$key1.'.'.$isub)
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label for="" class="text-muted">Fee Group </label>
                                                    <input class="form-control form-control-sm rounded" type="text" name="fee_group[{{$key1}}][]" value="{{ $sub_user_subject->fee_group ??  old('fee_group.'.$key1.'.'.$isub) }}">
                                                    @error('fee_group.'.$key1.'.'.$isub)
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label for="" class="text-muted">Additional Fee </label>
                                                    <input class="form-control form-control-sm rounded" type="text" name="additional_fee[{{$key1}}][]" value="{{ $sub_user_subject->additional_fee ??  old('additional_fee.'.$key1.'.'.$isub) }}">
                                                    @error('additional_fee.'.$key1.'.'.$isub)
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-3 d-flex justify-content-between align-items-start">
                                                    <div style="width:{{$loop->index > 0 ? '85' : '100'}}%">
                                                        <label for="" class="text-muted">Head </label>
                                                        <input class="form-control form-control-sm rounded" type="text" name="head[{{$key1}}][]" value="{{ $sub_user_subject->head ??  old('head.'.$key1.'.'.$isub) }}">
                                                        @error('head.'.$key1.'.'.$isub)
                                                            <small class="text-danger fw-light">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    @if($loop->index > 0)
                                                        <button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteDetailSubject('{{$key1 + $key2}}')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-trash2"></i></button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-primary py-1" onclick="addDetailSubject('{{$key1}}')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-plus"></i></button>
                                                    @endif
                                                </div>
                                            </div>
                                            @php
                                                $isub++;
                                            @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @elseif (old('count_subject') !== null)
                    @for($i = 0 ; $i < count(old('count_subject')) ; $i++)
                        <div class="row border py-2 mx-1 mb-1 subject">
                            <div class="col-md-12">
                                <input type="hidden" value="1" name="count_subject[]">
                                <div class="row g-2">
                                    <div class="col-md-4 mb-3">
                                        <label for="" class="text-muted">Agreement <sup class="text-danger">*</sup></label>
                                        <input type="file" name="agreement[]" value="" class="form-control form-control-sm" />
                                        @error('agreement.0')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                         @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="" class="text-muted">Subject Name <sup class="text-danger">*</sup></label>
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
                                    <div class="col-md-4 mb-3">
                                        <label for="" class="text-muted">Year <sup class="text-danger">*</sup></label>
                                        <select name="year[]" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @for ($year = date('Y') - 5; $year <= date('Y') + 5; $year++)
                                                <option value="{{$year}}" {{ $year == old('year.'.$i) ? 'selected' : null }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                        @error('year.'.$i)
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="detail-subject border py-2 px-3 mb-1" id="detail-subject-{{ $i }}">
                                        @for ($j=0; $j < count(old('count_subject_detail.'.$i)); $j++)
                                                <div class="row" id="subjectDetailField-{{$j}}">
                                                    <input type="hidden" value="1" name="count_subject_detail[{{$i}}][]">
                                                    <div class="col-md-2 mb-3">
                                                        <label for="" class="text-muted">Grade <sup class="text-danger">*</sup></label>
                                                        <select name="grade[{{$i}}][]" class="select w-100">
                                                            <option data-placeholder="true"></option>
                                                            <option value="[9,10]" {{ old('grade.'.$i.'.'.$j) == '[9,10]' ? 'selected' : null }}>9-10</option>
                                                            <option value="[11,12]" {{ old('grade.'.$i.'.'.$j) == '[11,12]' ? 'selected' : null }}>11-12</option>
                                                        </select>
                                                        @error('grade.'.$i.'.'.$j)
                                                            <small class="text-danger fw-light">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2 mb-3">
                                                        <label for="" class="text-muted">Fee Indiviudal <sup class="text-danger">*</sup></label>
                                                        <input class="form-control form-control-sm rounded" type="text" name="fee_individual[{{$i}}][]" value="{{ old('fee_individual.'.$i.'.'.$j) }}">
                                                        @error('fee_individual.'.$i.'.'.$j)
                                                            <small class="text-danger fw-light">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2 mb-3">
                                                        <label for="" class="text-muted">Fee Group </label>
                                                        <input class="form-control form-control-sm rounded" type="text" name="fee_group[{{$i}}][]" value="{{ old('fee_group.'.$i.'.'.$j) }}">
                                                        @error('fee_group.'.$i.'.'.$j)
                                                            <small class="text-danger fw-light">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2 mb-3">
                                                        <label for="" class="text-muted">Additional </label>
                                                        <input class="form-control form-control-sm rounded" type="text" name="additional_fee[{{$i}}][]" value="{{ old('additional_fee.'.$i.'.'.$j) }}">
                                                        @error('additional_fee.'.$i.'.'.$j)
                                                            <small class="text-danger fw-light">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3 mb-3 d-flex justify-content-between align-items-start">
                                                        <div style="width:{{$i > 0 ? '85' : '100'}}%">
                                                            <label for="" class="text-muted">Head </label>
                                                            <input class="form-control form-control-sm rounded" type="text" name="head[{{$i}}][]" value="{{ old('head.'.$i.'.'.$j)}}">
                                                            @error('head.'.$i.'.'.$j)
                                                                <small class="text-danger fw-light">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                        @if($j > 0)
                                                            <button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteDetailSubject('{{$j}}')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-trash2"></i></button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline-primary py-1" onclick="addDetailSubject('{{$i}}')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-plus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                @else
                <div class="row border py-2 mx-1 mb-1 subject">
                    <div class="col-md-12">
                        <input type="hidden" value="1" name="count_subject[]">
                        <div class="row g-2">
                            <div class="col-md-4 mb-3">
                                <label for="" class="text-muted">Agreement <sup class="text-danger">*</sup></label>
                                <input type="file" name="agreement[]" value="" class="form-control form-control-sm" />
                                @error('agreement.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="" class="text-muted">Subject Name <sup class="text-danger">*</sup></label>
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
                            <div class="col-md-4 mb-3">
                                <label for="" class="text-muted">Year <sup class="text-danger">*</sup></label>
                                <select name="year[]" class="select w-100">
                                    <option data-placeholder="true"></option>
                                    @for ($year = date('Y') - 5; $year <= date('Y') + 5; $year++)
                                        <option value="{{$year}}">{{ $year }}</option>
                                    @endfor
                                </select>
                                @error('year.0')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="detail-subject border py-2 px-3 mb-1" id="detail-subject-0">
                                <div class="row" id="subjectDetailField-0">
                                    <input type="hidden" value="1" name="count_subject_detail[0][]">
                                        <div class="col-md-2 mb-3">
                                            <label for="" class="text-muted">Grade <sup class="text-danger">*</sup></label>
                                            <select name="grade[0][]" class="select w-100">
                                                <option data-placeholder="true"></option>
                                                <option value="[9,10]">9-10</option>
                                                <option value="[11,12]">11-12</option>
                                            </select>
                                            @error('grade.0.0')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="" class="text-muted">Fee Individual <sup class="text-danger">*</sup></label>
                                            <input class="form-control form-control-sm rounded" type="text" name="fee_individual[0][]">
                                            @error('fee_individual.0.0')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="" class="text-muted">Fee Group </label>
                                            <input class="form-control form-control-sm rounded" type="text" name="fee_group[0][]">
                                            @error('fee_group.0.0')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="" class="text-muted">Additional Fee </label>
                                            <input class="form-control form-control-sm rounded" type="text" name="additional_fee[0][]">
                                            @error('additional_fee.0.0')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-3 mb-3 d-flex justify-content-between align-items-start">
                                            <div style="width: 100%">
                                                <label for="" class="text-muted">Head </label>
                                                <input class="form-control form-control-sm rounded" type="text" name="head[0][]">
                                                @error('head.0.0')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary py-1" onclick="addDetailSubject('0')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-plus"></i></button>
                                        </div>
                                </div>
                            </div>
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
        var index = $('.subject').length;
        let id = Math.floor((Math.random() * 100) + 1);
        $("#subjectField").append(
            '<div class="row border py-2 mx-1 mb-1 subject subject-' + id + '">' +
                '<div class="col-md-12">' +
                '<input type="hidden" value="1" name="count_subject[]">' +
                '<div class="row g-2">' +
                    '<div class="col-md-4 mb-3">' +
                        '<label for="" class="text-muted">Agreement <sup class="text-danger">*</sup></label>' +
                        '<input type="file" name="agreement[]" class="form-control form-control-sm" />' +
                        @error('agreement.0')
                            '<small class="text-danger fw-light">{{ $message }}</small>' +
                        @enderror
                    '</div>' +
                    '<div class="col-md-4 mb-3">' +
                        '<label for="" class="text-muted">Subject Name <sup class="text-danger">*</sup></label>' +
                        '<select name="subject_id[]" id="" class="select w-100">' +
                        '<option data-placeholder="true"></option>' +
                        @foreach ($subjects as $subject)
                            '<option value="{{ $subject->id }}">{{ $subject->name }}</option>' +
                        @endforeach
                        '</select>' +
                        @error('subject_id.0')
                            '<small class="text-danger fw-light">{{ $message }}</small>' +
                        @enderror
                    '</div>' +
                    '<div class="col-md-4 mb-3 d-flex justify-content-between align-items-end">' +
                        '<div style="width:85%">' +
                            '<label for="" class="text-muted">Year <sup class="text-danger">*</sup></label>' +
                                '<select name="year[]" class="select w-100">' +
                                    '<option data-placeholder="true"></option>' +
                                    @for ($year = date('Y') - 5; $year <= date('Y') + 5; $year++)
                                        '<option value="{{$year}}">{{ $year }}</option>' +
                                    @endfor
                                '</select>' +
                                @error('year.0')
                                    '<small class="text-danger fw-light">{{ $message }}</small>' +
                                @enderror
                            '</div>' +
                            '<button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteSubject(' + id +
                            ')"><i class="bi bi-trash2"></i></button>' +
                    '</div>' +
                    '<div class="detail-subject border py-2 mx-1 mb-1" id="detail-subject-'+index+'">' +
                        '<div class="row" id="subjectDetailField-'+index+'">' +
                            '<input type="hidden" value="1" name="count_subject_detail['+index+'][]">' +
                            '<div class="col-md-2 mb-3">' +
                                '<label for="" class="text-muted">Grade <sup class="text-danger">*</sup></label>' +
                                '<select name="grade['+index+'][]" class="select w-100">' +
                                    '<option data-placeholder="true"></option>' +
                                    '<option value="[9,10]">9-10</option>' +
                                    '<option value="[11,12]">11-12</option>' +
                                '</select>' +
                                @error('grade.0.0')
                                    '<small class="text-danger fw-light">{{ $message }}</small>' +
                                @enderror
                                '</div>' +
                                '<div class="col-md-2 mb-3">' +
                                    '<label for="" class="text-muted">Fee Individual <sup class="text-danger">*</sup></label>' +
                                    '<input class="form-control form-control-sm rounded" type="text" name="fee_individual['+index+'][]">' +
                                    @error('fee_individual.0.0')
                                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                                    @enderror
                                '</div>' +
                                '<div class="col-md-2 mb-3">' +
                                    '<label for="" class="text-muted">Fee Group </label>' +
                                    '<input class="form-control form-control-sm rounded" type="text" name="fee_group['+index+'][]">' +
                                    @error('fee_group.0.0')
                                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                                    @enderror
                                '</div>' +
                                '<div class="col-md-2 mb-3">' +
                                    '<label for="" class="text-muted">Additional Fee</label>' +
                                    '<input class="form-control form-control-sm rounded" type="text" name="additional_fee['+index+'][]">' +
                                    @error('additional_fee.0.0')
                                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                                    @enderror
                                '</div>' +
                                '<div class="col-md-3 mb-3 d-flex justify-content-between align-items-start">' +
                                    '<div style="width: 100%">' +
                                        '<label for="" class="text-muted">Head</label>' +
                                        '<input class="form-control form-control-sm rounded" type="text" name="head['+index+'][]">' +
                                        @error('head.0.0')
                                            '<small class="text-danger fw-light">{{ $message }}</small>' +
                                        @enderror
                                    '</div>' +
                                    '<button type="button" class="btn btn-sm btn-outline-primary py-1" onclick="addDetailSubject('+index+')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-plus"></i></button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +

                '</div>' +
                '</div>' +
            '</div>'
        )

        initSelect2('.subject ')
    }

    function addDetailSubject(index){
        var indexSub = $('#detail-subject-'+index).length;

        let id = Math.floor((Math.random() * 100) + 1);
        $("#detail-subject-"+index).append(
            '<div class="row" id="subjectDetailField-'+id+'">' +
                '<input type="hidden" value="1" name="count_subject_detail['+index+'][]">' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Grade <sup class="text-danger">*</sup></label>' +
                    '<select name="grade['+index+'][]" class="select w-100">' +
                        '<option data-placeholder="true"></option>' +
                        '<option value="[9,10]">9-10</option>' +
                        '<option value="[11,12]">11-12</option>' +
                    '</select>' +
                    @error('grade.0.0')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Fee Individual <sup class="text-danger">*</sup></label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_individual['+index+'][]">' +
                    @error('fee_individual.0.0')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Fee Group</label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_group['+index+'][]">' +
                    @error('fee_group.0.0')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Additional Fee</label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="additional_fee['+index+'][]">' +
                    @error('additional_fee.0.0')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-3 mb-3 d-flex justify-content-between align-items-start">' +
                    '<div style="width: 100%">' +
                        '<label for="" class="text-muted">Head</label>' +
                        '<input class="form-control form-control-sm rounded" type="text" name="head['+index+'][]">' +
                        @error('head.0.0')
                            '<small class="text-danger fw-light">{{ $message }}</small>' +
                        @enderror
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteDetailSubject('+id+')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-trash2"></i></button>' +
                '</div>' +
            '</div>'
        )
        initSelect2('.detail-subject ')
    }

    function deleteSubject(id) {
        $('.subject-' + id).remove();
    }

    function deleteDetailSubject(id) {
        $('#subjectDetailField-' + id).remove();
    }

    @if(isset($user))
        // agreement button
        function downloadAgreement(id){
            var url = '{{ url("user/" . Request::route('user_role') . "/" . $user->id . "/download_agreement") }}/' + id;
            window.open(url, '_blank');
        }

        function removeAgreement(id){
            $('#agreement-upload-'+id).removeClass('d-none');
            $('#btn-remove-'+id).addClass('d-none');
            $('#btn-download-'+id).addClass('d-none');
            $('#btn-roolback-'+id).removeClass('d-none');
        }

        function roolbackAgreement(id){
            $('#btn-roolback-'+id).addClass('d-none');
            $('#btn-remove-'+id).removeClass('d-none');
            $('#btn-download-'+id).removeClass('d-none');
            $('#agreement-upload-'+id).addClass('d-none');

        }
    @endif

</script>
