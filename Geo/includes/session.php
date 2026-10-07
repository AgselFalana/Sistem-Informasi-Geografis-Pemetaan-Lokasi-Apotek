<?php
// Session helper
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']);
}

function get_admin_username() {
    return $_SESSION['admin_username'] ?? 'Guest';
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        header("Location: login.php");
        exit();
    }
}
?>
