@extends('app')
@section('title', 'Import School')
@section('style')
@endsection
@section('body')
    <section>
        <div class="container-fluid my-3">
            <div class="row justify-content-center align-items-center" style="height: 100vh">
                <div class="col-md-8">
                    <div class="text-center">
                        <h2>Import School</h2>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="" method="POST" id="importSchool" enctype="multipart/form-data">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <input type="file" name="file" class="form-control" required>
                                    </div>
                                    <div class="col-5">
                                        <select id="type" class="form-select" required onchange="checkType()">
                                            <option value=""></option>
                                            <option value="merge" selected>Merge</option>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-sm btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <input type="file" class="form-control">
                                    </div>
                                    <div class="col-5">
                                        <select name="file" id="" class="form-select" required>
                                            <option value=""></option>
                                            
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-sm btn-primary">Send Email</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </section>

    <script>
        function checkType(){
            let type = $('#type').val()
            $('#invitation').attr('action', '{{url("instance/school/import/merge")}}');
                   
        }
    </script>
@endsection

