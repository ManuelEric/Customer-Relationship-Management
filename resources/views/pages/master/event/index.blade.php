@extends('layout.main')

@section('title', 'Event')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="text-white m-0">
                <i class="bi bi-tag me-1"></i>
                Events
            </h5>
            <a href="{{ url('master/event/create') }}" class="btn btn-sm btn-info"><i class="bi bi-plus-square me-1"></i>
                Add
                Event</a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Event Title</th>
                        <th>Event Location</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th class="bg-info text-white">Action</th>
                    </tr>
                </thead>
                <tfoot class="bg-light text-white">
                    <tr>
                        <td colspan="6"></td>
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
                    <input type="hidden" name="" id="firstLink">
                    <div class="container">
                        <div class="row px-2">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="cta" id="ctaForm"
                                        onchange="linkOption()">
                                    <label class="form-check-label" for="ctaForm">
                                        CTA Form
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="attend" id="attendForm"
                                        onchange="linkOption()">
                                    <label class="form-check-label" for="attendForm">
                                        Attend Event
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="offline" id="offlineForm"
                                        onchange="linkOption()">
                                    <label class="form-check-label" for="offlineForm">
                                        Offline Event
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="ots" id="otsForm"
                                        onchange="linkOption()">
                                    <label class="form-check-label" for="otsForm">
                                        On The Spot
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
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
        function linkOption(param) {
            var cta = $('#ctaForm').is(':checked') ? '&ft=cta' : '' // form type
            var attend = $('#attendForm').is(':checked') ? '&as=attend' : '' // attend status
            var offline = $('#offlineForm').is(':checked') ? '&et=offline' : '' // event type
            var ots = $('#otsForm').is(':checked') ? '&s=ots' : '' // status
            var link = $('#firstLink').val() + cta + attend + offline + ots
            $('#link').val(link)
        };

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


            var table = $('#eventTable').DataTable({
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
                columns: [{
                        data: 'event_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'event_title',
                    },
                    {
                        data: 'event_location',
                    },
                    {
                        data: 'event_startdate',
                        render: function(data, type, row) {
                            let event_startdate = row.event_startdate ? moment(row
                                .event_startdate).format("MMMM Do YYYY HH:mm:ss") : '-'
                            return event_startdate
                        }
                    },
                    {
                        data: 'event_enddate',
                        render: function(data, type, row) {
                            let event_enddate = row.event_enddate ? moment(row
                                .event_enddate).format("MMMM Do YYYY HH:mm:ss") : '-'
                            return event_enddate
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-info generateLinkEmbed"><i class="bi bi-link"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-warning ms-1 showEvent"><i class="bi bi-eye"></i></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger ms-1 deleteEvent"><i class="bi bi-trash2"></i></button>'
                    }
                ]
            });

            @php
                $privilage = $menus['Master']->where('submenu_name', 'Event')->first();
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

            $('#eventTable tbody').on('click', '.generateLinkEmbed ', function() {
                var data = table.row($(this).parents('tr')).data();
                let event_title = data.event_title;
                let event_id = data.event_id;

                // populate
                let embed_link = "{{ $registrationUrl }}?event_name=" + encodeURIComponent(event_title) + "&ev=" + event_id

                $('#link').val(embed_link)
                $('#firstLink').val(embed_link)
                $('#linkEmbed').modal('show')
                // window.location.href = "{{ url('master/event') }}/" + data.event_id;
            });

            $('#eventTable tbody').on('click', '.showEvent ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('master/event') }}/" + data.event_id;
            });

            $('#eventTable tbody').on('click', '.deleteEvent ', function() {
                var data = table.row($(this).parents('tr')).data();
                confirmDelete('master/event', data.event_id)
            });

        });
    </script>
@endsection
