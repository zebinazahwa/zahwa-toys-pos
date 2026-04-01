<?php
// ======= FILE: produk.php (Daftar Mainan) =======

// Memanggil gerbang koneksi ke database Zahwa Toys
require_once 'koneksi.php';

// Menempelkan desain header dan menu dari file potongan header.php ke dalam layar paling atas
include 'header.php';

// == BLOK LOGIKA (OTAK PHP) UNTUK MENGHAPUS DATA KALAU TOMBOL HAPUS SITEKAN ==

// Logika percabangan IF: Mengecek "Apakah ada pertanyaan bersandi 'hapus' yang terlempar ke alamat browser (URL)?"
// Contohnya kalau di URL ketengah tulisan: produk.php?hapus=15
if(isset($_GET['hapus'])) {
    
    // Jika benar ditemukan kode hapusnya, SIMPAN angka id tersebut ke dalam variabel memori sementara kita: '$id'
    $id = $_GET['hapus'];
    
    // Masukkan mantra/perintah hapus database via mysqli_query()
    // Perintah 'DELETE FROM produk WHERE id=$id' itu bahasa robot yang artinya: BONGKAR tabel produk cari yang nomor seri ID-nya (15 tadi) dan Lenyapkan.
    $hapus = mysqli_query($koneksi, "DELETE FROM produk WHERE id='$id'");
    
    // Cek sukses tidaknya: Jika proses di laci berhasil dihapus (True)...
    if($hapus) {
        // Tampilkan peringatan sukses yang berwarna hijau HTML (Karena pakai class 'alert-success' CSS yang kita punya)
        echo "<div class='alert alert-success'>Data produk berhasil dihapus secara bersih (Clean)!</div>";
    // TAPI kalau ditolak database... (Biasanya ditolak kalau barang tsb rupanya masih ada di riwayat bon bekas penjualan)
    } else {
        // Tampilkan kotak peringatan merah (alert-danger)...
        echo "<div class='alert alert-danger'>Gagal Dihapus! Data mainan ini mungkin masih terikat aman di dokumen riwayat penjualan bulan lalu.</div>";
    }
}
// Selesai Blok Logika PHP-nya. Sisanya adalah urusan wajah HTML.
?>

<!-- Tag Heading 2 mencetak tulisan tebal Judul -->
<h2>
    Data Produk
    <!-- Tag a (Hyperlink), yaitu tombol panah navigasi yang disulap jadi bentuk tombol (Class="btn") -->
    <!-- Jika diklik, pengguna akan dilempar menyebrang menuju file pengisi form baru ('produk_tambah.php') -->
    <!-- float:right digunakan untuk nendang tombol ini geser mentok ke dinding sisi Kanan layarnya -->
    <a href="produk_tambah.php" class="btn" style="float: right;">+ Tambah Produk</a>
</h2>

