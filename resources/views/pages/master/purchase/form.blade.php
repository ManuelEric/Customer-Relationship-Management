@extends('layout.main')

@section('title', 'Purchase Request')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Purchase Request</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')

    @php
        $disabled = isset($purchaseRequest) && isset($edit) ? null : (isset($edit) ? null : 'disabled');
    @endphp

    <div class="modal modal-md fade" id="detailModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0 p-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Requested Item
                    </h4>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST" id="detailForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div>
                                    <label for="">Item <sup class="text-danger">*</sup></label>
                                    <input type="text" name="item" id="item"
                                        class="form-control form-control-sm rounded">
                                </div>
                                @error('item')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <div>
                                    <label for="">Amount <sup class="text-danger">*</sup></label>
                                    <input type="number" name="amount" id="amount"
                                        class="form-control form-control-sm rounded">
                                </div>
                                @error('amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <div>
                                    <label for="">Price per Unit <sup class="text-danger">*</sup></label>
                                    <input type="number" name="price_per_unit" id="price"
                                        class="form-control form-control-sm rounded">
                                </div>
                                @error('price_per_unit')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <div>
                                    <label for="">Total <sup class="text-danger">*</sup></label>
                                    <input type="number" name="total" id="total"
                                        class="form-control form-control-sm rounded" readonly>
                                </div>
                                @error('total')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                        data-bs-dismiss="modal">
                                        <i class="bi bi-x me-1"></i>
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3">
                                        <i class="bi bi-save2"></i>
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <form
        action="{{ isset($purchaseRequest) ? route('purchase.update', ['purchase' => $purchaseRequest->purchase_id]) : route('purchase.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($purchaseRequest))
            @method('PUT')
        @endif
        <div class="row g-3">
            <div class="col-md-4 text-center">
                <div class="card rounded">
                    <div class="card-body">
                        <img loading="lazy"  loading="lazy" src="{{ asset('img/icon/purchase.webp') }}" alt="" class="w-25">
                        <h5>
                            {{ isset($purchaseRequest->purchase_id) ? 'Purchase Request No. ' . $purchaseRequest->purchase_id : 'Add New Purchase Request' }}
                        </h5>
                        @if (isset($purchaseRequest))
                            <div class="mt-3 d-flex justify-content-center">
                                @if (isset($edit))
                                    <a href="{{ url('master/purchase/' . strtolower($purchaseRequest->purchase_id)) }}"
                                        class="btn btn-sm btn-outline-info rounded mx-1">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                @else
                                    <a href="{{ url('master/purchase/' . strtolower($purchaseRequest->purchase_id) . '/edit') }}"
                                        class="btn btn-sm btn-outline-info rounded mx-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endif
                                <button type="button"
                                    onclick="confirmDelete('master/purchase', '{{ $purchaseRequest->purchase_id }}')"
                                    class="btn btn-sm btn-outline-danger rounded mx-1">
                                    <i class="bi bi-trash2"></i> Delete
                                </button>
                                <a href="{{ route('purchase.print', ['purchase' => $purchaseRequest->purchase_id]) }}">
                                    <button type="button" class="btn btn-sm btn-outline-info rounded mx-1">
                                        <i class="bi bi-printer"></i> Print
                                    </button>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card rounded">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-tags me-2"></i>
                                Purchase Request
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label for="">
                                        Department <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="purchase_department" class="select w-100" {{ $disabled }}>
                                        <option data-placeholder="true"></option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ (isset($purchaseRequest->purchase_department) && $purchaseRequest->purchase_department == $department->id) || old('purchase_department') == $department->id ? 'selected' : null }}>
                                                {{ $department->dept_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('purchase_department')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="">
                                        Request Status <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="purchase_statusrequest" class="select w-100" {{ $disabled }}>
                                        <option data-placeholder="true"></option>
                                        @for ($i = 0; $i < count($requestStatus); $i++)
                                            <option value="{{ $requestStatus[$i] }}"
                                                {{ (isset($purchaseRequest->purchase_statusrequest) && $purchaseRequest->purchase_statusrequest == $requestStatus[$i]) || old('purchase_statusrequest') == $requestStatus[$i] ? 'selected' : null }}>
                                                {{ $requestStatus[$i] }}</option>
                                        @endfor
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
                                    <input type="date" name="purchase_requestdate"
                                        class="form-control form-control-sm rounded" {{ $disabled }}
                                        value="{{ isset($purchaseRequest->purchase_requestdate) ? $purchaseRequest->purchase_requestdate : old('purchase_requestdate') }}">
                                    @error('purchase_requestdate')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Notes <sup class="text-danger">*</sup>
                                    </label>
                                    <textarea name="purchase_notes" {{ $disabled }}>
                                        {{ isset($purchaseRequest->purchase_notes) ? $purchaseRequest->purchase_notes : old('purchase_notes') }}
                                    </textarea>
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
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($purchaseRequest->purchase_attachment) ? $purchaseRequest->purchase_attachment : null }}"
                                        {{ $disabled }}>
                                    @error('purchase_attachment')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                    @if (isset($purchaseRequest->purchase_attachment))
                                        <small class="text-info fw-light">
                                            {{-- <a href="{{ public_path('storage/uploaded_file/finance/').$purchaseRequest->purchase_attachment }}">Download file</a> --}}
                                            <a
                                                href="{{ route('purchase.download', ['file_name' => $purchaseRequest->purchase_attachment]) }}">Download</a>
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Created By <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="requested_by" class="select w-100" {{ $disabled }}>
                                        <option data-placeholder="true"></option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ (isset($purchaseRequest->requested_by) && $purchaseRequest->requested_by == $employee->id) || old('requested_by') == $employee->id ? 'selected' : null }}>
                                                {{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        @if (empty($purchaseRequest) || isset($edit))
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save2 me-1"></i>
                                Save</button>
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>

        @if (!isset($edit) && isset($purchaseRequest))
            @include('pages.master.purchase.detail.list')
        @endif

    </form>


    @if ($errors->first('item') || $errors->first('amount') || $errors->first('price_per_unit') || $errors->first('total'))
        <script>
            $(document).ready(function() {
                $("#detailModal").modal('show');
            })
        </script>
    @endif
    <script>
        function calculate() {
            var val1 = $('#amount, .amount').val();
            var val2 = $('#price, .amount').val();
            var val3 = val1 * val2;

            $('#total, .amount').val(val3);
        }

        $('#amount').change(function() {
            calculate()
        })

        $("#price").change(function() {
            calculate()
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
                            '<input type="text" name="item[]" class="form-control form-control-sm rounded" value="">' +
                            '</div>' +
                            '</div>' +

                            '<div class="col-md-3">' +
                            '<div class="mb-2">' +
                            '<label for=""> Price <sup class="text-danger">*</sup></label>' +
                            '<input type="number" name="price_per_unit[]" class="form-control form-control-sm rounded"  id="price' +
                            x + '">' +
                            '</div>' +
                            '</div>' +

                            '<div class="col-md-3">' +
                            '<div class="mb-2">' +
                            '<label for=""> Amount <sup class="text-danger">*</sup></label>' +
                            '<input type="number" name="amount[]" class="form-control form-control-sm rounded" id="amount' +
                            x + '">' +
                            '</div>' +
                            '</div>' +

                            ' <div class="col-md-3">' +
                            '<div class="d-flex justify-content-between align-items-end">' +
                            '<div class="mb-2">' +
                            '<label for=""> Total <sup class="text-danger">*</sup></label>' +
                            '<input readonly type="text" name="total[]" class="form-control form-control-sm rounded" id="total' +
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
    <script type="text/javascript">
        function returnData(purchase_id, detail_id) {
            $("#detailForm").append('<input type="hidden" name="_method" value="PUT">');
            Swal.showLoading()
            let link = "{{ url('master/purchase') }}/" + purchase_id.toLowerCase() + '/detail/' + detail_id

            axios.get(link)
                .then(function(response) {
                    // handle success
                    let data = response.data.data
                    $('#item').val(data.item)
                    $('#amount').val(data.amount)
                    $('#price').val(data.price_per_unit)
                    $('#total').val(data.total)

                    $('#detailForm').attr('action', '{{ url('master/purchase') }}/' + data.purchase_id + '/detail/' +
                        data.id)
                    Swal.close()
                    $("#detailModal").modal('show')
                })
                .catch(function(error) {
                    console.log(error)
                    // handle error
                    Swal.close()
                    notification(error.response.data.success, error.response.data.message)
                })
        }

        @if (isset($purchaseRequest))
            function resetForm() {
                $("#detailForm").trigger('reset');
                $("#detailForm").attr('action',
                    "{{ route('purchase.detail.store', ['purchase' => $purchaseRequest->purchase_id]) }}")
                $("#detailForm").find('input[name=_method]').remove()
            }
        @endif
    </script>

@endsection
