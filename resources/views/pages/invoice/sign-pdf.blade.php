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
                        onclick="addImage(null, null)"></i></button>
            </div>
            <div class="tool">
                <button class="btn btn-danger btn-sm" onclick="deleteSelectedObject(event)"><i
                        class="fa fa-trash"></i></button>
            </div>
            <div class="tool">
                <button class="btn btn-danger btn-sm" onclick="clearPage()">Clear Page</button>
            </div>
            <div class="tool">
                <button class="btn btn-light btn-sm" 
                @if(isset($invoice->schprog_id))
                    onclick="savePDF('save','{{ $attachment }}','{{ route('invoice-sch.upload_signed_document', ['invoice' => $invoice->invb2b_num, 'currency' => $currency]) }}')">
                @elseif(isset($invoice->ref_id))
                    onclick="savePDF('save','{{ $attachment }}','{{ route('invoice-ref.upload_signed_document', ['invoice' => $invoice->invb2b_num, 'currency' => $currency])  }}')">
                @elseif(isset($invoice->partnerprog_id))
                    onclick="savePDF('save','{{ $attachment }}','{{ route('invoice-corp.upload_signed_document', ['invoice' => $invoice->invb2b_num, 'currency' => $currency])  }}')">
                @elseif(isset($invoice->bundling_id))
                    onclick="savePDF('save','{{ $attachment }}','{{ route('invoice.client-program.upload-signed-bundle', ['bundle' => Request::route('bundle'), 'currency' => Request::route('currency')]) }}')">
                @else
                    onclick="savePDF('save','{{ $attachment->attachment }}','{{ route('invoice.client-program.upload-signed', ['client_program' => Request::route('client_program'), 'currency' => Request::route('currency')]) }}')"
                @endif
                       <i class="fa fa-save me-2"></i>
                    Save</button>
            </div>
        </div>
    </div>
    <div id="pdf-container"></div>

<div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
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
</div>

@endsection
@section('script')
<script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (isset($attachment))
            @if (isset($attachment->inv_id))
                var file = "{{ Storage::url('invoice/client/'.$attachment->attachment) }}"
            @elseif (isset($attachment->invoiceB2b->partnerprog_id))
                var file = "{{ Storage::url('invoice/partner_prog/'.$attachment->attachment) }}"
            @elseif (isset($attachment->invoiceB2b->schprog_id))
                var file = "{{ Storage::url('invoice/sch_prog/'.$attachment->attachment) }}"
            @elseif (isset($attachment->invoiceB2b->ref_id))    
                var file = "{{ Storage::url('invoice/referral/'.$attachment->attachment) }}"
            @endif
        @endif

        var pdf = new PDFAnnotate("pdf-container", file, {
            onPageUpdated(page, oldData, newData) {
                console.log(page, oldData, newData);
            },
            ready() {
                console.log("Plugin initialized successfully");
            },
            scale: 1.8,
            pageImageCompression: "SLOW",
            //  FAST, MEDIUM, SLOW(Helps to control the new PDF file size)
        });
        
    </script>
    <script src="{{ asset('js/pdf-annotation/script-pdf.js') }}"></script>
@endsection
