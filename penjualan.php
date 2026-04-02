<?php
require_once 'auth_check.php';
?>
<?php
// ======= FILE: penjualan.php (Layar Kasir Mesin Pinto Utama) =======

// Syarat hidup PHP agar ngga mandul/tak punya nyawa ke Database Zahwa Toys:
require_once 'backend/koneksi.php';

// Memastikan bahwa Session Keranjang itu tidak pernah mati (dihidupkan paksa/pasti via Header).
// Di dalam file header.php ini ada tulisan session_start();
include 'frontend/header.php';

// == MEMBUAT KERANJANG VIRTUAL DULU DI OTAK KOMPUTER (SESSION) ==
// IF: Coba dengerin mesin, BILA MANA (isset) belum ada sama sekali panci bernama 'cart' di Session...
if(!isset($_SESSION['cart'])) {
    // Ya bikininlah! Jadikan dia array kotak kosong `[ ]` buat naruh barang ntar.
    $_SESSION['cart'] = [];
}

// === LOGIKA TOMBOL '1. TAMBAH KE KERANJANG BELANJA' DITEKAN ===
if(isset($_POST['tambah_keranjang'])) {
    
    // Nangkep Identitas ID Mainan yang dipilih lewat Dropdown `<select name="produk_id">`
    $produk_id = $_POST['produk_id'];
    
    // Nangkep angka kuantitas yang dia ketik secara keras kepala tipe integer (INT).
    $jumlah = (int)$_POST['jumlah'];

    // Misi Cek Gudang: Woi SQL, Cari dan Pilih semua info dari rak `produk` YANG ID-NYA pas dengan `$produk_id`!
    $cek_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$produk_id'");
    
    // Bungkus info barang (misal Boneka Barbie) itu ke array pecahan $dt_produk
    $dt_produk = mysqli_fetch_assoc($cek_produk);

    // Kalo emang ada baris barangnya (True)
    if($dt_produk) {
        
        // Cek lagi: Apakah (IF) STOK Gudang saat ini (Lebih Besar /Sama Dengan >=) dari yang KASIR MINTA?
        if($dt_produk['stok'] >= $jumlah) {
            
            // JIKA IYA STOK AMAN: 
            // Coba lihat keranjangnya, Apakah tipe ID mainan ini SEBELUMNYA UDAH PERNAH DICETEK/Masuk Keranjang?
            if(isset($_SESSION['cart'][$produk_id])) {
                // Kalo udah ada di troli, YA DITAMBAH AJA JUMLAHNYA DONG `+=` !!! Gausah dibikin baris nota kembar 2 biji.
                $_SESSION['cart'][$produk_id]['jumlah'] += $jumlah;
            // KALAU INI MAINAN BARU PERTAMA KALI MASUK TROLI SEKARANG:
            } else {
                // Buatkan Kotak / Kamar Array di Session dengan ID Mainan itu. Isi datanya nama, harga, dan jumlah permintaannya!
                $_SESSION['cart'][$produk_id] = [
                    'nama_produk' => $dt_produk['nama_produk'],
                    'harga' => $dt_produk['harga'],
                    'jumlah' => $jumlah
                ];
            }
            // Selesai menroli! Munculin pesan Puji Syukur pakai PopUP HTML ijo (Success).
            echo "<div class='alert alert-success'>Produk berhasil ditambahkan ke keranjang belanja.</div>";
            
        // NAH, KALO STOK GUDANGNYA AJA GA CUKUP (MISAL STOK TINGGAL 2, KASIR INPUT 10)
        } else {
            // TERIAK PAKAI BALON MERAH (Danger) nolak perintah kasir. Kasih tau sisa stok real-nya berapa.
            echo "<div class='alert alert-danger'>Maaf, stok produk tidak mencukupi. Sisa stok: ".$dt_produk['stok']." unit.</div>";
        }
    }
}

// === LOGIKA TOMBOL KE2 : 'MENGHAPUS ITEM DARI DALAM KERANJANG/TROLI' KALO KASIR SALAH PENCET ===
if(isset($_GET['hapus_cart'])) {
    
    // Nangkep si Nomor ID mainan yang dicentang `HAPUS` dari Alamat URL browser (`?hapus_cart=9`)
    $id_hapus = $_GET['hapus_cart'];
    
    // Mantra Penghancur Array Session: UNSET.
    // Artnya: Panci SESSION khusus ruangan anak ID '9', LEBURKAN! Hapus semuanya.
    unset($_SESSION['cart'][$id_hapus]); 
    
    // Lempar Browser nge-refresh url nya biar bersih dari tanda `?hapus_cart=9` itu. Pake Javascript Jumper Loc.
    echo "<script>window.location='penjualan.php';</script>";
}

