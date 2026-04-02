<?php
require_once 'auth_check.php';
?>
<?php
// ======= FILE: produk_tambah.php (Halaman Form Untuk Memasukkan Mainan Baru) =======

// Syarat Utama PHP: Melakukan interogasi ke jembatan koneksi.php agar kita bisa mengobrol dengan database
require_once 'backend/koneksi.php';

// Memanggil desain visual Header Top agar logo dan menunya termunculkan ke Layar monitor
include 'frontend/header.php';

// ==== BLOK LOGIKA (BAGIAN SULAP PENYIMPANAN DATA SAAT TOMBOL SUMBIT DITEKAN) ====

// Cek Pertanyaan IF (Jika Server Menangkap pengiriman Form bermetode POST yang kebetulan nyangkut pada name="simpan"... )
// Ingat: $_POST adalah "Truk Sembunyi" dari PHP. User nggak tau isinya di luar/URL gak beda URL-nya (Aman).
if(isset($_POST['simpan'])) {
    
    // Menerima input isian dari Kotak HTML <input name="nama">...
    // MENGAPA dipakein Jaket mysqli_real_escape_string()? INI PENTING! Agar "Ilmu Hitam" (SQL Injection Attack dari hacker iseng) yang ngetik aneh-aneh (seperti simbol ' " \) dicuci bersih jadi teks biasa nggak mematikan kode database kita.
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    
    // Menerima ketikan pengunjung di input harganya... (Dicuci lagi pakai mysqli_real...)
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    
    // Mencuci angka stok mainan yang dikeluar pengunjung di input name="stok"...
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);

    // MERAMU MANTRA SQL: Memasukkan/Insert INTO tabel nama,harga,stok... dengan Nilai (VALUES) yang ada di Variabel kita ('$nama', dll)
    // Titipkan mantra panjang ini dalam kurir PHP `mysqli_query`
    $simpan = mysqli_query($koneksi, "INSERT INTO produk (nama_produk, harga, stok) VALUES ('$nama', '$harga', '$stok')");

    // Mengecek nasib si kurir: (Diterima gak di database nya?). Jika TRUE berhasil...
    if($simpan) {
        // Cara Jitu Memperingatkan Pemilik pakai PHP dan Javascript.
        // echo script ini langsung nembakin Javascript Pop-up (alert), dan kalau diOK, otomatis browser mental ke halaman `window.location='produk.php'` . Nggak ribet!
        echo "<script>alert('Data produk baru telah berhasil disimpan.'); window.location='produk.php';</script>";
        
    // Kalau database nolak atau error (Misal server lagi offline/ada kesemrawutan nama tabel)
    } else {
        // Tampilkan Teks Kotakan Merah di atas halaman. "Gagal"
        echo "<div class='alert alert-danger'>Gagal menyimpan data produk. Silakan hubungi administrator sistem.</div>";
    }
}
// Akhir blok sulapnya ya! 
?>

<!-- ==== BAGIAN TAMPILAN (PEMBUATAN FORMULAR HTML MURNI) ==== -->

<!-- Judul Halaman di atas Form -->
<h2>Tambah Produk Baru</h2>

<!-- Membungkus ruang lembar kerja dengan Card/Kartu biar mirip desain Google Material (Berada di tengah sedikit timbul bayangannya) -->
<div class="card">
    
    <!-- Tag Ajaib form! Atribut METHOD="POST". Ini adalah teknik di mana saat tombol submit di HTML ditekan, semua nilai yg di input dikirim sembunyi-sembunyi dan diurus oleh PHP bagian blok logika di file yang sama (Action="" artinya dituju ke tempat diri kita sendiri) -->
    <form method="POST" action="">
        
        <!-- Bungkus input grup biar rapi dan dilonggarkan spasi (margin-bottom) -->
        <div class="form-group">
            <!-- Label hanya untuk tulisan pemanis penjelas kotak isian di bawahnya -->
            <label>Nama Produk</label>
            <!-- Kotak untuk diketik (input), tipe bebas huruf/angka (text). Name="nama" adalah GANTUNGAN yang jadi nama alamat buat kurir POST PHP menangkap nilainya nanti. Parameter required (berarti wajib ngisi / tidak boleh dikosongkan) -->
            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Boneka Teddy Bear">
        </div>
        
        <!-- Grup isian baris kedua -->
        <div class="form-group">
            <label>Harga (Rupiah)</label>
            <!-- Tipe Number artinya user bodoh/usil gabakalan bisa mencetin tombol Spasi, A-Z. Cuma angka. Atribut Min=0 Biar gak minus. Namanya ditangkap pakai gembok 'harga'. -->
            <input type="number" name="harga" class="form-control" required placeholder="Contoh: 150000" min="0">
        </div>
        
        <!-- Grup baris ketiga (Terkahir untuk stok awal barang tsb hadir di toko) -->
        <div class="form-group">
            <label>Jumlah Stok</label>
            <input type="number" name="stok" class="form-control" required placeholder="Contoh: 10" min="0">
        </div>
        
        <!-- Nah ini mesin pemicu/pemukul bola-nya (Tombol Submit). Atribut name="simpan" inilah yang bakal ngetring ke antena PHP (if-isset($_POST['simpan']) di baris 11 di atas tadi. -->
        <button type="submit" name="simpan" class="btn">Simpan Data</button>
        
        <!-- Ini adalah Tombol Pasif (cuma pakai tag hyperlink a) yang dilempar balik aja kalau user mau membatalkan/balik arah -->
        <!-- Css memakaikan baju khusus yaitu btn-danger biar warnanya beda (merah peringatan), mengandalkan mata/perilaku visual (UX Design).  -->
        <a href="produk.php" class="btn btn-danger">Batal</a>
    
    <!-- Tutup pintu bungkus Formulirnya -->
    </form>
<!-- Tutup kotak Card mejanya -->
</div>

<?php
// Seperti halnya yang sering terucap, sebuah website yang baik tak boleh lupa dipakaikan alas kaki (Footer)
include 'frontend/footer.php';
?>
