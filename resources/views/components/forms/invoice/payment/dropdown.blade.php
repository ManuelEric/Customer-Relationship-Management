<div class="me-2">
    <!-- We must ship. - Taylor Otwell -->
    <div class="dropdown">
        <button class="btn btn-outline-warning py-1 dropdown-toggle btn-sm" style="font-size: 11px" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            Payment Link
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" style="font-size: 11px">
            <li><a class="dropdown-item" href="{{ route('redirect.payment.link', ['payment_method' => 'CC', 'installment' => $installment, 'id' => $id]) }}">Credit Card</a></li>
            <li><a class="dropdown-item" href="{{ route('redirect.payment.link', ['payment_method' => 'VA', 'bank' => 'BCA', 'installment' => $installment, 'id' => $id]) }}">Virtual Account - BCA</a></li>
            <li><a class="dropdown-item" href="{{ route('redirect.payment.link', ['payment_method' => 'VA', 'bank' => 'BRI', 'installment' => $installment, 'id' => $id]) }}">Virtual Account - BRI</a></li>
            <li><a class="dropdown-item" href="{{ route('redirect.payment.link', ['payment_method' => 'VA', 'bank' => 'NIAGA', 'installment' => $installment, 'id' => $id]) }}">Virtual Account - CIMB NIAGA</a></li>
            <li><a class="dropdown-item" href="{{ route('redirect.payment.link', ['payment_method' => 'VA', 'bank' => 'MANDIRI', 'installment' => $installment, 'id' => $id]) }}">Virtual Account - MANDIRI</a></li>
        </ul>
    </div>
</div>