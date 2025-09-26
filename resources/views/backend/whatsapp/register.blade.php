@extends('backend.layouts.app')
@section(section: 'main')
@section('title', 'Whatsapp')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <i class="fs-2" data-duoicon="user"></i>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    {{-- <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Whatsapp</a></li> --}}
                    <li class="breadcrumb-item active" aria-current="page">Whatsapp</li>
                </ol>
            </nav>
            @include('backend.whatsapp.modal.daftar')
            <!-- Heading -->
            <h1 class="fs-4 mb-0">Whatsapp</h1>
        </div>

    </div>
    <!-- Page content -->
    <div class="col-lg-10">
        <section class="pt-5" id="welcome">
            <h2 class="fs-5">Whatsapp Gateway</h2>
            <p class="mb-0">
                Whatsapp gateway merupakan fitur untuk mengirim notifikasi invoice / tagihan secara otomatis. Selain
                itu, Anda juga bisa mengirim pesan whatsapp secara massal ke pelanggan untuk memudahkan Anda
                menyampaikan informasi. Fitur ini bisa Anda gunakan secara gratis tanpa biaya tambahan
            </p>
        </section>
        <section class="pt-5" id="developmentSetup">
            <h2 class="fs-5">Syarat & Ketentuan</h2>
            <p>Berikut syarat dan ketentuan yang harus dipatuhi</p>
            <ol>
                <li>
                    Gunakan akun whatsapp yang sudah aktif dan sering digunakan untuk chat minimal 3 bulan agar
                    terhindar dari pemblokiran pihak whatsapp
                </li>
                <li>Dilarang mengirim broadcast selain untuk keperluan yang berhubungan dengan bisnis Anda</li>
                <li>Kami tidak bertanggung jawab apabila akun whatsapp Anda terblokir, harap gunakan dengan bijak dan
                    jangan terlalu sering mengirim broadcast massal</li>
            </ol>
            <p class="mb-0">
                Apabila anda setuju dengan syarat dan ketentuan diatas, silakan klik tombol <b>Daftar Sekarang</b>
                dibawah ini
            </p>
            <button class="btn btn-primary mt-5" data-bs-toggle="modal" data-bs-target="#daftar">Daftar
                Sekarang</button>
        </section>
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
                    'mpwa_server':$('#mpwa_server').val(),
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
