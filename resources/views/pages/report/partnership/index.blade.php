@extends('layout.main')

@section('title', 'Partnership Report - Bigdata Platform')

@section('content')

    <div class="row">
        <div class="col-md-3 mb-2">
            <div class="position-sticky" style="top:15%;">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="p-0 m-0">Period</h6>
                    </div>
                    <div class="card-body">
                        <form action="">
                            <div class="mb-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id=""
                                    class="form-control form-control-sm rounded"
                                    value="{{ Request::get('start_date') ? Request::get('start_date') : null }}">
                            </div>
                            <div class="mb-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" id=""
                                    class="form-control form-control-sm rounded"
                                    value="{{ Request::get('end_date') ? Request::get('end_date') : null }}">
                            </div>
                            <div class="text-center">
                                <button class="btn btn-sm btn-outline-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Partnership Detail  --}}
                @include('pages.report.partnership.component.detail')
            </div>
        </div>


        <div class="col-md-9">
            <div class="text-end">
                <button class="btn btn-sm btn-outline-info" onclick="ExportToExcel()" style="margin-bottom: 15px">
                    <i class="bi bi-file-earmark-excel me-1"></i> Print
                </button>
            </div>

            {{-- School Program  --}}
            @include('pages.report.partnership.component.school-program')

            {{-- Partner Program  --}}
            @include('pages.report.partnership.component.partner-program')

            {{-- Referral in  --}}
            @include('pages.report.partnership.component.referral-in')

            {{-- Referral out  --}}
            @include('pages.report.partnership.component.referral-out')

            {{-- School visit --}}
            @include('pages.report.partnership.component.school-visit')

            {{-- New School  --}}
            @include('pages.report.partnership.component.new-school')

            {{-- New Partner  --}}
            @include('pages.report.partnership.component.new-partner')

            {{-- New University  --}}
            @include('pages.report.partnership.component.new-univ')

        </div>
    </div>

    <script>
        function showHideDetail(d) {
            if ($('#' + d).hasClass('d-none')) {
                $('#' + d).removeClass('d-none')
            } else {
                $('#' + d).addClass('d-none')
            }
        }
    </script>

    <script>
        @php
            $privilage = $menus['Report']->where('submenu_name', 'Partnership')->first();
        @endphp
        $(document).ready(function() {
            @if ($privilage['copy'] == 0)
                document.oncontextmenu = new Function("return false");

                $('body').bind('cut copy paste', function(event) {
                    event.preventDefault();
                });
            @endif
        });

        function ExportToExcel() {

            var sheetName = ['School Programs', 'Partner Programs', 'Referral In', 'Referral Out', 'School Visits',
                'New School', 'New Partner', 'New University'
            ];

            var tableName = ['tblsch_prog', 'tblpartner_prog', 'tbl_ref_in', 'tbl_ref_out', 'tbl_schvisit', 'tbl_newsch',
                'tbl_newpartner', 'tbl_newuniv'
            ];

            var ws = new Array();

            var workbook = XLSX.utils.book_new();
            tableName.forEach(function(d, i) {
                ws[i] = XLSX.utils.table_to_sheet(document.getElementById(tableName[i]));
                XLSX.utils.book_append_sheet(workbook, ws[i], sheetName[i]);
            })

            var sheetNamePrograms = ['School Programs', 'Partner Programs', 'Referral In', 'Referral Out'];


            sheetNamePrograms.forEach(function(d, i) {

                var sheet = d;
                var full_ref = workbook.Sheets[sheet]['!fullref'];
                var last_ref = full_ref.slice(full_ref.indexOf(':') + 1);


                if (sheet == 'School Programs' || sheet == 'Partner Programs') {
                    var last_col = parseInt(last_ref.slice(last_ref.indexOf('G') + 1)) - 1;

                    if (last_col > 2) {
                        for (var j = 2; j <= last_col; j++) {

                            var index = 'F' + j;
                            var format_cell = 'Rp#,##0;(Rp#,##0)';

                            workbook.Sheets[sheet][index].v = parseInt(workbook.Sheets[sheet][index].v.replace(
                                'Rp.', "").replaceAll(",", ""));
                            workbook.Sheets[sheet][index].t = 'n';
                            workbook.Sheets[sheet][index].z = format_cell;
                        }

                        workbook.Sheets[sheet]['F' + j] = {
                            t: 'n',
                            z: format_cell,
                            f: `SUM(${'F'+2}:` + index + ")",
                            F: 'F' + j + ":" + 'F' + j
                        }
                    }
                } else {
                    var col = ['E', 'F', 'G', 'H'];
                    var last_col = parseInt(last_ref.slice(last_ref.indexOf('J') + 1)) - 1;

                    col.forEach(function(d, i) {
                        if (last_col > 2) {
                            for (var i = 2; i <= last_col; i++) {
                                var index = d + i;

                                var format_cell;
                                var remove_cursymbol;
                                switch (d) {
                                    case 'E':
                                        remove_cursymbol = 'Rp.';
                                        format_cell = 'Rp#,##0;(Rp#,##0)';
                                        break;
                                    case 'F':
                                        remove_cursymbol = '$.';
                                        format_cell = '$#,##0;($#,##0)';
                                        break;
                                    case 'G':
                                        remove_cursymbol = 'S$.';
                                        format_cell = '$#,##0;($#,##0)';
                                        break;
                                    case 'H':
                                        remove_cursymbol = '£.';
                                        format_cell = '£#,##0;(£#,##0)';
                                        break;
                                }
                                if (workbook.Sheets[sheet][index].v != '-')
                                    workbook.Sheets[sheet][index].v = parseInt(workbook.Sheets[sheet][index]
                                        .v.replace(remove_cursymbol, "").replaceAll(",", ""));

                                workbook.Sheets[sheet][index].t = 'n';
                                workbook.Sheets[sheet][index].z = format_cell;
                            }
                            workbook.Sheets[sheet][d + i] = {
                                t: 'n',
                                z: format_cell,
                                f: `SUM(${d+2}:` + index + ")",
                                F: d + i + ":" + d + i
                            }
                        }
                    })
                }

            })

            XLSX.writeFile(workbook, "report-partnership.xlsx");

        }
    </script>
@endsection
