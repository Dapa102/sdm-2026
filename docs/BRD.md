# BUSINESS REQUIREMENTS DOCUMENT (BRD)
## SISTEM SUMBER DAYA MANUSIA TERINTEGRASI 
### (Fokus: Absen Dinas Luar, Sistem Merit, & Pembinaan Karir)

---

| **Dokumen** | **Keterangan** |
|---|---|
| Nama Dokumen | Business Requirements Document – Sistem SDM Terintegrasi (Final v2.0) |
| Versi | 2.0 (Revisi berdasarkan klarifikasi Dinas Luar) |
| Tanggal | 18 Juni 2026 |
| Status | Final untuk Pengembangan MVP |
| Penulis | Product Manager |
| Pemilik Proyek | HR Director |

---

## 1. RINGKASAN EKSEKUTIF

Dokumen ini merinci kebutuhan bisnis untuk pengembangan **Sistem Sumber Daya Manusia Terintegrasi** yang terdiri dari 3 layanan utama. Yang membedakan BRD ini adalah spesifikasi bahwa **Layanan Absen Jarak Jauh** diperuntukkan **khusus bagi karyawan yang sedang menjalankan Dinas Luar Kota (Perjalanan Dinas)** sebagai pengganti absensi di kantor pusat. 

Karyawan yang sedang dinas wajib melakukan **check-in dan check-out SETIAP HARI** (sama seperti di kantor), dengan validasi lokasi mengacu pada **titik tujuan dinas** (bukan kantor) serta wajib dilengkapi **Surat Tugas** resmi yang dibuat sebelumnya oleh HR/Manajer. Data absensi dinas ini akan menjadi salah satu input utama dalam perhitungan **Point Merit**, yang selanjutnya mendorong rekomendasi **Pembinaan Karir**.

---

## 2. LATAR BELAKANG PROYEK

### 2.1 Kondisi Saat Ini (AS-IS)
| **No** | **Permasalahan** | **Dampak** |
|---|---|---|
| 1 | Absensi karyawan dinas luar masih manual (WhatsApp/SMS/foto dikirim ke HR) | Data mudah hilang, sulit diaudit, dan rawan kecurangan |
| 2 | Tidak ada validasi lokasi riil saat dinas, sehingga perusahaan tidak yakin karyawan benar-benar di lokasi klien | Potensi kerugian biaya perjalanan dan hilangnya kepercayaan |
| 3 | Penghargaan untuk karyawan yang sering dinas luar tidak terukur secara objektif | Karyawan enggan ditugaskan ke luar kota |
| 4 | Rekomendasi pelatihan tidak mempertimbangkan pengalaman lapangan/dinas luar | Pengembangan karir tidak sinkron dengan kebutuhan aktual |

### 2.2 Visi Sistem (TO-BE)
Membangun sistem terintegrasi yang:
- Mewajibkan **Surat Tugas** sebagai prasyarat absensi dinas luar.
- Merekam kehadiran dinas secara **akurat via GPS** (dibandingkan dengan lokasi tujuan) dan **foto bukti** setiap check-in/check-out.
- Menghitung point merit secara objektif (memberi **bonus** untuk dinas yang tuntas, tanpa **potongan** jika terlambat karena perjalanan).
- Memberikan rekomendasi pelatihan berdasarkan intensitas dan keberhasilan dinas luar.

---

## 3. TUJUAN PROYEK (KUANTITATIF)

| **No** | **Indikator Keberhasilan** | **Target MVP** |
|---|---|---|
| 1 | Akurasi pencocokan lokasi GPS dengan tujuan dinas | ≥ 95% |
| 2 | Waktu pembuatan Surat Tugas oleh HR | < 5 menit per surat |
| 3 | Kecepatan proses check-in/out | < 3 detik |
| 4 | Persentase karyawan yang mengunggah bukti foto saat check-in | 100% (wajib) |
| 5 | Adopsi sistem oleh karyawan dinas | ≥ 90% di bulan pertama |

---

