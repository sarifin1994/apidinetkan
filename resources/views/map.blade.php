<!DOCTYPE html>
<html>
<head>
    <title>Network Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        #map {
            height: 100vh;
            width: 100%;
        }

        /* Animasi garis putus-putus */
        .animated-line {
            stroke-dasharray: 10, 10;     /* panjang dash, panjang spasi */
            animation: dashmove 1s linear infinite;
        }

        @keyframes dashmove {
            to {
                stroke-dashoffset: -20;   /* geser dash ke kiri */
            }
        }
    </style>
</head>
<body>
<div id="map"></div>

<script>
    var map = L.map('map').setView([-6.5, 107.0], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markers = {};
    var polylines = [];

    function renderRouters(routers) {
        // Hapus garis lama
        polylines.forEach(line => map.removeLayer(line));
        polylines = [];

        routers.forEach(router => {
            // Jika marker belum ada → buat
            if (!markers[router.id]) {
                markers[router.id] = L.marker([router.latitude, router.longitude]).addTo(map);
            }

            // Update tooltip
            markers[router.id].bindTooltip(
                "<b>" + router.name + "</b><br>" +
                "IP: " + router.ip_address + "<br>" +
                "Lat: " + router.latitude + "<br>" +
                "Lng: " + router.longitude + "<br>" +
                "Status: <span style='color:" +
                (router.status.status === 'online' ? 'green' : 'red') + "'>" +
                router.status.status + "</span>"
            );

            // Update popup
            var popupContent = "<h4>" + router.name + "</h4>" +
                "IP: " + router.ip_address + "<br>" +
                "Lat: " + router.latitude + "<br>" +
                "Lng: " + router.longitude + "<br>" +
                "Status: <span style='color:" +
                (router.status.status === 'online' ? 'green' : 'red') + "'>" +
                router.status.status + "</span><br>";

            if (router.status.status === 'online') {
                popupContent += "CPU Load: " + router.status.cpu_load + "%<br>" +
                                "Free Memory: " + router.status.free_memory + "<br>" +
                                "Uptime: " + router.status.uptime + "<br>";
            }

            markers[router.id].bindPopup(popupContent);

            // Garis ke parent
            if (router.parent_id) {
                var parent = routers.find(r => r.id === router.parent_id);
                if (parent) {
                    var color = (router.status.status === 'online') ? 'green' : 'red';
                    var line = L.polyline([
                        [router.latitude, router.longitude],
                        [parent.latitude, parent.longitude]
                    ], {
                        color: color,
                        weight: 3,
                        className: 'animated-line' // kasih class animasi
                    }).addTo(map);
                    polylines.push(line);
                }
            }
        });
    }

    // Load pertama
    fetch('/api/routers')
        .then(res => res.json())
        .then(data => renderRouters(data));

    // Auto refresh tiap 10 detik
    setInterval(() => {
        fetch('/api/routers')
            .then(res => res.json())
            .then(data => renderRouters(data));
    }, 10000);
</script>
</body>
</html>
