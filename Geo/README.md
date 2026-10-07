# 🏥 Puskesmas Terdekat - Aplikasi Pencari Puskesmas & Apotek dengan Geolocation

Aplikasi web untuk menemukan puskesmas/apotek terdekat berdasarkan lokasi pengguna, dengan peta interaktif, geolocation GPS, dan rute jalan asli (bukan garis lurus).

## ✨ Fitur Utama

### 🗺️ Maps Integration
- **Leaflet.js v1.9.4** - Peta interaktif OpenStreetMap
- **Marker Puskesmas/Apotek** - Penanda merah untuk semua lokasi
- **Marker User** - Penanda biru untuk lokasi pengguna
- **Geolocation API** - Ambil lokasi user otomatis
- **Interaktif** - Zoom, pan, klik untuk detail

### 📍 Geolocation Features
- ✅ Ambil lokasi GPS user otomatis (permintaan akses)
- ✅ Tampilkan koordinat & akurasi lokasi
- ✅ Center map ke lokasi user otomatis
- ✅ Support browser modern (Chrome, Firefox, Safari, Edge)

### 📏 Perhitungan Jarak & Rute
- ✅ **Haversine Formula** - hitung jarak garis lurus, dipakai untuk menentukan urutan & badge ⭐ TERDEKAT
- ✅ **OSRM Routing** - gambar rute jalan asli (bukan garis lurus) ke lokasi terdekat, plus estimasi jarak & waktu tempuh jalan
- ✅ Deteksi otomatis kalau koordinat di database mencurigakan (digeser jauh dari jalan terdekat)
- ✅ Sort & highlight lokasi terdekat otomatis

### 👤 User Features
- ✅ Lihat semua puskesmas/apotek di map
- ✅ Klik lokasi → zoom & popup info
- ✅ Sidebar daftar lokasi dengan jarak terdekat
- ✅ Responsive design (mobile-friendly)
- ✅ Tanpa login (public access)

### 🔐 Admin Features
- ✅ Login dengan username/password
- ✅ CRUD puskesmas/apotek (Tambah/Edit/Hapus), termasuk upload gambar, jam operasional, no. izin, dan jenis layanan
- ✅ Logout

## 🚀 Quick Start

### 1. Setup Database
Import `puskesmas_akurat.sql` ke database `db_puskesmas` lewat phpMyAdmin/HeidiSQL (file ini otomatis membuat tabel `puskesmas` dan mengisi data puskesmas + apotek Samarinda).

### 2. Buka Aplikasi
```
http://localhost/Geo/puskesmas-terdekat/puskesmas.php
```

### 3. Izinkan Akses Lokasi
Browser akan meminta izin akses lokasi GPS. Klik **"Allow"** untuk fitur geolocation bekerja.

## 📋 URL & Navigation

| Halaman | URL | Keterangan |
|---------|-----|----------|
| **🗺️ Maps (Utama)** | `/puskesmas.php` | Tampil lokasi + geolocation + rute |
| **⚙️ Admin Panel** | `/admin.php` | Kelola data (admin only) |
| **🔐 Login** | `/login.php` | Login admin |

## 🔐 Login Credentials

```
Username: admin
Password: admin123
```
⚠️ Ganti kredensial ini sebelum dipakai di luar localhost/demo.

## 📁 Struktur File
```
Geo/
├── puskesmas.php ⭐       // Main app - Maps + Geolocation + Haversine + OSRM
├── login.php              // Admin login
├── admin.php              // Admin panel (CRUD)
├── logout.php             // Logout
├── index.php              // Redirect ke puskesmas.php
│
├── includes/
│   ├── navbar.php         // Navigation (responsive)
│   ├── session.php        // Session helpers
│   ├── constants.php      // Konstanta jenis_layanan (dipakai admin.php & puskesmas.php)
│   ├── db_schema.php      // Bootstrap kolom tabel puskesmas
│   └── upload_helper.php  // Upload/hapus file gambar
│
├── css/
│   └── style.css          // Styling (responsive)
│
├── js/
│   ├── haversine.js       // Perhitungan jarak garis lurus
│   ├── osrm.js            // Rute jalan asli & jarak/waktu tempuh jalan
│   └── map-init.js        // Inisialisasi peta, marker, & pemanggil haversine/osrm
│
├── uploads/               // Folder gambar puskesmas/apotek
├── koneksi.php            // Database connection
├── puskesmas_akurat.sql   // Import ini untuk setup database dari nol
├── patch_tambah_kolom.sql // Buat nambah kolom baru kalau database sudah ada isinya
└── README.md              // Dokumentasi
```

