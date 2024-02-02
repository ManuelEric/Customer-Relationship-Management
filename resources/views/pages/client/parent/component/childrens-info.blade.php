<div class="col-md-7">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h6 class="my-1 p-0">
                <i class="bi bi-info-circle me-1"></i>
                List of Child
            </h6>
        </div>
        <div class="card-body" style="overflow: auto;">
            @if (isset($parent->childrens))
            <table class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Full Name</th>
                        <th>School Name</th>
                        <th>Graduation Year</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($parent->childrens as $children)
                        <tr align="center">
                            <td>{{ $no++ }}</td>    
                            <td>{{ $children->fullname }}</td>
                            <td>{{ isset($children->school) ? $children->school->sch_name : '-' }}</td>
                            <td>{{ $children->graduation_year }}</td>
                            <td>
                                <button class="btn btn-outline-light btn-sm rounded text-danger ms-2" data-bs-toggle="tooltip"
                                    data-bs-title="Disconnect Children"
                                    onclick="confirmDelete('client/parent/{{ $parent->id }}/student', '{{ $children->id }}')"
                                    >
                                    <i class="bi bi-dash-circle-fill"></i>
                                </button>
                                <a href="{{ url('client/student').'/'.$children->id }}" class="btn btn-outline-warning btn-sm rounded"><i
                                    class="bi bi-eye"></i></a>
                            </td>
                        </tr> 
                    @endforeach
                </tbody>
            </table>
            @else
                There's no children
            @endif
        </div>
    </div>
</div>
