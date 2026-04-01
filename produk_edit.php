<?php
// ======= FILE: produk_edit.php (Pusat Operasi Bedah/Mengubah Mainan dari yang Sudah Ada) =======

// Membutuhkan tali sambungan database secara wajib dari file koneksi
require_once 'koneksi.php';

// Tempelkan tampilan navigasi atas web dari template yang disetujui (header.php)
include 'header.php';

// MEMERIKSA PERINTAH URL SI PEMANGGIL (Siapa Korbannya?)
// Kita ambil nomor ID yang tertempel pada url misalnya di jendela: ?id=7
// Berarti variabel $id bernilai "7". ID adalah penanda pasport di database (Primary Key)
$id = $_GET['id'];

// == BLOK 1: NGAMBIL HASIL LAWAS UNTUK DITAMPILAAN DI FORMULA HTML ==
// Kita jalankan SQL: Tolong ambil SEMUA KOLOM dari tabel produk, TAPI CUMA UNTUK YANG NOMOR PASPOR "7" tadi (WHERE id='7').
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id'");
// Hasil lemari berantakan yang ditemukan itu lalu kita pisah-pisahin di laci dalam array `assoc` (Singkatan Associative Mappings / Di mapping berdasarkan nama kotaknya).
$data = mysqli_fetch_assoc($query);


// == BLOK 2: BILA MANUSIA NGE-KLIK TOMBOL "SIMPAN PERUBAHAN" DI BAWAH (Method POST) ==
// IF ini bakal diabaikan komputer, sampai orang itu mau merubah klik tombol yang ber-name="update" (Ngedit Data).
if(isset($_POST['update'])) {
    
    // Tarik isian hasil revisi yang baru diketik orang...
    // Cuci teks pakai sanitizer terobosan canggih mysqqli_real_escape_string agar karakter nyeleneh tidak bikin sistem kolaps
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    
    // Sama juga... Cuci inputan harganya ...
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    
    // Sama juga... Cuci Jumlah Stok...
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);

    // Tulis Mantra Revisi/Ubah Tabel (UPDATE).
    // Rumus robot SQL-nya: TOLONG UPDATE produk, ATUR NAMA='$nama' ... TAPI jangan ngawur semuanya! LAKUKAN HANYA PADA YANG KTP/Kunci Utama-nya sama dengan Si ID ("7") tadi.
    $update = mysqli_query($koneksi, "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok' WHERE id='$id'");

    // Jika proses update tabel benar dan lancar dieksekusi ...
    if($update) {
        // Javascript mencetak Notifikasi Alert Pop-UP dan melempar halaman pulang kembali ke gudang produk utama!
        echo "<script>alert('Sip! Spesifikasi Mainan ini berhasil direvisi ya!'); window.location='produk.php';</script>";
        
    // Kalau salah/server ke-block ...
    } else {
        // Print Kotak Peringatan Warna Merah HTML CSS ...
        echo "<div class='alert alert-danger'>Yahh Gagal diubah deh produknya. Sistem lagi rewel kali?</div>";
    }
}
?>

<!-- === LAYOUT PEMANDANGAN HALAMAN EDITAN === -->

<!-- Judul Gede Untuk Web -->
<h2>Operasi Edit Data Produk</h2>

<!-- Mengemas di Kotak Cantik Berkelas 'Card' (yang bikin putih menyala) -->
<div class="card">
    
    <!-- Mengajukan form pake POST, lalu melapor (Action="") ke dirinya sendirinya / file PHP barusan. -->
    <form method="POST" action="">
        <!-- Jarak barisa renggang untuk Nama Produk -->
        <div class="form-group">
            <label>Penggantian Nama Produk / Nama Mainan</label>
            <!-- TRIK UTAMA EDIT: Atribut `value=" "` kita sematkan. Dan kita sisipkan ECHO PHP `$data[...]`! 
                 Tujuan Trik ini? Biar kotak isiannya TIDAK KOSONG pas halaman baru termuat. Isiannya kita jejali dari data lawas/lama secara default. -->
            <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($data['nama_produk'] ?? ''); ?>" required>
        </div>
        
        <!-- Jarak barisa grup untuk Harga Lama -->
        <div class="form-group">
            <label>Setel Ulang Harga Asli Satuan Rupiah (Tanpa titik koma)</label>
            <!-- Menampilkan secara bawaan value array asosiatif hasil query ke-[harga] sebelumnya. -->
            <!-- Jika array kosong/null (karena misalnya ID gak valid), ya kasih angka sakti '0' lewat syntax ( ?? '0' ) -->
            <input type="number" name="harga" class="form-control" value="<?php echo $data['harga'] ?? '0'; ?>" required min="0">
        </div>
        
        <!-- Kelompok baris isian khusus Stok tersisa ... -->
        <div class="form-group">
            <label>Koreksi Jumlah Stok Asli (PCS)</label>
            <!-- Menyematkan memori hasil select $data['stok'] sebagai pre-filed kotak angkanya -->
            <!-- min="0" untuk proteksi frontend biar pengunjung gak ngetik stock minus. -->
            <input type="number" name="stok" class="form-control" value="<?php echo $data['stok'] ?? '0'; ?>" required min="0">
        </div>
        
        <!-- Tombol Tipe Submission Pemantik. Begitu dipencet ke kiri... data ngalir ke Method POST di baris 17 atas... BOOM! -->
        <button type="submit" name="update" class="btn">Timpa Dan Simpan Perubahan Ini</button>
        
        <!-- Jembatan lompat hyper-link biasa (bukan tombool submit). Untuk kabur menghindar dari ngedit... -->
        <a href="produk.php" class="btn btn-danger">Aduh Ga Jadi Deh (Balik)</a>
    </form>
</div>

<?php
// Ya biasa aja sihh.. Penutup doang buat tempelan bagian warna gelap bawah di CSS (Kaki Footer)...
include 'footer.php';
?>
