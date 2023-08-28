@extends('layout.pdf')
@section('title', 'RECEIPT - SIGN NEED')
@section('body')
    <div class="toolbar d-flex justify-content-between">
        <div class="">
            <div class="tool">
                <span>RECEIPT - SIGN</span>
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
                        onclick="addImage(null, null)"></i></button>
            </div>
            <div class="tool">
                <button class="btn btn-danger btn-sm" onclick="deleteSelectedObject(event)"><i
                        class="fa fa-trash"></i></button>
            </div>
            <div class="tool">
                <button class="btn btn-danger btn-sm" onclick="clearPage()">Clear Page</button>
            </div>
            {{-- <div class="tool">
                <button class="btn btn-info btn-sm" onclick="showPdfData()">{}</button>
            </div> --}}
            <div class="tool">
                <button class="btn btn-light btn-sm"
                    @if (isset($receipt->invoiceB2b)) 
                        @if ($receipt->invoiceB2b->schprog_id)
                            onclick="savePDF('save','{{ $attachment }}','{{ isset($receipt) ? url('api/receipt-sch/' . $receipt->id . '/upload/' . $currency) : '' }}')">
                        @elseif($receipt->invoiceB2b->ref_id)
                            onclick="savePDF('save','{{ $attachment }}','{{ isset($receipt) ? url('api/receipt-ref/' . $receipt->id . '/upload/' . $currency) : '' }}')">
                        @elseif($receipt->invoiceB2b->partnerprog_id)
                            onclick="savePDF('save','{{ $attachment }}','{{ isset($receipt) ? url('api/receipt-corp/' . $receipt->id . '/upload/' . $currency) : '' }}')"> 
                        @endif
                    @else
                        {{-- onclick="savePDF('save','{{ $attachment->attachment }}','{{ route('receipt.client-program.upload-signed', ['receipt' => Request::route('receipt'), 'currency' => Request::route('currency')]) }}')" --}}
                        onclick="savePDF('save','{{ $attachment->attachment }}','{{ route('receipt.client-program.upload-signed', ['receipt' => Request::route('receipt'), 'currency' => Request::route('currency')]) }}')"
                    @endif
                    <i class="fa fa-save me-2"></i>
                    Save</button>
                {{-- <button class="btn btn-light btn-sm" onclick="savePDF('save','{{ $attachment->attachment }}','{{ isset($receipt) ? url('api/receipt/'.$receipt->id.'/upload/'.$currency) : '' }}')"><i
                        class="fa fa-save me-2"></i>
                    Save</button> --}}
            </div>
        </div>
    </div>
    <div id="pdf-container"></div>

    {{-- <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="dataModalLabel">PDF annotation data</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<pre class="prettyprint lang-json linenums">
				</pre>
			</div>
		</div>
	</div>
</div> --}}
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        @if (isset($attachment) && gettype($attachment) != "string")
            var file = "{{ asset('storage/uploaded_file/receipt/client/'.$attachment->attachment) }}"
        @else
            var file = "{{ asset($attachment) }}"
        @endif
    
        var pdf = new PDFAnnotate("pdf-container", file, {
            onPageUpdated(page, oldData, newData) {
                // console.log(page, oldData, newData);
            },
            ready() {
                console.log("Plugin initialized successfully");
            },
            scale: 1.8,
            pageImageCompression: "SLOW", // FAST, MEDIUM, SLOW(Helps to control the new PDF file size)
        });
    </script>
    <script src="{{ asset('js/pdf-annotation/script-pdf.js') }}"></script>
@endsection
