@extends('layout.main')

@section('title', 'Student - Bigdata Platform')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Student
        </a>
        <a href="{{ url('client/student/create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-square me-1"></i> Add
            Student</a>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == "prospective" ? 'active' : '' }}" aria-current="page"
                        href="{{ url('client/student?st=prospective') }}">Prospective</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == "potential" ? 'active' : '' }}"
                        href="{{ url('client/student?st=potential') }}">Potential</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == "current" ? 'active' : '' }}"
                        href="{{ url('client/student?st=current') }}">Current</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::get('st') == "completed" ? 'active' : '' }}"
                        href="{{ url('client/student?st=completed') }}">Completed</a>
                </li>
            </ul>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-dark text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Name</th>
                        <th>Mail</th>
                        <th>Phone</th>
                        <th>Parents Name</th>
                        <th>Parents Phone</th>
                        <th>School</th>
                        <th>Graduation Year</th>
                        <th>Grade</th>
                        <th>Instagram</th>
                        <th>Location</th>
                        <th>Lead</th>
                        <th>Level of Interest</th>
                        <th>Interested Program</th>
                        <th>Success Program</th>
                        <th>Mentor/Tutor</th>
                        <th>Year of Study Abroad</th>
                        <th>Country of Study Abroad</th>
                        <th>University Destination</th>
                        <th>Interest Major</th>
                        <th>Last Update</th>
                        <th>Status</th>
                        <th class="bg-info text-white">Priority</th>
                        <th class="bg-info text-white"># Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($students))
                    @php
                        $no = 1;
                    @endphp
                        @foreach ($students as $student)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $student->first_name.' '.$student->last_name }}</td>
                                <td>{{ $student->mail }}</td>
                                <td>{{ $student->phone }}</td>
                                <td>{{ $student->parents()->first()->first_name.' '.$student->parents()->first()->last_name }}</td>
                                <td>{{ $student->parents()->first()->phone }}</td>
                                <td>{{ $student->school->sch_name }}</td>
                                <td>{{ $student->graduation_year }}</td>
                                <td>{{ $student->st_grade }}</td>
                                <td>{{ $student->insta }}</td>
                                <td>{{ $student->address }}</td>
                                {{-- <td>{{ $student }}</td> --}}
                                <td>{{ isset($student->lead) ? ($student->lead->main_lead == "KOL" ? $student->lead->sub_lead : $student->lead->main_lead) : null }}</td>
                                <td>Level of Interest</td>
                                <td>Interested Program</td>
                                <td>Success Program</td>
                                <td>Main Mentor</td>
                                <td>Year of Study Abroad</td>
                                <td>Country of Study Abroad</td>
                                <td>University Destination</td>
                                <td>Interest Major</td>
                                <td>Last Update</td>
                                <td>Status</td>
                                <td>Priority</td>
                                <td class="text-center"><a href="{{ url('client/mentee/1') }}"
                                        class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="23"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Need Changing --}}
    <script>
        $(document).ready(function() {
            var table = $('#clientTable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
                ],
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                scrollX: true,
                fixedColumns: {
                    left: 2,
                    right: 2
                },
            });

            $('#clientTable tbody').on('click', '.editClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('asset') }}/" + data.asset_id.toLowerCase() + '/edit';
            });

            $('#clientTable tbody').on('click', '.deleteClient ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('asset', data.asset_id)
            });
        });
    </script>
@endsection
