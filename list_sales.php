<?php
require_once 'backend/koneksi.php';
$q = mysqli_query($koneksi, 'SELECT id, tanggal_penjualan, total_harga FROM penjualan ORDER BY id DESC LIMIT 5');
echo "ID | DATE | TOTAL\n";
while($r = mysqli_fetch_assoc($q)) {
    echo "{$r['id']} | {$r['tanggal_penjualan']} | {$r['total_harga']}\n";
}
?>
