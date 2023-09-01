@extends('app')
@section('title', 'Mailing - STEM + Wonderlab')
@section('style')
@endsection
@section('body')
    <section>
        <div class="container-fluid my-3">
            <div class="row justify-content-center align-items-center" style="height: 100vh">
                <div class="col-md-8">
                    <div class="text-center">
                        <h2>STEM Wonderlab - Mailing</h2>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="" method="POST">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <input type="file" class="form-control">
                                    </div>
                                    <div class="col-5">
                                        <select name="" id="" class="form-select" required>
                                            <option value=""></option>
                                            <option value="VVIP">VVIP</option>
                                            <option value="VIP">VVIP</option>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-sm btn-primary">Send Email</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <input type="file" class="form-control">
                                    </div>
                                    <div class="col-5">
                                        <select name="" id="" class="form-select" required>
                                            <option value=""></option>
                                            <option value="Reminder 1">Reminder 1</option>
                                            <option value="Reminder 2">Reminder 2</option>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-sm btn-primary">Send Email</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
