<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
    </head>

    <body>
        
        <div class="card border-0">
            <div class="card-body p-0 text-center position-relative">
                <div class="w-100 h-100 position-absolute top-0 start-0 d-flex align-items-center justify-content-center">
                    <div class="bg-white p-2 rounded-3" style="width: 40px;">
                        <img src="{{asset('img/favicon.png')}}" alt="" class="w-100" >
                    </div>
                </div>
                {!! QrCode::size(200)->generate($url) !!}
            </div>
        </div>
        <img src="http://127.0.0.1:8000/api/create-qr/200?url=https://makerspace.all-inedu.com/" alt="">
    </body>
    
</html>
            