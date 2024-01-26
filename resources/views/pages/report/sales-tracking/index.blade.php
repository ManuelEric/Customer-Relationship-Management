@extends('layout.main')

@section('title', 'Sales Tracking')

@section('content')
    @if (Request::get('start') && Request::get('end'))
        <div class="row">
            <div class="col">
                <div class="alert alert-success">
                    Sales tracking report between <u>{{ date('F, dS Y', strtotime(Request::get('start'))) }}</u> and
                    <u>{{ date('F, dS Y', strtotime(Request::get('end'))) }}</u>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-primary" role="alert">
            Report period between <u>{{ date('F, dS Y', strtotime($dateDetails['startDate'])) }}</u> and <u>{{ date('F, dS Y', strtotime($dateDetails['endDate'])) }}</u>
        </div>
    @endif


    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3 position-sticky" style="top:15%;">
                <form action="" id="filterForm">
                    <div class="card-header">
                        <h6 class="p-0 m-0">Advanced Filter</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label>Start Date</label>
                            <input type="date" name="start" class="form-control form-control-sm rounded"
                                value="{{ Request::get('start') ?? $dateDetails['startDate'] }}">
                        </div>
                        <div class="mb-2">
                            <label>End Date</label>
                            <input type="date" name="end" class="form-control form-control-sm rounded"
                                value="{{ Request::get('end') ?? $dateDetails['endDate'] }}">
                        </div>
                        <div class="mb-2">
                            <label>Main Program</label>
                            <select name="main" id="main_prog" class="select w-100">
                                <option value=""></option>
                                @foreach ($mainPrograms as $mainProgram)
                                    <option value="{{ $mainProgram->id }}">{{ $mainProgram->prog_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2 d-none prog-name-cont">
                            <label>Program Name</label>
                            <select name="program" id="prog_name" class="select w-100">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>PIC</label>
                            <select name="pic" id="pic_name" class="select w-100">
                                <option value=""></option>
                                @foreach ($pics as $pic)
                                    <option value="{{ $pic->uuid }}" @selected($pic->uuid == Request::get('pic'))>{{ $pic->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-9">
            {{-- Program Status  --}}
            @include('pages.report.sales-tracking.component.program-status')

            {{-- Client Program  --}}
            @include('pages.report.sales-tracking.component.client-program')

            {{-- Leads Number  --}}
            {{-- @include('pages.report.sales-tracking.component.hot-leads-number') --}}

            {{-- Sales Target  --}}
            {{-- @include('pages.report.sales-tracking.component.sales-target') --}}

            {{-- Initial Assessment Progress  --}}
            @include('pages.report.sales-tracking.component.initial-assessment')

            {{-- Conversion Lead  --}}
            @include('pages.report.sales-tracking.component.conversion-lead')

            {{-- Average Conversion Time  --}}
            @include('pages.report.sales-tracking.component.average-conversion')
        </div>
    </div>


    {{-- Modal for Lead Detail  --}}
    <div class="modal modal-lg fade" tabindex="-1" id="leadModalDetail">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body overflow-auto" style="max-height: 400px">
                    <table class="table table-striped">
                    </table>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        @if (Request::get('main'))
            $("#main_prog").select2().val("{{ Request::get('main') }}").trigger('change')

        @endif
    })


    $("#main_prog").on('change', function() {
        showLoading();

        var mainProgId = $(this).val();
        if (mainProgId == "") {
            
            $(".prog-name-cont").addClass('d-none');
            $("#prog_name").html('<option></option>');
            swal.close();
            return;
        }
        

        var baseUrl = "{{ url('/') }}/api/get/program/main/" + mainProgId;

        axios.get(baseUrl)
            .then(function (response) {
                let obj = response.data;
                var html = ""

                let selectedProgram = "{{ Request::get('program') }}"
                
                for (var key in obj.data) {
                    var selected = "";
                    if (selectedProgram && obj.data[key].prog_id == selectedProgram) 
                        selected = "selected";
                    
                    
                    html += "<option value='" + obj.data[key].prog_id + "' " + selected +">" + obj.data[key].prog_program + "</option>"
                    
                        

                }

                $("#prog_name").html(html)
                $(".prog-name-cont").removeClass('d-none');
                swal.close();

            })
            .catch(function (error) {
                console.log(error)
            })
    });
</script>
<script>
    @php
        $privilage = $menus['Report']->where('submenu_name', 'Sales Tracking')->first();
    @endphp
    $(document).ready(function() {
        @if ($privilage['copy'] == 0)
            document.oncontextmenu = new Function("return false");

            $('body').bind('cut copy paste', function(event) {
                event.preventDefault();
            });
        @endif
    });

    $(document).on('click', '.lead-source-item', function() {
        var _this = $(this);
        const requestParam = getParam(_this);
        var url = '{{ url('/api/v1/get/detail/lead-source') }}';
        requestParam['url'] = url;

        showDetailLead(requestParam)
    })

    $(document).on('click', '.conversion-lead-item', function() {
        var _this = $(this);
        const requestParam = getParam(_this);
        console.log(requestParam)
        var url = '{{ url('/api/v1/get/detail/conversion-lead') }}';
        requestParam['url'] = url;

        showDetailLead(requestParam)
    })

    function showDetailLead(param) {
        showLoading();

        axios.get(param['url'], {
                params: param
            })
            .then(function(response) {

                const obj = response.data.data;

                swal.close();
                $("#leadModalDetail").modal('show');
                $("#leadModalDetail .modal-title").html(obj.title)

                $("#leadModalDetail table").html(obj.context);

            })
            .catch(function(error) {
                // handle error
                Swal.close()
                notification(error.response.data.success, error.response.data.message)
            })
    }

    function getParam(_this) {
        var leadId = _this.data('lead');
        var leadName = _this.data('leadname');
        var startDate = _this.data('sdate');
        var endDate = _this.data('edate');
        var subLead = _this.data('sublead');
        
        // added
        var url = "{{ url('/') }}"
        const urlParams = new URLSearchParams(url);
        var mainProgId = urlParams.get('main');
        var progId = urlParams.get('program');
        var picUUID = urlParams.get('pic')

        return {
            leadId: leadId,
            leadName: leadName,
            startDate: startDate,
            endDate: endDate,
            subLead: subLead,
            //
            mainProgId: mainProgId,
            progId: progId,
            picUUID: picUUID
        }

    }
</script>
@endpush