// === LOGIKA BARU: MENGHAPUS SEMUA ISI KERANJANG (KOSONGKAN) ===
if(isset($_GET['hapus_semua'])) {
    unset($_SESSION['cart']);
    echo "<script>window.location='penjualan.php';</script>";
}

// === LOGIKA BARU: UPDATE JUMLAH DI KERANJANG ===
if(isset($_POST['update_cart'])) {
    $id_update = $_POST['produk_id'];
    $qty_baru = (int)$_POST['jumlah'];

    // Cek Stok Gudang Dulu sebelum update
    $cek = mysqli_query($koneksi, "SELECT stok FROM produk WHERE id='$id_update'");
    $d = mysqli_fetch_assoc($cek);

    if($qty_baru > 0 && $d['stok'] >= $qty_baru) {
        $_SESSION['cart'][$id_update]['jumlah'] = $qty_baru;
        session_write_close();
        echo "<script>alert('Jumlah pesanan berhasil diperbarui!'); window.location='penjualan.php';</script>";
        exit;
    } else if($qty_baru <= 0) {
        unset($_SESSION['cart'][$id_update]); // Jika 0 atau minus, hapus aja
        session_write_close();
        echo "<script>window.location='penjualan.php';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal! Stok di gudang tidak cukup untuk jumlah tersebut.</div>";
    }
}

// === LOGIKA PENGHAKIMAN KE-3: BAYAR TRANSAKSI (CHECKOUT) KASIRRR ===
if(isset($_POST['checkout'])) {
    
    // Sedot KTP/ID Pelanggan dari kotak Select Pilihan kasir
    $pelanggan_id = $_POST['pelanggan_id'];
    
    // Siapkan Timbangan Uang (Variabel Kalkulator)
    $total_harga = 0;
    
    // Hitung Mundur Perulangan (Foreach: Untuk setiap anak) di panci SESSION keranjang:
    // Panggil $item dan peras jus datanya..
    foreach($_SESSION['cart'] as $item) {
        // Uang berjalan = Harga sepotong barang * Qty-nya. Plus sama uang sebelumnnya (`+=`). Terus sampe abis isi keranjangnya.
        $total_harga += ($item['harga'] * $item['jumlah']);
    }

    // A. BIKIN NOTA INDUK (HEADER TRANSAKSI) ke Tabel MySQL Penjualan Dulu (Tanggal Jam dll)
    // Aturan main: Jika ada pelanggan diketim /dipilih (Gak Kosong Null "" )..
    if($pelanggan_id != "") {
        // MANTRA INSERT: Bikin struk dengan mencatat Jumlah TOTAL BILL seluruhnya sama NAMA KTP Pelangganya.
        $insert_penjualan = mysqli_query($koneksi, "INSERT INTO penjualan (total_harga, pelanggan_id) VALUES ('$total_harga', '$pelanggan_id')");
    // Kalau Kasir Milih pelanggan ke pembeli ghaib / UMUM (Kosong)...
    } else {
        // MANTRA INSERT: Bikin struk, TAPI KTP Pelangganya di-NULL kan (Orang tak dijenali / lewat).
        $insert_penjualan = mysqli_query($koneksi, "INSERT INTO penjualan (total_harga, pelanggan_id) VALUES ('$total_harga', NULL)");
    }
    
    // JIKA Aksi Bikin Struk Header di Tahap A berhasil ter-Simpan... Lanjutt
    if($insert_penjualan) {
        
        // JURUS RAHASIA: Ambil ID STRUK Nomor Urut Paling TERBARU Yang MySQL Barusan Bikin Pakai Kode `mysqli_insert_id`.
        $penjualan_id = mysqli_insert_id($koneksi);
        
        // B. MASUKKAN ANAK BARANG (RINCIAN BELANJAAN) SATU PER SATU KE NOTA ITU. Perulangan diaktifkan ulang mencari keranjang!
        foreach($_SESSION['cart'] as $id_produk => $item) {
            
            // Catat isi quantity belanja anak ini
            $jml = $item['jumlah'];
            // Subtotal untuk anak barang malang ini
            $subtotal = $item['harga'] * $jml;
            
            // MANTRA INSERT ANAKAN : Kirim info Rincian Barang tsb ke tabel Kaki Tangan `detail_penjualan`. Tautkan dengan `$penjualan_id` Induk Notanya!.
            mysqli_query($koneksi, "INSERT INTO detail_penjualan (penjualan_id, produk_id, jumlah_produk, subtotal) VALUES ('$penjualan_id', '$id_produk', '$jml', '$subtotal')");
            
            // C. JURUS POTONG OTOMATIS GUDANG.
            // Woi Mysql, PERBARUI tabel produk, ATUR biar Kolom Stok = Angka STOK LAMA Dikurangi (-) Angka JML DIBELI Saat Ini. HANYA Pada (WHERE) Rak yg ID nya sesuai barang ini. Cerdass!
            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jml WHERE id='$id_produk'");
        }
        
        // D. Karena Orang/Pembelinya udah bayar lunas keluar pintu... BUANG SEMUA ISI KERANJANG VIRTUALNYA DARI KOTAK SAMPAH SESSION. Biar antrian pembeli kasir balakang nggak keikut list belanjaan dia awkwk.
        unset($_SESSION['cart']);
        
        // Munculkan PopUp Alert JS bahwa KASIR SUKSES. Lalu lempar halaman menyebrang jalan ke `detail_penjualan.php` (Catatan Histori).
        echo "<script>alert('Transaksi berhasil diselesaikan. Stok produk telah diperbarui.'); window.location='detail_penjualan.php';</script>";
        
    // Kalau Sistem Utama (Nota Induk Error)
    } else {
        // Munculin PopUp ERROR PHP
        echo "<div class='alert alert-danger'>Terjadi kesalahan sistem. Gagal memproses transaksi. Silakan hubungi teknisi.</div>";
    }
}
?>

