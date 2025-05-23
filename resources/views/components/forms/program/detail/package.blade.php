<select name="package" class="select w-100 tutor-package">
    <option data-placeholder="true"></option>
    @switch($programType)
        @case("test-preparation")
            <option value="Group" @selected(isset($clientProgram) && $clientProgram->package == "Group")>Group</option>
            <option value="Express" @selected(isset($clientProgram) && $clientProgram->package == "Express")>Express</option>
            <option value="Bootcamp" @selected(isset($clientProgram) && $clientProgram->package == "Bootcamp")>Bootcamp</option>
            <option value="Private" @selected(isset($clientProgram) && $clientProgram->package == "Private")>Private</option>
            <option value="Semi Private" @selected(isset($clientProgram) && $clientProgram->package == "Semi Private")>Semi Private</option>
            <option value="Hourly" @selected(isset($clientProgram) && $clientProgram->package == "Hourly")>Hourly</option>
            <option value="Deposit Trial" @selected(isset($clientProgram) && $clientProgram->package == "Deposit Trial")>Deposit Trial</option>
        @break

        @case("subject-tutoring")
            <option value="Basic" @selected(isset($clientProgram) && $clientProgram->package == "Basic")>Basic</option>
            <option value="Pro" @selected(isset($clientProgram) && $clientProgram->package == "Pro")>Pro</option>
            <option value="Elite" @selected(isset($clientProgram) && $clientProgram->package == "Elite")>Elite</option>
            <option value="Hourly" @selected(isset($clientProgram) && $clientProgram->package == "Hourly")>Hourly</option>
            <option value="Free Trial" @selected(isset($clientProgram) && $clientProgram->package == "Free Trial")>Free Trial</option>
            <option value="Deposit Trial" @selected(isset($clientProgram) && $clientProgram->package == "Deposit Trial")>Deposit Trial</option>
            <option value="Bonus" @selected(isset($clientProgram) && $clientProgram->package == "Bonus")>Bonus</option>
        @break

        @case("competition")
            <option value="Group" @selected(isset($clientProgram) && $clientProgram->package == "Group")>Group</option>
        @break

        @case("skillset-tutoring")
            <option value="Private" @selected(isset($clientProgram) && $clientProgram->package == "Private")>Private</option>
            <option value="Deposit Trial" @selected(isset($clientProgram) && $clientProgram->package == "Deposit Trial")>Deposit Trial</option>
        @break
    @endswitch
</select>