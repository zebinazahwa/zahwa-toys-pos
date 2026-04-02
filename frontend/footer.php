<!-- File ini berguna untuk diletakkan di paling akhir struktur dokumen HTML. Jadi kita nggak capek ngetik ulang. -->

<!-- Tag /main ini artinya kita MENUTUP elemen container (wadah berlatar putih web) yang sebelumnya DIBUKA di baris ke-49 file header.php -->
</main>

<!-- Mulai Area Kaki Website (Footer) -->
<!-- <footer ...> Membuka label penutup halaman, menggunakan inline style murni untuk ngatur rata tengah (center), jarak bantal (padding), warna tulisan yang agak abu-abu (#777), huruf agak kecil (14px), jarak luar atas (margin-top), & memunculkan segaris di atas kakinya web (border-top solid 1px) -->
<footer style="text-align: center; padding: 20px; color: #777; font-size: 14px; margin-top: 40px; border-top: 1px solid #eee;">
    
    <!-- Tanda "Dan-Copy-TitikKoma" (&copy;) adalah kode HTML rahasia untuk menampilkan logo Hak Cipta 'C' dilingkari -->
    &copy; 
    
    <!-- Tag PHP di tengah HTML ini cuma numpang mampir. -->
    <!-- Tujuan echo date('Y') adalah supaya angka tahun di kaki web kita tidak perlu diketik manual. Dia nambah tahun otomatis (Tahun 2026, 2027 besok otomatis ganti sendiri) sesuai jam Server (Y = Year 4 digit) -->
    <!-- Tulisan nama proyek pesanan kita -->
    Zahwa Toys. Hak Cipta &copy; <?php echo date('Y'); ?>. Sistem Manajemen Inventaris dan Penjualan Berbasis Web.
    
<!-- Menutup batas kaki web secara mutlak -->
</footer>

<!-- Tag /body, ini adalah penutup KERANGKA FISIK web yang tadi dibuka di header.php baris ke-16 -->
</body>
<!-- Tag /html, ini penutup jiwa struktur bahasa HTML sesungguhnya (tag terluar) yang tadi dibuka di header.php baris ke-6 -->
</html>
