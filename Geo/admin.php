<?php

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include "koneksi.php";
// include "includes/db_schema.php";
include "includes/upload_helper.php";

$upload_dir = __DIR__ . '/uploads/';


/* =========================================================
   PROSES TAMBAH DATA
========================================================= */

if (isset($_POST['tambah'])) {

    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $latitude = (float) ($_POST['latitude'] ?? 0);
    $longitude = (float) ($_POST['longitude'] ?? 0);
    $telepon = trim($_POST['telepon'] ?? '');
    $jam_operasional = trim($_POST['jam_operasional'] ?? '');
    $no_izin = trim($_POST['no_izin'] ?? '');
    $jenis_layanan = trim($_POST['jenis_layanan'] ?? '');

    $gambar = '';

    if (!empty($_FILES['gambar']['name'])) {

        $upload_result = handle_upload_gambar($_FILES['gambar'], $upload_dir);

        if ($upload_result['success']) {

            $gambar = $upload_result['filename'];

        } else {

            $_SESSION['error'] = $upload_result['message'];

            header("Location: admin.php");

            exit;
        }
    }


    $query = "
        INSERT INTO apotek
        (
            nama,
            alamat,
            latitude,
            longitude,
            telepon,
            gambar,
            jam_operasional,
            no_izin,
            jenis_layanan
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";


    $stmt = $conn->prepare($query);


    if ($stmt) {

        $stmt->bind_param(
            "ssddsssss",
            $nama,
            $alamat,
            $latitude,
            $longitude,
            $telepon,
            $gambar,
            $jam_operasional,
            $no_izin,
            $jenis_layanan
        );


        if ($stmt->execute()) {

            $_SESSION['success'] =
                "Data berhasil ditambahkan.";

        } else {

            $_SESSION['error'] =
                "Data gagal ditambahkan.";
        }


        $stmt->close();

    } else {

        $_SESSION['error'] =
            "Query gagal diproses.";
    }


    header("Location: admin.php");

    exit;
}


/* =========================================================
   PROSES UPDATE DATA
========================================================= */

if (isset($_POST['update'])) {

    $id = (int) ($_POST['id'] ?? 0);

    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $latitude = (float) ($_POST['latitude'] ?? 0);
    $longitude = (float) ($_POST['longitude'] ?? 0);
    $telepon = trim($_POST['telepon'] ?? '');
    $jam_operasional = trim($_POST['jam_operasional'] ?? '');
    $no_izin = trim($_POST['no_izin'] ?? '');
    $jenis_layanan = trim($_POST['jenis_layanan'] ?? '');


    /* =====================================================
       AMBIL GAMBAR LAMA
    ===================================================== */

    $old_query =
        "SELECT gambar FROM apotek WHERE id = ?";


    $old_stmt =
        $conn->prepare($old_query);


    if ($old_stmt) {

        $old_stmt->bind_param(
            "i",
            $id
        );


        $old_stmt->execute();


        $old_result =
            $old_stmt->get_result();


        $old_data =
            $old_result->fetch_assoc();


        $old_gambar =
            $old_data['gambar'] ?? '';


        $old_stmt->close();

    } else {

        $old_gambar = '';
    }


    $gambar = $old_gambar;


    /* =====================================================
       HAPUS GAMBAR LAMA
    ===================================================== */

    if (
        isset($_POST['hapus_gambar']) &&
        $_POST['hapus_gambar'] == '1'
    ) {

        if (!empty($old_gambar)) {

            $old_file =
                $upload_dir . $old_gambar;


            if (file_exists($old_file)) {

                unlink($old_file);
            }
        }


        $gambar = '';
    }


    /* =====================================================
       UPLOAD GAMBAR BARU
    ===================================================== */

    if (!empty($_FILES['gambar']['name'])) {

        $upload_result =
            handle_upload_gambar($_FILES['gambar'], $upload_dir);

        if ($upload_result['success']) {

            $gambar_baru =
                $upload_result['filename'];


            /* Hapus gambar lama */

            if (!empty($old_gambar)) {

                $old_file =
                    $upload_dir . $old_gambar;


                if (file_exists($old_file)) {

                    unlink($old_file);
                }
            }


            $gambar =
                $gambar_baru;

        } else {

            $_SESSION['error'] =
                $upload_result['message'];


            header(
                "Location: admin.php?edit=" . $id
            );


            exit;
        }
    }


    /* =====================================================
       UPDATE DATABASE
    ===================================================== */

    $query = "
        UPDATE apotek
        SET
            nama = ?,
            alamat = ?,
            latitude = ?,
            longitude = ?,
            telepon = ?,
            gambar = ?,
            jam_operasional = ?,
            no_izin = ?,
            jenis_layanan = ?
        WHERE id = ?
    ";


    $stmt =
        $conn->prepare($query);


    if ($stmt) {

        $stmt->bind_param(
            "ssddsssssi",
            $nama,
            $alamat,
            $latitude,
            $longitude,
            $telepon,
            $gambar,
            $jam_operasional,
            $no_izin,
            $jenis_layanan,
            $id
        );


        if ($stmt->execute()) {

            $_SESSION['success'] =
                "Data berhasil diperbarui.";

        } else {

            $_SESSION['error'] =
                "Data gagal diperbarui.";
        }


        $stmt->close();

    } else {

        $_SESSION['error'] =
            "Query gagal diproses.";
    }


    header("Location: admin.php");

    exit;
}


/* =========================================================
   PROSES HAPUS DATA
========================================================= */

if (isset($_GET['delete'])) {

    $id =
        (int) $_GET['delete'];


    /* Ambil gambar */

    $query =
        "SELECT gambar FROM apotek WHERE id = ?";


    $stmt =
        $conn->prepare($query);


    if ($stmt) {

        $stmt->bind_param(
            "i",
            $id
        );


        $stmt->execute();


        $result =
            $stmt->get_result();


        $data_delete =
            $result->fetch_assoc();


        $gambar_delete =
            $data_delete['gambar'] ?? '';


        $stmt->close();

    } else {

        $gambar_delete = '';
    }


    /* Hapus data */

    $query =
        "DELETE FROM apotek WHERE id = ?";


    $stmt =
        $conn->prepare($query);


    if ($stmt) {

        $stmt->bind_param(
            "i",
            $id
        );


        if ($stmt->execute()) {


            /* Hapus file gambar */

            if (!empty($gambar_delete)) {

                $file_delete =
                    $upload_dir . $gambar_delete;


                if (file_exists($file_delete)) {

                    unlink($file_delete);
                }
            }


            $_SESSION['success'] =
                "Data berhasil dihapus.";

        } else {

            $_SESSION['error'] =
                "Data gagal dihapus.";
        }


        $stmt->close();

    } else {

        $_SESSION['error'] =
            "Query gagal diproses.";
    }


    header("Location: admin.php");

    exit;
}


/* =========================================================
   PESAN
========================================================= */

$success =
    $_SESSION['success'] ?? '';


$error =
    $_SESSION['error'] ?? '';


unset(
    $_SESSION['success'],
    $_SESSION['error']
);


/* =========================================================
   DATA UNTUK EDIT
========================================================= */

$edit_data = null;


if (isset($_GET['edit'])) {

    $edit_id =
        (int) $_GET['edit'];


    $query =
        "SELECT * FROM apotek WHERE id = ?";


    $stmt =
        $conn->prepare($query);


    if ($stmt) {

        $stmt->bind_param(
            "i",
            $edit_id
        );


        $stmt->execute();


        $result =
            $stmt->get_result();


        $edit_data =
            $result->fetch_assoc();


        $stmt->close();
    }
}


/* =========================================================
   PAGINATION
========================================================= */

$per_page = 2;


$page =
    isset($_GET['page'])
        ? (int) $_GET['page']
        : 1;


if ($page < 1) {

    $page = 1;
}


/* Hitung total data */

$count_query =
    "SELECT COUNT(*) AS total FROM apotek";


$count_result =
    $conn->query($count_query);


$total_data = 0;


if ($count_result) {

    $count_row =
        $count_result->fetch_assoc();


    $total_data =
        (int) $count_row['total'];
}


$total_pages =
    max(
        1,
        (int) ceil(
            $total_data / $per_page
        )
    );


if ($page > $total_pages) {

    $page =
        $total_pages;
}


$offset =
    ($page - 1) * $per_page;


/* Ambil data sesuai halaman */

$query = "
    SELECT *
    FROM apotek
    ORDER BY id
    LIMIT $per_page OFFSET $offset
";


$result =
    $conn->query($query);


$apotek_list = [];


if ($result) {

    while ($row =
        $result->fetch_assoc()
    ) {

        $apotek_list[] =
            $row;
    }
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

    <title>Admin - Data Apotek</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f6fa;

            color: #333;
        }


        /* =====================================================
           CONTAINER
        ===================================================== */

        .container {

            width: 100%;

            max-width: 1500px;

            margin: 0 auto;

            padding: 25px;
        }


        /* =====================================================
           ALERT
        ===================================================== */

        .alert {

            padding: 13px 16px;

            border-radius: 7px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        .alert-success {

            background: #e8f7ed;

            color: #257942;

            border:
                1px solid #bce5c9;
        }


        .alert-error {

            background: #fdecec;

            color: #b42318;

            border:
                1px solid #f3b8b8;
        }


        /* =====================================================
           MAIN CONTENT
        ===================================================== */

        .main-content {

            display: grid;

            grid-template-columns:
                1fr
                1fr;

            gap: 25px;

            align-items: stretch;

            margin-right: 100px;

            margin-left: 130px;
        }


        /* =====================================================
           FORM
        ===================================================== */

        .admin-form {

            background: #fff;

            padding: 22px;

            border-radius: 10px;

            box-shadow:
                0 2px 10px
                rgba(0,0,0,0.08);

            height: 100%;

            display: flex;

            flex-direction: column;
        }


        .admin-form form {

            height: 100%;

            display: flex;

            flex-direction: column;
        }


        .admin-form h3,
        .data-section h3 {

            margin-top: 0;

            margin-bottom: 18px;

            font-size: 19px;

            color: #333;
        }


        .form-group {

            margin-bottom: 13px;
        }


        .form-group > label {

            display: block;

            margin-bottom: 6px;

            font-size: 13px;

            font-weight: 600;

            color: #444;
        }


        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="password"] {

            width: 60%;

            height: 38px;

            padding: 8px 10px;

            border:
                1px solid #d8dbe5;

            border-radius: 6px;

            outline: none;

            font-size: 13px;

            background: #fff;
        }


        .form-group input:focus {

            border-color: #667eea;

            box-shadow:
                0 0 0 2px
                rgba(102,126,234,0.10);
        }


        /* =====================================================
           LATITUDE + LONGITUDE + GAMBAR
        ===================================================== */

        .coordinate-image-area {

            position: relative;

            width: 100%;
        }


        .coordinate-fields {

            width: 60%;
        }


        .coordinate-fields .form-group {

            margin-bottom: 13px;
        }


        .coordinate-fields input[type="text"] {

            width: 100%;

            height: 38px;

            padding: 8px 10px;

            border:
                1px solid #d8dbe5;

            border-radius: 6px;

            outline: none;

            font-size: 13px;

            background: #fff;
        }


        .coordinate-fields input[type="text"]:focus {

            border-color: #667eea;

            box-shadow:
                0 0 0 2px
                rgba(102,126,234,0.10);
        }


        /* =====================================================
           GAMBAR DI SEBELAH KANAN
        ===================================================== */

        .image-area {

            position: absolute;

            top: 0;

            right: 0;

            width: 210px;

            display: flex;

            flex-direction: column;

            align-items: center;
        }


        .image-upload-box {

            width: 210px;

            height: 280px;

            border:
                1px dashed #cfd3e6;

            border-radius: 10px;

            background: #f8f9ff;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            cursor: pointer;

            overflow: hidden;

            position: relative;

            transition: 0.2s;
        }


        .image-upload-box:hover {

            border-color: #667eea;

            background: #f1f3ff;
        }


        .image-upload-box input[type="file"] {

            display: none;
        }


        .image-upload-placeholder {

            color: #888;

            font-size: 11px;

            padding: 8px;
        }


        .image-upload-placeholder strong {

            display: block;

            color: #667eea;

            font-size: 14px;

            margin-bottom: 6px;
        }


        .image-preview {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;
        }


        /* =====================================================
           HAPUS GAMBAR
        ===================================================== */

        .delete-image {

            display: flex !important;

            align-items: center;

            justify-content: center;

            gap: 5px;

            width: 210px;

            margin-top: 7px;

            font-size: 11px !important;

            font-weight: normal !important;

            cursor: pointer;
        }


        .delete-image input {

            width: auto !important;

            height: auto !important;
        }


        /* =====================================================
           BUTTON
        ===================================================== */

        .form-actions {

            display: flex;

            justify-content: center;

            align-items: center;

            gap: 10px;

            margin-top: auto;

            padding-top: 20px;

            padding-bottom: 0;
        }


        .btn {

            display: inline-block;

            border: none;

            border-radius: 6px;

            padding: 10px 17px;

            font-size: 13px;

            cursor: pointer;

            text-decoration: none;

            text-align: center;
        }


        .btn-primary {

            background: #667eea;

            color: #fff;
        }


        .btn-primary:hover {

            background: #5568d8;
        }


        .btn-secondary {

            background: #e9ebf3;

            color: #444;
        }


        .btn-secondary:hover {

            background: #dddfea;
        }


        .btn-edit {

            background: #667eea;

            color: #fff;
        }


        .btn-delete {

            background: #dc3545;

            color: #fff;
        }


        /* =====================================================
           DATA SECTION
        ===================================================== */

        .data-section {

            background: #fff;

            padding: 22px;

            border-radius: 10px;

            box-shadow:
                0 2px 10px
                rgba(0,0,0,0.08);

            min-width: 0;

            height: 100%;
        }


        /* =====================================================
           DATA LIST
        ===================================================== */

        .data-list {

            width: 100%;

            display: grid;

            grid-template-columns: 1fr;

            gap: 15px;
        }


        /* =====================================================
           DATA CARD
        ===================================================== */

        .data-card {

            width: 100%;

            background: #fff;

            border-radius: 10px;

            overflow: hidden;

            box-shadow:
                0 2px 8px
                rgba(0,0,0,0.10);

            border:
                1px solid #eee;

            box-sizing: border-box;

            display: flex;

            flex-direction: row;

            align-items: stretch;
        }


        /* =====================================================
           GAMBAR DATA
        ===================================================== */

        .data-card-image {

            width: 120px;

            height: 160px;

            object-fit: cover;

            display: block;

            flex-shrink: 0;

            order: 2;

            align-self: center;

            margin: auto 25px auto 0;
        }


        .data-card-image-empty {

            width: 120px;

            height: 160px;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            background: #f1f2f6;

            color: #999;

            font-size: 13px;

            flex-shrink: 0;

            order: 2;

            align-self: center;

            margin: auto 0;
        }


        /* =====================================================
           DATA CONTENT
        ===================================================== */

        .data-card-content {

            padding: 15px;

            flex: 1;

            min-width: 0;

            order: 1;

            display: flex;

            flex-direction: column;
        }


        .data-card-content h4 {

            margin:
                0 0 10px 0;

            font-size: 17px;

            color: #333;
        }


        .data-info {

            display: grid;

            gap: 6px;

            font-size: 13px;

            color: #555;

            line-height: 1.45;
        }


        .data-info strong {

            color: #333;
        }


        .coordinates {

            font-size: 12px;

            color: #777;
        }


        .card-actions {

            display: flex;

            gap: 8px;

            margin-top: auto;

            padding-top: 13px;
        }


        .card-actions .btn {

            flex: 1;
        }


        /* =====================================================
           PAGINATION
        ===================================================== */

        .pagination {

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            margin-top: 20px;

            padding-top: 15px;

            border-top:
                1px solid #eee;

            flex-wrap: wrap;
        }


        .pagination a,
        .pagination span {

            min-width: 32px;

            height: 32px;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 0 8px;

            border-radius: 5px;

            text-decoration: none;

            font-size: 12px;
        }


        .pagination a {

            background: #f0f1f5;

            color: #444;
        }


        .pagination a:hover {

            background: #667eea;

            color: #fff;
        }


        .pagination .active {

            background: #667eea;

            color: #fff;
        }


        .pagination .disabled {

            background: #f5f5f5;

            color: #bbb;
        }


        /* =====================================================
           EMPTY DATA
        ===================================================== */

        .empty-data {

            padding: 50px 20px;

            text-align: center;

            color: #999;

            font-size: 14px;
        }


        /* =====================================================
           RESPONSIVE 1100px
        ===================================================== */

        @media (max-width: 1100px) {

            .main-content {

                grid-template-columns:
                    420px
                    minmax(0, 1fr);

                gap: 20px;
            }


            .image-area {

                width: 120px;
            }


            .image-upload-box {

                width: 120px;

                height: 160px;
            }


            .delete-image {

                width: 120px;
            }


            .data-card-image,
            .data-card-image-empty {

                width: 110px;

                height: 147px;
            }
        }


        /* =====================================================
           RESPONSIVE 900px
        ===================================================== */

        @media (max-width: 900px) {

            .main-content {

                grid-template-columns: 1fr;

                align-items: start;
            }


            .admin-form,
            .data-section {

                height: auto;
            }
        }


        /* =====================================================
           RESPONSIVE 700px
        ===================================================== */

        @media (max-width: 700px) {

            .coordinate-image-area {

                position: relative;

                width: 100%;
            }


            .coordinate-fields {

                width: 100%;
            }


            .coordinate-fields input[type="text"] {

                width: 100%;
            }


            .image-area {

                position: relative;

                top: auto;

                right: auto;

                width: 120px;

                margin: 10px auto 0;
            }


            .image-upload-box {

                width: 120px;

                height: 160px;
            }


            .delete-image {

                width: 120px;
            }


            .data-card-image,
            .data-card-image-empty {

                width: 100px;

                height: 133px;
            }
        }


        /* =====================================================
           RESPONSIVE 600px
        ===================================================== */

        @media (max-width: 600px) {

            .container {

                padding: 15px;
            }


            .main-content {

                grid-template-columns: 1fr;

                margin-left: 0;

                margin-right: 0;
            }


            .admin-form,
            .data-section {

                padding: 15px;

                height: auto;
            }


            .form-group input[type="text"],
            .form-group input[type="number"],
            .form-group input[type="password"] {

                width: 100%;
            }


            .coordinate-fields input[type="text"] {

                width: 100%;
            }


            .data-card {

                display: flex;

                flex-direction: row;
            }


            .data-card-image,
            .data-card-image-empty {

                width: 90px;

                height: 120px;

                flex-shrink: 0;
            }


            .data-card-content {

                padding: 12px;
            }


            .data-card-content h4 {

                font-size: 15px;

                margin-bottom: 8px;
            }


            .data-info {

                font-size: 12px;

                gap: 5px;
            }


            .coordinates {

                font-size: 11px;
            }


            .card-actions {

                margin-top: auto;

                padding-top: 8px;

                gap: 5px;
            }


            .card-actions .btn {

                padding: 8px 10px;

                font-size: 11px;
            }


            .form-actions {

                flex-direction: column;
            }


            .btn-primary,
            .btn-secondary {

                width: 100%;

                text-align: center;
            }
        }

    </style>

</head>


<body>


<?php include 'includes/navbar.php'; ?>


<div class="container">


    <!-- =====================================================
         ALERT
    ===================================================== -->

    <?php if (!empty($success)): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($success) ?>

        </div>

    <?php endif; ?>


    <?php if (!empty($error)): ?>

        <div class="alert alert-error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         MAIN CONTENT
    ===================================================== -->

    <div class="main-content">


        <!-- =================================================
             FORM
        ================================================== -->

        <div class="admin-form">

            <h3>

                <?= $edit_data
                    ? 'Edit Data Apotek'
                    : 'Tambah Data Apotek'
                ?>

            </h3>


            <?php if ($edit_data): ?>


                <!-- =========================================
                     FORM EDIT
                ========================================== -->

                <form
                    action="admin.php"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $edit_data['id'] ?>"
                    >


                    <!-- NAMA -->

                    <div class="form-group">

                        <label for="nama">
                            Nama Apotek
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            required
                            value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>"
                            placeholder="Masukkan nama apotek"
                        >

                    </div>


                    <!-- ALAMAT -->

                    <div class="form-group">

                        <label for="alamat">
                            Alamat
                        </label>

                        <input
                            type="text"
                            id="alamat"
                            name="alamat"
                            required
                            value="<?= htmlspecialchars($edit_data['alamat'] ?? '') ?>"
                            placeholder="Masukkan alamat apotek"
                        >

                    </div>


                    <!-- =================================================
                         LATITUDE + LONGITUDE + GAMBAR
                    ================================================== -->

                    <div class="coordinate-image-area">


                        <!-- BAGIAN KIRI -->

                        <div class="coordinate-fields">


                            <!-- LATITUDE -->

                            <div class="form-group">

                                <label for="latitude">
                                    Latitude
                                </label>

                                <input
                                    type="text"
                                    id="latitude"
                                    name="latitude"
                                    required
                                    value="<?= htmlspecialchars($edit_data['latitude'] ?? '') ?>"
                                    placeholder="0.50404"
                                >

                            </div>


                            <!-- LONGITUDE -->

                            <div class="form-group">

                                <label for="longitude">
                                    Longitude
                                </label>

                                <input
                                    type="text"
                                    id="longitude"
                                    name="longitude"
                                    required
                                    value="<?= htmlspecialchars($edit_data['longitude'] ?? '') ?>"
                                    placeholder="117.537003"
                                >

                            </div>

                        </div>


                        <!-- BAGIAN KANAN - GAMBAR -->

                        <div class="image-area">

                            <label
                                class="image-upload-box"
                                for="gambar"
                            >

                                <input
                                    type="file"
                                    id="gambar"
                                    name="gambar"
                                    accept="image/*"
                                    onchange="previewImage(event)"
                                >


                                <?php if (!empty($edit_data['gambar'])): ?>

                                    <img
                                        id="imagePreview"
                                        class="image-preview"
                                        src="uploads/<?= htmlspecialchars($edit_data['gambar']) ?>"
                                        alt="Preview gambar"
                                    >

                                <?php else: ?>

                                    <div
                                        id="imagePlaceholder"
                                        class="image-upload-placeholder"
                                    >

                                        <strong>
                                            📷 Pilih Gambar
                                        </strong>

                                        Klik untuk memilih gambar

                                    </div>


                                    <img
                                        id="imagePreview"
                                        class="image-preview"
                                        style="display:none;"
                                        alt="Preview gambar"
                                    >

                                <?php endif; ?>

                            </label>


                            <?php if (!empty($edit_data['gambar'])): ?>

                                <label class="delete-image">

                                    <input
                                        type="checkbox"
                                        name="hapus_gambar"
                                        value="1"
                                    >

                                    Hapus gambar lama

                                </label>

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- TELEPON -->

                    <div class="form-group">

                        <label for="telepon">
                            Nomor Telepon
                        </label>

                        <input
                            type="text"
                            id="telepon"
                            name="telepon"
                            value="<?= htmlspecialchars($edit_data['telepon'] ?? '') ?>"
                            placeholder="Masukkan nomor telepon"
                        >

                    </div>


                    <!-- JAM OPERASIONAL -->

                    <div class="form-group">

                        <label for="jam_operasional">
                            Jam Operasional
                        </label>

                        <input
                            type="text"
                            id="jam_operasional"
                            name="jam_operasional"
                            value="<?= htmlspecialchars($edit_data['jam_operasional'] ?? '') ?>"
                            placeholder="Contoh: 08.00 - 22.00"
                        >

                    </div>


                    <!-- NOMOR IZIN -->

                    <div class="form-group">

                        <label for="no_izin">
                            Nomor Izin Apotek
                        </label>

                        <input
                            type="text"
                            id="no_izin"
                            name="no_izin"
                            value="<?= htmlspecialchars($edit_data['no_izin'] ?? '') ?>"
                            placeholder="Masukkan nomor izin"
                        >

                    </div>


                    <!-- JENIS LAYANAN -->

                    <div class="form-group">

                        <label for="jenis_layanan">
                            Jenis Layanan
                        </label>

                        <input
                            type="text"
                            id="jenis_layanan"
                            name="jenis_layanan"
                            value="<?= htmlspecialchars($edit_data['jenis_layanan'] ?? '') ?>"
                            placeholder="Masukkan jenis layanan"
                        >

                    </div>


                    <!-- BUTTON -->

                    <div class="form-actions">

                        <button
                            type="submit"
                            name="update"
                            class="btn btn-primary"
                        >
                            Update Data
                        </button>


                        <a
                            href="admin.php"
                            class="btn btn-secondary"
                        >
                            Batal
                        </a>

                    </div>

                </form>


            <?php else: ?>


                <!-- =========================================
                     FORM TAMBAH
                ========================================== -->

                <form
                    action="admin.php"
                    method="POST"
                    enctype="multipart/form-data"
                >


                    <!-- NAMA -->

                    <div class="form-group">

                        <label for="nama">
                            Nama Apotek
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            required
                            placeholder="Masukkan nama apotek"
                        >

                    </div>


                    <!-- ALAMAT -->

                    <div class="form-group">

                        <label for="alamat">
                            Alamat
                        </label>

                        <input
                            type="text"
                            id="alamat"
                            name="alamat"
                            required
                            placeholder="Masukkan alamat apotek"
                        >

                    </div>


                    <!-- =================================================
                         LATITUDE + LONGITUDE + GAMBAR
                    ================================================== -->

                    <div class="coordinate-image-area">


                        <!-- BAGIAN KIRI -->

                        <div class="coordinate-fields">


                            <!-- LATITUDE -->

                            <div class="form-group">

                                <label for="latitude">
                                    Latitude
                                </label>

                                <input
                                    type="text"
                                    id="latitude"
                                    name="latitude"
                                    required
                                    placeholder="0.50404"
                                >

                            </div>


                            <!-- LONGITUDE -->

                            <div class="form-group">

                                <label for="longitude">
                                    Longitude
                                </label>

                                <input
                                    type="text"
                                    id="longitude"
                                    name="longitude"
                                    required
                                    placeholder="117.537003"
                                >

                            </div>

                        </div>


                        <!-- BAGIAN KANAN - GAMBAR -->

                        <div class="image-area">

                            <label
                                class="image-upload-box"
                                for="gambar"
                            >

                                <input
                                    type="file"
                                    id="gambar"
                                    name="gambar"
                                    accept="image/*"
                                    onchange="previewImage(event)"
                                >


                                <div
                                    id="imagePlaceholder"
                                    class="image-upload-placeholder"
                                >

                                    <strong>
                                        📷 Pilih Gambar
                                    </strong>

                                    Klik untuk memilih gambar

                                </div>


                                <img
                                    id="imagePreview"
                                    class="image-preview"
                                    style="display:none;"
                                    alt="Preview gambar"
                                >

                            </label>

                        </div>

                    </div>


                    <!-- TELEPON -->

                    <div class="form-group">

                        <label for="telepon">
                            Nomor Telepon
                        </label>

                        <input
                            type="text"
                            id="telepon"
                            name="telepon"
                            placeholder="Masukkan nomor telepon"
                        >

                    </div>


                    <!-- JAM OPERASIONAL -->

                    <div class="form-group">

                        <label for="jam_operasional">
                            Jam Operasional
                        </label>

                        <input
                            type="text"
                            id="jam_operasional"
                            name="jam_operasional"
                            placeholder="Contoh: 08.00 - 22.00"
                        >

                    </div>


                    <!-- NOMOR IZIN -->

                    <div class="form-group">

                        <label for="no_izin">
                            Nomor Izin Apotek
                        </label>

                        <input
                            type="text"
                            id="no_izin"
                            name="no_izin"
                            placeholder="Masukkan nomor izin"
                        >

                    </div>


                    <!-- JENIS LAYANAN -->

                    <div class="form-group">

                        <label for="jenis_layanan">
                            Jenis Layanan
                        </label>

                        <input
                            type="text"
                            id="jenis_layanan"
                            name="jenis_layanan"
                            placeholder="Masukkan jenis layanan"
                        >

                    </div>


                    <!-- BUTTON -->

                    <div class="form-actions">

                        <button
                            type="submit"
                            name="tambah"
                            class="btn btn-primary"
                        >
                            Tambah Data
                        </button>

                    </div>

                </form>

            <?php endif; ?>

        </div>


        <!-- =================================================
             DAFTAR DATA APOTEK
        ================================================== -->

        <div class="data-section">

            <h3>
                Daftar Data Apotek
            </h3>


            <div class="data-list">

                <?php if (!empty($apotek_list)): ?>


                    <?php foreach ($apotek_list as $data): ?>

                        <div class="data-card">


                            <!-- INFORMASI DI KIRI -->

                            <div class="data-card-content">

                                <h4>

                                    <?= htmlspecialchars($data['nama']) ?>

                                </h4>


                                <div class="data-info">


                                    <div>

                                        <strong>
                                            Alamat:
                                        </strong>

                                        <?= htmlspecialchars($data['alamat']) ?>

                                    </div>


                                    <div>

                                        <strong>
                                            Telepon:
                                        </strong>

                                        <?= htmlspecialchars($data['telepon']) ?>

                                    </div>


                                    <div>

                                        <strong>
                                            Jam Operasional:
                                        </strong>

                                        <?= htmlspecialchars($data['jam_operasional']) ?>

                                    </div>


                                    <div>

                                        <strong>
                                            No. Izin:
                                        </strong>

                                        <?= htmlspecialchars($data['no_izin']) ?>

                                    </div>


                                    <div>

                                        <strong>
                                            Jenis Layanan:
                                        </strong>

                                        <?= htmlspecialchars($data['jenis_layanan']) ?>

                                    </div>


                                    <div class="coordinates">

                                        <strong>
                                            Latitude:
                                        </strong>

                                        <?= htmlspecialchars($data['latitude']) ?>

                                        <br>

                                        <strong>
                                            Longitude:
                                        </strong>

                                        <?= htmlspecialchars($data['longitude']) ?>

                                    </div>

                                </div>


                                <!-- ACTION -->

                                <div class="card-actions">

                                    <a
                                        href="admin.php?edit=<?= (int) $data['id'] ?>"
                                        class="btn btn-edit"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="admin.php?delete=<?= (int) $data['id'] ?>"
                                        class="btn btn-delete"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')"
                                    >
                                        Hapus
                                    </a>

                                </div>

                            </div>


                            <!-- GAMBAR DI KANAN -->

                            <?php if (!empty($data['gambar'])): ?>

                                <img
                                    src="uploads/<?= htmlspecialchars($data['gambar']) ?>"
                                    alt="<?= htmlspecialchars($data['nama']) ?>"
                                    class="data-card-image"
                                >

                            <?php else: ?>

                                <div class="data-card-image-empty">

                                    Tidak ada gambar

                                </div>

                            <?php endif; ?>


                        </div>

                    <?php endforeach; ?>


                <?php else: ?>

                    <div class="empty-data">

                        Belum ada data apotek.

                    </div>

                <?php endif; ?>

            </div>


            <!-- PAGINATION -->

            <?php if ($total_pages > 1): ?>

                <div class="pagination">


                    <!-- PREVIOUS -->

                    <?php if ($page > 1): ?>

                        <a
                            href="admin.php?page=<?= $page - 1 ?>"
                        >
                            &laquo;
                        </a>

                    <?php else: ?>

                        <span class="disabled">
                            &laquo;
                        </span>

                    <?php endif; ?>


                    <!-- NOMOR HALAMAN -->

                    <?php for (
                        $i = 1;
                        $i <= $total_pages;
                        $i++
                    ): ?>

                        <?php if ($i == $page): ?>

                            <span class="active">

                                <?= $i ?>

                            </span>

                        <?php else: ?>

                            <a
                                href="admin.php?page=<?= $i ?>"
                            >

                                <?= $i ?>

                            </a>

                        <?php endif; ?>

                    <?php endfor; ?>


                    <!-- NEXT -->

                    <?php if ($page < $total_pages): ?>

                        <a
                            href="admin.php?page=<?= $page + 1 ?>"
                        >
                            &raquo;
                        </a>

                    <?php else: ?>

                        <span class="disabled">
                            &raquo;
                        </span>

                    <?php endif; ?>


                </div>

            <?php endif; ?>


        </div>

    </div>

</div>


<!-- =========================================================
     JAVASCRIPT PREVIEW GAMBAR
========================================================= -->

<script>

function previewImage(event) {

    const file =
        event.target.files[0];


    const preview =
        document.getElementById(
            'imagePreview'
        );


    const placeholder =
        document.getElementById(
            'imagePlaceholder'
        );


    if (file) {

        const reader =
            new FileReader();


        reader.onload =
            function(e) {

                preview.src =
                    e.target.result;


                preview.style.display =
                    'block';


                if (placeholder) {

                    placeholder.style.display =
                        'none';
                }

            };


        reader.readAsDataURL(file);
    }

}

</script>


</body>

</html>