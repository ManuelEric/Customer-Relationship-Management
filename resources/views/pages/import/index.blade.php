@extends('layout.main')

@section('title', 'Import Data')
{{-- @section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Receipt</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection --}}
@section('content')

<div class="card bg-secondary mb-3 p-2">
    <div class="row row-cols-md-2 row-cols-1 align-items-center justify-content-md-between justify-content-start g-3">
        <div class="col">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Import Data
            </h5>
        </div>
        <div class="col text-md-end text-start">
            <a href="#" role="button" id="sync-btn" class="btn btn-sm btn-info"><i
                class="bi bi-arrow-clockwise me-1"></i>
            Sync Data</a>
            <a href="https://docs.google.com/spreadsheets/d/1xam159C7dirHCH9txq1g9xp98mDbktCBvg_clc4hgxI/edit?usp=sharing" target="_blank" class="btn btn-sm btn-info"><i
                class="bi bi-box-arrow-up-right me-1"></i>
            Spreadsheet</a>
        </div>
    </div>
</div>

<div class="row align-items-center row-cols-md-5 row-cols-2">
    <div class="mb-3">
        <div class="card card-body" id="parent">
            <div class="text-center">
                <div class="position-relative overflow-hidden mb-2" style="height: 150px;">
                    <img loading="lazy"  src="{{ asset('img/form-embed/parent.webp') }}" class="card-img-bottom w-100 h-100" style="object-fit:contain;">
                </div>
                <h5>Parents</h5>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <div class="card card-body" id="student">
            <div class="text-center">
                <div class="position-relative overflow-hidden mb-2" style="height: 150px;">
                    <img loading="lazy"  src="{{ asset('img/form-embed/student.webp') }}" class="card-img-bottom w-100 h-100" style="object-fit:contain;">
                </div>
                <h5>Students</h5>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <div class="card card-body" id="teacher">
            <div class="text-center">
                <div class="position-relative overflow-hidden mb-2" style="height: 150px;">
                    <img loading="lazy"  src="{{ asset('img/form-embed/teacher.webp') }}" class="card-img-bottom w-100 h-100" style="object-fit:contain;">
                </div>
                <h5>Teacher</h5>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <div class="card card-body"  id="client-event">
            <div class="text-center">
                <div class="position-relative overflow-hidden mb-2" style="height: 150px;">
                    <img loading="lazy"  src="{{ asset('img/profile.webp') }}" class="card-img-bottom w-100 h-100" style="object-fit:contain;">
                </div>
                <h5>Client Events</h5>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <div class="card card-body" id="client-program">
            <div class="text-center">
                <div class="position-relative overflow-hidden mb-2" style="height: 150px;">
                    <img loading="lazy"  src="{{ asset('img/program.webp') }}" class="card-img-bottom w-100 h-100" style="object-fit:contain;">
                </div>
                <h5>Client Programs</h5>
            </div>
        </div>
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
                 {{-- <button type="button" id="import" class="btn btn-primary btn-sm">
                     <i class="bi bi-save2 me-1"></i>
                     Import</button> --}}
                 <button type="button" id="import2" class="btn btn-primary btn-sm">
                     <i class="bi bi-save2 me-1"></i>
                     Import</button>
             </div>
             
         </div>
     </div>
 </div>
</div>

