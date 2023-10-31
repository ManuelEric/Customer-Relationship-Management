<div class="card rounded mb-2">
    <div class="card-header">
        <div class="">
            <h5 class="m-0 p-0">Interest Countries</h5>
        </div>
    </div>
    <div class="card-body">
        @forelse ($student->destinationCountries as $country)
            <div class="badge badge-success me-1 mb-2">{{ $country->name }}</div>
        @empty
            There's no interest countries yet
        @endforelse
    </div>
</div>