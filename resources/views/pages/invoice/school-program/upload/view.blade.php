<!DOCTYPE html>
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
    <form enctype="multipart/form-data" action="{{ route('invoice-sch.upload_signed_document', ['invoice' => $invoiceSch->invb2b_num]) }}" method="POST" id="form-upload-attachment">
        @csrf
        <input type="hidden" name="invoice_id" value="{{ $invoiceSch->invb2b_id }}">
        <input type="file" name="signed_attachment">
        @error('signed_attachment')
            <small class="text-danger fw-light">{{ $message }}</small>
        @enderror
        <button type="submit">Upload</button>
    </form>
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
</body>
</html>