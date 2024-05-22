<div class="accordion accordion-flush shadow-sm">
    <div class="accordion-item rounded">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#listProgram">
                <h6 class="m-0 p-0">
                    <i class="bi bi-person me-2"></i>
                    List Program
                </h6>
            </button>
        </h2>
        <div id="listProgram" class="accordion-collapse collapse show" aria-labelledby="listProgram">
            <div class="accordion-body p-2">
                <div class="card">
                    <div class="card-body" style="overflow: auto;">
                        <table class="table table-bordered" id="list-program">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Program Name</th>
                                    <th>Client Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($receipt->invoiceProgram->bundling->details as $bundlingDetail)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $bundlingDetail->client_program->program->program_name }}</td>
                                        <td>{{ $bundlingDetail->client_program->client->full_name }}</td>
                                        {{-- <td class="text-center"><a href="{{ route('invoice.program.create') . '?prog=' . $bundlingDetail->client_program->clientprog_id }}" target="_blank"><h5><i
                                            class="bi bi-eye me-1"></i></h5></a></td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
