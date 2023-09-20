<html style="height: 100%;" ma1="ma">

<head>
    <meta name="viewport" content="width=device-width, minimum-scale=0.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

</head>

<body style="margin: auto; height: 200px; width:200px; background-color: rgb(14, 14, 14);">

    <div>

        {{-- <div class="card border-0" style="background-color: rgb(14, 14, 14);"> --}}
            <div class="text-center position-relative" id="my-node" style="background-color: rgb(14, 14, 14);">
                <div class="w-100 h-100 position-absolute top-0 start-0 d-flex align-items-center justify-content-center">
                    <div class="bg-white p-2 rounded-3" style="width: 40px;">
                        <img src="{{ asset('img/favicon.png') }}" alt="" class="w-100">
                    </div>
                </div>
                {!! QrCode::size(200)->generate($url) !!}
                {{-- <img style="display: block;-webkit-user-select: none;margin: auto;background-color: hsl(0, 0%, 90%);transition: background-color 300ms;"
                    src="https://api.qrserver.com/v1/create-qr-code/?size={{ $size }}x{{ $size }}&amp;data={{ $url }}"> --}}
            </div>
        {{-- </div> --}}
    </div>
    <br>

    <div id="a"></div>


</body>

<script>
    const screenshotTarget = document.getElementById('my-node');

    html2canvas(screenshotTarget).then((canvas) => {
        const base64image = canvas.toDataURL("image/png");
        document.body.appendChild(canvas);
        var img = document.createElement("img"); // create an image object
        image.src = canvas.toDataURL("image/png"); // get canvas content as data URI
        document.getElementById('a').appendChild(image);
        // var anchor = document.createElement('a');
        //             anchor.setAttribute("href", base64image);
        //             anchor.setAttribute("download", "my-qr.png");
        //             anchor.click();
        //             anchor.remove();
    });
</script>

</html>