## 4. RUANG LINGKUP (IN SCOPE vs OUT OF SCOPE)

### 4.1 Termasuk (In Scope) untuk MVP
| **Layanan** | **Fitur yang Dicakup (MVP)** |
|---|---|
| **Absen Dinas Luar** | Manajemen Surat Tugas, Check-in/out GPS harian, Validasi Radius Tujuan, Upload Foto, Approval 1 Level, Riwayat Dinas. |
| **Sistem Merit** | Perhitungan point otomatis (bonus per hari dinas), Dashboard saldo, Katalog reward sederhana, Pengajuan & approval reward. |
| **Pembinaan Karir** | Katalog pelatihan, rekomendasi berbasis point/rules, pendaftaran pelatihan, penambahan point setelah selesai. |

### 4.2 Tidak Termasuk (Out of Scope) untuk MVP
- Verifikasi Wajah (Face Recognition)
- Mode Offline (sinkronisasi tanpa internet)
- Geofencing dinamis untuk banyak cabang (cukup 1 titik tujuan per Surat Tugas)
- Single Sign-On (SSO) dengan sistem lain
- Integrasi langsung ke sistem Penggajian (Payroll)

---

## 5. PEMANGKU KEPENTINGAN (STAKEHOLDERS)

| **Peran** | **Tanggung Jawab dalam MVP** |
|---|---|
| **Karyawan** | Melihat surat tugas aktif, melakukan check-in/out harian via web responsive, upload foto, mengajukan point. |
| **Manajer / Atasan Langsung** | Membuat Surat Tugas untuk tim, menyetujui absensi dinas bawahan, menyetujui penukaran point. |
| **Admin HR** | Mengelola data master karyawan, mengkonfigurasi parameter point dasar, memonitor rekap dinas. |
| **Tim IT / Developer** | Membangun sistem sesuai spesifikasi MVP ini. |

---

## 6. KEBUTUHAN FUNGSIONAL (LENGKAP DENGAN FLAG MVP)

### 6.1 Layanan Absen Jarak Jauh (KHUSUS DINAS LUAR)

> **Konsep Utama:** Absensi ini hanya berlaku jika ada **Surat Tugas (ST)**. Karyawan wajib check-in/out **setiap hari** selama periode ST, membandingkan GPS dengan lokasi tujuan ST, dan wajib upload foto.

| **ID** | **Kebutuhan Fungsional** | **Prioritas** | **Deskripsi Minimal** |
|---|---|---|---|
| **A-00** | **Manajemen Surat Tugas** | **WAJIB - MVP** | HR/Manajer membuat ST dengan mengisi: Nama Karyawan, Tanggal Mulai-Selesai, Alamat/Lokasi Tujuan Dinas (peta + koordinat GPS), Radius yang diizinkan (default 300m), serta upload dokumen ST (PDF). |
| **A-01** | **Check-in Harian (Terikat ST)** | **WAJIB - MVP** | Karyawan buka daftar "Dinas Aktif", pilih ST-nya, lalu tekan Check-in. Sistem mencatat waktu, koordinat GPS aktual, dan **mewajibkan upload foto selfie + lokasi**. |
| **A-02** | **Check-out Harian** | **WAJIB - MVP** | Karyawan menyelesaikan dinas di hari tersebut dengan menekan Check-out. Wajib diisi di sore hari (minimal 7 jam setelah check-in) dan wajib upload foto bukti saat check-out. |
| **A-03** | **Validasi Radius Dinamis** | **WAJIB - MVP** | Sistem membandingkan GPS check-in dengan koordinat tujuan ST. Jika di luar radius, sistem memberi peringatan "Di Luar Lokasi Dinas" namun tetap mengizinkan check-in. Status teknis disimpan sebagai `OUT_OF_RANGE` dan status approval tetap `PENDING` untuk diverifikasi Manajer. |
| **A-04** | **Upload Bukti Foto** | **WAJIB - MVP** | Saat check-in dan check-out, karyawan wajib mengambil foto (selfie dengan latar belakang lokasi dinas). Foto tersimpan sebagai bukti audit. |
| **A-05** | **Riwayat Dinas Saya** | **WAJIB - MVP** | Karyawan dapat melihat kalender/daftar riwayat absen dinasnya (status: Menunggu, Disetujui, Ditolak). |
| **A-06** | **Approval Absensi oleh Manajer** | **WAJIB - MVP** | Manajer menerima notifikasi daftar absensi dinas bawahan. Manajer bisa menyetujui (menjadi SAH) atau menolak (dianggap ALPA) per hari atau sekaligus untuk seluruh periode. |
| A-07 | Laporan Rekap Dinas (Excel/PDF) | FASE 2 | Ekspor data dinas untuk keperluan reimburse / arsip HR. |
| A-08 | Notifikasi Push Otomatis | FASE 2 | Pengingat check-in via email/WA. |
| A-09 | Verifikasi Wajah (AI) | FASE 2 | Validasi tambahan untuk mencegah titip absen. |
| A-10 | Mode Offline | FASE 2 | Check-in tanpa sinyal internet (disimpan lokal, dikirim saat online). |

