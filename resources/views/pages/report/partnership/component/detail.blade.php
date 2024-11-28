@if (count($school_visits) > 0 || count($school_programs) > 0 || count($partner_programs) > 0)
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="p-0 m-0">Partnership Detail</h6>
        </div>
        <div class="card-body">
            @if (count($school_visits) > 0)
                <div class="card mb-1 border border-danger text-info">
                    <div class="card-body d-flex justify-content-between align-items-center py-2">
                        <strong class="">
                            Total School Visit
                        </strong>
                        <h5 class="text-end m-0 badge bg-info text-white">
                            {{ count($school_visits) }}
                        </h5>
                    </div>
                </div>
            @endif

            @if (count($school_programs) > 0)
                <div class="card mb-1 border border-danger text-info">
                    <div class="card-body d-flex justify-content-between align-items-center py-2">
                        <strong class="">
                            Total School Program
                        </strong>
                        <h5 class="text-end m-0 badge bg-info text-white">
                            {{ count($school_programs) }}
                        </h5>
                    </div>
                </div>
            @endif

            @if (count($partner_programs) > 0)
                <div class="card mb-1 border border-danger text-info">
                    <div class="card-body d-flex justify-content-between align-items-center py-2">
                        <strong class="">
                            Total Partner Program
                        </strong>
                        <h5 class="text-end m-0 badge bg-info text-white">
                            {{ count($partner_programs) }}
                        </h5>
                    </div>
                </div>
            @endif

            @if (count($referrals_in) > 0)
                <div class="card mb-1 border border-danger text-info">
                    <a href="#referral-in"
                        class="card-body d-flex justify-content-between align-items-center py-2 text-decoration-none text-info">
                        <strong class="">
                            Total Referral In
                        </strong>
                        <h5 class="text-end m-0 badge bg-info text-white">
                            {{ count($referrals_in) }}
                        </h5>
                    </a>
                </div>
            @endif

            @if (count($referrals_out) > 0)
                <div class="card mb-1 border border-danger text-info">
                    <a href="#referral-out"
                        class="card-body d-flex justify-content-between align-items-center py-2 text-decoration-none text-info">
                        <strong class="">
                            Total Referral Out
                        </strong>
                        <h5 class="text-end m-0 badge bg-info text-white">
                            {{ count($referrals_out) }}
                        </h5>
                    </a>
                </div>
            @endif

            @if (count($schools) > 0)
                <div class="card mb-1 border border-danger text-info">
                    <a href="#school"
                        class="card-body d-flex justify-content-between align-items-center py-2 text-decoration-none text-info">
                        <strong class="">
                            Total New School
                        </strong>
                        <h5 class="text-end m-0 badge bg-info text-white">
                            {{ count($schools) }}
                        </h5>
                    </a>
                </div>
            @endif

            @if (count($partners) > 0)
                <div class="card mb-1 border border-danger text-info">
                    <a href="#partner"
                        class="card-body d-flex justify-content-between align-items-center py-2 text-decoration-none text-info">
                        <strong class="">
                            Total New Partner
                        </strong>
                        <h5 class="text-end m-0 badge bg-info text-white">
                            {{ count($partners) }}
                        </h5>
                    </a>
                </div>
            @endif

            @if (count($universities) > 0)
                <div class="card mb-1 border border-danger text-info">
                    <a href="#university"
                        class="card-body d-flex justify-content-between align-items-center py-2 text-decoration-none text-info">
                        <strong class="">
                            Total New University
                        </strong>
                        <h5 class="text-end m-0 badge bg-info text-white">
                            {{ count($universities) }}
                        </h5>
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
