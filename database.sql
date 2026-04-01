-- ==========================================
-- FILE DATABASE UNTUK BELAJAR (ZAHWA TOYS)
-- ==========================================

-- 1. Membuat ruangan / wadah database bernama zahwa_toys (jika belum ada)
CREATE DATABASE IF NOT EXISTS zahwa_toys;

-- 2. Memilih wadah zahwa_toys tersebut untuk kita gunakan sekarang
USE zahwa_toys;

-- 3. Membuat tabel untuk menyimpan data Pelanggan
CREATE TABLE pelanggan (
    -- Kolom id sebagai Kunci Utama (Primary Key). AUTO_INCREMENT agar angkanya nambah sendiri 1,2,3...
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Kolom nama_pelanggan wajib diisi (NOT NULL) dengan maksimal 100 huruf (VARCHAR)
    nama_pelanggan VARCHAR(100) NOT NULL,
    -- Kolom alamat dengan tipe TEXT untuk menyimpan teks tulisan panjang (tanpa batas pasti)
    alamat TEXT,
    -- Kolom nomor_telepon dengan tipe VARCHAR khusus 20 huruf/angka
    nomor_telepon VARCHAR(20)
);

-- 4. Membuat tabel untuk menyimpan data Produk Mainan
CREATE TABLE produk (
    -- Kolom id sebagai Kunci Utama dengan angka otomatis nambah
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Kolom nama barang/produk wajib ada (NOT NULL)
    nama_produk VARCHAR(100) NOT NULL,
    -- Kolom harga bertipe angka desimal panjang 10 digit, dengan 2 angka di belakang koma (misal: 10000.00)
    harga DECIMAL(10, 2) NOT NULL,
    -- Kolom stok bertipe bilangan bulat (INT) yang wajib diisi (NOT NULL)
    stok INT NOT NULL
);

-- 5. Membuat tabel untuk Kepala Nota Transaksi (Penjualan Global)
CREATE TABLE penjualan (
    -- ID unik nota untuk nota 1, nota 2, dst
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Kolom waktu tanggal_penjualan, otomatis diisi waktu saat ini/sekarang (DEFAULT CURRENT_TIMESTAMP)
    tanggal_penjualan DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- Kolom pencatat total uang bayaran satu bon keseluruhan
    total_harga DECIMAL(10, 2) NOT NULL,
    -- Kolom menyimpan siapa ID pembelinya (diambil dari tabel pelanggan)
    pelanggan_id INT,
    -- Aturan Jaga-Jaga: FOREIGN KEY mengikat tabel penjualan ini ke tabel pelanggan.
    -- ON DELETE RESTRICT berarti: Kita gak boleh hapus Pelanggan kalau dia masih nunggak/punya nota.
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT
);

-- 6. Membuat tabel untuk Rincian Isi Keranjang per bon (Detail Penjualan)
CREATE TABLE detail_penjualan (
    -- ID unik rincian per baris barang yang di-scan kasir
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Kolom penunjuk ke Nota mana rincian ini menempel (Merujuk ke tabel penjualan)
    penjualan_id INT,
    -- Kolom penunjuk ke Mainan apa yang dibeli (Merujuk ke tabel produk)
    produk_id INT,
    -- Berapa banyak mainan ini yang dibeli di kasir (1, 2, atau 10 buah)
    jumlah_produk INT NOT NULL,
    -- Uang yang harus dibayar khusus mainan ini saja (harga x jumlah)
    subtotal DECIMAL(10, 2) NOT NULL,
    -- Aturan: Kalau nota utamanya dihapus (penjualan dihapus), maka rincian ini IKUT terhapus otomatis (CASCADE)
    FOREIGN KEY (penjualan_id) REFERENCES penjualan(id) ON DELETE CASCADE,
    -- Aturan: Nggak boleh sembarangan hapus mainan dari master data kalau masih ada di riwayat pembelian (RESTRICT)
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE RESTRICT
);
