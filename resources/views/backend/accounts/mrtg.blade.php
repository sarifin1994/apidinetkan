@extends('backend.layouts.app')

@section('title', 'Account MRTG')

@section('main')

  <!-- Container-fluid starts -->
  <div class="container-fluid">
    <div class="container-fluid">
      <div class="page-title">
        <div class="row">
          <div class="col-sm-6 ps-0">
            <h3>MRTG</h3>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12 mb-4">
        <div class="mb-3">
          <button type="button" onclick="call_all()" class="btn btn-primary">Load Data</button>
          
        </div>
      </div>
      <!-- Account Information Card -->
      <div class="col-xl-12 mb-4">
        <div class="card rounded border-0 shadow-sm">
          <div class="card-body">
            <!-- <div id="loading-text" style="display:none" class="d-flex justify-content-center">
              <h3>Dont refresh data loading</h3>
            </div> -->
            @if($list_graph)
              @foreach($list_graph as $key=>$val)
              <!-- daily -->
              <div class="mb-3">
                <div class="card-header d-flex justify-content-center text-white">
                  <h5 class="d-flex align-items-center mb-0">
                    <label for="trafficChart{{$key}}">Daily {{ $val['data']->graph_name }}</label>
                  </h5>
                </div>
                <div class="d-flex justify-content-center"><canvas id="trafficChart{{$key}}" width="900px" height="400px"></canvas></div>
                <div id="subtitle{{$key}}"></div>
                <hr>
              </div>

              <!-- weekly -->
              <div class="mb-3">
                <div class="card-header d-flex justify-content-center text-white">
                  <h5 class="d-flex align-items-center mb-0">
                    <label for="trafficChartweek{{$key}}">Weekly {{ $val['data']->graph_name }}</label>
                  </h5>
                </div>
                <div class="d-flex justify-content-center"><canvas id="trafficChartweek{{$key}}" width="900px" height="400px"></canvas></div>
                <div id="subtitleweek{{$key}}"></div>
                <hr>
              </div>

              <!-- month -->
              <div class="mb-3">
                <div class="card-header d-flex justify-content-center text-white">
                  <h5 class="d-flex align-items-center mb-0">
                    <label for="trafficChartmonth{{$key}}">Monthly {{ $val['data']->graph_name }}</label>
                  </h5>
                </div>
                <div class="d-flex justify-content-center"><canvas id="trafficChartmonth{{$key}}" width="900px" height="400px"></canvas></div>
                <div id="subtitlemonth{{$key}}"></div>
                <hr>
              </div>

              <!-- year -->
              <div class="mb-3">
                <div class="card-header d-flex justify-content-center text-white">
                  <h5 class="d-flex align-items-center mb-0">
                    <label for="trafficChartyear{{$key}}">Yearly {{ $val['data']->graph_name }}</label>
                  </h5>
                </div>
                <div class="d-flex justify-content-center"><canvas id="trafficChartyear{{$key}}" width="900px" height="400px"></canvas></div>
                <div id="subtitleyear{{$key}}"></div>
                <hr>
              </div>
              @endforeach
            @endif
          </div>
        </div>
      </div>

      <!-- Usage Details Card -->
      
    </div>
  </div>
  <!-- Container-fluid Ends-->
