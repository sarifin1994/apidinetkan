<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="author" content="Putra Garsel Interkoneksi">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0 , user-scalable=0, minimum-scale=1.0, maximum-scale=1.0"
        name="viewport">
    <meta content="Solusi Terpadu untuk Radius, Billing, Notifikasi, Pembayaran, hingga OLT ZTE dalam Satu Platform."
        name="description">
    <meta content="Radiusqu, OLT, Biling, Radius, Notifikasi, Pembayaran" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="Radiusqu" />
    <meta name="author" content="Radiusqu Team" />
    <meta content="la-themes" name="author">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:title" content="Radiusqu">
    <meta property="og:description"
        content="Solusi Terpadu untuk Radius, Billing, Notifikasi, Pembayaran, hingga OLT ZTE dalam Satu Platform.">
    <meta property="og:image" content="{{ asset('assets/images/radiusqu_white.png') }}">
    <title>Login | {{ config('app.name') }}</title>

    <!--font-awesome-css-->
    <link href="{{ asset('assets_new/vendor/fontawesome/css/all.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">

    <!-- tabler icons-->
    <link href="{{ asset('assets_new/vendor/tabler-icons/tabler-icons.css') }}" rel="stylesheet" type="text/css">

    <!-- phosphor-icon css-->
    <link href="{{ asset('assets_new/vendor/phosphor/phosphor-bold.css') }}" rel="stylesheet">

    <!-- Bootstrap css-->
    <link href="{{ asset('assets_new/vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <!-- App css-->
    <link href="{{ asset('assets_new/css/style.css') }}" rel="stylesheet" type="text/css">

    <!-- Responsive css-->
    <link href="{{ asset('assets_new/css/responsive.css') }}" rel="stylesheet" type="text/css">

    <!--flag Icon css-->
    <link href="{{ asset('assets_new/vendor/flag-icons-master/flag-icon.css') }}" rel="stylesheet" type="text/css">

    <!-- simplebar css-->
    <link href="{{ asset('assets_new/vendor/simplebar/simplebar.css') }}" rel="stylesheet" type="text/css">


</head>

<body>
    <div class="app-wrapper d-block">
        <div class="">
            <!-- Body main section starts -->
            <main class="w-100 p-0">
                <!-- Login to your Account start -->
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-12 p-0">
                            <div class="login-form-container">
                                <div class="form_container">
                                    <div class="mb-4">
                                        <a class="logo" href="{{ url('/') }}">
                                            <img alt="#" src="{{ asset('assets/images/favicon_login.png') }}"
                                                class="">
                                        </a>
                                    </div>
                                    @if (session('success'))
                                    <div class="alert alert-light-border-primary d-flex align-items-center justify-content-between alert-auto-hide"
                                        role="alert"
                                        style="position: fixed; top: 1rem; right: 1rem; min-width: 300px; z-index: 9999;">
                                        <p class="mb-0" style="margin: 0;">
                                            <i class="ti ti-check" style="font-size: 18px; margin-right: 0.5rem;"></i>
                                            {{ session('success') }}
                                        </p>
                                        <i class="ti ti-x" data-bs-dismiss="alert" style="cursor:pointer;"></i>
                                    </div>
                                @endif
                                @if ($errors->has('error'))
                                    <div class="alert alert-light-border-danger d-flex align-items-center justify-content-between alert-auto-hide"
                                        role="alert"
                                        style="position: fixed; top: 1rem; right: 1rem; min-width: 300px; z-index: 9999;">
                                        <p class="mb-0" style="margin: 0;">
                                            <i class="ti ti-ban" style="font-size: 18px; margin-right: 0.5rem;"></i>
                                            {{ $errors->first('error') }}
                                        </p>
                                        <i class="ti ti-x" data-bs-dismiss="alert" style="cursor:pointer;"></i>
                                    </div>
                                @endif
                                    <form class="app-form" action="{{ route('auth') }}" method="POST">
                                        @csrf
                                        <div class="mb-3 text-center">
                                            <h3>Selamat Datang di Radiusqu</h3>
                                            <p class="f-s-12 text-secondary">Silakan login untuk mengakses dashboard
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="emailId">Username</label>
                                            <input class="form-control" name="username" id="username"
                                                type="text" placeholder="Username">
                                            @error('username')
                                                <div class="mt-1">
                                                    <span class="text-danger"> {{ $message }} </span>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="password">Password</label>
                                            <input class="form-control" type="password" id="password"
                                                name="password" placeholder="Password">
                                            @error('password')
                                                <div class="mt-1">
                                                    <span class="text-danger"> {{ $message }} </span>
                                                </div>
                                            @enderror
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary w-100">Login</button>
                                        </div>
                                        <br />
                                        <div class="text-center">
                                            Belum punya akun?
                                            <a class="text-primary text-decoration-underline" href="/register">Daftar
                                                sekarang</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Login to your Account end -->
            </main>
            <!-- Body main section ends -->
        </div>
    </div>
    <!-- latest jquery-->
    <script src="{{ asset('assets_new/js/jquery-3.6.3.min.js') }}"></script>

    <!-- Bootstrap js-->
    <script src="{{ asset('assets_new/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const autoAlerts = document.querySelectorAll('.alert-auto-hide');
            autoAlerts.forEach(function(el) {
                setTimeout(function() {
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500); // tunggu transisi
                }, 3000); // tampil 3 detik
            });
        });
    </script>

</body>

</html>
