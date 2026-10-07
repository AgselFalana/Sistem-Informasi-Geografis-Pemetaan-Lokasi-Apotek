// Semua yang berhubungan dengan OSRM (Open Source Routing Machine).
// Butuh calculateDistanceHaversine() dari haversine.js, jadi file itu
// harus dimuat lebih dulu.

const SNAP_TOLERANCE_METERS = 150;

// Jarak & waktu tempuh jalan asli ke banyak tujuan sekaligus (OSRM Table Service).
async function getRoadDistances(userLat, userLng, destinations) {
    const coordsList = [userLng + ',' + userLat]
        .concat(destinations.map(function (d) { return d.lng + ',' + d.lat; }));
    const coordsStr = coordsList.join(';');

    // destinations index 1..N (index 0 = user/source)
    const destIdx = destinations.map(function (_, i) { return i + 1; }).join(';');

    const url = 'https://router.project-osrm.org/table/v1/driving/' + coordsStr +
        '?sources=0&destinations=' + destIdx + '&annotations=distance,duration';

    const response = await fetch(url);
    if (!response.ok) {
        throw new Error('OSRM request failed: ' + response.status);
    }
    const data = await response.json();

    if (data.code !== 'Ok') {
        throw new Error('OSRM error: ' + data.code);
    }

    // data.destinations[i].location = koordinat setelah di-snap OSRM
    // ke jalan terdekat. Kalau jauh dari koordinat asli, berarti
    // koordinat aslinya kemungkinan nyasar (bukan di jalan/gedung asli).
    const destWaypoints = data.destinations || [];

    // data.distances[0] = array jarak (meter) dari source ke tiap destination
    // data.durations[0] = array waktu tempuh (detik)
    return destinations.map(function (dest, i) {
        const distMeters = data.distances[0][i];
        const durSeconds = data.durations[0][i];

        // OSRM bisa return null kalau titik tidak terjangkau lewat jalan
        // (misal koordinat nyasar ke tengah sungai/laut, di luar radius snap)
        if (distMeters === null || durSeconds === null) {
            return {
                id: dest.id,
                distanceKm: null,
                durationMin: null,
                unreachable: true,
                snapOffsetM: null
            };
        }

        // Cek seberapa jauh OSRM "menggeser" koordinat asli ke jalan
        // terdekat. Kalau kegedean, koordinat di database kemungkinan salah.
        let snapOffsetM = null;
        const wp = destWaypoints[i];
        if (wp && wp.location) {
            // wp.location = [lng, lat] hasil snap OSRM
            snapOffsetM = calculateDistanceHaversine(
                dest.lat, dest.lng, wp.location[1], wp.location[0]
            ) * 1000; // km -> meter
        }
        const suspicious = snapOffsetM !== null && snapOffsetM > SNAP_TOLERANCE_METERS;

        return {
            id: dest.id,
            distanceKm: distMeters / 1000,
            durationMin: durSeconds / 60,
            unreachable: false,
            snapOffsetM: snapOffsetM,
            suspicious: suspicious
        };
    });
}

async function drawRoute(map, fromLat, fromLng, toLat, toLng, state) {
    const url = 'https://router.project-osrm.org/route/v1/driving/' +
        fromLng + ',' + fromLat + ';' + toLng + ',' + toLat +
        '?overview=full&geometries=geojson';

    const response = await fetch(url);
    if (!response.ok) {
        throw new Error('OSRM route request failed: ' + response.status);
    }
    const data = await response.json();

    if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
        throw new Error('OSRM route error: ' + data.code);
    }

    // Cek snap offset di titik tujuan (waypoints[1] = destination)
    let snapOffsetM = null;
    const destWaypoint = data.waypoints && data.waypoints[1];
    if (destWaypoint && destWaypoint.location) {
        snapOffsetM = calculateDistanceHaversine(
            toLat, toLng, destWaypoint.location[1], destWaypoint.location[0]
        ) * 1000;
    }
    const suspicious = snapOffsetM !== null && snapOffsetM > SNAP_TOLERANCE_METERS;

    // Hapus rute lama sebelum gambar yang baru
    if (state.routeLayerRef.current) {
        map.removeLayer(state.routeLayerRef.current);
        state.routeLayerRef.current = null;
    }

    // geometry.coordinates dari GeoJSON formatnya [lng, lat], Leaflet butuh [lat, lng]
    const latlngs = data.routes[0].geometry.coordinates.map(function (c) {
        return [c[1], c[0]];
    });

    // Kalau koordinat tujuan mencurigakan (digeser jauh dari data asli),
    // gambar rute dengan warna beda (oranye putus-putus) sebagai peringatan
    // visual, bukan diam-diam dianggap valid kayak rute normal.
    const routeLayer = L.polyline(latlngs, suspicious ? {
        color: '#ff9800',
        weight: 5,
        opacity: 0.8,
        dashArray: '8, 8'
    } : {
        color: '#667eea',
        weight: 5,
        opacity: 0.8
    }).addTo(map);

    state.routeLayerRef.current = routeLayer;

    if (suspicious) {
        routeLayer.bindPopup(
            '⚠️ Koordinat tujuan digeser ~' + Math.round(snapOffsetM) +
            'm untuk nempel ke jalan terdekat.<br>Kemungkinan data koordinat di database ini salah.'
        ).openPopup();
    }

    // Zoom peta supaya seluruh rute kelihatan
    map.fitBounds(routeLayer.getBounds(), { padding: [40, 40] });

    return { route: data.routes[0], snapOffsetM: snapOffsetM, suspicious: suspicious };
}
