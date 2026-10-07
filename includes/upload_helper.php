<?php

function handle_upload_gambar($file, $upload_dir_fs)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [
            'success' => true,
            'filename' => '',
            'message' => ''
        ];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'filename' => '',
            'message' => 'Upload gambar gagal (kode error PHP: ' . $file['error'] . ')'
        ];
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        return [
            'success' => false,
            'filename' => '',
            'message' => 'Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.'
        ];
    }

    $max_size = 2 * 1024 * 1024;

    if ($file['size'] > $max_size) {
        return [
            'success' => false,
            'filename' => '',
            'message' => 'Ukuran gambar maksimal 2MB.'
        ];
    }

    // Pastikan folder uploads tersedia
    if (!is_dir($upload_dir_fs)) {
        if (!mkdir($upload_dir_fs, 0755, true)) {
            return [
                'success' => false,
                'filename' => '',
                'message' => 'Folder uploads tidak dapat dibuat.'
            ];
        }
    }

    $new_name = 'apotek_' . uniqid() . '_' . time() . '.' . $ext;

    if (!move_uploaded_file($file['tmp_name'], $upload_dir_fs . $new_name)) {
        return [
            'success' => false,
            'filename' => '',
            'message' => 'Gagal menyimpan file gambar ke server. Cek folder uploads/.'
        ];
    }

    return [
        'success' => true,
        'filename' => $new_name,
        'message' => 'Gambar berhasil diupload.'
    ];
}


function delete_gambar_file($upload_dir_fs, $filename)
{
    if (!empty($filename) && file_exists($upload_dir_fs . $filename)) {
        @unlink($upload_dir_fs . $filename);
    }
}