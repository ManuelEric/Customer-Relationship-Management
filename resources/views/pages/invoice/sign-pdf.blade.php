@extends('layout.pdf')
@section('title', 'INVOICE - SIGN NEED')
@section('body')
    <div class="toolbar d-flex justify-content-between">
        <div class="">
            <div class="tool">
                <span>INVOICE - SIGN</span>
            </div>
        </div>
        <div class="">
            <div class="tool">
                <button class="tool-button active"><i class="fa fa-hand-paper-o" title="Free Hand"
                        onclick="enableSelector(event)"></i></button>
            </div>
            <div class="tool">
                <button class="tool-button"><i class="fa fa-pencil" title="Pencil"
                        onclick="enablePencil(event)"></i></button>
            </div>
            <div class="tool">
                <button class="tool-button"><i class="fa fa-picture-o" title="Add an Image"
                        onclick="addImage(event)"></i></button>
            </div>
            <div class="tool">
                <button class="btn btn-danger btn-sm" onclick="deleteSelectedObject(event)"><i
                        class="fa fa-trash"></i></button>
            </div>
            <div class="tool">
                <button class="btn btn-danger btn-sm" onclick="clearPage()">Clear Page</button>
            </div>
            <div class="tool">
                <button class="btn btn-light btn-sm" onclick="savePDF('save','{{ $invoice->attachment }}','{{ route('invoice.program.upload-signed', ['client_program' => Request::route('client_program')]) }}')"><i
                        class="fa fa-save me-2"></i>
                    Save</button>
            </div>
        </div>
    </div>
    <div id="pdf-container"></div>

@endsection
@section('script')
    <script>

        var pdf = new PDFAnnotate("pdf-container", "{{ asset('storage/uploaded_file/invoice/'.$invoice->attachment) }}", {
            onPageUpdated(page, oldData, newData) {
                console.log(page, oldData, newData);
            },
            ready() {
                console.log("Plugin initialized successfully");
            },
            scale: 1.8,
            pageImageCompression: "MEDIUM", // FAST, MEDIUM, SLOW(Helps to control the new PDF file size)
        });

        
    </script>
    <script src="{{ asset('js/pdf-annotation/script-pdf.js') }}"></script>
@endsection
