<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin = isset($_SESSION['admin_logged_in']);

include "koneksi.php";


/* =========================================================
   AMBIL DATA APOTEK
   ========================================================= */

$query = "SELECT * FROM apotek";

$result = mysqli_query(
    $conn,
    $query
);

if (!$result) {

    die(
        "Error: " .
        mysqli_error($conn)
    );

}

$apotek_data = [];

while (
    $row = mysqli_fetch_assoc($result)
) {

    $apotek_data[] = $row;

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Apotek - Apotek Terdekat
    </title>


    <!-- =====================================================
         LEAFLET CSS
         ===================================================== -->

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >


    <!-- =====================================================
         STYLE UTAMA WEBSITE
         ===================================================== -->

    <link
        rel="stylesheet"
        href="css/style.css"
    >


    <!-- =====================================================
         RESPONSIVE STYLE
         ===================================================== -->

    <style>

        /* =====================================================
           GLOBAL RESET
           ===================================================== */

        *,
        *::before,
        *::after {

            box-sizing: border-box;

        }


        html {

            width: 100%;

            min-width: 0;

            scroll-behavior: smooth;

        }


        body {

            width: 100%;

            min-width: 0;

            margin: 0;

            padding: 0;

            overflow-x: hidden;

        }


        img {

            max-width: 100%;

        }


        /* =====================================================
           CONTAINER
           ===================================================== */

        .container {

            width: 100%;

            max-width: 1400px;

            margin: 0 auto;

            padding-left: 20px;

            padding-right: 20px;

        }


        .main-content {

            width: 100%;

            min-width: 0;

        }


        /* =====================================================
           JUDUL
           ===================================================== */

        .page-title {

            color: #333;

            margin: 0 0 20px 0;

            font-size: 24px;

            line-height: 1.3;

            font-weight: 700;

        }


        /* =====================================================
           CONTAINER MAP + LIST
           ===================================================== */

        .apotek-container {

            display: grid;

            grid-template-columns:
                minmax(0, 2fr)
                minmax(300px, 1fr);

            gap: 20px;

            width: 100%;

            margin-top: 20px;

            align-items: start;

        }


        /* =====================================================
           MAP SECTION
           ===================================================== */

        .map-section {

            width: 100%;

            min-width: 0;

            background: #fff;

            padding: 15px;

            border-radius: 8px;

            box-shadow:
                0 2px 10px rgba(
                    0,
                    0,
                    0,
                    .10
                );

            overflow: hidden;

        }


        /* =====================================================
           MAP
           ===================================================== */

        #map {

            width: 100% !important;

            height: 600px;

            min-height: 400px;

            border-radius: 8px;

            background: #e0e0e0;

            box-shadow:
                0 2px 10px rgba(
                    0,
                    0,
                    0,
                    .10
                );

            z-index: 1;

        }


        /* =====================================================
           LIST CONTAINER
           ===================================================== */

        .apotek-list {

            width: 100%;

            min-width: 0;

            background: #fff;

            padding: 20px;

            border-radius: 8px;

            box-shadow:
                0 2px 10px rgba(
                    0,
                    0,
                    0,
                    .10
                );

            height: 600px;

            max-height: 600px;

            overflow-y: auto;

            overflow-x: hidden;

            scrollbar-width: thin;

        }


        .apotek-list::-webkit-scrollbar {

            width: 5px;

        }


        .apotek-list::-webkit-scrollbar-track {

            background: transparent;

        }


        .apotek-list::-webkit-scrollbar-thumb {

            background: #cbd5e1;

            border-radius: 20px;

        }


        .apotek-list::-webkit-scrollbar-thumb:hover {

            background: #94a3b8;

        }


        /* =====================================================
           JUDUL LIST
           ===================================================== */

        .apotek-list h3 {

            margin:

                0 0 15px 0;

            padding-bottom: 10px;

            border-bottom:
                2px solid #667eea;

            color: #333;

            font-size: 18px;

            line-height: 1.4;

        }


        /* =====================================================
           LOCATION INFO
           ===================================================== */

        .location-info {

            width: 100%;

            background: #e3f2fd;

            padding: 10px;

            border-radius: 5px;

            margin-bottom: 15px;

            border-left:
                4px solid #2196F3;

            font-size: 13px;

            line-height: 1.5;

            color: #1565c0;

            overflow-wrap: anywhere;

            word-break: break-word;

        }


        .location-info strong {

            display: block;

            color: #1565c0;

            margin-bottom: 3px;

        }


        /* =====================================================
           LIST DATA
           ===================================================== */

        #apotek-list-container {

            width: 100%;

            min-width: 0;

        }


        /* =====================================================
           CARD APOTEK
           ===================================================== */

        .apotek-item {

            width: 100%;

            min-width: 0;

            padding: 15px;

            margin-bottom: 8px;

            border-bottom:
                1px solid #eee;

            border-left:
                4px solid transparent;

            border-radius: 6px;

            cursor: pointer;

            overflow: hidden;

            overflow-wrap: anywhere;

            word-break: break-word;

            transition:
                background .2s ease,
                transform .2s ease,
                box-shadow .2s ease;

        }


        .apotek-item:hover {

            background: #f5f5f5;

            transform:
                translateX(3px);

        }


        .apotek-item:active {

            transform:
                translateX(1px);

        }


        .apotek-item.nearest {

            border-left-color:
                #28a745;

            background:
                #f5fff7;

        }


        /* =====================================================
           GAMBAR CARD
           ===================================================== */

        .apotek-thumb {

            display: block;

            width: 100%;

            max-width: 100%;

            height: 120px;

            object-fit: cover;

            border-radius: 6px;

            margin-bottom: 8px;

        }


        .apotek-thumb-empty {

            height: 90px;

            background: #f0f0f0;

            border:
                1px dashed #ccc;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #aaa;

            font-size: 12px;

            border-radius: 6px;

        }


        /* =====================================================
           NAMA APOTEK
           ===================================================== */

        .apotek-item h4 {

            margin:
                0 0 5px 0;

            color: #333;

            font-size: 14px;

            font-weight: 700;

            line-height: 1.4;

            display: flex;

            align-items: center;

            gap: 6px;

            flex-wrap: wrap;

            overflow-wrap: anywhere;

        }


        /* =====================================================
           PARAGRAF
           ===================================================== */

        .apotek-item p {

            margin: 5px 0;

            color: #666;

            font-size: 12px;

            line-height: 1.5;

            overflow-wrap: anywhere;

            word-break: break-word;

        }


        /* =====================================================
           BADGE TERDEKAT
           ===================================================== */

        .badge-closest {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            background: #28a745;

            color: #fff;

            padding:
                3px 8px;

            border-radius: 20px;

            font-size: 10px;

            line-height: 1.2;

            font-weight: 700;

            white-space: nowrap;

        }


        /* =====================================================
           JENIS LAYANAN
           ===================================================== */

        .layanan-info {

            display: inline-block;

            max-width: 100%;

            background: #f1f3ff;

            color: #4c51bf;

            padding:
                3px 8px;

            border-radius: 12px;

            font-size: 11px !important;

            font-weight: 700;

            margin-top: 4px;

            line-height: 1.4;

            overflow-wrap: anywhere;

        }


        /* =====================================================
           JARAK HAVERSINE
           ===================================================== */

        .distance-info {

            width: 100%;

            margin-top: 8px;

            padding-top: 8px;

            border-top:
                1px solid #eee;

            font-weight: 700;

            color: #667eea;

            font-size: 13px;

            line-height: 1.4;

        }


        /* =====================================================
           OSRM
           ===================================================== */

        .osrm-status {

            width: 100%;

            margin-top: 6px;

            min-height: 14px;

            font-size: 11px;

            line-height: 1.4;

            color: #777;

            overflow-wrap: anywhere;

        }


        /* =====================================================
           LEAFLET POPUP
           COMPACT
           ===================================================== */

        .leaflet-popup {

            margin-bottom: 14px;

        }


        .leaflet-popup-content-wrapper {

            background: #fff;

            border-radius: 10px;

            box-shadow:
                0 5px 18px
                rgba(
                    0,
                    0,
                    0,
                    .18
                );

            overflow: hidden;

        }


        .leaflet-popup-content {

            width: 215px !important;

            max-width:
                calc(100vw - 50px);

            margin:
                8px 10px;

            line-height: 1.4;

        }


        .leaflet-popup-content img {

            width: 100%;

            height: 78px;

            object-fit: cover;

            border-radius: 7px;

            display: block;

            margin-bottom: 7px;

        }


        .leaflet-popup-tip {

            box-shadow:
                1px 1px 3px
                rgba(
                    0,
                    0,
                    0,
                    .08
                );

        }


        /* =====================================================
           TABLET
           769 - 1024
           ===================================================== */

        @media
        (min-width: 769px)
        and
        (max-width: 1024px) {

            .container {

                padding-left: 15px;

                padding-right: 15px;

            }


            .page-title {

                font-size: 22px;

                margin-bottom: 15px;

            }


            .apotek-container {

                grid-template-columns:
                    minmax(0, 1.5fr)
                    minmax(250px, 1fr);

                gap: 14px;

            }


            .map-section {

                padding: 10px;

            }


            .apotek-list {

                padding: 15px;

                height: 500px;

                max-height: 500px;

            }


            #map {

                height: 500px;

                min-height: 400px;

            }


            .apotek-thumb {

                height: 100px;

            }

        }


        /* =====================================================
           MOBILE
           <= 768
           ===================================================== */

        @media
        (max-width: 768px) {

            .container {

                width: 100%;

                padding-left: 12px;

                padding-right: 12px;

            }


            .page-title {

                font-size: 21px;

                margin:
                    15px 0;

            }


            /* -----------------------------------------------
               SATU KOLOM
               ----------------------------------------------- */

            .apotek-container {

                display: flex;

                flex-direction: column;

                width: 100%;

                gap: 12px;

                margin-top: 10px;

            }


            /* -----------------------------------------------
               MAP DI ATAS
               ----------------------------------------------- */

            .map-section {

                width: 100%;

                padding: 10px;

                order: 1;

                border-radius: 8px;

            }


            #map {

                width: 100% !important;

                height: 420px;

                min-height: 320px;

                border-radius: 6px;

            }


            /* -----------------------------------------------
               LIST DI BAWAH
               ----------------------------------------------- */

            .apotek-list {

                width: 100%;

                height: auto;

                max-height: none;

                padding: 12px;

                order: 2;

                overflow: visible;

            }


            .apotek-list h3 {

                font-size: 17px;

            }


            .location-info {

                font-size: 12px;

                padding: 10px;

            }


            .apotek-item {

                padding: 12px;

            }


            .apotek-thumb {

                height: 120px;

            }

        }


        /* =====================================================
           HP
           <= 480
           ===================================================== */

        @media
        (max-width: 480px) {

            .container {

                padding-left: 8px;

                padding-right: 8px;

            }


            .page-title {

                font-size: 20px;

                margin:
                    12px 0;

            }


            .apotek-container {

                gap: 10px;

                margin-top: 5px;

            }


            .map-section {

                padding: 7px;

            }


            #map {

                height: 330px;

                min-height: 280px;

                border-radius: 5px;

            }


            .apotek-list {

                padding: 10px;

                border-radius: 6px;

            }


            .apotek-list h3 {

                font-size: 16px;

                padding-bottom: 8px;

                margin-bottom: 10px;

            }


            .location-info {

                font-size: 11px;

                padding: 9px;

                margin-bottom: 10px;

            }


            .apotek-item {

                padding: 10px;

                margin-bottom: 6px;

            }


            .apotek-thumb {

                height: 105px;

                border-radius: 5px;

            }


            .apotek-thumb-empty {

                height: 80px;

            }


            .apotek-item h4 {

                font-size: 13px;

            }


            .apotek-item p {

                font-size: 11px;

                line-height: 1.45;

            }


            .distance-info {

                font-size: 11px;

            }


            .osrm-status {

                font-size: 10px;

            }


            .badge-closest {

                font-size: 9px;

                padding:
                    3px 7px;

            }


            .layanan-info {

                font-size: 10px !important;

            }


            /* Popup HP */

            .leaflet-popup-content {

                width: 205px !important;

                max-width:
                    calc(100vw - 40px);

                margin:
                    7px 9px;

            }


            .leaflet-popup-content img {

                height: 72px;

            }

        }


        /* =====================================================
           HP SANGAT KECIL
           <= 360
           ===================================================== */

        @media
        (max-width: 360px) {

            .container {

                padding-left: 5px;

                padding-right: 5px;

            }


            .page-title {

                font-size: 18px;

            }


            .map-section {

                padding: 5px;

            }


            #map {

                height: 300px;

            }


            .apotek-list {

                padding: 8px;

            }


            .apotek-list h3 {

                font-size: 15px;

            }


            .apotek-item {

                padding: 9px;

            }


            .apotek-thumb {

                height: 90px;

            }


            .apotek-item h4 {

                font-size: 12px;

            }


            .apotek-item p {

                font-size: 10.5px;

            }


            .leaflet-popup-content {

                width: 190px !important;

                max-width:
                    calc(100vw - 30px);

            }


            .leaflet-popup-content img {

                height: 65px;

            }

        }


        /* =====================================================
           LANDSCAPE HP
           ===================================================== */

        @media
        (max-width: 768px)
        and
        (orientation: landscape) {

            .apotek-container {

                flex-direction: column;

            }


            #map {

                height: 360px;

            }


            .apotek-list {

                max-height: none;

                height: auto;

            }

        }


        /* =====================================================
           DESKTOP BESAR
           >= 1400
           ===================================================== */

        @media
        (min-width: 1400px) {

            .container {

                max-width: 1400px;

            }


            .apotek-container {

                grid-template-columns:
                    minmax(0, 2.1fr)
                    minmax(330px, .9fr);

                gap: 22px;

            }


            #map {

                height:
                    calc(100vh - 210px);

                min-height: 550px;

            }


            .apotek-list {

                height:
                    calc(100vh - 210px);

                max-height:
                    calc(100vh - 210px);

            }

        }


        /* =====================================================
           LEAFLET CONTROL MOBILE
           ===================================================== */

        @media
        (max-width: 480px) {

            .leaflet-control-zoom {

                margin-left: 7px !important;

                margin-top: 7px !important;

            }


            .leaflet-control-zoom a {

                width: 30px !important;

                height: 30px !important;

                line-height: 30px !important;

                font-size: 18px !important;

            }


            .leaflet-control-attribution {

                font-size: 8px !important;

            }

        }


        /* =====================================================
           MENCEGAH POPUP KELUAR LAYAR
           ===================================================== */

        .leaflet-popup-content-wrapper {

            max-width:
                calc(100vw - 20px);

        }


        /* =====================================================
           PREVENT HORIZONTAL OVERFLOW
           ===================================================== */

        .apotek-container,
        .map-section,
        .apotek-list,
        .apotek-item,
        #map {

            max-width: 100%;

        }

    </style>

