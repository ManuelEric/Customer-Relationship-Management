<div class="card mb-3" id="referral-out">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="p-0 m-0">Referral Out</h6>
    </div>
    <div class="card-body overflow-auto" style="max-height: 250px">
        <div class="table-responsive">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="tbl_ref_out">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Partner Name</th>
                        <th>Program Name</th>
                        <th>Participants</th>
                        <th>Referral Fee IDR</th>
                        <th>Referral Fee USD</th>
                        <th>Referral Fee SGD</th>
                        <th>Referral Fee GBP</th>
                        <th>PIC</th>
                        <th>Created at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($referrals_out as $referral)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $referral->partner->corp_name }}</td>
                            <td>{{ $referral->additional_prog_name }}</td>
                            <td>{{ $referral->number_of_student }}</td>
                            <td>Rp. {{ number_format($referral->revenue) }}</td>
                            <td>{{ $referral->currency == 'USD' ? '$. ' . number_format($referral->revenue_other) : '-' }}
                            </td>
                            <td>{{ $referral->currency == 'SGD' ? 'S$. ' . number_format($referral->revenue_other) : '-' }}
                            </td>
                            <td>{{ $referral->currency == 'GBP' ? '£. ' . number_format($referral->revenue_other) : '-' }}
                            </td>
                            <td>{{ $referral->user->first_name }} {{ $referral->user->last_name }}</td>
                            <td class="text-center">{{ $referral->created_at }}</td>
                        </tr>
                    @empty
                        <td colspan="10" class="text-center">Not new referral yet</td>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Total Amount</th>
                        <th>Rp. {{ number_format($referrals_out->sum('revenue')) }}</th>
                        <th>$. {{ number_format($referrals_out->where('currency', 'USD')->sum('revenue_other')) }}
                        </th>
                        <th>S$. {{ number_format($referrals_out->where('currency', 'SGD')->sum('revenue_other')) }}
                        </th>
                        <th>£. {{ number_format($referrals_out->where('currency', 'GBP')->sum('revenue_other')) }}
                        </th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>
