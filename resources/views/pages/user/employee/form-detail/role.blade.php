<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 p-0">Role & Position</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 mb-3">
                @php
                    $roles = [];
                    if (isset($user->roles))
                        $roles = $user->roles()->pluck('tbl_roles.id')->toArray();
                @endphp
                <label for="">Role <sup class="text-danger">*</sup></label>
                <select name="role[]" id="" class="select w-100" multiple>
                    <option data-placeholder="true"></option>
                    <option value="1"
                        @if (isset($user))
                            @selected(in_array(1, $roles))
                        @else
                            @selected(Request::route('user_role') == 'employee')
                        @endif
                        >Employee</option>
                    <option value="2" 
                        @if (isset($user))
                            @selected(in_array(2, $roles))
                        @else
                            @selected(Request::route('user_role') == 'mentor')
                        @endif
                        >Mentor</option>
                    <option value="3" 
                        @if (isset($user))
                            @selected(in_array(3, $roles))
                        @else
                            @selected(Request::route('user_role') == 'editor')
                        @endif
                        >Editor</option>
                    <option value="4" 
                        @if (isset($user))
                            @selected(in_array(4, $roles))
                        @else
                            @selected(Request::route('user_role') == 'tutor')
                        @endif
                        >Tutor</option>
                    @if ($isAdmin)
                    <option value="8" 
                        @if (isset($user))
                            @selected(in_array(8, $roles))
                        @else
                            @selected(Request::route('user_role') == 'admin')
                        @endif
                        >Admin</option>
                    @endif
                </select>
                @error('role.*')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-3 mb-3">
                @php
                $departmentId = [];
                if (isset($user) && $typeInfo = $user->user_type()->where('tbl_user_type_detail.status', 1)->first()) 
                    $departmentId = $typeInfo->pivot->department_id
                @endphp
                <label for="">Department <sup class="text-danger">*</sup></label>
                <select name="department" id="" class="select w-100">
                    <option data-placeholder="true"></option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" 
                            @selected($departmentId == $department->id)
                            @selected(old('department') == $department->id)>{{ $department->dept_name }}</option>
                    @endforeach
                </select>
                @error('department')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="">Position <sup class="text-danger">*</sup></label>
                <select name="position" id="" class="select w-100">
                    <option data-placeholder="true"></option>
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}" 
                            @selected(isset($user) && isset($user->position) && $user->position->id == $position->id) 
                            @selected(old('position') == $position->id)>{{ $position->position_name }}</option>
                    @endforeach
                </select>
                @error('position')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="row mt-md-0 mt-4">
            <div class="col-md-3 mt-md-0 mt-3">
                <label for="">Hire Date <sup class="text-danger">*</sup></label>
                <input type="date" name="hiredate" id="" class="form-control form-control-sm rounded" value="{{ isset($user->hiredate) ? $user->hiredate : old('hiredate') }}">
                @error('hiredate')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-3 mt-md-0 mt-3">
                <label for="">Employee Type <sup class="text-danger">*</sup></label>
                <select name="type" id="employeeType" class="select w-100" onchange="employeeTypeCheck()">
                    <option data-placeholder="true"></option>
                    @foreach ($user_types as $user_type)
                        <option value="{{ $user_type->id }}"
                            @selected(isset($user) && in_array($user_type->id, $user->user_type()->where('tbl_user_type_detail.status', 1)->pluck('tbl_user_type.id')->toArray()))
                            @selected(old('type') == $user_type->id)
                            >{{ $user_type->type_name }}</option>
                    @endforeach
                </select>
                @error('type')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-3 mt-md-0 mt-3 period start-period">
                <label for="">Start Period <sup class="text-danger">*</sup></label>
                <input type="date" name="start_period" id="" class="form-control form-control-sm rounded" value="{{ isset($typeInfo) ? $typeInfo->pivot->start_date : old('start_period') }}">
                @error('start_period')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-3 mt-md-0 mt-3 period end-period">
                <label for="">End Period <sup class="text-danger">*</sup></label>
                <input type="date" name="end_period" id="" class="form-control form-control-sm rounded" value="{{ isset($typeInfo) ? $typeInfo->pivot->end_date : old('end_period') }}">
                @error('end_period')
                    <small class="text-danger fw-light">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        
        @if (old('type'))
            $("#employeeType").select2().val("{{ old('type') }}").trigger('change')
        @endif

        @if (isset($user) && $employeeType = $user->user_type()->where('tbl_user_type_detail.status', 1)->first())
            $("#employeeType").select2().val("{{ $employeeType->pivot->user_type_id }}").trigger('change')
        @endif

    })

    function employeeTypeCheck() {
        let val = $('#employeeType').find(':selected').text();
        $('.period').addClass('d-none')
        if (val == 'Full-Time') {
            $('.start-period').removeClass('d-none')
        } else {
            $('.start-period').removeClass('d-none')
            $('.end-period').removeClass('d-none')
        }
    }
</script>
