<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pusatkan Lokasi User</title>

    <!-- MapLibre CSS -->
    <link href="https://unpkg.com/maplibre-gl@3.6.1/dist/maplibre-gl.css" rel="stylesheet"/>

    <style>
        #map {
            width: 100%;
            height: 400px;
            border-radius: 10px;
        }
        .btn-loc {
            margin-top: 10px;
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-loc:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<h3>Pilih Lokasi Pengiriman</h3>

<button class="btn-loc" onclick="centerUserLocation()">Pusatkan Lokasi Saya</button>

<div id="map"></div>

<br>

Latitude: <input type="text" id="lat" readonly>
Longitude: <input type="text" id="lng" readonly>

<!-- MapLibre JS -->
<script src="https://unpkg.com/maplibre-gl@3.6.1/dist/maplibre-gl.js"></script>

<script>
    // Lokasi default di Jember
    let defaultJember = [113.687, -8.172];

    // Inisialisasi map
    var map = new maplibregl.Map({
        container: 'map',
        style: 'https://tiles.openfreemap.org/styles/liberty',
        center: defaultJember,
        zoom: 13
    });

    // Marker
    var marker = new maplibregl.Marker({ draggable: true, color: "#1E90FF" })
        .setLngLat(defaultJember)
        .addTo(map);

    // Input update
    function updateInputs(lngLat) {
        document.getElementById('lat').value = lngLat.lat;
        document.getElementById('lng').value = lngLat.lng;
    }

    updateInputs({ lat: defaultJember[1], lng: defaultJember[0] });

    marker.on('dragend', () => updateInputs(marker.getLngLat()));

    map.on('click', e => {
        marker.setLngLat(e.lngLat);
        updateInputs(e.lngLat);
    });

    // ================================
    // FUNGSI MEMUSATKAN KE LOKASI USER
    // ================================
    function centerUserLocation() {
        if (!navigator.geolocation) {
            alert("Browser tidak mendukung GPS.");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            pos => {
                let userLng = pos.coords.longitude;
                let userLat = pos.coords.latitude;

                // Pindah map ke lokasi user
                map.flyTo({
                    center: [userLng, userLat],
                    zoom: 15,
                    essential: true
                });

                // Pindahkan marker
                marker.setLngLat([userLng, userLat]);

                updateInputs({ lat: userLat, lng: userLng });
            },
            err => {
                alert("Tidak bisa mendapatkan lokasi. Pastikan GPS aktif.");
            }
        );
    }
</script>

</body>
</html>