{{-- Modal sync data --}}
<div class="modal fade" id="syncModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Sync Data
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                    <div class="row g-2">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <select name="type" id="type-sync" class="modal-select-sync w-100">
                                    <option data-placeholder="true"></option>
                                    <option value="edufair">Edufair</option>
                                    <option value="event">Event</option>
                                    <option value="kol">KOL</option>
                                    <option value="lead">Lead</option>
                                    <option value="major">Major</option>
                                    <option value="partner">Partner</option>
                                    <option value="program">Program</option>
                                    <option value="school">School</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</a>
                        <button type="button" id="submit-sync" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Sync</button>
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

    $(document).ready(function() {

        $('#type-sync').select2({
            dropdownParent: $('#syncModal .modal-body'),
            placeholder: "Select value",
            allowClear: true
        });

    });

    $('#sync-btn').click(function(e){
        $("#syncModal").modal('show');
        // $("#category").val('parent')
    });

    $('#parent').click(function(e){
        $("#inputRange").modal('show');
        $("#category").val('parent')
    });

    $('#client-event').click(function(e){
        $("#inputRange").modal('show');
        $("#category").val('client-event')
    });

    $('#client-program').click(function(e){
        $("#inputRange").modal('show');
        $("#category").val('client-program')
    });

    $('#student').click(function(e){
        $("#inputRange").modal('show');
        $("#category").val('student')
    });

    $('#teacher').click(function(e){
        $("#inputRange").modal('show');
        $("#category").val('teacher')
    });

    $('#import').click(function(e){
        $("#inputRange").modal('hide');
        $('#content-import-information').html('');
        var category = $("#category").val();
        var start = $("#start").val();
        var end = $("#end").val();

        showLoading()
        axios
            .get("{{ url('api/import') }}/" + category + "?start=" + start + "&end=" + end, {
                headers:{
                    'Authorization': 'Bearer ' + '{{ Session::get("access_token") }}'
                }
            })
            .then(function(response){
                html = '';
                html += `<h5>Import ${category}</h5>`;
                html += `<ul>`;
                    
                
                if(response.data.success == false){
                    var error = response.data.error
                    if(Object.keys(error).length){
                        Object.keys(error).forEach(key => {
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

    $('#import2').click(function(e){
        $("#inputRange").modal('hide');
        $('#content-import-information').html('');
        var category = $("#category").val();
        var start = $("#start").val();
        var end = $("#end").val();

        showLoading()
        axios
            .get("{{ url('api/import') }}/" + category + "?start=" + start + "&end=" + end, {
                headers:{
                    'Authorization': 'Bearer ' + '{{ Session::get("access_token") }}'
                }
            })
            .then(function(response){
                html = '';
                // html += `<h5>Import ${category}</h5>`;
                $('#title-modal-import').html(`Import ${category}`);
                
                if(response.data.success == false){
                    html += `<ul>`;
                    var error = response.data.error
                    if(Object.keys(error).length){
                        Object.keys(error).forEach(key => {
                            html += `<li class="text-danger">${key + ': ' + error[key]}</li>`
                        });
                    }

                    $("#modal-notif-import").modal('show');
                    $('#content-import-information').html(html);

                }else{
                    var data = response.data;
                    
                    if( data['batch_id'] === undefined ){
                        html += `<ul>`;
                        html += `<li>Total Imported: ${parseInt(data.total_imported)}</li>`
                        if(data.message !== null){
                            html += `<li>Message: ${data.message}</li>`
                        }
                        html += `</ul>`

                        $("#modal-notif-import").modal('show');
                        $('#content-import-information').html(html);

                    }else{
                        localStorage.setItem("batch_id", data.batch_id);
                        html += `<div id="loading-bar">`;
                        html += `<div class="progress" role="progressbar" aria-label="Animated striped example" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="height:25px;">`;
                        html += `<div class="progress-bar progress-bar-striped progress-bar-animated" id="bar" style="width: 0%">0%</div>`;
                        html += `</div>`;
                        html += `<p class="text-center mt-2" id="total">Importing ...</p>`;
                        html += `</div>`;

                            
                        $("#modal-notif-import").modal('show');
                        $('#content-import-information').html(html);

                        var i = 0;

                        let myInterval = setInterval(() => {
                            axios
                            .get("{{ url('api/batch') }}/" + localStorage.getItem("batch_id"), {
                                headers:{
                                    'Authorization': 'Bearer ' + '{{ Session::get("access_token") }}'
                                }
                            }).then(function(response){
                                console.log(response);
                                $('#bar').css({'width': response.data.progress + '%'});
                                $('#bar').text(response.data.progress + '%');
                                $('#total').html(`Importing ${response.data.total_imported}/${response.data.total_data}`);
                                
                                i++;

                                if(response.data.progress == 100){
                                    console.log('100 fully');
                                    
                                    clearInterval(myInterval);
                                }

                                if(i >= 100){
                                    $("#modal-notif-import").modal('hide');
                                    clearInterval(myInterval);
                                    var msg = 'Timeout!';
                                    notification('error', msg);
                                }
                            }).catch(function(error, response) {
                                clearInterval(myInterval);
                                $("#modal-notif-import").modal('hide');
                                var msg = 'Something went wrong. Please try again';
                                notification('error', msg);

                            });
                        }, 3000);
                       
                    }

                }

                

                swal.close()
            }).catch(function(error, response) {
                console.log(error);
                var msg = 'Something went wrong. Please try again';
                if(error.response.status == 429){
                    msg = 'Please wait 1 minute!'
                }
                swal.close()
                notification('error', msg);

            })
    })

    $('#submit-sync').click(function(e){
        $("#syncModal").modal('hide');
        var type = $("#type-sync").val();

        showLoading()
        axios
            .get("{{ url('api/sync') }}/" + type, {
                headers:{
                    'Authorization': 'Bearer ' + '{{ Session::get("access_token") }}'
                }
            })
            .then(function(response){
                
                swal.close()
                notification('success', 'Successfully syncronized data')
            }).catch(function(error, response) {
                var msg = 'Something went wrong. Please try again';
        
                swal.close()
                notification('error', msg);

            })
    })
</script>

@endsection

