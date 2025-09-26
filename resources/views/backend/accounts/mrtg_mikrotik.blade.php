@extends('backend.layouts.app_new')

@section('title', 'Account MRTG')

@section('main')

  <!-- Container-fluid starts -->
  <div class="container-fluid">
    <h3>MRTG</h3>
    <div class="row accordions-rtl">
      <!-- <div class="col-xl-12 mb-4">
        <div class="mb-3">
          <button type="button" onclick="call_all()" class="btn btn-primary">Load Data</button>
          
        </div>
      </div> -->
      <!-- Account Information Card -->
      <div class="col-md-12">
        <div class="card equal-card">
          <div class="card-header code-header">
            <h5> Daftar Service </h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <button type="button" onclick="call_all()" class="btn btn-primary">Load Data</button>
            </div>
            <div class="accordion app-accordion accordion-light-primary app-accordion-icon-left app-accordion-plus" id="accordionlefticonExample">
              @foreach($services as $service)
                @if($service->service_detail->graph_type == 'cacti')
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse"
                      data-bs-target="#lefticon-collapseOne{{$service->id}}" aria-expanded="true"
                      aria-controls="lefticon-collapseOne{{$service->id}}">
                      {{$service->service_id}} - {{$service->service->name}}
                      </button>
                    </h2>
                    <div id="lefticon-collapseOne{{$service->id}}" class="accordion-collapse collapse show">
                      <div class="accordion-body">
                        <!-- Line Chart start -->
                        @foreach($graph as $row)
                        @if($row->service_id == $service->service_id && isset($row->id))
                          <!-- daily -->
                          <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                  <h5>{{$row->graph_name}}</h5>
                                </div>
                                <div class="card-body">
                                  <canvas id="trafficChartdaily{{$row->id}}"></canvas>
                                </div>
                                <div class="card-footer">
                                  <div id="subtitledaily{{$row->id}}"></div>
                                </div>

                                
                                <div class="card-body">
                                  <canvas id="trafficChartweekly{{$row->id}}"></canvas>
                                </div>
                                <div class="card-footer">
                                  <div id="subtitleweekly{{$row->id}}"></div>
                                </div>

                                                                
                                <div class="card-body">
                                  <canvas id="trafficChartmonthly{{$row->id}}"></canvas>
                                </div>
                                <div class="card-footer">
                                  <div id="subtitlemonthly{{$row->id}}"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach

                      </div>
                    </div>
                  </div>
                @endif
                @if($service->service_detail->graph_type == 'mikrotik')
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#lefticon-collapseOne{{$service->id}}" aria-expanded="true"
                    aria-controls="lefticon-collapseOne{{$service->id}}">
                    {{$service->service_id}} - {{$service->service->name}}
                    </button>
                  </h2>
                  <div id="lefticon-collapseOne{{$service->id}}" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                      <!-- Line Chart start -->
                      <div class="col-lg-12">
                          <div class="card">
                              <div class="card-header">
                                  <h5 id="vlan_name{{$service->service_id}}"></h5>
                              </div>
                              <div class="card-body">
                                  <canvas id="myChart9{{$service->service_id}}"></canvas>
                              </div>
                              <hr>
                              <div class="card-body">
                                  <canvas id="myChart9week{{$service->service_id}}"></canvas>
                              </div>
                              <hr>
                              <div class="card-body">
                                  <canvas id="myChart9month{{$service->service_id}}"></canvas>
                              </div>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endif
                
                @if($service->service_detail->graph_type == 'libre')
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse"
                      data-bs-target="#lefticon-collapseOne{{$service->id}}" aria-expanded="true"
                      aria-controls="lefticon-collapseOne{{$service->id}}">
                      {{$service->service_id}} - {{$service->service->name}}
                      </button>
                    </h2>
                    <div id="lefticon-collapseOne{{$service->id}}" class="accordion-collapse collapse show">
                      <div class="accordion-body">
                        @foreach($service->service_libre as $libre)
                        <div class="mb-3">
                          <h5>{{$libre->hostname}} - {{$libre->ifName}}</h5>
                          <img src="{{ url('/admin/account/mrtg/get_ifname_image/' . $libre->hostname . '/' . rawurlencode($libre->ifName)) }}"
                            alt="Grafik Port"
                            style="max-width: 100%; height: auto; display: block;">
                        </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
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
  call_all();
  function call_all(){
    Swal.fire({
        title: 'Please wait...',
        text: 'Processing Data ....',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    // Penampung global semua chart instance
    if (!window.myCharts) {
        window.myCharts = {};
    }
    setTimeout(function() {
      call_all_repeat();
      swal.close();
      },3000);
  }
  
  const delay = 1*60*1000; //3 menit
  setTimeout(function() {
    call_all_repeat();
  },delay);

  function call_all_repeat(){
    loadData_daily();
    loadData_weekly();
    loadData_monthly(); 
    loadData();
  }

  function loadData_daily() {
    @foreach($services as $sr)
      @if($sr->service_detail->graph_type == 'mikrotik')
      $.ajax({
          type: 'GET',
          url: `{{ route('admin.account.mrtg.graph_json_mikrotik',($sr->service_id ?? 0)) }}`,
          dataType: 'json',
          success: function(json) {
          const data = json.rx;
          const data2 = json.tx;
          const previousY = json.from[0];
          const delayBetweenPoints = json.delayBetweenPoints;
          const chart2 = document.getElementById('myChart9' + json.service_id);
          let vlan_name = document.getElementById('vlan_name' + json.service_id);
          vlan_name.innerText = json.vlan_name;
          
          // Jika sudah ada chart lama untuk service ini, destroy dulu
          const serviceId = "daily"+json.service_id;
          if (window.myCharts[serviceId]) {
              window.myCharts[serviceId].destroy();
          }
          // Create new chart
          window.myCharts[serviceId] = new Chart(chart2, {
              type: 'line',
              data: {
                  datasets: [
                    {
                      label: 'RX Bits PerSecond',
                      borderColor: hexToRGB(getLocalStorageItem('color-danger','#E14E5A'),1),
                      borderWidth: 1,
                      radius: 0,
                      data: data,
                  },
                  {
                      label: 'TX Bits PerSecond',
                      borderColor: hexToRGB(getLocalStorageItem('color-primary','#0F626A'),1),
                      borderWidth: 1,
                      radius: 0,
                      data: data2,
                  }
                ]
              },
              options: {
                  animation:{
                      x: {
                          type: 'number',
                          easing: 'linear',
                          duration: delayBetweenPoints,
                          from: NaN,
                      },
                      y: {
                          type: 'number',
                          easing: 'linear',
                          duration: delayBetweenPoints,
                          from: previousY,
                      }
                  },
                  interaction: {
                      intersect: false
                  },
                  plugins: {
                      legend: true
                  },
                  scales: {
                    x: {
                        type: 'time',
                        time: {
                            parser: 'yyyy-MM-dd HH:mm',
                            tooltipFormat: 'yyyy-MM-dd HH:mm',
                            unit: 'hour'
                        },
                        title: {
                            display: true,
                            text: 'Daily'
                        }
                    }
                }

              }
          });
        }
      });
      @endif
    @endforeach
  }

  function loadData_weekly() {
    let myChartInstance;
    @foreach($services as $sr)
      @if($sr->service_detail->graph_type == 'mikrotik')
        $.ajax({
            type: 'GET',
            url: `{{ route('admin.account.mrtg.graph_json_mikrotik_weekly',($sr->service_id ?? 0)) }}`,
            dataType: 'json',
            success: function(json) {
            const data = json.rx;
            const data2 = json.tx;
            const previousY = json.from[0];
            const delayBetweenPoints = json.delayBetweenPoints;
            const chart2week = document.getElementById('myChart9week' + json.service_id);
            // let vlan_name = document.getElementById('vlan_name' + json.service_id);
            
            // Jika sudah ada chart lama untuk service ini, destroy dulu
            const serviceId = "weekly"+json.service_id;
            if (window.myCharts[serviceId]) {
                window.myCharts[serviceId].destroy();
            }
            // Create new chart
            window.myCharts[serviceId] = new Chart(chart2week, {
                type: 'line',
                data: {
                    datasets: [
                      {
                        label: 'RX Bits PerSecond',
                        borderColor: hexToRGB(getLocalStorageItem('color-danger','#E14E5A'),1),
                        borderWidth: 1,
                        radius: 0,
                        data: data,
                    },
                    {
                        label: 'TX Bits PerSecond',
                        borderColor: hexToRGB(getLocalStorageItem('color-primary','#0F626A'),1),
                        borderWidth: 1,
                        radius: 0,
                        data: data2,
                    }
                  ]
                },
                options: {
                    animation:{
                        x: {
                            type: 'number',
                            easing: 'linear',
                            duration: delayBetweenPoints,
                            from: NaN,
                        },
                        y: {
                            type: 'number',
                            easing: 'linear',
                            duration: delayBetweenPoints,
                            from: previousY,
                        }
                    },
                    interaction: {
                        intersect: false
                    },
                    plugins: {
                        legend: true
                    },
                    scales: {
                      x: {
                          type: 'time',
                          time: {
                              parser: 'yyyy-MM-dd HH:mm',
                              tooltipFormat: 'EEE',
                              unit: 'day',
                              stepSize: 2,
                              displayFormats: {
                                day: 'yyyy-MM-dd'
                              }

                          },
                          title: {
                              display: true,
                              text: 'Weekly'
                          },
                          ticks: {
                            autoSkip: false, // 🟢 (optional) supaya tidak skip label
                            maxRotation: 45, // 🟢 sudut label
                            minRotation: 45
                          }
                      }
                  }

                }
            });
          }
        });
      @endif
    @endforeach
  }

  

  function loadData_monthly() {
    let myChartInstancemonth;
    @foreach($services as $sr)
      @if($sr->service_detail->graph_type == 'mikrotik')
        $.ajax({
            type: 'GET',
            url: `{{ route('admin.account.mrtg.graph_json_mikrotik_monthly',($sr->service_id ?? 0)) }}`,
            dataType: 'json',
            success: function(json) {
            const data = json.rx;
            const data2 = json.tx;
            const previousY = json.from[0];
            const delayBetweenPoints = json.delayBetweenPoints;
            const chart2month = document.getElementById('myChart9month' + json.service_id);
            
            // Jika sudah ada chart lama untuk service ini, destroy dulu
            const serviceId = "monthly"+json.service_id;
            if (window.myCharts[serviceId]) {
                window.myCharts[serviceId].destroy();
            }
            // Create new chart
            window.myCharts[serviceId] = new Chart(chart2month, {
                type: 'line',
                data: {
                    datasets: [
                      {
                        label: 'RX Bits PerSecond',
                        borderColor: hexToRGB(getLocalStorageItem('color-danger','#E14E5A'),1),
                        borderWidth: 1,
                        radius: 0,
                        data: data,
                    },
                    {
                        label: 'TX Bits PerSecond',
                        borderColor: hexToRGB(getLocalStorageItem('color-primary','#0F626A'),1),
                        borderWidth: 1,
                        radius: 0,
                        data: data2,
                    }
                  ]
                },
                options: {
                    animation:{
                        x: {
                            type: 'number',
                            easing: 'linear',
                            duration: delayBetweenPoints,
                            from: NaN,
                        },
                        y: {
                            type: 'number',
                            easing: 'linear',
                            duration: delayBetweenPoints,
                            from: previousY,
                        }
                    },
                    interaction: {
                        intersect: false
                    },
                    plugins: {
                        legend: true
                    },
                    scales: {
                      x: {
                          type: 'time',
                          time: {
                              parser: 'yyyy-MM-dd HH:mm',
                              tooltipFormat: 'yyyy-MM-dd HH:mm',
                              unit: 'week',
                              displayFormats: {
                                week: "'Week' w" // Ini kadang hanya menampilkan nomor minggu saja
                              }

                          },
                          title: {
                              display: true,
                              text: 'Monthly'
                          },
                          ticks: {
                            autoSkip: false, // 🟢 (optional) supaya tidak skip label
                            maxRotation: 45, // 🟢 sudut label
                            minRotation: 45
                          }
                      }
                  }

                }
            });
          }
        });
      @endif
    @endforeach
  }
</script>

<!-- untuk cacti -->
<script>
  loadData();
  function loadData()
  {
    @foreach($services as $service)
      @if($service->service_detail->graph_type == 'cacti')
        @foreach($graph as $row)
          @if($row->service_id == $service->service_id && isset($row->id))
            $.ajax({
                type: 'GET',
                url: `get_graph_json/` + {{$row->id ?? 0}},
                dataType: 'json',
                success: function(json) {
                  // console.log(response);
                  // const json = JSON.parse(response);
                  json.forEach((row) => {
                    updateChart(row, '{{$row->id}}');
                  })
              }
            });

            
            $.ajax({
                type: 'GET',
                url: `week_get_graph_json/` + {{$row->id ?? 0}},
                dataType: 'json',
                success: function(json) {
                  // console.log(response);
                  // const json = JSON.parse(response);
                  json.forEach((row) => {
                    updateChartWeekly(row, '{{$row->id}}');
                  })
              }
            });

                        
            $.ajax({
                type: 'GET',
                url: `month_get_graph_json/` + {{$row->id ?? 0}},
                dataType: 'json',
                success: function(json) {
                  // console.log(response);
                  // const json = JSON.parse(response);
                  json.forEach((row) => {
                    updateChartMonthly(row, '{{$row->id}}');
                  })
              }
            });
          @endif
        @endforeach
      @endif
    @endforeach
  }

  function updateChart(res, service_id)
  {
    var ctx= "";
    var subtitle = "";

    var canvas = document.getElementById('trafficChartdaily'+service_id);
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

    ctx = document.getElementById('trafficChartdaily'+service_id).getContext('2d');
    subtitle = document.getElementById('subtitledaily'+service_id)
    var data ="";
    var datasets ="";
    var cleanData ="";
    var cleanDataSets ="";
    var labels ="";
    var datasetsx ="";
    data = res.labels;
    datasets = res.datasets
    legends = res.legends;
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
                    time: { unit: 'minute' },
                    title: {
                            display: true,
                            text: 'Daily'
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

  

  function updateChartWeekly(res, service_id)
  {
    var ctx= "";
    var subtitle = "";

    var canvas = document.getElementById('trafficChartweekly'+service_id);
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

    ctx = document.getElementById('trafficChartweekly'+service_id).getContext('2d');
    subtitle = document.getElementById('subtitleweekly'+service_id)
    var data ="";
    var datasets ="";
    var cleanData ="";
    var cleanDataSets ="";
    var labels ="";
    var datasetsx ="";
    data = res.labels;
    datasets = res.datasets
    legends = res.legends;
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
                    },  
                    title: {
                            display: true,
                            text: 'Weekly'
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

  function updateChartMonthly(res, service_id)
  {
    var ctx= "";
    var subtitle = "";

    var canvas = document.getElementById('trafficChartweekly'+service_id);
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

    ctx = document.getElementById('trafficChartweekly'+service_id).getContext('2d');
    subtitle = document.getElementById('subtitleweekly'+service_id)
    var data ="";
    var datasets ="";
    var cleanData ="";
    var cleanDataSets ="";
    var labels ="";
    var datasetsx ="";
    data = res.labels;
    datasets = res.datasets
    legends = res.legends;
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
                    },  
                    title: {
                            display: true,
                            text: 'Monthly'
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
</script>

@endpush
