@extends('layout.main')

@section('title', 'Alumni Acceptance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('acceptance.index') }}">Alumni Acceptance</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')
    <div class="row mt-3">
        <div class="col-md-4 text-center">
            <div class="card">
                <div class="card-body">
                    <img loading="lazy"  src="{{ asset('img/asset.webp') }}" alt="" class="w-75">
                    @if (isset($isUpdate) && $isUpdate === true)
                    {{-- View of Edit  --}}
                    <div class="text-center" id="client-information-section">
                        <h4>{{ $client->full_name }}</h4>
                        <h6>{{ $client->school->sch_name }} | {{ $client->graduation_year }}</h6>
                    </div>
                    {{-- End of view  --}}
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    {{-- View of Create  --}}
                    <div class="mb-4">
                        <form action="{{ $url }}" method="POST">
                            @csrf 
                            @if (isset($isUpdate) && $isUpdate === true)
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label>Alumni Name <sup class="text-danger">*</sup></label>
                                    <select name="alumni" class="select w-100">
                                        <option data-placeholder="true"></option>=
                                        @forelse ($alumnis as $alumni)
                                            <option value="{{ $alumni->id }}">{{ $alumni->full_name }}</option>
                                        @empty
                                            
                                        @endforelse
                                    </select>
                                    @error('alumni_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <button type="button" class="btn btn-info btn-sm" id="add_univ">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <div id="univ_list">
                                        <div class="row align-items-end g-2 mb-3">
                                            <div class="col-md-4">
                                                <label>University Name</label>
                                                <select name="uni_id[]" id="" class="select w-100">
                                                    <option data-placeholder="true"></option>
                                                    @forelse ($universities as $university)
                                                        <option value="{{ $university->univ_id }}">{{ $university->univ_name }}</option>
                                                    @empty
                                                        
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Major</label>
                                                <select  name="major[]" id="" class="select w-100">
                                                    <option data-placeholder="true"></option>
                                                    @forelse ($majors as $major)
                                                        <option value="{{ $major->id }}">{{ $major->name }}</option>
                                                    @empty
                                                        
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Status</label>
                                                <select name="status[]" id="" class="select w-100">
                                                    <option value="waitlisted">Waitlisted</option>
                                                    <option value="accepted">Accepted</option>
                                                    <option value="denied">Denied</option>
                                                    <option value="chosen">Chosen</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-12 text-center">
                                <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-safe"></i>
                                    Save changes
                                </button>
                            </div>
                        </form>
                    </div>
                    {{-- End of view  --}}

                    @if (isset($acceptances))
                    {{-- View of Edit  --}}
                    <hr>
                    <h4 class="pt-3">University Acceptance Tracker</h4>
                    <div class="border rounded mt-4 ps-2 pe-2"> 
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>University Name</th>
                                    <th>Major</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($acceptances as $acceptance)
                                    <tr>
                                        <td>{{ $acceptance->univ_name }}</td>
                                        <td>{{ $acceptance->pivot->major->name }}</td>
                                        <td>{{ $acceptance->pivot->status }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-danger"
                                                onclick="confirmDelete('client/acceptance', {{ $acceptance->pivot->id }})">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- End of view  --}}

                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        @if (isset($acceptance))
            $("select[name=alumni]").select2().val("{{ $client->id }}").trigger('change');
            $("select[name=alumni]").select2({disabled: 'readonly'});
        @endif
    })

    // $("select[name=alumni]").on('change', function() {
    //     var alumniId = $(this).val();
    //     var url = "{{ url('/api/get/client') }}/" + alumniId;
    //     var clientInformation = $("#client-information-section");
    //     showLoading();

    //     axios.get(url)
    //         .then(function (response) {
    //             let obj = response.data.data;

    //             clientInformation.find('h4').html(obj.full_name);

    //             var school_name = obj.school_name;
    //             if (obj.school_name == null)
    //                 school_name = '<a href="{{ url('client/student/') }}/'+ alumniId +'/edit" title="click here to store a school">null</a>';

    //             var graduation_year = obj.graduation_year;
    //             if (obj.graduation_year == null)
    //                 graduation_year = '<a href="{{ url('client/student/') }}/'+ alumniId +'/edit" title="click here to store graduation year">null</a>';

    //             clientInformation.find('h6').html("Graduated from " + school_name + " in " + graduation_year);
    //             swal.close();
    //         })
    //         .catch(function (error) {

    //             notification('error', error.message);
    //         });
    // }); 

    var index = 1
    $('#add_univ').click(function() {
        const id = index++

        $('#univ_list').append(getUnivListForm(id));

        $("select").select2({
            allowClear: true,
            placeholder: 'Select Value',
        })
    })

    function deleteRow(id) {
        $('#univ_list #univ_' + id).remove();
    }

    function getUnivListForm(id) {

        const message = 
            '<div class="row align-items-end g-2 mb-3" id="univ_' + id + '">' +
                '<div class="col-md-4 univ-'+ id +'">' +
                '<label>University Name</label>' +
                '<select name="uni_id[]" id="" class="select w-100">' +
                    '<option data-placeholder="true"></option>' + 
                    @forelse ($universities as $university)
                        '<option value="{{ $university->univ_id }}">{{ $university->univ_name }}</option>' +
                    @empty
                        
                    @endforelse
                '</select>' +
                '</div>' +
                '<div class="col-md-4">' +
                '<label>Major</label>' +
                '<select  name="major[]" id="" class="select w-100">' +
                    '<option data-placeholder="true"></option>' +
                    @forelse ($majors as $major)
                        '<option value="{{ $major->id }}">{{ $major->name }}</option>' +
                    @empty
                        
                    @endforelse
                '</select>' +
                '</div>' +
                '<div class="col-md-3">' +
                '<label>Status</label>' +
                '<select name="status[]" id="" class="select w-100">' +
                    '<option value="waitlisted">Waitlisted</option>' +
                    '<option value="accepted">Accepted</option>' +
                    '<option value="denied">Denied</option>' +
                    '<option value="chosen">Chosen</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-md-1 text-end">' +
                '<button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(' + id + ')">' +
                '<i class="bi bi-x"></i>' +
                '</button>' +
                '</div>' +
            '</div>';

        return message;
    }
</script>
@endpush
