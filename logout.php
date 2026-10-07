<?php 
if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
} 
 
// Hapus semua data session 
$_SESSION = []; 
 
// Hancurkan session 
session_destroy(); 
 
// Setelah logout, kembali ke halaman awal 
header("Location: apotek.php"); 
exit(); 
?>