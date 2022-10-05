@extends('app')

@section('title', 'Bigdata Platform')

@section('body')

    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">BIGDATA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="navbar-nav me-auto mb-2 mb-lg-0">

                </div>
                <div class="d-flex">
                    <a href="{{ route('/login') }}" class="btn btn-outline-success" type="submit">Log In</a>
                </div>
            </div>
        </div>
    </nav>

@endsection
