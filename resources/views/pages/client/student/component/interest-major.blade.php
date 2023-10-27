<div class="card rounded mb-2">
    <div class="card-header">
        <div class="">
            <h5 class="m-0 p-0">Interest Major</h5>
        </div>
    </div>
    <div class="card-body">
        @forelse ($student->interestMajor as $major)
            <div class="badge badge-primary me-1 mb-2">{{ $major->name }}</div>
        @empty
            There's no interest major yet
        @endforelse
    </div>
</div>