<?php
require_once 'auth_check.php';
?>
<?php
// ======= FILE: faktur.php (Surat Struk Rincian Kertas Bukti Yang Akan Dicetak PDF/Kertas Toko) =======

// Memanggil Kabel Listrik Jaringan Ke Database zahwa_toys Xampp! Wajib..
require_once 'backend/koneksi.php';

// Menyelipkan Topi Navbar dan Kertas Dasar Web dr Header..
include 'frontend/header.php';

// === LANGKAH 1: NANGKAP SURAT PENGANTAR (KODE NOTA) ===
// Menarik/Mengambil ID peluru transaksi yang mau ditampilkan khusus ini ke layar.
// Datang Darimana ini? Dari klikan tombol `Lihat Rincian` di halaman riwayat tadi yang nyimpen link berakhiran URL: faktur.php?id=3
$id_transaksi = $_GET['id'];

// === LANGKAH 2: CARI DATA INDUK/KEPALA SURATNYA DI SERVER MENTAH MYSQL ===
// Kita butuh Tahu INI NOTA TANGGAL BERAPA, TOTALNYA BERAPA, YG BELI SIAPA. Makanya pakai tabel Penjualan Relasi JOIN.
// "LEFT JOIN pelanggan": Nempel Ke tabel Pelanggan ya bos untuk nyomot namanya doang!
// "WHERE penjualan.id = $id...": SYARAT MUTLAK. HANYA CARI bon/struk yang Nomor Seri INVOICE-nya Cocok ama yang $id diklik (Contoh 3) !! Awas bocor nota orang laen kwkwk.
$q_penjualan = mysqli_query($koneksi, "
    SELECT penjualan.*, pelanggan.nama_pelanggan 
    FROM penjualan 
    LEFT JOIN pelanggan ON penjualan.pelanggan_id = pelanggan.id 
    WHERE penjualan.id = '$id_transaksi'
");

// Bongkarin dan letakkan piring memori dr Laci array ke penampungan string associative bernama $penjualan. (Gak Pake While. Kenapa? Ya karena Induk Nota cuma 1 lho kan per Struk Resi!!).
$penjualan = mysqli_fetch_assoc($q_penjualan);
?>

<!-- === LAYOUT KANVAS KHUSUS PAPAN PENCETAKAN HTML BUKTI FAKTUR === -->

<!-- KEPALA STRUK KERTAS: Judul Kertas Struk Besar... Tambahin Nomor Struknya (Bantalyng 3 Spansi Nol di Kiri ID pakai str_pad PHP kyk kmren INV-003) -->
<h2>Faktur Pembayaran : INV-<?php echo str_pad($penjualan['id'], 3, '0', STR_PAD_LEFT); ?></h2>

<!-- Badan Kertas Invoice/Bagian Putih -->
<!-- Disematkan ID CSS id="area-cetak" . Biasanya Programmer kalo bikin JavaScript Advanced buat printer kasir thermal, mereka ngambil layar yang isinya cuman id ini doang utk diprint ngehindar nabrak Tombol di layarnya. -->
<div class="card" id="area-cetak">
    
    <!-- Data Paragraph: Tgl waktu beli. Dicetak dan Dikonversi Format Indonya Pake PHP Date Function -->
    <p><strong>Tanggal & Waktu Transaksi:</strong> <?php echo date('d M Y H:i', strtotime($penjualan['tanggal_penjualan'])); ?></p>
    
    <!-- Data Prragraph: Kepada Yth Pembeli. Menggunakan Operator (?) -> Kalo bukan Member MySQL (Alias nama orgnya KOSONG), Maka tulis Teks Asal "Pembeli Biasa Umum" -->
    <p><strong>Pelanggan:</strong> <?php echo $penjualan['nama_pelanggan'] ? $penjualan['nama_pelanggan'] : 'Pelanggan Umum'; ?></p>
    
    <!-- Sedikit Baju CSS Garis horizontal melintang pembatas atas tabel dan tulisan kepala surat atas... -->
    <hr style="margin: 15px 0;">

    <!-- TABEL UTAMA UNTUK NUNJUKIN ANAK-ANAK BARANG (MAINAN APA AJA YG DIBELI DALEM KARDUS BELANJAAN) -->
    <table class="table">
        <!-- Kop Kolom Atap Judul -->
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        
        <!-- Perut Laci Pembungkus Mainan Loop... -->
        <tbody>
            <?php
            // === LANGKAH 3: NGELUARIN ANAK RINCIAN (BODY NOTA DETAIL NYA) SECARA MASSAL ===
            
            // Tadi kan Kepalanya CUMA SATU. Nah kalo Badan Belanjaan bisa lebih daru satu bungkus per nota kan? Makanya dicari dari tabel `detail_penjualan` !
            // LAKUKAN TEKNIK "INNER JOIN" LAGIII. Buat apa? Biar ketauan ID Mainannya itu nama panjang barang aslinya apa sihh pas ngambil dr tabel `produk` sebelahnya...
            // Terus TENTUKAN SYARAT WHERE penanda kunci gembok detail anak ini TERTANAM pada ($id_tranaksi resinya)! Yaa.. 
            $qty_detail = mysqli_query($koneksi, "
                SELECT detail_penjualan.*, produk.nama_produk 
                FROM detail_penjualan 
                JOIN produk ON detail_penjualan.produk_id = produk.id 
                WHERE detail_penjualan.penjualan_id = '$id_transaksi'
            ");

            // PUTAR DAN PECAH ARRAY PERANAKNYA SAMPAI ABISS... Di Loop Pake WHiLE ya genks!
            while($dt = mysqli_fetch_assoc($qty_detail)) {
            ?>
            <!-- Buka Baris Tabel Buat Nampilin Si Mainan 1... Mainan 2 dsb... -->
            <tr>
                <!-- CETAK NAMANYA MAINAN HASIL JOIN TADI: -->
                <td><?php echo $dt['nama_produk']; ?></td>
                
                <!-- RUMUS MATEMATIKA CERDIK MENDAPATKAN HARGA AWAL ASLI SEKARAN SATUAN:  -->
                <!-- Di database kita GAK menyimpen riwayat harga satuan, CUMA simpen Total duit (subtotal) dibagi Jumlah QTY nya (Dapet dah Satunya Piro :v).. Dicetak jadi Rupoiyah Cantik numberFormating.. -->
                <td>Rp <?php echo number_format($dt['subtotal'] / $dt['jumlah_produk'], 0, ',', '.'); ?></td>
                
                <!-- Pamerkan angka Jumlah produk Unit pcs nya yg dimemori db nya  -->
                <td><?php echo $dt['jumlah_produk']; ?> Unit</td>
                
                <!-- Cetak Uang Murni subtotal hitungan SQL buat anak ini doang. -->
                <td>Rp <?php echo number_format($dt['subtotal'], 0, ',', '.'); ?></td>
            <!-- Selesai ngebaris HTML tr-->
            </tr>
            <?php }  // Selesai Loopingannya si Komputer Mutar Muter di While Baris Atas... ?>
        </tbody>
        
        <!-- Area Sepatu Tabel Paling Bawah Skuyd... -->
        <tfoot style="background:#fff3f3; color:#333;">
            <tr >
                <!-- Kolom disatukan biar Teks Geser Rata KAnan Polos 3 Papan Meja (colspan=3) -->
                <th colspan="3" style="text-align: right;">Total Pembayaran:</th>
                <!-- Tampilkan TOTAL KESELURUHAN BILL dr laci Penjualan Kepala yg paling awal tadi kita ambil diatas ($penjualan) -->
                <!-- Fontnya di merahin, digedein level dewa (18px pixelss) -->
                <th style="color:red; font-size:18px;">Rp <?php echo number_format($penjualan['total_harga'], 0, ',', '.'); ?></th>
            </tr>
        </tfoot>
    <!-- Hentikan Papan Catur Tbelnya... -->
    </table>
    
    <!-- Area Menu FUNGSI Tombol Tergantung Dibawah... -->
    <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-start;">
        
        <!-- Tombol Tulis CSS biasa buat melarikan diri kembali ke Riwayat Laporan Toko (detail_penjualan)  -->
        <a href="detail_penjualan.php" class="btn btn-warning">  Kembali </a>
        
        <!-- JURUS SDEWA KLIK CETAK PDF HTML/KERTAS KASIR MURNI --->
        <button onclick="window.print()" class="btn" style="background-color: #333;"><img src="https://img.icons8.com/ios-glyphs/30/ffffff/print.png" alt="print" style="width:16px; margin-bottom:-2px; margin-right:5px;"/> Cetak Struk</button>
        
    </div>
</div>

<?php
// PHP Biasaa, Nempelin Muka Footer item Bawah yg Ada taun tahun dinamisnya ituuh
include 'frontend/footer.php';
?>
