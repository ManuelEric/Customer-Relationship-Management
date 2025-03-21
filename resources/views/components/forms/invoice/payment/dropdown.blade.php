<div class="me-2">
    <!-- We must ship. - Taylor Otwell -->
    <div class="dropdown">
        <button class="btn btn-outline-warning py-1 dropdown-toggle btn-sm" style="font-size: 11px" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            Payment Link
        </button>
        <ul class="btn-payment-dropdown dropdown-menu" data-installment="{{ $installment }}" data-index="{{ $id }}" aria-labelledby="dropdownMenuButton1" style="font-size: 11px">
            <li><a class="btn-generate-payment dropdown-item" data-pmethod="CC" href="javascript:void(0)">Credit Card</a></li>
            <li><a class="btn-generate-payment dropdown-item" data-pmethod="VA" data-bname="BCA" href="javascript:void(0)">Virtual Account - BCA</a></li>
            <li><a class="btn-generate-payment dropdown-item" data-pmethod="VA" data-bname="BRI" href="javascript:void(0)">Virtual Account - BRI</a></li>
            <li><a class="btn-generate-payment dropdown-item" data-pmethod="VA" data-bname="NIAGA" href="javascript:void(0)">Virtual Account - CIMB NIAGA</a></li>
            <li><a class="btn-generate-payment dropdown-item" data-pmethod="VA" data-bname="MANDIRI" href="javascript:void(0)">Virtual Account - MANDIRI</a></li>
        </ul>
    </div>
</div>