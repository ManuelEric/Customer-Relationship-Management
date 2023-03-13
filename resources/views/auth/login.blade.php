@extends('app')

@section('title', 'Login | Bigdata Platform')

@section('body')
    <style>
        #main {
            height: 100vh;
        }
    </style>

    <section id="main">
        <div class="container-fluid h-100 p-0">
            <div class="row h-100 g-0">
                <div class="col-6 h-100 bg-light">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <img src="{{ asset('img/logo.png') }}" alt="" class="w-25">
                            <img src="{{ asset('img/login.png') }}" alt="" class="w-75">
                        </div>
                    </div>
                </div>

                <div class="col-6 h-100 bg-dark">
                    <div class="container h-100">
                        <div class="row align-items-center justify-content-center h-100">
                            <div class="col-6 text-white">
                                <form action="{{ route('login.action') }}" method="POST">
                                    @csrf
                                    <h3 class="text-center">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>
                                        LOG IN
                                    </h3>
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    <div class="my-3">
                                        <label for="">Email</label>
                                        <input type="text" class="form-control @error('email') is-invalid @enderror"
                                            name="email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Password</label>
                                        <input type="password" class="form-control  @error('password') is-invalid @enderror"
                                            name="password">
                                    </div>
                                    <div class="pt-3">
                                        <button class="btn btn-primary w-100" type="submit">
                                            Submit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
