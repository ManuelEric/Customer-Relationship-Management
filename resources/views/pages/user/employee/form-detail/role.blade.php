<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 p-0">Role & Position</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 mb-3">
                <label for="">Role</label>
                <select name="role[]" id="" class="select w-100" multiple>
                    <option data-placeholder="true"></option>
                    <option value="Employee">Employee</option>
                    <option value="Mentor">Mentor</option>
                    <option value="Tutor">Tutor</option>
                </select>
                @error('role')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-3 mb-3">
                <label for="">Department</label>
                <select name="department" id="" class="select w-100">
                    <option data-placeholder="true"></option>
                    <option value="Client Management">Client Management</option>
                    <option value="Digital">Digital</option>
                    <option value="IT">IT</option>
                </select>
                @error('department')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-4 mb-3">
                <label for="">Position</label>
                <select name="position" id="" class="select w-100">
                    <option data-placeholder="true"></option>
                </select>
                @error('position')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label for="">Hire Date</label>
                <input type="date" name="hire_date" id="" class="form-control form-control-sm rounded">
                @error('hire_date')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-3">
                <label for="">Employee Type</label>
                <select name="role[]" id="employeeType" class="select w-100" onchange="employeeTypeCheck()">
                    <option data-placeholder="true"></option>
                    <option value="Full Time">Full Time</option>
                    <option value="Probation">Probation</option>
                    <option value="Internship">Internship</option>
                </select>
                @error('role')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-3 period start-period">
                <label for="">Start Period</label>
                <input type="date" name="start_period" id="" class="form-control form-control-sm rounded">
                @error('start_period')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-3 period end-period">
                <label for="">End Period</label>
                <input type="date" name="end_period" id="" class="form-control form-control-sm rounded">
                @error('end_period')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
    function employeeTypeCheck() {
        let val = $('#employeeType').val();
        $('.period').addClass('d-none')
        if (val == 'Full Time') {
            $('.start-period').removeClass('d-none')
        } else {
            $('.start-period').removeClass('d-none')
            $('.end-period').removeClass('d-none')
        }
    }
</script>
