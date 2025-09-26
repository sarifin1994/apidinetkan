<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="{{ asset('assets/images/logo_new.png') }}" />
    <meta name="author" content="Radiusqu Team" />
    <title>Register | {{ config('app.name') }}</title>
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

        <!-- Pastikan sudah menyertakan Material Symbols Outlined -->
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">


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
        <div class="col-lg-6 col-md-12 text-center d-flex justify-content-center align-items-center d-none d-md-flex" style="height: 100vh; background-color: #F0F4FF">
            <div style="width: 100%; max-width: 240px; height: auto;">
                <img src="{{ asset('assets/images/radiusqu.png') }}" 
                     style="width: 100%; height: auto; object-fit: contain;" 
                     class="img-fluid" alt="Logo">
            </div>
        </div>    
        <div class="col-lg-5 col-md-12 offset-xl-1 d-flex align-items-center py-10" style="max-height: 100vh; overflow-y: auto;">
            <div class="container">
                {{-- <div class="row"> --}}
                <div class="col-12" style="max-width: 25rem">
                    <img src="{{ asset('assets/images/logo-black.png') }}" class="mt-10 mb-7 img-fluid d-block d-md-none" style="max-width: 240px;" alt="Logo">
                    <hr class="d-block d-md-none">
                    <!-- Heading -->
                    <h1 class="fs-4">Daftar</h1>

                    <!-- Subheading -->
                    <p class="lead text-body-secondary">Silakan lengkapi data berikut untuk daftar</p>

                    <!-- Form -->
                    <form action="{{ route('register') }}" method="post" onsubmit="showSpinner(this)" class="mb-5">
                        @csrf

                        <div class="mb-4">
                            <label class="visually-hidden" for="name">Nama Lengkap</label>
                            <input class="form-control" value="{{ old('name') }}" name="name" id="name" type="text"
                                placeholder="Nama Lengkap" autocomplete="off" />
                                @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="visually-hidden" for="email">Email</label>
                            <input class="form-control" value="{{ old('email') }}"name="email" id="email" type="email"
                                placeholder="Email" autocomplete="off" />
                                @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="visually-hidden" for="whatsapp">Nomor Whatsapp</label>
                            <input class="form-control" value="{{ old('whatsapp') }}"name="whatsapp" id="whatsapp" type="number"
                                placeholder="Nomor Whatsapp" autocomplete="off" />
                                @error('whatsapp')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="visually-hidden" for="username">Username</label>
                            <input class="form-control" value="{{ old('username') }}"name="username" id="username" type="text"
                                placeholder="Username" autocomplete="off" />
                            @error('username')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="visually-hidden" for="password">Password</label>
                            <input class="form-control" name="password" id="password" type="password"
                                placeholder="Password" autocomplete="off" />
                                @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            
                        </div>
                        <div class="mb-4">
                            <label class="visually-hidden" for="password_confirmation">Confirm Password</label>
                            <input class="form-control" name="password_confirmation" id="password_confirmation"
                                type="password" placeholder="Konfirmasi Password" autocomplete="off" />
                        </div>

                        {{-- <div class="mb-4">
                            <input type="checkbox" class="form-check-input" checked> <span class="mr-1">Saya setuju dengan </span>
                            <span class="text-orange"><a href="https://radiusqu.com/syarat-dan-ketentuan" target="_blank">syarat dan ketentuan</a></span>
                        </div> --}}

                        @if (session('error'))
                            <div class="alert alert-danger">
                                <b>Opps!</b> {{ session('error') }}
                            </div>
                        @endif
                        
                        <button class="btn btn-primary w-100" type="submit" id="daftarBtn">Daftar</button>
                    </form>

                    <!-- Text -->
                    <p class="text-body-secondary mb-0">Sudah punya akun? <a href="/auth">Login</a>
                    </p>
                </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>

    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>

    <!-- Vendor JS -->
    <script src="../assets/js/vendor.bundle.js"></script>

    <!-- Theme JS -->
    <script src="../assets/js/theme.bundle.js"></script>

    <script>
        function showSpinner(form) {
            var daftarBtn = form.querySelector('#daftarBtn');
            daftarBtn.disabled = true;
            daftarBtn.innerHTML =
                'Daftar <span class="material-symbols-outlined spinning" style="vertical-align: middle;">progress_activity</span>';
        }
    </script>
</body>

</html>
