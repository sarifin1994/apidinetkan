@extends('backend.layouts.app')
@section('main')
@section('title', 'Dashboard')
<div class="container-lg">
    <!-- Page content -->
    <header class="text-center text-sm-start my-7">
        <span class="badge py-2 px-4 mb-5 border border-primary-subtle rounded-pill fs-base text-primary">Order
            Lisensi</span>
        <div class="row align-items-center">
            <div class="col">
                <h1 class="fs-1">Yuk pilih lisensimu</h1>
                <p class="lead text-body-secondary mb-0">Pilih sesuai kebutuhan, berikan yang terbaik untuk usahamu</p>
            </div>
            <div class="col-12 col-sm-auto mt-4 mt-sm-0">
                <a class="btn btn-light d-block">Hubungi Kami</a>
            </div>
        </div>
    </header>

    <!-- Pricing options -->
    <div class="row mb-4">
        @foreach ($licenses as $license)
            <div class="col-12 col-lg-4 mb-4">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body">
                        <!-- Nama License -->
                        <h5 class="fs-5 mb-1">{{ $license->name }}</h5>
                        <p class="text-body-secondary">{{ $license->deskripsi }}</p>

                        <!-- Harga -->
                        <div class="d-flex align-items-center mb-4">
                            {{-- <span class="fs-3">Rp</span> --}}
                            <span class="fs-1 fw-semibold">{{ number_format($license->price, 0, ',', '.') }}</span>
                            @if($license->name === 'Local Server')
                            <span class="fs-6 text-body-secondary ms-1">/ instalasi</span>
                            @else
                            <span class="fs-6 text-body-secondary ms-1">/ bulan</span>
                            @endif
                        </div>

                        <!-- Features -->
                        <ul class="list-group list-group-flush mb-4">
                            @if($license->name === 'Local Server')
                            <li class="list-group-item bg-body-tertiary border-dark px-0">
                                <div class="row">
                                    <div class="col text-body-secondary">
                                        Full Local Server</div>
                                    <div class="col-auto">
                                        <span class="material-symbols-outlined text-success">check</span>
                                    </div>
                                </div>
                            </li>
                            @endif
                            @if($license->spek !== NULL)
                            <li class="list-group-item bg-body-tertiary border-dark px-0">
                                <div class="row">
                                    <div class="col text-body-secondary">
                                        {{$license->spek}}</div>
                                    <div class="col-auto">
                                        <span class="material-symbols-outlined text-success">check</span>
                                    </div>
                                </div>
                            </li>
                            @endif
                            <li class="list-group-item bg-body-tertiary border-dark px-0">
                                <div class="row">
                                    <div class="col text-body-secondary">
                                        Unlimited NAS</div>
                                    <div class="col-auto">
                                        <span class="material-symbols-outlined text-success">check</span>
                                    </div>
                                </div>
                            </li>
                           
                            <li class="list-group-item bg-body-tertiary border-dark px-0">
                                <div class="row">
                                    @if($license->limit_pppoe === 1000000000)
                                    <div class="col text-body-secondary">Unlimited PPPoE Users</div>
                                    @else
                                    <div class="col text-body-secondary">
                                        {{ number_format($license->limit_pppoe, 0, ',', '.') }} PPPoE Users</div>
                                        @endif
                                    <div class="col-auto">
                                        <span class="material-symbols-outlined text-success">check</span>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-body-tertiary border-dark px-0">
                                <div class="row">
                                    @if($license->limit_hs === 1000000000)
                                    <div class="col text-body-secondary">Unlimited Hotspot Users</div>
                                    @else
                                    <div class="col text-body-secondary">
                                        {{ number_format($license->limit_hs, 0, ',', '.') }} Hotspot Users</div>
                                        @endif
                                    <div class="col-auto">
                                        <span class="material-symbols-outlined text-success">check</span>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-body-tertiary border-dark px-0">
                                <div class="row">
                                    <div class="col text-body-secondary">
                                        Whatsapp Gateway</div>
                                    <div class="col-auto">
                                        <span class="material-symbols-outlined text-success">check</span>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-body-tertiary border-dark px-0">
                                <div class="row">
                                    <div class="col text-body-secondary">
                                        OLT Management</div>
                                    <div class="col-auto">
                                        @if ($license->olt === 1)
                                            <span class="material-symbols-outlined text-success">check</span>
                                        @else
                                            <span class="material-symbols-outlined text-danger">close</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item bg-body-tertiary border-dark px-0">
                                <div class="row">
                                    <div class="col text-body-secondary">
                                        Payment Gateway</div>
                                    <div class="col-auto">
                                        @if ($license->midtrans === 1)
                                            <span class="material-symbols-outlined text-success">check</span>
                                        @else
                                            <span class="material-symbols-outlined text-danger">close</span>
                                        @endif
                                    </div>
                                </div>
                            </li>


                        </ul>

                        <!-- Button -->
                        @if($license->name === 'Local Server')
                        <a class="btn btn-dark d-block  @if(multi_auth()->license_id > $license->id) disabled @endif" data-id="{{$license->id}}" data-name="{{$license->name}}" href="javascript:void(0)">Hubungi Kami</a>
                        @else
                        <a class="orderLicense btn btn-dark d-block  @if(multi_auth()->license_id > $license->id) disabled @endif" data-id="{{$license->id}}" data-name="{{$license->name}}" href="javascript:void(0)">Order Sekarang</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <!-- Header -->
    <header class="mb-5">
        <h2 class="fs-5 mb-1">FAQ</h2>
        <p class="text-body-secondary mb-0">Pertanyaan yang sering diajukan.</p>
    </header>

    <div class="accordion accordion-flush" id="accordionFAQ">
        <!-- FAQ 1: Apa itu RADIUSQU? -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    Apa itu Radiusqu?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body px-0">
                    Radiusqu merupakan billing internet berbasis Radius untuk memudahkan kamu mengelola pelanggan PPPoE
                    atau hotspot voucheran.
                    Semua data pelanggan atau voucher dari semua cabang disimpan terpusat di satu server dengan aman.
                </div>
            </div>
        </div>

        <!-- FAQ 2: Apakah ada batasan Mikrotik untuk lisensi yang saya pilih? -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Apakah ada batasan Mikrotik untuk lisensi yang saya pilih?
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body px-0">
                    Tidak ada, kamu bisa menambahkan Mikrotik tanpa batas.
                </div>
            </div>
        </div>

        <!-- FAQ 3: Apakah Radius client bisa menggunakan IP Public? -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Apakah Radius client bisa menggunakan IP Public?
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body px-0">
                    Demi keamanan server, Radius server kami hanya menerima request melalui VPN.
                    Silakan buat account VPN di menu yang telah disediakan gratis agar radius server kami bisa menerima
                    request dari Mikrotik kamu.
                </div>
            </div>
        </div>

        <!-- FAQ 4: Apakah ada batasan user online untuk lisensi yang saya pilih? -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    Apakah ada batasan user online untuk lisensi yang saya pilih?
                </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body px-0">
                    Tidak ada, namun apabila kamu sering mengalami radius timeout akibat terlalu banyak user online
                    khususnya user hotspot,
                    harap hubungi kami untuk mendapatkan spesifikasi server yang sesuai.
                </div>
            </div>
        </div>

        <!-- FAQ 5: Ada pertanyaan lainnya? -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button px-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                    Ada pertanyaan lainnya?
                </button>
            </h2>
            <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body px-0">
                    Silakan hubungi kami melalui live chat yang telah disediakan.
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@push('scripts')
@if(env('STATUS_MIDTRANS') === 'Production')
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{env('CLIENT_MIDTRANS')}}"></script>
@else
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{env('CLIENT_MIDTRANS')}}"></script>
@endif

<script type="text/javascript">
 $(document).on('click', '.orderLicense', function (e) {
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
    }).then(function (result) {
        if (result.isConfirmed) {
            $.ajax({
                url: "/order-license",
                type: "POST",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                data: { id: id },
                success: function (response) {
                    let orderNumber = response.order_id; // Pastikan order_id dikirim dari server
                    if(response.paymentUrl){
                        window.location.href = response.paymentUrl; // direct halaman duitku
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
                error: function (err) {
                    Swal.fire("Error!", "Terjadi kesalahan, coba lagi!", "error");
                }
            });
        }
    });
});


</script>
@endpush
