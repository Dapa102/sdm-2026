# IMPLEMENTATION PLAN
## Sistem SDM Terintegrasi MVP

### Acuan
- BRD: `docs/BRD.md` v2.0
- PRD: `docs/PRD.md` v2.0
- Platform: Web responsive berbasis Laravel 12 + Filament 5 + Livewire 4

---

## 1. Tujuan Implementasi

Membangun MVP Sistem SDM Terintegrasi berbasis web yang mencakup:

1. Absen Dinas Luar berbasis Surat Tugas, GPS, dan foto check-in/check-out.
2. Sistem Merit untuk perolehan, histori, masa berlaku, dan penukaran point.
3. Pembinaan Karir melalui katalog pelatihan, rekomendasi berbasis rule, dan bonus point setelah pelatihan selesai.

---

## 2. Prinsip Implementasi

- Web responsive menjadi target utama untuk desktop dan mobile browser.
- HTTPS wajib untuk akses GPS dan kamera.
- Semua fitur administratif dibangun sebagai Filament Resource.
- Fitur check-in/check-out dibangun sebagai Custom Page + Livewire Component.
- Business logic utama dipisahkan ke Service class.
- Semua perubahan penting tercatat di audit log.
- Point hanya bertambah/berkurang melalui transaksi immutable.

---

## 3. Fase Implementasi

### Fase 0 — Project Setup

**Tujuan:** Menyiapkan fondasi aplikasi.

**Task:**
- Install project dari FilaStarter.
- Konfigurasi environment.
- Setup database PostgreSQL/MySQL.
- Jalankan migration awal dari boilerplate.
- Setup Filament Breezy, Shield, dan Logger.
- Setup storage public untuk upload dokumen dan foto.
- Setup role awal:
  - Super Admin
  - Admin HR
  - Manajer
  - Karyawan

**Output:**
- Aplikasi Laravel + Filament berjalan lokal.
- Login dan RBAC dasar aktif.
- Storage upload siap.

---

### Fase 1 — Database & Model Foundation

**Tujuan:** Membuat struktur data utama sesuai PRD.

**Task:**
- Buat migration dan model:
  - `User`
  - `SuratTugas`
  - `AttendanceLog`
  - `MeritTransaction`
  - `RewardCatalog`
  - `RewardRequest`
  - `Training`
  - `TrainingEnrollment`
- Tambahkan relasi Eloquent antar model.
- Tambahkan UUID primary key sesuai konfigurasi FilaStarter.
- Tambahkan `manager_id` pada `users`.
- Tambahkan seeder role, permission, user demo, dan reward awal.
- Tambahkan constraint penting:
  - Unique attendance per ST per tanggal.
  - Unique enrollment per user per training.
  - Required `document_url` untuk Surat Tugas.
  - Required `reason` untuk Reward Request.

**Output:**
- Database schema lengkap.
- Model dan relasi siap digunakan.
- Data awal tersedia untuk testing.

---

### Fase 2 — RBAC, Policy, dan Navigation

**Tujuan:** Mengunci akses per role.

**Task:**
- Generate permission via Filament Shield.
- Buat atau sesuaikan Policy untuk:
  - `SuratTugasPolicy`
  - `AttendanceLogPolicy`
  - `MeritTransactionPolicy`
  - `RewardCatalogPolicy`
  - `RewardRequestPolicy`
  - `TrainingPolicy`
  - `TrainingEnrollmentPolicy`
- Atur akses:
  - Super Admin: semua akses.
  - Admin HR: kelola ST, reward catalog, training, enrollment completion, semua laporan.
  - Manajer: buat ST untuk bawahan, approve absensi, approve reward.
  - Karyawan: lihat data sendiri, check-in/out, ajukan reward, daftar pelatihan.
- Sembunyikan menu yang tidak relevan per role.

**Output:**
- Permission granular aktif.
- Data antar user tidak bocor.
- Navigation sesuai role.

---

### Fase 3 — Modul Surat Tugas

**Tujuan:** Membuat prasyarat absensi dinas luar.

