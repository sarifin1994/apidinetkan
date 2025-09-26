<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="Radiusqu" />
    <meta name="author" content="Radiusqu Team" />
      <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{env('APP_URL')}}">
  <meta property="og:title" content="Radiusqu">
  <meta property="og:description" content="Solusi Terpadu untuk Radius, Billing, Notifikasi, Pembayaran, hingga OLT ZTE dalam Satu Platform.">
  <meta property="og:image" content="{{ asset('assets/images/radiusqu_white.png') }}">
    <title>@yield('title') | {{ config('app.name') }}</title>

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
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <link href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
    #map {
    width: 100%;
    height: 500px; /* Pastikan tinggi cukup besar */
    }
    #map_edit {
    width: 100%;
    height: 500px; /* Pastikan tinggi cukup besar */
    }
    </style>

    <style>
        #sidebar {
            transition: width 0.3s ease;
        }

        /* Ukuran “Base”, “Medium” & “Small” */
        #sidebar.sidenav-base {
            width: 250px;
        }

        #sidebar.sidenav-md {
            width: 200px;
        }

        #sidebar.sidenav-sm {
            width: 80px;
        }

        /* Tombol WhatsApp Modern di Pojok Kanan Bawah */
        .whatsapp-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            background-color: #25D366;
            padding: 10px;
            border-radius: 10%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .whatsapp-button i {
            color: white;
            font-size: 30px;
            transition: transform 0.3s ease;
        }

        .whatsapp-button:hover {
            background-color: #128C7E;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .whatsapp-button:hover i {
            transform: scale(1.1);
        }

       /* Footer Style */
footer {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    padding: 20px;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

footer .container-fluid {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    padding-left: 30px; /* Add padding to prevent text from being too close to the sidebar */
    padding-right: 30px; /* Add padding to the right side as well */
}

footer .container-fluid .left {
    flex: 1;
    text-align: left;
    font-size: 14px;
    /* margin-right: 30px; */
    margin-left: 180px; /* Add left padding to prevent cutting off */
}

footer .container-fluid .right {
    text-align: right;
    font-size: 14px;
}

footer .container-fluid .right a {
    color: #333;
    text-decoration: none;
    font-weight: bold;
}

footer .container-fluid .right a:hover {
    text-decoration: underline;
}

/* Responsiveness for smaller screens */
@media (max-width: 768px) {
    footer .container-fluid {
        flex-direction: column;
        text-align: center;
    }

    footer .container-fluid .left,
    footer .container-fluid .right {
        margin: 0;
        font-size: 12px;
        margin-top: 10px;
    }

    footer .container-fluid .left {
        margin-bottom: 10px;
    }

    footer .container-fluid .right {
        font-size: 13px;
    }

    footer .container-fluid .right a {
        font-size: 13px;
    }
}
    </style>

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
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        #whatsapp-chat {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 260px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
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

    </style>

</head>

<body>
    @include('backend.layouts.navbar')

    <main class="main px-lg-6">
        @yield('main')
    </main>

    <!-- Footer Section -->
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
        <div id="whatsapp-bubble" onclick="toggleChat()">
            <span class="material-symbols-outlined">
            support_agent
            </span>
        </div>

        <div class="container-fluid">
            <div class="left">
                <p style="font-weight: bold;">&copy; {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.</p>
            </div>
            <div class="right">
                <p>Powered by <a href="{{env('APP_URL')}}" target="_blank">PT. Putra Garsel Interkoneksi</a></p>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/js/vendor.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/theme.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script> -->
     <!-- Leaflet CSS -->
    <link rel="stylesheet" href="{{ asset('assets/leaflet/leaflet.css') }}">
    <!-- Leaflet JS -->
    <script src="{{ asset('assets/leaflet/leaflet.js') }}"></script>


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


    @yield('js')
    @stack('scripts')

</body>

</html>
