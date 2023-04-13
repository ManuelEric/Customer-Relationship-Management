@extends('layout.main')

@section('title', 'Client Event - Bigdata Platform')

@section('content')
<style>
    .btn-download span, .btn-import span {
        display: none;
    }
    .btn-download:hover > span, .btn-import:hover > span {
        display: inline-block;
    }
</style>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Client Event
        </a>
        <div class="">

            <a href="{{ url('api/download/excel-template/client-event') }}" class="btn btn-sm btn-outline-info btn-download"><i
                    class="bi bi-download me-1"></i> <span>Download Templates</span></a>
            <a href="#" class="btn btn-sm btn-outline-info btn-import" data-bs-toggle="modal" data-bs-target="#importData"><i
                    class="bi bi-cloud-upload me-1"></i> <span>Import</span></a>
            <a href="{{ url('program/event/create') }}" class="btn btn-sm btn-primary"><i
                    class="bi bi-plus-square me-1"></i>
                Add Client Event </a>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="eventTable">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="bg-info text-white">#</th>
                        <th class="bg-info text-white">Client Name</th>
                        <th>Event Name</th>
                        {{-- <th>Lead</th> --}}
                        <th>Conversion Lead</th>
                        <th>Joined Date</th>
                        <th>Status</th>
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

    <div class="modal fade" id="importData" tabindex="-1" aria-labelledby="importDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{route('program.event.import')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="importDataLabel">Import CSV Data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">CSV File</label>
                                <input type="file" name="file" id="" class="form-control form-control-sm">
                            </div>
                            <small class="text-warning mt-3">
                                * Please clean the file first, before importing the csv file. <br>
                                You can download the csv template <a href="{{ url('api/download/excel-template/client-event') }}">here</a>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i>
                            Close</button>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-upload"></i>
                            Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
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
                    left: 2,
                    right: 1
                },
                processing: true,
                serverSide: true,
                ajax: '',
                columns: [{
                        data: 'clientevent_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'client_name',
                         render: function(data, type, row, meta) {
                            var existing = moment(row.created_at).format('MMMM Do YYYY, h:mm') == moment(row.client_created_at).format('MMMM Do YYYY, h:mm');
                            var newClientEvent = moment().format("MMM Do YY") == moment(row.created_at).format('MMM Do YY');
                            
                            if(newClientEvent == true){
                                return data + (existing == true ? ' <span class="badge text-bg-primary" style="font-size:8px;">New</span>' : ' <span class="badge text-bg-success" style="font-size:8px";>Existing</span>');
                            }else{
                                return data;
                            }
                        }
                    },
                    {
                        data: 'event_name',
                        name: 'tbl_events.event_title'
                    },
                    {
                        data: 'conversion_lead',
                        // name: 'tbl_lead.main_lead'
                    },
                    {
                        data: 'joined_date',
                        render: function(data, type, row, meta) {
                            return moment(data).format('dddd, DD MMM YYYY');

                        }
                    },
                    {
                        data: 'status',
                        render: function(data, type, row, meta) {
                            switch (parseInt(row.status)) {
                                case 0:
                                    return "Join"
                                    break;

                                case 1:
                                    return "Attend"
                                    break;
                                
                            }
                        }
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button"class="btn btn-sm btn-outline-warning detailEvent"><i class="bi bi-eye"></i></button>'
                    }
                ],
                createdRow: function(row, data, index) {
                    let currentDate = new Date().toJSON().slice(0, 10);
                    if (data['created_at'].slice(0,10) == currentDate) {
                        $('td', row).addClass('table-success');
                    }
                }
            });

            @php            
                $privilage = $menus['Program']->where('submenu_name', 'Client Event')->first();
            @endphp

            @if($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false"); 
                
                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif

            @if ($privilage['export'] == 0)
                table.button(1).disable();
            @endif

            $('#eventTable tbody').on('click', '.detailEvent ', function() {
                var data = table.row($(this).parents('tr')).data();
                window.location.href = "{{ url('program/event') }}/" + data.clientevent_id;
            });
        });
    </script>

@endsection
