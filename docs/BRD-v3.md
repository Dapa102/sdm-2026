# BUSINESS REQUIREMENTS DOCUMENT (BRD)
## SISTEM SUMBER DAYA MANUSIA TERINTEGRASI
### Versi 3.0 - Arsitektur 8 Layanan Ideal

---

| **Dokumen** | **Keterangan** |
|---|---|
| Nama Dokumen | Business Requirements Document - Sistem SDM Terintegrasi |
| Versi | 3.0 |
| Tanggal | 18 Juni 2026 |
| Status | Draft Pengembangan Ruang Lingkup Ideal |
| Acuan | BRD v2.0 dan PRD v2.0 |
| Pemilik Proyek | HR Director |
| Platform Target | Web Responsive |

---

## 1. RINGKASAN EKSEKUTIF

BRD versi 3.0 ini memperluas ruang lingkup Sistem SDM Terintegrasi dari 3 layanan MVP menjadi 8 layanan ideal yang lebih lengkap untuk kebutuhan operasional HR jangka menengah dan panjang.

Pada BRD v2.0, fokus utama sistem adalah:

1. Absen Dinas Luar
2. Sistem Merit
3. Pembinaan Karir

Pada BRD v3.0, sistem dikembangkan menjadi platform SDM terpadu yang juga mencakup data karyawan, absensi umum, penggajian, benefit, workflow approval, notifikasi, audit, dan pelaporan. Meski ruang lingkup diperluas, rekomendasi implementasi tetap menggunakan pendekatan **modular monolith** di Laravel, bukan microservice terpisah pada fase awal.

---

## 2. TUJUAN BISNIS

| **No** | **Tujuan** | **Dampak Bisnis** |
|---|---|---|
| 1 | Menyatukan data karyawan dalam satu sumber kebenaran | Mengurangi duplikasi data dan kesalahan administrasi |
| 2 | Mendigitalisasi absensi, dinas luar, cuti, dan approval | Proses HR lebih cepat, transparan, dan mudah diaudit |
| 3 | Menjadikan attendance dan merit sebagai input payroll dan reward | Perhitungan kompensasi lebih objektif |
| 4 | Menyediakan layanan payroll yang terstruktur dan aman | Slip gaji, potongan, tunjangan, dan komponen payroll dapat ditelusuri |
| 5 | Menghubungkan merit, reward, dan training ke pengembangan karir | Karyawan mendapat jalur pengembangan yang lebih jelas |
| 6 | Menyediakan reporting HR lintas layanan | Manajemen dapat mengambil keputusan berbasis data |

---

## 3. PRINSIP ARSITEKTUR LAYANAN

Sistem akan dibagi menjadi 8 layanan domain. Dalam konteks implementasi Laravel, "layanan" tidak berarti harus menjadi aplikasi/microservice terpisah. Untuk fase awal dan menengah, layanan direpresentasikan sebagai modul domain di dalam satu aplikasi:

- Models dan migrations per domain.
- Services untuk business logic.
- Filament Resources untuk administrasi.
- Policies untuk otorisasi.
- Events/Listeners untuk integrasi antar domain.
- Jobs/Scheduler untuk proses periodik.

Pendekatan ini dipilih karena:

- Lebih sederhana untuk tim kecil/menengah.
- Lebih cepat untuk MVP dan iterasi.
- Lebih mudah menjaga transaksi database antar domain.
- Tetap bisa dipisah menjadi microservice di masa depan jika beban dan kompleksitas sudah cukup besar.

---

## 4. DAFTAR 8 LAYANAN IDEAL

