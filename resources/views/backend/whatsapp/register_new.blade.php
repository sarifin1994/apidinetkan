@extends('backend.layouts.app_new')
@section(section: 'main')
@section('title', 'Whatsapp')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Whatsapp</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="active" href="#">
                        <span><i class="ti ti-brand-whatsapp f-s-16"></i> Whatsapp</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            <!-- Tambah Button -->

        </div>
    </div><br />
    @include('backend.whatsapp.modal.daftar')

    <!-- Page content -->
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="fs-4 fw-semibold mb-2 d-flex align-items-center">
                    <i class="ti ti-brand-whatsapp me-2"></i> Whatsapp Gateway
                </h2>
                <p class="text-muted mb-0">
                    Whatsapp Gateway merupakan fitur untuk mengirim notifikasi invoice/tagihan secara otomatis.
                    Selain itu, Anda juga bisa mengirim pesan WhatsApp secara massal ke pelanggan, memudahkan
                    penyampaian informasi bisnis Anda.
                    Fitur ini dapat digunakan secara <strong>gratis</strong> tanpa biaya tambahan.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="fs-4 fw-semibold mb-3 d-flex align-items-center">
                    <i class="ti ti-clipboard-text me-2"></i> Syarat &amp; Ketentuan
                </h2>
                <p class="text-muted">Berikut syarat dan ketentuan yang harus dipatuhi:</p>
                <ol class="text-muted ps-4">
                    <li>Gunakan akun WhatsApp yang aktif dan sudah digunakan minimal 3 bulan untuk aktivitas chatting
                        guna menghindari pemblokiran oleh pihak WhatsApp.</li>
                    <li>Dilarang mengirim broadcast untuk tujuan non-bisnis atau spam.</li>
                    <li>Kami tidak bertanggung jawab atas pemblokiran akun WhatsApp. Gunakan fitur ini secara bijak dan
                        hindari mengirim broadcast secara berlebihan.</li>
                </ol>
                <p class="text-muted">
                    Jika Anda menyetujui syarat dan ketentuan di atas, silakan klik tombol <strong>Daftar
                        Sekarang</strong> di bawah ini.
                </p>
                <button class="btn btn-primary mt-4 d-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#daftar">
                    <i class="ti ti-user-plus me-2"></i> Daftar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#action_daftar').on('click', function() {
        Swal.fire({
            title: "Daftar WA Gateway",
            icon: 'warning',
            text: "Pastikan nomor yang anda masukkan sudah benar",
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Ya, Daftar",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            // cancelButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                let timerInterval;
                Swal.fire({
                    title: "Daftar WA Gateway",
                    icon: "info",
                    html: "Mendaftarkan WA Gateway, harap tunggu",
                    timer: 9999999,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                        const timer = Swal.getPopup().querySelector("b");
                        timerInterval = setInterval(() => {
                            timer.textContent = `${Swal.getTimerLeft()}`;
                        }, 9999999);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                }).then((result) => {
                    /* Read more about handling dismissals below */
                    if (result.dismiss === Swal.DismissReason.timer) {
                        console.log("Timeout");
                    }
                });

                var data = {
                    'mpwa_server': $('#mpwa_server').val(),
                    'no_wa_daftar': $('#no_wa_daftar').val(),
                };


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    }
                });

                $.ajax({
                    url: `/whatsapp/daftar/mpwa`,
                    type: "POST",
                    data: data,
                    cache: false,
                    dataType: "json",

                    // tampilkan pesan Success
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(
                            function() {
                                location.reload();
                            }, 2000);
                    },

                    error: function(err) {
                        $("#message").html(
                            "Some Error Occurred!"
                        )
                    }

                });
            }
        });
    });
</script>
@endpush
