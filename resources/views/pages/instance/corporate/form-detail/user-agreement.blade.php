<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                User Agreement
            </h6>
        </div>
    </div>
    <div class="card-body">
        <ol class="list-group list-group-numbered">
        @forelse ($corporate->individualProfessional->user_subjects->groupBy(['subject_id', 'year']) as $key => $user_subject_by_subject_id)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ $user_subject_by_subject_id->first()->first()->subject->name }}</div>
                    @foreach ($user_subject_by_subject_id as $user_subject_by_year)
                        <hr>
                        @foreach ($corporate->individualProfessional->user_subjects()->where('subject_id', $user_subject_by_year->first()->subject_id)->where('year', $user_subject_by_year->first()->year)->get() as $user_subject)
                                <b>{{ $user_subject->year }} {{ $user_subject->grade != null ? '|' . $user_subject->grade : '' }}</b>  
                                @if($user_subject->agreement != null && $loop->index == 0)
                                    <div class="d-grid gap-2 d-md-flex mx-auto">
                                        <h6>
                                            <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Download" class="download" onclick="downloadAgreement('{{$user_subject->id}}')"><i class="bi bi-download"></i></a>
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

<script>
    function downloadAgreement(id){
        var url = '{{ url("user/" . $corporate->individualProfessional->user_subjects->first()->user_roles->role->role_name . "/" . $corporate->individualProfessional->id . "/download_agreement") }}/' + id;
        window.open(url, '_blank');
    }
</script>