<?php
// ======= FILE: index.php (DASHBOARD) =======
// File ini yang paling pertama dibaca mesin.

// Memanggil file koneksi secara wajib (require), jadi file ini langsung punya akses Database
require_once 'koneksi.php';

// Menempelkan tampilan (HTML Atas) yang ada di header.php ke baris ini 
include 'header.php';

// == BLOK 1: NGAMBIL HASIL TOTAL UNTUK DIPAJANG ==

// Menjalankan perintah (Query) menghitung kolom (COUNT) di seluruh tabel pelanggan
$query_pelanggan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pelanggan");
// Memecah hasil hitungan tabelnya jadi array
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
// Masukkan angkanya ke penampung `$total_pelanggan`
$total_pelanggan = $data_pelanggan['total'] ?? 0;

// Menjalankan perintah hitung jumlah baris data dalam tabel produk
$query_produk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
// Memecah array row kembalian MySQL
$data_produk = mysqli_fetch_assoc($query_produk);
// Disimpan ke keranjang $total_produk
$total_produk = $data_produk['total'] ?? 0;

// Menghitung jumlah TRANSAKSI berdasarkan hari yang sama hari ini (menggunakan MySQL CURDATE())
$query_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penjualan WHERE DATE(tanggal_penjualan) = CURDATE()");
// Cek IF keamanan: Kalau tabel dtemukan...
if($query_transaksi) {
    // Ambil angkanya
    $data_transaksi = mysqli_fetch_assoc($query_transaksi);
    // Masukkan ke variabel jumlah TRX
    $total_transaksi = $data_transaksi['total'] ?? 0;
// Kondisi ELSE jika tabel gagal dibaca/gagal terkoneksi 
} else {
    // Kasih angka nol biar gak error
    $total_transaksi = 0;
}

// Menjumlahkan MATA UANG RUPIAH dengan fungsi MySQL (SUM) di tabel penjualan khusus hari ini
$query_pendapatan = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM penjualan WHERE DATE(tanggal_penjualan) = CURDATE()");
// Jika sukses perintahnya
if($query_pendapatan) {
    // Pecah data uangnya ke memori server
    $data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
    // Variabel total pendapatan = hasil dari dalam database
    $total_pendapatan = $data_pendapatan['total'] ?? 0;
// Jika gagal mencari
} else {
    // Anggap tidak ada laci uang masuk
    $total_pendapatan = 0;
}
?>

<!-- Menampilkan Teks Judul memakai Tag Heading 2 (H2) -->
<h2>Dashboard</h2>

<!-- Paragraph P untuk menulis selamat datang -->
<p>Selamat datang di sistem manajemen kasir <strong>Zahwa Toys</strong>. Silakan pilih menu di atas untuk mulai mengelola penjualan atau data kita.</p>

<!-- Membuka div/ruang blok dengan kelas khusus desain dashboard kotak-kotak -->
<div class="dashboard-cards">
    
    <!-- Bagian Kartu / Kotak Informasi 1: Tentang jumlah item gudang -->
    <div class="card">
        <!-- Judul kecil bagian -->
        <h3>Total Produk Tersedia</h3>
        <!-- Memanggil langsung tag PHP echo (cetak) dengan number_format (misal 1000 jadi 1.000 dengan titik koma standar Rp) -->
        <div class="score"><?php echo number_format($total_produk, 0, ',', '.'); ?> Item</div>
    </div>
    
    <!-- Bagian Kartu / Kotak Informasi 2: Untuk jumlah member/pelanggan setia -->
    <div class="card">
        <h3>Total Pelanggan</h3>
        <!-- Echo mengeluarkan nilai variabel $total_pelanggan hasil MySQL COUNT tadi -->
        <div class="score"><?php echo number_format($total_pelanggan, 0, ',', '.'); ?> Orang</div>
    </div>
    
    <!-- Bagian Kartu / Kotak Informasi 3: Total aktivitas transaksi bon di tanggal hari ini saja -->
    <div class="card">
        <h3>Transaksi Hari Ini</h3>
        <!-- Echo menampilkan total trx -->
        <div class="score"><?php echo number_format($total_transaksi, 0, ',', '.'); ?> TRX</div>
    </div>
    
    <!-- Bagian Kartu / Kotak Informasi 4: Total pemasukan / omset laci hari ini -->
    <div class="card">
        <h3>Pendapatan Hari Ini</h3>
        <!-- Tempelkan teks statis 'Rp ' lalu gabungkan angka dari tabel SUM() tadi -->
        <div class="score">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></div>
    </div>
<!-- Menutup wadah dashboard-cards utamanya -->
</div>

<?php
// Menyertakan kaki website (footer), di mana CSS container utamanya juga akan otomatis ditutup di file sana
include 'footer.php';
?>
