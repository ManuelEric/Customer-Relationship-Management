<div class="card rounded mb-2">
    <div class="card-header">
        <div class="">
            <h5 class="m-0 p-0">Parents Information</h5>
        </div>
    </div>
    <div class="card-body">
        @forelse ($student->parents as $parent)
            <div class="row mb-2 g-1">
                <div class="col d-flex justify-content-between">
                    <label>
                        Parents Name
                    </label>
                    <label>:</label>
                </div>
                <div class="col-md-9 col-8">
                    {{ $parent->fullname }}
                </div>
            </div>
            <div class="row mb-2 g-1">
                <div class="col d-flex justify-content-between">
                    <label>
                        Parents Email
                    </label>
                    <label>:</label>
                </div>
                <div class="col-md-9 col-8">
                    {{ $parent->mail }}
                </div>
            </div>
            <div class="row mb-2 g-1">
                <div class="col d-flex justify-content-between">
                    <label>
                        Parents Phone
                    </label>
                    <label>:</label>
                </div>
                <div class="col-md-9 col-8">
                    {{ $parent->phone }}
                </div>
            </div>

        @empty
            There's no parent information yet
        @endforelse
    </div>
</div>
