<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="Radiusqu" />
    <meta name="author" content="Radiusqu Team" />
    <title>Verifikasi | {{ config('app.name') }}</title>
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{env('APP_URL')}}">
  <meta property="og:title" content="Radiusqu">
  <meta property="og:description" content="Solusi Terpadu untuk Radius, Billing, Notifikasi, Pembayaran, hingga OLT ZTE dalam Satu Platform.">
  <meta property="og:image" content="{{ asset('assets/images/radiusqu_white.png') }}">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" />

    <!-- Fonts and icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />

    <!-- Libs CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/libs.bundle.css') }}" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme.bundle.css') }}" />

    <script>
        /*!
         * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
         * Copyright 2011-2024 The Bootstrap Authors
         * Licensed under the Creative Commons Attribution 3.0 Unported License.
         * Modified by Simpleqode
         */

        (() => {
            'use strict';

            const getStoredTheme = () => localStorage.getItem('theme');
            const setStoredTheme = (theme) => localStorage.setItem('theme', theme);

            const getStoredNavigationPosition = () => localStorage.getItem('navigationPosition');
            const setStoredNavigationPosition = (navigationPosition) => localStorage.setItem('navigationPosition',
                navigationPosition);

            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme();
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };

            const getPreferredNavigationPosition = () => {
                const storedNavigationPosition = getStoredNavigationPosition();
                if (storedNavigationPosition) {
                    return storedNavigationPosition;
                }
                return 'sidenav';
            };

            const setTheme = (theme) => {
                if (theme === 'auto') {
                    document.documentElement.setAttribute('data-bs-theme', window.matchMedia(
                        '(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', theme);
                }
            };

            const setNavigationPosition = (navigationPosition) => {
                document.documentElement.setAttribute('data-bs-navigation-position', navigationPosition);
            };

            setTheme(getPreferredTheme());
            setNavigationPosition(getPreferredNavigationPosition());

            const showActiveTheme = (theme, settingsSwitcher) => {
                document.querySelectorAll('[data-bs-theme-value]').forEach((element) => {
                    element.classList.remove('active');
                    element.setAttribute('aria-pressed', 'false');

                    if (element.getAttribute('data-bs-theme-value') === theme) {
                        element.classList.add('active');
                        element.setAttribute('aria-pressed', 'true');
                    }
                });
                if (settingsSwitcher) {
                    settingsSwitcher.focus();
                }
            };

            const showActiveNavigationPosition = (navigationPosition, settingsSwitcher) => {
                document.querySelectorAll('[data-bs-navigation-position-value]').forEach((element) => {
                    element.classList.remove('active');
                    element.setAttribute('aria-pressed', 'false');

                    if (element.getAttribute('data-bs-navigation-position-value') === navigationPosition) {
                        element.classList.add('active');
                        element.setAttribute('aria-pressed', 'true');
                    }
                });

                if (settingsSwitcher) {
                    settingsSwitcher.focus();
                }
            };

            const refreshCharts = () => {
                const charts = document.querySelectorAll('.chart-canvas');

                charts.forEach((chart) => {
                    const chartId = chart.getAttribute('id');
                    const instance = Chart.getChart(chartId);

                    if (!instance) {
                        return;
                    }

                    if (instance.options.scales.y) {
                        instance.options.scales.y.grid.color = getComputedStyle(document.documentElement)
                            .getPropertyValue('--bs-border-color');
                        instance.options.scales.y.ticks.color = getComputedStyle(document.documentElement)
                            .getPropertyValue('--bs-secondary-color');
                    }

                    if (instance.options.scales.x) {
                        instance.options.scales.x.ticks.color = getComputedStyle(document.documentElement)
                            .getPropertyValue('--bs-secondary-color');
                    }

                    if (instance.options.elements.arc) {
                        instance.options.elements.arc.borderColor = getComputedStyle(document
                            .documentElement).getPropertyValue('--bs-body-bg');
                        instance.options.elements.arc.hoverBorderColor = getComputedStyle(document
                            .documentElement).getPropertyValue('--bs-body-bg');
                    }

                    instance.update();
                });
            };

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                const storedTheme = getStoredTheme();
                if (storedTheme !== 'light' && storedTheme !== 'dark') {
                    setTheme(getPreferredTheme());
                }
            });

            window.addEventListener('DOMContentLoaded', () => {
                showActiveTheme(getPreferredTheme());
                showActiveNavigationPosition(getPreferredNavigationPosition());

                document.querySelectorAll('[data-bs-theme-value]').forEach((toggle) => {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        const theme = toggle.getAttribute('data-bs-theme-value');
                        const settingsSwitcher = toggle.closest('.nav-item').querySelector(
                            '[data-bs-settings-switcher]');
                        console.log(settingsSwitcher);
                        setStoredTheme(theme);
                        setTheme(theme);
                        showActiveTheme(theme, settingsSwitcher);
                        refreshCharts();
                    });
                });

                document.querySelectorAll('[data-bs-navigation-position-value]').forEach((toggle) => {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        const navigationPosition = toggle.getAttribute(
                            'data-bs-navigation-position-value');
                        const settingsSwitcher = toggle.closest('.nav-item').querySelector(
                            '[data-bs-settings-switcher]');
                        setStoredNavigationPosition(navigationPosition);
                        setNavigationPosition(navigationPosition);
                        showActiveNavigationPosition(navigationPosition, settingsSwitcher);
                    });
                });
            });
        })();
    </script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-12 text-center d-flex justify-content-center align-items-center d-none d-md-flex"
                style="height: 100vh; background-color: #05f">
                <img src="{{ asset('assets/images/logo_new.png') }}" class="img-fluid" style="max-width: 240px;"
                    alt="Logo">
            </div>
            <div class="col-lg-5 col-md-12 offset-xl-1 d-flex align-items-center py-10">

                <div class="container">
                    {{-- <div class="row"> --}}
                    <div class="col-12" style="max-width: 25rem">
                        <img src="{{ asset('assets/images/logo-black.png') }}" class="mt-10 mb-7 img-fluid d-block d-md-none" style="max-width: 240px;" alt="Logo">
                        <!-- Heading -->
                        <h1 class="fs-4">Verifikasi</h1>

                        <!-- Subheading -->
                        <p class="lead text-body-secondary">Silakan masukan kode otp yang telah dikirimkan ke nomor
                            whatsapp
                        </p>

                        <!-- Form -->
                        <form action="{{ route('verify') }}" method="post" class="mb-5">
                            @csrf
                            <input type="text" class="form-control mb-4" name="whatsapp" id="whatsapp"
                                value="{{ session('whatsapp') ?? (multi_auth()->whatsapp ?? '') }}" readonly>
                            <div class="mb-4">
                                <div class="hstack gap-1 mx-auto mb-4" data-toggle="verification-code" data-length="6">
                                    <input type="text" class="form-control form-control-lg text-center"
                                        maxlength="1" inputmode="numeric" oninput="moveFocus(this, event)">
                                    <input type="text" class="form-control form-control-lg text-center"
                                        maxlength="1" inputmode="numeric" oninput="moveFocus(this, event)">
                                    <input type="text" class="form-control form-control-lg text-center"
                                        maxlength="1" inputmode="numeric" oninput="moveFocus(this, event)">
                                    <input type="text" class="form-control form-control-lg text-center"
                                        maxlength="1" inputmode="numeric" oninput="moveFocus(this, event)">
                                    <input type="text" class="form-control form-control-lg text-center"
                                        maxlength="1" inputmode="numeric" oninput="moveFocus(this, event)">
                                    <input type="text" class="form-control form-control-lg text-center"
                                        maxlength="1" inputmode="numeric" oninput="moveFocus(this, event)">
                                </div>

                                <input class="form-control" name="otp" id="otp" type="hidden"
                                    value="" />

                                {{-- <input class="form-control" name="otp" id="otp" type="text"
                                    placeholder="Kode OTP" autocomplete="off" /> --}}
                                {{-- <p class="text-body-secondary mb-0"><a
                                    href="./sign-up.html">Lupa password?</a> --}}
                                </p>
                            </div>

                            @if ($errors->has('otp'))
                                <p class="text-danger">{{ $errors->first('otp') }}</p>
                            @endif
                            <button class="btn btn-primary w-100" type="submit">Verifikasi</button>
                        </form>
                        <p class="text-body-secondary mb-0">Belum menerima kode otp? <a href="javascript:void(0)"
                                id="resendOtp">Kirim ulang</a>


                            <!-- Text -->
                            {{-- <p class="text-body-secondary mb-0">Nomor salah? <a
                            href="#!">Ganti</a>
                    </p> --}}
                    </div>
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function moveFocus(currentInput, event) {
            // Jika input berisi angka, fokus akan berpindah ke input berikutnya
            if (currentInput.value.length === currentInput.maxLength) {
                let nextInput = currentInput.nextElementSibling;
                if (nextInput && nextInput.tagName === "INPUT") {
                    nextInput.focus();
                }
            }
            // Jika tombol backspace ditekan dan input kosong, fokus akan pindah ke input sebelumnya
            if (event.key === 'Backspace' && currentInput.value === "") {
                let prevInput = currentInput.previousElementSibling;
                if (prevInput && prevInput.tagName === "INPUT") {
                    prevInput.focus();
                }
            }
        }

        // Menggabungkan semua input ke dalam satu nilai
        document.querySelector('.hstack').addEventListener('input', function() {
            let otp = '';
            let inputs = document.querySelectorAll('.hstack input');
            inputs.forEach(function(input) {
                otp += input.value;
            });
            document.getElementById('otp').value = otp;
        });

        $('#resendOtp').on('click', function() {
            Swal.fire({
                title: 'Kirim ulang OTP?',
                text: "Kami akan mengirim ulang kode ke nomor kamu.",
                icon: 'question',
                showCancelButton: true,
                reverseButtons: !0,
                confirmButtonText: 'Ya, kirim ulang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/resend-otp',
                        method: 'POST',
                        data: {
                            whatsapp: $('#whatsapp').val(),
                            _token: '{{ csrf_token() }}',
                        },
                        beforeSend: function() {
                            $('#resendOtp').text('Mengirim ulang...');
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                confirmButtonText: 'OK'
                            });
                            $('#resendOtp').text('Kirim ulang');
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal mengirim ulang OTP',
                                confirmButtonText: 'OK'
                            });
                            $('#resendOtp').text('Kirim ulang');
                        }
                    });
                }
            });
        });
    </script>


</body>

</html>
