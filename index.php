<?php
require_once 'auth_check.php';
?>
<?php
// ======= FILE: index.php (DASHBOARD) =======
// File ini yang paling pertama dibaca mesin.

// Memanggil file koneksi secara wajib (require), jadi file ini langsung punya akses Database
require_once 'backend/koneksi.php';

// Menempelkan tampilan (HTML Atas) yang ada di header.php ke baris ini 
include 'frontend/header.php';

// == BLOK 1: NGAMBIL HASIL TOTAL UNTUK DIPAJANG ==

// Menjalankan perintah (Query) menghitung kolom (COUNT) di seluruh tabel pelanggan
$query_pelanggan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pelanggan");
// Memecah hasil hitungan tabelnya jadi array
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
// Masukkan angkanya ke penampung `$total_pelanggan`
$total_pelanggan = $data_pelanggan['total'] ?? 0;

// Menjalankan perintah hitung jumlah baris data dalam tabel produk
$query_produk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
// Memecah array row kembalian MySQL
$data_produk = mysqli_fetch_assoc($query_produk);
// Disimpan ke keranjang $total_produk
$total_produk = $data_produk['total'] ?? 0;

// Menghitung jumlah TRANSAKSI berdasarkan hari yang sama hari ini (menggunakan MySQL CURDATE())
$query_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penjualan WHERE DATE(tanggal_penjualan) = CURDATE()");
// Cek IF keamanan: Kalau tabel dtemukan...
if($query_transaksi) {
    // Ambil angkanya
    $data_transaksi = mysqli_fetch_assoc($query_transaksi);
    // Masukkan ke variabel jumlah TRX
    $total_transaksi = $data_transaksi['total'] ?? 0;
// Kondisi ELSE jika tabel gagal dibaca/gagal terkoneksi 
} else {
    // Kasih angka nol biar gak error
    $total_transaksi = 0;
}

// Menjumlahkan MATA UANG RUPIAH dengan fungsi MySQL (SUM) di tabel penjualan khusus hari ini
$query_pendapatan = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM penjualan WHERE DATE(tanggal_penjualan) = CURDATE()");
// Jika sukses perintahnya
if($query_pendapatan) {
    // Pecah data uangnya ke memori server
    $data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
    // Variabel total pendapatan = hasil dari dalam database
    $total_pendapatan = $data_pendapatan['total'] ?? 0;
// Jika gagal mencari
} else {
    // Anggap tidak ada laci uang masuk
    $total_pendapatan = 0;
}

// == BLOK 2: DATA KESELURUHAN (SEMUA WAKTU) ==
// Kadang dashboard kosong kalau hari ini belum ada jualan, jadi kita tampilin juga total semua hari.
$query_all_trx = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penjualan");
$total_all_transaksi = mysqli_fetch_assoc($query_all_trx)['total'] ?? 0;

$query_all_rev = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM penjualan");
$total_all_pendapatan = mysqli_fetch_assoc($query_all_rev)['total'] ?? 0;
?>

<!-- Teks Judul memakai Tag Heading 2 (H2) -->
<h2>Dashboard</h2>

<!-- Paragraph P untuk menulis selamat datang -->
<p>Selamat datang di Sistem Manajemen Kasir <strong>Zahwa Toys</strong>. Silakan gunakan menu navigasi untuk mulai mengelola data dan transaksi sistem.</p>

