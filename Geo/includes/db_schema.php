<?php

// Menambahkan kolom yang belum ada pada tabel apotek agar aplikasi tetap berjalan.

function ensure_column($conn, $column, $ddl)
{
    $check = mysqli_query(
        $conn,
        "SHOW COLUMNS FROM apotek LIKE '$column'"
    );

    if (!$check) {
        return;
    }

    if (mysqli_num_rows($check) === 0) {
        mysqli_query(
            $conn,
            "ALTER TABLE apotek ADD COLUMN $ddl"
        );
    }
}

ensure_column(
    $conn,
    'kategori',
    "kategori VARCHAR(20) NOT NULL DEFAULT 'apotek' AFTER gambar"
);

ensure_column(
    $conn,
    'jam_operasional',
    "jam_operasional VARCHAR(255) NULL AFTER kategori"
);

ensure_column(
    $conn,
    'no_izin',
    "no_izin VARCHAR(255) NULL AFTER jam_operasional"
);

ensure_column(
    $conn,
    'jenis_layanan',
    "jenis_layanan VARCHAR(255) NULL AFTER no_izin"
);

?>