| **No** | **Layanan** | **Fungsi Utama** | **Prioritas** |
|---|---|---|---|
| 1 | Employee & Organization Service | Data karyawan, struktur organisasi, jabatan, divisi, relasi manager | MVP Foundation |
| 2 | Attendance & Leave Service | Absensi harian, cuti, izin, sakit, lembur, riwayat kehadiran | Fase 2 |
| 3 | Travel Duty Service | Surat Tugas, dinas luar, lokasi tujuan, check-in/out dinas | MVP |
| 4 | Payroll & Compensation Service | Gaji, tunjangan, potongan, slip gaji, periode payroll | Fase 2/Fase 3 |
| 5 | Merit & Performance Service | Point merit, performa, bonus berbasis approval, histori point | MVP |
| 6 | Rewards & Benefits Service | Katalog reward, pengajuan reward, benefit karyawan | MVP |
| 7 | Learning & Career Service | Pelatihan, enrollment, rekomendasi training, pembinaan karir | MVP |
| 8 | Workflow, Notification & Reporting Service | Approval lintas modul, notifikasi, audit trail, dashboard, export laporan | MVP Foundation + Fase 2 |

---

## 5. DETAIL KEBUTUHAN PER LAYANAN

### 5.1 Employee & Organization Service

**Tujuan:** Menjadi master data utama untuk semua layanan SDM.

| **ID** | **Kebutuhan** | **Prioritas** | **Deskripsi** |
|---|---|---|---|
| EO-01 | Data Karyawan | MVP Foundation | Menyimpan nama, email, NIP, status kerja, tanggal masuk, dan data dasar karyawan. |
| EO-02 | Struktur Organisasi | MVP Foundation | Menyimpan divisi/departemen, jabatan, dan relasi atasan langsung. |
| EO-03 | Role & Permission | MVP Foundation | Mengatur akses Super Admin, Admin HR, Manajer, dan Karyawan. |
| EO-04 | Riwayat Perubahan Jabatan | Fase 2 | Mencatat mutasi, promosi, demosi, dan perpindahan divisi. |
| EO-05 | Dokumen Karyawan | Fase 2 | Menyimpan kontrak kerja, KTP, NPWP, sertifikat, dan dokumen administrasi lain. |

**Catatan Integrasi:**

- Attendance, Payroll, Merit, Reward, dan Training wajib mengambil data user dari layanan ini.
- Perubahan manager berdampak pada workflow approval bawahan.

---

### 5.2 Attendance & Leave Service

**Tujuan:** Mengelola kehadiran umum karyawan, cuti, izin, sakit, dan lembur.

| **ID** | **Kebutuhan** | **Prioritas** | **Deskripsi** |
|---|---|---|---|
| AL-01 | Absensi Kantor | Fase 2 | Check-in/out untuk kehadiran reguler di kantor atau cabang. |
| AL-02 | Pengajuan Cuti | Fase 2 | Karyawan mengajukan cuti, manager menyetujui atau menolak. |
| AL-03 | Izin dan Sakit | Fase 2 | Karyawan mengajukan izin/sakit dengan dokumen pendukung. |
| AL-04 | Lembur | Fase 2 | Pengajuan dan approval lembur sebagai input payroll. |
| AL-05 | Rekap Kehadiran | Fase 2 | Laporan bulanan absensi, cuti, izin, sakit, dan lembur. |

**Catatan Integrasi:**

- Data absensi, cuti, dan lembur menjadi input Payroll.
- Absensi dinas luar tetap dikelola oleh Travel Duty Service, tetapi hasil akhirnya masuk ke rekap kehadiran.

---

### 5.3 Travel Duty Service

**Tujuan:** Mengelola Surat Tugas dan absensi khusus dinas luar.

| **ID** | **Kebutuhan** | **Prioritas** | **Deskripsi** |
|---|---|---|---|
| TD-01 | Manajemen Surat Tugas | MVP | HR/Manajer membuat ST berisi karyawan, tanggal, lokasi tujuan, koordinat, radius, dan dokumen PDF. |
| TD-02 | Check-in Dinas Luar | MVP | Karyawan check-in harian berdasarkan ST aktif, GPS, dan foto bukti. |
| TD-03 | Check-out Dinas Luar | MVP | Karyawan check-out minimal 7 jam setelah check-in dengan GPS dan foto. |
| TD-04 | Validasi Radius Tujuan | MVP | Sistem membandingkan lokasi aktual dengan koordinat tujuan ST. |
| TD-05 | Approval Absensi Dinas | MVP | Manager menyetujui atau menolak absensi dinas bawahan. |
| TD-06 | Rekap Dinas Luar | Fase 2 | Export laporan dinas luar untuk HR, reimbursement, dan audit. |

