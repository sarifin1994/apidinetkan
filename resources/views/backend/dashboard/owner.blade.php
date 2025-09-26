@extends('backend.layouts.app')
@section('main')
@section('title', 'Dashboard')

<!-- Content -->
<div class="container-lg">
    <!-- Header Greeting -->
    <h2 class="fw-bold mb-2">
        Hello, {{ multi_auth()->name }}
    </h2>
    <p class="text-muted mb-8">Selamat datang di dashboard akun Anda</p>

    <div class="row mb-8">

        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/user" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    Total User<br>
                                </h4>
                                <!-- Nilai dinamis dengan spinner -->
                                <div class="fs-lg fw-semibold" id="totaluser" data-value="{{ $totaluser }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="text-primary">
                                    <i class="fs-4" data-duoicon="credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/user" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    User Trial<br>
                                </h4>
                                <!-- Nilai dinamis dengan spinner -->
                                <div class="fs-lg fw-semibold" id="usertrial" data-value="{{ $usertrial }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="text-primary">
                                    <i class="fs-4" data-duoicon="credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <!-- Card 3: Hotspot - User Online -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/user" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    User Active<br>
                                </h4>
                                <!-- Nilai dinamis dengan spinner -->
                                <div class="fs-lg fw-semibold" id="useractive" data-value="{{ $useractive }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="text-primary">
                                    <i class="fs-4" data-duoicon="credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Card 4: PPPoE - User Online -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/user" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    User Expired<br>
                                </h4>
                                <!-- Nilai dinamis dengan spinner -->
                                <div class="fs-lg fw-semibold" id="userexpired" data-value="{{ $userexpired }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="text-primary">
                                    <i class="fs-4" data-duoicon="clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <!-- Account Information -->
        <div class="col-xl-6 mb-4">
            <div class="card border rounded">
                <div class="card-header bg-light fw-semibold">Server Information</div>
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">OS</td>
                            <td class="fw-bold text-dark">{{ $serverInfo['OS'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Lisensi</td>
                            <td class="fw-bold text-dark">{{ $serverInfo['OS'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> --}}

    <div class="row g-3">
      <!-- Server Information -->
      <div class="col-md-6">
          <div class="card border rounded p-3">
              <h4 class="card-header bg-light fw-semibold">🖥 Server Information</h4>
              <table class="table mb-0">
                  <tbody>
                      <tr><td class="text-muted">OS</td><td class="fw-bold text-dark">{{ $serverInfo['OS'] }}</td></tr>
                      <tr><td class="text-muted">Hostname</td><td class="fw-bold text-dark">{{ $serverInfo['Hostname'] }}</td></tr>
                      <tr><td class="text-muted">PHP Version</td><td class="fw-bold text-dark">{{ $serverInfo['PHP Version'] }}</td></tr>
                      <tr><td class="text-muted">Laravel Version</td><td class="fw-bold text-dark">{{ $serverInfo['Laravel Version'] }}</td></tr>
                      <tr>
                        <td class="text-muted">Status MySQL</td>
                        <td class="fw-bold text-dark">
                            @if($mysqlStatus === 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">MySQL Connections</td>
                        <td class="fw-bold text-dark">{{ $mysqlConnections }}</td>
                    </tr>
                    <tr>
                      <td class="text-muted">Status Radius Server</td>
                      <td class="fw-bold text-dark">
                          @if($radiusStatus === 'active')
                              <span class="badge bg-success">Running</span>
                          @else
                              <span class="badge bg-danger">Stop</span>
                          @endif
                      </td>
                  </tr>
                    
                    

                  </tbody>
              </table>
          </div>
      </div>

      <!-- CPU Usage -->
      <div class="col-md-6">
          <div class="card border rounded p-3">
              <h4 class="card-header bg-light fw-semibold">⚡ CPU Usage</h4>
              <table class="table mb-0">
                  <tbody>
                      <tr><td class="text-muted">Model</td><td class="fw-bold text-dark">{{ $serverInfo['Model Name'] }}</td></tr>
                      <tr><td class="text-muted">Cores</td><td class="fw-bold text-dark">{{ $serverInfo['CPU Cores'] }}</td></tr>
                      <tr><td class="text-muted">Speed</td><td class="fw-bold text-dark">{{ $serverInfo['CPU MHz'] }} MHz</td></tr>
                      <tr><td class="text-muted">Architecture</td><td class="fw-bold text-dark">{{ $serverInfo['CPU Architecture'] }}</td></tr>
                  </tbody>
              </table>
              <div class="chart-container">
                  <canvas id="cpuChart"></canvas>
              </div>
          </div>
      </div>

      <!-- RAM Usage -->
      <div class="col-md-6">
          <div class="card border rounded p-3">
              <h4 class="card-header bg-light fw-semibold">💾 RAM Usage</h4>
              <div class="chart-container">
                  <canvas id="ramChart"></canvas>
              </div>
          </div>
      </div>

      <!-- Disk Usage -->
      <div class="col-md-6">
          <div class="card border rounded p-3">
              <h4 class="card-header bg-light fw-semibold">🗄️ Disk Usage</h4>
              <div class="chart-container">
                  <canvas id="diskChart"></canvas>
              </div>
          </div>
      </div>
  </div>

</div>



</div>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
 <!-- Chart.js Scripts -->
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
<script>
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        searching: false,
        paginate: false,
        info: false,
        ajax: '{{ url()->current() }}',
        columns: [
            // {
            //     data: null,
            //     'sortable': false,
            //     render: function(data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     }
            // },
            {
                data: 'name',
                name: 'name',
                'sortable': false,
            },
            {
                data: 'ip_router',
                name: 'ip_router'
            },
            {
                data: 'timezone',
                name: 'timezone',
                render: function(data) {
                    if (data === '0') {
                        return 'Asia/Jakarta';
                    } else if (data === '3600') {
                        return 'Asia/Makassar';
                    } else if (data === '7200') {
                        return 'Asia/Jayapura';
                    }
                },
            },
            {
                data: 'ping',
                name: 'ping',
                render: function(data, type, row, meta) {
                    // Buat id unik untuk elemen pada baris ini
                    var id = 'ping-' + meta.row;
                    // Nilai sebenarnya berdasarkan kondisi data
                    var content = (data === 1) ?
                        '<span class="badge bg-success-subtle text-success">Online</span>' :
                        '<span class="badge bg-danger-subtle text-danger">Offline</span>';
                    // Simulasikan loading dengan mengganti konten setelah 1.5 detik
                    setTimeout(function() {
                        var el = document.getElementById(id);
                        if (el) {
                            el.innerHTML = content;
                        }
                    }, 1500);
                    // Kembalikan spinner sebagai placeholder
                    return '<span id="' + id +
                        '"><span class="material-symbols-outlined spinner">progress_activity</span></span>';
                }
            },
            {
                data: 'online',
                name: 'online',
                render: function(data, type, row, meta) {
                    var id = 'online-' + meta.row;
                    var content = '<span class="material-symbols-outlined">monitoring</span> ' + data +
                        ' online';
                    setTimeout(function() {
                        var el = document.getElementById(id);
                        if (el) {
                            el.innerHTML = content;
                        }
                    }, 1500);
                    return '<span id="' + id +
                        '"><span class="material-symbols-outlined spinner">progress_activity</span></span>';
                }

            },
        ]
    });

    document.addEventListener("DOMContentLoaded", function() {
        const delay = 1000; // Delay dalam milidetik (1.5 detik)
        // Pilih semua elemen yang memiliki atribut data-value
        const elements = document.querySelectorAll('[data-value]');
        elements.forEach(function(el) {
            setTimeout(function() {
                el.innerHTML = el.getAttribute('data-value');
            }, delay);
        });
    });
</script>
@endpush
