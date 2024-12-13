<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                Agreement
            </h6>
        </div>
        <div class="">
            <button class="btn btn-sm btn-outline-primary rounded mx-1" id="btn-add-agreement">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <ol class="list-group list-group-numbered">
        @forelse ($user->user_subjects as $user_subject)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ $user_subject->subject->name }}</div>
                    <b>{{ $user_subject->year }} | {{ $user_subject->grade}}</b> 
                    <table>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>Fee Individual</td>
                            <td>:</td>
                            <td> {{ $user_subject->fee_individual ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Fee Group</td>
                            <td>:</td>
                            <td> {{ $user_subject->fee_group ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Additional Fee</td>
                            <td>:</td>
                            <td> {{ $user_subject->additional_fee ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Head</td>
                            <td>:</td>
                            <td> {{ $user_subject->head ?? '-' }}</td>
                        </tr>
                    </table>
                    @if($user_subject->agreement != null)
                        <div class="d-grid gap-2 d-md-flex mx-auto">
                            <h4>
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Download" class="download" onclick="downloadAgreement('{{$user_subject->id}}')"><i class="bi bi-download"></i></a>
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit" data-user-subject-id="{{ $user_subject->id }}" class="ms-2 edit text-warning" onclick="editAgreement('{{$user_subject->id}}')"><i class="bi bi-pencil"></i></a>
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete" data-user-subject-id="{{ $user_subject->id }}" class="ms-2 delete text-danger" onclick="confirmDelete('user/{{Request::route('user_role')}}/{{$user->id}}/agreement', {{ $user_subject->id }})"><i class="bi bi-trash"></i></a>
                            </h4>
                        </div>
                        <div class="text-center">
                        </div>
                    @endif
                </div>
                
            </li>
        @empty
            <li class="list-group-item d-flex justify-content-between align-items-start">
                Ga ada
            </li>
        @endforelse
        </ol>
    </div>
</div>

<div class="modal modal-md fade" id="agreementForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0 p-0">
                    <i class="bi bi-plus me-2"></i>
                    Agreement
                </h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('user.store.agreement', ['user' => $user->id, 'user_role' =>  Request::route('user_role')]) }}" 
                    id="detailForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="">Role <sup class="text-danger">*</sup></label>
                            <select name="role_agreement" id="role_agreement" class="agreement-select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($user->roles as $role)
                                    <option value="{{ $role->pivot->id }}" {{ old('role_agreement') == $role->id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            @error('role_agreement')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">Subject <sup class="text-danger">*</sup></label>
                            <select name="subject_id" id="subject_id" class="agreement-select w-100">
                            </select>
                            @error('subject_id')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Agreement File <sup class="text-danger">*</sup></label>
                            <div class="file-agreement">
                                <input type="file" name="agreement" value="{{ old('agreement') }}" class="form-control form-control-sm rounded">
                            </div>
                            @error('agreement')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="">Year <sup class="text-danger">*</sup></label>
                            <select name="year" id="year" class="agreement-select w-100">
                                <option data-placeholder="true"></option>
                                @for ($year = date('Y') - 5; $year <= date('Y') + 5; $year++)
                                    <option value="{{$year}}">{{ $year }}</option>
                                @endfor
                            </select>
                            @error('year')
                                <small class="text-danger fw-light">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="detail-subject" id="detail-subject-0">
                            <div class="row border py-2 px-3 mb-1" id="subjectDetailField-0">
                                <input type="hidden" value="1" name="count_agreement_detail[]">
                                @if($is_tutor)
                                    <div class="col-md-6 mb-2">
                                        <label for="">Grade <sup class="text-danger">*</sup></label>
                                        <select name="grade[]" class="agreement-select w-100">
                                            <option data-placeholder="true"></option>
                                            <option value="9-10">9-10</option>
                                            <option value="11-12">11-12</option>
                                        </select>
                                        @error('grade.0')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif
                                <div class="col-md-6 mb-2">
                                    <label for="">Fee Individual <sup class="text-danger">*</sup></label>
                                    <input class="form-control form-control-sm rounded" type="text" name="fee_individual[]">
                                    @error('fee_individual.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">Fee Group </label>
                                    <input class="form-control form-control-sm rounded" type="text" name="fee_group[]">
                                    @error('fee_group.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">Additional Fee </label>
                                    <input class="form-control form-control-sm rounded" type="text" name="additional_fee[]">
                                    @error('additional_fee.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2 d-flex justify-content-between align-items-start">
                                    <div style="width: 100%">
                                        <label for="" class="text-muted">Head</label>
                                        <input class="form-control form-control-sm rounded" type="text" name="head[]">
                                        @error('head.0')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <button type="button" id="btn-add-detail-agreement" class="btn btn-sm btn-outline-primary py-1" onclick="addDetailAgreement()" style="margin-top:18px; margin-left:10px;"><i class="bi bi-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-12 mt-2">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                    data-bs-dismiss="modal">
                                    <i class="bi bi-x me-1"></i>
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-sm btn-primary rounded-3">
                                    <i class="bi bi-save2"></i>
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.agreement-select').select2({
            dropdownParent: $('#agreementForm .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });

        $('#role_agreement').on('change', function(){
            var selected_role = $(this).select2().find(":selected").html();
            var edit = $(this).data('edit');
            var selected_subject = $(this).data('edit') === true ? $(this).data('subject') : null;
            
            var baseUrl = "{{ url('/') }}/api/v1/get/subjects/" + selected_role;

            showLoading();
            axios.get(baseUrl)
            .then(function(response) {
                // handle success
                let subjects = response.data.data
                
                let list_option_subjects = '<option data-placeholder="true"></option>';
                
                subjects.forEach((subject) => {
                    list_option_subjects += '<option value="'+subject.id+'" '+ (selected_subject !== null && subject.id == selected_subject ? "selected" : "") +'>' +subject.name+ '</option>';
                });

                $('#subject_id').html(list_option_subjects);
                
                Swal.close()
            })
            .catch(function(error) {
                // handle error
                Swal.close()
                notification(error.response.data.success, 'Something went wrong. Please try again or contact the administrator.')
            })
        });
    });

    function addDetailAgreement() {
        let index = 0;
        let id = Math.floor((Math.random() * 100) + 1);
        $("#detail-subject-"+index).append(
            '<div class="row border py-2 px-3 mb-1" id="subjectDetailField-'+id+'">' +
                '<input type="hidden" value="1" name="count_agreement_detail[]">' +
                @if($is_tutor)
                    '<div class="col-md-6 mb-2">' +
                        '<label for="">Grade <sup class="text-danger">*</sup></label>' +
                        '<select name="grade[]" class="agreement-select w-100">' +
                            '<option data-placeholder="true"></option>' +
                            '<option value="9-10">9-10</option>' +
                            '<option value="11-12">11-12</option>' +
                        '</select>' +
                        @error('grade.0')
                            '<small class="text-danger fw-light">{{ $message }}</small>'
                        @enderror
                    '</div>' +
                @endif
                '<div class="col-md-6 mb-2">' +
                    '<label for="">Fee Individual <sup class="text-danger">*</sup></label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_individual[]">' +
                    @error('fee_individual.0')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-6 mb-2">' +
                    '<label for="">Fee Group </label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_group[]">' +
                    @error('fee_group.0')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-6 mb-2">' +
                    '<label for="">Additional Fee </label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="additional_fee[]">' +
                    @error('additional_fee.0')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-6 mb-2 d-flex justify-content-between align-items-start">' +
                    '<div style="width: 100%">' +
                        '<label for="" class="text-muted">Head</label>' +
                        '<input class="form-control form-control-sm rounded" type="text" name="head[]">' +
                        @error('head.0')
                            '<small class="text-danger fw-light">{{ $message }}</small>' +
                        @enderror
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteDetailSubject('+id+')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-trash2"></i></button>' +
                '</div>' +

            '</div>'
        )
        initSelect2('.detail-subject ')
    }

    function deleteDetailSubject(id) {
        $('#subjectDetailField-' + id).remove();
    }
    
    function editAgreement(id) {

        var baseUrl = "{{ url('/user/'.Request::route("user_role").'/'.$user->id.'/agreement') }}/" + id;        
        
        $('#agreementForm').modal('show');
        showLoading();
        axios.get(baseUrl)
        .then(function(response) {
            // handle success
            let user_subject = response.data.data
            var html_agreement = '';
                        
            $('#role_agreement').attr('data-edit', 'true');
            $('#role_agreement').attr('data-subject', user_subject.subject_id);
            $('#role_agreement').val(user_subject.user_role_id).trigger('change');
            $('#year').val(user_subject.year).trigger('change');
            $('select[name="grade[]"]').val(user_subject.grade).trigger('change');
            $('input[name="fee_individual[]"]').val(user_subject.fee_individual);
            $('input[name="fee_group[]"]').val(user_subject.fee_group);
            $('input[name="additional_fee[]"]').val(user_subject.additional_fee);
            $('input[name="head[]"]').val(user_subject.head);

            if(user_subject.agreement !== null){
                
                html_agreement += '<button id="btn-download-'+user_subject.id+'" type="button" class="btn btn-sm btn-info me-2 download" onclick="downloadAgreement('+user_subject.id+')">' +
                    '<i class="bi bi-download me-2"></i>' +
                        'Download' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-danger remove" id="btn-remove-'+ user_subject.id+'" onclick="removeAgreement('+user_subject.id+')">' +
                    '<i class="bi bi-trash"></i>'+
                    '</button>' +

                    '<div id="agreement-upload-'+user_subject.id+'" class="upload-file d-flex justify-content-center align-items-center d-none">' +
                    '<input type="hidden" name="agreement_text" value='+user_subject.agreement+'>' +
                    '<input type="file" name="agreement" class="form-control form-control-sm rounded">' +
                    '<i id="btn-roolback-'+user_subject.id+'" class="bi bi-backspace ms-2 cursor-pointer text-danger rollback" onclick="roolbackAgreement(' + user_subject.id + ')"></i>' +
                    '</div>' ;
                    
                $('.file-agreement').html('');
                $('.file-agreement').html(html_agreement);
            }
            
            // let html_method = '@method('put')';
            // $('.put').html(html_method);
            // $('#detailForm').attr('action', '{{ url('master/sales-target') }}/' + data.id + '')

            $('#btn-add-detail-agreement').addClass('d-none');
            
            Swal.close()
        })
        .catch(function(error) {
            // handle error
            Swal.close()
            notification(error.response.data.success, 'Something went wrong. Please try again or contact the administrator.')
        })
        
    }

    $('#btn-add-agreement').on('click', function(){
      
        $('#detailForm input[type="text"]').val('');
        $('#detailForm select').val('');
        
        $('#agreementForm').modal('show');
        $('#btn-add-detail-agreement').removeClass('d-none');

    });

    @if (isset($user))
        function downloadAgreement(id){
            var url = '{{ url("user/" . Request::route('user_role') . "/" . $user->id . "/download_agreement") }}/' + id;
            window.open(url, '_blank');
        }

        function removeAgreement(id){
            $('#agreement-upload-'+id).removeClass('d-none');
            $('#btn-remove-'+id).addClass('d-none');
            $('#btn-download-'+id).addClass('d-none');
            $('#btn-roolback-'+id).removeClass('d-none');
        }

        function roolbackAgreement(id){
            $('#btn-roolback-'+id).addClass('d-none');
            $('#btn-remove-'+id).removeClass('d-none');
            $('#btn-download-'+id).removeClass('d-none');
            $('#agreement-upload-'+id).addClass('d-none');

        }
    @endif
</script>


@if(
    $errors->has('agreement_name') | 
    $errors->has('agreement_type') | 
    $errors->has('start_date') | 
    $errors->has('end_date') | 
    $errors->has('corp_pic') | 
    $errors->has('empl_id') | 
    $errors->has('attachment')  
    )
            
    <script>
        $(document).ready(function(){
            $('#agreementForm').modal('show'); 
        })

    </script>

@endif