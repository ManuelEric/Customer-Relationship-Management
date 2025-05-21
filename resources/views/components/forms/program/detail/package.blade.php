<select name="tutor_package" class="select w-100 tutor-package">
    <option data-placeholder="true"></option>
    @switch($programType)
        @case("test-preparation")
            <option value="Group">Group</option>
            <option value="Express">Express</option>
            <option value="Bootcamp">Bootcamp</option>
            <option value="Private">Private</option>
            <option value="Semi Private">Semi Private</option>
            <option value="Hourly">Hourly</option>
            <option value="Deposit Trial">Deposit Trial</option>
        @break

        @case("subject-tutoring")
            <option value="Basic">Basic</option>
            <option value="Pro">Pro</option>
            <option value="Elite">Elite</option>
            <option value="Hourly">Hourly</option>
            <option value="Free Trial">Free Trial</option>
            <option value="Deposit Trial">Deposit Trial</option>
            <option value="Bonus">Bonus</option>
        @break

        @case("competition")
            <option value="Group">Group</option>
        @break

        @case("skillset-tutoring")
            <option value="Private">Private</option>
            <option value="Deposit Trial">Deposit Trial</option>
        @break
    @endswitch
</select>