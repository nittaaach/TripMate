# Product Requirements Document (PRD) — TripMate

**Versi:** 1.0  
**Status:** Draft untuk validasi kebutuhan bisnis  
**Tanggal:** 26 Oktober 2026  
**Produk:** TripMate — platform rental mobil berbasis preferensi perjalanan

## 1. Ringkasan Produk

TripMate adalah platform rental mobil yang membantu customer memilih kendaraan berdasarkan rencana liburan, kebutuhan perjalanan, dan ketersediaan unit. Customer yang belum tahu mobil apa yang cocok dapat mengisi destinasi, titik awal, tanggal, jumlah penumpang, bagasi, anggaran, serta kebutuhan khusus. Sistem menyaring kendaraan yang tidak memenuhi syarat, lalu memberi peringkat kendaraan yang paling sesuai beserta alasan rekomendasinya.

Customer yang sudah mengetahui kendaraan yang diinginkan dapat melewati rekomendasi dan mencari atau memilih kendaraan secara langsung. Rekomendasi adalah bantuan pengambilan keputusan, bukan alur wajib.

**Asumsi stack awal:** Laravel 12, PHP 8.3+, MySQL 8, Laravel authentication, dan routing/directions API untuk estimasi jarak berkendara. Detail stack dapat disesuaikan saat implementasi.

## 2. Latar Belakang dan Masalah

- Customer sering kesulitan menentukan tipe mobil yang sesuai dengan medan dan kondisi destinasi.
- Pilihan kendaraan tidak cukup ditentukan oleh jumlah penumpang; jarak, kondisi jalan, bagasi, biaya, dan kebutuhan sopir juga berpengaruh.
- Informasi ketersediaan harus sesuai dengan rentang tanggal sewa agar unit tidak dipesan ganda.
- Customer yang sudah tahu mobil pilihannya tidak seharusnya dipaksa melewati wizard rekomendasi.

## 3. Tujuan Produk

1. Membantu customer menemukan kendaraan yang sesuai dengan rencana liburan.
2. Menampilkan alasan rekomendasi secara transparan dan mudah dipahami.
3. Memungkinkan customer langsung memilih mobil tanpa rekomendasi.
4. Menyediakan alur booking dan pencatatan pembayaran yang dapat dikelola.
5. Memberi admin/staff kemampuan mengelola armada, destinasi, ketersediaan, booking, dan pembayaran.

### Indikator keberhasilan awal

Target berikut adalah hipotesis MVP yang perlu divalidasi setelah tersedia data penggunaan:
- Minimal 70% customer yang memulai wizard menyelesaikan input rekomendasi.
- Minimal 30% sesi rekomendasi menghasilkan kendaraan yang dibuka detailnya atau dipilih.
- 0 double booking yang disebabkan oleh race condition aplikasi.
- 100% hasil rekomendasi yang ditampilkan memenuhi filter wajib: kapasitas, status kendaraan, tanggal, dan kebutuhan dasar.
- Customer dapat menyelesaikan jalur pemilihan langsung tanpa membuka wizard rekomendasi.

## 4. Pengguna dan Peran

### 4.1 Customer
- Menjelajah destinasi dan kendaraan.
- Membuat rencana liburan untuk rekomendasi opsional.
- Memilih kendaraan hasil rekomendasi atau memilih sendiri.
- Membuat booking, melihat status, mengirim/melihat pembayaran, dan memberi ulasan setelah sewa selesai.

### 4.2 Admin
- Mengelola akun/akses staf bila diperlukan.
- Mengelola data destinasi dan kendaraan.
- Mengelola foto kendaraan, tarif, dan status unit.
- Memantau seluruh booking dan pembayaran.
- Mengelola data kondisi medan/jalan destinasi.

### 4.3 Staff
- Memproses booking, konfirmasi, serah-terima, dan pengembalian kendaraan sesuai izin yang diberikan.
- Memverifikasi pembayaran manual jika metode tersebut digunakan.
- Melihat jadwal perawatan dan ketersediaan unit.

## 5. Ruang Lingkup

