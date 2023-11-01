@if (count($existingMentee) > 0 && count($existingNonMentee) > 0 && count($existingNonClient) > 0)
    <div class="card mb-3">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h6 class="p-0 m-0">Existing Client</h6>
            {{-- <span class="badge bg-primary">{{ count($clientEvents) }}</span> --}}
        </div>
        <div class="card-body p-2">
            <ul class="list-group">
                <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="">Mentee</div>
                    <div class="dropdown">
                        <span class="badge badge-info dropdown-toggle"
                            data-bs-toggle="dropdown">{{ $existingMentee->count() }}</span>
                        <div class="dropdown-menu overflow-auto text-center px-2"
                            style="max-width: 450px; max-height:200px;">
                            {{ $existingMentee->count() > 0 ? '' : 'There is no data.' }}
                            <table class="table table-striped table-hover">
                                @foreach ($existingMentee as $existMentee)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $existMentee->client->full_name }}</td>
                                        <td>{{ $existMentee->client->school->sch_name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </li>
                <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="">Non Mentee</div>
                    <div class="dropdown">
                        <span class="badge badge-info dropdown-toggle"
                            data-bs-toggle="dropdown">{{ $existingNonMentee->count() }}</span>
                        <div class="dropdown-menu overflow-auto text-center px-2"
                            style="max-width: 450px; max-height:200px;">
                            {{ $existingNonMentee->count() > 0 ? '' : 'There is no data.' }}
                            <table class="table table-striped table-hover">
                                @foreach ($existingNonMentee as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->client->full_name }}</td>
                                        <td>{{ $item->client->school->sch_name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </li>
                <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="">Non Client</div>
                    <div class="dropdown">
                        <span class="badge badge-info dropdown-toggle"
                            data-bs-toggle="dropdown">{{ $existingNonClient->count() }}</span>
                        <div class="dropdown-menu overflow-auto text-center px-2"
                            style="max-width: 450px; max-height:200px;">
                            {{ $existingNonClient->count() > 0 ? '' : 'There is no data.' }}
                            <table class="table table-striped table-hover">
                                @foreach ($existingNonClient as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->client->full_name }}</td>
                                        <td>{{ $item->client->school->sch_name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </li>

            </ul>
        </div>
    </div>
@endif
