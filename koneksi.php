<?php
// Tag pembuka bahasa PHP agar server ngerti bahwa kode ke bawah ini adalah program PHP

// ======= FILE: koneksi.php =======
// Tujuan file ini: Membuka pintu gerbang antara file-file PHP kita menuju Database XAMPP.

// Variabel $host untuk memberitahu alamat servernya. "localhost" adalah alamat komputer kita sendiri.
$host = 'localhost';

// Variabel $user untuk identitas login MySQL. Bawaannya (default) XAMPP itu "root".
$user = 'root';

// Variabel $pass untuk kata sandi MySQL. Bawaannya XAMPP itu kosong tidak ada password.
$pass = '';

// Variabel $db untuk memberitahu nama folder/kumpulan tabel database yang mau kita pakai ("zahwa_toys").
$db = 'zahwa_toys';

// Fungsi utama mysqli_connect bertugas mengeksekusi kunci pintu dengan 4 gembok/variabel yang kita tentukan tadi.
// Hasil kunci ini dimasukkan ke wadah bernama $koneksi.
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Mengecek kondisi (if), apakah jembatan $koneksi itu GAGAL terbentuk? (tanda seru ! artinya GAGAL/TIDAK)
if (!$koneksi) {
    // Jika gagal, program DIMATIKAN seketika (die) lalu dimunculkan pesan "Koneksi gagal" beserta detail error bawaannya.
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menutup program PHP secara resmi. Jika file ini di-include oleh file lain, kode bawahnya akan disambung
?>