**Catatan Integrasi:**

- Approval absensi dinas yang sah menambahkan point ke Merit & Performance Service.
- Data dinas luar dapat menjadi input Payroll jika perusahaan menerapkan uang dinas, reimbursement, atau tunjangan perjalanan.

---

### 5.4 Payroll & Compensation Service

**Tujuan:** Mengelola proses penggajian, tunjangan, potongan, slip gaji, dan periode payroll.

| **ID** | **Kebutuhan** | **Prioritas** | **Deskripsi** |
|---|---|---|---|
| PC-01 | Master Komponen Gaji | Fase 2 | Mendefinisikan gaji pokok, tunjangan tetap, tunjangan tidak tetap, potongan, bonus, dan koreksi. |
| PC-02 | Data Kompensasi Karyawan | Fase 2 | Menyimpan komponen gaji per karyawan berdasarkan kontrak atau jabatan. |
| PC-03 | Periode Payroll | Fase 2 | Membuat periode payroll bulanan dengan status draft, review, approved, paid, dan locked. |
| PC-04 | Perhitungan Payroll | Fase 2/Fase 3 | Menghitung gaji berdasarkan komponen tetap, absensi, cuti, lembur, potongan, dan adjustment. |
| PC-05 | Slip Gaji | Fase 2/Fase 3 | Karyawan dapat melihat dan mengunduh slip gaji setelah payroll disetujui. |
| PC-06 | Payroll Approval | Fase 3 | HR Payroll menyiapkan, Finance/Management menyetujui, lalu payroll dikunci. |
| PC-07 | Export Payroll | Fase 3 | Export payroll untuk bank transfer, akuntansi, atau sistem keuangan. |

**Aturan Penting Payroll:**

- Payroll tidak boleh langsung mengubah data attendance, merit, atau employee.
- Payroll hanya membaca snapshot/input dari domain lain pada saat proses payroll dijalankan.
- Periode payroll yang sudah `locked` tidak boleh berubah kecuali melalui adjustment resmi di periode berikutnya.
- Semua perubahan payroll wajib tercatat di audit log.

**Catatan Integrasi:**

- Attendance & Leave menyediakan absensi, cuti, izin, sakit, dan lembur.
- Travel Duty menyediakan data dinas luar, uang dinas, atau reimbursement jika diperlukan.
- Rewards & Benefits dapat menyediakan benefit tertentu yang memengaruhi payroll.
- Merit & Performance dapat menjadi input bonus, tetapi hanya jika ada aturan kompensasi resmi.

---

### 5.5 Merit & Performance Service

**Tujuan:** Mengelola point merit, histori transaksi point, dan indikator performa sederhana.

| **ID** | **Kebutuhan** | **Prioritas** | **Deskripsi** |
|---|---|---|---|
| MP-01 | Point Otomatis dari Dinas | MVP | Absensi dinas yang disetujui manager menambahkan +10 point. |
| MP-02 | Point dari Training | MVP | Training yang selesai menambahkan +25 point. |
| MP-03 | Histori Point | MVP | Karyawan melihat riwayat point masuk, keluar, dan expired. |
| MP-04 | Masa Berlaku Point | MVP | Point berlaku 12 bulan sejak diperoleh. |
| MP-05 | Adjustment Point | Fase 2 | Admin HR dapat menambah/mengurangi point dengan alasan dan approval. |
| MP-06 | Indikator Performance | Fase 3 | Menggabungkan kehadiran, dinas, training, dan penilaian manager. |

**Catatan Integrasi:**

- Travel Duty dan Learning & Career dapat menghasilkan point.
- Rewards & Benefits mengurangi point saat reward disetujui.
- Payroll boleh membaca data performance untuk bonus jika kebijakan perusahaan mengizinkan.

---

### 5.6 Rewards & Benefits Service

**Tujuan:** Mengelola reward berbasis point dan benefit karyawan.

