@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Dashboard')
<!-- Body main section starts -->
    <div class="container-fluid">
        <!-- Cards Row -->
        <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-4">
            <div class="col">
                <a href="/user" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <span class="bg-primary h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                            <i class="ph ph-users f-s-24"></i>
                        </span>
                        <div class="card-body eshop-cards">
                            <span class="ripple-effect"></span>
                            <h3 class="text-primary mb-0" id="incometoday">{{ $totaluser }}</h3>
                            <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">Total User</p>
                            <span class="badge bg-light-primary">Users</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="/user" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <span class="bg-warning h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                            <i class="ph ph-users f-s-24"></i>
                        </span>
                        <div class="card-body eshop-cards">
                            <span class="ripple-effect"></span>
                            <h3 class="text-warning mb-0" id="totalunpaid">{{ $usertrial }}</h3>
                            <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">Users Trial</p>
                            <span class="badge bg-light-warning">Users</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="/user" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <span class="bg-danger h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                            <i class="ph ph-users f-s-24"></i>
                        </span>
                        <div class="card-body eshop-cards">
                            <span class="ripple-effect"></span>
                            <h3 class="text-danger mb-0 txt-ellipsis-1" id="hotspotonline">{{ $userexpired }}</h3>
                            <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">User Expired</p>
                            <span class="badge bg-light-danger">Users</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="/user" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <span class="bg-success h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                            <i class="ph-duotone ph-users f-s-24"></i>
                        </span>
                        <div class="card-body eshop-cards">
                            <span class="ripple-effect"></span>
                            <h3 class="text-success mb-0" id="pppoeonline">{{ $useractive }}</h3>
                            <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">User Active</p>
                            <span class="badge bg-light-success">Users</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Ringkasan Keuangan & Informasi Layanan -->
        <div class="row g-3">
            <div class="col-6 col-xxl-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🖥 Server Information</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <div id="polar2"></div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ph-bold ph-linux-logo f-s-20"></i>
                                </span>
                                <div class="flex-grow-1 ms-5">
                                    <h6 class="mb-0">OS</h6>
                                </div>
                                <p class="text-secondary fw-bold mb-0">{{ $serverInfo['OS'] }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ph-bold ph-identification-card f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5">
                                    <h6 class="mb-0">Hostname</h6>
                                </div>
                                <p class="text-secondary fw-bold mb-0">{{ $serverInfo['Hostname'] }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ti ti-versions f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5  ">
                                    <h6 class="mb-0">Version</h6>
                                </div>
                                <p class="text-secondary fw-bold mb-0">{{ $serverInfo['PHP Version'] }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ti ti-versions f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5  ">
                                    <h6 class="mb-0">Laravel Version</h6>
                                </div>
                                <p class="text-secondary fw-bold mb-0">{{ $serverInfo['Laravel Version'] }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ti ti-status-change f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5  ">
                                    <h6 class="mb-0">Status MySQL</h6>
                                </div>
                                @if($mysqlStatus == 'Active')
                                <p class="badge bg-success-subtle bg-success f-s-10 text-uppercase">Active</p>
                                @else
                                <p class="badge bg-danger-subtle bg-danger f-s-10 text-uppercase">InActive</p>
                                @endif
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ti ti-sitemap f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5  ">
                                    <h6 class="mb-0">MySQL Connections</h6>
                                </div>
                                 <p class="text-secondary fw-bold mb-0">{{ $mysqlConnections }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ti ti-status-change f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5  ">
                                    <h6 class="mb-0">Status Radius Server</h6>
                                </div>
                                @if($radiusStatus == 'Active')
                                <p class="badge bg-success-subtle bg-success f-s-10 text-uppercase">Active</p>
                                @else
                                <p class="badge bg-danger-subtle bg-danger f-s-10 text-uppercase">InActive</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xxl-6">
                <div class="card">
                    <div class="card-header">
                        <h5>⚡ CPU Usage</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <div id="polar2"></div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ti ti-box-model f-s-20"></i>
                                </span>
                                <div class="flex-grow-1 ms-5">
                                    <h6 class="mb-0">Model</h6>
                                </div>
                                <p class="text-secondary fw-bold mb-0">{{ $serverInfo['Model Name'] }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="fa-solid fa-brands fa-slack fa-fw f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5">
                                    <h6 class="mb-0">Core</h6>
                                </div>
                                <p class="text-secondary fw-bold mb-0">{{ $serverInfo['CPU Cores'] }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="fa-solid fa-clock fa-fw f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5  ">
                                    <h6 class="mb-0">Speed</h6>
                                </div>
                                <p class="text-secondary fw-bold mb-0">{{ $serverInfo['CPU MHz'] }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <span class="text-light-primary h-40 w-40 d-flex-center b-r-10 position-absolute">
                                    <i class="ti ti-building-arch f-s-22"></i>
                                </span>
                                <div class="flex-grow-1 ms-5  ">
                                    <h6 class="mb-0">Architecture</h6>
                                </div>
                                <p class="text-secondary fw-bold mb-0">{{ $serverInfo['CPU Architecture'] }}</p>
                            </div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                 <canvas id="cpuChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6 col-xxl-6">
                <div class="card">
                    <div class="card-header">
                        <h5>💾 RAM Usage</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <div id="polar2"></div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                <canvas id="ramChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xxl-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🗄️ Disk Usage</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <div id="polar2"></div>
                        </div>
                        <div class="file-manager-sidebar mb-4">
                            <div class="d-flex align-items-center position-relative">
                                                 <canvas id="diskChart"></canvas>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Body main section ends -->
@endsection
@push('scripts')
<script>
   // Inisialisasi Chart.js
   // Ambil nilai cpuUsage dari backend
   const cpuUsage = {!! json_encode($cpuUsage) !!};

// Inisialisasi Chart.js
const ctx = document.getElementById('cpuChart').getContext('2d');

// Jika kamu hanya memiliki satu data CPU usage, kita tampilkan sebagai chart dengan satu titik,
// atau bisa juga dibuat chart real-time dengan polling yang akan menambahkan data baru.
const cpuChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Now'], // Label waktu, bisa dikembangkan jika menambahkan data polling
        datasets: [{
            label: 'CPU Usage (%)',
            data: [cpuUsage],
            borderColor: '#ff6384',
            backgroundColor: 'rgba(255,99,132,0.2)',
            fill: true,
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
  // RAM Usage Doughnut Chart
  var ctxRAM = document.getElementById('ramChart').getContext('2d');
  new Chart(ctxRAM, {
      type: 'doughnut',
      data: {
          labels: ['Used Memory (MB)', 'Free Memory (MB)'],
          datasets: [{
              data: [{{ $serverInfo['Used Memory (MB)'] }}, {{ $serverInfo['Free Memory (MB)'] }}],
              backgroundColor: ['#dc3545', '#17a2b8'],
          }]
      },
      options: {
          responsive: true,
      }
  });

  // Disk Usage Bar Chart
  var ctxDisk = document.getElementById('diskChart').getContext('2d');
  new Chart(ctxDisk, {
      type: 'bar',
      data: {
          labels: ['Total', 'Used', 'Available'],
          datasets: [{
              label: 'Disk Space (GB)',
              data: [
                  parseFloat('{{ str_replace("G", "", $serverInfo["Disk Total"]) }}'),
                  parseFloat('{{ str_replace("G", "", $serverInfo["Disk Used"]) }}'),
                  parseFloat('{{ str_replace("G", "", $serverInfo["Disk Available"]) }}')
              ],
              backgroundColor: ['#ffc107', '#dc3545', '#17a2b8']
          }]
      },
      options: {
          responsive: true,
          scales: { y: { beginAtZero: true } }
      }
  });
</script>
@endpush