<div class="row">
    <div class="col-12 mt-5">
        <div class="d-flex justify-content-between align-items-end">
            <h5 class="m-0 p-0"><i class="bi bi-list me-1"></i> List of Item Requested</h5>
            <button type="button" class="btn btn-sm btn-secondary" onclick="resetForm()" data-bs-target="#detailModal" data-bs-toggle="modal"><i
                class="bi bi-plus me-1"></i> Add
            Item</button>
        </div>
    </div>
    <div class="col-12 mt-4">
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
                @forelse ($purchaseRequest->detail as $detail)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $detail->item }}</td>
                    <td>{{ $detail->amount }}</td>
                    <td>{{ 'Rp '. number_format($detail->price_per_unit, 2, ",", ".") }}</td>
                    <td>{{ 'Rp '. number_format($detail->total, 2, ",", ".") }}</td>
                    <td align="center">
                        <button type="button" onclick="returnData('{{ $detail->purchase_id }}', '{{ $detail->id }}')" class="btn btn-sm btn-outline-warning mx-1" data-bs-target="#detailModal"><i class="bi bi-pencil"></i></button>
                        <button type="button" onclick="confirmDelete('master/purchase/{{ $detail->purchase_id }}/detail', '{{ $detail->id }}')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" align="center">There's no listing item</td>
                </tr>

                @endforelse
            </tbody>
        </table>
    </div>
</div>