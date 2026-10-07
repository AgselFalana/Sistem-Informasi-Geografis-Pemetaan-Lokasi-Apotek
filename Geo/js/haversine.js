// Perhitungan jarak garis lurus (Haversine Formula). Dipakai untuk urutan
// list & badge "TERDEKAT", dan sebagai alat bantu osrm.js untuk cek
// seberapa jauh OSRM menggeser koordinat ke jalan terdekat.

function calculateDistanceHaversine(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Hitung jarak ke banyak tujuan sekaligus lalu urutkan dari yang terdekat.
function rankByHaversine(userLat, userLng, destinations) {
    const results = destinations.map(function (dest) {
        return {
            id: dest.id,
            distanceKm: calculateDistanceHaversine(userLat, userLng, dest.lat, dest.lng)
        };
    });
    const sorted = results.slice().sort(function (a, b) {
        return a.distanceKm - b.distanceKm;
    });
    return { results: results, sorted: sorted, nearest: sorted[0] };
}
