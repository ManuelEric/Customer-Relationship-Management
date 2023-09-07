@extends('app')
@section('title', 'Scanner')
@section('style')
    <style>
        @layer base {

            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        }

        #html5-qrcode-button-camera-stop {
            background: #e87979;
            color: white;
            padding: 0px 10px 3px 10px;
            margin: 10px;
            border-radius: 10px;
            display: block;
            box-shadow: 2px 2px #deddee;
        }

        #html5-qrcode-button-camera-start {
            background: #abe879;
            color: rgb(42, 42, 42);
            padding: 0px 10px 3px 10px;
            margin: 10px;
            border-radius: 10px;
            display: block;
            box-shadow: 2px 2px #deddee;
        }

        #reader__scan_region {
            display: flex;
            justify-content: center;
        }

        #reader__scan_region img {
            width: 25%;
        }
    </style>
@endsection
@section('script')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endsection
@section('body')
    <section>
        <div class="container-fluid">
            <div class="row align-items-stretch">
                <div class="col-8 px-5" style="height: 100vh; background:#233469;">
                    <div class="d-flex align-items-center h-100">
                        <div class="text-white">
                            <h1>Test</h1>
                        </div>
                    </div>
                </div>
                <div class="col-4 px-5">
                    <div class="d-flex align-items-center h-100">
                        <div class="w-100">
                            <div id="reading" class="card text-center shadow d-none">
                                <div class="card-body">
                                    <img width="100"
                                        src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzNzEuNjQzIDM3MS42NDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM3MS42NDMgMzcxLjY0MyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PHBhdGggZD0iTTEwNS4wODQgMzguMjcxaDE2My43Njh2MjBIMTA1LjA4NHoiLz48cGF0aCBkPSJNMzExLjU5NiAxOTAuMTg5Yy03LjQ0MS05LjM0Ny0xOC40MDMtMTYuMjA2LTMyLjc0My0yMC41MjJWMzBjMC0xNi41NDItMTMuNDU4LTMwLTMwLTMwSDEyNS4wODRjLTE2LjU0MiAwLTMwIDEzLjQ1OC0zMCAzMHYxMjAuMTQzaC04LjI5NmMtMTYuNTQyIDAtMzAgMTMuNDU4LTMwIDMwdjEuMzMzYTI5LjgwNCAyOS44MDQgMCAwIDAgNC42MDMgMTUuOTM5Yy03LjM0IDUuNDc0LTEyLjEwMyAxNC4yMjEtMTIuMTAzIDI0LjA2MXYxLjMzM2MwIDkuODQgNC43NjMgMTguNTg3IDEyLjEwMyAyNC4wNjJhMjkuODEgMjkuODEgMCAwIDAtNC42MDMgMTUuOTM4djEuMzMzYzAgMTYuNTQyIDEzLjQ1OCAzMCAzMCAzMGg4LjMyNGMuNDI3IDExLjYzMSA3LjUwMyAyMS41ODcgMTcuNTM0IDI2LjE3Ny45MzEgMTAuNTAzIDQuMDg0IDMwLjE4NyAxNC43NjggNDUuNTM3YTkuOTg4IDkuOTg4IDAgMCAwIDguMjE2IDQuMjg4IDkuOTU4IDkuOTU4IDAgMCAwIDUuNzA0LTEuNzkzYzQuNTMzLTMuMTU1IDUuNjUtOS4zODggMi40OTUtMTMuOTIxLTYuNzk4LTkuNzY3LTkuNjAyLTIyLjYwOC0xMC43Ni0zMS40aDgyLjY4NWMuMjcyLjQxNC41NDUuODE4LjgxNSAxLjIxIDMuMTQyIDQuNTQxIDkuMzcyIDUuNjc5IDEzLjkxMyAyLjUzNCA0LjU0Mi0zLjE0MiA1LjY3Ny05LjM3MSAyLjUzNS0xMy45MTMtMTEuOTE5LTE3LjIyOS04Ljc4Ny0zNS44ODQgOS41ODEtNTcuMDEyIDMuMDY3LTIuNjUyIDEyLjMwNy0xMS43MzIgMTEuMjE3LTI0LjAzMy0uODI4LTkuMzQzLTcuMTA5LTE3LjE5NC0xOC42NjktMjMuMzM3YTkuODU3IDkuODU3IDAgMCAwLTEuMDYxLS40ODZjLS40NjYtLjE4Mi0xMS40MDMtNC41NzktOS43NDEtMTUuNzA2IDEuMDA3LTYuNzM3IDE0Ljc2OC04LjI3MyAyMy43NjYtNy42NjYgMjMuMTU2IDEuNTY5IDM5LjY5OCA3LjgwMyA0Ny44MzYgMTguMDI2IDUuNzUyIDcuMjI1IDcuNjA3IDE2LjYyMyA1LjY3MyAyOC43MzMtLjQxMyAyLjU4NS0uODI0IDUuMjQxLTEuMjQ1IDcuOTU5LTUuNzU2IDM3LjE5NC0xMi45MTkgODMuNDgzLTQ5Ljg3IDExNC42NjEtNC4yMjEgMy41NjEtNC43NTYgOS44Ny0xLjE5NCAxNC4wOTJhOS45OCA5Ljk4IDAgMCAwIDcuNjQ4IDMuNTUxIDkuOTU1IDkuOTU1IDAgMCAwIDYuNDQ0LTIuMzU4YzQyLjY3Mi0zNi4wMDUgNTAuODAyLTg4LjUzMyA1Ni43MzctMTI2Ljg4OC40MTUtMi42ODQuODIxLTUuMzA5IDEuMjI5LTcuODYzIDIuODM0LTE3LjcyMS0uNDU1LTMyLjY0MS05Ljc3Mi00NC4zNDV6bS0yMzIuMzA4IDQyLjYyYy01LjUxNCAwLTEwLTQuNDg2LTEwLTEwdi0xLjMzM2MwLTUuNTE0IDQuNDg2LTEwIDEwLTEwaDE1djIxLjMzM2gtMTV6bS0yLjUtNTIuNjY2YzAtNS41MTQgNC40ODYtMTAgMTAtMTBoNy41djIxLjMzM2gtNy41Yy01LjUxNCAwLTEwLTQuNDg2LTEwLTEwdi0xLjMzM3ptMTcuNSA5My45OTloLTcuNWMtNS41MTQgMC0xMC00LjQ4Ni0xMC0xMHYtMS4zMzNjMC01LjUxNCA0LjQ4Ni0xMCAxMC0xMGg3LjV2MjEuMzMzem0zMC43OTYgMjguODg3Yy01LjUxNCAwLTEwLTQuNDg2LTEwLTEwdi04LjI3MWg5MS40NTdjLS44NTEgNi42NjgtLjQzNyAxMi43ODcuNzMxIDE4LjI3MWgtODIuMTg4em03OS40ODItMTEzLjY5OGMtMy4xMjQgMjAuOTA2IDEyLjQyNyAzMy4xODQgMjEuNjI1IDM3LjA0IDUuNDQxIDIuOTY4IDcuNTUxIDUuNjQ3IDcuNzAxIDcuMTg4LjIxIDIuMTUtMi41NTMgNS42ODQtNC40NzcgNy4yNTEtLjQ4Mi4zNzgtLjkyOS44LTEuMzM1IDEuMjYxLTYuOTg3IDcuOTM2LTExLjk4MiAxNS41Mi0xNS40MzIgMjIuNjg4aC05Ny41NjRWMzBjMC01LjUxNCA0LjQ4Ni0xMCAxMC0xMGgxMjMuNzY5YzUuNTE0IDAgMTAgNC40ODYgMTAgMTB2MTM1LjU3OWMtMy4wMzItLjM4MS02LjE1LS42OTQtOS4zODktLjkxNC0yNS4xNTktMS42OTQtNDIuMzcgNy43NDgtNDQuODk4IDI0LjY2NnoiLz48cGF0aCBkPSJNMTc5LjEyOSA4My4xNjdoLTI0LjA2YTUgNSAwIDAgMC01IDV2MjQuMDYxYTUgNSAwIDAgMCA1IDVoMjQuMDZhNSA1IDAgMCAwIDUtNVY4OC4xNjdhNSA1IDAgMCAwLTUtNXpNMTcyLjYyOSAxNDIuODZoLTEyLjU2VjEzMC44YTUgNSAwIDEgMC0xMCAwdjE3LjA2MWE1IDUgMCAwIDAgNSA1aDE3LjU2YTUgNSAwIDEgMCAwLTEwLjAwMXpNMjE2LjU2OCA4My4xNjdoLTI0LjA2YTUgNSAwIDAgMC01IDV2MjQuMDYxYTUgNSAwIDAgMCA1IDVoMjQuMDZhNSA1IDAgMCAwIDUtNVY4OC4xNjdhNSA1IDAgMCAwLTUtNXptLTUgMjQuMDYxaC0xNC4wNlY5My4xNjdoMTQuMDZ2MTQuMDYxek0yMTEuNjY5IDEyNS45MzZIMTk3LjQxYTUgNSAwIDAgMC01IDV2MTQuMjU3YTUgNSAwIDAgMCA1IDVoMTQuMjU5YTUgNSAwIDAgMCA1LTV2LTE0LjI1N2E1IDUgMCAwIDAtNS01eiIvPjwvc3ZnPg=="
                                        alt="Camera based scan" style="opacity: 0.8;">
                                    <div class="mt-3">
                                        Please, completing the form!
                                    </div>
                                </div>
                            </div>
                            <div id="reader" class="rounded shadow p-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade modal-lg" tabindex="-1" id="clientDetail">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ url('client-detail') }}" frameborder="0" width="100%" height="360"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeModal() {
            $('#clientDetail').modal('hide')
            $('#reading').addClass('d-none')
            $('#reader').removeClass('d-none')
            html5QrcodeScanner.render(onScanSuccess)
        }

        function onScanSuccess(decodedText, decodedResult) {
            showLoading();
            
            const url = decodedText;
            const arrSegments = url.split('/');
            const maxIndexes = arrSegments.length - 1;

            const identifier = arrSegments[maxIndexes];
            let source = "{{ url('client-detail') }}/" + identifier;

            var iframe = $("#client-detail-ctx")
            iframe.attr('src', source)

            // console.log(`Scan result: ${decodedText}`, decodedResult);
            // Handle on success condition with the decoded text or result.
            // $('#clientDetail').modal('show')
            // window.location.href = `${decodedText}`;
            html5QrcodeScanner.clear();
            $('#reading').removeClass('d-none')
            $('#reader').addClass('d-none')
        }

        function onScanError(errorMessage) {
            console.warn('Please scan your QR-Code!');
        }

        // initialiaze scanner 
        var html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: 350
            });
        html5QrcodeScanner.render(onScanSuccess);

        function submitUpdate()
        {
            window.location.reload();
        }
    </script>
@endsection