---

### 6.2 Layanan Sistem Merit (Perhitungan Point)

> **Konsep Utama:** Karena ini Dinas Luar, **tidak ada potongan point** untuk keterlambatan. Fokusnya adalah **Bonus Penyelesaian Dinas**.

| **ID** | **Kebutuhan Fungsional** | **Prioritas** | **Deskripsi Minimal** |
|---|---|---|---|
| **M-01** | **Perhitungan Point Otomatis** | **WAJIB - MVP** | Sistem menghitung point berdasarkan aturan: **(a) Bonus Dinas:** +10 Point per hari dinas yang statusnya "Disetujui" Manajer. **(b) Tidak ada potongan** meskipun check-in terlambat (kecuali tidak check-in sama sekali, maka bonus hangus untuk hari itu). |
| **M-02** | **Dashboard Saldo Point** | **WAJIB - MVP** | Karyawan melihat total point yang dimiliki, rincian perolehan (dari dinas mana saja), dan riwayat penggunaan. |
| **M-03** | **Katalog Reward (Minimal)** | **WAJIB - MVP** | Daftar 5 reward awal: Voucher Belanja (50pt), Bonus Tunai (100pt), Cuti Tambahan 1 hari (75pt), Merchandise (25pt), Kesempatan Pelatihan Premium (200pt). |
| **M-04** | **Pengajuan Penukaran Point** | **WAJIB - MVP** | Karyawan memilih reward, mengisi alasan, lalu mengajukan ke atasan. |
| **M-05** | **Approval Penukaran Point** | **WAJIB - MVP** | Manajer menerima notifikasi dan menyetujui/menolak pengajuan point bawahan. Jika disetujui, point otomatis berkurang. |
| **M-07** | **Histori Transaksi** | **WAJIB - MVP** | Catatan lengkap "Point Masuk" (dari dinas) dan "Point Keluar" (untuk reward) yang tidak bisa diubah. |
| M-06 | Konfigurasi Parameter Dinamis | FASE 2 | Admin bisa mengubah besaran bonus per hari dinas (default 10) tanpa coding. |
| M-08 | Laporan Analitik Distribusi Point | FASE 2 | Dashboard tren perolehan point per divisi. |

---

### 6.3 Layanan Pembinaan Karir

> **Konsep Utama:** Rekomendasi pelatihan didasarkan pada akumulasi point dan frekuensi dinas luar.

