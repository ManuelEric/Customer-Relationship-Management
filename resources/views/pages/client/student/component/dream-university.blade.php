<div class="card rounded mb-2">
    <div class="card-header">
        <div class="">
            <h5 class="m-0 p-0">Dream University</h5>
        </div>
    </div>
    <div class="card-body">
        @forelse ($student->interestUniversities as $university)
            <div class="badge badge-danger me-1 mb-2">{{ $university->univ_name }}</div>
        @empty
            There's no dream university
        @endforelse
    </div>
</div>