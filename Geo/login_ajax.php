<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit();
}

include "includes/auth_config.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === $ADMIN_USERNAME && $password === $ADMIN_PASSWORD) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Username atau password salah!']);
}
