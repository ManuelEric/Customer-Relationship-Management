@extends('layout.main')

@section('title', 'Import Data')
{{-- @section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Receipt</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection --}}
@section('content')

<div class="row align-items-center">
    <div class="col">
        <a class="btn btn-secondary" href="#" role="button" id="parent">Parents</a>
    </div>
    <div class="col">
        <a class="btn btn-secondary" href="#" role="button">Link</a>
    </div>
    <div class="col">
        <a class="btn btn-secondary" href="#" role="button">Link</a>
    </div>
  </div>


 {{-- Modal input range --}}
 <div class="modal fade" id="inputRange" data-bs-backdrop="static" data-bs-keyboard="false"
 aria-labelledby="staticBackdropLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content">
         <div class="modal-header">
             <span>
                 Input Range Data
             </span>
             <i class="bi bi-pencil-square"></i>
         </div>
         <div class="modal-body w-100 text-start">
             <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <label>Start <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="start" name="start" value="" placeholder="2" required>
                    </div>
                    <div class="col-md-6">
                        <label>End <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="end" name="end" value="" placeholder="10" required>
                    </div>
                    <input type="hidden" id="category" name="category">
                </div>
             </div>

             <div class="d-flex justify-content-between">
                 <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                     data-bs-dismiss="modal">
                     <i class="bi bi-x-square me-1"></i>
                     Cancel</button>
                 <button type="button" id="import" class="btn btn-primary btn-sm">
                     <i class="bi bi-save2 me-1"></i>
                     Import</button>
             </div>
             
         </div>
     </div>
 </div>
</div>

 {{-- Modal notif import --}}
 <div class="modal modal-md fade" id="modal-notif-import" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0 p-0" id="title-modal-import">
                    Import Information
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-import-information">
            </div>
        </div>
    </div>
</div>

<script>
    $('#parent').click(function(e){
        $("#inputRange").modal('show');
        $("#category").val('parent')
    });

    $('#import').click(function(e){
        $('#content-import-information').html('');
        var category = $("#category").val();
        var start = $("#start").val();
        var end = $("#end").val();

        showLoading()
        axios
            .get("{{ url('api/import') }}/" + category + "?start=" + start + "&end=" + end)
            .then(function(response){
                console.log(response);
                html = '';
                html += `<h5>Import ${category}</h5>`;
                html += `<ul>`;
                    
                
                if(response.data.success == false){
                    var error = response.data.error
                    if(Object.keys(error).length){
                        Object.keys(error).forEach(key => {
                            console.log(key, error[key]);
                            html += `<li class="text-danger">${key + ': ' + error[key]}</li>`
                        });
                    }
                }else{
                    var data = response.data.data;
                    html += `<li>Total Imported: ${parseInt(data.total_imported)}</li>`
                    if(data.message !== null){
                        html += `<li>Message: ${data.message}</li>`
                    }
                    html += `</ul>`

                }

                $("#modal-notif-import").modal('show');
                $('#content-import-information').html(html);
                

                swal.close()
            }).catch(function(error, response) {
                var msg = 'Something went wrong. Please try again';
                if(error.response.status == 429){
                    msg = 'Please wait 1 minute!'
                }
                swal.close()
                notification('error', msg);

            })
    })
</script>

@endsection