## 🛠️ Teknologi

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Maps**: Leaflet.js v1.9.4 + OpenStreetMap
- **Routing**: OSRM (Open Source Routing Machine) - profil `driving`
- **Geolocation**: Browser Geolocation API
- **Jarak (ranking)**: Haversine Formula

## 📍 Cara Kerja

1. **Request Permission** - Browser meminta izin akses lokasi
2. **Get Location** - JavaScript ambil lat/lng dari GPS
3. **Haversine** - Hitung jarak garis lurus ke semua lokasi → dipakai untuk sort list & badge ⭐ TERDEKAT
4. **OSRM** - Gambar rute jalan asli ke lokasi terdekat + hitung jarak/waktu tempuh jalan untuk semua lokasi sebagai info tambahan

⚠️ **Catatan**: OSRM publik yang dipakai tidak memperhitungkan kondisi macet real-time, dan hanya punya profil `driving` (belum ada profil khusus motor).

## 🔧 Troubleshooting

### ❌ Maps tidak tampil?
1. Buka browser console (F12 → Console)
2. Cek ada error messages?
3. Pastikan internet aktif (load Leaflet CDN & OSRM)

### ❌ Geolocation tidak muncul?
1. **Browser tidak support?** - Gunakan Chrome, Firefox, Safari, atau Edge
2. **Belum izin?** - Klik tombol "Allow" ketika browser minta akses lokasi
3. **HTTPS di production?** - Geolocation hanya kerja di HTTPS (kecuali localhost)
4. **GPS tidak aktif?** - Pastikan GPS device aktif (untuk mobile)

### ❌ Rute jalan tidak muncul / warna oranye putus-putus?
Itu tandanya OSRM menggeser koordinat cukup jauh ke jalan terdekat — kemungkinan koordinat di database salah (misal nyasar ke sungai). Cek ulang koordinatnya di Google Maps.

### ❌ Admin login gagal?
1. Username: `admin` (lowercase)
2. Password: `admin123`
3. Clear cache & cookies browser

### ❌ Database error?
1. Pastikan XAMPP Apache + MySQL running
2. Buka http://localhost/phpmyadmin
3. Verifikasi database `db_puskesmas` ada dan tabel `puskesmas` sudah di-import dari `puskesmas_akurat.sql`

## 📱 Browser Support

| Browser | Status | Geolocation | Maps |
|---------|--------|-----------|------|
| Chrome | ✅ | ✅ | ✅ |
| Firefox | ✅ | ✅ | ✅ |
| Safari | ✅ | ✅ | ✅ |
| Edge | ✅ | ✅ | ✅ |
| IE 11 | ❌ | ❌ | ❌ |

## 🔐 Security Features

- ✅ Session-based authentication
- ✅ XSS protection (htmlspecialchars)
- ✅ SQL injection protection (mysqli, prepared values di query utama)
- ✅ HTTPS ready (untuk production)

## 📝 Notes

- **Geolocation** memerlukan izin user & browser support
- **GPS Accuracy** tergantung device (±5-250m untuk mobile)
- **Haversine Formula** dipakai untuk ranking "terdekat", bukan untuk rute
- **OSRM** dipakai untuk rute jalan asli, tergantung kelengkapan data OpenStreetMap
- **Leaflet** open-source & gratis (OpenStreetMap)