| **ID** | **Kebutuhan** | **Prioritas** | **Deskripsi** |
|---|---|---|---|
| RB-01 | Katalog Reward | MVP | Admin HR mengelola reward, harga point, status aktif, dan stok jika diperlukan. |
| RB-02 | Pengajuan Reward | MVP | Karyawan memilih reward dan mengajukan penukaran point. |
| RB-03 | Approval Reward | MVP | Manager menyetujui atau menolak pengajuan reward bawahan. |
| RB-04 | Deduct Point | MVP | Jika reward disetujui, sistem mengurangi point karyawan. |
| RB-05 | Benefit Karyawan | Fase 2 | Menyimpan benefit seperti asuransi, fasilitas kerja, dan tunjangan non-payroll. |
| RB-06 | Fulfillment Reward | Fase 2 | HR menandai reward sudah diproses, dikirim, atau selesai. |

**Catatan Integrasi:**

- Membaca saldo dari Merit & Performance Service.
- Dapat mengirim benefit tertentu ke Payroll & Compensation jika benefit bersifat tunai atau potongan.

---

### 5.7 Learning & Career Service

**Tujuan:** Mengelola pelatihan, rekomendasi pembinaan, enrollment, dan perkembangan karir.

| **ID** | **Kebutuhan** | **Prioritas** | **Deskripsi** |
|---|---|---|---|
| LC-01 | Katalog Pelatihan | MVP | Admin HR mengelola daftar training, durasi, deskripsi, dan minimum point. |
| LC-02 | Rekomendasi Training | MVP | Sistem merekomendasikan training berdasarkan point dan intensitas dinas. |
| LC-03 | Pendaftaran Training | MVP | Karyawan mendaftar training yang tersedia atau direkomendasikan. |
| LC-04 | Penyelesaian Training | MVP | HR menandai peserta selesai dan sistem memberi point. |
| LC-05 | Riwayat Pembelajaran | Fase 2 | Karyawan melihat training yang pernah diikuti dan status sertifikasi. |
| LC-06 | Jalur Karir | Fase 3 | Sistem menampilkan kompetensi dan rekomendasi jalur karir. |

**Catatan Integrasi:**

- Training selesai menambahkan point ke Merit & Performance.
- Riwayat training dapat menjadi input Performance dan Career Path.

---

### 5.8 Workflow, Notification & Reporting Service

**Tujuan:** Menyediakan approval lintas domain, notifikasi, audit trail, dashboard, dan laporan.

| **ID** | **Kebutuhan** | **Prioritas** | **Deskripsi** |
|---|---|---|---|
| WNR-01 | Approval Workflow | MVP Foundation | Mendukung approval absensi dinas, reward, dan proses HR lain berdasarkan atasan langsung. |
| WNR-02 | Notifikasi Internal | Fase 2 | Mengirim notifikasi untuk approval pending, check-in reminder, reward status, dan training status. |
| WNR-03 | Audit Trail | MVP Foundation | Semua perubahan penting dicatat dengan user, waktu, event, dan data terkait. |
| WNR-04 | Dashboard Per Role | MVP | Dashboard berbeda untuk Karyawan, Manajer, Admin HR, dan Super Admin. |
| WNR-05 | Export Laporan | Fase 2 | Export Excel/PDF untuk absensi, dinas, point, reward, training, dan payroll. |
| WNR-06 | Report Builder Sederhana | Fase 3 | Admin HR dapat membuat laporan dengan filter tanpa coding. |

**Catatan Integrasi:**

- Semua layanan memakai workflow dan audit yang konsisten.
- Notifikasi tidak boleh menjadi syarat transaksi utama. Jika notifikasi gagal, transaksi utama tetap valid dan dapat di-retry.

---

## 6. INTEGRASI ANTAR LAYANAN

