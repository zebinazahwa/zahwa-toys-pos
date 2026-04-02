<?php
require_once 'auth_check.php';
?>
<?php
// ======= FILE: penjualan_edit.php (Layar Edit Nota Lama) =======
require_once 'backend/koneksi.php';
include 'frontend/header.php';

// Pastikan ada ID nota yang diedit
if(!isset($_GET['id'])) {
    header("Location: detail_penjualan.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// --- LOGIKA 1: UPDATE HEADER (PELANGGAN) ---
if(isset($_POST['update_header'])) {
    $pel_id = $_POST['pelanggan_id'] != "" ? "'".$_POST['pelanggan_id']."'" : "NULL";
    mysqli_query($koneksi, "UPDATE penjualan SET pelanggan_id = $pel_id WHERE id='$id'");
    echo "<div class='alert alert-success'>Data pelanggan berhasil diperbarui.</div>";
}

// --- LOGIKA 2: UPDATE QUANTITY BARANG (DENGAN PENYESUAIAN STOK) ---
if(isset($_POST['update_item'])) {
    $detail_id = $_POST['detail_id'];
    $qty_baru = (int)$_POST['jumlah'];

    // 1. Ambil data lama & Stok saat ini
    $q_old = mysqli_query($koneksi, "SELECT detail_penjualan.*, produk.stok 
                                     FROM detail_penjualan 
                                     JOIN produk ON detail_penjualan.produk_id = produk.id 
                                     WHERE detail_penjualan.id='$detail_id'");
    $d_old = mysqli_fetch_assoc($q_old);
    $qty_lama = $d_old['jumlah_produk'];
    $produk_id = $d_old['produk_id'];
    $stok_sekarang = $d_old['stok'];

    // 2. Hitung selisih untuk stok
    $selisih = $qty_baru - $qty_lama;

    // 3. CEK STOK: Jika nambah beli (selisih positif), pastikan stok di gudang cukup
    if($selisih > 0 && $stok_sekarang < $selisih) {
        echo "<div class='alert alert-danger'>Gagal! Stok produk tidak mencukupi untuk penambahan jumlah tersebut (Sisa stok: $stok_sekarang).</div>";
    } else {
        // 4. Update Stok Produk (Jika nambah beli, stok berkurang. Jika kurang beli, stok bertambah)
        mysqli_query($koneksi, "UPDATE produk SET stok = stok - $selisih WHERE id='$produk_id'");

        // 5. Hitung Subtotal baru & Update Detail
        $q_p = mysqli_query($koneksi, "SELECT harga FROM produk WHERE id='$produk_id'");
        $harga = mysqli_fetch_assoc($q_p)['harga'];
        $subtotal_baru = $harga * $qty_baru;

        mysqli_query($koneksi, "UPDATE detail_penjualan SET jumlah_produk='$qty_baru', subtotal='$subtotal_baru' WHERE id='$detail_id'");

        // 6. Update Total Harga di Tabel Penjualan (Header)
        $q_sum = mysqli_query($koneksi, "SELECT SUM(subtotal) as total FROM detail_penjualan WHERE penjualan_id='$id'");
        $total_total = mysqli_fetch_assoc($q_sum)['total'] ?? 0;
        mysqli_query($koneksi, "UPDATE penjualan SET total_harga='$total_total' WHERE id='$id'");

        echo "<script>alert('Jumlah item berhasil diperbarui.'); window.location='penjualan_edit.php?id=$id';</script>";
        exit;
    }
}

// --- LOGIKA 3: HAPUS SATU ITEM DARI NOTA (RESTORE STOK) ---
if(isset($_GET['hapus_item'])) {
    $det_id = $_GET['hapus_item'];
    
    $q_d = mysqli_query($koneksi, "SELECT * FROM detail_penjualan WHERE id='$det_id'");
    $d_d = mysqli_fetch_assoc($q_d);
    $produk_id = $d_d['produk_id'];
    $qty = $d_d['jumlah_produk'];

    // Balikkan stok
    mysqli_query($koneksi, "UPDATE produk SET stok = stok + $qty WHERE id='$produk_id'");
    
    // Hapus detail
    mysqli_query($koneksi, "DELETE FROM detail_penjualan WHERE id='$det_id'");

    // Update total header
    $q_sum = mysqli_query($koneksi, "SELECT SUM(subtotal) as total FROM detail_penjualan WHERE penjualan_id='$id'");
    $total_total = mysqli_fetch_assoc($q_sum)['total'] ?? 0;
    mysqli_query($koneksi, "UPDATE penjualan SET total_harga='$total_total' WHERE id='$id'");

    echo "<script>alert('Item berhasil dihapus dari nota dan stok telah diperbarui.'); window.location='penjualan_edit.php?id=$id';</script>";
}

// Ambil data penjualan saat ini
$q_pen = mysqli_query($koneksi, "SELECT * FROM penjualan WHERE id='$id'");
$data_pen = mysqli_fetch_assoc($q_pen);
?>

<h2>Ubah Transaksi : INV-<?php echo str_pad($id, 3, '0', STR_PAD_LEFT); ?></h2>

<div class="card" style="margin-bottom: 20px;">
    <h3>1. Informasi Pelanggan</h3>
    <form method="POST" action="">
        <div class="form-group">
            <label>Pilih Pelanggan</label>
            <select name="pelanggan_id" class="form-control">
                <option value="">-- Pelanggan Umum --</option>
                <?php
                $q_pel = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                while($p = mysqli_fetch_assoc($q_pel)) {
                    $selected = ($p['id'] == $data_pen['pelanggan_id']) ? "selected" : "";
                    echo "<option value='".$p['id']."' $selected>".$p['nama_pelanggan']."</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="update_header" class="btn">Simpan Perubahan</button>
        <a href="detail_penjualan.php" class="btn btn-warning">Kembali ke Riwayat</a>
    </form>
</div>

<div class="card">
    <h3>2. Rincian Produk</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th width="150px">Jumlah</th>
                <th>Subtotal</th>
                <th>Hapus</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q_det = mysqli_query($koneksi, "
                SELECT detail_penjualan.*, produk.nama_produk 
                FROM detail_penjualan 
                JOIN produk ON detail_penjualan.produk_id = produk.id 
                WHERE detail_penjualan.penjualan_id = '$id'
            ");
            while($item = mysqli_fetch_assoc($q_det)) {
            ?>
            <tr>
                <td><?php echo $item['nama_produk']; ?></td>
                <td>Rp <?php echo number_format($item['subtotal']/$item['jumlah_produk'],0,',','.'); ?></td>
                <td>
                    <form method="POST" action="" style="display: flex; gap: 5px;">
                        <input type="hidden" name="detail_id" value="<?php echo $item['id']; ?>">
                        <input type="number" name="jumlah" value="<?php echo $item['jumlah_produk']; ?>" min="1" class="form-control" style="width: 70px;">
                        <button type="submit" name="update_item" class="btn btn-sm" style="background-color: #6367FF; color: white;" title="Simpan Perubahan">Simpan</button>
                    </form>
                </td>
                <td>Rp <?php echo number_format($item['subtotal'],0,',','.'); ?></td>
                <td>
                    <a href="penjualan_edit.php?id=<?php echo $id; ?>&hapus_item=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini? Stok produk akan dikembalikan.')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; font-size: 18px;">
                <td colspan="3" align="right">Total Pembayaran:</td>
                <td colspan="2" style="color: #6367FF;">Rp <?php echo number_format($data_pen['total_harga'],0,',','.'); ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<?php 
include 'frontend/footer.php';
?>
