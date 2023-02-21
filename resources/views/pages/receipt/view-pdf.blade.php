@extends('layout.pdf')
@section('title', 'RECEIPT - VIEW')
@section('body')
    <div class="toolbar">
        <div class="tool">
            <span>RECEIPT</span>
        </div>
        <div class="tool">
            <button class="tool-button active"><i class="fa fa-hand-paper-o" title="Free Hand"
                    onclick="enableSelector(event)"></i></button>
        </div>
        <div class="tool">
            <button class="btn btn-light btn-sm" onclick="savePDF('print','{{$receiptAttachment->attachment}}')"><i class="fa fa-print"
                    title="Print"></i> Print</button>
        </div>
    </div>
    <div id="pdf-container"></div>
@endsection
@section('script')
    <script>
        var pdf = new PDFAnnotate("pdf-container", "{{ asset('storage/uploaded_file/receipt/'.$receiptAttachment->attachment) }}", {
            onPageUpdated(page, oldData, newData) {
                console.log(page, oldData, newData);
            },
            ready() {
                console.log("Plugin initialized successfully");
            },
            scale: 1.7,
            pageImageCompression: "SLOW", // FAST, MEDIUM, SLOW(Helps to control the new PDF file size)
        });
    </script>
    <script src="{{ asset('js/pdf-annotation/script-pdf.js') }}"></script>
@endsection
