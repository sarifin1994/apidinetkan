@extends('backend.layouts.app_new')
@section('title', 'Dashboard')
@section('main')
    <div class="container-fluid">
        <!-- Header Greeting -->
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Hello, {{ multi_auth()->name }}</h2>
                    <div class="text-muted mt-1">Selamat datang di dashboard akun Anda</div>
                </div>
            </div>
        </div>

        <!-- Statistic Cards -->
        <div class="row row-cards mt-4">
            <!-- Hotspot Total -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <a href="/hotspot_online"
                        class="card-body d-flex align-items-center justify-content-between text-decoration-none text-dark">
                        <div>
                            <div class="text-muted">Hotspot<br><small>Total User</small></div>
                            <div class="h3 mb-0" id="hotspottotal" data-value="{{ $hotspottotal }}">
                                <span class="skeleton-line"></span>
                            </div>
                        </div>
                        <svg class="icon icon-tabler icon-tabler-wifi" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 18l.01 0" />
                            <path d="M9.172 15.172a4 4 0 0 1 5.656 0" />
                            <path d="M6.343 12.343a8 8 0 0 1 11.314 0" />
                            <path d="M3.515 9.515a12 12 0 0 1 16.97 0" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Hotspot Online -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <a href="/hotspot_online"
                        class="card-body d-flex align-items-center justify-content-between text-decoration-none text-dark">
                        <div>
                            <div class="text-muted">Hotspot<br><small>User Online</small></div>
                            <div class="h3 mb-0" id="hotspotonline" data-value="{{ $hotspotonline }}">
                                <span class="skeleton-line"></span>
                            </div>
                        </div>
                        <svg class="icon icon-tabler icon-tabler-device-desktop" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <rect x="3" y="4" width="18" height="12" rx="1" />
                            <line x1="7" y1="20" x2="17" y2="20" />
                            <line x1="9" y1="16" x2="9" y2="20" />
                            <line x1="15" y1="16" x2="15" y2="20" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- PPPoE Total -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <a href="/pppoe_online"
                        class="card-body d-flex align-items-center justify-content-between text-decoration-none text-dark">
                        <div>
                            <div class="text-muted">PPPoE<br><small>Total User</small></div>
                            <div class="h3 mb-0" id="pppoetotal" data-value="{{ $pppoetotal }}">
                                <span class="skeleton-line"></span>
                            </div>
                        </div>
                        <svg class="icon icon-tabler icon-tabler-network" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <circle cx="12" cy="5" r="2" />
                            <circle cx="5" cy="19" r="2" />
                            <circle cx="19" cy="19" r="2" />
                            <line x1="12" y1="7" x2="12" y2="13" />
                            <line x1="5" y1="17" x2="12" y2="13" />
                            <line x1="19" y1="17" x2="12" y2="13" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- PPPoE Online -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <a href="/pppoe_online"
                        class="card-body d-flex align-items-center justify-content-between text-decoration-none text-dark">
                        <div>
                            <div class="text-muted">PPPoE<br><small>User Online</small></div>
                            <div class="h3 mb-0" id="pppoeonline" data-value="{{ $pppoeonline }}">
                                <span class="skeleton-line"></span>
                            </div>
                        </div>
                        <svg class="icon icon-tabler icon-tabler-users" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M17 11v-1a4 4 0 1 0 -8 0v1" />
                            <path d="M17 11a4 4 0 0 0 0 8h0a4 4 0 0 0 0 -8z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mikrotik Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Mikrotik</h3>
                    </div>
                    <div class="table-responsive">
                        <table id="myTable" class="table card-table table-vcenter text-nowrap table-hover"  width="100%">
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
                                <!-- AJAX Data -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let table = $('#myTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url()->current() }}',
            scrollX: true,
            searching: false,
            paginate: false,
            info: false,
            columns: [{
                    data: 'name'
                },
                {
                    data: 'ip_router'
                },
                {
                    data: 'timezone',
                    render: data => data === '0' ? 'Asia/Jakarta' : (data === '3600' ? 'Asia/Makassar' :
                        'Asia/Jayapura')
                },
                {
                    data: 'ping',
                    render: (data, type, row, meta) => {
                        const id = 'ping-' + meta.row;
                        const content = data === 1 ?
                            '<span class="badge bg-success">Online</span>' :
                            '<span class="badge bg-danger">Offline</span>';
                        setTimeout(() => {
                            const el = document.getElementById(id);
                            if (el) el.innerHTML = content;
                        }, 1000);
                        return `<span id="${id}"><span class="skeleton-line"></span></span>`;
                    }
                },
                {
                    data: 'online',
                    render: (data, type, row, meta) => {
                        const id = 'online-' + meta.row;
                        const content = `<span class="icon">monitoring</span> ${data} online`;
                        setTimeout(() => {
                            const el = document.getElementById(id);
                            if (el) el.innerHTML = content;
                        }, 1000);
                        return `<span id="${id}"><span class="skeleton-line"></span></span>`;
                    }
                },
            ]
        });

        document.addEventListener("DOMContentLoaded", function() {
            const delay = 1000;
            const elements = document.querySelectorAll('[data-value]');
            elements.forEach(function(el) {
                setTimeout(function() {
                    el.innerHTML = el.getAttribute('data-value');
                }, delay);
            });
        });
    </script>
@endpush
