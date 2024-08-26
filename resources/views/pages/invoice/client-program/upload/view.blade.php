@extends('app')

@section('title', 'Upload Invoice Here')

@section('body')

    <div class="container" style="height: 100vh">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-md-7">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <img loading="lazy"  src="{{ asset('img/logo.webp') }}" alt="" class="w-25">
                            <h3 class="p-0 m-0">INVOICE</h3>
                        </div>
                        <hr>
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6">
                                <h6 class="m-0 p-0">{{ $clientProg->client->full_name }}</h6>
                                <p class="m-0 p-0">{{ $clientProg->program_name }}</p>
                            </div>
                            <div class="col-md-4">
                                <table>
                                    <tr>
                                        <td>Invoice No</td>
                                        <td>: {{ $clientProg->invoice->inv_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>Date</td>
                                        <td>: {{ date('D, d M Y', strtotime($clientProg->invoice->created_at)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Due Date</td>
                                        <td>: {{ date('D, d M Y', strtotime($clientProg->invoice->inv_duedate)) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <div class="card-body">
                                <h5 class="mb-3 text-muted">
                                    Please, upload signed invoice here
                                </h5>
                                <form enctype="multipart/form-data"
                                    action="{{ route('invoice.program.upload_signed_document', ['client_program' => $clientProg->clientprog_id]) }}"
                                    method="POST" id="form-upload-attachment">
                                    @csrf
                                    <input type="hidden" name="invoice_id" value="{{ $clientProg->invoice->inv_id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <input type="file" name="signed_attachment" class="form-control">
                                            @error('signed_attachment')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        &nbsp;
                                        <button type="submit" class="btn btn-info">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div id="pspdfkit" style="height: 100vh"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<script src="{{ asset('assets/package/dist/pspdfkit.js') }}"></script>
    <script>
        PSPDFKit.load({
            container: "#pspdfkit",
              document: "{{ asset('storage/uploaded_file/invoice/0009_INV-JEI_ACTP_I_23.pdf') }}" // Add the path to your document here.
        })
        .then(async (instance) => {
            console.log("PSPDFKit loaded", instance);
            (async () => {
                const pagesAnnotations = await Promise.all(
                    Array.from({ length: instance.totalPageCount }).map((_, pageIndex) =>
                        instance.getAnnotations(pageIndex)
                    )
                );
                const annotationIds = pagesAnnotations.flatMap(pageAnnotations =>
                    pageAnnotations.map(annotation => annotation.id).toArray()
                );
                console.log(annotationIds)
                await instance.delete(annotationIds)
            })();
        })
        .catch(function(error) {
            console.error(error.message);
        });
    </script>
    <script type="text/javascript">
        $("form#form-upload-attachment").submit(function(e) {
            e.preventDefault()
            Swal.showLoading();

            var link = $(this).attr('action');
            var formData = new FormData(this);

            $.ajax({
                url: link,
                type: "POST",
                data: formData,
                success: function(result) {
                    console.log(result)
                    Swal.close()
                    // handle success
                    Swal.fire(
                        'Success!',
                        'The document has been uploaded!',
                        'success'
                    )
                    window.close()
                },
                error: function(error) {
                    console.log(error)
                    Swal.close()

                    Swal.fire(
                        'Something went wrong!',
                        'Cannot upload the document. Please try again',
                        'error'
                    )

                },
                cache: false,
                contentType: false,
                processData: false
            })
        })
    </script>

@endsection




{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Signed Attachment</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//fastly.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <form enctype="multipart/form-data" action="{{ route('invoice.program.upload_signed_document', ['client_program' => $clientProg->clientprog_id]) }}" method="POST" id="form-upload-attachment">
        @csrf
        <input type="hidden" name="invoice_id" value="{{ $clientProg->invoice->inv_id }}">
        <input type="file" name="signed_attachment">
        @error('signed_attachment')
            <small class="text-danger fw-light">{{ $message }}</small>
        @enderror
        <button type="submit">Upload</button>
    </form>

</body>
</html> --}}
