<select name="curriculum" class="select w-100 tutor-curriculum">
    <option data-placeholder="true"></option>
    @if ( $programType == "subject-tutoring" )
        <option value="IBDP">IBDP</option>
        <option value="IB_MYP">IB MYP</option>
        <option value="Cambridge_ALevel">Cambridge A-Level</option>
        <option value="Cambridge_IGCSE">Cambridge IGCSE</option>
        <option value="Advanced_Placement">Advanced Placement</option>
        <option value="National">National</option>
    @endif
</select>