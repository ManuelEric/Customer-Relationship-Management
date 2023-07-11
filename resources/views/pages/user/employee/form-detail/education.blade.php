<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 p-0">Education Detail</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEducation()"><i
                class="bi bi-plus"></i></button>
    </div>
    <div class="card-body" id="educationContent">
        <div class="row" id="educationField">
            {{-- <div class="col-md-12 education"> --}}
                @if (isset($user) && count($user->educations) > 0 )
                    @foreach ($user->educations as $education)
                        <div class="col-md-12 education edu-{{ $loop->index }}">
                            <div class="row g-2">
                                <div class="col-md-4 mb-3">
                                    <label for="" class="text-muted">Graduated From</label>
                                    <select name="graduated_from[]" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($univ_countries as $country)
                                            <optgroup label="{{ $country->univ_country }}">
                                                @foreach ($universities->where('univ_country', $country->univ_country) as $university)
                                                    <option value="{{ $university->univ_id }}"
                                                        @if ($education->univ_id == $university->univ_id)
                                                            {{ "selected" }}
                                                        @endif
                                                        >{{ $university->univ_name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('graduated_from.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Graduation Date</label>
                                    <input class="form-control form-control-sm rounded" type="date" name="graduation_date[]" value="{{ $education->pivot->graduation_date ??  old('graduation_date.'.$loop->index) }}">
                                    @error('graduation_date.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Degree</label>
                                    <select name="degree[]" id="" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="Bachelor" @selected($education->pivot->degree == "Bachelor")>Bachelor</option>
                                        <option value="Magister" @selected($education->pivot->degree == "Magister")>Magister</option>
                                    </select>
                                    @error('degree.'.$loop->index)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div @class([
                                    'col-md-4',
                                    'mb-3',
                                    'd-flex justify-content-between align-items-end' => $loop->index > 0,
                                ])>
                                    @if ($loop->index > 0)
                                    <div style="width: 85%">
                                    @endif
                                        <label for="" class="text-muted">Major</label>
                                        <select name="major[]" id="" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @foreach ($majors as $major)
                                                <option value="{{ $major->id }}" @selected($education->pivot->major_id == $major->id)>{{ $major->name }}</option>                                
                                            @endforeach
                                        </select>
                                        @error('major.'.$loop->index)
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    @if ($loop->index > 0)
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteEducation('{{ $loop->index }}')"><i class="bi bi-trash2"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @elseif (old('graduated_from') !== null)
                    @for($i = 0 ; $i < count(old('graduated_from')) ; $i++)
                        <div class="col-md-12 education">
                            <div class="row g-2">
                                <div class="col-md-4 mb-3">
                                    <label for="" class="text-muted">Graduated From</label>
                                    <select name="graduated_from[]" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($univ_countries as $country)
                                            <optgroup label="{{ $country->univ_country }}">
                                                @foreach ($universities->where('univ_country', $country->univ_country) as $university)
                                                    <option value="{{ $university->univ_id }}" 
                                                        @if (old('graduated_from.'.$i) == $university->univ_id)
                                                            {{ "selected" }}
                                                        @endif
                                                        >{{ $university->univ_name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('graduated_from.'.$i)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Graduation Date</label>
                                    <input class="form-control form-control-sm rounded" type="date" name="graduation_date[]" value="{{ old('graduation_date.'.$i) }}">
                                    @error('graduation_date.'.$i)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="" class="text-muted">Degree</label>
                                    <select name="degree[]" id="" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="Bachelor" @selected(old('degree.'.$i) == "Bachelor")>Bachelor</option>
                                        <option value="Magister" @selected(old('degree.'.$i) == "Magister")>Magister</option>
                                    </select>
                                    @error('degree.'.$i)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="" class="text-muted">Major</label>
                                    <select name="major[]" id="" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        @foreach ($majors as $major)
                                            <option value="{{ $major->id }}" @selected(old('major.'.$i) == $major->id)>{{ $major->name }}</option>                                
                                        @endforeach
                                    </select>
                                    @error('major.'.$i)
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endfor
                @else
                <div class="col-md-12 education">
                    <div class="row g-2">
                        <div class="col-md-4 mb-3">
                            <label for="" class="text-muted">Graduated From</label>
                            <select name="graduated_from[]" class="select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($univ_countries as $country)
                                    <optgroup label="{{ $country->univ_country }}">
                                        @foreach ($universities->where('univ_country', $country->univ_country) as $university)
                                            <option value="{{ $university->univ_id }}">{{ $university->univ_name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('graduated_from.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="" class="text-muted">Graduation Date</label>
                            <input class="form-control form-control-sm rounded" type="date" name="graduation_date[]">
                            @error('graduation_date.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="" class="text-muted">Degree</label>
                            <select name="degree[]" id="" class="select w-100">
                                <option data-placeholder="true"></option>
                                <option value="Bachelor">Bachelor</option>
                                <option value="Magister">Magister</option>
                            </select>
                            @error('degree.0')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="text-muted">Major</label>
                            <select name="major[]" id="" class="select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($majors as $major)
                                    <option value="{{ $major->id }}">{{ $major->name }}</option>                                
                                @endforeach
                            </select>
                            @error('major.0')
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
    function addEducation() {
        let id = Math.floor((Math.random() * 100) + 1);
        $("#educationField").append(
            '<div class="col-md-12 education edu-' + id + '">' +
            '<div class="row g-2">' +
                '<div class="col-md-4 mb-3">' +
                    '<label for="" class="text-muted">Graduated From <sup class="text-danger">*</sup></label>' +
                    '<select name="graduated_from[]" id="" class="select w-100">' +
                    '<option data-placeholder="true"></option>' +
                    @foreach ($univ_countries as $country)
                        '<optgroup label="{{ $country->univ_country }}">' +
                            @foreach ($universities->where('univ_country', $country->univ_country) as $university)
                                '<option value="{{ $university->univ_id }}">{{ $university->univ_name }}</option>' +
                            @endforeach
                        '</optgroup>' +
                    @endforeach
                    '</select>' +
                    @error('graduated_from')
                        '<small class="text-danger fw-light">{{ $message }}</small>'
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Graduation Date</label>' +
                    '<input class="form-control form-control-sm rounded" type="date" name="graduation_date[]">' +
                    @error('graduation_date')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-2 mb-3">' +
                    '<label for="" class="text-muted">Degree <sup class="text-danger">*</sup></label>' +
                    '<select name="degree[]" id="" class="select w-100">' +
                    '<option data-placeholder="true"></option>' +
                    '<option value="Bachelor">Bachelor</option>' +
                    '<option value="Magister">Magister</option>' +
                    '</select>' +
                    @error('degree')
                        '<small class="text-danger fw-light">{{ $message }}</small>'
                    @enderror
                '</div>' +
                '<div class="col-md-4 mb-3 d-flex justify-content-between align-items-end">' +
                    '<div style="width:85%">' +
                        '<label for="" class="text-muted">Major <sup class="text-danger">*</sup></label>' +
                        '<select name="major[]" id="" class="select w-100">' +
                        '<option data-placeholder="true"></option>' +
                        @foreach ($majors as $major)
                            '<option value="{{ $major->id }}">{{ $major->name }}</option>' +                            
                        @endforeach
                        '</select>' +
                        @error('major')
                            '<small class="text-danger fw-light">{{ $message }}</small>'
                        @enderror
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteEducation(' + id +
                    ')"><i class="bi bi-trash2"></i></button>' +
                '</div>' +
            '</div>' +
            '</div>'
        )

        initSelect2('.education ')
    }

    function deleteEducation(id) {
        $('.edu-' + id).remove();
    }
</script>