| **ID** | **Kebutuhan Fungsional** | **Prioritas** | **Deskripsi Minimal** |
|---|---|---|---|
| **C-02** | **Rekomendasi Berbasis Rule** | **WAJIB - MVP** | Jika point karyawan mencapai ambang batas (misal > 100 point dalam 3 bulan), sistem menampilkan rekomendasi pelatihan lanjutan (contoh: "Sertifikasi Manajemen Proyek" atau "Negosiasi dengan Klien"). |
| **C-03** | **Katalog Pelatihan** | **WAJIB - MVP** | Daftar pelatihan yang tersedia dengan deskripsi, durasi, dan syarat point minimal. |
| **C-04** | **Pendaftaran Pelatihan** | **WAJIB - MVP** | Karyawan dapat mendaftar ke pelatihan yang direkomendasikan atau yang tersedia. |
| **C-09** | **Sinkronisasi ke Merit (Umpan Balik)** | **WAJIB - MVP** | Ketika HR/Admin menandai karyawan "Lulus/Selesai Pelatihan", sistem otomatis menambahkan +25 point ke akun karyawan tersebut. |
| C-01 | Pemetaan Kompetensi Kompleks | FASE 2 | Asesmen multidimensi untuk gap kompetensi. |
| C-05 | Visualisasi Jalur Karir (Peta Jabatan) | FASE 2 | Diagram alur promosi jabatan. |
| C-08 | Rekomendasi Manual dari Manajer | FASE 2 | Manajer bisa langsung menunjuk bawahan untuk ikut pelatihan tertentu. |

---

### 6.4 Integrasi Wajib Antar Layanan (MVP)

| **Alur** | **Deskripsi** |
|---|---|
| **Absensi → Merit** | Setelah Manajer menyetujui absensi dinas hari ini, sistem otomatis menambah +10 Point ke akun karyawan (tanpa perlu input manual). |
| **Pembinaan → Merit** | Setelah HR menandai pelatihan selesai, sistem otomatis menambah +25 Point. |
| **Merit → Pembinaan** | Jika total point melewati threshold tertentu, sistem menampilkan rekomendasi pelatihan di dashboard karyawan. |

---

## 7. KEBUTUHAN NON-FUNGSIONAL (NFR) - MVP

| **Kategori** | **Kebutuhan** | **Spesifikasi** |
|---|---|---|
| **Performa** | Response Time | < 3 detik (karena wajib upload foto, waktu bisa sedikit lebih lama) |
| **Keamanan** | Otentikasi | Login Email + Password (MFA ditunda ke Fase 2) |
| | Otorisasi | 3 Role bisnis (Karyawan, Manajer, Admin HR) + 1 Role teknis (Super Admin) |
| | Audit Trail | Seluruh check-in/out dan approval tercatat log-nya |
| **Platform** | Perangkat | **WAJIB** Web responsive (desktop + mobile browser) dengan HTTPS karena GPS dan Kamera menggunakan Geolocation API dan Camera API. |
| **Penyimpanan** | File | Foto bukti disimpan di cloud storage (AWS S3 / Google Cloud) |

---

## 8. ATURAN BISNIS KHUSUS (BUSINESS RULES) - HARUS DIINGAT OLEH DEVELOPER

| **ID** | **Aturan** |
|---|---|
| BR-01 | **Surat Tugas adalah Prasyarat Mutlak.** Karyawan TIDAK BISA check-in dinas luar jika tidak ada Surat Tugas yang aktif atas namanya. |
| BR-02 | **Absensi Harian Wajib.** Selama periode ST (misal 3 hari), karyawan WAJIB check-in dan check-out SETIAP HARI. Jika satu hari terlewat, bonus untuk hari itu hangus (tidak dapat diganti). |
| BR-03 | **Toleransi Keterlambatan.** Karyawan tetap diizinkan check-in meskipun terlambat dari jam kantor (misal datang jam 10.00), dan **TIDAK** ada pemotongan point. |
| BR-04 | **Lokasi Referensi.** Validasi GPS menggunakan **koordinat tujuan ST** (bukan koordinat kantor pusat). |
| BR-05 | **Approval Wajib.** Point bonus dinas (+10/hari) BARU ditambahkan ke saldo karyawan setelah Manajer meng-klik tombol "Setujui" di dashboard. |
| BR-06 | **Masa Berlaku Point.** Point memiliki masa berlaku 12 bulan sejak diperoleh (jika tidak digunakan, hangus). |

---

## 9. FITUR MVP – RANGKUMAN AKHIR UNTUK TIM PENGEMBANG

