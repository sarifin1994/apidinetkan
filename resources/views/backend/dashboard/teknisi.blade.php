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
              <a href="/hotspot/online" style="text-decoration: none; color: inherit;">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col">
                      <!-- Heading -->
                      <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                        Hotspot<br>
                        <small>Total User</small>
                      </h4>
                      <!-- Nilai dinamis dengan spinner -->
                      <div class="fs-lg fw-semibold" id="hotspottotal" data-value="{{ $hotspottotal }}">
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
              <a href="/hotspot/online" style="text-decoration: none; color: inherit;">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col">
                      <!-- Heading -->
                      <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                        Hotspot<br>
                        <small>User Onlline</small>
                      </h4>
                      <!-- Nilai dinamis dengan spinner -->
                      <div class="fs-lg fw-semibold" id="hotspotonline" data-value="{{ $hotspotonline }}">
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
          <a href="/hotspot/online" style="text-decoration: none; color: inherit;">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <!-- Heading -->
                  <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                    PPPoE<br>
                    <small>Total User</small>
                  </h4>
                  <!-- Nilai dinamis dengan spinner -->
                  <div class="fs-lg fw-semibold" id="pppoetotal" data-value="{{ $pppoetotal }}">
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
          <a href="/pppoe/online" style="text-decoration: none; color: inherit;">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <!-- Heading -->
                  <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                    PPPoE<br>
                    <small>User Online</small>
                  </h4>
                  <!-- Nilai dinamis dengan spinner -->
                  <div class="fs-lg fw-semibold" id="pppoeonline" data-value="{{ $pppoeonline }}">
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

    <div class="row">
      <div class="col-12 col-xxl-12">
        <!-- Daftar Mikrotik -->
        <div class="card mb-6">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col">
                <h3 class="fs-6 mb-0">Daftar Mikrotik</h3>
              </div>
              {{-- <div class="col-auto my-n3 me-n3">
                <a class="btn btn-link" href="/radius/mikrotik">
                  Lihat Semua
                  <span class="material-symbols-outlined">arrow_right_alt</span>
                </a>
              </div> --}}
            </div>
          </div>
          <div class="pb-5 table-responsive">
            <table id="myTable" class="table table-flush table-hover mb-0" width="100%">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>IP Address</th>
                  <th>Timezone</th>
                  <th>Status</th>
                  <th>Total User</th>
                </tr>
              </thead>
              <tbody>
                <!-- Data tabel akan diisi secara dinamis -->
              </tbody>
            </table>
          </div>
        </div>
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