| **Alur** | **Sumber** | **Tujuan** | **Deskripsi** |
|---|---|---|---|
| Data karyawan | Employee & Organization | Semua layanan | Semua domain memakai data karyawan yang sama. |
| Absensi dinas approved | Travel Duty | Merit & Performance | Menambahkan point dinas. |
| Training completed | Learning & Career | Merit & Performance | Menambahkan point penyelesaian training. |
| Reward approved | Rewards & Benefits | Merit & Performance | Mengurangi point sesuai harga reward. |
| Attendance summary | Attendance & Leave | Payroll & Compensation | Menjadi input potongan, lembur, atau tunjangan. |
| Travel duty summary | Travel Duty | Payroll & Compensation | Menjadi input uang dinas/reimbursement jika kebijakan berlaku. |
| Performance summary | Merit & Performance | Payroll & Compensation | Dapat menjadi input bonus jika disetujui perusahaan. |
| Approval request | Semua layanan | Workflow, Notification & Reporting | Mencatat approval, notifikasi, dan audit. |

---

## 7. RUANG LINGKUP BERDASARKAN FASE

### 7.1 MVP

Fokus MVP tetap mengikuti BRD v2.0, dengan tambahan fondasi data karyawan dan workflow.

| **Layanan** | **Fitur MVP** |
|---|---|
| Employee & Organization | User, role, manager, data dasar karyawan |
| Travel Duty | Surat Tugas, check-in/out GPS, foto, approval |
| Merit & Performance | Point otomatis, histori point, expiry sederhana |
| Rewards & Benefits | Katalog reward, pengajuan, approval, deduct point |
| Learning & Career | Katalog training, rekomendasi, enrollment, completion point |
| Workflow, Notification & Reporting | Approval dasar, audit trail, dashboard per role |

### 7.2 Fase 2

| **Layanan** | **Fitur Fase 2** |
|---|---|
| Attendance & Leave | Absensi kantor, cuti, izin, sakit, lembur |
| Payroll & Compensation | Master komponen gaji, data kompensasi, periode payroll draft |
| Rewards & Benefits | Benefit karyawan dan fulfillment reward |
| Workflow, Notification & Reporting | Notifikasi, export Excel/PDF, laporan lintas modul |

### 7.3 Fase 3

| **Layanan** | **Fitur Fase 3** |
|---|---|
| Payroll & Compensation | Perhitungan payroll penuh, slip gaji, payroll approval, export bank |
| Merit & Performance | Indikator performance lebih lengkap |
| Learning & Career | Jalur karir dan kompetensi |
| Workflow, Notification & Reporting | Report builder sederhana dan dashboard manajemen |

---

## 8. STAKEHOLDER

| **Peran** | **Tanggung Jawab** |
|---|---|
| Karyawan | Menggunakan absensi, melihat saldo point, mengajukan reward, mendaftar training, melihat slip gaji jika payroll aktif. |
| Manajer | Membuat/menyetujui Surat Tugas, menyetujui absensi, reward, cuti, lembur, dan memantau tim. |
| Admin HR | Mengelola master karyawan, ST, training, reward, benefit, laporan HR, dan konfigurasi layanan. |
| HR Payroll | Mengelola komponen gaji, periode payroll, validasi input payroll, dan slip gaji. |
| Finance/Management | Menyetujui payroll final dan melihat laporan biaya SDM. |
| Super Admin | Mengelola konfigurasi sistem, role, permission, dan audit teknis. |

---

## 9. ROLE & PERMISSION AWAL

| **Role** | **Akses Utama** |
|---|---|
| Super Admin | Semua akses sistem, konfigurasi, role, permission, audit. |
| Admin HR | Data karyawan, ST, training, reward, benefit, laporan HR. |
| HR Payroll | Payroll, komponen gaji, periode payroll, slip gaji, export payroll. |
| Manajer | Data bawahan, approval, Surat Tugas bawahan, monitoring tim. |
| Karyawan | Data pribadi, absensi, dinas aktif, point, reward, training, slip gaji pribadi. |

---

## 10. KEBUTUHAN NON-FUNGSIONAL

| **Kategori** | **Kebutuhan** | **Spesifikasi** |
|---|---|---|
| Platform | Web responsive | Desktop dan mobile browser. |
| Keamanan | Authentication | Email dan password pada MVP, MFA dapat ditambahkan fase berikutnya. |
| Otorisasi | RBAC | Role dan permission granular per layanan. |
| Audit | Activity log | Semua transaksi penting harus tercatat. |
| Performa | Response time | Operasi utama ditargetkan kurang dari 3 detik, kecuali upload file/foto. |
| Data Privacy | Payroll dan dokumen karyawan | Akses dibatasi ketat, tidak boleh terlihat oleh role yang tidak berwenang. |
| Integritas Data | Payroll locked period | Periode payroll yang dikunci tidak boleh berubah langsung. |
| Storage | File dan foto | Menggunakan storage public/private sesuai sensitivitas dokumen. |

