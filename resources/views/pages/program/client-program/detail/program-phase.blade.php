<div class="accordion accordion-flush shadow-sm mb-3">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#programBoughtInfo">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    Program Bought
                </h6>
            </button>
        </h2>
        <div id="programBoughtInfo" class="accordion-collapse collapse show">
            <div class="accordion-body p-2">
                <div class="card">
                    <div class="card-body" style="overflow: auto;">
                        @if(isset($programPhases) && $programPhases != null)
                            <table class="table table-borderless" id="list-program-bought">
                                <tbody>
                                    @foreach ($programPhases as $programPhase)
                                        {{-- Category --}}
                                        <tr align="center">
                                            <td colspan="3" class="text-start" style="font-size: 16px !important; font-weight:500;">{{ $programPhase->phase_name }}</td>
                                        </tr>
                                        @foreach ($programPhase->phase_detail as $phase_detail)
                                            {{-- Package --}}
                                            @php
                                                $data_phase_lib[$phase_detail->id] = [];
                                                $data_quota = 0;
                                                $is_check_program_phase = false;
                                                $clientprog_program_phase = null;
                                                if($clientprog_program_phase = $phase_detail->client_program->where('clientprog_id', $clientProgram->clientprog_id)->first()){
                                                    $is_check_program_phase = true;
                                                    $data_quota = $clientprog_program_phase->pivot->quota;
                                                }

                                                foreach ($phase_detail->phase_libraries as $pl) {
                                                    $data_phase_lib[$pl->phase_detail_id] = $pl;
                                                    if($clientprog_program_phase = $pl->client_program->where('clientprog_id', $clientProgram->clientprog_id)->first()){
                                                        $is_check_program_phase = true;
                                                        $data_quota = $clientprog_program_phase->pivot->quota;
                                                    }
                                                }
                                            @endphp
                                            <tr align="left">
                                                <td><input class="form-check-input check-package" type="checkbox" value="" data-phase-detail-id="{{$phase_detail->id}}" data-phase-lib-id="{{$data_phase_lib[$phase_detail->id]->id ?? '-'}}" data-clientprog-id="{{ isset($clientProgram) ? $clientProgram->clientprog_id : '-' }}" id="check-{{$phase_detail->id}}" {{ $is_check_program_phase != null ? 'checked' : '' }}></td>
                                                <td>{{ $phase_detail->phase_detail_name }}</td>
                                                <td style="min-width: 70px">

                                                    <input type="number" min="0" id="quota-{{$phase_detail->id}}" data-phase-detail-id={{$phase_detail->id}} data-phase-lib-id="{{$data_phase_lib[$phase_detail->id]->id ?? '-'}}" data-clientprog-id="{{ isset($clientProgram) ? $clientProgram->clientprog_id : '-' }}" class="form-control form-control-sm quota-program-bought" value="{{ $data_quota }}" {{ !$is_check_program_phase ? 'disabled' : '' }}>

                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            There is no program bought
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
