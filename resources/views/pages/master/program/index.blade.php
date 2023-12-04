@extends('layout.main')

@section('title', 'Program')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Programs
            </h5>
            <a href="{{ url('master/program/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i> Add
                Program</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Program ID</th>
                        <th>Main Program</th>
                        <th>Sub Program</th>
                        <th>Program Name</th>
                        <th>Type</th>
                        <th>Payment Category</th>
                        <th>Need Tutor/Mentor</th>
                        <th>Scope</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="9"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="modal fade" id="linkEmbed" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span>
                        Link Form Embed
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100 text-start">
                    {{-- <form action="" method="POST" id="reminderForm"> --}}
                    @csrf
                    {{-- @method('put') --}}
                    <div class="form-group">
                        {{-- <label for="">Phone Number Parent</label> --}}
                        <input type="text" name="link" id="link" disabled class="form-control w-100">
                    </div>
                    {{-- <hr> --}}
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="submit" onclick="copyLink()" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Copy</button>
                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>


    {{-- Need Changing --}}
    <script>

        function copyLink() {
            $('#ctaForm').prop('checked', false)
            $('#attendForm').prop('checked', false)
            $('#offlineForm').prop('checked', false)
            $('#otsForm').prop('checked', false)
            $('#linkEmbed').modal('hide');
            // Get the text field
            var copyText = document.getElementById("link");
            // Select the text field
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(copyText.value);


            // Alert the copied text
            // alert("Copied the text: " + copyText.value);
            Swal.fire({
                icon: 'success',
                text: "Form embed successfully copied ",
                timer: 1500,
                width:300,
                showConfirmButton: false,
            });
            //    swal("Copied the text: " + copyText.value);
        }

        $(document).ready(function() {
            var table = $('#programTable').DataTable({
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
                    left: window.matchMedia('(max-width: 767px)').matches ? 0 : 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                pagingType: window.matchMedia('(max-width: 767px)').matches ? 'full' : 'simple_numbers',
                columns: [
                    {
                        data: 'prog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'prog_id',
                        render: function(data, type, row, meta) {
                            var badge = '';
                            if (row.active === 0)
                                badge = '<span class="badge text-bg-danger" style="font-size:8px";>Inactive</span>';

                            if (row.active == 1 && row.created_at == Date.now())
                                badge = '<span class="badge text-bg-success" style="font-size: 8px;">New</span>'

                            return data + ' ' + badge;
                        }
                    },
                    {
                        data: 'main_prog_name',
                    },
                    {
                        data: 'sub_prog_name',
                    },
                    {
                        data: 'program_name',
                    },
                    {
                        data: 'prog_type',
                    },
                    {
                        data: 'prog_payment',
                        render: function(data, type, row, meta) {
                            return row.prog_payment.toUpperCase()
                        }
                    },
                    {
                        data: 'prog_mentor',
                    },
                    {
                        data: 'prog_scope',
                        render: function(data, type, row, meta) {
                            if (data != null)
                                return row.prog_scope.charAt(0).toUpperCase() + row.prog_scope.slice(1);
                            
                            return data;
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-info generateLinkEmbed"><i class="bi bi-link"></i></button>' +
                            ' <button type="button" class="btn btn-sm btn-outline-warning editProgram"><i class="bi bi-pencil"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteProgram"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            });

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Program')->first();
            @endphp

            @if ($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false");

                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif

            @if ($privilage['export'] == 0)
                table.button(1).disable();
            @endif

            realtimeData(table)

            $('#programTable tbody').on('click', '.generateLinkEmbed ', function() {
                var data = table.row($(this).parents('tr')).data();
                $('#link').val("{{ url('form/program') }}?program_name=" + encodeURIComponent(data.prog_program))
                // $('#firstLink').val("{{ url('form/event') }}?event_name=" + encodeURIComponent(data
                //     .event_title))
                $('#linkEmbed').modal('show')
                // window.location.href = "{{ url('master/event') }}/" + data.event_id;
            });

            $('#programTable tbody').on('click', '.editProgram ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/program') }}/" + data.prog_id + '/edit';
            });

            $('#programTable tbody').on('click', '.deleteProgram ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/program', data.prog_id)
            });
        });
    </script>
@endsection
