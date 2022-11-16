<div class="row">
    <div class="col-12 mt-5">
        <div class="d-flex justify-content-between align-items-end">
            <h5 class="m-0 p-0"><i class="bi bi-list me-1"></i> List of Item Requested</h5>
        </div>
    </div>
    <div class="col-12 mt-2">
        <table class="table table-bordered table-hover nowrap align-middle w-100">
            <thead class="bg-dark text-white">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Amount (unit)</th>
                    <th>Price/Unit</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1
                @endphp
                @foreach ($purchaseRequest->detail as $detail)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $detail->item }}</td>
                    <td>{{ $detail->amount }}</td>
                    <td>{{ 'Rp '. number_format($detail->price_per_unit, 2, ",", ".") }}</td>
                    <td>{{ 'Rp '. number_format($detail->total, 2, ",", ".") }}</td>
                    <td>
                        <a href="?detail={{ $detail->id }}">
                            <button type="button" class="btn btn-info">Edit</button>
                        </a>
                        <button class="btn btn-danger">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>