<!-- Membuka div/ruang blok dengan kelas khusus desain dashboard kotak-kotak -->
<div class="dashboard-cards">
    
    <!-- Bagian Kartu / Kotak Informasi 1: Tentang jumlah item gudang -->
    <div class="card">
        <!-- Judul kecil bagian -->
        <h3>Total Produk Tersedia</h3>
        <!-- Memanggil langsung tag PHP echo (cetak) dengan number_format (misal 1000 jadi 1.000 dengan titik koma standar Rp) -->
        <div class="score"><?php echo number_format($total_produk, 0, ',', '.'); ?> Item</div>
    </div>
    
    <!-- Bagian Kartu / Kotak Informasi 2: Untuk jumlah member/pelanggan setia -->
    <div class="card">
        <h3>Total Pelanggan</h3>
        <!-- Echo mengeluarkan nilai variabel $total_pelanggan hasil MySQL COUNT tadi -->
        <div class="score"><?php echo number_format($total_pelanggan, 0, ',', '.'); ?> Orang</div>
    </div>
    
    <!-- Bagian Kartu / Kotak Informasi 3: Total aktivitas transaksi bon di tanggal hari ini saja -->
    <div class="card">
        <h3>Transaksi Hari Ini</h3>
        <!-- Echo menampilkan total trx -->
        <div class="score"><?php echo number_format($total_transaksi, 0, ',', '.'); ?> TRX</div>
    </div>
    
    <!-- Bagian Kartu / Kotak Informasi 4: Total pemasukan / omset laci hari ini -->
    <div class="card">
        <h3>Pendapatan Hari Ini</h3>
        <!-- Tempelkan teks statis 'Rp ' lalu gabungkan angka dari tabel SUM() tadi -->
        <div class="score">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></div>
    </div>

    <!-- DATA KESELURUHAN (TAMBAHAN BIAR GAK KOSONG) -->
    <div class="card" style="border-left-color: #17a2b8;">
        <h3>Total Semua Transaksi</h3>
        <div class="score" style="color: #17a2b8;"><?php echo number_format($total_all_transaksi, 0, ',', '.'); ?> Kotak</div>
    </div>

    <div class="card" style="border-left-color: #28a745;">
        <h3>Total Pendapatan (Seluruh Waktu)</h3>
        <div class="score" style="color: #28a745;">Rp <?php echo number_format($total_all_pendapatan, 0, ',', '.'); ?></div>
    </div>
<!-- Menutup wadah dashboard-cards utamanya -->
</div>

<!-- ==================== MODERN HELP WIDGET (FAB & DRAWER) ==================== -->

<!-- Overlay untuk background saat drawer buka -->
<div class="help-overlay" id="helpOverlay" onclick="toggleHelpDrawer()"></div>

<!-- Floating Action Button (FAB) -->
<button class="help-fab" onclick="toggleHelpDrawer()" title="Bantuan & FAQ">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        <line x1="9" y1="9" x2="15" y2="9"></line>
        <line x1="9" y1="13" x2="15" y2="13"></line>
    </svg>
</button>

<!-- Side Drawer Panel -->
<div class="help-drawer" id="helpDrawer">
    <!-- Header Drawer -->
    <div class="drawer-header">
        <div style="display: flex; align-items: center; gap: 12px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <h3 style="margin:0; font-size:18px;">Layanan Bantuan</h3>
        </div>
        <button class="drawer-close-btn" onclick="toggleHelpDrawer()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Body Drawer -->
    <div class="drawer-body">
        
        <!-- Search Bar FAQ -->
        <div class="faq-search-wrapper">
            <div class="faq-search-box">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="faqSearchInput" class="faq-search-input" placeholder="Cari panduan..." onkeyup="filterFaq()">
            </div>
        </div>

        <div class="help-section" style="margin-top: 0; padding-top: 0; border: none;">
            <!-- Panduan Cepat (Quick Start Cards) -->
            <div class="quickstart-grid">
                <div class="quickstart-card">
                    <div class="qs-icon qs-icon-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </div>
                    <h4>Kelola Produk</h4>
                    <p>Tambah, edit, dan hapus data produk melalui menu <strong>Produk</strong>.</p>
                    <a href="produk.php" class="qs-link">Buka Produk →</a>
                </div>

                <div class="quickstart-card">
                    <div class="qs-icon qs-icon-purple">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <h4>Proses Penjualan</h4>
                    <p>Lakukan transaksi penjualan dengan cepat dan cetak struk langsung.</p>
                    <a href="penjualan.php" class="qs-link">Buka Penjualan →</a>
                </div>
            </div>

            <!-- FAQ Accordion -->
            <div class="faq-container">
                <h3 class="faq-group-title">FAQ Terbaru</h3>

                <!-- FAQ Item 1 -->
                <div class="faq-item" id="faq-1">
                    <button class="faq-question" onclick="toggleFaq('faq-1')" aria-expanded="false">
                        <span>Cara menambahkan produk baru?</span>
                        <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Silakan buka menu <strong>Produk</strong> > Pilih tombol <strong>"Tambah Produk"</strong> > Isi Formulir > Simpan Data.</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item" id="faq-2">
                    <button class="faq-question" onclick="toggleFaq('faq-2')" aria-expanded="false">
                        <span>Cara melakukan transaksi penjualan?</span>
                        <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Akses menu <strong>Penjualan</strong> > Pilih produk yang akan dibeli > Tambahkan ke keranjang > Selesaikan Pembayaran.</p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item" id="faq-3">
                    <button class="faq-question" onclick="toggleFaq('faq-3')" aria-expanded="false">
                        <span>Cara mencetak faktur penjualan?</span>
                        <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Setelah pembayaran selesai, klik <strong>Cetak Faktur</strong> atau buka riwayat pada menu <strong>Detail Penjualan</strong>.</p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item" id="faq-4">
                    <button class="faq-question" onclick="toggleFaq('faq-4')" aria-expanded="false">
                        <span>Penjelasan data Dashboard?</span>
                        <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Halaman ini menampilkan akumulasi produk, jumlah pelanggan, serta rekapitulasi transaksi untuk hari ini.</p>
                    </div>
                </div>

                <!-- No Results State -->
                <div id="faqNoResults" class="faq-no-results">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="11" y1="8" x2="11" y2="14"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                    <p>Pertanyaan tidak ditemukan.<br><small>Coba kata kunci lain.</small></p>
                </div>

            </div><!-- end .faq-container -->

            <!-- Info Kontak -->
            <div class="help-contact-box">
                <div class="contact-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.6 19.79 19.79 0 0 1 1.62 5 2 2 0 0 1 3.6 2.81h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10.4a16 16 0 0 0 6 6l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 18h.19c1.1 0 1.98.88 1.98 1.97z"></path>
                    </svg>
                </div>
                <div>
                    <h4 style="font-size:14px;">Butuh Bantuan Lebih Lanjut?</h4>
                    <p style="font-size:12px;">Hubungi Administrator Zahwa Toys.</p>
                </div>
            </div>
        </div><!-- end .help-section -->
    </div><!-- end .drawer-body -->
