<?php
require_once 'auth_check.php';
?>
<?php
// ======= FILE: pelanggan.php (Halaman Tampil Daftar Member Toko) =======

// Membutuhkan mantra persambungan mesin Database MySQL di XAMPP milik kita.
// Tanpa file 'koneksi.php' disertakan, komputer nggak tau harus ngetok pintu laci pelanggan mana.
require_once 'backend/koneksi.php';

// Menarik paksa gabungan desain baju Layout (CSS), Judul Web, dan Deret Tombol Menu Atas (Navigasi Header)
include 'frontend/header.php';

// === LOGIKA HAPUS DATA MEMBER ===
// Ngecek apakah ada satpam PHP yang ngelapor kalo tombol Hapus berwarna merah di layar bawah abis dipencet?
// Kalau dipencet, link-nya itu ngirim angka sandi kayak gini di pucuk Chrome: "?hapus=2". (Metode nangkepnya GET).
if(isset($_GET['hapus'])) {
    
    // Oh ternyata ada! Ayo kita tangkap angka '2' itu dan taruh ke kaleng sementara milik bernama $id.
    $id = $_GET['hapus'];
    
    // Perintahkan SQL: Woi MySQL, "DELETE FROM pelanggan WHERE id='2'".
    // Maksudnya: Bukain tabel pelanggan dong, TRUS Bantai/Hilangkan baris yang Punya ID / Kunci sama persis kek angka 2!
    $hapus = mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id='$id'");
    
    // Hasil bantaiannya dievaluasi. Kalo sukses dihapus ke alam baka (True)...
    if($hapus) {
        // Tampilkan HTML teks warna daun pandan ijo muda terang dari gaya "alert-success"-nya Bootstrap buatan kita.
        echo "<div class='alert alert-success'>Data pelanggan berhasil dihapus.</div>";
    }
}
// Nah! PHP Logika Berakhir untuk urusan hapus-hapusan.
?>

<!-- === MULAI TAMPILAN GRAFIS MATA MANUSIA === -->

<!-- Tag h2 untuk Tulisan Sub Judul agak lebay gedenya (Tebel dan item) -->
<h2>
    Daftar Pelanggan (Member)
    <!-- Tag `a` adalah tag Jembatan Penghubung (Link) biasa.
         Disamarkan dengan make up baju clasic button ("btn").
         `float: right;` itu jurus ngambang pindah posisi ke tembok kanan. Biar tulisan ini ama tombol kepisah jauh posisinnya. -->
    <a href="pelanggan_tambah.php" class="btn" style="float: right;">Tambah Pelanggan</a>
</h2>

<!-- Tag penyusun tabel, layaknya Excel di Office. Dibuka di sini. -->
<table class="table">
    
    <!-- Bagian Atap / Kop Tabel untuk barisan judul tabel (THEAD). Warnanya lebih pudar abu-abu dari baris di bawahnya -->
    <thead>
        <!-- Barisan Lurus kesamping membungkus 5 Kotakan Atap -->
        <tr>
            <!-- Kotak-kotak kecil alias Kepala Kolom TH (Table Header)  -->
            <th>No.</th>
            <th>Nama Pelanggan</th>
            <th>Alamat</th>
            <th>Nomor Telepon</th>
            <!-- Lebarnya ini dipaksa 150 Pixel gaboleh megar, karena isinya cuma dua tombol dempet (Aksi) -->
            <th width="150px">Tindakan</th>
        </tr>
    </thead>
    
    <!-- Bagian Lemak/Isi Utama Perut Tabel (Table Body). Warnanya putih bersih -->
    <tbody>
        <?php
        // === BLOK PHP KECIL PEMBUAT BARIS MENGULAR (LOOPING MYSQL) ===
        
        // Modal awal ngitung tabel pake $no (Dimulai dari angka favorit kita: 1)
        $no = 1; 
        
        // Panggil Si Pesuruh: Minta ('SELECT') Seluruh Ciri-ciri Orang ('*') yang nyewa laci pelanggan..
        // Syarat? Gada (Gapake WHERE).
        // Ngurutinnya: ORDER BY id DESC (Urutkan dari Data Anak Baru yg Terakhir dibuat / Paling fresh nangkring di urutan Atas Dewe).
        $query = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY id DESC");
        
        // Mulai Mutar Otak: Komputer diperintah ngulang kegiatan ngetik HTML TR TD secara berkali-kali... Sesuai banyaknya member.
        // Caranya dengan mengucap WHILE dan FETCH_ASSOC (merubah kode abstrak mesin jadi laci Array mudah dieja).
        // Setiap mutar (Loop), datanya diserahkan ke kurir pengantar (Variabel $row).
        while($row = mysqli_fetch_assoc($query)) {
        // Kurung Pembuka { Sengaja disisakan terbuka. Awas jangan dihapus! Tar meledak error wkwk. Biar baris di bawah ini kelempar & diulang-ulang.
        ?>
        <!-- TR (Table Row): Buka Tali pembungkus datar untuk orang pertama (dan seterusnya) -->
        <tr>
            <!-- Echo angka urutnya, trus tambah satu poin ke angkanya (+1) di akhir perputarannya. -->
            <td><?php echo $no++; ?></td>
            
            <!-- Menggunakan PHP Echo, mencetak nilai dari dalam wadah Array $row. Yang diminta? Ya nama orang-nya. -->
            <!-- Tulisannya harus sama kaya kolom MySQL di XAMPP database kita ('nama_pelanggan'). Kalau 'nama_aja' tar Null (gak kebaca apa-apa). -->
            <td><?php echo $row['nama_pelanggan']; ?></td>
            
            <!-- Cetak jalan/RT/RW domisilinya pembeli mainan kita dari DB -->
            <td><?php echo $row['alamat']; ?></td>
            
            <!-- Cetak isi piringan yg namanya nomor_telepon. -->
            <td><?php echo $row['nomor_telepon']; ?></td>
            
            <!-- Nah, ini dia. Baris koloman terakhir nampung dua tombol bahaya! -->
            <td>
                <!-- TOMBOL MERUBAH: Pake teknik `Bawa Gembok` ke URL form sebelahnya (?id=...). Maksud gembok itu? Biar formnya paham siapa yg dituju buat diobrak-abrik! -->
                <a href="pelanggan_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Ubah</a>
                
                <!-- TOMBOL MENGHAPUS: Sama kaya Bawa Sandi di URL (?hapus=...).
                     CUMAN! Karena bahaya. Dikasih bumbu satpam Javascript dulu: `onclick="return confirm('...')"`.
                     Jadinya, entar ditahan dulu. Mau dihapus nggak nih Beneran? Kalau Ok, ya PHP baris atas tadi nangkap dan eksekusi SQL Hapus secara brutal. -->
                <a href="pelanggan.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data pelanggan ini?')" class="btn btn-sm btn-danger">Hapus</a>
            </td>
        <!-- Cekrek! Potong baris orang per-orang selesai. -->
        </tr>
        <?php 
        // Ehh kurung awalnya belum ditutup rupanya. Nah tutup sekarang " } " !  
        // Baru dah komputernya lega karena urusan ngulang-ngulangnya selesai abis kalau orangnya udah absen semua.
        } 
        ?>
    <!-- Selesai badan dan lemak isi perut tabel. -->
    </tbody>
<!-- Bungkus keseluruhan elemen meja raksana (table) kita ... Tutup! -->
</table>

<?php
// PHP memanggil stempel hak cipta (footer/footer) warna perak di bawah halaman dan nutup DIV bodong agar layout web tak ambyar ke mana-mana...
include 'frontend/footer.php';
?>
