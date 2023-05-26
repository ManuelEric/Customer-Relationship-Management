<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3 g-0">
                            <div class="col-12 mb-2">
                                <label for="">Program Name</label>
                                <select name="q-program" id="" class="select w-100" multiple>
                                    @foreach ($allPrograms as $key => $value)
                                        <optgroup label="{{ $key }}">
                                            @foreach ($value as $program)
                                                <option value="{{ $program->prog_id }}">{{ $program->prog_program }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 d-none" id="filter-withmonth-container">
                                <label for=""></label>
                                <div class="row align-items-center g-0">
                                    <div class="col-5">
                                        <input type="month" name="q-first-monthyear" id="q-first-monthyear" class="form-control">
                                    </div>
                                    <div class="col-2 text-center">
                                        VS
                                    </div>
                                    <div class="col-5">
                                        <input type="month" name="q-second-monthyear" id="q-second-monthyear" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" id="filter-year-container">
                                <label for="">Year</label>
                                <div class="row align-items-center g-0">
                                    <div class="col">
                                        <select name="q-first-year" id="" class="select-pc w-100">
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
                                        <select name="q-second-year" id="" class="select-pc w-100">
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
                        <div class="col-12">
                            <div class="row align-items-center g-0">
                                <div class="col ps-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="filter-use-monthyear" id="use-filter-by-month">
                                        <label class="form-check-label" for="use-filter-by-month">
                                            Use filter by month
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <hr>
                            <button class="btn btn-sm btn-outline-primary btn-compare">
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
                            <table class="table table-hover table-bordered" id="comparison-table">
                                <thead class="text-center">
                                    <tr>
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">Program Name</th>
                                        <th colspan="2">Comparison (IDR)</th>
                                    </tr>
                                    <tr>
                                        <th class="dashboard-pc--year_1">2022</th>
                                        <th class="dashboard-pc--year_2">2023</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($comparisons as $comparison)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $comparison->prog_name.': '.$comparison->prog_program }}</td>
                                            <td class="text-center">{{ number_format($comparison->revenue_year1, '2', '.',',') }} </td>
                                            <td class="text-center">{{ number_format($comparison->revenue_year2, '2', '.',',') }} </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
