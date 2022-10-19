@extends('layout.main')

@section('title', 'Purchase Request - Bigdata Platform')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('master/purchase') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Purchase Request
        </a>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <form action="" method="POST">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('img/purchase.webp') }}" alt="" class="w-75">
                    </div>
                    <div class="col-md-8">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label for="">
                                        Department <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="dept_id" class="select w-100">
                                        <option data-placeholder="true"></option>
                                    </select>
                                    @error('dept_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Request Status <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="purchase_statusrequest" class="select w-100">
                                        <option data-placeholder="true"></option>
                                    </select>
                                    @error('purchase_statusrequest')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="">
                                        Request Date <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="date" name="purchase_date" class="form-control form-control-sm rounded"
                                        value="">
                                    @error('purchase_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Notes <sup class="text-danger">*</sup>
                                    </label>
                                    <textarea name="purchase_notes"></textarea>
                                    @error('purchase_notes')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Attachment <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="file" name="purchase_attachment"
                                        class="form-control form-control-sm rounded" value="">
                                    @error('purchase_attachment')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Created By <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="user_id" class="form-control form-control-sm rounded"
                                        value="">
                                    @error('user_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="d-flex justify-content-between align-items-end">
                            <h5 class="m-0 p-0"><i class="bi bi-list me-1"></i> Items</h5>
                            <button type="button" class="btn btn-sm btn-secondary add_more_button"><i
                                    class="bi bi-plus me-1"></i> Add
                                Item</button>
                        </div>
                        <hr class="mt-2">
                        <div class="container" id="item">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="">
                                            Item Name <sup class="text-danger">*</sup>
                                        </label>
                                        <input type="text" name="purchasedtl_good[]"
                                            class="form-control form-control-sm rounded" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="">
                                            Price <sup class="text-danger">*</sup>
                                        </label>
                                        <input type="number" name="purchasedtl_price[]" id="price"
                                            class="form-control form-control-sm rounded" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="">
                                            Amount <sup class="text-danger">*</sup>
                                        </label>
                                        <input type="number" name="purchasedtl_amount[]" id="amount"
                                            class="form-control form-control-sm rounded" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="">
                                            Total <sup class="text-danger">*</sup>
                                        </label>
                                        <input type="text" name="purchasedtl_total[]" id="total" readonly
                                            class="form-control form-control-sm rounded" value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save2 me-1"></i>
                                Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        $('#amount').keyup(function() {
            var val1 = $('#amount').val();
            var val2 = $('#price').val();
            var val3 = val1 * val2;

            $('#total').val(val3);
        })

        $(document).ready(function() {
            var max_fields_limit = 5; //set limit for maximum input fields
            var x = $('#item .row').length
            // var x = 1; //initialize counter for text box
            $('.add_more_button').click(function(
                e) { //click event on add more fields button having class add_more_button
                e.preventDefault();

                if (x < max_fields_limit) { //check conditions

                    if ($('#total').val() == '' || $('#total' + (x)).val() == '') {
                        notif('error', 'Please fill in the item first!')
                    } else {
                        x++; //counter increment
                        console.log(x)
                        $('#item').append(
                            '<div class="row item' + x + '">' +
                            '<div class="col-md-3">' +
                            '<div class="mb-2">' +
                            '<label for=""> Item Name <sup class="text-danger">*</sup></label>' +
                            '<input type="text" name="purchasedtl_good[]" class="form-control form-control-sm rounded" value="">' +
                            '</div>' +
                            '</div>' +

                            '<div class="col-md-3">' +
                            '<div class="mb-2">' +
                            '<label for=""> Price <sup class="text-danger">*</sup></label>' +
                            '<input type="number" name="purchasedtl_priceperunit[]" class="form-control form-control-sm rounded"  id="price' +
                            x + '">' +
                            '</div>' +
                            '</div>' +

                            '<div class="col-md-3">' +
                            '<div class="mb-2">' +
                            '<label for=""> Amount <sup class="text-danger">*</sup></label>' +
                            '<input type="number" name="purchasedtl_amount[]" class="form-control form-control-sm rounded" id="amount' +
                            x + '">' +
                            '</div>' +
                            '</div>' +

                            ' <div class="col-md-3">' +
                            '<div class="d-flex justify-content-between align-items-end">' +
                            '<div class="mb-2">' +
                            '<label for=""> Total <sup class="text-danger">*</sup></label>' +
                            '<input readonly type="text" name="purchasedtl_total[]" class="form-control form-control-sm rounded" id="total' +
                            x + '">' +
                            '</div>' +
                            '<div class="mb-2">' +
                            '<button type="button" class="btn btn-sm btn-danger remove_field float-right" data-item="' +
                            x + '"><i class="bi bi-trash2"></i></button>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +


                            '</div>'
                        ); //add input field

                        $('#amount' + x).keyup(function() {
                            var val1 = $('#amount' + x).val();
                            var val2 = $('#price' + x).val();
                            var val3 = val1 * val2;

                            $('#total' + x).val(val3);
                        })
                    }

                } else {
                    notif('error', 'Item has exceeded the maximum limit')
                }
            });

            $('#item').on("click", ".remove_field", function(
                e) { //user click on remove text links
                e.preventDefault();
                let index = $(this).data('item')
                $('.item' + index).remove();
                x = $('#item .row').length
            })


            function notif(status, title) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: status,
                    title: title
                })
            }
        });
    </script>

@endsection
