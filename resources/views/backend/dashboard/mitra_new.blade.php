@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Dashboard')

<!-- Content -->
<div class="container-fluid py-5">
  <!-- Header Greeting -->
  <div class="mb-5">
    <h2 class="fw-bold mb-1 text-dark">👋 Hello, {{ multi_auth()->name }}</h2>
    <p class="text-muted fs-5">Selamat datang di dashboard akun Anda</p>
  </div>

  <div class="row g-4">
    <!-- Stylish Card Component -->
    @php
      $cards = [
        [
          'title' => 'Total User',
          'subtitle' => 'PPPoE',
          'value' => $pppoetotal,
          'icon' => 'users',
          'color' => 'primary',
          'link' => '/pppoe_user'
        ],
        [
          'title' => 'User Pending',
          'subtitle' => 'PPPoE',
          'value' => $pppoepending,
          'icon' => 'clock',
          'color' => 'warning',
          'link' => '/pppoe_user'
        ],
        [
          'title' => 'Total Komisi',
          'subtitle' => 'Bulan Ini',
          'value' => number_format($totalkomisi, 0, ',', '.'),
          'icon' => 'currency-dollar',
          'color' => 'success',
          'link' => '/keuangan/mitra'
        ],
        [
          'title' => 'Total Invoice',
          'subtitle' => 'Unpaid',
          'value' => $totalunpaid,
          'icon' => 'file-invoice',
          'color' => 'danger',
          'link' => '/invoice/unpaid'
        ],
      ];
    @endphp

    @foreach ($cards as $card)
      <div class="col-sm-6 col-xl-3">
        <a href="{{ $card['link'] }}" class="text-decoration-none">
          <div class="card shadow-lg border-0 rounded-4 h-100 hover-shadow position-relative overflow-hidden">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
              <div class="mb-3">
                <small class="text-uppercase text-{{ $card['color'] }} fw-semibold">{{ $card['subtitle'] }}</small>
                <h5 class="fw-bold text-dark mb-1">{{ $card['title'] }}</h5>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <div class="fs-3 fw-bold text-dark" data-value="{{ $card['value'] }}">
                  <i class="tabler-icon icon-loading"></i>
                </div>
                <div class="bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }} rounded-circle p-2 shadow-sm">
                  <i class="tabler-icon tabler-{{ $card['icon'] }} fs-2"></i>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/@tabler/icons-webfont"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const delay = 1000;
    const elements = document.querySelectorAll('[data-value]');
    elements.forEach(function(el) {
      setTimeout(function() {
        el.textContent = el.getAttribute('data-value');
      }, delay);
    });
  });
</script>
@endpush