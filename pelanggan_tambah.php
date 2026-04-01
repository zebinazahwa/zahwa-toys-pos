<?php
// ======= FILE: pelanggan_tambah.php (Pos Pendaftaran Member Baru) =======

// Syarat hidup PHP agar ngga mandul/tak punya nyawa ke Database:
require_once 'koneksi.php';

// Masukkan topi dan jubah desain visual dari file header
include 'header.php';

// === LOGIKA SANG ARSITEK PENDATAAN MASUK ===
// if(isset($_POST...)) = Mendeteksi jika jari pengunjung nggak iseng numpang lewat, tapi benar-benar MENEKAN / "Trigger" Tombol "Simpan" di Form bawah yang name="simpan".  
if(isset($_POST['simpan'])) {
    
    // Menerima data lemparan rahasia jalur METHOD="POST".
    // Kenapa POST, bukan GET? Karena nama dan Alamat kepanjangan dan privasi banget buat dipampang di alamat bar www browser (GET).
    // `$koneksi` di depannya disuntikkan filter anti-nuklir karakter (mysqli_real_escape_string) supaya gak dihack via karakter tanda kutip/petik SQL aneh-aneh ...
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    
    // Cuci isian formulir teks area ALAMAT... jadikan teks suci dan aman. Trus ditampung di keranjang $alamat ...
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    // Cuci juga angka-angka norak 089XXX tadi ... simpen di keranjang $telepon
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    // MERACIK SQL MANTRA CREATE: Masukkan (INSERT) ke Dalam wadah(INTO) pelanggan, pada kotak-kotak tabel "nama_pelanggan,dll..", Dengan Nilai (VALUES) yang dibalut tanda kutip sql ('$nama', dll).
    // Berikan perintah sql seram panjang itu ke fungsi pembaca sql php kita, yakni mysqli_query(). Jalankan dengan jembatan $koneksi!
    $simpan = mysqli_query($koneksi, "INSERT INTO pelanggan (nama_pelanggan, alamat, nomor_telepon) VALUES ('$nama', '$alamat', '$telepon')");

    // Jika takdirnya berhasil disimpan di MySQL sana tanpa drama error duplicate/sejenisnga...
    if($simpan) {
        // Echo memunculkan trik dewa JAVASCRIPT kilat.
        // Javascript Alert nampilin PopUp dari atas browser layarnya. Habis user klik (OK) di popup itu, baris window.location mindahin/lempar kaget orang itu balik ke jalan pulang menu Data Pelanggan.
        echo "<script>alert('YES! Pelanggan langganan sukses dibuat dan terekod di database MySQL!'); window.location='pelanggan.php';</script>";
        
    // Ya tapi kalo MySQL nya yang ngambek misalnya entah karena apa...
    } else {
        // Kirim sinyal kotak berwarna merah (alert-danger nya css kita) ngomong "Gagal" lah apalagi. Palingan tabel salah ejaan di dbnya.
        echo "<div class='alert alert-danger'>Yahh, Gagal lho nyimpan data pelanggan baru. Hubungi Programmer Zebina cepaattt!</div>";
    }
}
?>

<!-- === KANVAS HTML PEMBUAT KOTAK ISIAN (FORMULIR/FORM) === -->

<!-- Sub Judul Halaman -->
<h2>Buat / Tambah Rekap Pelanggan Baru Toko</h2>

<!-- Ruang pembungkus estetik berlayar tebal belakang CSS -->
<div class="card">
    
    <!-- Formulir tempat orang mencurahkan teks / ketik. Metode Lempar Pengiriman Data nya Pake Truk POST. Action di file ini aaja ="" -->
    <form method="POST" action="">
        
        <!-- Grup Isian buat nge-Spasi CSS -->
        <div class="form-group">
            <label>Pengisian Nama Lengkap Pelanggan (Pria/Wanita)</label>
            <!-- Kotak KETIK biasa Tipe Teks. Nama pengait gawangnya Adalah `name="nama"`. Parameter ini ditangkap Truk POST PHP di atas. REQUIRED di ujung itu aturan keras bahwa Gaboleh kosong, harus diisi! Placeholder itu cuma abu-abu penuntun ngetik  -->
            <input type="text" name="nama" class="form-control" required placeholder="Contoh Pengisian: Bapak Andi Sutiyo">
        </div>
        
        <!-- Grup Spasi Isian -->
        <div class="form-group">
            <label>Jalan / Alamat Detail</label>
            <!-- Tag `textarea` ini inputan yang kotaknya GEDE/Tingggi banget dan bisa di enter dalemnya. Parameter ROWS=3 artinya standar awal minimal 3 Baris tingginya -->
            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap domisili pembeli (Opsional sihhh) ..."></textarea>
        </div>
        
        <!-- Grup Teks ketiga -->
        <div class="form-group">
            <label>Nomor Jaringan Telepon / WA (Awali 08...)</label>
            <!-- Kotak input biasa -->
            <input type="text" name="telepon" class="form-control" placeholder="Angka Contoh Pengisian: 08123xxxx">
        </div>
        
        <!-- NAH INI, PEMICU PELATUK API UNTUK MENGIRIM KESELURUHAN TEKS DALAM FORMULIR KE TRUK $_POST TADI -->
        <button type="submit" name="simpan" class="btn">Rekam ke Pencatatan Data</button>
        
        <!-- Kalo males lanjutin ngetik, di PHP mending buang pakai Hyperlink Link lari ke `pelanggan.php`. Jadi data ga sempet kesimpan -->
        <a href="pelanggan.php" class="btn btn-danger">Halahh, Males Ngetik, Mundur deh! (Batal)</a>
        
    <!-- Selesai Blok Kerajaan Formulirnya. Semua isi inputnya tertangani ... -->
    </form>
<!-- Habis sudah kardus estetis kita. -->
</div>

<?php
// Sama kaya yang lain. Sisipkan potongan File Kaki dan penutup container HTML-nya!
include 'footer.php';
?>
