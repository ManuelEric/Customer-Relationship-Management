<div class="card mb-3" id="university">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="p-0 m-0">New University</h6>
    </div>
    <div class="card-body overflow-auto" style="max-height: 250px">
        <div class="table-responsive">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_newuniv">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>University Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Created at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($universities as $university)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $university->univ_name }}</td>
                            <td>{{ $university->univ_mail }}</td>
                            <td>{{ $university->univ_phone }}</td>
                            <td>{{ $university->univ_address }}</td>
                            <td class="text-center">{{ $university->created_at }}</td>
                        </tr>
                    @empty
                        <td colspan="6" class="text-center">Not new university yet</td>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