> *Ini adalah daftar "Wajib" yang harus sudah siap di versi 1.0 (target rilis 3-4 bulan).*

**1. Modul Admin / HR:**
- [ ] CRUD Surat Tugas (buat, edit, hapus, lihat).
- [ ] Upload dokumen ST (PDF).
- [ ] Tandai peserta pelatihan "Selesai".

**2. Modul Karyawan (Web Responsive):**
- [ ] Login.
- [ ] Dashboard "Dinas Aktif Saya".
- [ ] Tombol Check-in (rekam GPS + kamera foto + pilih ST).
- [ ] Tombol Check-out (rekam GPS + waktu + kamera foto).
- [ ] Lihat Saldo Point & Histori.
- [ ] Ajukan Penukaran Reward.
- [ ] Daftar Pelatihan.

**3. Modul Manajer:**
- [ ] Buat Surat Tugas untuk bawahan.
- [ ] Dashboard "Daftar Absensi Bawahan" (lihat foto & GPS).
- [ ] Tombol "Setujui" atau "Tolak" per hari dinas.
- [ ] Tombol "Setujui" atau "Tolak" pengajuan reward bawahan.

**4. Backend & Database:**
- [ ] API Check-in/out.
- [ ] Cronjob/Scheduler untuk validasi harian (jika diperlukan).
- [ ] Relasi database: User -> Surat Tugas -> Hari Dinas -> Check-in/out.

---

## 10. KRITERIA KEBERHASILAN MVP (GO-LIVE)

| **No** | **Kriteria** | **Target** |
|---|---|---|
| 1 | Karyawan berhasil check-in/out dengan GPS dan foto | 100% user flow berjalan tanpa error |
| 2 | Manajer berhasil menyetujui absensi | Proses < 1 menit per transaksi |
| 3 | Sistem berhasil menambah point setelah approval | Akurasi perhitungan 100% |
| 4 | Karyawan berhasil menukar point dengan reward | Proses end-to-end (ajuan → approval → pengurangan point) berhasil |

---

## 11. JADWAL TINGKAT TINGGI (HIGH-LEVEL TIMELINE)

| **Fase** | **Durasi** | **Output** |
|---|---|---|
| Perencanaan & Desain Database | 2 Minggu | Skema database, Wireframe UI |
| Pengembangan MVP (Backend + Mobile) | 10 Minggu | Aplikasi siap uji internal |
| UAT (User Acceptance Test) dengan 20 user pilot | 2 Minggu | Siap rilis |
| Deployment & Sosialisasi | 1 Minggu | Go-Live untuk seluruh divisi |

---

## 12. LAMPIRAN – ALUR USER JOURNEY (STUDI KASUS)

**Skenario:** *Andi (Karyawan) ditugaskan dinas ke Bandung selama 2 hari (Senin-Selasa).*

1. **Jumat sebelumnya:** HR (Budi) membuat Surat Tugas di sistem untuk Andi, mengisi lokasi "Jl. Asia Afrika No. 1, Bandung", periode 21-22 Juni.
2. **Senin, 07.30:** Andi tiba di Bandung. Buka Web App → "Dinas Aktif" → pilih tugas Bandung → tekan **Check-in**. GPS terekam (Bandung), Andi foto selfie dengan gedung klien, lalu submit.
3. **Senin, 17.00:** Andi tekan **Check-out**, GPS dan foto bukti check-out terekam.
4. **Selasa, 07.45:** Andi check-in lagi (sedikit terlambat, tapi sistem tetap izinkan dan TIDAK potong point).
5. **Selasa, 17.00:** Andi check-out dengan GPS dan foto bukti.
6. **Rabu pagi:** Manajer Andi masuk dashboard, lihat 2 hari absensi Andi lengkap dengan foto. Klik **"Setujui Semua"**.
7. **Otomatis:** Sistem menambah **+20 Point** ke akun Andi (2 hari × 10).
8. **Andi melihat saldo:** "Saya punya 20 point, bisa saya tukar dengan Voucher Belanja!"

