document.addEventListener('DOMContentLoaded', () => {

    // ========================================================
    // CONFIG
    // ========================================================

    const MAX_DISTANCE_KM = 3;

    const DEFAULT_CENTER = [-0.502, 117.153];
    const DEFAULT_ZOOM = 12;
    const USER_ZOOM = 15;

    // GPS hanya dianggap berubah kalau berpindah minimal 20 meter
    const LOCATION_UPDATE_THRESHOLD_METERS = 20;

    // Toleransi snap koordinat ke jalan
    const SNAP_TOLERANCE_METERS = 150;

    // ---- Pengaturan U-turn ----

    // Rute tanpa U-turn boleh lebih panjang maksimal sekian kali dari rute ber-U-turn
    const MAX_DETOUR_RATIO = 1.8;

    // ...ditambah maksimal sekian meter (supaya jarak pendek tetap fleksibel)
    const MAX_DETOUR_EXTRA_METERS = 500;

    // U-turn dianggap wajar kalau terjadi dalam jarak segini dari tujuan
    const UTURN_OK_NEAR_DESTINATION_METERS = 150;

    // Heading GPS hanya dipercaya kalau user bergerak minimal segini (m/s)
    const MIN_SPEED_FOR_HEADING = 1;


    // ========================================================
    // MAP CONTAINER
    // ========================================================

    const mapContainer = document.getElementById('map');

    if (!mapContainer) {
        console.error('Element #map tidak ditemukan.');
        return;
    }


    // ========================================================
    // LOCATION MESSAGE
    // ========================================================

    function showLocationMessage(message) {
        const locationText = document.getElementById('user-location-text');
        if (locationText) {
            locationText.innerHTML = message;
        }
    }


    // ========================================================
    // VALIDATE DATA
    // ========================================================

    if (!Array.isArray(window.apotekData)) {
        console.error('window.apotekData tidak ditemukan atau bukan array.');
        showLocationMessage('⚠️ Data apotek tidak dapat dimuat.');
        return;
    }

    console.log('Jumlah data apotek:', window.apotekData.length);


    // ========================================================
    // MAP
    // ========================================================

    const map = L.map('map', {
        center: DEFAULT_CENTER,
        zoom: DEFAULT_ZOOM,
        closePopupOnClick: true
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);


    // ========================================================
    // POPUP STYLE (COMPACT)
    // ========================================================

    const popupStyle = document.createElement('style');

    popupStyle.textContent = `
        .leaflet-popup {
            margin-bottom: 14px;
        }

        .leaflet-popup-content-wrapper {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .18);
            overflow: hidden;
        }

        .leaflet-popup-content {
            width: 215px !important;
            max-width: calc(100vw - 50px);
            margin: 8px 10px;
            box-sizing: border-box;
        }

        .leaflet-popup-tip {
            box-shadow: 1px 1px 3px rgba(0, 0, 0, .08);
        }

        .leaflet-popup-content > div {
            width: 100%;
            max-height: 215px;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
        }

        .leaflet-popup-content > div::-webkit-scrollbar {
            width: 3px;
        }

        .leaflet-popup-content > div::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .leaflet-popup-content img {
            width: 100%;
            height: 78px;
            object-fit: cover;
            border-radius: 7px;
            display: block;
            margin-bottom: 7px;
        }

        @media (max-width: 480px) {
            .leaflet-popup-content {
                width: 205px !important;
                max-width: calc(100vw - 42px);
                margin: 8px 9px;
            }

            .leaflet-popup-content > div {
                max-height: 200px;
            }

            .leaflet-popup-content img {
                height: 72px;
            }
        }
    `;

    document.head.appendChild(popupStyle);


    // ========================================================
    // REFRESH MAP SIZE
    // ========================================================

    function refreshMapSize() {
        map.invalidateSize();
    }

    setTimeout(refreshMapSize, 200);

    window.addEventListener('resize', refreshMapSize);

    window.addEventListener('orientationchange', () => {
        setTimeout(refreshMapSize, 300);
    });


    // ========================================================
    // STATE
    // ========================================================

    const markers = {};

    const routeState = {
        routeLayerRef: {
            current: null
        }
    };

    // Lokasi user terakhir
    let userLocation = null;

    // Marker lokasi user
    let userMarker = null;

    // ID GPS watcher
    let locationWatchId = null;

    // Posisi GPS yang terakhir diproses
    let lastCalculatedLocation = null;

    // Mencegah proses lokasi bertumpuk
    let isUpdatingRecommendation = false;

    // Arah hadap user dari GPS (derajat, 0-359). null = tidak diketahui
    let lastHeading = null;

    // ========================================================
    // MANUAL ROUTE STATE
    // ========================================================
    //
    // null     : belum ada pilihan manual
    // ada ID   : user memilih apotek tertentu
    //
    // Selama ada ID, GPS TIDAK akan mengganti tujuan
    // menjadi apotek terdekat.
    //

    let selectedRouteId = null;


    // ========================================================
    // MARKER ICON
    // ========================================================

    const markerIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    const userIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });


    // ========================================================
    // ESCAPE HTML
    // ========================================================

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    // ========================================================
    // HAVERSINE DISTANCE (km)
    // ========================================================

    function calculateDistanceHaversine(lat1, lon1, lat2, lon2) {
        const R = 6371;

        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c;
    }


    // ========================================================
    // RANK APOTEK
    // ========================================================

    function rankByHaversine(userLat, userLng, destinations) {
        const results = destinations.map((destination) => ({
            id: destination.id,
            distanceKm: calculateDistanceHaversine(
                userLat,
                userLng,
                destination.lat,
                destination.lng
            )
        }));

        const sorted = results
            .slice()
            .sort((a, b) => a.distanceKm - b.distanceKm);

        return {
            results: results,
            sorted: sorted,
            nearest: sorted.length > 0 ? sorted[0] : null
        };
    }


    // ========================================================
    // OSRM TABLE (jarak jalan)
    // ========================================================

    async function getRoadDistances(userLat, userLng, destinations) {

        if (!destinations || destinations.length === 0) {
            return [];
        }

        const coordsList = [userLng + ',' + userLat].concat(
            destinations.map((d) => d.lng + ',' + d.lat)
        );

        const coordsStr = coordsList.join(';');

        const destIdx = destinations
            .map((_, index) => index + 1)
            .join(';');

        const url =
            'https://router.project-osrm.org/table/v1/driving/' +
            coordsStr +
            '?sources=0&destinations=' +
            destIdx +
            '&annotations=distance,duration';

        const response = await fetch(url);

        if (!response.ok) {
            throw new Error('OSRM request failed: ' + response.status);
        }

        const data = await response.json();

        if (data.code !== 'Ok') {
            throw new Error('OSRM error: ' + data.code);
        }

        const distances =
            data.distances && data.distances[0]
                ? data.distances[0]
                : [];

        const destWaypoints = data.destinations || [];

        return destinations.map((destination, index) => {

            const distMeters = distances[index];

            if (distMeters === null || distMeters === undefined) {
                return {
                    id: destination.id,
                    distanceKm: null,
                    unreachable: true,
                    snapOffsetM: null,
                    suspicious: false
                };
            }

            let snapOffsetM = null;

            const waypoint = destWaypoints[index];

            if (waypoint && waypoint.location) {
                snapOffsetM =
                    calculateDistanceHaversine(
                        destination.lat,
                        destination.lng,
                        waypoint.location[1],
                        waypoint.location[0]
                    ) * 1000;
            }

            const suspicious =
                snapOffsetM !== null &&
                snapOffsetM > SNAP_TOLERANCE_METERS;

            return {
                id: destination.id,
                distanceKm: distMeters / 1000,
                unreachable: false,
                snapOffsetM: snapOffsetM,
                suspicious: suspicious
            };
        });
    }


    // ========================================================
    // COMPACT POPUP
    // ========================================================

    function buildPopupContent(apotek) {

        const nama = escapeHtml(apotek.nama || '-');
        const alamat = escapeHtml(apotek.alamat || '-');
        const telepon = escapeHtml(apotek.telepon || '');
        const jam = escapeHtml(apotek.jam_operasional || '');
        const jenisLayanan = escapeHtml(apotek.jenis_layanan || '');

        const imageHtml = apotek.gambar
            ? `
                <img
                    src="uploads/${escapeHtml(apotek.gambar)}"
                    alt="${nama}"
                    style="
                        width:100%;
                        height:78px;
                        object-fit:cover;
                        border-radius:7px;
                        display:block;
                        margin-bottom:7px;
                    "
                >
            `
            : `
                <div
                    style="
                        width:100%;
                        height:65px;
                        background:#f5f6f8;
                        border:1px dashed #d9dce1;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#9ca3af;
                        font-size:11px;
                        border-radius:7px;
                        margin-bottom:7px;
                        box-sizing:border-box;
                    "
                >
                    📷 Belum ada gambar
                </div>
            `;

        return `
            <div
                style="
                    width:100%;
                    box-sizing:border-box;
                    font-family:Arial,sans-serif;
                    color:#1f2937;
                "
            >
                ${imageHtml}

                <div
                    style="
                        font-size:13px;
                        font-weight:700;
                        line-height:1.35;
                        margin-bottom:4px;
                        color:#111827;
                    "
                >
                    ${nama}
                </div>

                <div
                    style="
                        font-size:10.5px;
                        line-height:1.45;
                        color:#6b7280;
                        margin-bottom:5px;
                    "
                >
                    📍 ${alamat}
                </div>

                ${telepon ? `
                    <div style="font-size:10.5px;color:#4b5563;margin-bottom:3px;">
                        📞 ${telepon}
                    </div>
                ` : ''}

                ${jam ? `
                    <div style="font-size:10.5px;color:#4b5563;margin-bottom:3px;">
                        🕒 ${jam}
                    </div>
                ` : ''}

                ${jenisLayanan ? `
                    <div
                        style="
                            display:inline-block;
                            margin-top:3px;
                            padding:3px 7px;
                            border-radius:12px;
                            background:#eef2ff;
                            color:#4f46e5;
                            font-size:9px;
                            font-weight:600;
                        "
                    >
                        ${jenisLayanan}
                    </div>
                ` : ''}

                <div
                    id="distance-${apotek.id}"
                    style="margin-top:5px;font-size:10px;color:#6b7280;"
                ></div>
            </div>
        `;
    }


    // ========================================================
    // CREATE MARKERS
    // ========================================================

    window.apotekData.forEach((apotek) => {

        const lat = parseFloat(apotek.latitude);
        const lng = parseFloat(apotek.longitude);

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            console.warn('Koordinat tidak valid:', apotek);
            return;
        }

        const marker = L.marker([lat, lng], { icon: markerIcon })
            .addTo(map)
            .bindPopup(buildPopupContent(apotek), {
                minWidth: 215,
                maxWidth: 235,
                autoPan: true,
                autoPanPaddingTopLeft: [20, 20],
                autoPanPaddingBottomRight: [20, 20],
                closeButton: true,
                keepInView: true
            });

        marker.on('popupopen', function () {
            setTimeout(() => {
                const popup = marker.getPopup();
                if (!popup) return;

                const popupElement = popup.getElement();
                if (!popupElement) return;

                // Pastikan popup tidak keluar viewport
                map.panInside(popupElement, {
                    paddingTopLeft: [15, 15],
                    paddingBottomRight: [15, 15],
                    animate: true
                });
            }, 80);
        });

        markers[apotek.id] = {
            marker: marker,
            lat: lat,
            lng: lng
        };
    });


    // ========================================================
    // CLEAR ROUTE
    // ========================================================

    function clearRoute() {
        if (routeState.routeLayerRef.current) {
            map.removeLayer(routeState.routeLayerRef.current);
            routeState.routeLayerRef.current = null;
        }
    }


    // ========================================================
    // RENDER LIST
    // ========================================================

    function renderList(haversineResults, nearestId) {

        const list = document.getElementById('apotek-list-container');

        if (!list) {
            return;
        }

        const listItems = Array.from(document.querySelectorAll('.apotek-item'));

        let visibleCount = 0;

        listItems.forEach((item) => {

            const id = item.getAttribute('data-id');

            const result = haversineResults.find((data) => data.id == id);

            if (!result) {
                item.style.display = 'none';
                return;
            }

            if (result.distanceKm > MAX_DISTANCE_KM) {
                item.style.display = 'none';
                return;
            }

            item.style.display = '';

            visibleCount++;

            item.setAttribute('data-distance', result.distanceKm);

            // ---- Hapus info jarak lama ----

            const oldDistance = item.querySelector('.distance-info');

            if (oldDistance) {
                oldDistance.remove();
            }

            // ---- Info jarak Haversine ----

            const distance = document.createElement('div');

            distance.className = 'distance-info';

            distance.style.cssText = `
                margin-top:8px;
                padding-top:8px;
                border-top:1px solid #eee;
                font-size:12px;
                font-weight:600;
                color:#667eea;
            `;

            distance.innerHTML = `
                📍 ${result.distanceKm.toFixed(2)} km
                <span style="font-weight:400;color:#999;">(Haversine)</span>
            `;

            item.appendChild(distance);

            // ---- Hapus badge lama ----

            const oldBadge = item.querySelector('.badge-closest');

            if (oldBadge) {
                oldBadge.remove();
            }

            // ---- Badge terdekat ----

            if (result.id == nearestId) {

                item.style.background = '#f0fdf4';
                item.style.borderLeft = '4px solid #22c55e';

                const badge = document.createElement('span');

                badge.className = 'badge-closest';

                badge.style.cssText = `
                    display:inline-flex;
                    align-items:center;
                    gap:4px;
                    background:#22c55e;
                    color:#fff;
                    padding:3px 8px;
                    border-radius:20px;
                    font-size:10px;
                    font-weight:600;
                    margin-left:7px;
                    vertical-align:middle;
                `;

                badge.textContent = '★ TERDEKAT';

                const heading = item.querySelector('h4');

                if (heading) {
                    heading.appendChild(badge);
                }

            } else {
                item.style.background = '';
                item.style.borderLeft = '';
            }
        });

        // ---- Sort ----

        const visibleItems = listItems
            .filter((item) => item.style.display !== 'none')
            .sort((a, b) =>
                parseFloat(a.dataset.distance) -
                parseFloat(b.dataset.distance)
            );

        visibleItems.forEach((item) => {
            list.appendChild(item);
        });

        // ---- Pesan kosong ----

        const oldMessage = document.getElementById('no-apotek-10km');

        if (oldMessage) {
            oldMessage.remove();
        }

        if (visibleCount === 0) {

            const message = document.createElement('div');

            message.id = 'no-apotek-10km';

            message.style.cssText = `
                padding:24px 18px;
                text-align:center;
                color:#6b7280;
                font-size:13px;
                line-height:1.6;
            `;

            message.innerHTML = `
                <div style="font-size:24px;margin-bottom:8px;">📍</div>

                <strong style="display:block;color:#374151;margin-bottom:4px;">
                    Tidak ada apotek terdekat
                </strong>

                <span>
                    Tidak ditemukan apotek dalam radius
                    ${MAX_DISTANCE_KM} km dari lokasi Anda.
                </span>
            `;

            list.appendChild(message);
        }
    }


    // ========================================================
    // U-TURN DETECTION & ROUTE PICKER
    // ========================================================

    function getUturnInfo(route) {

        const steps = route.legs.flatMap((leg) => leg.steps);

        // Jarak yang tersisa ke tujuan pada awal tiap step
        let remaining = route.distance;

        let count = 0;
        let nearDestinationOnly = true;

        steps.forEach((step) => {

            const isUturn =
                step.maneuver &&
                step.maneuver.modifier === 'uturn';

            if (isUturn) {

                count++;

                if (remaining > UTURN_OK_NEAR_DESTINATION_METERS) {
                    nearDestinationOnly = false;
                }
            }

            remaining -= step.distance;
        });

        return {
            count: count,
            nearDestinationOnly: nearDestinationOnly
        };
    }


    function pickBestRoute(routes) {

        const info = routes.map((route) => ({
            route: route,
            ...getUturnInfo(route)
        }));

        // Tercepat dulu
        info.sort((a, b) => a.route.duration - b.route.duration);

        const fastest = info[0];

        // Kondisi 1: rute tercepat memang tanpa U-turn
        if (fastest.count === 0) {
            return fastest.route;
        }

        // Kondisi 2: U-turn hanya di dekat tujuan
        if (fastest.nearDestinationOnly) {
            return fastest.route;
        }

        // Cari alternatif tanpa U-turn
        const noUturn = info.filter((i) => i.count === 0);

        if (noUturn.length > 0) {

            const alt = noUturn[0];

            const maxAllowed =
                fastest.route.distance * MAX_DETOUR_RATIO +
                MAX_DETOUR_EXTRA_METERS;

            // Kondisi 3: alternatif masih masuk akal
            if (alt.route.distance <= maxAllowed) {
                console.log('Rute alternatif tanpa U-turn dipilih.');
                return alt.route;
            }
        }

        // Kondisi 4: tidak ada alternatif / terlalu jauh, U-turn diterima
        console.log('U-turn diterima (tidak ada alternatif yang masuk akal).');
        return fastest.route;
    }


    // ========================================================
    // DRAW ROUTE
    // ========================================================

    async function drawRoute(fromLat, fromLng, toLat, toLng) {

        // Bearing hanya untuk titik awal; titik tujuan dikosongkan
        const bearingParam =
            lastHeading !== null
                ? '&bearings=' + Math.round(lastHeading) + ',60;'
                : '';

        const url =
            'https://router.project-osrm.org/route/v1/driving/' +
            fromLng + ',' + fromLat +
            ';' +
            toLng + ',' + toLat +
            '?overview=full&geometries=geojson&steps=true&alternatives=3' +
            bearingParam;

        const response = await fetch(url);

        if (!response.ok) {
            throw new Error('OSRM route request failed: ' + response.status);
        }

        const data = await response.json();

        if (
            data.code !== 'Ok' ||
            !data.routes ||
            data.routes.length === 0
        ) {
            throw new Error('OSRM route error: ' + data.code);
        }

        // ---- Pilih rute terbaik (hindari U-turn bila masuk akal) ----

        const bestRoute = pickBestRoute(data.routes);

        // ---- Snap check ----

        let snapOffsetM = null;

        const destWaypoint = data.waypoints && data.waypoints[1];

        if (destWaypoint && destWaypoint.location) {
            snapOffsetM =
                calculateDistanceHaversine(
                    toLat,
                    toLng,
                    destWaypoint.location[1],
                    destWaypoint.location[0]
                ) * 1000;
        }

        const suspicious =
            snapOffsetM !== null &&
            snapOffsetM > SNAP_TOLERANCE_METERS;

        // ---- Hapus rute lama ----

        clearRoute();

        // ---- GeoJSON → LatLng ----

        const latlngs = bestRoute.geometry.coordinates.map(
            (c) => [c[1], c[0]]
        );

        // ---- Garis rute utama ----

        const routeLine = L.polyline(
            latlngs,
            suspicious
                ? {
                    color: '#ff9800',
                    weight: 5,
                    opacity: 0.82,
                    dashArray: '8, 8',
                    lineCap: 'round',
                    lineJoin: 'round'
                }
                : {
                    color: '#667eea',
                    weight: 5,
                    opacity: 0.88,
                    lineCap: 'round',
                    lineJoin: 'round'
                }
        );

        // ---- Garis putus-putus dari ujung rute ke titik apotek asli ----

        const routeEnd = latlngs[latlngs.length - 1];

        const connector = L.polyline(
            [routeEnd, [toLat, toLng]],
            {
                color: '#667eea',
                weight: 3,
                dashArray: '4, 6',
                opacity: 0.7
            }
        );

        // ---- Gabungkan supaya ikut terhapus di clearRoute() ----

        const routeGroup = L.layerGroup([routeLine, connector]).addTo(map);

        routeState.routeLayerRef.current = routeGroup;

        routeLine.bringToFront();

        // ---- Peringatan snap ----

        if (suspicious) {
            routeLine.bindPopup(`
                ⚠️ Koordinat tujuan digeser sekitar
                ${Math.round(snapOffsetM)}
                meter ke jalan terdekat.
            `);
        }

        // ---- Fit route ----

        map.fitBounds(
            L.latLngBounds(latlngs).extend([toLat, toLng]),
            {
                paddingTopLeft: [25, 25],
                paddingBottomRight: [25, 25],
                maxZoom: 16,
                animate: true
            }
        );

        return {
            route: bestRoute,
            snapOffsetM: snapOffsetM,
            suspicious: suspicious
        };
    }


    // ========================================================
    // UPDATE USER MARKER
    // ========================================================

    function updateUserMarker(latitude, longitude) {

        if (userMarker) {
            userMarker.setLatLng([latitude, longitude]);
        } else {
            userMarker = L.marker(
                [latitude, longitude],
                { icon: userIcon }
            ).addTo(map);
        }

        userMarker.bindPopup(`
            <strong>📍 Lokasi Anda</strong>
            <br>
            Latitude: ${latitude.toFixed(6)}
            <br>
            Longitude: ${longitude.toFixed(6)}
        `);
    }


    // ========================================================
    // UPDATE RECOMMENDATION
    // ========================================================

    async function updateRecommendation(latitude, longitude, moveMap) {

        if (isUpdatingRecommendation) {
            return;
        }

        isUpdatingRecommendation = true;

        try {

            // ---- Simpan lokasi user ----

            userLocation = {
                lat: latitude,
                lng: longitude
            };

            // ---- Teks lokasi ----

            showLocationMessage(`
                Latitude: <strong>${latitude.toFixed(6)}</strong>
                Longitude: <strong>${longitude.toFixed(6)}</strong>
            `);

            // ---- Marker user ----

            updateUserMarker(latitude, longitude);

            // ---- Center map pertama kali ----

            if (moveMap) {
                map.setView([latitude, longitude], USER_ZOOM);
            }

            // ---- Destinations ----

            const destinations = window.apotekData
                .map((apotek) => ({
                    id: apotek.id,
                    lat: parseFloat(apotek.latitude),
                    lng: parseFloat(apotek.longitude)
                }))
                .filter((d) =>
                    !Number.isNaN(d.lat) &&
                    !Number.isNaN(d.lng)
                );

            if (destinations.length === 0) {
                console.warn('Tidak ada koordinat apotek.');
                return;
            }

            // ---- Haversine ----

            const haversineRank = rankByHaversine(
                latitude,
                longitude,
                destinations
            );

            // ---- Nearby ----

            const nearbyResults = haversineRank.results
                .filter((r) => r.distanceKm <= MAX_DISTANCE_KM)
                .sort((a, b) => a.distanceKm - b.distanceKm);

            // ---- Log ----

            console.log('================================');
            console.log('GPS UPDATE');
            console.log('Latitude:', latitude);
            console.log('Longitude:', longitude);
            console.log('Heading:', lastHeading);
            console.log('Apotek dalam radius:', nearbyResults.length);

            if (nearbyResults.length > 0) {
                console.log(
                    'Apotek terdekat:',
                    nearbyResults[0].id,
                    nearbyResults[0].distanceKm.toFixed(2),
                    'km'
                );
            }

            if (selectedRouteId !== null) {
                console.log('Rute manual aktif:', selectedRouteId);
            }

            console.log('================================');

            // ---- Filter marker ----

            Object.entries(markers).forEach(([id, markerData]) => {

                const result = haversineRank.results.find(
                    (item) => item.id == id
                );

                if (!result) {
                    return;
                }

                const isNearby = result.distanceKm <= MAX_DISTANCE_KM;

                if (isNearby) {
                    if (!map.hasLayer(markerData.marker)) {
                        markerData.marker.addTo(map);
                    }
                } else {
                    if (map.hasLayer(markerData.marker)) {
                        map.removeLayer(markerData.marker);
                    }
                }
            });

            // ---- Nearest ID ----

            const nearestId =
                nearbyResults.length > 0
                    ? nearbyResults[0].id
                    : null;

            // ---- Render list ----

            renderList(nearbyResults, nearestId);

            // ---- Tidak ada hasil ----

            if (nearbyResults.length === 0) {
                clearRoute();
                return;
            }

            // ---- Nearby destinations ----

            const nearbyDestinations = nearbyResults
                .map((result) =>
                    destinations.find((d) => d.id == result.id)
                )
                .filter(Boolean);

            // ---- Tentukan tujuan rute ----

            let routeDestination = null;

            if (selectedRouteId === null) {

                // MODE 1: otomatis ke apotek terdekat

                routeDestination = nearbyDestinations.find(
                    (d) => d.id == nearestId
                );

                if (routeDestination) {
                    console.log('Mode otomatis →', routeDestination.id);
                }

            } else {

                // MODE 2: manual

                routeDestination = nearbyDestinations.find(
                    (d) => d.id == selectedRouteId
                );

                if (routeDestination) {

                    console.log('Mode manual →', routeDestination.id);

                } else {

                    // Apotek terpilih sudah di luar radius 3 km:
                    // tetap pertahankan rute manual dari data marker.

                    const selectedMarker = markers[selectedRouteId];

                    if (selectedMarker) {
                        routeDestination = {
                            id: selectedRouteId,
                            lat: selectedMarker.lat,
                            lng: selectedMarker.lng
                        };
                    }
                }
            }

            // ---- Gambar rute ----

            if (routeDestination) {

                drawRoute(
                    latitude,
                    longitude,
                    routeDestination.lat,
                    routeDestination.lng
                ).catch((error) => {
                    console.warn('Gagal menggambar rute:', error.message);
                });
            }

            // ---- Status loading OSRM ----

            document.querySelectorAll('.apotek-item').forEach((item) => {

                if (item.style.display === 'none') {
                    return;
                }

                const oldStatus = item.querySelector('.osrm-status');

                if (oldStatus) {
                    oldStatus.remove();
                }

                const status = document.createElement('div');

                status.className = 'osrm-status';

                status.style.cssText = `
                    margin-top:5px;
                    font-size:11px;
                    color:#9ca3af;
                `;

                status.textContent = '↻ Menghitung jarak jalan...';

                item.appendChild(status);
            });

            // ---- Jarak jalan ----

            getRoadDistances(latitude, longitude, nearbyDestinations)
                .then((osrmResults) => {

                    osrmResults.forEach((result) => {

                        const item = document.querySelector(
                            `.apotek-item[data-id="${result.id}"]`
                        );

                        if (!item || item.style.display === 'none') {
                            return;
                        }

                        const status = item.querySelector('.osrm-status');

                        if (!status) {
                            return;
                        }

                        if (result.unreachable) {
                            status.innerHTML = '⚠️ Rute jalan tidak ditemukan.';
                            status.style.color = '#dc2626';
                            return;
                        }

                        if (result.suspicious) {
                            status.innerHTML = `
                                ⚠️ Koordinat digeser ~
                                ${Math.round(result.snapOffsetM)}
                                m ke jalan terdekat.
                            `;
                            status.style.color = '#ea580c';
                            return;
                        }

                        status.innerHTML = `
                            🚗 ${result.distanceKm.toFixed(2)} km jalan
                        `;

                        status.style.color = '#6b7280';
                    });
                })
                .catch((error) => {

                    console.warn('OSRM gagal:', error.message);

                    document.querySelectorAll('.apotek-item').forEach((item) => {

                        if (item.style.display === 'none') {
                            return;
                        }

                        const status = item.querySelector('.osrm-status');

                        if (status) {
                            status.innerHTML = '⚠️ Jarak jalan tidak tersedia.';
                            status.style.color = '#dc2626';
                        }
                    });
                });

        } finally {
            isUpdatingRecommendation = false;
        }
    }


    // ========================================================
    // GPS SUCCESS
    // ========================================================

    function handleLocationSuccess(position) {

        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;

        console.log(
            'GPS:',
            latitude,
            longitude,
            'accuracy:',
            position.coords.accuracy,
            'meter'
        );

        // ---- Simpan heading (hanya kalau user benar-benar bergerak) ----

        const heading = position.coords.heading;
        const speed = position.coords.speed;

        const headingValid =
            typeof heading === 'number' &&
            !Number.isNaN(heading) &&
            typeof speed === 'number' &&
            !Number.isNaN(speed) &&
            speed >= MIN_SPEED_FOR_HEADING;

        lastHeading = headingValid ? heading : null;

        // ---- GPS pertama ----

        if (!lastCalculatedLocation) {

            lastCalculatedLocation = {
                lat: latitude,
                lng: longitude
            };

            updateRecommendation(latitude, longitude, true);

            return;
        }

        // ---- Hitung perpindahan ----

        const movedDistance =
            calculateDistanceHaversine(
                lastCalculatedLocation.lat,
                lastCalculatedLocation.lng,
                latitude,
                longitude
            ) * 1000;

        console.log('Perpindahan:', movedDistance.toFixed(1), 'meter');

        // ---- Abaikan noise GPS ----

        if (movedDistance < LOCATION_UPDATE_THRESHOLD_METERS) {
            console.log('GPS berubah sedikit, diabaikan.');
            return;
        }

        // ---- Update lokasi ----

        lastCalculatedLocation = {
            lat: latitude,
            lng: longitude
        };

        updateRecommendation(latitude, longitude, false);
    }


    // ========================================================
    // GPS ERROR
    // ========================================================

    function handleLocationError(error) {

        console.warn('Geolocation error:', error);

        switch (error.code) {

            case error.PERMISSION_DENIED:
                showLocationMessage(`
                    ⚠️ Izin lokasi ditolak.<br>
                    Izinkan akses lokasi pada browser,
                    lalu refresh halaman.
                `);
                break;

            case error.POSITION_UNAVAILABLE:
                showLocationMessage(`
                    ⚠️ Lokasi tidak tersedia.<br>
                    Pastikan layanan lokasi perangkat sedang aktif.
                `);
                break;

            case error.TIMEOUT:
                showLocationMessage(`
                    ⚠️ Waktu mengambil lokasi habis.<br>
                    Coba refresh halaman.
                `);
                break;

            default:
                showLocationMessage('⚠️ Lokasi tidak dapat diperoleh.');
                break;
        }
    }


    // ========================================================
    // START GPS WATCH
    // ========================================================

    if ('geolocation' in navigator) {

        console.log('Memulai GPS watcher...');

        locationWatchId = navigator.geolocation.watchPosition(
            handleLocationSuccess,
            handleLocationError,
            {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 0
            }
        );

        console.log('Location Watch ID:', locationWatchId);

    } else {
        showLocationMessage('⚠️ Browser tidak mendukung fitur lokasi.');
    }


    // ========================================================
    // CLICK APOTEK CARD
    // ========================================================

    document.addEventListener('click', (event) => {

        const item = event.target.closest('.apotek-item');

        if (!item) {
            return;
        }

        if (item.style.display === 'none') {
            return;
        }

        const id = item.getAttribute('data-id');
        const lat = parseFloat(item.getAttribute('data-lat'));
        const lng = parseFloat(item.getAttribute('data-lng'));

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            console.warn('Koordinat apotek tidak valid.');
            return;
        }

        // ---- Rute manual: GPS update tidak akan mengganti tujuan ----

        selectedRouteId = id;

        console.log('================================');
        console.log('RUTE MANUAL DIPILIH');
        console.log('Tujuan ID:', selectedRouteId);
        console.log('================================');

        // ---- Buka popup marker ----

        if (markers[id]) {

            const marker = markers[id].marker;

            if (map.hasLayer(marker)) {
                marker.openPopup();
            }
        }

        // ---- Gambar rute manual ----

        if (userLocation) {

            drawRoute(
                userLocation.lat,
                userLocation.lng,
                lat,
                lng
            ).catch((error) => {
                console.warn('Gagal menggambar rute:', error.message);
            });

        } else {
            map.setView([lat, lng], 16);
        }
    });


    // ========================================================
    // CLEANUP
    // ========================================================

    window.addEventListener('beforeunload', () => {

        if (locationWatchId !== null) {
            navigator.geolocation.clearWatch(locationWatchId);
        }
    });

});