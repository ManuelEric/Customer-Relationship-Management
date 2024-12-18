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
        @forelse ($user->user_subjects->groupBy(['subject_id', 'year']) as $key => $user_subject_by_subject_id)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ $user_subject_by_subject_id->first()->first()->subject->name }}</div>
                    @foreach ($user_subject_by_subject_id as $user_subject_by_year)
                        <hr>
                        @foreach ($user->user_subjects()->where('subject_id', $user_subject_by_year->first()->subject_id)->where('year', $user_subject_by_year->first()->year)->get() as $user_subject)
                            {{-- {{dd($user_subject)}} --}}
                                <b>{{ $user_subject->year }} {{ $user_subject->grade != null ? '| ' . $user_subject->grade : '' }}</b>  
                                @if($user_subject->agreement != null && $loop->index == 0)
                                    {{-- {{dd($user_subject->subject_id)}} --}}
                                    <div class="d-grid gap-2 d-md-flex mx-auto">
                                        <h6>
                                            <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Download" class="download" onclick="downloadAgreement('{{$user_subject->id}}')"><i class="bi bi-download"></i></a>
                                            <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit" data-user-subject-id="{{ $user_subject->id }}" class="ms-2 edit text-warning" onclick="editAgreement('{{$user_subject->subject_id}}', '{{$user_subject->year}}')"><i class="bi bi-pencil"></i></a>
                                            <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete" data-user-subject-id="{{ $user_subject->id }}" class="ms-2 delete text-danger" onclick="confirmDelete('user/{{Request::route('user_role')}}/{{$user->id}}/agreement/{{ $user_subject->subject_id }}/year', {{ $user_subject->year }})"><i class="bi bi-trash"></i></a>
                                        </h6>
                                    </div>
                                    <div class="text-center">
                                    </div>
                                @endif
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
                               
                        @endforeach
                    @endforeach
                </div>
                
            </li>
        @empty
            <p>
                There is no user agreement data yet
            </p>
        @endforelse
        </ol>
    </div>
</div>

