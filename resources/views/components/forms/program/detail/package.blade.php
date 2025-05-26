@php
    $testPreparationList = ['Group', 'Express', 'Bootcamp', 'Private', 'Semi Private', 'Hourly', 'Deposit Trial'];
    $subjectTutoringList = ['Basic', 'Pro', 'Elite', 'Hourly', 'Free Trial', 'Deposit Trial', 'Bonus'];
    $competitionList = ['Group'];
    $skillsetTutoringList = ['Private', 'Deposit Trial'];
@endphp
<select name="package[{{ $programType }}][]" class="select w-100 tutor-package" @if(isset($disabled)) {{ $disabled }} @endif>
    <option data-placeholder="true"></option>
    @switch($programType)
        @case("test-preparation")
            @foreach($testPreparationList as $key => $package)
                <option value="{{ $package }}" 
                    @selected(isset($clientProgram) && $clientProgram->package == $package)
                    @selected(old('package') == $package)    
                >{{ $package }}</option>
            @endforeach
        @break

        @case("subject-tutoring")
            @foreach($subjectTutoringList as $key => $package)
                <option value="{{ $package }}" 
                    @selected(isset($clientProgram) && $clientProgram->package == $package)
                    @selected(old('package') == $package)    
                >{{ $package }}</option>
            @endforeach
        @break

        @case("competition")
            @foreach($competitionList as $key => $package)
                <option value="{{ $package }}" 
                    @selected(isset($clientProgram) && $clientProgram->package == $package)
                    @selected(old('package') == $package)    
                >{{ $package }}</option>
            @endforeach
        @break

        @case("skillset-tutoring")
            @foreach ($skillsetTutoringList as $key => $package)
                <option value="{{ $package }}" 
                    @selected(isset($clientProgram) && $clientProgram->package == $package)
                    @selected(old('package') == $package)    
                >{{ $package }}</option>
            @endforeach
        @break
    @endswitch
</select>
@error('package.'.$programType.'.'.$textIndex)
    <small class="text-danger fw-light">{{ $message }}</small>
@enderror