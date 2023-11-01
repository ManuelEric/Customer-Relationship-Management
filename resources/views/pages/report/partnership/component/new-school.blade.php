<div class="card mb-3" id="school">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="p-0 m-0">New School</h6>
    </div>
    <div class="card-body overflow-auto" style="max-height: 250px">
        <div class="table-responsive">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_newsch">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>School Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Created at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schools as $school)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $school->sch_name }}</td>
                            <td>{{ $school->sch_mail }}</td>
                            <td>{{ $school->sch_phone }}</td>
                            <td>{!! $school->sch_location !!}</td>
                            <td class="text-center">{{ $school->created_at }}</td>
                        </tr>
                    @empty
                        <td colspan="6" class="text-center">Not new school yet</td>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
