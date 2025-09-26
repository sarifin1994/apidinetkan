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
      <!-- Card 1: Keuangan - Income Hari Ini -->
      <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
        <div class="card bg-body-tertiary border-transparent">
          <a href="/keuangan/transaksi" style="text-decoration: none; color: inherit;">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <!-- Heading -->
                  <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                    Pemasukan<br>
                    <small>Hari Ini</small>
                  </h4>
                  <!-- Nilai dinamis dengan spinner -->
                  <div class="fs-lg fw-semibold" id="incometoday"
                       data-value="Rp{{ number_format($incometoday, 0, '.', '.') }}">
                    <span class="material-symbols-outlined spinner">progress_activity</span>
                  </div>
                </div>
                <div class="col-auto">
                  <!-- Avatar -->
                  <div class="text-primary">
                    <i class="fs-4" data-duoicon="discount"></i>
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
              <a href="/pppoe/online" style="text-decoration: none; color: inherit;">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col">
                      <!-- Heading -->
                      <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                        Total Unpaid<br>
                        <small>Bulan Ini</small>
                      </h4>
                      <!-- Nilai dinamis dengan spinner -->
                      <div class="fs-lg fw-semibold" id="totaltagihan" data-value="Rp{{ number_format($totaltagihan, 0, '.', '.') }}">
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

      <!-- Card 2: Invoice - Total Unpaid -->
      <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
        <div class="card bg-body-tertiary border-transparent">
          <a href="/invoice/unpaid" style="text-decoration: none; color: inherit;">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <!-- Heading -->
                  <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                    Unpaid Invoice<br>
                    <small>Bulan Ini</small>
                  </h4>
                  <!-- Nilai dinamis dengan spinner -->
                  <div class="fs-lg fw-semibold" id="totalunpaid" data-value="{{ $totalunpaid }}">
                    <span class="material-symbols-outlined spinner">progress_activity</span>
                  </div>
                </div>
                <div class="col-auto">
                  <!-- Avatar -->
                  <div class="text-primary">
                    <i class="fs-4" data-duoicon="slideshow"></i>
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
          <a href="/hotspot/online" style="text-decoration: none; color: inherit;">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <!-- Heading -->
                  <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                    Paid Invoice<br>
                    <small>Bulan Ini</small>
                  </h4>
                  <!-- Nilai dinamis dengan spinner -->
                  <div class="fs-lg fw-semibold" id="totalpaid" data-value="{{ $totalpaid }}">
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
    </div>

    <div class="row">
      <div class="col-12 col-xxl-12">
        @if(multi_auth()->role === 'Admin' || multi_auth()->role === 'Kasir' && optional(\App\Models\Setting\Role::where('shortname', multi_auth()->shortname)->first())->kasir_melihat_total_keuangan === 1)
        <!-- Ringkasan Pendapatan -->
        <div class="card mb-6">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col">
                <h3 class="fs-6 mb-0">Ringkasan Keuangan</h3>
              </div>
              <div class="col-auto my-n3 me-n3">
                <a class="btn btn-link" href="/keuangan/transaksi">
                  Lihat Semua
                  <span class="material-symbols-outlined">arrow_right_alt</span>
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-area">
              <canvas id="chartIncomeExpense" width="100%" height="300"></canvas>
            </div>
          </div>
        </div>
        @endif

      </div>
    </div>
  </div>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
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
    // Set new default font family and font color to mimic Bootstrap's default styling
    (Chart.defaults.global.defaultFontFamily = "Inter"),
    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = "#858796";

    function number_format(number, decimals, dec_point, thousands_sep) {
        // *     example: number_format(1234.56, 2, ',', ' ');
        // *     return: '1 234,56'
        number = (number + "").replace(",", "").replace(" ", "");
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = typeof thousands_sep === "undefined" ? "." : thousands_sep,
            dec = typeof dec_point === "undefined" ? "." : dec_point,
            s = "",
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return "" + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || "").length < prec) {
            s[1] = s[1] || "";
            s[1] += new Array(prec - s[1].length + 1).join("0");
        }
        return s.join(dec);
    }

    var ctx = document.getElementById("chartIncomeExpense");
var myLineChart = new Chart(ctx, {
    type: "line",
    data: {
        labels: [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun", 
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ],
        datasets: [
            {
                label: "Pendapatan",
                lineTension: 0.3,
                backgroundColor: "rgba(0, 97, 242, 0.05)",
                borderColor: "rgba(0, 97, 242, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(0, 97, 242, 1)",
                pointBorderColor: "rgba(0, 97, 242, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(0, 97, 242, 1)",
                pointHoverBorderColor: "rgba(0, 97, 242, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: @json($dataIncome['data']),
            },
            {
                label: "Pengeluaran",
                lineTension: 0.3,
                backgroundColor: "rgba(220, 53, 69, 0.05)",
                borderColor: "rgba(220, 53, 69, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(220, 53, 69, 1)",
                pointBorderColor: "rgba(220, 53, 69, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(220, 53, 69, 1)",
                pointHoverBorderColor: "rgba(220, 53, 69, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: @json($dataExpense['data']),
            }
        ]
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                time: {
                    unit: "month"
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 12
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 6,
                    padding: 10,
                    callback: function(value, index, values) {
                        return "Rp " + number_format(value);
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }]
        },
        legend: {
            display: true
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: "#6e707e",
            titleFontSize: 14,
            borderColor: "#dddfeb",
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: "index",
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || "";
                    return datasetLabel + ": Rp " + number_format(tooltipItem.yLabel);
                }
            }
        }
    }
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
