<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 p-0">Education Detail</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEducation()"><i
                class="bi bi-plus"></i></button>
    </div>
    <div class="card-body" id="educationContent">
        <div class="row" id="educationField">
            <div class="col-md-12 education">
                <div class="row g-2">
                    <div class="col-md-4 mb-3">
                        <label for="" class="text-muted">Graduated From</label>
                        <select name="graduated_from[]" id="" class="select w-100">
                            <option data-placeholder="true"></option>
                        </select>
                        @error('graduated_from')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="" class="text-muted">Degree</label>
                        <select name="degree[]" id="" class="select w-100">
                            <option data-placeholder="true"></option>
                            <option value="Bachelor">Bachelor</option>
                            <option value="Magister">Magister</option>
                        </select>
                        @error('degree')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="" class="text-muted">Major</label>
                        <select name="major[]" id="" class="select w-100">
                            <option data-placeholder="true"></option>
                        </select>
                        @error('major')
                            <small class="text-danger fw-light">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
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
            '<label for="" class="text-muted">Graduated From</label>' +
            '<select name="graduated_from[]" id="" class="select w-100">' +
            '<option data-placeholder="true"></option>' +
            '</select>' +
            @error('graduated_from')
                '<small class="text-danger fw-light">{{ $message }}</small>'
            @enderror
            '</div>' +
            '<div class="col-md-4 mb-3">' +
            '<label for="" class="text-muted">Degree</label>' +
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
            '<label for="" class="text-muted">Major</label>' +
            '<select name="major[]" id="" class="select w-100">' +
            '<option data-placeholder="true"></option>' +
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
