@if (isset($feeder))
    <div class="card mb-3">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h6 class="p-0 m-0">Feeder Schools</h6>
        </div>
        <div class="card-body p-2 overflow-auto" style="max-height: 200px;">
            <ul class="list-group">
                @if ($feeder !== null)
                    @foreach ($feeder as $key => $val)
                        <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                            <div class="">{{ $key }}</div>
                            <span class="badge badge-warning">{{ $val }}</span>
                        </li>
                    @endforeach
                @else
                    <li class="text-center">There's no feeder schools</li>
                @endif
            </ul>
        </div>
    </div>
@endif