### 5.1 MVP — termasuk
- Autentikasi dan otorisasi berbasis peran (`customer`, `admin`, `staff`).
- Katalog kendaraan beserta foto, spesifikasi, harga, dan status.
- Katalog destinasi yang dikelola admin.
- Wizard rencana perjalanan dan rekomendasi kendaraan opsional.
- Integrasi routing API untuk estimasi jarak dan durasi, dengan penanganan kegagalan API.
- Penyaringan kelayakan kendaraan dan ranking skor rekomendasi.
- Detail kendaraan dan pemilihan kendaraan manual.
- Booking dengan validasi tanggal dan ketersediaan.
- Pencatatan pembayaran; payment gateway dapat ditambahkan setelah pilihan provider dan kebutuhan bisnis ditetapkan.
- Dashboard admin/staff untuk pengelolaan inti.
- Riwayat booking customer dan ulasan untuk booking yang selesai (dapat ditunda bila waktu MVP terbatas).

### 5.2 Tidak termasuk dalam MVP kecuali disepakati
- Dynamic pricing berbasis permintaan.
- Pelacakan GPS kendaraan secara real-time.
- Prediksi kondisi jalan real-time.
- Multi-vendor marketplace untuk banyak perusahaan rental.
- Rekomendasi generatif berbasis LLM.
- Asuransi, klaim, dan pengelolaan denda yang kompleks.
- Aplikasi native Android/iOS terpisah; MVP diasumsikan web responsif.

## 6. Alur Pengguna Utama

### Alur A — Customer membutuhkan rekomendasi
1. Customer membuka halaman pencarian dan memilih **“Bantu pilihkan mobil”**.
2. Customer mengisi destinasi, titik awal, tanggal mulai/selesai, jumlah penumpang, level bagasi, tipe perjalanan, anggaran harian (opsional), kebutuhan sopir, dan kebutuhan khusus.
3. Sistem memvalidasi input, mengambil estimasi rute dari routing API, dan menyimpan snapshot jarak/durasi bila tersedia.
4. Sistem memfilter unit yang tidak memenuhi syarat atau tidak tersedia pada rentang tanggal.
5. Sistem menghitung skor kendaraan yang lolos dan menampilkan hasil berurutan beserta alasan rekomendasi.
6. Customer membandingkan detail, memilih kendaraan, lalu melanjutkan booking.
7. Sistem menghitung ringkasan biaya, memvalidasi ulang ketersediaan, dan membuat booking.
8. Customer menyelesaikan langkah pembayaran sesuai metode yang tersedia.
9. Customer melihat status booking dan detail instruksi sewa.

### Alur B — Customer sudah tahu mobil yang diinginkan
1. Customer memilih **“Pilih mobil sendiri”**.
2. Customer memfilter katalog berdasarkan tanggal, kategori, transmisi, kapasitas, dan harga.
3. Sistem menampilkan unit yang cocok dan tersedia.
4. Customer membuka detail kendaraan, memilih tanggal/lokasi, lalu melanjutkan booking.
5. Sistem memvalidasi ulang harga dan ketersediaan sebelum booking dikonfirmasi.

### Alur C — Admin mengelola armada
1. Admin login.
2. Admin menambah/memperbarui data kendaraan dan foto.
3. Admin menetapkan tarif, kapasitas, karakteristik medan, kondisi jalan, status, serta informasi konsumsi bahan bakar/range baterai jika diketahui.
4. Sistem memvalidasi data dan memperbarui katalog/rekomendasi.

## 7. Persyaratan Fungsional

