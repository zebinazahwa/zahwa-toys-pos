<?php
require_once 'backend/koneksi.php';

$tables = ['pelanggan', 'produk', 'penjualan', 'detail_penjualan'];

foreach ($tables as $table) {
    echo "--- Table: $table ---\n";
    $result = mysqli_query($koneksi, "DESCRIBE $table");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']} | {$row['Extra']}\n";
    }
    echo "\n";
}
?>
