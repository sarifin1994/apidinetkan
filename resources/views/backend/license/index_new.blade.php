@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Dashboard')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="page-title">License</h2>
             <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span>
                            <i class="ti ti-license f-s-16"></i> License
                        </span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Order License</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Section Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                {{-- <span class="badge bg-primary text-uppercase mb-2">Order Lisensi</span> --}}
                <h1 class="page-title">Yuk pilih lisensimu</h1>
                <div class="text-muted">Pilih sesuai kebutuhan, berikan yang terbaik untuk usahamu</div>
            </div>
            <div class="col-auto">
                <a href="#" class="btn btn-outline-primary">
                    <i class="ti ti-headset"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="row row-cards">
        @foreach ($licenses as $license)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-borderless">
                    <div class="card-body">
                        <h3 class="card-title">{{ $license->name }}</h3>
                        <p class="text-muted">{{ $license->deskripsi }}</p>

                        <div class="d-flex align-items-center mb-3">
                            <span class="h1 fw-bold">{{ number_format($license->price, 0, ',', '.') }}</span>
                            <span
                                class="text-muted ms-2">{{ $license->name === 'Local Server' ? '/ instalasi' : '/ bulan' }}</span>
                        </div>

                        <ul class="list-group list-group-flush mb-4">
                            @if ($license->name === 'Local Server')
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Full Local Server
                                    <i class="ti ti-check text-success"></i>
                                </li>
                            @endif

                            @if ($license->spek)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $license->spek }}
                                    <i class="ti ti-check text-success"></i>
                                </li>
                            @endif

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Unlimited NAS
                                <i class="ti ti-check text-success"></i>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @if ($license->limit_pppoe === 1000000000)
                                    Unlimited PPPoE Users
                                @else
                                    {{ number_format($license->limit_pppoe, 0, ',', '.') }} PPPoE Users
                                @endif
                                <i class="ti ti-check text-success"></i>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @if ($license->limit_hs === 1000000000)
                                    Unlimited Hotspot Users
                                @else
                                    {{ number_format($license->limit_hs, 0, ',', '.') }} Hotspot Users
                                @endif
                                <i class="ti ti-check text-success"></i>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Whatsapp Gateway
                                <i class="ti ti-check text-success"></i>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                OLT Management
                                @if ($license->olt)
                                    <i class="ti ti-check text-success"></i>
                                @else
                                    <i class="ti ti-x text-danger"></i>
                                @endif
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Payment Gateway
                                @if ($license->midtrans)
                                    <i class="ti ti-check text-success"></i>
                                @else
                                    <i class="ti ti-x text-danger"></i>
                                @endif
                            </li>
                        </ul>

                        <!-- CTA Button -->
                        @if ($license->name === 'Local Server')
                            <a href="javascript:void(0)"
                                class="btn btn-secondary w-100 @if (multi_auth()->license_id > $license->id) disabled @endif"
                                data-id="{{ $license->id }}" data-name="{{ $license->name }}">
                                <i class="ti ti-phone-call"></i> Hubungi Kami
                            </a>
                        @else
                            <a href="javascript:void(0)"
                                class="orderLicense btn btn-primary w-100 @if (multi_auth()->license_id > $license->id) disabled @endif"
                                data-id="{{ $license->id }}" data-name="{{ $license->name }}">
                                <i class="ti ti-shopping-cart"></i> Order Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- FAQ Section -->
    <div class="mt-5">
        <h2 class="mb-2">FAQ</h2>
        <div class="text-muted mb-4">Pertanyaan yang sering diajukan</div>
        <div class="accordion" id="accordionFAQ">
            @php
                $faq = [
                    [
                        'q' => 'Apa itu Radiusqu?',
                        'a' =>
                            'Radiusqu merupakan billing internet berbasis Radius untuk mengelola pelanggan PPPoE atau hotspot voucheran. Semua data tersimpan aman dan terpusat.',
                    ],
                    [
                        'q' => 'Apakah ada batasan Mikrotik untuk lisensi yang saya pilih?',
                        'a' => 'Tidak ada, kamu bisa menambahkan Mikrotik tanpa batas.',
                    ],
                    [
                        'q' => 'Apakah Radius client bisa menggunakan IP Public?',
                        'a' =>
                            'Radius server hanya menerima request melalui VPN untuk keamanan. Silakan buat akun VPN di menu yang tersedia.',
                    ],
                    [
                        'q' => 'Apakah ada batasan user online untuk lisensi yang saya pilih?',
                        'a' =>
                            'Tidak ada. Jika terjadi radius timeout karena banyak user online, hubungi kami untuk saran spesifikasi server.',
                    ],
                    [
                        'q' => 'Ada pertanyaan lainnya?',
                        'a' => 'Silakan hubungi kami melalui live chat yang telah disediakan.',
                    ],
                ];
            @endphp
            @foreach ($faq as $i => $item)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $i }}">
                        <button class="accordion-button @if ($i !== 0) collapsed @endif"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $i }}"
                            aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $i }}">
                            {{ $item['q'] }}
                        </button>
                    </h2>
                    <div id="collapse{{ $i }}"
                        class="accordion-collapse collapse @if ($i === 0) show @endif"
                        data-bs-parent="#accordionFAQ">
                        <div class="accordion-body">
                            {{ $item['a'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection


@push('scripts')
@if (env('STATUS_MIDTRANS') === 'Production')
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('CLIENT_MIDTRANS') }}"></script>
@else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('CLIENT_MIDTRANS') }}"></script>
@endif

<script type="text/javascript">
    $(document).on('click', '.orderLicense', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');

        Swal.fire({
            title: "Order License",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin order license ' + name + '?',
            showCancelButton: true,
            confirmButtonText: "Ya, Checkout",
            cancelButtonText: "Batal",
            reverseButtons: true,
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/order-license",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    data: {
                        id: id
                    },
                    success: function(response) {
                        let orderNumber = response
                        .order_id; // Pastikan order_id dikirim dari server
                        if (response.paymentUrl) {
                            window.location.href = response
                            .paymentUrl; // direct halaman duitku
                        }
                        // snap.pay(response.snap_token, {
                        //     onSuccess: function (result) {
                        //         Swal.fire("Success!", "Pembayaran berhasil!", "success").then(() => {
                        //             window.location.href = "/order/" + orderNumber; // Redirect ke halaman order
                        //         });
                        //     },
                        //     onPending: function (result) {
                        //         Swal.fire("Info!", "Pembayaran sedang diproses!", "info");
                        //     },
                        //     onError: function (result) {
                        //         Swal.fire("Error!", "Pembayaran gagal!", "error");
                        //     },
                        //     onClose: function () {
                        //         Swal.fire("Info!", "Anda belum menyelesaikan pembayaran.", "warning");
                        //     }
                        // });
                    },
                    error: function(err) {
                        Swal.fire("Error!", "Terjadi kesalahan, coba lagi!", "error");
                    }
                });
            }
        });
    });
</script>
@endpush
