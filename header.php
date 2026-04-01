<?php
// Memberi tahu server untuk memulai sesi penyimpanan kecil (session) di browser pengunjung, untuk mencatat keranjang dsb
session_start();
// Menutup tag awal web PHP
?>
<!-- Tag standar pendefinisi bahwa ini dokumen tipe HTML -->
<!DOCTYPE html>
<!-- Mengubah bahasa dokumen teks default menjadi Bahasa Indonesia -->
<html lang="id">
<!-- Tag <head> untuk menyimpan pengaturan background komputer yang tak terlihat pengunjung -->
<head>
    <!-- Tag meta agar tulisan bisa dibaca normal oleh browser pakai set karakter UTF-8 -->
    <meta charset="UTF-8">
    <!-- Meta viewport ini rahasia agar web bisa nyusut di layar HP Android/iPhone (Responsive) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Mengganti Teks nama tab browser di paling atas aplikasi Google Chrome -->
    <title>Sistem Kasir - Zahwa Toys</title>
    
    <!-- Link href= digunakan untuk mengaitkan / memanggil baju desain kita (CSS) dari file sebelah -->
    <link rel="stylesheet" href="style.css">
<!-- Menutup pengaturan head -->
</head>
<!-- Membuka jasad web / body utama (Yang bisa dilihat oleh mata) -->
<body>

<!-- Tag header khusus untuk pembungkus kotak merah di bagian layar atas (Navbar) -->
<header>
    <!-- Tag Heading 1 yang berfungsi menampilkan teks raksana/Logo Judul "Zahwa Toys" -->
    <h1>Zahwa Toys</h1>
    
    <!-- Menambahkan area Navigasi (Tombol-tombol menu panjang) -->
    <nav>
        <!-- Tag ul singkatan dari Unordered List (Daftar list yang berupa garis ke samping/bawah) -->
        <ul>
            <!-- Tag li (List Item), dan <a> ini berarti tombol Hyperlink (Bisa diklik untuk lompat file) -->
            <!-- Link menuju file beranda (index) -->
            <li><a href="index.php">Dashboard</a></li>
            <!-- Link menuju file master data Pelanggan -->
            <li><a href="pelanggan.php">Pelanggan</a></li>
            <!-- Link menuju file master data Produk/Item jualan -->
            <li><a href="produk.php">Produk</a></li>
            <!-- Link langsung masuk ke menu scan Mesin Kasir -->
            <li><a href="penjualan.php">Penjualan</a></li>
            <!-- Link untuk melihat riwayat atau histori struk -->
            <li><a href="detail_penjualan.php">Detail Penjualan</a></li>
        <!-- Menutup tag pembungkus tombol-tombol -->
        </ul>
    <!-- Menutup tag area navigasi -->
    </nav>
<!-- Menutup atap merah header -->
</header>

<!-- Membuka container/kotak bungkus isi utama. Tulisan class="container" mengikuti setingan CSS style.css -->
<!-- Ingat, tag MAIN ini belum ditutup (</main>)! Karena dia ditutup di file tujuan (footer.php) -->
<main class="container">
