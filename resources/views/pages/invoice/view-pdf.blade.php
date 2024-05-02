@extends('layout.pdf')
@section('title', 'INVOICE - VIEW')
@section('body')
<style>
    @media print {
        #pdf-container > .canvas-container{
            position: unset !important;
            
        }
        canvas, .canvas-container {
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
        }
    }
    
</style>
    <div class="toolbar">
        <div class="tool">
            <span>INVOICE</span>
        </div>
        <div class="tool">
            <button class="tool-button active"><i class="fa fa-hand-paper-o" title="Free Hand"
                    onclick="enableSelector(event)"></i></button>
        </div>
        <div class="tool">
            <button class="btn btn-light btn-sm" 
                @if (isset($attachment) && $attachment->inv_id != NULL)
                    onclick="savePDF('print','{{$attachment->attachment}}')"><i class="fa fa-print"
                @elseif (isset($invoiceAttachment))
                    onclick="savePDF('print','{{$invoiceAttachment->attachment}}')"><i class="fa fa-print"
                @endif
                    title="Print"></i> Print</button>
        </div>
    </div>
    <div id="pdf-container"></div>
@endsection
@section('script')
 {{-- var pdf = new PDFAnnotate("pdf-container", "{{ asset('storage/uploaded_file/invoice/'.$invoiceAttachment->attachment) }}", { --}}
 <script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (isset($attachment) && $attachment->inv_id != NULL)
            var file = "{{ asset('storage/uploaded_file/invoice/client/'.$attachment->attachment) }}"
        @elseif (isset($invoiceAttachment))
            var file = "{{ asset($invoiceAttachment->attachment) }}"
        @endif

        var pdf = new PDFAnnotate("pdf-container", file, {
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
    <script>
        if (window.matchMedia) {
            var mediaQueryList = window.matchMedia('print');
            mediaQueryList.addListener(function(mql) {
                if (mql.matches) {
                    beforePrint();
                } else {
                    afterPrint();
                }
            });
        }
        
        function printPDF()
        {
            $(".toolbar").css({'display': 'none'});
            window.print();
        }

        var afterPrint = function() {
            $(".toolbar").css({'display': 'block'})
        };
        var beforePrint = function() {
            console.log('Functionality to run before printing.');
        };
    </script>
@endsection