</head>


<body>


    <!-- =====================================================
         NAVBAR
         ===================================================== -->

    <?php include 'includes/navbar.php'; ?>


    <!-- =====================================================
         CONTAINER
         ===================================================== -->

    <div class="container">

        <div class="main-content">


            <!-- =================================================
                 JUDUL
                 ================================================= -->

            <h1 class="page-title">

                Cari Apotek Terdekat

            </h1>


            <!-- =================================================
                 MAP + LIST
                 ================================================= -->

            <div class="apotek-container">


                <!-- =================================================
                     MAP
                     ================================================= -->

                <div class="map-section">

                    <div id="map"></div>

                </div>


                <!-- =================================================
                     DAFTAR APOTEK
                     ================================================= -->

                <div class="apotek-list">


                    <h3>

                        Daftar Apotek Terdekat

                    </h3>


                    <!-- =================================================
                         LOKASI USER
                         ================================================= -->

                    <div class="location-info">

                        <strong>

                            📌 Lokasi Anda:

                        </strong>


                        <span id="user-location-text">

                            Mengambil lokasi...

                        </span>

                    </div>


                    <!-- =================================================
                         LIST DATA APOTEK
                         ================================================= -->

                    <div id="apotek-list-container">


                        <?php if (
                            count($apotek_data) > 0
                        ): ?>


                            <?php foreach (
                                $apotek_data
                                as $apotek
                            ): ?>


                                <div
                                    class="apotek-item"

                                    data-id="<?= htmlspecialchars(
                                        $apotek['id']
                                    ) ?>"

                                    data-lat="<?= htmlspecialchars(
                                        $apotek['latitude']
                                    ) ?>"

                                    data-lng="<?= htmlspecialchars(
                                        $apotek['longitude']
                                    ) ?>"
                                >


                                    <!-- =================================
                                         GAMBAR
                                         ================================= -->

                                    <?php if (
                                        !empty(
                                            $apotek['gambar']
                                        )
                                    ): ?>


                                        <img

                                            class="apotek-thumb"

                                            src="uploads/<?= htmlspecialchars(
                                                $apotek['gambar']
                                            ) ?>"

                                            alt="<?= htmlspecialchars(
                                                $apotek['nama']
                                            ) ?>"

                                            loading="lazy"

                                        >


                                    <?php else: ?>


                                        <div
                                            class="
                                                apotek-thumb
                                                apotek-thumb-empty
                                            "
                                        >

                                            📷 Belum ada gambar

                                        </div>


                                    <?php endif; ?>


                                    <!-- =================================
                                         NAMA
                                         ================================= -->

                                    <h4>

                                        <?= htmlspecialchars(
                                            $apotek['nama']
                                        ) ?>

                                    </h4>


                                    <!-- =================================
                                         ALAMAT
                                         ================================= -->

                                    <p>

                                        📍

                                        <?= htmlspecialchars(
                                            $apotek['alamat']
                                        ) ?>

                                    </p>


                                    <!-- =================================
                                         TELEPON
                                         ================================= -->

                                    <p>

                                        📞

                                        <?= htmlspecialchars(
                                            $apotek['telepon'] ?? '-'
                                        ) ?>

                                    </p>


                                    <!-- =================================
                                         JAM OPERASIONAL
                                         ================================= -->

                                    <?php if (
                                        !empty(
                                            $apotek[
                                                'jam_operasional'
                                            ]
                                        )
                                    ): ?>


                                        <p>

                                            🕒

                                            <?= htmlspecialchars(
                                                $apotek[
                                                    'jam_operasional'
                                                ]
                                            ) ?>

                                        </p>


                                    <?php endif; ?>


                                    <!-- =================================
                                         NOMOR IZIN
                                         ================================= -->

                                    <?php if (
                                        !empty(
                                            $apotek['no_izin']
                                        )
                                    ): ?>


                                        <p>

                                            📄 No. Izin:

                                            <?= htmlspecialchars(
                                                $apotek[
                                                    'no_izin'
                                                ]
                                            ) ?>

                                        </p>


                                    <?php endif; ?>


                                    <!-- =================================
                                         JENIS LAYANAN
                                         ================================= -->

                                    <?php

                                    $jenis_layanan =
                                        $apotek[
                                            'jenis_layanan'
                                        ] ?? '-';

                                    ?>


                                    <p class="layanan-info">

                                        <?= htmlspecialchars(
                                            $jenis_layanan
                                        ) ?>

                                    </p>


                                    <!-- =================================
                                         DISTANCE
                                         ================================= -->

                                    <div
                                        class="distance-info"
                                    ></div>


                                    <!-- =================================
                                         OSRM
                                         ================================= -->

                                    <div
                                        class="osrm-status"
                                    ></div>


                                </div>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <div
                                style="
                                    color:#999;
                                    text-align:center;
                                    padding:20px;
                                "
                            >

                                Tidak ada data apotek

                            </div>


                        <?php endif; ?>


                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         LEAFLET JS
         ========================================================= -->

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    ></script>


    <!-- =========================================================
         DATA APOTEK
         ========================================================= -->

    <script>

        window.apotekData = <?= json_encode(

            $apotek_data,

            JSON_HEX_TAG |
            JSON_HEX_AMP |
            JSON_HEX_APOS |
            JSON_HEX_QUOT

        ); ?>;


        console.log(
            'DATA APOTEK:',
            window.apotekData
        );

    </script>


    <!-- =========================================================
         JAVASCRIPT YANG SUDAH ADA
         ========================================================= -->

    <script
        src="js/haversine.js"
    ></script>


    <script
        src="js/osrm.js"
    ></script>


    <script
        src="js/map-init.js"
    ></script>


    <!-- =========================================================
         RESPONSIVE MAP
         ========================================================= -->

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                /*
                 * Leaflet kadang belum mengetahui
                 * ukuran container setelah responsive.
                 */

                function refreshMap() {

                    if (
                        typeof map !== 'undefined' &&
                        map &&
                        typeof map.invalidateSize ===
                            'function'
                    ) {

                        map.invalidateSize();

                    }

                }


                setTimeout(
                    refreshMap,
                    200
                );


                setTimeout(
                    refreshMap,
                    500
                );


                setTimeout(
                    refreshMap,
                    1000
                );


                window.addEventListener(
                    'resize',
                    function () {

                        setTimeout(
                            refreshMap,
                            150
                        );

                    }
                );


                window.addEventListener(
                    'orientationchange',
                    function () {

                        setTimeout(
                            refreshMap,
                            400
                        );

                    }
                );

            }

        );

    </script>


</body>

</html>