<!-- Tag Table: Ini adalah pembentuk matriks baris kolom layaknya Ms. Excel -->
<table class="table">
    <!-- THEAD (Table Head / Kepala Tabel), isinya adalah nama-nama Kolom/Tema-nya (Teks biasa yang tebal) -->
    <thead>
        <!-- TR (Table Row) = Membuka sebuah baris lurus mendatar -->
        <tr>
            <!-- TH (Table Headering/Kolom) = Kotakan judul pertama, yaitu Nomor -->
            <th>No</th>
            <!-- TH = Kotak judul kedua, yaitu Nama -->
            <th>Nama Produk</th>
            <!-- TH = Kotak ketiga: Harga rupiah -->
            <th>Harga (Rp)</th>
            <!-- TH = Kotakan Stock -->
            <th>Stok</th>
            <!-- TH = Kotakan buat ngeletakin tombol-tombol yang memakan lebar khusus 150 pixel mutlak -->
            <th width="150px">Aksi</th>
        </tr>
    </thead>
    
    <!-- TBODY (Table Body / Isi), ini kandangnya data-data dinamis yang akan kita sumpel di dalamnya -->
    <tbody>
        <?php
        // BLOK PHP KECIL UNTUK MENARIK SEMUA DATA DARI "GUDANG"
        
        // Bikin alat bantu variabel '$no'. Tugasnya nginget-nginget aja untuk urutan 1,2,3 di tabel biar cantik, mulai angka 1 dulu ya..
        $no = 1;
        
        // Panggil kurir kita minta datanya dari server database
        // "SELECT * FROM produk" artinya Minta Semuanya (!) dari laci produk.
        // "ORDER BY id DESC" artinya Panggilnya dibalik dong! (Yang nomor ID paling baru dibuat kemaren sore, harus pamer maju ditampilin paling Atas!).
        $query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC");
        
        // Trik Perulangan (While-Loop). Jadi komputer akan muter terus nyusun tabel sebanyak stok mainan yang ditemuinya!
        // Alat '$row' bertugas mencungkil bungkus kode mesinnya menjadi pecahan laci-laci array (assoc) tiap 1 putaran.
        while($row = mysqli_fetch_assoc($query)) {
        // PERHATIAN: Kurawal Buka "{ " di baris ini TIDAK kita tutup dulu di bawahnya, melainkan ditutup setelah baris HTML tabel. Kenapa? Tujuannya supaya tag <tr> baris HTML itu diulang-ulang.
        ?>
        <!-- Membuka baris (TR - Table Row) isi tabel -->
        <tr>
            <!-- TD (Table Data): Mengisi kotak ke-1  -->
            <!-- Ini dipanggil Echo mencetak angka $no yang sekarang, lalu plus-plus(++) artinya "habis dicetak, angkamu tak titipin +1 buat puteran selanjutnya yak". -->
            <td><?php echo $no++; ?></td>
            
            <!-- TD: Memanggil laci array hasil MySQL bernama [nama_produk] (Ini WAJIB sama ejaannya dengan tulisan kolom MYSQL phpmyadmin kita) -->
            <td><?php echo $row['nama_produk']; ?></td>
            
            <!-- TD: Menampilkan harga. Sebelum dicetak ke layar, dimasukkan mesin cuci 'number_format' dulu biar angka polosan 4000000 jadi cantik 4.000.000 -->
            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
            
            <!-- TD: Menampilkan isi dari laci [stok] -->
            <td><?php echo $row['stok']; ?></td>
            
            <!-- TD: Nah bagian ini yang unik, yaitu kotak khusus tempat tombol-tombol operasional -->
            <td>
                <!-- Membikin tombol "Edit" dengan Hyperlink. Dia ini lompat ke laci 'produk_edit.php' TAPI diselundupkan kunci gembok bertuliskan ?id= (contoh ?id=5). Gunanya biar form tujuannya langsung kenal sapa yang ngetok pintunya -->
                <a href="produk_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                
                <!-- Membikin tombol "Hapus". Sama kaya pasrahin gembok rahasia ?hapus=10 dari URL. -->
                <!-- Menambahkan bumbu onclick bawaan browser (Javascript Confirm Box) supaya muncul POP UP "U Are Sure" agar user gak ketidaksengajaan terpecet delete sembarangan -->
                <a href="produk.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Yakin menghapus produk ini?')" class="btn btn-sm btn-danger">Hapus</a>
            </td>
        <!-- Menutup baris mendatar -->
        </tr>
        <?php
        // Nah ini dia, Kurawal Penutup " } " putaran While-nya baru ditutup di baris PHP baru ini!
        // Berarti perulangan pengisian data otomatis terhenti sampai barang/produknya kehabisan laci.
        } 
        ?>
    <!-- Menutup wadah keranjang isi datanya -->
    </tbody>
<!-- Mengucapkan selamat tinggal selesai menyusun meja Tabelnya secara sempurna dengan ditutup tag HTML /TABLE -->
</table>

<?php
// Tak lupa kita panggil kode file tersembunyi berisikan penutup desain (kaki-kaki hitam bawah) bernama footer.php
include 'footer.php';
?>
