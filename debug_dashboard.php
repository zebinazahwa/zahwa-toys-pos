<?php
require_once 'backend/koneksi.php';

echo "<h2>Informasi Sistem Pengujian (Debug)</h2>";

// Cek table structures
$tables = ['pelanggan', 'produk', 'penjualan', 'detail_penjualan'];
foreach ($tables as $t) {
    echo "<h3>Struktur Tabel: $t</h3>";
    $q = mysqli_query($koneksi, "DESCRIBE $t");
    if ($q) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; margin-bottom: 15px;'>";
        while ($r = mysqli_fetch_assoc($q)) {
            echo "<tr><td>" . implode("</td><td>", $r) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "Tabel $t TIDAK DITEMUKAN!<br>";
    }
}

// Cek current date in MySQL
$date_q = mysqli_query($koneksi, "SELECT CURDATE() as tgl_mysql, NOW() as jam_mysql");
$date_r = mysqli_fetch_assoc($date_q);
echo "<h3>Informasi Waktu Server (MySQL)</h3>";
echo "Tanggal: " . $date_r['tgl_mysql'] . "<br>";
echo "Waktu: " . $date_r['jam_mysql'] . "<br>";

// Cek data hari ini
$q_trx = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penjualan WHERE DATE(tanggal_penjualan) = CURDATE()");
$r_trx = mysqli_fetch_assoc($q_trx);
echo "<h3>Statistik Transaksi Hari Ini</h3>";
echo "Total Transaksi: " . ($r_trx['total'] ?? '0') . "<br>";

$q_rev = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM penjualan WHERE DATE(tanggal_penjualan) = CURDATE()");
$r_rev = mysqli_fetch_assoc($q_rev);
echo "Total Pendapatan: Rp " . number_format($r_rev['total'] ?? 0, 0, ',', '.') . "<br>";

// Cek all sales to see if dates are different
echo "<h3>Riwayat Transaksi Terbaru (5 Data Terakhir)</h3>";
$q_all = mysqli_query($koneksi, "SELECT id, tanggal_penjualan, total_harga FROM penjualan ORDER BY id DESC LIMIT 5");
if ($q_all) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    while ($r = mysqli_fetch_assoc($q_all)) {
        echo "<tr><td>" . implode("</td><td>", $r) . "</td></tr>";
    }
    echo "</table>";
}
?>
