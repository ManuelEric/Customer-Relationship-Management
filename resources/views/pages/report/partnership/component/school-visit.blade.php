<div class="card mb-3" id="school_visit">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="p-0 m-0">School Visit</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_schvisit">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>School Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Internal PIC</th>
                        <th>School PIC</th>
                        <th>Visit Date</th>
                        <th>Created at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schoolVisits as $schoolVisit)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $schoolVisit->school->sch_name }}</td>
                            <td>{{ $schoolVisit->school->sch_mail }}</td>
                            <td>{{ $schoolVisit->school->sch_phone }}</td>
                            <td>{!! $schoolVisit->school->sch_location !!}</td>
                            <td>{{ $schoolVisit->pic_from_allin->first_name }}
                                {{ $schoolVisit->pic_from_allin->last_name }}</td>
                            <td>{{ $schoolVisit->pic_from_school->schdetail_fullname }}</td>
                            <td class="text-center">{{ $schoolVisit->visit_date }}</td>
                            <td class="text-center">{{ $schoolVisit->created_at }}</td>
                        </tr>
                    @empty
                        <td colspan="9" class="text-center">Not school visit yet</td>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
