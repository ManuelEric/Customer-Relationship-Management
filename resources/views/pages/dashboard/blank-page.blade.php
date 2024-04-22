@extends('layout.main')

@section('title', 'Dashboard - Bigdata Platform')

@section('css')
    <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
    
<style>
   .quote {
    margin: 0;
    background: #eee;
    padding: 1em;
    border-radius: 1em;
    }
    .quote figcaption,
    .quote blockquote {
    margin: 1em;
    }
</style>
<figure class="quote">
    <blockquote>
        <h5>
            {{ $data->q }}
        </h5>
    </blockquote>
    <figcaption>
      &mdash; {{$data->a}}  </figcaption>
</figure>

<div class="alert alert-primary text-black mt-3" role="alert">
    Create your <a href="#" id="liveToastBtn" class="alert-link">own dashboard</a> or go to <a href="{{ url('/dashboard') }}" class="alert-link">general dashboard</a> 
</div>


<div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center w-100">

    <!-- Then put toasts within -->
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <i class="bi bi-motherboard"></i>
        <strong class="ms-2 me-auto">System</strong>
        {{-- <small>11 mins ago</small> --}}
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        This feature is currently under development. &#128579
      </div>
    </div>
  </div>


<script>
    const toastTrigger = document.getElementById('liveToastBtn')
    const toastLiveExample = document.getElementById('liveToast')

    if (toastTrigger) {
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
        toastTrigger.addEventListener('click', () => {
            toastBootstrap.show()
        })
    }
</script>

@endsection
