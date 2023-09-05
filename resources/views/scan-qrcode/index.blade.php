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
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe src="" id="client-detail-ctx" frameborder="0" width="100%" height="360"></iframe>
            </div>
          </div>
        </div>
      </div>
    
    <script>
        $(function() {
            $("#client-detail-ctx").on('load', function() {
                $('#clientDetail').modal('show');
                swal.close();

            });
        })

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
