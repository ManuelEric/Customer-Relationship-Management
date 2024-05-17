<div class="card rounded">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="">
            <h5 class="m-0 p-0">Programs</h5>
        </div>
        <div class="">
            <a href="{{ route('student.program.create', ['student' => $student->id]) }}"
                class="btn btn-sm btn-primary">Add Program</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
            <thead class="bg-secondary text-white">
                <tr class="text-center" role="row">
                    <th class="bg-info text-white">
                        <i class="bi bi-check"></i>
                    </th>
                    <th class="bg-info text-white">No</th>
                    <th class="bg-info text-white">Program Name</th>
                    <th>Conversion Lead</th>
                    <th>First Discuss</th>
                    <th>PIC</th>
                    <th>Program Status</th>
                    <th>Running Status</th>
                    <th>Initial Consult Date</th>
                    <th>Initial Assessment Sent</th>
                    <th>End Date</th>
                    <th>Notes</th>
                    <th>Total Universities</th>
                    <th>Total Dollar</th>
                    <th>Kurs Dollar-Rupiah</th>
                    <th>Total Rupiah</th>
                    <th>#</th>
                </tr>
            </thead>
            <tfoot class="bg-light text-white">
                <tr>
                    <td colspan="17"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
    <script>
        var url = "{{ url('client/student') . '/' . $student->id . '/program' }}"
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
                    },
                    {
                        text: '<i class="bi bi-basket"></i> Bundle',
                        action: function(e, dt, node, config) {
                            addBundle();
                        }
                    },
                ],
                scrollX: true,
                fixedColumns: {
                    left: 0,
                    right: 0
                },
                processing: true,
                serverSide: true,
                ajax: '{{ url('api/client/' . $student->id . '/programs') }}',
                columns: [{
                        orderable: false,
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<input type="checkbox" class="editor-active cursor-pointer" data-id="' +
                                data + '" data-no="' + (meta.row + meta.settings._iDisplayStart + 1) +'">'
                        }
                    },
                    {
                        data: 'prog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'program_name',
                        render: function(data, type, row, meta) {
                            
                            return row.is_bundle > 0 ? data + ' <span class="badge text-bg-success" style="font-size:8px";>Bundle</span>' : data;
                        }
                    },
                    {
                        data: 'conversion_lead',
                        className: 'text-center',
                    },
                    {
                        data: 'first_discuss_date',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ? moment(data).format("MMMM Do YYYY") : '-'
                        }
                    },
                    {
                        data: 'pic_name',
                        className: 'text-center',
                    },
                    {
                        data: 'program_status',
                        className: 'text-center',
                    },
                    {
                        data: 'prog_running_status',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            switch (parseInt(data)) {
                                case 0:
                                    return "Not yet"
                                    break;

                                case 1:
                                    return "Ongoing"
                                    break;

                                case 2:
                                    return "Done"
                                    break;
                            }
                        }

                    },
                    {
                        data: 'initconsult_date',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ? moment(data).format("MMMM Do YYYY") : '-'
                        }
                    },
                    {
                        data: 'assessmentsent_date',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ? moment(data).format("MMMM Do YYYY") : '-'
                        }
                    },
                    {
                        data: 'prog_end_date',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return data ? moment(data).format("MMMM Do YYYY") : '-'
                        }
                    },
                    {
                        data: 'strip_tag_notes',
                        className: 'text-center',
                        searchable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'total_uni',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if(parseInt(data) === 0){
                                return "-"
                            }else{
                                return data;
                            }
                        }
                    },
                    {
                        data: 'total_foreign_currency',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if(parseInt(data) === 0){
                                return "-"
                            }else{
                                return new Intl.NumberFormat("en-US", {
                                    style: "currency",
                                    currency: "USD",
                                    minimumFractionDigits: 0
                                }).format(data);
                            }
                        }
                    },
                    {
                        data: 'foreign_currency_exchange',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if(parseInt(data) === 0){
                                return "-"
                            }else{
                                return new Intl.NumberFormat("id-ID", {
                                    style: "currency",
                                    currency: "IDR",
                                    minimumFractionDigits: 0
                                }).format(data);
                            }
                        }
                    },
                    {
                        data: 'total_idr',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row, meta) {
                            if(parseInt(data) === 0){
                                return "-"
                            }else{
                                return new Intl.NumberFormat("id-ID", {
                                    style: "currency",
                                    currency: "IDR",
                                    minimumFractionDigits: 0
                                }).format(data);
                            }
                        }
                    },
                    {
                        data: 'clientprog_id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<a href="' + url + '/' + data +
                                '" class="btn btn-sm btn-warning"><i class="bi bi-info-circle me-2"></i>More</a>'
                        }
                    }
                ],
                createdRow: function ( row, data, index ) {

                    if ( data['program_status'] == 'Success')
                        $('td', row).eq(5).addClass('bg-success text-light');


                    if ( data['prog_running_status'] == 2 ) 
                        $('td', row).eq(6).addClass('bg-success text-light');
                    else if (data['prog_running_status'] == 0)
                        $('td', row).eq(6).addClass('bg-warning');

                    
                },
            });

            // Tooltip 
            $('#programTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });

            function addBundle() {
                var selected = [];
                var no = [];
                var html = '';
                $('input.editor-active').each(function() {
                    if ($(this).prop('checked')) {
                        selected.push($(this).data('id'));
                        no.push($(this).data('no'));
                    }
                });

                if (selected.length > 1) {
                    Swal.fire({
                        title: "Confirmation!",
                        text: 'Are you sure to create bundle this program?',
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            showLoading();
                            var link = '{{ route('program.client.bundle') }}';
                            axios.post(link, {
                                    choosen: selected,
                                    number: no
                                })
                                .then(function(response) {
                                    
                                    html = '';
                                    html += `<ul>`;

                                    if(response.data.success == false){
                                        var error = response.data.error
                                        if(Object.keys(error).length){
                                            Object.keys(error).forEach(key => {
                                                html += `<li class="text-danger">${key + ': ' + error[key]}</li>`
                                            });
                                        }
                                        html += `</ul>`;
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            html: html,
                                        });
                                    }else{
                                        swal.close();
                                        notification('success', 'Successfully created a bundle program');
                                        location.reload();
                                    }

                                    $("#rawTable").DataTable().ajax.reload()
                                })
                                .catch(function(error) {
                                    
                                    swal.close();
                                    notification('error', error.message);
                                })
                        }
                    });

                } else if(selected.length === 1){
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Please select at least 2 client program!",
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Please select the client program data first!",
                    });
                }
            }
                
        });
    </script>
@endpush