| ID | Fitur | Persyaratan |
|---|---|---|
| FR-01 | Autentikasi | Customer, admin, dan staff dapat login/logout; password disimpan sebagai hash. |
| FR-02 | Otorisasi | Endpoint dan halaman admin/staff dilindungi middleware/policy berdasarkan peran. |
| FR-03 | Destinasi | Customer dapat melihat destinasi aktif; admin dapat CRUD destinasi, koordinat, tipe medan, kondisi jalan, dan catatan akses. |
| FR-04 | Katalog kendaraan | Customer dapat melihat detail, foto, harga, kapasitas, transmisi, energi, fasilitas, dan kategori kendaraan. |
| FR-05 | Jalur manual | Customer dapat memilih mobil sendiri tanpa membuat rencana rekomendasi. |
| FR-06 | Input perjalanan | Sistem menerima destinasi, titik awal, tanggal, jumlah penumpang, bagasi, tipe perjalanan, budget opsional, kebutuhan sopir, dan catatan. |
| FR-07 | Estimasi rute | Sistem mengambil jarak/durasi berkendara dari routing API dan menyimpan snapshot serta sumbernya jika tersedia. |
| FR-08 | Filter kelayakan | Sistem menyingkirkan unit yang kapasitasnya kurang, statusnya tidak aktif, maintenance, bentrok booking, atau tidak memenuhi batasan wajib. |
| FR-09 | Ranking rekomendasi | Sistem memberi skor 0–100 untuk unit yang lolos dan menyimpan komponen skor serta alasan yang bisa dimengerti customer. |
| FR-10 | Hasil kosong | Jika tidak ada unit cocok, sistem menjelaskan penyebab umum dan memberi opsi mengubah tanggal, budget, kebutuhan, atau memilih destinasi/armada lain. |
| FR-11 | Booking | Sistem menghitung harga, menyimpan snapshot tarif, dan menghasilkan kode booking unik. |
| FR-12 | Anti double booking | Sistem mengecek ulang konflik dalam transaksi/locking sebelum membuat atau mengonfirmasi booking. |
| FR-13 | Pembayaran | Sistem mencatat metode, jumlah, status, referensi provider, waktu bayar, dan bukti transfer bila manual. |
| FR-14 | Manajemen booking | Admin/staff dapat melihat booking dan memperbarui status sesuai hak akses. |
| FR-15 | Maintenance | Sistem dapat memblokir kendaraan pada periode perawatan; fitur ini direkomendasikan untuk MVP agar jadwal akurat. |
| FR-16 | Riwayat customer | Customer dapat melihat rencana, booking, pembayaran, dan statusnya sendiri. |
| FR-17 | Ulasan | Customer dapat memberi rating 1–5 untuk booking yang selesai, maksimal satu ulasan per booking. |
| FR-18 | Audit operasional | Perubahan status penting sebaiknya tercatat dalam log/audit trail pada implementasi produksi. |

## 8. Aturan Bisnis Rekomendasi

### 8.1 Filter wajib (hard filters)
Kendaraan tidak boleh masuk ranking jika:
- `status` bukan `active` atau `is_available` bernilai false.
- Kapasitas kursi kurang dari kebutuhan penumpang menurut definisi bisnis yang konsisten.
- Kendaraan sedang dalam periode maintenance yang bertabrakan dengan periode sewa.
- Ada booking aktif yang bertabrakan dengan periode sewa.
- Harga melampaui budget maksimum jika budget ditetapkan sebagai batas keras oleh customer.
- Kebutuhan wajib seperti sopir atau fasilitas tertentu tidak terpenuhi.
- Tipe medan/kondisi jalan tidak termasuk dalam kemampuan kendaraan.

**Penting:** tetapkan apakah `passenger_count` menghitung pengemudi. Rancangan awal menyarankan jumlah penumpang customer saja, sedangkan `vehicles.seats` saat ini didefinisikan termasuk pengemudi. Jika customer menyewa dengan sopir, jumlah kursi untuk customer perlu dihitung konsisten agar tidak merekomendasikan kendaraan yang terlalu kecil.

### 8.2 Ranking (soft scoring)
Setelah hard filters, skor awal yang disarankan:
- Kecocokan medan dan kondisi jalan: **30%**
- Kapasitas penumpang dan bagasi: **20%**
- Kecocokan jarak/range/efisiensi: **20%**
- Kecocokan budget: **20%**
- Kebutuhan kenyamanan/sopir/transmisi/fasilitas: **10%**

Bobot adalah konfigurasi awal, bukan fakta tetap. Validasi dengan data dan uji pengguna. Bila faktor tidak memiliki data, sistem sebaiknya menormalisasi bobot yang tersedia atau menandai data tidak lengkap—jangan menganggap data kosong sebagai kecocokan sempurna.

### 8.3 Alasan rekomendasi
Alasan harus berasal dari data yang benar, misalnya:
- “Kapasitas sesuai untuk 4 penumpang dan bagasi sedang.”
- “Tipe kendaraan mendukung medan pegunungan dengan kondisi jalan moderat.”
- “Estimasi rute 180 km; estimasi efisiensi/range memenuhi kebutuhan perjalanan berdasarkan data kendaraan.”
- “Tarif harian berada dalam anggaran yang Anda masukkan.”