**Task:**
- Buat `SuratTugasResource`.
- Form fields:
  - Karyawan
  - Tanggal mulai
  - Tanggal selesai
  - Nama lokasi
  - Latitude
  - Longitude
  - Radius meter default 300
  - Upload dokumen PDF wajib
  - Status
- Validasi:
  - `end_date >= start_date`
  - `start_date >= today`, kecuali Super Admin
  - dokumen wajib PDF
  - hanya HR/Manajer boleh membuat ST
  - Manajer hanya boleh membuat ST untuk bawahan
- Table fields:
  - Karyawan
  - Lokasi
  - Periode
  - Radius
  - Status
  - Pembuat
- Batasi edit/delete jika sudah ada `AttendanceLog`.

**Output:**
- Surat Tugas bisa dibuat, dilihat, diedit, dan dibatalkan sesuai aturan.
- ST aktif muncul untuk karyawan sesuai periode.

---

### Fase 4 — Modul Absen Dinas Luar

**Tujuan:** Membangun check-in/check-out harian berbasis ST, GPS, dan foto.

**Task:**
- Buat Custom Page `AttendancePage` untuk Karyawan.
- Tampilkan ST aktif hari ini.
- Implementasi check-in:
  - Ambil GPS browser via Geolocation API.
  - Ambil foto via Camera API.
  - Hitung jarak dengan Haversine.
  - Set `location_status`:
    - `VALID` jika dalam radius.
    - `OUT_OF_RANGE` jika di luar radius.
  - Simpan `AttendanceLog` status approval `PENDING`.
  - Cegah duplikasi check-in pada ST dan tanggal yang sama.
- Implementasi check-out:
  - Wajib sudah check-in hari sama.
  - Wajib minimal 7 jam setelah check-in.
  - Ambil GPS check-out.
  - Ambil foto check-out.
  - Simpan waktu, koordinat, dan foto check-out.
- Buat riwayat absensi untuk karyawan.
- Tambahkan pesan error untuk:
  - Tidak ada ST aktif.
  - GPS ditolak.
  - Kamera ditolak.
  - Check-in duplikat.
  - Check-out sebelum 7 jam.

**Output:**
- Karyawan bisa check-in/out harian dari web mobile.
- Foto dan GPS tersimpan.
- Status luar radius tetap masuk sebagai `OUT_OF_RANGE` dan menunggu verifikasi Manajer.

---

### Fase 5 — Approval Absensi & Merit Bonus

**Tujuan:** Menghubungkan approval absensi dengan penambahan point.

**Task:**
- Buat `AttendanceLogResource` untuk HR/Manajer.
- Buat dashboard/widget pending approval untuk Manajer.
- Filter data:
  - Manajer hanya melihat absensi bawahan.
  - HR/Super Admin bisa melihat semua.
- Action approve:
  - Ubah `approval_status` menjadi `APPROVED`.
  - Isi `approved_by`.
  - Tambah +10 point via `MeritService`.
  - Cegah double point jika sudah pernah approved.
- Action reject:
  - Wajib isi alasan.
  - Ubah `approval_status` menjadi `REJECTED`.
  - Simpan `rejection_reason`.
  - Tidak menambah point.
- Bulk approve untuk beberapa record.

**Output:**
- Approval absensi berjalan per hari atau bulk.
- Point otomatis bertambah setelah approval.
- Audit trail tercatat.

---

### Fase 6 — Merit Transaction & Point Balance

**Tujuan:** Membangun ledger point yang akurat dan immutable.

**Task:**
- Buat `MeritService`:
  - `addPoints()`
  - `deductPoints()`
  - `getBalance()`
  - `processAttendanceApproval()`
  - `processTrainingCompletion()`
  - `processRewardApproval()`
- Buat `MeritTransactionResource` view-only untuk histori.
- Dashboard Karyawan:
  - Total point aktif.
  - Point masuk.
  - Point keluar.
  - Point mendekati kadaluarsa.