<!-- === STUDIO UTAMA TAMPILAN GRAFIS WEB KASIR === -->

<!-- Judul Halaman Raksasa -->
<h2>Sistem Penjualan (POS)</h2>

<!-- Kartu 1: Area Tukang Scan Barcode Mainan -->
<div class="card" style="margin-bottom: 20px;">
    <h3>Langkah 1: Input Produk</h3>
    
    <!-- Bentuk form pengiriman POST ke alat pelahap PHP "Cart" baris atas -->
    <!-- Display Flex CSS: Bikin kotak2 isiannya dijejer rapat LURUs menyamping sejajar. Kek barcode kasir beneran -->
    <form method="POST" action="" style="display: flex; gap: 10px; align-items: flex-end;">
        
        <!-- Pilihan Dropdown Mainan -->
        <div class="form-group" style="flex: 1;"> <!-- flex 1 artinya serakah nyita tempat melar ke kanan -->
            <label>Daftar Produk</label>
            
            <!-- Kotak Tag Select HTML. Untuk ngebuka daftar gulung orang milih opsional Name nya 'produk_id'. WAJIB ISI (REQ) -->
            <select name="produk_id" class="form-control" required>
                <!-- Opsi Pajangan pertama doang -->
                <option value="">-- Cari atau Pilih Produk --</option>
                <?php
                // Tampilkan aja semua barang YG STOKNYA MASIH ADA DI ATAS NOL (> 0). Ngapain nampilin barang habis stock!  
                $q_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok > 0");
                
                // Looping ngecitak <OPTION> buat nempelin barang db dlm daftar gulung select 
                while($p = mysqli_fetch_assoc($q_produk)) {
                    // Cetak valuenya berupa (ID ASLI MYSQL) dan tulisannya ya harga rupah sama Cek Saldo Stock
                    echo "<option value='".$p['id']."'>".$p['nama_produk']." || (Rp ".number_format($p['harga'],0,',','.').") || Stok: ".$p['stok']." Unit</option>";
                }
                ?>
            <!-- Tutup Kotak Gulung -->
            </select>
        </div>
        
        <!-- Pilihan Ketikan Kuantitas (Jumlah yg diorder) -->
        <div class="form-group" style="width: 150px;">
            <label>Jumlah</label>
            <!-- Tipe data angka number mulsa dr bates Min 1 -->
            <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
        </div>
        
        <!-- Tombol Masuk Ke Troli Virtual Cart (Pemicu Pelatuk) -->
        <div class="form-group">
            <button type="submit" name="tambah_keranjang" class="btn">Tambahkan ke Keranjang</button>
        </div>
        
    </form>
</div>


