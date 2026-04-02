<?php
require_once 'auth_check.php';
?>
<?php
// ======= FILE: detail_penjualan.php (Riwayat Nota Histori Transaksi Keseluruhan Toko) =======

// Mewajibkan jembata masuk jaringan Server MySQL zahwa_toys
require_once 'backend/koneksi.php';

// Memeluk dan Menyertakan Desain Muka Casing Web Kita (Bawah Kop Nav)
include 'frontend/header.php';
?>

<!-- Tag Judul Teks Heading Agak besar level 2 Halaman Ini -->
<h2>Riwayat Transaksi Penjualan</h2>

<!-- Pakai Template Kelas Bungkus "Card" ala material UI biar bersih menonjol Putih -->
<div class="card">
    
    <!-- Papan Catur tabel Garis CSS disiapkan untuk disumpal data log mysql -->
    <table class="table">
        
        <!-- Area THEAD (Tembok Tebal Atap Lurus Kolom Judul Tabel) -->
        <thead>
            <tr>
                <th>No. Struk (Invoice)</th>
                <th>Tanggal & Waktu</th>
                <th>Nama Pelanggan</th>
                <th>Total Pembayaran</th>
                <th width="100px">Tindakan</th>
            </tr>
        </thead>
        
        <!-- Area Perut Karet Pembungkus Anak Baris (TBODY) -->
        <tbody>
            <?php
            // ==== SENI MENYATUKAN DUA BENUA (TABEL MYSQL YANG BERBEDA) ====
            /* 
              TUGAS PENTING JURUS MAUT (Pengambilan Relasional SQL):
              Kita itu kan lagi make tabel PENJUALAN. NAh, Tapi di tabel PENJUALAN itu CUMAN CATET "pelanggan_id" yg wujudnya ANGKA doang (misal ID 1, 2, 5).
              Loh emang pembeli ZahwaToys hapal ID? Enggak kan... Makanya kita pakai teknik **"LEFT JOIN"**. 
              
              Tujuannya: Tolong SQL, Jodohkan/Gabungin Tabel Penjualan (Sebagai Bos Kiri / LEFT) DENGAN Tabel Pelanggan.
              Trus ambil kolom 'nama_pelanggan'-nya dr sono biar bisa dibaca, DAN Tempelin ke baris Penjualan yang pas dengan nilai ON id/kuncinya sama!.
            */
            
            // Siapkan Kuas Query-nya dan Lempar Tembakan Perintahnya!:
            // SELECT Penjualan.* (AMBIL SELURUH BADAN TABEL PENJUALAN) COMMA, pelanggan.nama_pelanggan (SEDCROT AMBIL NAMANYA AJA DARI SEBELAH).
            // ORDER BY id DESC (Urutkan dari Struk Invoice PALING AKHIR/TERBARU DIBIKIN yang muncul paling atas muka tabel layar html!).
            $query = mysqli_query($koneksi, "
                SELECT penjualan.*, pelanggan.nama_pelanggan 
                FROM penjualan 
                LEFT JOIN pelanggan ON penjualan.pelanggan_id = pelanggan.id 
                ORDER BY penjualan.id DESC
            ");
            
            // JURUS MUTAR LOOP Tabel HMTL berulang Ulang SAMPAI Semua Kertas bon MySQL Di Layar Abis (Mecah assoc jadi array dr string $row)
            while($row = mysqli_fetch_assoc($query)) {
            ?>
            <!-- Potongan Tabel Baris Kesamping Punya 1 Resi Orang Beli -->
            <tr>
                
                <!-- Bikin Nomor Struk Yang Cantik!! (INV-001) -->
                <!-- Pakai PHP function bawaannya bernama `str_pad` (Gunanya utk Menambahkan spasi 0 sbg bantalan). Di setting jadi 3 karakter. Pad Kiri (LEFT). -->
                <!-- Jadi kalau ID aslinya 5, dia bakal dicetak: INV-005 -->
                <td><strong>INV-<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                
                <!-- Tanggal Dan Jam: Ubah format jelek bawaan Mesin MySQL (Y-M-D) Dikonversi/Diformat jadi enak dibaca orang Manusia Indonesia (d M Y H:i misal 10 Des 2026 14:00) lewat mantra date() digabung strtotime() memecah tulisan.  -->
                <td><?php echo date('d M Y H:i', strtotime($row['tanggal_penjualan'])); ?></td>
                
                <!-- OPERATOR TERNARY SYARAT MAGIC (?) UNTUK NAMBAH EFEK : -->
                <!-- Artinya: APAKAH ada (True) array nilai nama_pelanggan di variabel row ini? 
                     Jika Iya -> Maka Echo-kan cetak namanya itu ('$row['nama_pelanggan']').
                     Sebaliknya Jika Tidak (Titik Dua :) alias dia cuma Pembeli ghaib -> Maka TULIS TEKS statis HTML/CSS 'Pelanggan Umum' berwarna tipis abu-abu ! -->
                <td><?php echo $row['nama_pelanggan'] ? $row['nama_pelanggan'] : '<em style="color:#aaa;">Pelanggan Umum</em>'; ?></td>
                
                <!-- Tampilkan Total Harga Nominal Belanja. Diolesan Lipstik NumberFormat sama dilipetin Warna Merah Tegas Tebal Baju Style nya.  -->
                <td style="color: #6367FF; font-weight: bold;">Rp <?php echo number_format($row['total_harga'],0,',','.'); ?></td>
                
                <!-- Kotakan Aksi Buat ngedetailin rincian anak laci yang spesifik dia doang... -->
                <td>
                    <!-- Tombol Detail (Faktur) -->
                    <a href="faktur.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" style="background-color: #17a2b8;">Detail</a>
                </td>
                
            <!-- Banting Pintu Baris Tabel Ini (Close TR), Ulangin dari pembuka loop sampe orang/nota DB nya abis !! -->
            </tr>
            <?php } // YAh.. ini Kurawal Kurung nutup batas akhir lopping phpnya ..! ?>
            
        <!-- Nutup Perut Karet Tabel HTML -->
        </tbody>
        
    <!-- Nutup Meja Utamanya -->
    </table>
<!-- Keluar Pintu Kardusnya Css -->
</div>

<?php
// Sama Lah.. Gabungin Bagian Elemen Kaki Hitam dan Tutup Wadah Jendela Container layot dr file footer.php disana
include 'frontend/footer.php';
?>
