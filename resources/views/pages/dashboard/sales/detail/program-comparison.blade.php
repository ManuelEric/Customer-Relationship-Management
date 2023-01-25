<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3 g-0">
                            <div class="col-12 mb-2">
                                <label for="">Program Name</label>
                                <select name="" id="" class="select w-100" multiple></select>
                            </div>
                            <div class="col-12">
                                <label for="">Year</label>
                                <div class="row align-items-center g-0">
                                    <div class="col">
                                        <select name="" id="" class="select w-100">
                                            @for ($i = 2020; $i <= date('Y'); $i++)
                                                <option value="{{ $i }}"
                                                    {{ $i == date('Y') - 1 ? 'selected' : '' }}>{{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-1 text-center">
                                        VS
                                    </div>
                                    <div class="col">
                                        <select name="" id="" class="select w-100">
                                            @for ($i = 2020; $i <= date('Y'); $i++)
                                                <option value="{{ $i }}"
                                                    {{ $i == date('Y') ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-search"></i>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">Program Name</th>
                                        <th colspan="2">Comparison</th>
                                    </tr>
                                    <tr>
                                        <th>2022</th>
                                        <th>2023</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 1; $i < 10; $i++)
                                        <tr>
                                            <td class="text-center">{{ $i }}</td>
                                            <td>Program Name</td>
                                            <td class="text-center">30 (IDR)</td>
                                            <td class="text-center">50 (IDR)</td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
