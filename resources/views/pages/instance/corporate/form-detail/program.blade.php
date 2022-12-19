<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h6 class="m-0 p-0">
                <i class="bi bi-building me-2"></i>
                Programs
            </h6>
        </div>
        <div class="">
            <a href="{{ url('program/corporate/' . strtolower($corporate->corp_id) . '/detail/create') }}"
                class="btn btn-sm btn-outline-primary rounded mx-1">
                <i class="bi bi-plus"></i>
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-borderless table-hover">
            @for ($i = 0; $i < 3; $i++)
                <tr>
                    <td>Program Name</td>
                    <td class="text-center">ALL-in PIC</td>
                    <td class="text-center">Program Date</td>
                    <td class="text-end">Success</td>
                </tr>
            @endfor
        </table>
    </div>
</div>
