@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function() {
            var incoming_start_date = null
            var start_date = incoming_start_date != null ? moment(incoming_start_date).format('L') : moment().startOf('month')
            var incoming_end_date = null
            var end_date = incoming_end_date != null ? moment(incoming_end_date).format('L') : moment().endOf('month')

            $('input[name="daterange"]').daterangepicker({
                startDate: start_date,
                endDate: end_date,
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end
                    .format('YYYY-MM-DD'));
            });
        });
    </script>
@endpush

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Filter
                        </h5>
                    </div>
                    <div class="card-body">
                            <div class="mb-3">
                                <label for="daterange">Date Range</label>
                                <input type="text" name="daterange" id="daterange" value=""
                                    class="form-control form-control-sm text-center" id="daterange" />
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-sm btn-outline-primary btn-daterange-domicile">
                                    <i class="bi bi-search me-1"></i>
                                    Submit
                                </button>
                            </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body overflow-auto" style="height: 450px">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="text-center">
                                    <tr class="text-white">
                                        <th class="bg-secondary rounded border-1 border-white">#</th>
                                        <th class="bg-secondary rounded border-1 border-white">Domicile</th>
                                        <th class="bg-secondary rounded border-1 border-white">Program</th>
                                        <th class="bg-secondary rounded border-1 border-white">Count of program</th>
                                    </tr>
                                </thead>
                                <tbody class="overflow-auto" style="max-height: 400px;" id="data-domicile">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>
</div>

@push('scripts')
    <script type="text/javascript" async>

        function get_domicile(uuid, daterange) {

            var url = window.location.origin + '/api/v1/dashboard/domicile'
    
            axios.get(url, {
                    params: {
                        date_range: daterange,
                        uuid: uuid,
                    }
                })
                .then(function(response) {
    
                    let html = null
                    const obj = response.data.data                    
                    
                    var no = 1;
                    Object.keys(obj).forEach(function(key) {
                        

                        html += "<tr>" +
                            "<td class='text-center'>" + no++ + "</td>" +
                            "<td>" + obj[key]['domicile'] + "</td>" +
                            "<td>" + obj[key]['main_prog'] + "</td>" +
                            "<td>" + obj[key]['count'] + "</td>" +
                            "</tr>"

                    });
    
                    $("#data-domicile").html(html)
                    swal.close()
                }).catch(function(error) {
                    swal.close()
                    notification('error', error.message);
                })
        }

        $(".btn-daterange-domicile").on('click', function() {
            var uuid = $("#cp_employee").val();
            var daterange = $('#daterange').val();
            showLoading()
            get_domicile(uuid, daterange);
        })

        window.onload = function() {
            var uuid = $("#cp_employee").val();
            var daterange = $('#daterange').val();
            get_domicile(uuid, daterange);
        };
    </script>
@endpush