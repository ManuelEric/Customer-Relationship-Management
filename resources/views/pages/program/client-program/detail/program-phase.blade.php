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
                        @if($programBought != null)
                            <table class="table table-borderless" id="list-program-bought">
                                <tbody>
                                    @foreach ($programBought as $pb)
                                        {{-- Category --}}
                                        <tr align="center">
                                            <td colspan="3" class="text-start" style="font-size: 16px !important; font-weight:500;">{{ $pb->phase_name }}</td>
                                        </tr>
                                        @foreach ($pb->phase_detail as $pd)
                                            {{-- Package --}}
                                            @php
                                                $data_phase_lib[$pd->id] = [];
                                                $is_check_program_phase = false;
                                                foreach ($pd->phase_libraries as $pl) {
                                                    $data_phase_lib[$pl->phase_detail_id] = $pl;
                                                    if(count($pl->client_program) > 0){
                                                        $is_check_program_phase = true;
                                                    }
                                                }
                                            @endphp
                                            <tr align="left">
                                                <td><input class="form-check-input check-package" type="checkbox" value="" data-package-id="{{$pd->id}}" data-phase-lib-id="{{$data_phase_lib[$pd->id]->id ?? '-'}}" data-clientprog-id="{{ isset($clientProgram) ? $clientProgram->clientprog_id : '-' }}" id="check-{{$pd->id}}" {{ $data_phase_lib[$pd->id] != null && $is_check_program_phase != null ? 'checked' : '' }}></td>
                                                <td>{{ $pd->phase_detail_name }}</td>
                                                <td>
                                                    <div class="qty" id="qty-{{$pd->id}}">
                                                        <span class="minus border border-dark mt-2" data-package="{{$pd->id}}">-</span>
                                                        <input type="number" class="count" name="qty" id="counting-{{$pd->id}}" value="
                                                        {{ $data_phase_lib[$pd->id] != null && $data_phase_lib[$pd->id]->quota != null ? $data_phase_lib[$pd->id]->quota : '0'}}">
                                                        <span class="plus border border-dark mt-2" data-package="{{$pd->id}}">+</span>
                                                    </div>
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
