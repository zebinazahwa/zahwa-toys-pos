<?php
// ======= FILE: logout.php =======
// Tujuan file ini: Menghapus sesi dan keluar dari sistem.

session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit();
?>
