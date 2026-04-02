<?php
// ======= FILE: auth_check.php =======
// Tujuan file ini: Memastikan bahwa hanya admin yang sudah login yang bisa membuka halaman.

// 1. Memulai sesi (session)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Mengecek apakah variabel sesi 'admin_id' sudah ada (artinya sudah login)
if (!isset($_SESSION['admin_id'])) {
    // 3. Jika belum login, tendang (alihkan) ke halaman login.php
    header("Location: login.php");
    exit();
}
?>