- Implementasi expiry:
  - `expiry_date = now()->addMonths(12)` untuk point masuk.
  - Scheduler `merit:expire` bulanan.
  - Exclude `is_expired = true` dari saldo.
- Pastikan transaksi tidak diedit manual oleh user biasa.

**Output:**
- Saldo point akurat.
- Histori transaksi tidak berubah.
- Masa berlaku 12 bulan terkelola.

---

### Fase 7 — Reward Catalog & Reward Request

**Tujuan:** Membuat flow penukaran point end-to-end.

**Task:**
- Buat `RewardCatalogResource` untuk Admin HR.
- Seed reward awal:
  - Voucher Belanja — 50pt
  - Bonus Tunai — 100pt
  - Cuti Tambahan 1 Hari — 75pt
  - Merchandise — 25pt
  - Training Premium — 200pt
- Buat `RewardRequestResource`.
- Form pengajuan karyawan:
  - Pilih reward aktif.
  - Isi alasan pengajuan.
  - Validasi saldo cukup.
  - Simpan status `PENDING`.
  - Point belum dikurangi saat submit.
- Approval Manajer:
  - Approve: status `APPROVED`, point dikurangi via `MeritService::deductPoints()`.
  - Reject: status `REJECTED`, alasan penolakan wajib, point tetap.
- Cegah approve jika saldo sudah tidak cukup saat approval.

**Output:**
- Karyawan bisa mengajukan reward.
- Manajer bisa approve/reject.
- Point berkurang hanya saat approved.

---

### Fase 8 — Pembinaan Karir & Training

**Tujuan:** Membuat rekomendasi dan pendaftaran pelatihan.

**Task:**
- Buat `TrainingResource` untuk Admin HR.
- Fields:
  - Judul
  - Deskripsi
  - Minimal point
  - Durasi jam
  - Status aktif
- Buat `TrainingEnrollmentResource`.
- Karyawan bisa daftar training jika memenuhi minimal point.
- Buat `TrainingRecommendationService`:
  - Hitung total point positif dalam 3 bulan terakhir.
  - Jika >= 100, tampilkan training eligible.
- Dashboard Karyawan:
  - Section rekomendasi pelatihan.
  - Daftar training tersedia.
- Admin HR bisa tandai enrollment sebagai `COMPLETED`.
- Saat completed:
  - Tambah +25 point via `MeritService`.
  - Simpan `completed_at`.
  - Cegah double point jika sudah completed sebelumnya.

**Output:**
- Training bisa dikelola.
- Karyawan bisa daftar.
- Rekomendasi muncul berdasarkan point.
- Completion training menambah point otomatis.

---

### Fase 9 — Dashboard per Role

**Tujuan:** Menyediakan ringkasan kerja sesuai kebutuhan tiap role.

**Task:**
- Dashboard Karyawan:
  - Total point.
  - ST aktif hari ini.
  - Tombol check-in/check-out.
  - Rekomendasi pelatihan.
  - Riwayat point terbaru.
- Dashboard Manajer:
  - Jumlah pending approval absensi.
  - Daftar pending approval absensi.
  - Daftar pending reward request.
  - Tim saya.
  - Shortcut buat ST.
- Dashboard Admin HR:
  - Total karyawan.
  - Total ST aktif.
  - Total point digunakan.
  - Top 5 karyawan point tertinggi.
  - Tren point bulanan.
- Dashboard Super Admin:
  - Akses penuh ke dashboard dan semua resource.

**Output:**
- User masuk ke dashboard sesuai role.
- Data ringkas tersedia tanpa membuka resource satu per satu.

---

### Fase 10 — Notification, Audit Log, dan Hardening

**Tujuan:** Meningkatkan feedback, auditability, dan keamanan.

**Task:**
- Tambahkan Filament Notification untuk:
  - Check-in berhasil/gagal.
  - Check-out berhasil/gagal.
  - Absensi approved/rejected.
  - Reward approved/rejected.
  - Training completed.
