@extends('backend.layouts.app')
@section('main')
@section('title', 'Account')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->

    @if (multi_auth()->status === 3)
        <h2 class="fw-semibold text-danger mb-2">Sorry, license kamu expired</h2>
        <p class="text-muted mb-4">Yuk lakukan perpanjangan lisensi agar kamu bisa tetap menikmati fitur Radiusqu</p>
    @else
        <h2 class="fw-semibold mb-2">Hello, {{ multi_auth()->username }}</h2>
        <p class="text-muted mb-4">Selamat datang di dashboard akun Anda. Lihat informasi akun dan penggunaan layanan
            Anda di bawah ini</p>
    @endif
    <div class="row">
        <!-- Account Information -->
        <div class="col-xl-6 mb-4">
            <div class="card border rounded">
                <div class="card-header bg-light fw-semibold">Account Information</div>
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">Username</td>
                            <td class="fw-semibold">{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Lisensi</td>
                            <td class="fw-semibold">{{ $user->license->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga / Bulan</td>
                            @php
                            $harga = $user->license->price - $user->discount;
                            @endphp
                            <td class="fw-semibold">Rp {{ number_format($harga, 0, ',', '.') }}
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="text-muted">Jatuh Tempo</td>
                            <td class="fw-semibold">
                                {{ \Carbon\Carbon::parse($user->next_due)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if ($user->status === 1)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Expired</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="card-footer position-relative bg-light">
                    <div class="d-flex align-items-center justify-content-between text-body">
                        <!-- Jika Lisensi id 1 TRIAL -->
                        @if (multi_auth()->license_id === 1)
                            <a class="stretched-link text-body" href="/order/license">Beli Lisensi</a>
                            <span class="material-symbols-outlined">shopping_cart_checkout</span>
                        @else
                            <a class="orderLicense stretched-link text-body" data-id="{{ multi_auth()->license_id }}"
                                href="javascript:void(0)">Bayar Sekarang</a>
                            <span class="material-symbols-outlined">shopping_cart_checkout</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Usage -->
        <div class="col-xl-6 mb-4">
            <div class="card border rounded">
                <div class="card-header bg-light fw-semibold">Account Usage</div>
                <div class="card-body">
                    @php
                        // Mengambil jumlah Mikrotik NAS berdasarkan shortname user yang sedang login
                        $nasCount = \App\Models\Mikrotik\Nas::where('shortname', multi_auth()->shortname)->count();
                        $nasLimit = 'Unlimited'; // Limit NAS adalah unlimited

                        // Mengambil jumlah pengguna PPPoE berdasarkan shortname user yang sedang login
                        $pppoeCount = \App\Models\Pppoe\PppoeUser::where('shortname', multi_auth()->shortname)->count();
                        // Mengambil batas maksimum PPPoE berdasarkan lisensi user
                        $pppoeLimit = \App\Models\Owner\License::where('id', multi_auth()->license_id)->first()
                            ->limit_pppoe;
                        // Menghitung persentase penggunaan PPPoE
                        $pppoePercentage = $pppoeLimit > 0 ? ($pppoeCount / $pppoeLimit) * 100 : 0;

                        // Mengambil jumlah pengguna Hotspot berdasarkan shortname user yang sedang login
                        $hotspotCount = \App\Models\Hotspot\HotspotUser::where(
                            'shortname',
                            multi_auth()->shortname,
                        )->count();
                        // Mengambil batas maksimum Hotspot berdasarkan lisensi user (gunakan limit_hs)
                        $hotspotLimit = \App\Models\Owner\License::where('id', multi_auth()->license_id)->first()
                            ->limit_hs;
                        // Menghitung persentase penggunaan Hotspot
                        $hotspotPercentage = $hotspotLimit > 0 ? ($hotspotCount / $hotspotLimit) * 100 : 0;
                    @endphp

                    <span>
                        Mikrotik NAS
                        <span class="float-end fw-bold">{{ $nasCount }} / {{ $nasLimit }}</span>
                    </span>
                    <div class="progress mb-4 mt-2">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                            aria-valuenow="{{ $nasCount }}" aria-valuemin="0" aria-valuemax="100">NAS</div>
                    </div>

                    <span>
                        PPPoE User
                        <span class="float-end fw-bold">{{ $pppoeCount }} / {{ $pppoeLimit }}</span>
                    </span>
                    <div class="progress mb-4 mt-2">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pppoePercentage }}%"
                            aria-valuenow="{{ $pppoeCount }}" aria-valuemin="0" aria-valuemax="{{ $pppoeLimit }}">
                            {{ $pppoeCount }}</div>
                    </div>

                    <span>
                        Hotspot User
                        <span class="float-end fw-bold">{{ $hotspotCount }} / {{ $hotspotLimit }}</span>
                    </span>
                    <div class="progress mb-4 mt-2">
                        <div class="progress-bar bg-secondary" role="progressbar"
                            style="width: {{ $hotspotPercentage }}%" aria-valuenow="{{ $hotspotCount }}"
                            aria-valuemin="0" aria-valuemax="{{ $hotspotLimit }}">{{ $hotspotCount }}</div>
                    </div>
                </div>

                <div class="card-footer position-relative bg-light">
                    <div class="d-flex align-items-center justify-content-between text-body">
                        <a class="stretched-link text-body" href="/order/license">Upgrade Lisensi</a>
                        <span class="material-symbols-outlined">upgrade</span>
                    </div>
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
    $(document).on('click', '.orderLicense', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');

        Swal.fire({
            title: "Renew License",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin memperpanjang license?',
            showCancelButton: true,
            confirmButtonText: "Ya, Bayar",
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
                        if(response.paymentUrl){
                            window.location.href = response.paymentUrl; // direct halaman duitku
                        }

                        // snap.pay(response.snap_token, {
                        //     onSuccess: function(result) {
                        //         Swal.fire("Success!", "Pembayaran berhasil!",
                        //             "success").then(() => {
                        //             window.location.href = "/order/" +
                        //                 orderNumber; // Redirect ke halaman order
                        //         });
                        //     },
                        //     onPending: function(result) {
                        //         Swal.fire("Info!",
                        //             "Pembayaran sedang diproses!", "info");
                        //     },
                        //     onError: function(result) {
                        //         Swal.fire("Error!", "Pembayaran gagal!",
                        //             "error");
                        //     },
                        //     onClose: function() {
                        //         Swal.fire("Info!",
                        //             "Anda belum menyelesaikan pembayaran.",
                        //             "warning");
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
    $('#update').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }
        let perusahaan = $('#company_id').val();
        // collect data by id
        var data = {
            'name': $('#name').val(),
            'singkatan': $('#singkatan').val(),
            'slogan': $('#slogan').val(),
            'email': $('#email').val(),
            'wa': $('#wa').val(),
            'website': $('#website').val(),
            'address': $('#address').val(),
            'note': $('#note').val(),
            'bank': $('#bank').val(),
            'holder': $('#holder').val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/setting/perusahaan/${perusahaan}`,
            type: "PUT",
            cache: false,
            data: data,
            dataType: "json",

            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        location.reload()
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
    var el = $(document).find('[name="' + key + '"]');
    // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
    el.addClass('is-invalid');
    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
});

                }
            },

            error: function(err) {
                $("#message").html("Some Error Occurred!")
            }

        });

    });
</script>
@endpush
