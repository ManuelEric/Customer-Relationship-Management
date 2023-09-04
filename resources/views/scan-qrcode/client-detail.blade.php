@extends('app')
@section('title', 'Confirmation')
@section('style')
    <style>
        input {
            border: 0 !important;
            border-bottom: 1px solid #16236a !important;
            border-radius: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 3px 0 !important;
        }
    </style>
@endsection
@section('body')
    <section>
        <div class="container-fluid my-3">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-8">
                    <form action="#" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-4 mb-3">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label>Number of Attend <span class="text-danger">*</span></label>
                                <input type="number" name="" id="" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Your Child's Name <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Your Child's Email <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Child's Number <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label>School Name <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Graduation Year <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-4 mb-3">
                                <label>Destination Country <span class="text-danger">*</span></label>
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-sm btn-primary"><i
                                            class="bi bi-send me-2"></i> Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function test() {
            parent.$('#clientDetail').modal('hide')
        }
    </script>
@endsection