Jangan membuat klaim keselamatan jalan atau kemampuan off-road yang tidak didukung data.

### 8.4 Jarak dan durasi
Gunakan routing/directions API untuk jarak berkendara, bukan jarak garis lurus. Simpan hasil sebagai snapshot pada `vacation_plans`. Bila API gagal, tampilkan fallback yang jelas dan jangan mengarang jarak; customer masih dapat memilih mobil manual atau mencoba kembali.

## 9. Persyaratan Booking dan Pembayaran

- `pickup_datetime` harus lebih awal daripada `return_datetime`.
- Aturan durasi tagihan (`rental_days`) harus diputuskan: per 24 jam atau per hari kalender, termasuk grace period dan pembulatan.
- Harga, deposit, diskon, dan biaya tambahan harus disalin ke booking sebagai snapshot agar riwayat transaksi tidak berubah ketika tarif kendaraan diedit.
- Status booking dan status pembayaran adalah dua hal berbeda; pembayaran berhasil tidak otomatis berarti booking aman bila validasi armada gagal.
- Definisikan apakah booking `pending` menahan unit dan berapa lama hold berlaku.
- Perubahan status hanya boleh dilakukan oleh peran yang berwenang.
- Untuk payment gateway, verifikasi callback/webhook di server, validasi signature, dan buat pemrosesan idempotent.
- Jangan menyimpan nomor kartu atau data pembayaran sensitif secara langsung.

## 10. Persyaratan Nonfungsional

### Keamanan
- Gunakan Laravel authentication, authorization policy/middleware, CSRF protection, validation, dan mass-assignment protection.
- Hash password dengan mekanisme Laravel.
- Simpan rahasia API di environment variables, bukan repository.
- Validasi upload gambar (MIME, ukuran, dimensi bila diperlukan) dan simpan melalui Laravel Storage.
- Jangan mempercayai total harga, skor, role, maupun status dari client; hitung/validasi di server.
- Terapkan rate limiting untuk login, pencarian rekomendasi, dan endpoint sensitif.

### Performa dan reliabilitas
- Target awal halaman katalog dan hasil rekomendasi: p95 di bawah 2,5 detik tanpa menghitung latensi eksternal yang tidak terkendali; ukur dan revisi berdasarkan lingkungan produksi.
- Index kolom pencarian dan foreign key penting.
- Gunakan pagination pada katalog dan panel admin.
- Tangani timeout, rate limit, dan kegagalan routing/payment provider.
- Proses eksternal yang lambat dapat dipindahkan ke queue bila diperlukan.

### Aksesibilitas dan UX
- Responsif untuk desktop dan mobile.
- Form memiliki label, validasi per field, dan ringkasan error.
- Tampilkan harga, deposit, biaya tambahan, tanggal, dan aturan pembatalan secara transparan.
- Jangan memaksa customer memakai rekomendasi.
- Berikan penjelasan mengapa hasil kosong dan cara memperbaiki pencarian.

### Privasi
- Kumpulkan data yang diperlukan saja.
- Batasi akses data pribadi dan booking sesuai kepemilikan/role.
- Jelaskan penggunaan lokasi dan data perjalanan.
- Tentukan masa retensi data dan proses penghapusan sesuai kebijakan bisnis serta hukum yang berlaku.

## 11. Entitas Data Utama

Skema spreadsheet yang diberikan memuat:
- `users`: akun customer/admin/staff.
- `destinations`: informasi destinasi, koordinat, medan, dan kondisi jalan.
- `vehicles`: spesifikasi, harga, kemampuan medan, kapasitas, dan status unit.
- `vehicle_images`: foto kendaraan.
- `vacation_plans`: input perjalanan, tanggal, jarak/durasi snapshot, dan preferensi customer.
- `vehicle_recommendations`: hasil ranking, skor komponen, alasan, dan pilihan customer.
- `bookings`: transaksi penyewaan dan snapshot harga.
- `payments`: catatan pembayaran.
- `vehicle_maintenance`: periode perawatan unit.
- `reviews`: ulasan booking yang selesai.

