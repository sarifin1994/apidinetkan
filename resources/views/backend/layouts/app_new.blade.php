<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="Radiusqu">
    <meta property="og:description"
        content="Solusi Terpadu untuk Radius, Billing, Notifikasi, Pembayaran, hingga OLT ZTE dalam Satu Platform.">
    <meta property="og:image" content="{{ asset('assets/images/radiusqu_white.png') }}">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/choices.js/1.1.6/styles/css/choices.min.css" integrity="sha512-/PTsSsk4pRsdHtqWjRuAL/TkYUFfOpdB5QDb6OltImgFcmd/2ZkEhW/PTQSayBKQtzjQODP9+IAeKd7S2yTXtA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <!-- Animation css -->
    <link href="{{ asset('assets_new/vendor/animation/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_new/vendor/fontawesome/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_new/vendor/leafletmaps/leaflet.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">

    <!--flag Icon css-->
    <link href="{{ asset('assets_new/vendor/flag-icons-master/flag-icon.css') }}" rel="stylesheet" type="text/css">

    <!-- tabler icons-->
    <link href="{{ asset('assets_new/vendor/tabler-icons/tabler-icons.css') }}" rel="stylesheet" type="text/css">

    <!-- apexcharts css-->

    <!-- glight css -->
    <link href="{{ asset('assets_new/vendor/glightbox/glightbox.min.css') }}" rel="stylesheet">

    <!-- Bootstrap css-->
    <link href="{{ asset('assets_new/vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <!-- simplebar css-->
    <link href="{{ asset('assets_new/vendor/simplebar/simplebar.css') }}" rel="stylesheet" type="text/css">

    <!-- App css-->
    <link href="{{ asset('assets_new/css/style.css') }}" rel="stylesheet" type="text/css">

    <!-- Responsive css-->
    <link href="{{ asset('assets_new/css/responsive.css') }}" rel="stylesheet" type="text/css">
    <link href=" {{ asset('assets_new/vendor/datatable/jquery.dataTables.min.css') }}" rel="stylesheet"
        type="text/css">
    <!--  slick css-->
    <link href="{{ asset('assets_new/vendor/slick/slick.css') }}" rel="stylesheet">

    <link href="{{ asset('assets_new/vendor/slick/slick-theme.css') }}" rel="stylesheet">

    <!-- <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" /> -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}" />

    <style>
        #whatsapp-bubble {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25d366;
            color: white;
            font-size: 24px;
            padding: 15px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 9999;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #whatsapp-chat {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 260px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 9999;
            font-family: sans-serif;
        }

        #whatsapp-chat .header {
            background-color: #25d366;
            color: white;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #whatsapp-chat .body {
            padding: 10px;
            font-size: 14px;
        }

        #whatsapp-chat .btn-chat {
            background-color: #25d366;
            color: white;
            display: block;
            text-align: center;
            padding: 8px;
            margin-top: 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .close-chat {
            cursor: pointer;
            font-size: 18px;
        }

        .table td,
        .table th {
            /* white-space: normal !important; */
            user-select: text !important;
        }
    </style>
    <style>
        #map {
            width: 100%;
            height: 500px;
            /* Pastikan tinggi cukup besar */
        }

        #map_edit {
            width: 100%;
            height: 500px;
            /* Pastikan tinggi cukup besar */
        }
    </style>
    <link href="{{ asset('assets_new/vendor/select/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="app-wrapper">

        <div class="loader-wrapper">
            <div class="loader_24"></div>
        </div>
        @if(is_dedicated() == true)
        @include('backend.layouts.navbar_ex_dedicated')
        @endif

        @if(is_dedicated() == false)
        @include('backend.layouts.navbar_new')
        @endif
        <div class="app-content">
            <div class="">

                <!-- Header Section starts -->
                <header class="header-main">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-8 col-sm-6 d-flex align-items-center header-left p-0">
                                <span class="header-toggle ">
                                    <i class="ph ph-squares-four"></i>
                                </span>

                                <div class="header-searchbar w-100">
                                    <form action="#" class="mx-sm-3 app-form app-icon-form ">
                                        <div class="position-relative">
                                            <input class="form-control" disabled id="topnavSearchInput"
                                                type="search"
                                                placeholder="SERVER TIME {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}"
                                                aria-label="Search" aria-describedby="navbarSearch" />
                                            <i class="ti ti-clock text-dark"></i>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="col-4 col-sm-6 d-flex align-items-center justify-content-end header-right p-0">

                                <ul class="d-flex align-items-center">
                                    <li class="header-dark">
                                        <div class="sun-logo head-icon bg-light-dark rounded-circle f-s-22 p-2">
                                            <i class="ph ph-moon-stars"></i>
                                        </div>
                                        <div class="moon-logo head-icon bg-light-dark rounded-circle f-s-22 p-2">
                                            <i class="ph ph-sun-dim"></i>
                                        </div>
                                    </li>

                                    <li class="header-notification">
                                        <a aria-controls="notificationcanvasRight"
                                            class="d-block head-icon position-relative bg-light-dark rounded-circle f-s-22 p-2"
                                            data-bs-target="#notificationcanvasRight" data-bs-toggle="offcanvas"
                                            href="#" role="button">
                                            <i class="ph ph-bell"></i>
                                            <span
                                                class="position-absolute translate-middle p-1 bg-primary border border-light rounded-circle animate__animated animate__fadeIn animate__infinite animate__slower"></span>
                                        </a>
                                        <div aria-labelledby="notificationcanvasRightLabel"
                                            class="offcanvas offcanvas-end header-notification-canvas"
                                            id="notificationcanvasRight" tabindex="-1">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="notificationcanvasRightLabel">
                                                    Notification</h5>
                                                <button aria-label="Close" class="btn-close"
                                                    data-bs-dismiss="offcanvas" type="button"></button>
                                            </div>
                                            <div class="offcanvas-body app-scroll p-0">
                                                <div class="head-container">
                                                    <div class="notification-message head-box">

                                                        <div class="message-content-box flex-grow-1 pe-2">

                                                            <a class="f-s-15 text-dark mb-0"
                                                                href="./read_email.html"><span
                                                                    class="f-w-500 text-dark">Gene Hart</span> wants to
                                                                edit <span
                                                                    class="f-w-500 text-dark">Report.doc</span></a>
                                                            <div>
                                                                <a class="d-inline-block f-w-500 text-success me-1"
                                                                    href="#">Approve</a>
                                                                <a class="d-inline-block f-w-500 text-danger"
                                                                    href="#">Deny</a>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <i class="ph ph-trash f-s-18 text-danger close-btn"></i>
                                                            <div>
                                                                <span class="badge text-light-primary"> sep 23 </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message head-box">

                                                        <div class="message-content-box flex-grow-1 pe-2">
                                                            <a class="f-s-15 text-dark mb-0"
                                                                href="./read_email.html">Hey
                                                                <span class="f-w-500 text-dark">Emery McKenzie</span>,
                                                                get ready: Your order from <span
                                                                    class="f-w-500 text-dark">@Shopper.com</span></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <i class="ph ph-trash f-s-18 text-danger close-btn"></i>
                                                            <div>
                                                                <span class="badge text-light-primary"> sep 23 </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message head-box">
                                                        <div class="message-content-box flex-grow-1 pe-2">
                                                            <a class="f-s-15 text-dark mb-0"
                                                                href="./read_email.html"><span
                                                                    class="f-w-500 text-dark">Simon Young</span> shared
                                                                a file called <span
                                                                    class="f-w-500 text-dark">Dropdown.pdf</span></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <i class="ph ph-trash f-s-18 text-danger close-btn"></i>
                                                            <div>
                                                                <span class="badge text-light-primary"> 30 min</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message head-box">
                                                        <div class="message-content-box flex-grow-1 pe-2">
                                                            <a class="f-s-15 text-dark mb-0"
                                                                href="./read_email.html"><span
                                                                    class="f-w-500 text-dark">Becky G. Hayes</span> has
                                                                added a comment to <span
                                                                    class="f-w-500 text-dark">Final_Report.pdf</span></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <i class="ph ph-trash f-s-18 text-danger close-btn"></i>
                                                            <div>
                                                                <span class="badge text-light-primary"> 45 min</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message head-box">
                                                        <div class="message-content-box  flex-grow-1 pe-2">
                                                            <a class="f-s-15 text-dark mb-0 "
                                                                href="./read_email.html"><span
                                                                    class="f-w-600 text-dark">@Romaine</span>
                                                                invited you to a meeting
                                                            </a>
                                                            <div>
                                                                <a class="d-inline-block f-w-500 text-success me-1"
                                                                    href="#">Join</a>
                                                                <a class="d-inline-block f-w-500 text-danger"
                                                                    href="#">Decline</a>
                                                            </div>

                                                        </div>
                                                        <div class="text-end">
                                                            <i class="ph ph-trash f-s-18 text-danger close-btn"></i>
                                                            <div>
                                                                <span class="badge text-light-primary"> 1 hour ago
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="hidden-massage py-4 px-3">
                                                        <div>
                                                            <i
                                                                class="ph-duotone  ph-bell-ringing f-s-50 text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Notification Not Found</h6>
                                                            <p class="text-dark">When you have any notifications added
                                                                here,will
                                                                appear here.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </header>
                <!-- Header Section ends -->
                <main>
                    @yield('main')
                </main>

                <!-- tap on top -->
                <div class="go-top" style="margin-bottom: 20px">
                    <span class="progress-value">
                        <i class="ti ti-chevron-up"></i>
                    </span>
                </div>

                <!-- Footer Section starts-->
                <footer>
                    <!-- Floating Chat Button -->
                    <div id="whatsapp-chat">
                        <div class="header">
                            <strong>Butuh Bantuan?</strong>
                            <span class="close-chat" onclick="toggleChat()">×</span>
                        </div>
                        <div class="body">
                            <p>Hi! Klik tombol di bawah ini untuk chat dengan CS kami via WhatsApp.</p>
                            <a href="https://wa.me/62816600661" target="_blank" class="btn-chat">Chat Sekarang</a>
                        </div>
                    </div>

                    <!-- Floating Bubble -->
                    <div id="whatsapp-bubble" onclick="toggleChat()" style="margin-bottom: 30px">
                        <!-- <span class="material-symbols-outlined">
                        support_agent
                        </span> -->
                        <i class="fa-solid fa-headset"></i>
                    </div>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-9 col-12">
                                <p class="footer-text f-w-600 mb-0">Copyright &copy; {{ date('Y') }}
                                    {{ config('app.name') }}. All Rights Reserved.
                                </p>
                            </div>
                            <div class="col-md-3">
                                <div class="footer-text text-end">
                                    <a class="f-w-500 text-primary" style="font-size: 12px;"
                                        href="{{ env('APP_URL') }}" target="_blank"> PT. Putra Garsel
                                        Interkoneksi</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>

            </div>
        </div>
        <!-- Footer Section ends-->
    </div>

    <!--customizer-->
    <div id="customizer"></div>

    <!-- latest jquery-->
    <script src="{{ asset('assets_new/js/jquery-3.6.3.min.js') }}"></script>

    <!-- Bootstrap js-->
    <script src="{{ asset('assets_new/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    <!-- Simple bar js-->
    <script src="{{ asset('assets_new/vendor/simplebar/simplebar.js') }}"></script>
    <script src="{{ asset('assets_new/vendor/fullcalendar/global.js') }}"></script>

    <script src="{{ asset('assets_new/vendor/slick/slick.min.js') }}"></script>

    <!-- phosphor js -->
    <script src="{{ asset('assets_new/vendor/phosphor/phosphor.js') }}"></script>

    <!-- Glight js -->
    <script src="{{ asset('assets_new/vendor/glightbox/glightbox.min.js') }}"></script>

    <!-- apexcharts-->
    {{-- <script src="{{ asset('assets_new/vendor/apexcharts/apexcharts.min.js') }}"></script> --}}

    <!-- Customizer js-->
    <script src="{{ asset('assets_new/js/customizer.js') }}"></script>

    <!-- Ecommerce js-->
    <!-- <script src="{{ asset('assets_new/js/ecommerce_dashboard.js') }}"></script> -->
    <script src="{{ asset('assets_new/js/timeline.js') }}"></script>
    <!-- App js-->
    <script src="{{ asset('assets_new/js/script.js') }}"></script>

    {{-- <script src=" {{ asset('assets_new/js/chart.js') }}"></script> --}}
    <script src=" {{ asset('assets_new/vendor/datatable/jquery.dataTables.min.js') }}"></script>

    <!-- sweetalert js-->
    <script src="{{ asset('assets_new/vendor/sweetalert/sweetalert.js') }}"></script>

    <!-- js -->
    <script src="{{ asset('assets_new/js/sweet_alert.js') }}"></script>
    <!-- select2 -->
    <script src="{{ asset('assets_new/vendor/select/select2.min.js') }}"></script>

    <!--js-->
    <script src="{{ asset('assets_new/js/select.js') }}"></script>
    <!-- fullcalendar js -->


    <!-- slick-file -->

    <!-- phosphor js -->
    <!--  Leaflet Maps plugins -->
    <script src="{{ asset('assets_new/vendor/leafletmaps/leaflet.js') }}"></script>

    <script src="{{ asset('assets_new/js/leaflet-map.js') }}"></script>
    <!-- calendar js -->
    <script src="{{ asset('assets_new/js/calendar.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/choices.js/1.1.6/choices.min.js"
        integrity="sha512-7PQ3MLNFhvDn/IQy12+1+jKcc1A/Yx4KuL62Bn6+ztkiitRVW1T/7ikAh675pOs3I+8hyXuRknDpTteeptw4Bw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>     -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <!-- App js-->
    <script>
        // Create a Date object using an ISO string from the server's current time
        var serverTime = new Date("{{ \Carbon\Carbon::now()->toIso8601String() }}");

        // Function to update the input field's value every second
        function updateTime() {
            serverTime.setSeconds(serverTime.getSeconds() + 1);

            var day = ("0" + serverTime.getDate()).slice(-2);
            var month = ("0" + (serverTime.getMonth() + 1)).slice(-2);
            var year = serverTime.getFullYear();
            var hours = ("0" + serverTime.getHours()).slice(-2);
            var minutes = ("0" + serverTime.getMinutes()).slice(-2);
            var seconds = ("0" + serverTime.getSeconds()).slice(-2);

            var formattedTime = day + "/" + month + "/" + year + " " + hours + ":" + minutes + ":" + seconds;

            document.getElementById('topnavSearchInput').value = "SERVER TIME " + formattedTime;
        }

        setInterval(updateTime, 1000);
    </script>

    <script>
        function toggleChat() {
            const chat = document.getElementById('whatsapp-chat');
            chat.style.display = chat.style.display === 'none' || chat.style.display === '' ? 'block' : 'none';
        }
    </script>

    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="{{ asset('assets/leaflet/leaflet.css') }}">
    <!-- Leaflet JS -->
    <script src="{{ asset('assets/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    @yield('js')
    @stack('scripts')
</body>

</html>