@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script> <!-- Tambahkan ini -->
<script>
  $(document).ready(() => {
    loadData();
    loadData_week();
    loadData_month();
    loadData_year();
  });


  function loadData()
  {
    $.ajax({
        type: 'GET',
        url: `get_graph_json`,
        dataType: 'json',
        success: function(json) {
          // console.log(response);
          // const json = JSON.parse(response);
          json.forEach((row) => {
            updateChart(row);
          })
      }
    });
  }

  function updateChart(res)
  {
    var ctx= "";
    var subtitle = "";

    var canvas = document.getElementById('trafficChart'+res.id);
    var existingChart = Chart.getChart(canvas); // Get existing chart
    // Simpan ukuran sebelum destroy
    var width = canvas.width;
    var height = canvas.height;

    if (existingChart) {
        existingChart.destroy(); // Destroy the existing chart
    }

    // Set ulang ukuran canvas
    canvas.width = width;
    canvas.height = height;

    ctx = document.getElementById('trafficChart'+res.id).getContext('2d');
    subtitle = document.getElementById('subtitle'+res.id)
    var data ="";
    var datasets ="";
    var cleanData ="";
    var cleanDataSets ="";
    var labels ="";
    var datasetsx ="";
    data = res.labels //"{{$val['labels']}}";
    datasets = res.datasets //"{{$val['datasets']}}";
    legends = res.legends //"{{$val['datasets']}}";
    // Bersihkan string dari &quot; menjadi "
    cleanData = data.replace(/&quot;/g, '"'); 
    cleanDataSets = datasets.replace(/&quot;/g, '"'); 
    // cleanLegend = JSON.parse(`[${legends}]`);
    subtitle.innerHTML = '';
    legends.forEach((row) => {
      if(row == ''){
        subtitle.innerHTML += '<br>';
      }
      if(row != ''){
        subtitle.innerHTML += '<div class="d-flex justify-content-center"><label>'+row+'</label></div>';
        // console.log(subtitle);
      }
    })
    
    // Konversi ke array dan buat objek Date
    labels = JSON.parse(`[${cleanData}]`).map(date => new Date(date));
    datasetsx = JSON.parse(`[${cleanDataSets}]`);
    var chartData = {};
    chartData = {
        labels: labels, // Array berisi objek Date
        datasets: datasetsx[0]
    };

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
          responsive: true, // Membuat chart responsif terhadap lebar
          // maintainAspectRatio: false, // Izinkan tinggi tetap
            scales: {
                x: {
                    type: 'time',
                    time: { unit: 'minute' }
                },
                // y: { beginAtZero: true }
                y: {
                    display: true,
                    title: {
                      display: true,
                      text: res.yvalue
                    }
                  }
            }
        }
    });
  }


  // week
  function loadData_week()
  {
    $.ajax({
        type: 'GET',
        url: `week_get_graph_json`,
        dataType: 'json',
        success: function(json) {
          // console.log(response);
          // const json = JSON.parse(response);
          json.forEach((row) => {
            updateChart_week(row);
          })
      }
    });
  }

  function updateChart_week(res)
  {
    var ctx= "";
    var subtitle = "";

    var canvas = document.getElementById('trafficChartweek'+res.id);
    var existingChart = Chart.getChart(canvas); // Get existing chart
    // Simpan ukuran sebelum destroy
    var width = canvas.width;
    var height = canvas.height;

    if (existingChart) {
        existingChart.destroy(); // Destroy the existing chart
    }

    // Set ulang ukuran canvas
    canvas.width = width;
    canvas.height = height;
    ctx = document.getElementById('trafficChartweek'+res.id).getContext('2d');
    subtitle = document.getElementById('subtitleweek'+res.id)
    var data ="";
    var datasets ="";
    var cleanData ="";
    var cleanDataSets ="";
    var labels ="";
    var datasetsx ="";
    data = res.labels //"{{$val['labels']}}";
    datasets = res.datasets //"{{$val['datasets']}}";
    legends = res.legends //"{{$val['datasets']}}";
    // Bersihkan string dari &quot; menjadi "
    cleanData = data.replace(/&quot;/g, '"'); 
    cleanDataSets = datasets.replace(/&quot;/g, '"'); 
    // cleanLegend = JSON.parse(`[${legends}]`);
    subtitle.innerHTML = '';
    legends.forEach((row) => {
      if(row == ''){
        subtitle.innerHTML += '<br>';
      }
      if(row != ''){
        subtitle.innerHTML += '<div class="d-flex justify-content-center"><label>'+row+'</label></div>';
        // console.log(subtitle);
      }
    })
    
    // Konversi ke array dan buat objek Date
    labels = JSON.parse(`[${cleanData}]`).map(date => new Date(date));
    datasetsx = JSON.parse(`[${cleanDataSets}]`);
    var chartData = {};
    chartData = {
        labels: labels, // Array berisi objek Date
        datasets: datasetsx[0]
    };

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
          responsive: true, // Membuat chart responsif terhadap lebar
          // maintainAspectRatio: false, // Izinkan tinggi tetap
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day', // Mengelompokkan data berdasarkan hari
                        tooltipFormat: 'EEEE', // Format tooltip agar menampilkan nama hari
                        displayFormats: {
                            day: 'EEEE' // Format label sumbu X agar menampilkan nama hari
                        }
                    }
                },
                // y: { beginAtZero: true }
                y: {
                    display: true,
                    title: {
                      display: true,
                      text: res.yvalue
                    }
                  }
            }
        }
    });
  }
  // week


  // // month
  function loadData_month()
  {
    $.ajax({
        type: 'GET',
        url: `month_get_graph_json`,
        dataType: 'json',
        success: function(json) {
          // console.log(response);
          // const json = JSON.parse(response);
          json.forEach((row) => {
            updateChart_month(row);
          })
      }
    });
  }

  function updateChart_month(res)
  {
    var ctx= "";
    var subtitle = "";

    var canvas = document.getElementById('trafficChartmonth'+res.id);
    var existingChart = Chart.getChart(canvas); // Get existing chart
    // Simpan ukuran sebelum destroy
    var width = canvas.width;
    var height = canvas.height;

    if (existingChart) {
        existingChart.destroy(); // Destroy the existing chart
    }

    // Set ulang ukuran canvas
    canvas.width = width;
    canvas.height = height;
    ctx = document.getElementById('trafficChartmonth'+res.id).getContext('2d');
    subtitle = document.getElementById('subtitlemonth'+res.id)
    var data ="";
    var datasets ="";
    var cleanData ="";
    var cleanDataSets ="";
    var labels ="";
    var datasetsx ="";
    data = res.labels //"{{$val['labels']}}";
    datasets = res.datasets //"{{$val['datasets']}}";
    legends = res.legends //"{{$val['datasets']}}";
    // Bersihkan string dari &quot; menjadi "
    cleanData = data.replace(/&quot;/g, '"'); 
    cleanDataSets = datasets.replace(/&quot;/g, '"'); 
    // cleanLegend = JSON.parse(`[${legends}]`);
    subtitle.innerHTML = '';
    legends.forEach((row) => {
      if(row == ''){
        subtitle.innerHTML += '<br>';
      }
      if(row != ''){
        subtitle.innerHTML += '<div class="d-flex justify-content-center"><label>'+row+'</label></div>';
        // console.log(subtitle);
      }
    })
    
    // Konversi ke array dan buat objek Date
    labels = JSON.parse(`[${cleanData}]`).map(date => new Date(date));
    datasetsx = JSON.parse(`[${cleanDataSets}]`);
    var chartData = {};
    chartData = {
        labels: labels, // Array berisi objek Date
        datasets: datasetsx[0]
    };

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
          responsive: true, // Membuat chart responsif terhadap lebar
          // maintainAspectRatio: false, // Izinkan tinggi tetap
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'week', // Menampilkan data per minggu
                        // tooltipFormat: "'Week' w", // Menampilkan Week 1, Week 2, dst. dalam tooltip
                        displayFormats: {
                            week: "'Week' w", // Format label untuk menampilkan "Week 1", "Week 2", dst.
                            // day: 'EEEE' // Menampilkan nama hari
                        }
                    }
                },
                // y: { beginAtZero: true }
                y: {
                    display: true,
                    title: {
                      display: true,
                      text: res.yvalue
                    }
                  }
            }
        }
    });
  }
  // // month


  // // year
  function loadData_year()
  {
    $.ajax({
        type: 'GET',
        url: `year_get_graph_json`,
        dataType: 'json',
        success: function(json) {
          // console.log(response);
          // const json = JSON.parse(response);
          json.forEach((row) => {
            updateChart_year(row);
          })
      }
    });
  }

  function updateChart_year(res)
  {
    var ctx= "";
    var subtitle = "";

    var canvas = document.getElementById('trafficChartyear'+res.id);
    var existingChart = Chart.getChart(canvas); // Get existing chart
    // Simpan ukuran sebelum destroy
    var width = canvas.width;
    var height = canvas.height;

    if (existingChart) {
        existingChart.destroy(); // Destroy the existing chart
    }

    // Set ulang ukuran canvas
    canvas.width = width;
    canvas.height = height;
    ctx = document.getElementById('trafficChartyear'+res.id).getContext('2d');
    subtitle = document.getElementById('subtitleyear'+res.id)
    var data ="";
    var datasets ="";
    var cleanData ="";
    var cleanDataSets ="";
    var labels ="";
    var datasetsx ="";
    data = res.labels //"{{$val['labels']}}";
    datasets = res.datasets //"{{$val['datasets']}}";
    legends = res.legends //"{{$val['datasets']}}";
    // Bersihkan string dari &quot; menjadi "
    cleanData = data.replace(/&quot;/g, '"'); 
    cleanDataSets = datasets.replace(/&quot;/g, '"'); 
    // cleanLegend = JSON.parse(`[${legends}]`);
    subtitle.innerHTML = '';
    legends.forEach((row) => {
      if(row == ''){
        subtitle.innerHTML += '<br>';
      }
      if(row != ''){
        subtitle.innerHTML += '<div class="d-flex justify-content-center"><label>'+row+'</label></div>';
        // console.log(subtitle);
      }
    })
    
    // Konversi ke array dan buat objek Date
    labels = JSON.parse(`[${cleanData}]`).map(date => new Date(date));
    datasetsx = JSON.parse(`[${cleanDataSets}]`);
    var chartData = {};
    chartData = {
        labels: labels, // Array berisi objek Date
        datasets: datasetsx[0]
    };

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
          responsive: true, // Membuat chart responsif terhadap lebar
          // maintainAspectRatio: false, // Izinkan tinggi tetap
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'month', // Menampilkan data per bulan
                        tooltipFormat: 'MMMM yyyy', // Format tooltip agar menampilkan nama bulan dan tahun
                        displayFormats: {
                            month: 'MMMM' // Format label untuk menampilkan nama bulan (January, February, dst.)
                        }
                      }
                },
                // y: { beginAtZero: true }
                y: {
                    display: true,
                    title: {
                      display: true,
                      text: res.yvalue
                    }
                  }
            }
        }
    });
  }
  // // year

  function call_all(){
    // document.getElementById("loading-text").style.display = "block";
    loadData();
    loadData_week();
    loadData_month();
    loadData_year();
    // document.getElementById("loading-text").style.display = "none";
  }

  // setInterval(loadData,300000)
  
  // setInterval(loadData,60000);
  // setInterval(loadData_week,60000);
  // setInterval(loadData_month,60000);
  // setInterval(loadData_year,60000);

  setInterval(call_all,300000);
  // setInterval(loadData,300000);
  // setInterval(loadData_week,300000);
  // setInterval(loadData_month,300000);
  // setInterval(loadData_year,300000);
  
</script>
@endpush
