<?php
// ======= FILE BEDAH / RUANG OPERASI: pelanggan_edit.php =======

// Ngobrol sama File koneksi dulu (Akses Pintu Gerbang Terbuka)
require_once 'koneksi.php';

// Memangill elemen Header Top Design
include 'header.php';

// Nangkep Identitas Rahasia Dari Surat Undangan di URL `?id=123`.
// Artinya: PHP tolong catat `ID=123` pakai jaring jaring bernama `$_GET['id']` terus diikat kuat pada nama variabel `$id`.
$id = $_GET['id'];

// == MENGHIDANGKAN SAJIAN TABEL LAMA KE DALAM FORM ==
// "MySQL, TOLONG PANGGIL (Select) data pelanggan yang ciri-cirinya Punya Pasport id='123'."
$query = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id='$id'");
// Dipecah / Dicungkil arraynya agar bisa dipanggil pake tanda kurung kotak ['$nama_pelanggan'] oleh form html bawah di dalam value.
$data = mysqli_fetch_assoc($query);

// == BLOK MANTRA LOGIKA PENYIMPANAN / PENIMPAAN UPDATE ==
// Kalau aja (IF) si tombol bernama `name="update"` di form bawah ditekan pake mouse.
if(isset($_POST['update'])) {
    
    // PHP Mulai Tangkap bola. $_POST['nama'] disedot. Dicuci dari debu radiasi "Sql Injection" dengan fungsi aneh `mysqli_real_escape_string`. Dan dititipin ke keranjang var_nama.
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    
    // Nangkep isian teks Alamat yang panjang trus dicuci..
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    // Nangkap Telepon ... Cuci dan Simpan sbg string.
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    // Tembakan Salvo Pembaruan (UPDATE DATA)!
    // Robot SQL disuruh (UPDATE) untuk tabel pelanggan. ATUR (SET) isi kolom nama_pelanggan agar = `$nama` baru, lalu atur kolom yg lain dan lain juga... TAPI!!
    // DIMANA / KONDISI KHUSUS (WHERE) id Pelangganya HARUS sama dengan `$id` yang kita obok obok saat ini saaaaajjaa. Awass keliru seisi pabrik!
    $update = mysqli_query($koneksi, "UPDATE pelanggan SET nama_pelanggan='$nama', alamat='$alamat', nomor_telepon='$telepon' WHERE id='$id'");

    // Kalau berhasil meluncur tak kembali (TRUE)
    if($update) {
        // JS Alert nampilin ucapan selamat, langsung window.location ngangkut manusianya ke terminal pelanggan.php (Tabel Semula yg ngebosenin td)
        echo "<script>alert('SIPP ALHAMDULLILLAH!! Revisi profil pelanggan sudah termodifikasi!'); window.location='pelanggan.php';</script>";
        
    // Kalau database SQL nya nangis nge-glitch .. 
    } else {
        // Muncullah box menakutkan HTML merah memperingatkan..
        echo "<div class='alert alert-danger'>Yahh Gagal diubah kak datanya. Coba lagi yee...</div>";
    }
}
?>

<!-- === STUDIO UTAMA TAMPILAN FORM EDITING === -->

<!-- Tag Heading penjelas fungsi sub menu ini untuk si A -->
<h2>Revisi Riwayat Data Pelanggan </h2>

<!-- CSS Kotak Bersih Berbayang Putih (Card Box Shadowing Style) -->
<div class="card">
    
    <!-- Formulir ini dikirim sembunyi2 (POST) saat disubmit kepalanya -->
    <form method="POST" action="">
        
        <div class="form-group">
            <!-- Peringatan nama apa ini -->
            <label>Nama Anda (Pelanggan)</label>
            
            <!-- BEDANYA AMA TAMBAH APA? Nahh Disini lho seninya PHP campur aduk HTML. -->
            <!-- Atribut Value="...." Ini Fungsinya Menyusupkan Data Text Lawas MySQL ($data['nama...']) kedalam lobang Input, SEBELUM pengunjung ngetik apapun. 
            Jadinya mereka tinggak nyocok-nyocokin ejaan mana huruf vokalnya yang salah buat dihapus. -->
            <!-- Trik `htmlspecialchars` ini Biar aman juga kalo teks aslinya mengandung kutip aneh dari database, dan nggak ngeberantakin Tag HTML.  -->
            <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($data['nama_pelanggan'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Alamat Pengantaran / Domisili Mainan</label>
            <!-- Karena dia Textarea.. Gak punya Atribut 'Value'. Nenyusupkan teks nya itu di tengah-tengah / diapit tag penutupnya dan membukanya tuh lhoo  -->
            <textarea name="alamat" class="form-control" rows="3"><?php echo htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Nomor Urut Telepon yang Aktif</label>
            <!-- Numpang masukin memori ke-telepon sebagai teks isian asal mula -->
            <input type="text" name="telepon" class="form-control" value="<?php echo htmlspecialchars($data['nomor_telepon'] ?? ''); ?>">
        </div>
        
        <!-- Pintu Gerbang Eksekusi Truk POST ('update') ... Dorong!!! -->
        <button type="submit" name="update" class="btn">Timpa Teks Dan Simpan Secara Permanen Ya</button>
        
        <!-- Jalan Pintas Buat Mundur Ngabur dari Halaman ini -->
        <a href="pelanggan.php" class="btn btn-danger">Halahh, Males ngedit, Balik Ajalah (Batal)!</a>
        
    </form>
</div>

<?php
// PHP Pemasang Ujung Kaki Sepatu (Penutup Body web nya)!
include 'footer.php';
?>
