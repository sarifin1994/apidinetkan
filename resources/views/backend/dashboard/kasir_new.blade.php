@extends('backend.layouts.app_new')
@section('title', 'Dashboard')
@section('main')

<div class="container-xl">
  <div class="page-header d-print-none">
    <div class="row align-items-center">
      <div class="col">
        <h2 class="page-title">
          Hello, {{ multi_auth()->name }}
        </h2>
        <div class="text-muted mt-1">Selamat datang di dashboard akun Anda</div>
      </div>
    </div>
  </div>

  <div class="row row-deck mt-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <span class="avatar avatar-md bg-primary-lt">
                <i class="ti ti-coins"></i>
              </span>
            </div>
            <div>
              <div class="text-muted">Pemasukan Hari Ini</div>
              <div class="h3 mb-0" id="incometoday" data-value="Rp{{ number_format($incometoday, 0, '.', '.') }}">
                <div class="skeleton-line"></div>
              </div>
            </div>
          </div>
        </div>
        <a href="/keuangan/transaksi" class="card-footer text-primary">Lihat Detail</a>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <span class="avatar avatar-md bg-warning-lt">
                <i class="ti ti-file-invoice"></i>
              </span>
            </div>
            <div>
              <div class="text-muted">Unpaid Invoice</div>
              <div class="h3 mb-0" id="totalunpaid" data-value="{{ $totalunpaid }}">
                <div class="skeleton-line"></div>
              </div>
            </div>
          </div>
        </div>
        <a href="/invoice/unpaid" class="card-footer text-primary">Lihat Detail</a>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <span class="avatar avatar-md bg-success-lt">
                <i class="ti ti-check"></i>
              </span>
            </div>
            <div>
              <div class="text-muted">Paid Invoice</div>
              <div class="h3 mb-0" id="totalpaid" data-value="{{ $totalpaid }}">
                <div class="skeleton-line"></div>
              </div>
            </div>
          </div>
        </div>
        <a href="/hotspot_online" class="card-footer text-primary">Lihat Detail</a>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <span class="avatar avatar-md bg-danger-lt">
                <i class="ti ti-calendar"></i>
              </span>
            </div>
            <div>
              <div class="text-muted">Total Unpaid Bulan Ini</div>
              <div class="h3 mb-0" id="totaltagihan" data-value="Rp{{ number_format($totaltagihan, 0, '.', '.') }}">
                <div class="skeleton-line"></div>
              </div>
            </div>
          </div>
        </div>
        <a href="/pppoe_online" class="card-footer text-primary">Lihat Detail</a>
      </div>
    </div>
  </div>

  @if(multi_auth()->role === 'Admin' || multi_auth()->role === 'Kasir' && optional(\App\Models\Setting\Role::where('shortname', multi_auth()->shortname)->first())->kasir_melihat_total_keuangan === 1)
  <div class="row row-deck mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Ringkasan Keuangan</h3>
          <div class="card-actions">
            <a href="/keuangan/transaksi" class="btn btn-sm btn-primary">Lihat Semua</a>
          </div>
        </div>
        <div class="card-body">
          <canvas id="chartIncomeExpense" height="300"></canvas>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll('[data-value]').forEach(el => {
    setTimeout(() => {
      el.innerHTML = el.getAttribute('data-value');
    }, 1000);
  });

  const ctx = document.getElementById("chartIncomeExpense");
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [
        {
          label: "Pendapatan",
          data: @json($dataIncome['data']),
          borderColor: "#206bc4",
          backgroundColor: "rgba(32, 107, 196, 0.1)",
          lineTension: 0.3
        },
        {
          label: "Pengeluaran",
          data: @json($dataExpense['data']),
          borderColor: "#d63939",
          backgroundColor: "rgba(214, 57, 57, 0.1)",
          lineTension: 0.3
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        yAxes: [{
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString();
            }
          }
        }]
      }
    }
  });
});
</script>
@endpush
