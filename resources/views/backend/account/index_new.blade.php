@extends('backend.layouts.app_new')
@section('title', 'Account')
@section('main')
    <div class="container-fluid">
        <div class="page-header mb-4">
            @if (multi_auth()->status === 3)
                <h2 class="page-title text-danger">⚠️ License kamu expired</h2>
                <p class="text-muted">Yuk lakukan perpanjangan lisensi agar kamu bisa tetap menikmati fitur Radiusqu</p>
            @else
                <h2 class="page-title">Halo, {{ multi_auth()->username }}</h2>
                <p class="text-muted">Selamat datang di dashboard akun Anda. Lihat informasi akun dan penggunaan layanan Anda
                    di bawah ini.</p>
            @endif
        </div>

        <div class="row row-cards">
            <!-- Account Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header fw-semibold">Account Information</div>
                    <table class="table card-table">
                        <tbody>
                            <tr>
                                <td class="text-muted">Username</td>
                                <td class="fw-medium text-end">{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Lisensi</td>
                                <td class="fw-medium text-end">{{ $user->license->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Harga / Bulan</td>
                                <td class="fw-medium text-end">
                                    Rp {{ number_format($user->license->price - $user->discount, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jatuh Tempo</td>
                                <td class="fw-medium text-end">
                                    {{ \Carbon\Carbon::parse($user->next_due)->translatedFormat('d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td class="text-end">
                                    @if ($user->status === 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Expired</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="card-footer">
                        @if (multi_auth()->license_id === 1)
                            <button type="button" class="btn btn-outline-primary w-100"
                                onclick="window.location.href='/order/license'">
                                <i class="ti ti-credit-card me-1"></i> Beli Lisensi
                            </button>
                        @else
                            <button type="button" class="btn btn-primary w-100 orderLicense"
                                data-id="{{ multi_auth()->license_id }}">
                                <i class="ti ti-wallet me-1"></i> Bayar Sekarang
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Usage -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header fw-semibold">Account Usage</div>
                    <div class="card-body">
                        @php
                            $nasCount = \App\Models\Mikrotik\Nas::where('shortname', multi_auth()->shortname)->count();
                            $nasLimit = 'Unlimited';

                            $pppoeCount = \App\Models\Pppoe\PppoeUser::where(
                                'shortname',
                                multi_auth()->shortname,
                            )->count();
                            $pppoeLimit = \App\Models\Owner\License::find(multi_auth()->license_id)->limit_pppoe;
                            $pppoePercentage = $pppoeLimit > 0 ? ($pppoeCount / $pppoeLimit) * 100 : 0;

                            $hotspotCount = \App\Models\Hotspot\HotspotUser::where(
                                'shortname',
                                multi_auth()->shortname,
                            )->count();
                            $hotspotLimit = \App\Models\Owner\License::find(multi_auth()->license_id)->limit_hs;
                            $hotspotPercentage = $hotspotLimit > 0 ? ($hotspotCount / $hotspotLimit) * 100 : 0;
                        @endphp

                        <!-- NAS -->
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>Mikrotik NAS</span>
                                <span class="fw-bold">{{ $nasCount }} / {{ $nasLimit }}</span>
                            </label>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: 100%">NAS</div>
                            </div>
                        </div>

                        <!-- PPPoE -->
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>PPPoE User</span>
                                <span class="fw-bold">{{ $pppoeCount }} / {{ $pppoeLimit }}</span>
                            </label>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: {{ $pppoePercentage }}%">
                                    {{ $pppoeCount }}
                                </div>
                            </div>
                        </div>

                        <!-- Hotspot -->
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>Hotspot User</span>
                                <span class="fw-bold">{{ $hotspotCount }} / {{ $hotspotLimit }}</span>
                            </label>
                            <div class="progress">
                                <div class="progress-bar bg-secondary" style="width: {{ $hotspotPercentage }}%">
                                    {{ $hotspotCount }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" class="btn btn-outline-info w-100"
                            onclick="window.location.href='/order/license'">
                            <i class="ti ti-arrow-up-right me-1"></i> Upgrade Lisensi
                        </button>
                    </div>
                </div>
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
                            if (response.paymentUrl) {
                                window.location.href = response
                                    .paymentUrl; // direct halaman duitku
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
                            el.after($('<div class="form-text text-danger">' + value[0] +
                                '</div>'));
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