---

## 11. ATURAN BISNIS UTAMA

| **ID** | **Aturan** |
|---|---|
| BR-01 | Data karyawan adalah master data utama untuk seluruh layanan. |
| BR-02 | Karyawan tidak dapat melakukan check-in dinas luar tanpa Surat Tugas aktif. |
| BR-03 | Absensi dinas luar wajib check-in dan check-out setiap hari selama periode ST. |
| BR-04 | Point dinas hanya diberikan setelah absensi dinas disetujui manager. |
| BR-05 | Point training hanya diberikan setelah HR menandai training selesai. |
| BR-06 | Point reward hanya dikurangi setelah pengajuan reward disetujui. |
| BR-07 | Payroll membaca data dari attendance, leave, travel duty, dan compensation sebagai input, tetapi tidak mengubah data sumber. |
| BR-08 | Payroll yang sudah dikunci tidak dapat diedit langsung. Koreksi dilakukan melalui adjustment resmi. |
| BR-09 | Semua approval harus mencatat approver, waktu approval, status, dan alasan jika ditolak. |
| BR-10 | Semua data sensitif seperti slip gaji, dokumen karyawan, dan komponen gaji wajib dibatasi berdasarkan role. |

---

## 12. REKOMENDASI STRUKTUR MODUL TEKNIS

Untuk implementasi Laravel, struktur service yang direkomendasikan:

```txt
app/Services/
├── EmployeeService.php
├── AttendanceService.php
├── TravelDutyService.php
├── PayrollService.php
├── MeritService.php
├── RewardService.php
├── LearningCareerService.php
└── WorkflowService.php
```

Jika ingin lebih granular pada fase berikutnya, beberapa service dapat dipecah:

```txt
PayrollCalculationService
PayrollPeriodService
LeaveService
NotificationService
ReportService
TrainingRecommendationService
```

Namun untuk awal pengembangan, 8 service domain di atas sudah cukup ideal dan tidak terlalu berat.

---

## 13. KRITERIA KEBERHASILAN

| **No** | **Kriteria** | **Target** |
|---|---|---|
| 1 | Data karyawan menjadi sumber utama semua layanan | Tidak ada duplikasi data user antar modul |
| 2 | Dinas luar berjalan end-to-end | ST -> check-in/out -> approval -> point berhasil |
| 3 | Reward berjalan end-to-end | Saldo point -> pengajuan -> approval -> deduct point berhasil |
| 4 | Training berjalan end-to-end | Rekomendasi/enrollment -> completion -> point berhasil |
| 5 | Payroll siap menerima input dari modul lain | Attendance/travel/benefit dapat menjadi input payroll |
| 6 | Semua transaksi penting memiliki audit trail | 100% event penting tercatat |
| 7 | Role dan permission melindungi data sensitif | Payroll dan dokumen karyawan tidak terlihat oleh role tidak berwenang |

---

## 14. KESIMPULAN

Untuk kebutuhan MVP, 3 layanan utama pada BRD v2.0 masih cukup. Namun untuk sistem SDM yang lebih ideal dan siap berkembang, BRD v3.0 merekomendasikan 8 layanan domain:

1. Employee & Organization Service
2. Attendance & Leave Service
3. Travel Duty Service
4. Payroll & Compensation Service
5. Merit & Performance Service
6. Rewards & Benefits Service
7. Learning & Career Service
8. Workflow, Notification & Reporting Service

Rekomendasi implementasi tetap menggunakan satu aplikasi Laravel modular. Payroll sebaiknya masuk sebagai domain terpisah karena memiliki sensitivitas data, aturan approval, dan kebutuhan audit yang lebih tinggi dibanding modul SDM lain.
