<div class="card rounded mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-person me-2"></i>
                Invoice Detail
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-warning py-1">
                <i class="bi bi-eye"></i> View Invoice
            </button>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-hover">
            <tr>
                <td width="20%">Invoice ID :</td>
                <td>INV-12312/24124/12412</td>
            </tr>
            <tr>
                <td>Price :</td>
                <td>
                    $20 (Rp. 300.000)
                </td>
            </tr>
            <tr>
                <td>Session :</td>
                <td>
                    3x
                </td>
            </tr>
            <tr>
                <td>Duration :</td>
                <td>
                    60 Minutes
                </td>
            </tr>
            <tr>
                <td>Early Bird :</td>
                <td>
                    $20 (Rp. 300.000)
                </td>
            </tr>
            <tr>
                <td>Discount Bird :</td>
                <td>
                    $20 (Rp. 300.000)
                </td>
            </tr>
            <tr>
                <td>Total Price :</td>
                <td>
                    $20 (Rp. 300.000)
                </td>
            </tr>
        </table>

        {{-- IF INSTALLMENT EXIST  --}}
        <div class="mt-3">
            Installment List
            <table class="table table-bordered table-hover">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Due Date</th>
                        <th>Percentage</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>No</td>
                        <td>Name</td>
                        <td>Due Date</td>
                        <td>Percentage</td>
                        <td>Amount</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
