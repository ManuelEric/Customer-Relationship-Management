@php
    $curriculumList = ['IBDP', 'IB MYP', 'Cambridge ALevel', 'Cambridge IGCSE', 'Advanced Placement', 'National'];
@endphp
<select name="curriculum" class="form-select form-select-sm w-100 tutor-curriculum" @if(isset($disabled)) {{ $disabled }} @endif>
    <option value="" selected disabled>Select curriculum name</option>
    @if ( $programType == "subject-tutoring" )
        @foreach ($curriculumList as $key => $curriculum)
            <option value="{{ $curriculum }}" 
                @selected(isset($clientProgram) && $clientProgram->curriculum == $curriculum)
                @selected(old('curriculum.'.$textIndex) == $curriculum)    
            >{{ $curriculum }}</option>
        @endforeach
    @endif
</select>
@error('curriculum')
    <small class="text-danger fw-light">{{ $message }}</small>
@enderror