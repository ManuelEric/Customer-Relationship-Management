@if (count($existing_mentee) > 0 && count($existing_non_mentee) > 0 && count($existing_non_client) > 0)
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
                            data-bs-toggle="dropdown">{{ $existing_mentee->count() }}</span>
                        <div class="dropdown-menu overflow-auto text-center px-2"
                            style="max-width: 450px; max-height:200px;">
                            {{ $existing_mentee->count() > 0 ? '' : 'There is no data.' }}
                            <table class="table table-striped table-hover">
                                @foreach ($existing_mentee as $mentee)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $mentee->client->full_name ?? '-' }}</td>
                                        <td>{{ $mentee->client->school->sch_name ?? '-' }}</td>
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
                            data-bs-toggle="dropdown">{{ $existing_non_mentee->count() }}</span>
                        <div class="dropdown-menu overflow-auto text-center px-2"
                            style="max-width: 450px; max-height:200px;">
                            {{ $existing_non_mentee->count() > 0 ? '' : 'There is no data.' }}
                            <table class="table table-striped table-hover">
                                @foreach ($existing_non_mentee as $non_mentee)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $non_mentee->client->full_name ?? '-' }}</td>
                                        <td>{{ $non_mentee->client->school->sch_name ?? '-' }}</td>
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
                            data-bs-toggle="dropdown">{{ $existing_non_client->count() }}</span>
                        <div class="dropdown-menu overflow-auto text-center px-2"
                            style="max-width: 450px; max-height:200px;">
                            {{ $existing_non_client->count() > 0 ? '' : 'There is no data.' }}
                            <table class="table table-striped table-hover">
                                @foreach ($existing_non_client as $non_client)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $non_client->client->full_name ?? '-' }}</td>
                                        <td>{{ $non_client->client->school->sch_name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </li>
                <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="">Parent Mentee</div>
                    <div class="dropdown">
                        <span class="badge badge-info dropdown-toggle"
                            data-bs-toggle="dropdown">{{ $existing_mentee->where('register_as', 'parent')->count() }}</span>
                        <div class="dropdown-menu overflow-auto text-center px-2"
                            style="max-width: 450px; max-height:200px;">
                            {{ $existing_mentee->count() > 0 ? '' : 'There is no data.' }}
                            <table class="table table-striped table-hover">
                                @foreach ($existing_mentee->where('register_as', 'parent') as $existMentee)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $existMentee->parent_name ?? '-' }}</td>
                                        <td>{{ $existMentee->parent_mail ?? '-' }}</td>
                                        <td>{{ $existMentee->parent_phone ?? '-' }}</td>
                                        {{-- <td>{{ $existMentee->register_as == 'student' ? 'Estimated as student' : '-' }}</td> --}}
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </li>
                <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="">Parent Non Mentee</div>
                    <div class="dropdown">
                        <span class="badge badge-info dropdown-toggle"
                            data-bs-toggle="dropdown">{{ $existing_non_mentee->where('register_as', 'parent')->count() }}</span>
                        <div class="dropdown-menu overflow-auto text-center px-2"
                            style="max-width: 450px; max-height:200px;">
                            {{ $existing_non_mentee->count() > 0 ? '' : 'There is no data.' }}
                            <table class="table table-striped table-hover">
                                @foreach ($existing_non_mentee->where('register_as', 'parent') as $non_mentee)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $non_mentee->parent_name ?? '-' }}</td>
                                        <td>{{ $non_mentee->parent_mail ?? '-' }}</td>
                                        <td>{{ $non_mentee->parent_phone ?? '-' }}</td>
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
