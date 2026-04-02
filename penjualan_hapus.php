<?php
require_once 'auth_check.php';
?>
<?php
// ======= FILE: penjualan_hapus.php (Logika Penghapusan & Pengembalian Stok) =======
require_once 'backend/koneksi.php';

// Pastikan ada kiriman ID melalui URL (?id=...)
if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 1. Ambil semua rincian barang dalam nota ini untuk dikembalikan stoknya ke gudang
    $query_detail = mysqli_query($koneksi, "SELECT * FROM detail_penjualan WHERE penjualan_id='$id'");
    
    if(mysqli_num_rows($query_detail) > 0) {
        while($row = mysqli_fetch_assoc($query_detail)) {
            $produk_id = $row['produk_id'];
            $qty = $row['jumlah_produk'];
            
            // 2. BALIKKAN STOK: Tambahkan kembali jumlah yang pernah dibeli ke stok produk saat ini
            mysqli_query($koneksi, "UPDATE produk SET stok = stok + $qty WHERE id='$produk_id'");
        }
    }

    // 3. Hapus data anak (detail_penjualan) terlebih dahulu demi integritas database
    $hapus_detail = mysqli_query($koneksi, "DELETE FROM detail_penjualan WHERE penjualan_id='$id'");

    // 4. Hapus data induk (penjualan)
    $hapus_induk = mysqli_query($koneksi, "DELETE FROM penjualan WHERE id='$id'");

    if($hapus_induk) {
        // Jika sukses, lempar balik ke histori dengan notifikasi sukses
        echo "<script>alert('Nota transaksi berhasil dihapus dan stok produk telah diperbarui.'); window.location='detail_penjualan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus nota transaksi.'); window.location='detail_penjualan.php';</script>";
    }
} else {
    // Jika tidak ada ID, ya balik aja
    header("Location: detail_penjualan.php");
}
?>
