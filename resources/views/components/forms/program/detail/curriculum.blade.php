@php
    $curriculumList = ['IBDP', 'IB MYP', 'Cambridge ALevel', 'Cambridge IGCSE', 'Advanced Placement', 'National'];
@endphp
<select name="curriculum[{{ $textIndex }}]" class="select w-100 tutor-curriculum" @if(isset($disabled)) {{ $disabled }} @endif>
    <option data-placeholder="true"></option>
    @if ( $programType == "subject-tutoring" )
        @foreach ($curriculumList as $key => $curriculum)
            <option value="{{ $curriculum }}" 
                @selected(isset($clientProgram) && $clientProgram->curriculum == $curriculum)
                @selected(old('curriculum.'.$textIndex) == $curriculum)    
            >{{ $curriculum }}</option>
        @endforeach
    @endif
</select>
@error('curriculum.'.$textIndex)
    <small class="text-danger fw-light">{{ $message }}</small>
@enderror