- Aktifkan activity log untuk:
  - CRUD Surat Tugas.
  - Check-in/out.
  - Approval absensi.
  - Point transaction.
  - Reward request.
  - Training enrollment.
- Validasi file upload:
  - PDF untuk ST.
  - Image untuk foto absensi.
  - Batas ukuran file.
- Pastikan semua query resource memakai eager loading.
- Pastikan semua table memakai pagination.
- Pastikan authorization dicek di action dan query scope.

**Output:**
- Sistem lebih aman, mudah diaudit, dan stabil.

---

### Fase 11 — Testing & UAT Preparation

**Tujuan:** Memastikan seluruh acceptance criteria lulus.

**Test Case Wajib:**
- Karyawan login dan hanya melihat data sendiri.
- Admin HR membuat ST dengan dokumen PDF wajib.
- ST aktif muncul di dashboard karyawan.
- Karyawan check-in dalam radius.
- Karyawan check-in luar radius tetap tersimpan sebagai `OUT_OF_RANGE` dan `PENDING`.
- Karyawan tidak bisa check-in tanpa ST aktif.
- Karyawan tidak bisa check-in dua kali pada ST dan tanggal yang sama.
- Karyawan tidak bisa check-out sebelum 7 jam.
- Karyawan bisa check-out setelah 7 jam dengan GPS dan foto.
- Manajer approve absensi dan point +10 bertambah.
- Manajer reject absensi dan point tidak bertambah.
- Karyawan mengajukan reward dengan alasan.
- Reward request tidak langsung mengurangi point.
- Manajer approve reward dan point berkurang.
- Manajer reject reward dan point tetap.
- Admin HR menandai training selesai dan point +25 bertambah.
- Point kadaluarsa tidak dihitung ke saldo aktif.
- Super Admin bisa mengakses semua resource.

**Output:**
- Semua skenario MVP pass.
- Aplikasi siap UAT pilot.

---

## 4. Urutan Prioritas Development

1. Setup project dan role.
2. Database schema dan model.
3. Surat Tugas.
4. Attendance check-in/check-out.
5. Approval absensi.
6. Merit transaction dan point balance.
7. Reward catalog dan reward request.
8. Training dan recommendation.
9. Dashboard per role.
10. Notification, audit log, hardening.
11. Testing dan UAT.

---

## 5. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Browser menolak GPS/kamera | Absensi gagal | Tampilkan instruksi permission dan wajib HTTPS |
| Upload foto lambat | Check-in/out > 3 detik | Batasi ukuran foto, compress client-side/server-side |
| Data point double karena approve berulang | Saldo tidak akurat | Guard approval idempotent dan cek transaksi existing |
| Manajer melihat data bukan bawahan | Kebocoran data | Query scope dan Policy wajib |
| Saldo berubah saat reward approval | Approval gagal atau saldo minus | Validasi saldo ulang saat approve |
| Training completion double point | Point ganda | Cek status sebelum complete dan cek transaksi reference |
| ST diubah setelah absensi dibuat | Audit kacau | Lock edit/delete jika sudah ada AttendanceLog |

---

## 6. Definition of Done MVP

MVP dianggap selesai jika:

- Semua role bisa login dan melihat dashboard sesuai akses.
- Admin HR/Manajer bisa membuat Surat Tugas dengan PDF wajib.
- Karyawan bisa check-in dan check-out via web mobile dengan GPS dan foto.
- Check-out wajib minimal 7 jam setelah check-in.
- Absensi luar radius tersimpan sebagai `OUT_OF_RANGE` dan tetap bisa diverifikasi.
- Manajer bisa approve/reject absensi.
- Approval absensi menambah +10 point otomatis.
- Karyawan bisa melihat saldo dan histori point.
- Karyawan bisa mengajukan reward dengan alasan.
- Approval reward mengurangi point otomatis.
- Admin HR bisa mengelola training dan menandai selesai.
- Training selesai menambah +25 point otomatis.
- Rekomendasi training muncul jika point 3 bulan terakhir >= 100.
- Audit log aktif untuk aktivitas penting.
- Semua test case wajib pass.
