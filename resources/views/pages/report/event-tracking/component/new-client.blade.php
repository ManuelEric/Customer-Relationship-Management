@if (count($new_client) > 0)
    <div class="card mb-3">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h6 class="p-0 m-0">New Client</h6>
            {{-- <span class="badge bg-primary">{{ count($clientEvents) }}</span> --}}
        </div>
        <div class="card-body p-2">
            <ul class="list-group">
                @if ($new_client->where('register_by', 'student')->count() > 0)
                    <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                        <div class="">Student</div>
                        <span class="badge badge-info"></span>
                        <div class="dropdown">
                            <span class="badge badge-info dropdown-toggle" data-bs-toggle="dropdown">
                                {{ $new_client->where('register_by', 'student')->count() }}
                            </span>
                            <div class="dropdown-menu overflow-auto text-center px-2"
                                style="max-width: 450px; max-height:200px;">
                                {{ $new_client->where('register_by', 'student')->count() > 0 ? '' : 'There is no data.' }}
                                <table class="table table-striped table-hover">
                                    @foreach ($new_client->where('register_by', 'student') as $item)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->client->full_name ?? '-' }}</td>
                                            <td>{{ $item->client->school->sch_name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </li>
                @endif

                @if ($new_client->where('register_by', 'parent')->count() > 0)
                    <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                        <div class="">Parent</div>
                        <div class="dropdown">
                            <span class="badge badge-info dropdown-toggle" data-bs-toggle="dropdown">
                                {{ $new_client->where('register_by', 'parent')->count() }}
                            </span>
                            <div class="dropdown-menu overflow-auto text-center px-2"
                                style="max-width: 450px; max-height:200px;">
                                {{ $new_client->where('register_by', 'parent')->count() > 0 ? '' : 'There is no data.' }}
                                <table class="table table-striped table-hover">
                                    @foreach ($new_client->where('register_by', 'parent') as $item)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->client->full_name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </li>
                @endif

                @if ($new_client->where('register_by', 'teacher/counselor')->count() > 0)
                    <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                        <div class="">Teacher / Counselor</div>
                        <div class="dropdown">
                            <span class="badge badge-info dropdown-toggle" data-bs-toggle="dropdown">
                                {{ $new_client->where('register_by', 'teacher/counselor')->count() }}
                            </span>
                            <div class="dropdown-menu overflow-auto text-center px-2"
                                style="max-width: 450px; max-height:200px;">
                                {{ $new_client->where('register_by', 'teacher/counselor')->count() > 0 ? '' : 'There is no data.' }}
                                <table class="table table-striped table-hover">
                                    @foreach ($new_client->where('register_by', 'teacher/counselor') as $item)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->client->full_name ?? '-' }}</td>
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
