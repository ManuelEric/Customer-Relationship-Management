<div class="row align-items-stretch">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <b class="text-center border-1">Lead Source</b>
                        <hr>
                        <ul class="list-group list-group-flush overflow-auto pe-3" style="height: 300px">
                            @for ($i = 0; $i < 20; $i++)
                                <li
                                    class="d-flex align-items-center justify-content-between pb-1 mb-1 border-bottom border-secondary">
                                    <small>
                                        Lead Source
                                    </small>
                                    <div class="d-flex justify-content-center align-items-center rounded-circle border border-primary"
                                        style="width: 25px; height:25px;">
                                        34
                                    </div>
                                </li>
                            @endfor
                        </ul>
                    </div>
                    <div class="col-md-6 text-center">
                      <b class="text-center border-1">Conversion Lead</b>
                      <hr>
                      <ul class="list-group list-group-flush overflow-auto pe-3" style="height: 300px">
                          @for ($i = 0; $i < 20; $i++)
                              <li
                                  class="d-flex align-items-center justify-content-between pb-1 mb-1 border-bottom border-secondary">
                                  <small>
                                      Conversion Lead
                                  </small>
                                  <div class="d-flex justify-content-center align-items-center rounded-circle border border-primary"
                                      style="width: 25px; height:25px;">
                                      34
                                  </div>
                              </li>
                          @endfor
                      </ul>
                  </div>
                </div>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body overflow-auto" style="height: 340px">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th style="font-size: 13px">Full Name</th>
                            <th style="font-size: 13px">Lead Source</th>
                            <th style="font-size: 13px">Conversion Lead</th>
                            <th style="font-size: 13px">Program Name</th>
                            <th style="font-size: 13px">Conversion Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="card-footer d-flex justify-between align-items-center w-100">
                <div class="w-50">
                    <small>Average of Follow Up</small>
                    <h5><i class="bi bi-calendar me-1"></i> 4 Days</h5>
                </div>
                <div class="w-50 text-end">
                    <small>Average of Conversion</small>
                    <h5><i class="bi bi-calendar me-1"></i> 5 Days</h5>
                </div>
            </div>
        </div>
        {{-- <div class="card">
            <div class="card-body">
                <canvas id="digitalLeadSource"></canvas>
            </div>
        </div> --}}
    </div>
</div>

<script>
    const digitalLeadSource = document.getElementById('digitalLeadSource');

    new Chart(digitalLeadSource, {
        type: 'bar',
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