</div><!-- end .help-drawer -->

<!-- Script JavaScript untuk Help Widget -->
<script>
// Fungsi toggle buka/tutup Drawer
function toggleHelpDrawer() {
    const drawer  = document.getElementById('helpDrawer');
    const overlay = document.getElementById('helpOverlay');
    
    drawer.classList.toggle('open');
    overlay.classList.toggle('active');

    // Mencegah scroll body saat drawer buka
    if (drawer.classList.contains('open')) {
        document.body.style.overflow = 'hidden';
        // Fokus otomatis ke input search
        setTimeout(() => document.getElementById('faqSearchInput').focus(), 400);
    } else {
        document.body.style.overflow = '';
    }
}

// Fungsi toggle buka/tutup tiap item FAQ
function toggleFaq(faqId) {
    const faqItem = document.getElementById(faqId);
    const button  = faqItem.querySelector('.faq-question');
    const isOpen = faqItem.classList.contains('active');

    // Tutup dulu semua item lain
    document.querySelectorAll('.faq-item.active').forEach(function(item) {
        if (item.id !== faqId) {
            item.classList.remove('active');
            item.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
        }
    });

    // Toggle item yang diklik
    if (!isOpen) {
        faqItem.classList.add('active');
        button.setAttribute('aria-expanded', 'true');
        
        // Scroll halus ke item yang dibuka jika terpotong
        setTimeout(() => {
            faqItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 300);
    } else {
        faqItem.classList.remove('active');
        button.setAttribute('aria-expanded', 'false');
    }
}

// Fungsi Filter / Cari FAQ
function filterFaq() {
    const input = document.getElementById('faqSearchInput');
    const filter = input.value.toLowerCase();
    const faqItems = document.querySelectorAll('.faq-item');
    const noResults = document.getElementById('faqNoResults');
    let hasResults = false;

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question span').innerText.toLowerCase();
        const answer = item.querySelector('.faq-answer').innerText.toLowerCase();
        
        if (question.includes(filter) || answer.includes(filter)) {
            item.style.display = "";
            hasResults = true;
        } else {
            item.style.display = "none";
        }
    });

    // Tampilkan pesan jika tidak ada hasil
    noResults.style.display = hasResults ? "none" : "block";
}
</script>

<?php
// Menyertakan kaki website (footer), di mana CSS container utamanya juga akan otomatis ditutup di file sana
include 'frontend/footer.php';
?>