<!-- Kartu 2: Layar Menampilkan Tabel Trolil Belanjaan Sementara Pelanggan. Ini BELUM ngaruh ke database sama sekali, murni mengapung di Session -->
<div class="card">
    
    <h3>Langkah 2: Keranjang Belanja</h3>
    
    <!-- Papan Catur Mejanya (Table html) -->
    <table class="table">
        <!-- Atap Kop Judul Koloman Meja -->
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Harga Satuan</th>
                <th width="120px">Jumlah</th>
                <th>Subtotal</th>
                <th width="100px">Aksi</th>
            </tr>
        </thead>
        
        <!-- Kantung Perut Meja Pembuka (Yang menampung daging isinya array) -->
        <tbody>
            <?php
            // Modal Mesin Hitung Kasir Kalkulator dr Angka NOL.
            $grand_total = 0;
            
            // Kalo KERANJANG VIRTUALNYA ITU GAK KOSONG (!empty).... Cuss Jalankan Mutar isinya!  
            if(!empty($_SESSION['cart'])) {
                
                // Membuka Tali Pengikat Array. Terjemahkan laci '$id' dan isi barang jadi '$item' satu per satu
                foreach($_SESSION['cart'] as $id => $item) {
                    
                    // Lakukan perkalian matematis Sub uangnya (HArga X Kuantitas Jml)
                    $sub = $item['harga'] * $item['jumlah'];
                    
                    // Sumbangkan nilai perkalian tadi ke laci Total Kalkulator Uang Utama `+=` Terus nambah teruus.
                    $grand_total += $sub;
            ?>
            <!-- Bongkar Baris Tabel HTML-nya secara live -->
            <tr>
                <!-- Lempar echo mencetak tulisan pancingan array SESSION: Nama, Harga Rupiah, Jumlah pcs... -->
                <td><?php echo $item['nama_produk']; ?></td>
                <td>Rp <?php echo number_format($item['harga'],0,',','.'); ?></td>
                <td>
                    <form method="POST" action="" style="display: flex; gap: 5px;">
                        <input type="hidden" name="produk_id" value="<?php echo $id; ?>">
                        <input type="number" name="jumlah" value="<?php echo $item['jumlah']; ?>" min="1" class="form-control" style="width: 60px; padding: 5px;">
                        <button type="submit" name="update_cart" class="btn btn-sm" style="background-color: #6367FF; color: white; padding: 5px 8px;" title="Simpan Perubahan">
                            Simpan
                        </button>
                    </form>
                </td>
                
                <!-- Format penulisan angka uang untuk di ujung tabel (Sub uang td) -->
                <td>Rp <?php echo number_format($sub,0,',','.'); ?></td>
                
                <!-- Tombol Berdarah untuk nge-Drop buang barang dr troli klu orgnya ngaku gajadi uang cekak (Bawa embel URL '?hapus_cart=' lalu gembokin pake nama si Array Laci $id nya dia) -->
                <td>
                    <a href="penjualan.php?hapus_cart=<?php echo $id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus item ini dari keranjang?')">Hapus</a>
                </td>
            </tr>
            <?php 
                // Kurung Kurawal TUTUP Loop Keranjang.
                }
            
            // Nah sebaliknya, KALO KERANJANG VIRTUAL KOSONG ALIAS MELOMPONG?
            } else {
                // Cetak HMTL tulisn nyinyir tengah pake Colspan megar 5 lobang menyatu td. Stylenya dikasih Abu (999) dan italic (miring)
                echo "<tr><td colspan='5' align='center' style='color: #999;font-style:italic;'>Keranjang belanja masih kosong. Silakan pilih produk untuk memulai transaksi.</td></tr>";
            }
            ?>
        </tbody>
        
        <!-- Area TFOOT: Pijakan Kaki Bawah Tabel -->
        <tfoot>
            <!-- Bagian ini nyatuin ruang (Colspan) sampe 3 kolom jadi 1 utk bikin space lapang -->
            <tr>
                <th colspan="3" style="text-align: right;">Total Pembayaran:</th>
                <!-- Tampilkan Total hasil mesin Kalkukator kita ++ tadi Pakai Rupiah Cantik, tebal Warna Header Merah -->
                <th colspan="2" style="color: #6367FF; font-size: 18px;">Rp <?php echo number_format($grand_total,0,',','.'); ?></th>
            </tr>
        </tfoot>
    <!-- Nutup papannya utuh -->
    </table>

    <?php if(!empty($_SESSION['cart'])) { ?>
    <div style="text-align: right; margin-top: 10px;">
        <a href="penjualan.php?hapus_semua=1" class="btn btn-sm" style="background-color: #dc3545; color: white;" onclick="return confirm('Kosongkan semua isi keranjang?')">Kosongkan Keranjang</a>
    </div>
    <?php } ?>


    <!-- === BAGIAN EKSEKUSOR PEMUAS KASIR CHECKOUT (PEMBAYARAN DITERIMA) === -->

    <!-- Syarat Tampilnya Fitur Di Bawah Ini: Hanya KETIKA dan JIKA Troli Gak Kosong!! NGAPAIN CHECKOUT ANGIN? -->
    <?php if(!empty($_SESSION['cart'])) { ?>
    
    <!-- Formulir Pukulan Final Method POST ke Logika 3 (Bayar/Checkout)  -->
    <!-- Sedikit kasih garis pemisah perapian baju css di atasnya ('border-top: 2px dashed #eee') -->
    <form method="POST" action="" style="margin-top: 20px; border-top: 2px dashed #eee; padding-top: 20px;">
        
        <!-- Kotak isian KTP Pelanggannya (Select Opsi gulung) -->
        <div class="form-group" style="max-width: 400px;">
            <label>Pilih Pelanggan (Opsional)</label>
            <select name="pelanggan_id" class="form-control">
                <!-- Opsi default nya ini Value Kosong "" Bagaikan Pembeli Casual Jalanan -->
                <option value="">-- Pelanggan Umum --</option>
                <?php
                // Tampilkan Data Semua Pelanggan di MySQL untuk dipilih kasir..
                $q_pel = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                while($pl = mysqli_fetch_assoc($q_pel)) {
                    echo "<option value='".$pl['id']."'>".$pl['nama_pelanggan']."</option>";
                }
                ?>
            </select>
        </div>
        
        <!-- TOMBOL BAYAR SEBESAR TRUK / SANGAT CRITICAL (Warna Ijo #28a745, lebae 300px gess) -->
        <!-- Dilengkapi bumbu proteksi JAVASCRIPT onclick Alert confirm biar gbs dibatalkan kalau kepencet. -->
        <button type="submit" name="checkout" class="btn" style="background-color: #28a745; width: 300px; font-size: 16px; padding: 15px;" onclick="return confirm('Apakah Anda yakin ingin memproses transaksi ini?')">Proses Pembayaran</button>
    </form>
    
    <?php } // Kurung Penutup dari aturan If-Tidak Kosong Tadi ?>
    
<!-- Pintu Tutup Kotak Kardus Card HTML Layar putih -->
</div>

<!-- Kartu 3: Transaksi Terakhir (Quick Access Edit/Hapus) -->
<div class="card" style="margin-top: 20px; border-top: 4px solid #6367FF;">
    <h3>Riwayat Transaksi Terbaru</h3>
    <table class="table">
        <thead>
            <tr>
                <th>No. Struk</th>
                <th>Waktu</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th width="80px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q_recent = mysqli_query($koneksi, "
                SELECT penjualan.*, pelanggan.nama_pelanggan 
                FROM penjualan 
                LEFT JOIN pelanggan ON penjualan.pelanggan_id = pelanggan.id 
                ORDER BY penjualan.id DESC LIMIT 5
            ");
            while($r = mysqli_fetch_assoc($q_recent)) {
            ?>
            <tr>
                <td><strong>INV-<?php echo str_pad($r['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                <td><?php echo date('d/m/y H:i', strtotime($r['tanggal_penjualan'])); ?></td>
                <td><?php echo $r['nama_pelanggan'] ? $r['nama_pelanggan'] : '<em style="color:#aaa;">Pelanggan Umum</em>'; ?></td>
                <td style="color: #6367FF; font-weight: bold;">Rp <?php echo number_format($r['total_harga'],0,',','.'); ?></td>
                <td>
                    <a href="faktur.php?id=<?php echo $r['id']; ?>" class="btn btn-sm" style="background-color: #17a2b8;">Detail</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div style="margin-top: 10px; text-align: right;">
        <a href="detail_penjualan.php" style="color: #6367FF; text-decoration: none; font-size: 14px;">Lihat Semua Riwayat &rarr;</a>
    </div>
</div>


<?php
// Yaah biasa lah.. manggil CSS dan kaki item web dari Footer.
include 'frontend/footer.php';
?>