<div class="modal modal-md fade" id="agreementForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
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
                            <input type="hidden" name="role" id="role-id-user-agreement">
                            <label for="">Role <sup class="text-danger">*</sup></label>
                            <select name="role_agreement" id="role_agreement" class="agreement-select w-100">
                                <option data-placeholder="true"></option>
                                @foreach ($user->roles as $role)
                                    <option value="{{ $role->pivot->id }}" {{ old('role_agreement') == $role->pivot->id ? 'selected' : '' }} data-role-id="{{ $role->id }}">{{ $role->role_name }}</option>
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
                            <div class="row border py-2 px-3 mb-1 subject-detail-field" id="subjectDetailField-0">
                                <input type="hidden" value="1" name="count_agreement_detail[]">
                                {{-- @if($is_tutor) --}}
                                    <div class="input-grade col-md-6 mb-2 d-none">
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
                                {{-- @endif --}}
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
            var selected_role_id = $(this).select2().find(":selected").data('role-id');
            var edit = $(this).data('edit');
            var selected_subject = $(this).data('edit') === true ? $(this).data('subject') : null;
            
            $('#role-id-user-agreement').val(selected_role_id);
            
            if(selected_role == 'Tutor'){
                $('.input-grade').removeClass('d-none');
            }else{
                $('.input-grade').addClass('d-none');
            }
            
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

    function addDetailAgreement(index = 0) {
        let id = Math.floor((Math.random() * 100) + 1);
        let selected_role = $('#role_agreement').select2().find(":selected").html();
        let count_subject_detail_field = $('.subject-detail-field').length        

        $("#detail-subject-"+index).append(
            '<div class="row border py-2 px-3 mb-1 subject-detail-field" id="subjectDetailField-'+id+'">' +
                '<input type="hidden" value="1" name="count_agreement_detail[]">' +
                '<div class="input-grade col-md-6 mb-2 '+(selected_role != 'Tutor' ? 'd-none' : '')+'">' +
                    '<label for="">Grade <sup class="text-danger">*</sup></label>' +
                    '<select name="grade[]" class="agreement-select w-100">' +
                        '<option data-placeholder="true"></option>' +
                        '<option value="9-10">9-10</option>' +
                        '<option value="11-12">11-12</option>' +
                    '</select>' +
                    @error('grade.1')
                        '<small class="text-danger fw-light">{{ $message }}</small>'
                    @enderror
                '</div>' +
                '<div class="col-md-6 mb-2">' +
                    '<label for="">Fee Individual <sup class="text-danger">*</sup></label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_individual[]">' +
                    @error('fee_individual.1')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-6 mb-2">' +
                    '<label for="">Fee Group </label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="fee_group[]">' +
                    @error('fee_group.1')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-6 mb-2">' +
                    '<label for="">Additional Fee </label>' +
                    '<input class="form-control form-control-sm rounded" type="text" name="additional_fee[]">' +
                    @error('additional_fee.1')
                        '<small class="text-danger fw-light">{{ $message }}</small>' +
                    @enderror
                '</div>' +
                '<div class="col-md-6 mb-2 d-flex justify-content-between align-items-start">' +
                    '<div style="width: 100%">' +
                        '<label for="" class="text-muted">Head</label>' +
                        '<input class="form-control form-control-sm rounded" type="text" name="head[]">' +
                        @error('head.1')
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
    
    function editAgreement(subject_id, year) {

        var baseUrl = "{{ url('/user/'.Request::route("user_role").'/'.$user->id.'/agreement') }}/" + subject_id + "/year/" + year;        
        
        $('#agreementForm').modal('show');
        showLoading();
        axios.get(baseUrl)
        .then(function(response) {
            // handle success
            let user_subjects = response.data.data
            
            var html_agreement = '';
            var keys = Object.keys(user_subjects) 

            console.log(keys);
            
            console.log(user_subjects[keys[0]].user_roles.role.id);
            
            
            $('#role_agreement').attr('data-edit', 'true');
            $('#role_agreement').attr('data-subject', user_subjects[keys[0]].subject_id);
            $('#role_agreement').val(user_subjects[keys[0]].user_role_id).trigger('change');
            $('#role_agreement').attr('data-role-id', user_subjects[keys[0]].user_roles.role.id);
            $('#role-id-user-agreement').val(user_subjects[keys[0]].user_roles.role.id);

            $('#year').val(user_subjects[keys[0]].year).trigger('change');

            if(user_subjects[keys[0]].agreement !== null){
                
                html_agreement += '<button id="btn-download-'+user_subjects[keys[0]].id+'" type="button" class="btn btn-sm btn-info me-2 download" onclick="downloadAgreement('+user_subjects[keys[0]].id+')">' +
                    '<i class="bi bi-download me-2"></i>' +
                        'Download' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-danger remove" id="btn-remove-'+ user_subjects[keys[0]].id+'" onclick="removeAgreement('+user_subjects[keys[0]].id+')">' +
                    '<i class="bi bi-trash"></i>'+
                    '</button>' +

                    '<div id="agreement-upload-'+user_subjects[keys[0]].id+'" class="upload-file d-flex justify-content-center align-items-center d-none">' +
                    '<input type="hidden" name="agreement_text" value='+user_subjects[keys[0]].agreement+'>' +
                    '<input type="file" name="agreement" class="form-control form-control-sm rounded">' +
                    '<i id="btn-roolback-'+user_subjects[keys[0]].id+'" class="bi bi-backspace ms-2 cursor-pointer text-danger rollback" onclick="roolbackAgreement(' + user_subjects[keys[0]].id + ')"></i>' +
                    '</div>' ;
                    
                $('.file-agreement').html('');
                $('.file-agreement').html(html_agreement);
            }
            
            var new_html_detail_agreement = '';
            var i = 0;
            Object.keys(user_subjects).forEach(function (key){
                
                @php
                    $index = 0;
                @endphp
                
                var id_field_detail_subject = Math.floor((Math.random() * 100) + 1);
                new_html_detail_agreement += '<div class="row border py-2 px-3 mb-1 subject-detail-field" id="subjectDetailField-'+id_field_detail_subject+'">' +
                    '<input type="hidden" value="1" name="count_agreement_detail[]">' +
                    @if($is_tutor)
                        '<div class="col-md-6 mb-2">' +
                            '<label for="">Grade <sup class="text-danger">*</sup></label>' +
                            '<select name="grade[]" class="agreement-select w-100">' +
                                '<option data-placeholder="true"></option>' +
                                '<option value="9-10" '+(user_subjects[key].grade == "9-10" ? "selected" : "")+'>9-10</option>' +
                                '<option value="11-12" '+(user_subjects[key].grade == "11-12" ? "selected" : "")+'>11-12</option>' +
                            '</select>' +
                            @error('grade.'. $index)
                                '<small class="text-danger fw-light">{{ $message }}</small>'
                            @enderror
                        '</div>' +
                    @endif
                    '<div class="col-md-6 mb-2">' +
                        '<label for="">Fee Individual <sup class="text-danger">*</sup></label>' +
                        '<input class="form-control form-control-sm rounded" type="text" name="fee_individual[]" value="'+(user_subjects[key].fee_individual ?? '')+'">' +
                        @error('fee_individual.'. $index)
                            '<small class="text-danger fw-light">{{ $message }}</small>' +
                        @enderror
                    '</div>' +
                    '<div class="col-md-6 mb-2">' +
                        '<label for="">Fee Group </label>' +
                        '<input class="form-control form-control-sm rounded" type="text" name="fee_group[]" value="'+(user_subjects[key].fee_group ?? '')+'">' +
                        @error('fee_group.'. $index)
                            '<small class="text-danger fw-light">{{ $message }}</small>' +
                        @enderror
                    '</div>' +
                    '<div class="col-md-6 mb-2">' +
                        '<label for="">Additional Fee </label>' +
                        '<input class="form-control form-control-sm rounded" type="text" name="additional_fee[]" value="'+(user_subjects[key].additional_fee ?? '')+'">' +
                        @error('additional_fee.'. $index)
                            '<small class="text-danger fw-light">{{ $message }}</small>' +
                        @enderror
                    '</div>' +
                    '<div class="col-md-6 mb-2 d-flex justify-content-between align-items-start">' +
                        '<div style="width: 100%">' +
                            '<label for="" class="text-muted">Head</label>' +
                            '<input class="form-control form-control-sm rounded" type="text" name="head[]" value="'+(user_subjects[key].head ?? '')+'">' +
                            @error('head.'. $index)
                                '<small class="text-danger fw-light">{{ $message }}</small>' +
                            @enderror
                        '</div>' +
                        (i > 0 ? '<button type="button" class="btn btn-sm btn-danger py-1" onclick="deleteDetailSubject('+id_field_detail_subject+')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-trash2"></i></button>' : '<button type="button" id="btn-add-detail-agreement" class="btn btn-sm btn-outline-primary py-1" onclick="addDetailAgreement('+i+')" style="margin-top:18px; margin-left:10px;"><i class="bi bi-plus"></i></button>')  +
                    '</div>' +

                '</div>'
                @php
                    $index++;
                @endphp
                i++;
            });

            $("#detail-subject-"+0).html('');
            $("#detail-subject-"+0).html(new_html_detail_agreement);

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
    $errors->has('role_agreement') || 
    $errors->has('subject_id') || 
    $errors->has('year') || 
    $errors->has('grade.*') || 
    $errors->has('fee_individual.*') || 
    $errors->has('fee_group.*') || 
    $errors->has('additional_fee.*') || 
    $errors->has('head.*') 
    )
            
    <script>
        $(document).ready(function(){
            $('#agreementForm').modal('show'); 
        })

    </script>

@endif