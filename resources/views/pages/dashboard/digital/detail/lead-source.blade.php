<div class="row align-items-stretch">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                Lead Source
            </div>
            <div class="card-body overflow-auto" style="max-height: 300px">
                <ul class="list-group list-group-flush" >
                    @for ($i = 0; $i < 20; $i++)
                    <li class="d-flex align-items-center justify-content-between pb-1 mb-1 border-bottom border-secondary">
                        <small>
                            Lead Source
                        </small>
                        <div class="d-flex justify-content-center align-items-center rounded-circle border border-primary" style="width: 25px; height:25px;">
                            34
                        </div>
                    </li>
                    @endfor
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <canvas id="digitalLeadSource"></canvas>
            </div>
        </div>
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