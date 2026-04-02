<?php
require_once 'backend/koneksi.php';

$curdate = mysqli_query($koneksi, "SELECT CURDATE() as tgl")->fetch_assoc()['tgl'];
$now = mysqli_query($koneksi, "SELECT NOW() as skrg")->fetch_assoc()['skrg'];

$total_all = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM penjualan")->fetch_assoc()['c'];
$total_today = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM penjualan WHERE DATE(tanggal_penjualan) = CURDATE()")->fetch_assoc()['c'];

echo "MySQL CURDATE: $curdate\n";
echo "MySQL NOW: $now\n";
echo "Total All TRX: $total_all\n";
echo "Total Today TRX: $total_today\n";

$last_5 = mysqli_query($koneksi, "SELECT id, tanggal_penjualan FROM penjualan ORDER BY id DESC LIMIT 5");
echo "Last 5 Sales:\n";
while($r = mysqli_fetch_assoc($last_5)) {
    echo "- ID: {$r['id']}, Date: {$r['tanggal_penjualan']}\n";
}
?>
