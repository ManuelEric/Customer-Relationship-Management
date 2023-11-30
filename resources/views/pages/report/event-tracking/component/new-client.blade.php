@if (count($newClient) > 0)
    <div class="card mb-3">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h6 class="p-0 m-0">New Client</h6>
            {{-- <span class="badge bg-primary">{{ count($clientEvents) }}</span> --}}
        </div>
        <div class="card-body p-2">
            <ul class="list-group">
                @if ($newClient->where('register_as', 'student')->count() > 0)
                    <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                        <div class="">Student</div>
                        <span class="badge badge-info"></span>
                        <div class="dropdown">
                            <span class="badge badge-info dropdown-toggle" data-bs-toggle="dropdown">
                                {{ $newClient->where('register_as', 'student')->count() }}
                            </span>
                            <div class="dropdown-menu overflow-auto text-center px-2"
                                style="max-width: 450px; max-height:200px;">
                                {{ $newClient->where('register_as', 'student')->count() > 0 ? '' : 'There is no data.' }}
                                <table class="table table-striped table-hover">
                                    @foreach ($newClient->where('register_as', 'student') as $item)
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
                @endif

                @if ($newClient->where('register_as', 'parent')->count() > 0)
                    <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                        <div class="">Parent</div>
                        <div class="dropdown">
                            <span class="badge badge-info dropdown-toggle" data-bs-toggle="dropdown">
                                {{ $newClient->where('register_as', 'parent')->count() }}
                            </span>
                            <div class="dropdown-menu overflow-auto text-center px-2"
                                style="max-width: 450px; max-height:200px;">
                                {{ $newClient->where('register_as', 'parent')->count() > 0 ? '' : 'There is no data.' }}
                                <table class="table table-striped table-hover">
                                    @foreach ($newClient->where('register_as', 'parent') as $item)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->client->full_name }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </li>
                @endif

                @if ($newClient->where('register_as', 'teacher/counselor')->count() > 0)
                    <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                        <div class="">Teacher / Counselor</div>
                        <div class="dropdown">
                            <span class="badge badge-info dropdown-toggle" data-bs-toggle="dropdown">
                                {{ $newClient->where('register_as', 'teacher/counselor')->count() }}
                            </span>
                            <div class="dropdown-menu overflow-auto text-center px-2"
                                style="max-width: 450px; max-height:200px;">
                                {{ $newClient->where('register_as', 'teacher/counselor')->count() > 0 ? '' : 'There is no data.' }}
                                <table class="table table-striped table-hover">
                                    @foreach ($newClient->where('register_as', 'teacher/counselor') as $item)
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
                @endif
            </ul>
        </div>
    </div>
@endif