Relasi dan tipe field rinci mengikuti workbook database schema yang disediakan. Sebelum migrasi final, konfirmasi aturan kursi, rental days, booking pending hold, cancellation/refund, dan provider pembayaran.

## 12. Kriteria Penerimaan MVP

1. Customer dapat memilih alur rekomendasi atau alur pilih mobil sendiri.
2. Input perjalanan tidak dapat disimpan jika tanggal tidak valid atau data wajib kosong.
3. Jarak rute hanya ditampilkan sebagai estimasi jika berasal dari sumber routing yang berhasil.
4. Hasil rekomendasi hanya berisi kendaraan yang lolos filter wajib.
5. Setiap hasil memiliki skor dan alasan yang konsisten dengan data kendaraan.
6. Ketika tidak ada kendaraan cocok, sistem menampilkan empty state dan opsi untuk menyesuaikan pencarian.
7. Customer tidak dapat melihat booking milik customer lain.
8. Sistem menolak booking yang tanggalnya berbenturan dengan booking/maintenance yang memblokir unit.
9. Dua request bersamaan tidak dapat mengonfirmasi unit yang sama untuk periode bertabrakan.
10. Harga booking tetap konsisten walau tarif kendaraan berubah setelah booking dibuat.
11. Admin dapat mengelola destinasi, kendaraan, foto, dan booking sesuai hak akses.
12. Kegagalan routing API atau payment provider tidak membuat data transaksi palsu atau status sukses palsu.

## 13. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Data medan kendaraan tidak akurat | Buat definisi operasional, review data oleh admin, dan tampilkan catatan bila data belum lengkap. |
| Routing API gagal/mahal | Adapter provider, timeout, cache bila sesuai ketentuan provider, dan fallback yang transparan. |
| Double booking | Transaksi DB, locking/strategi konsistensi, pemeriksaan konflik, serta test konkurensi. |
| Skor rekomendasi terasa tidak relevan | Tampilkan alasan, konfigurasi bobot, kumpulkan feedback, dan evaluasi metrik. |
| Tarif berubah setelah booking | Simpan snapshot harga dan aturan biaya pada booking. |
| Scope membesar | Prioritaskan katalog, rekomendasi rule-based, booking, dan admin; tunda fitur lanjutan. |

## 14. Pertanyaan Bisnis yang Harus Diputuskan

1. Apakah rental dihitung per 24 jam atau per hari kalender?
2. Apakah kursi `vehicles.seats` menghitung pengemudi? Bagaimana jika customer menyewa dengan sopir?
3. Apakah booking `pending` menahan unit? Berapa lama sebelum kedaluwarsa?
4. Metode pembayaran MVP: transfer manual, tunai, atau payment gateway tertentu?
5. Bagaimana kebijakan deposit, pembatalan, pengembalian dana, keterlambatan, dan biaya tambahan?
6. Apakah customer boleh menyewa tanpa akun, atau login wajib?
7. Apakah rental satu arah/multi-stop didukung pada MVP atau hanya dicatat sebagai preferensi?
8. Sumber routing API dan batas biaya/kuota apa yang akan digunakan?
9. Apakah `budget_per_day` merupakan batas keras atau preferensi untuk ranking?
10. Siapa yang bertanggung jawab memperbarui kondisi jalan dan data kemampuan kendaraan?

## 15. Rekomendasi Tahapan Implementasi

**Tahap 1 — Fondasi:** auth/role, destinasi, kendaraan, foto, seed data, panel admin dasar.  
**Tahap 2 — Rekomendasi:** vacation plan, adapter routing API, hard filters, scoring, alasan rekomendasi, empty state.  
**Tahap 3 — Booking:** pemilihan unit, kalkulasi harga, anti double booking, status booking, riwayat.  
**Tahap 4 — Pembayaran:** pencatatan pembayaran manual atau integrasi provider yang dipilih.  
**Tahap 5 — Operasional:** maintenance, ulasan, audit log, monitoring, pengujian beban dan keamanan.

---
**Catatan:** Dokumen ini adalah PRD awal untuk menyepakati kebutuhan dan aturan bisnis. Detail yang belum diputuskan ditandai sebagai asumsi atau pertanyaan, bukan dianggap sebagai keputusan final.
