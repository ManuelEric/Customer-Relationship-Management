<div class="card mb-3" id="partner">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="p-0 m-0">New Partner</h6>
    </div>
    <div class="card-body overflow-auto" style="max-height: 250px">
        <div class="table-responsive">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_newpartner">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Partner Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Created at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($partners as $partner)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $partner->corp_name }}</td>
                            <td>{{ $partner->corp_mail }}</td>
                            <td>{{ $partner->corp_phone }}</td>
                            <td>{{ $partner->corp_address }}</td>
                            <td class="text-center">{{ $partner->created_at }}</td>
                        </tr>
                    @empty
                        <td colspan="6" class="text-center">Not new partner yet</td>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
