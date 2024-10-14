@extends('layout.main')

@section('title', 'List of Alumni')

@section('content')
    <div class="card bg-secondary mb-1 p-2">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="text-white m-0">
                    <i class="bi bi-tag me-1"></i>
                    Alumni
                </h5>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">
                <div class="dropdown">
                    <button href="#" class="btn btn-sm btn-light text-dark dropdown-toggle" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" id="filter">
                        <i class="bi bi-funnel me-2"></i> Filter
                    </button>
                    <form action="" class="dropdown-menu dropdown-menu-end pt-0 shadow advance-filter"
                        style="width: 400px;" id="advanced-filter">
                        <div class="dropdown-header bg-info text-dark py-2 d-flex justify-content-between">
                            Advanced Filter
                            <i class="bi bi-search"></i>
                        </div>
                        <div class="row p-3">
                            <div class="col-md-12 mb-2">
                                <label for="">School Name</label>
                                <select name="school_name[]" class="select form-select form-select-sm w-100" multiple
                                    id="school-name">
                                    @foreach ($advanced_filter['schools'] as $school)
                                        <option value="{{ $school->sch_name }}"
                                            {{ Request::get('sch') == $school->sch_name && Request::get('sch') != '' && Request::get('sch') != null ? 'selected' : null }}>
                                            {{ $school->sch_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-2">
                                <label for="">Graduation Year</label>
                                <select name="graduation_year[]" class="select form-select form-select-sm w-100" multiple
                                    id="graduation-year">
                                    @for ($i = $advanced_filter['max_graduation_year']; $i >= 2016; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="card rounded">
        <div class="card-body">
            <ul class="nav nav-tabs flex-nowrap w-100 overflow-auto mb-3" style="overflow-y: hidden !important;">
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ Request::get('st') == 'mentee' ? 'active' : null }}"
                        href="{{ url('client/alumni?st=mentee') }}">Mentee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ Request::get('st') == 'non-mentee' ? 'active' : null }}"
                        href="{{ url('client/alumni?st=non-mentee') }}">Non Mentee</a>
                </li>
            </ul>
            <table class="table table-bordered table-hover nowrap align-middle w-100" id="clientTable">
                <thead class="bg-secondary text-white">
                    <tr class="text-center" role="row">
                        <th class="bg-info text-white">No</th>
                        <th class="bg-info text-white">Name</th>
                        <th>Parents Name</th>
                        <th>Parent Email</th>
                        <th>Parent Phone</th>
                        <th>School</th>
                        <th>Graduation Year</th>
                        <th class="bg-info text-white"># Action</th>
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

    {{-- Need Changing --}}
    <script>
        $('#cancel').click(function() {
            $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle')
        });

        var widthView = $(window).width();
        $(document).ready(function() {

            var options = {
                order: [
                    // [20, 'desc'],
                    [1, 'asc']
                ],
                buttons: [
                    'pageLength', {
                        extend: 'excel',
                        text: 'Export to Excel',
                    }
                ],
                fixedColumns: {
                    left: (widthView < 768) ? 1 : 2,
                    right: 1
                },
                ajax: {
                    url: '',
                    data: function(params){
                        params.school_name = $("#school-name").val()
                        params.graduation_year = $("#graduation-year").val()
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'full_name',
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        data: 'parent_name',
                        name: 'parent_name',
                        defaultContent: '-',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'parent_mail',
                        name: 'parent_mail',
                        defaultContent: '-'
                    },
                    {
                        data: 'parent_phone',
                        name: 'parent_phone',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'school_name',
                        name: 'school_name',
                        defaultContent: '-'
                    },

                    {
                        data: 'graduation_year',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: '',
                        className: 'text-center',
                        defaultContent: '<button type="button" class="btn btn-sm btn-outline-warning editClient" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="More Detail"><i class="bi bi-eye"></i></button>'
                    }
                ],
                createdRow: function(row, data, index) {
                    // temporary condition
                    // will change soon
                    if (data['st_statusact'] == 0) {
                        $('td', row).addClass('text-danger');
                        $('td:nth-last-child(1) .deleteUser', row).addClass('d-none');
                        // $('td:nth-last-child(2)', row).addClass('bg-danger rounded text-white my-2');
                    }

                    var newClientThisMonth = moment().format("MMM YY") == moment(data.created_at).format(
                        'MMM YY');

                    if (newClientThisMonth) {
                        $('td', row).addClass('table-success');
                    }
                }
            };

            var table = initializeDataTable('#clientTable', options, 'rt_client');

            @php
                $privilage = $menus['Client']->where('submenu_name', 'Alumnis')->first();
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

            $('#clientTable tbody').on('click', '.editClient ', function() {
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const type = urlParams.get('st');


                var data = table.row($(this).parents('tr')).data();
                window.open("{{ url('client/alumni/') }}/" + type + "/" + data.id, "_blank");
            });

            // Tooltip 
            $('#clientTable tbody').on('mouseover', 'tr', function() {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                    html: true
                });
            });

            /* for advanced filter */
            $("#school-name").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })

            $("#graduation-year").on('change', function(e) {
                var value = $(e.currentTarget).find("option:selected").val();
                table.draw();
            })
        });
    </script>
@endsection
