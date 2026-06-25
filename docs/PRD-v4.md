# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## SISTEM SDM UNTUK ABSENSI JARAK JAUH PEGAWAI LAPANGAN BERBASIS WEB

### Versi 4.0 - Fokus Monitoring Kehadiran Pegawai Lapangan

---

| **Dokumen** | **Keterangan** |
| :--- | :--- |
| **Nama Dokumen** | Product Requirements Document - Sistem SDM untuk Absensi Jarak Jauh Pegawai Lapangan |
| **Versi** | 4.0 |
| **Tanggal** | 25 Juni 2026 |
| **Acuan** | BRD v4.0 |
| **Audience** | Product Owner, Tim Engineering, QA, UI/UX, Admin/HR, Supervisor |
| **Platform Target** | Web Responsive, desktop dan mobile browser |
| **Base Stack** | Laravel, Filament, Blade, JavaScript, MariaDB/MySQL |
| **Status** | Draft Teknis untuk Pengembangan MVP |

---

## 1. Pendahuluan

PRD ini menerjemahkan BRD v4.0 menjadi spesifikasi produk dan teknis untuk membangun **Sistem SDM untuk Absensi Jarak Jauh Pegawai Lapangan Berbasis Web**.

Sistem tetap berada dalam domain Sistem Sumber Daya Manusia karena objek utamanya adalah pegawai, supervisor, kehadiran, jadwal kerja, dan laporan absensi. Namun, ruang lingkup MVP tidak mencakup HRIS lengkap. MVP difokuskan pada pencatatan, monitoring, validasi, koreksi, dan pelaporan kehadiran pegawai lapangan.

Sistem dirancang untuk membantu organisasi yang memiliki pegawai di luar kantor utama, seperti cleaning service, security, teknisi lapangan, sales lapangan, surveyor, kurir internal, petugas maintenance, pegawai cabang, atau pegawai yang menjalankan surat tugas.

### 1.1 Tujuan PRD

Tujuan PRD ini adalah:

* Menjelaskan kebutuhan produk secara lebih rinci dari BRD v4.
* Menjadi panduan implementasi untuk tim pengembangan.
* Menjadi dasar penyusunan database, modul, halaman, validasi, dan pengujian.
* Menjaga ruang lingkup agar tetap fokus pada absensi jarak jauh pegawai lapangan.
* Memastikan sistem tetap memiliki unsur SDM tanpa melebar menjadi HRIS penuh.

### 1.2 Definisi Produk

Produk yang dibangun adalah aplikasi web untuk:

* Mengelola data pegawai lapangan.
* Mengelola lokasi kerja.
* Mengelola jadwal kerja atau surat tugas.
* Melakukan absensi masuk dan pulang berbasis waktu, lokasi, dan foto.
* Memvalidasi absensi bermasalah oleh supervisor atau admin.
* Melakukan koreksi absensi dengan catatan alasan.
* Menampilkan rekap dan laporan kehadiran.

### 1.3 Prinsip Produk

| **Prinsip** | **Implikasi Implementasi** |
| :--- | :--- |
| Fokus pada absensi lapangan | Semua fitur MVP harus mendukung pencatatan dan validasi kehadiran pegawai lapangan. |
| Tetap dalam domain SDM | Istilah pegawai, supervisor, Admin/HR, dan manajemen tetap digunakan. |
| Bukan HRIS lengkap | Payroll, rekrutmen, kontrak kerja, reward, merit, dan pembinaan karir tidak menjadi fitur utama MVP. |
| Web responsive | Pegawai harus bisa absen dari smartphone melalui browser. |
| Validasi manusia tetap penting | GPS dan foto menjadi bukti pendukung, tetapi keputusan akhir absensi bermasalah tetap pada supervisor/admin. |
| Data dapat diaudit | Koreksi, approval, dan perubahan penting harus memiliki catatan. |
| Integrasi sederhana | Minimal satu service pihak ketiga digunakan untuk memenuhi kebutuhan arsitektur berbasis layanan. |

---

## 2. Ruang Lingkup Produk

### 2.1 In Scope MVP

| **Modul** | **Fitur MVP** |
| :--- | :--- |
| User Management | Login, logout, role, status akun, pembatasan akses. |
| Employee Management | CRUD pegawai, relasi pegawai dengan user, relasi pegawai dengan supervisor. |
| Work Location Management | CRUD lokasi kerja, alamat, latitude, longitude, radius toleransi, status aktif. |
| Assignment/Schedule Management | Jadwal kerja atau surat tugas, tanggal, jam mulai, jam selesai, lokasi, pegawai, supervisor, status. |
| Employee Attendance | Dashboard pegawai, absen masuk, absen pulang, lokasi, foto, catatan kendala, riwayat pribadi. |
| Attendance Validation | Status kehadiran, status validasi, validasi supervisor/admin, approval, rejection. |
| Attendance Correction | Koreksi manual untuk lupa absen, GPS gagal, lokasi tidak akurat, atau alasan lain yang diterima. |
| Leave/Sick Basic Input | Input izin dan sakit sederhana oleh supervisor atau admin. |
| Supervisor Dashboard | Monitoring bawahan, absensi hari ini, absensi perlu verifikasi, rekap bawahan. |
| Admin/HR Dashboard | Master data, seluruh absensi, koreksi, laporan, konfigurasi integrasi. |
| Management Dashboard | Ringkasan jumlah pegawai, hadir, terlambat, tidak hadir, perlu verifikasi. |
| Reporting | Laporan harian, per pegawai, per lokasi, keterlambatan, izin, sakit, tidak hadir, periode. |
| Third Party Integration | Cloudinary untuk foto, Google Maps untuk peta/lokasi, atau Telegram Bot untuk notifikasi. |

### 2.2 Out of Scope MVP

Fitur berikut tidak dikembangkan dalam MVP:

* Payroll atau perhitungan gaji.
* Rekrutmen pegawai.
* Kontrak kerja.
* Akuntansi perusahaan.
* Merit point sebagai fitur utama.
* Reward sebagai fitur utama.
* Training sebagai fitur utama.
* Pembinaan karir sebagai fitur utama.
* Aplikasi mobile native Android atau iOS.
* Deteksi fake GPS secara penuh.
* Integrasi fingerprint.
* Integrasi CCTV.
* Sanksi otomatis terhadap pegawai.
* Monitoring produktivitas detail di luar absensi.

### 2.3 Target Pengguna

| **Role** | **Deskripsi** | **Kebutuhan Utama** |
| :--- | :--- | :--- |
| Pegawai Lapangan | Pegawai yang bekerja di luar kantor utama | Melihat jadwal, absen masuk, absen pulang, upload foto, lihat riwayat. |
| Supervisor | Atasan yang memantau pegawai lapangan | Melihat absensi bawahan, memvalidasi, menolak, mengoreksi, melihat rekap. |
| Admin/HR | Pengelola data SDM dan absensi | Mengelola master data, jadwal, lokasi, akun, laporan, koreksi. |
| Manajemen/Pemilik | Pengambil keputusan | Melihat ringkasan kehadiran dan laporan periode. |
| Super Admin | Role teknis sistem | Mengelola konfigurasi teknis, semua data, dan akses penuh. |

---

## 3. Role dan Hak Akses

### 3.1 Role Sistem

Role awal yang wajib tersedia:

* `super_admin`
* `admin_hr`
* `supervisor`
* `employee`
* `management`

### 3.2 Matriks Hak Akses

| **Fitur** | **Super Admin** | **Admin/HR** | **Supervisor** | **Pegawai** | **Manajemen** |
| :--- | :---: | :---: | :---: | :---: | :---: |
| Kelola user | Ya | Ya | Tidak | Tidak | Tidak |
| Kelola role | Ya | Tidak | Tidak | Tidak | Tidak |
| Kelola pegawai | Ya | Ya | Lihat bawahan | Lihat diri sendiri | Lihat ringkasan |
| Kelola lokasi kerja | Ya | Ya | Lihat | Lihat terkait jadwal | Lihat laporan |
| Kelola jadwal/surat tugas | Ya | Ya | Ya untuk bawahan | Lihat milik sendiri | Lihat laporan |
| Absen masuk/pulang | Tidak | Tidak | Opsional jika punya data pegawai | Ya | Tidak |
| Lihat absensi | Semua | Semua | Bawahan | Milik sendiri | Ringkasan/laporan |
| Validasi absensi | Ya | Ya | Bawahan | Tidak | Tidak |
| Koreksi absensi | Ya | Ya | Bawahan | Tidak | Tidak |
| Input izin/sakit | Ya | Ya | Bawahan | Tidak | Tidak |
| Laporan | Semua | Semua | Bawahan | Riwayat pribadi | Ringkasan dan periode |
| Konfigurasi integrasi | Ya | Ya | Tidak | Tidak | Tidak |

### 3.3 Aturan Otorisasi

* Pegawai hanya boleh melihat jadwal, absensi, foto, dan riwayat miliknya sendiri.
* Supervisor hanya boleh melihat dan memvalidasi pegawai yang berada di bawah tanggung jawabnya.
* Admin/HR dapat melihat seluruh pegawai, lokasi, jadwal, dan absensi.
* Manajemen hanya melihat ringkasan dan laporan, bukan melakukan koreksi data operasional.
* Super Admin memiliki akses penuh untuk kebutuhan teknis dan pemeliharaan.

---

## 4. Arsitektur Produk

### 4.1 Pendekatan Aplikasi

Sistem menggunakan pendekatan **modular monolith**. Semua modul berjalan dalam satu aplikasi Laravel, tetapi business logic dipisahkan berdasarkan domain agar mudah dipelihara.

Modul utama:

* User Management Module
* Employee Module
* Work Location Module
* Assignment/Schedule Module
* Attendance Module
* Attendance Approval Module
* Attendance Correction Module
* Report Module
* Third Party Integration Module

### 4.2 Struktur Direktori Rekomendasi

```txt
app/
├── Actions/
│   ├── Attendance/
│   │   ├── CheckInEmployee.php
│   │   ├── CheckOutEmployee.php
│   │   ├── ValidateAttendanceLocation.php
│   │   └── CorrectAttendanceRecord.php
│   └── Integration/
│       ├── UploadAttendancePhoto.php
│       └── SendAttendanceNotification.php
├── Filament/
│   └── Admin/
│       ├── Resources/
│       │   ├── UserResource.php
│       │   ├── EmployeeResource.php
│       │   ├── WorkLocationResource.php
│       │   ├── AssignmentResource.php
│       │   ├── AttendanceRecordResource.php
│       │   ├── AttendanceApprovalResource.php
│       │   └── AttendanceCorrectionResource.php
│       ├── Pages/
│       │   ├── EmployeeDashboard.php
│       │   ├── SupervisorDashboard.php
│       │   ├── AdminDashboard.php
│       │   └── ManagementDashboard.php
│       └── Widgets/
│           ├── TodayAttendanceStatsWidget.php
│           ├── PendingVerificationWidget.php
│           └── AttendanceByLocationWidget.php
├── Integrations/
│   ├── Cloudinary/
│   ├── GoogleMaps/
│   └── Telegram/
├── Models/
│   ├── User.php
│   ├── Employee.php
│   ├── WorkLocation.php
│   ├── Assignment.php
│   ├── AttendanceRecord.php
│   ├── AttendanceApproval.php
│   ├── AttendanceCorrection.php
│   └── IntegrationLog.php
├── Policies/
├── Services/
│   ├── AttendanceService.php
│   ├── LocationDistanceService.php
│   ├── ReportService.php
│   └── IntegrationService.php
└── Support/
    └── Enums/
```

### 4.3 Teknologi

| **Lapisan** | **Teknologi** | **Keterangan** |
| :--- | :--- | :--- |
| Backend | Laravel | Routing, auth, model, validation, service, queue, scheduler. |
| Admin Panel | Filament | Resource CRUD, dashboard, table, form, action. |
| Frontend Pegawai | Blade, Livewire, JavaScript | Dashboard mobile responsive, GPS, kamera/upload foto. |
| Database | MariaDB/MySQL | Penyimpanan data utama. |
| Web Server | Nginx | Deployment web. |
| Container | Docker | Development dan deployment environment. |
| Storage Foto | Cloudinary atau local/private storage | Cloudinary direkomendasikan untuk integrasi service pihak ketiga. |
| Peta | Google Maps Platform atau link koordinat | Untuk menampilkan lokasi kerja dan lokasi absensi. |
| Notifikasi | Telegram Bot API | Opsional untuk absensi perlu verifikasi. |

---

## 5. User Journey

### 5.1 Journey Pegawai Lapangan

1. Pegawai login ke sistem.
2. Pegawai membuka dashboard.
3. Sistem menampilkan jadwal atau surat tugas hari ini.
4. Pegawai melihat detail lokasi kerja dan jam kerja.
5. Pegawai menekan tombol **Absen Masuk**.
6. Browser meminta izin lokasi.
7. Sistem mengambil latitude dan longitude jika izin diberikan.
8. Pegawai mengunggah foto absensi masuk.
9. Pegawai dapat menulis catatan jika ada kendala.
10. Sistem menyimpan absensi masuk.
11. Sistem menentukan status kehadiran dan status validasi awal.
12. Saat pekerjaan selesai, pegawai menekan **Absen Pulang**.
13. Sistem mengambil lokasi pulang dan foto jika diperlukan.
14. Sistem menyimpan absensi pulang.
15. Pegawai dapat melihat status dan riwayat absensi.

### 5.2 Journey Supervisor

1. Supervisor login ke sistem.
2. Supervisor membuka dashboard.
3. Sistem menampilkan ringkasan absensi bawahan.
4. Supervisor melihat daftar absensi perlu verifikasi.
5. Supervisor membuka detail absensi.
6. Supervisor melihat waktu, lokasi, jarak dari lokasi kerja, foto, dan catatan pegawai.
7. Supervisor memilih tindakan:
   * Setujui absensi.
   * Tolak absensi.
   * Koreksi absensi.
8. Sistem menyimpan riwayat validasi atau koreksi.
9. Supervisor melihat rekap kehadiran bawahan.

### 5.3 Journey Admin/HR

1. Admin/HR login ke sistem.
2. Admin/HR mengelola user, pegawai, supervisor, dan lokasi kerja.
3. Admin/HR membuat jadwal kerja atau surat tugas.
4. Admin/HR memonitor seluruh data absensi.
5. Admin/HR melakukan koreksi jika diperlukan.
6. Admin/HR mengelola laporan berdasarkan pegawai, lokasi, dan periode.
7. Admin/HR mengelola konfigurasi integrasi pihak ketiga.

### 5.4 Journey Manajemen/Pemilik

1. Manajemen login ke sistem.
2. Manajemen melihat ringkasan kehadiran hari ini.
3. Manajemen melihat jumlah hadir, terlambat, tidak hadir, izin, sakit, dan perlu verifikasi.
4. Manajemen membuka laporan berdasarkan periode.
5. Manajemen memfilter laporan berdasarkan lokasi kerja atau pegawai.

---

## 6. Spesifikasi Fungsional Detail

### 6.1 Authentication dan User Management

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| UM-01 | User dapat login menggunakan email dan password. | MVP |
| UM-02 | User dapat logout dari sistem. | MVP |
| UM-03 | Admin/HR dapat membuat akun user baru. | MVP |
| UM-04 | Admin/HR dapat mengubah nama, email, nomor telepon, role, dan status akun. | MVP |
| UM-05 | Admin/HR dapat menonaktifkan akun user. | MVP |
| UM-06 | Sistem membatasi akses berdasarkan role. | MVP |
| UM-07 | Super Admin dapat mengelola role dan permission. | MVP |

Acceptance criteria:

* User dengan kredensial valid dapat login.
* User nonaktif tidak dapat login.
* Pegawai tidak dapat membuka halaman admin master data.
* Supervisor tidak dapat melihat data pegawai di luar bawahannya.

### 6.2 Employee Management

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| EM-01 | Admin/HR dapat membuat data pegawai. | MVP |
| EM-02 | Admin/HR dapat mengubah data pegawai. | MVP |
| EM-03 | Admin/HR dapat menonaktifkan pegawai. | MVP |
| EM-04 | Admin/HR dapat menghubungkan pegawai dengan user. | MVP |
| EM-05 | Admin/HR dapat menentukan supervisor pegawai. | MVP |
| EM-06 | Supervisor dapat melihat daftar bawahan. | MVP |
| EM-07 | Pegawai dapat melihat data dasar dirinya sendiri. | MVP |

Field minimal:

* Nama pegawai
* User terkait
* Kode pegawai
* Jabatan
* Status pegawai
* Tanggal bergabung
* Supervisor

Validation:

* `employee_code` harus unik.
* `user_id` hanya boleh terhubung ke satu employee aktif.
* `supervisor_user_id` harus user dengan role supervisor atau admin yang diizinkan.

### 6.3 Work Location Management

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| WL-01 | Admin/HR dapat membuat lokasi kerja. | MVP |
| WL-02 | Admin/HR dapat mengubah lokasi kerja. | MVP |
| WL-03 | Admin/HR dapat menonaktifkan lokasi kerja. | MVP |
| WL-04 | Sistem menyimpan nama lokasi, nama klien, alamat, latitude, longitude, dan radius toleransi. | MVP |
| WL-05 | Supervisor dapat melihat lokasi kerja yang terkait dengan jadwal bawahannya. | MVP |
| WL-06 | Pegawai dapat melihat lokasi kerja yang terkait dengan jadwalnya. | MVP |

Validation:

* Latitude wajib berupa angka antara -90 dan 90.
* Longitude wajib berupa angka antara -180 dan 180.
* Radius toleransi wajib lebih dari 0 meter.
* Lokasi nonaktif tidak dapat dipilih untuk jadwal baru.

### 6.4 Assignment/Schedule Management

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| AS-01 | Admin/HR dapat membuat jadwal kerja atau surat tugas. | MVP |
| AS-02 | Supervisor dapat membuat jadwal untuk bawahan. | MVP |
| AS-03 | Jadwal berisi pegawai, lokasi kerja, supervisor, tanggal, jam mulai, jam selesai, judul, dan deskripsi. | MVP |
| AS-04 | Pegawai hanya melihat jadwal miliknya sendiri. | MVP |
| AS-05 | Sistem menampilkan jadwal hari ini di dashboard pegawai. | MVP |
| AS-06 | Admin/HR atau supervisor dapat mengubah status jadwal. | MVP |
| AS-07 | Jadwal yang sudah memiliki absensi tidak dapat dihapus sembarangan. | MVP |

Status assignment:

* `terjadwal`
* `berjalan`
* `selesai`
* `dibatalkan`

Business rules:

* Pegawai hanya dapat melakukan absensi jika memiliki assignment aktif pada tanggal tersebut.
* Assignment dibatalkan tidak dapat digunakan untuk absensi.
* Jam selesai harus lebih besar dari jam mulai.
* Satu assignment dapat memiliki satu attendance record untuk satu pegawai pada tanggal yang sama.

### 6.5 Employee Attendance Dashboard

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| ED-01 | Pegawai melihat kartu jadwal hari ini. | MVP |
| ED-02 | Pegawai melihat nama lokasi, alamat, jam mulai, jam selesai, dan status absensi. | MVP |
| ED-03 | Tombol Absen Masuk muncul jika belum check-in. | MVP |
| ED-04 | Tombol Absen Pulang muncul jika sudah check-in dan belum check-out. | MVP |
| ED-05 | Pegawai dapat melihat riwayat absensi pribadi. | MVP |
| ED-06 | Pegawai dapat melihat status validasi absensi. | MVP |

UX requirement:

* Tampilan mobile harus sederhana.
* Tombol absensi harus mudah dijangkau.
* Status absensi harus terlihat jelas.
* Error lokasi, foto, atau koneksi harus ditampilkan dengan bahasa yang mudah dipahami.

### 6.6 Absensi Masuk

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| AT-01 | Pegawai dapat melakukan absen masuk dari assignment aktif. | MVP |
| AT-02 | Sistem mencatat waktu server saat absen masuk. | MVP |
| AT-03 | Sistem mengambil latitude dan longitude pegawai jika izin lokasi diberikan. | MVP |
| AT-04 | Pegawai wajib mengunggah foto absen masuk. | MVP |
| AT-05 | Pegawai dapat mengisi catatan kendala. | MVP |
| AT-06 | Sistem menghitung jarak pegawai dari lokasi kerja. | MVP |
| AT-07 | Sistem menentukan status validasi awal. | MVP |
| AT-08 | Sistem mencegah duplikasi absen masuk pada assignment yang sama. | MVP |

Alur teknis:

1. Frontend meminta lokasi browser melalui Geolocation API.
2. Frontend mengirim assignment id, latitude, longitude, foto, dan catatan ke backend.
3. Backend memvalidasi assignment milik pegawai.
4. Backend menyimpan foto ke Cloudinary atau storage aplikasi.
5. Backend menghitung jarak dengan formula Haversine.
6. Backend menyimpan attendance record.
7. Backend mengembalikan status berhasil.

Status awal:

* Jika lokasi terbaca dan jarak <= radius toleransi, `verification_status = valid`.
* Jika lokasi terbaca dan jarak > radius toleransi, `verification_status = di_luar_lokasi`.
* Jika lokasi gagal terbaca, `verification_status = perlu_verifikasi`.
* Jika jam check-in lebih dari jam mulai, `attendance_status = terlambat`.
* Jika jam check-in sesuai ketentuan, `attendance_status = hadir`.

### 6.7 Absensi Pulang

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| AO-01 | Pegawai dapat melakukan absen pulang setelah absen masuk. | MVP |
| AO-02 | Sistem mencatat waktu server saat absen pulang. | MVP |
| AO-03 | Sistem mengambil lokasi pulang jika tersedia. | MVP |
| AO-04 | Pegawai mengunggah foto absen pulang jika kebijakan sistem mewajibkan. | MVP |
| AO-05 | Sistem menyimpan lokasi dan foto pulang. | MVP |
| AO-06 | Sistem menandai perlu verifikasi jika data pulang bermasalah. | MVP |
| AO-07 | Sistem mencegah absen pulang berulang. | MVP |

Catatan keputusan produk:

* Untuk MVP, foto absen masuk wajib.
* Foto absen pulang dapat dibuat wajib melalui konfigurasi `attendance_require_checkout_photo`.
* Default rekomendasi MVP adalah foto absen pulang wajib agar bukti absensi lebih kuat.

### 6.8 Status Kehadiran dan Status Validasi

Status kehadiran menjelaskan kondisi kehadiran pegawai.

| **Status** | **Kode** | **Deskripsi** |
| :--- | :--- | :--- |
| Hadir | `hadir` | Pegawai melakukan absensi sesuai jadwal. |
| Terlambat | `terlambat` | Pegawai check-in setelah jam mulai atau toleransi. |
| Izin | `izin` | Pegawai memiliki izin yang dicatat admin/supervisor. |
| Sakit | `sakit` | Pegawai sakit dan dicatat admin/supervisor. |
| Tidak hadir | `tidak_hadir` | Pegawai tidak absen dan tidak memiliki izin/sakit. |

Status validasi menjelaskan kualitas atau validitas data absensi.

| **Status** | **Kode** | **Deskripsi** |
| :--- | :--- | :--- |
| Valid | `valid` | Data absensi sesuai lokasi dan aturan utama. |
| Perlu verifikasi | `perlu_verifikasi` | Data belum cukup kuat atau perlu dicek manual. |
| Di luar lokasi | `di_luar_lokasi` | Lokasi pegawai di luar radius toleransi. |
| Ditolak | `ditolak` | Absensi ditolak supervisor/admin. |
| Dikoreksi manual | `dikoreksi_manual` | Absensi diperbaiki oleh supervisor/admin dengan alasan. |

Aturan:

* Status kehadiran dan status validasi wajib dipisahkan.
* Pegawai bisa `hadir` tetapi `di_luar_lokasi`.
* Pegawai bisa `hadir` tetapi `dikoreksi_manual`.
* Absensi `ditolak` tidak otomatis menghapus record.

### 6.9 Validasi Absensi

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| AV-01 | Supervisor melihat absensi bawahannya yang perlu verifikasi. | MVP |
| AV-02 | Admin/HR melihat seluruh absensi yang perlu verifikasi. | MVP |
| AV-03 | Supervisor/Admin dapat menyetujui absensi. | MVP |
| AV-04 | Supervisor/Admin dapat menolak absensi. | MVP |
| AV-05 | Penolakan wajib memiliki catatan alasan. | MVP |
| AV-06 | Sistem menyimpan siapa yang melakukan validasi dan kapan. | MVP |
| AV-07 | Sistem menyimpan riwayat validasi. | MVP |

Data yang tampil pada detail validasi:

* Nama pegawai
* Assignment
* Lokasi kerja
* Waktu check-in
* Waktu check-out
* Koordinat check-in
* Koordinat check-out
* Jarak dari lokasi kerja
* Foto check-in
* Foto check-out
* Catatan pegawai
* Status kehadiran
* Status validasi

### 6.10 Koreksi Absensi

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| AC-01 | Supervisor/Admin dapat mengoreksi absensi. | MVP |
| AC-02 | Koreksi hanya dapat dilakukan dengan alasan. | MVP |
| AC-03 | Sistem menyimpan nilai lama dan nilai baru. | MVP |
| AC-04 | Sistem mengubah status validasi menjadi `dikoreksi_manual`. | MVP |
| AC-05 | Riwayat koreksi dapat dilihat oleh admin dan supervisor terkait. | MVP |

Jenis koreksi:

* Koreksi waktu masuk.
* Koreksi waktu pulang.
* Koreksi status kehadiran.
* Koreksi status validasi.
* Koreksi catatan.
* Koreksi lokasi jika ada kesalahan input atau kondisi khusus.

Aturan:

* Koreksi tidak boleh menghapus bukti asli.
* Nilai lama dan baru disimpan untuk audit.
* Pegawai tidak dapat mengoreksi absensinya sendiri.

### 6.11 Izin dan Sakit Sederhana

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| LS-01 | Admin/Supervisor dapat menginput status izin. | MVP |
| LS-02 | Admin/Supervisor dapat menginput status sakit. | MVP |
| LS-03 | Status izin/sakit terhubung dengan assignment. | MVP |
| LS-04 | Pegawai dengan izin/sakit tidak wajib absen. | MVP |
| LS-05 | Data izin/sakit masuk ke laporan. | MVP |

Catatan:

* MVP tidak membutuhkan alur pengajuan izin oleh pegawai.
* Dokumen pendukung sakit bersifat opsional untuk fase lanjutan.

### 6.12 Dashboard Supervisor

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| SD-01 | Menampilkan jumlah bawahan aktif. | MVP |
| SD-02 | Menampilkan absensi bawahan hari ini. | MVP |
| SD-03 | Menampilkan absensi perlu verifikasi. | MVP |
| SD-04 | Menampilkan daftar pegawai terlambat. | MVP |
| SD-05 | Menampilkan daftar pegawai tidak hadir. | MVP |
| SD-06 | Menyediakan action setujui, tolak, dan koreksi. | MVP |
| SD-07 | Menampilkan rekap berdasarkan periode. | MVP |

### 6.13 Dashboard Admin/HR

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| AD-01 | Menampilkan ringkasan pegawai aktif. | MVP |
| AD-02 | Menampilkan ringkasan absensi hari ini. | MVP |
| AD-03 | Menampilkan absensi perlu verifikasi seluruh sistem. | MVP |
| AD-04 | Menyediakan akses master user, pegawai, lokasi, assignment. | MVP |
| AD-05 | Menyediakan akses laporan absensi. | MVP |
| AD-06 | Menyediakan akses konfigurasi integrasi. | MVP |

### 6.14 Dashboard Manajemen

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| MD-01 | Menampilkan jumlah pegawai aktif. | MVP |
| MD-02 | Menampilkan jumlah hadir hari ini. | MVP |
| MD-03 | Menampilkan jumlah terlambat hari ini. | MVP |
| MD-04 | Menampilkan jumlah tidak hadir hari ini. | MVP |
| MD-05 | Menampilkan jumlah absensi perlu verifikasi. | MVP |
| MD-06 | Menampilkan laporan berdasarkan periode. | MVP |
| MD-07 | Menampilkan laporan berdasarkan lokasi kerja. | MVP |

### 6.15 Reporting

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| RP-01 | Laporan absensi harian. | MVP |
| RP-02 | Laporan absensi per pegawai. | MVP |
| RP-03 | Laporan absensi per lokasi kerja. | MVP |
| RP-04 | Laporan keterlambatan. | MVP |
| RP-05 | Laporan izin dan sakit. | MVP |
| RP-06 | Laporan tidak hadir. | MVP |
| RP-07 | Laporan absensi perlu verifikasi. | MVP |
| RP-08 | Filter laporan berdasarkan periode. | MVP |
| RP-09 | Export Excel/PDF. | Fase 2 |

Filter laporan:

* Tanggal mulai
* Tanggal selesai
* Pegawai
* Supervisor
* Lokasi kerja
* Status kehadiran
* Status validasi

### 6.16 Third Party Integration

| **ID** | **Requirement** | **Prioritas** |
| :--- | :--- | :--- |
| IN-01 | Sistem mendukung Cloudinary untuk upload foto absensi. | MVP |
| IN-02 | Sistem menyimpan URL foto dari provider ke database. | MVP |
| IN-03 | Sistem mendukung Google Maps untuk menampilkan titik lokasi. | MVP/Fase 2 |
| IN-04 | Sistem mendukung Telegram Bot untuk notifikasi absensi bermasalah. | Fase 2 |
| IN-05 | Sistem menyimpan log integrasi. | MVP |

Rekomendasi MVP:

* Integrasi utama: Cloudinary.
* Google Maps digunakan jika API key tersedia.
* Telegram Bot dijadikan opsi fase lanjutan.

Aturan integrasi:

* Kegagalan upload foto harus ditampilkan kepada user.
* Jika Google Maps gagal, data koordinat tetap disimpan.
* Jika Telegram gagal, absensi tetap tersimpan dan error dicatat di `integration_logs`.
* Credential integrasi disimpan di environment/config, bukan hardcode.

---

## 7. Model Data

### 7.1 `users`

| **Field** | **Tipe** | **Keterangan** |
| :--- | :--- | :--- |
| id | bigint | Primary key |
| name | varchar | Nama user |
| email | varchar | Email unik |
| password | varchar | Password hash |
| phone_number | varchar nullable | Nomor telepon |
| role | enum/string | Role utama |
| status | enum/string | Aktif/nonaktif |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### 7.2 `employees`

| **Field** | **Tipe** | **Keterangan** |
| :--- | :--- | :--- |
| id | bigint | Primary key |
| user_id | bigint nullable | Relasi ke users |
| employee_code | varchar | Kode pegawai unik |
| position | varchar nullable | Jabatan |
| status | enum/string | Aktif/nonaktif |
| join_date | date nullable | Tanggal bergabung |
| supervisor_user_id | bigint nullable | User supervisor |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### 7.3 `work_locations`

| **Field** | **Tipe** | **Keterangan** |
| :--- | :--- | :--- |
| id | bigint | Primary key |
| location_name | varchar | Nama lokasi |
| client_name | varchar nullable | Nama klien/lokasi bisnis |
| address | text | Alamat |
| latitude | decimal | Latitude lokasi kerja |
| longitude | decimal | Longitude lokasi kerja |
| radius_tolerance | integer | Radius toleransi dalam meter |
| status | enum/string | Aktif/nonaktif |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### 7.4 `assignments`

| **Field** | **Tipe** | **Keterangan** |
| :--- | :--- | :--- |
| id | bigint | Primary key |
| employee_id | bigint | Pegawai yang ditugaskan |
| work_location_id | bigint | Lokasi kerja |
| supervisor_user_id | bigint | Supervisor penanggung jawab |
| assignment_date | date | Tanggal tugas |
| start_time | time | Jam mulai |
| end_time | time | Jam selesai |
| title | varchar | Judul jadwal/surat tugas |
| description | text nullable | Deskripsi |
| assignment_status | enum/string | Status assignment |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### 7.5 `attendance_records`

| **Field** | **Tipe** | **Keterangan** |
| :--- | :--- | :--- |
| id | bigint | Primary key |
| employee_id | bigint | Relasi pegawai |
| assignment_id | bigint | Relasi assignment |
| check_in_time | datetime nullable | Waktu absen masuk |
| check_out_time | datetime nullable | Waktu absen pulang |
| check_in_latitude | decimal nullable | Latitude masuk |
| check_in_longitude | decimal nullable | Longitude masuk |
| check_out_latitude | decimal nullable | Latitude pulang |
| check_out_longitude | decimal nullable | Longitude pulang |
| check_in_distance_meters | integer nullable | Jarak masuk dari lokasi kerja |
| check_out_distance_meters | integer nullable | Jarak pulang dari lokasi kerja |
| check_in_photo_url | text nullable | URL foto masuk |
| check_out_photo_url | text nullable | URL foto pulang |
| attendance_status | enum/string | Status kehadiran |
| verification_status | enum/string | Status validasi |
| notes | text nullable | Catatan pegawai/admin |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

Index rekomendasi:

* `employee_id`
* `assignment_id`
* `attendance_status`
* `verification_status`
* `check_in_time`

Constraint rekomendasi:

* Unique `assignment_id` pada attendance record aktif untuk mencegah duplikasi absensi.

### 7.6 `attendance_approvals`

| **Field** | **Tipe** | **Keterangan** |
| :--- | :--- | :--- |
| id | bigint | Primary key |
| attendance_record_id | bigint | Relasi attendance |
| approved_by | bigint | User yang memvalidasi |
| approval_status | enum/string | Disetujui/ditolak/perlu koreksi |
| approval_note | text nullable | Catatan validasi |
| approved_at | datetime nullable | Waktu validasi |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### 7.7 `attendance_corrections`

| **Field** | **Tipe** | **Keterangan** |
| :--- | :--- | :--- |
| id | bigint | Primary key |
| attendance_record_id | bigint | Relasi attendance |
| corrected_by | bigint | User yang mengoreksi |
| correction_type | varchar | Jenis koreksi |
| old_value | json/text | Nilai lama |
| new_value | json/text | Nilai baru |
| correction_reason | text | Alasan koreksi |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### 7.8 `integration_logs`

| **Field** | **Tipe** | **Keterangan** |
| :--- | :--- | :--- |
| id | bigint | Primary key |
| provider_name | varchar | cloudinary/google_maps/telegram_bot |
| action | varchar | Nama aksi |
| request_status | varchar | success/failed |
| response_message | text nullable | Ringkasan respons/error |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

---

## 8. Business Rules

| **ID** | **Aturan** |
| :--- | :--- |
| BR-01 | Pegawai harus login sebelum melakukan absensi. |
| BR-02 | Pegawai hanya dapat absen pada assignment aktif miliknya. |
| BR-03 | Assignment dibatalkan tidak dapat digunakan untuk absensi. |
| BR-04 | Absen masuk wajib memiliki foto. |
| BR-05 | Absen pulang hanya dapat dilakukan jika sudah ada absen masuk. |
| BR-06 | Satu assignment hanya boleh memiliki satu attendance record aktif. |
| BR-07 | Lokasi GPS digunakan sebagai bukti pendukung, bukan keputusan mutlak. |
| BR-08 | Jika lokasi di luar radius, absensi tetap disimpan tetapi perlu verifikasi. |
| BR-09 | Jika lokasi gagal terbaca, absensi tetap dapat diajukan dengan status perlu verifikasi. |
| BR-10 | Supervisor hanya dapat memvalidasi absensi bawahan. |
| BR-11 | Admin/HR dapat memvalidasi dan mengoreksi seluruh absensi. |
| BR-12 | Koreksi absensi wajib memiliki alasan. |
| BR-13 | Koreksi absensi tidak boleh menghapus bukti asli. |
| BR-14 | Status kehadiran dan status validasi harus dipisahkan. |
| BR-15 | Sistem tidak menghitung payroll otomatis. |
| BR-16 | Sistem tidak memberi sanksi otomatis berdasarkan absensi. |

---

## 9. UX dan UI Requirements

### 9.1 Pegawai Mobile First

Halaman pegawai harus dioptimalkan untuk smartphone.

Requirement:

* Tombol Absen Masuk dan Absen Pulang terlihat jelas.
* Informasi jadwal hari ini tampil di layar pertama.
* Status absensi tampil dengan label yang mudah dipahami.
* Form catatan kendala mudah diisi.
* Preview foto ditampilkan sebelum submit jika memungkinkan.
* Pesan error lokasi dan kamera menggunakan bahasa sederhana.

### 9.2 Supervisor dan Admin

Requirement:

* Dashboard menampilkan prioritas kerja: absensi perlu verifikasi.
* Tabel dapat difilter berdasarkan tanggal, pegawai, lokasi, dan status.
* Detail absensi menampilkan foto dan koordinat.
* Action approve/reject/correct mudah ditemukan.
* Modal penolakan dan koreksi wajib meminta catatan.

### 9.3 Manajemen

Requirement:

* Dashboard ringkas dan mudah dibaca.
* Menampilkan angka utama: aktif, hadir, terlambat, tidak hadir, perlu verifikasi.
* Filter periode tersedia.
* Detail operasional tidak perlu terlalu dalam.

---

## 10. Non-Functional Requirements

| **Kategori** | **Requirement** |
| :--- | :--- |
| Keamanan | Data pegawai, foto, lokasi, dan absensi hanya dapat diakses sesuai role. |
| Authentication | Login email dan password. |
| Authorization | Role-based access control dan policy per resource. |
| Privacy | Foto dan lokasi hanya digunakan untuk validasi kehadiran. |
| Auditability | Approval dan koreksi harus tercatat. |
| Performance | Halaman utama dan dashboard ditampilkan dalam waktu wajar. |
| Compatibility | Mendukung browser modern pada desktop dan smartphone. |
| Reliability | Data absensi tetap tersimpan walau integrasi notifikasi gagal. |
| Maintainability | Master data dapat diperbarui admin tanpa ubah kode. |
| Integration | Minimal satu service pihak ketiga digunakan. |
| Backup | Database perlu mendukung backup berkala. |

Catatan teknis:

* Fitur GPS dan kamera membutuhkan HTTPS pada environment production.
* Jika local development memakai domain seperti `https://remote-attendance.test`, sertifikat lokal perlu dikonfigurasi.
* Foto absensi termasuk data sensitif dan tidak boleh ditampilkan ke user yang tidak berhak.

---

## 11. API dan Route Requirements

Walaupun sistem berbasis web dan Filament, beberapa endpoint khusus dapat dibuat untuk kebutuhan absensi pegawai.

### 11.1 Route Pegawai

| **Method** | **Path** | **Kegunaan** |
| :--- | :--- | :--- |
| GET | `/employee/dashboard` | Dashboard pegawai. |
| GET | `/employee/attendance/history` | Riwayat absensi pribadi. |
| POST | `/employee/attendance/check-in` | Submit absen masuk. |
| POST | `/employee/attendance/check-out` | Submit absen pulang. |

### 11.2 Route Supervisor

| **Method** | **Path** | **Kegunaan** |
| :--- | :--- | :--- |
| GET | `/supervisor/dashboard` | Dashboard supervisor. |
| GET | `/supervisor/attendance/pending` | Absensi perlu verifikasi. |
| POST | `/supervisor/attendance/{id}/approve` | Setujui absensi. |
| POST | `/supervisor/attendance/{id}/reject` | Tolak absensi. |
| POST | `/supervisor/attendance/{id}/correct` | Koreksi absensi. |

### 11.3 Route Admin/HR

Route master data dapat dikelola melalui Filament Resources:

* UserResource
* EmployeeResource
* WorkLocationResource
* AssignmentResource
* AttendanceRecordResource
* AttendanceApprovalResource
* AttendanceCorrectionResource

---

## 12. Validasi Teknis

### 12.1 Validasi Lokasi

Sistem menghitung jarak antara koordinat pegawai dan koordinat lokasi kerja menggunakan formula Haversine.

Input:

* Latitude pegawai
* Longitude pegawai
* Latitude lokasi kerja
* Longitude lokasi kerja
* Radius toleransi lokasi kerja

Output:

* Jarak dalam meter
* Status dalam radius atau luar radius

Aturan:

* Jika koordinat pegawai kosong, status validasi menjadi `perlu_verifikasi`.
* Jika jarak <= radius, status validasi menjadi `valid`.
* Jika jarak > radius, status validasi menjadi `di_luar_lokasi`.

### 12.2 Validasi Foto

Requirement:

* File foto check-in wajib.
* File foto check-out mengikuti konfigurasi.
* Format file: jpg, jpeg, png, webp.
* Ukuran maksimal disesuaikan konfigurasi, rekomendasi 2 MB sampai 5 MB.
* Foto disimpan di Cloudinary atau storage aplikasi.
* Database hanya menyimpan URL/path foto.

### 12.3 Validasi Waktu

Requirement:

* Waktu check-in dan check-out menggunakan waktu server.
* Jam mulai dan jam selesai berasal dari assignment.
* Keterlambatan dihitung dari check-in time dibanding start_time.
* Toleransi keterlambatan dapat dibuat konfigurasi, default 0 menit untuk MVP.

---

## 13. Acceptance Criteria MVP

### 13.1 Pegawai

* Pegawai dapat login.
* Pegawai dapat melihat jadwal hari ini.
* Pegawai dapat melakukan absen masuk pada assignment aktif.
* Sistem mencatat waktu absen masuk.
* Sistem mencatat lokasi jika izin lokasi diberikan.
* Pegawai dapat mengunggah foto absen masuk.
* Sistem menentukan status validasi berdasarkan radius lokasi.
* Pegawai dapat melakukan absen pulang.
* Pegawai dapat melihat riwayat absensi pribadi.

### 13.2 Supervisor

* Supervisor dapat melihat daftar bawahan.
* Supervisor dapat melihat absensi bawahan hari ini.
* Supervisor dapat melihat absensi perlu verifikasi.
* Supervisor dapat melihat foto dan lokasi absensi.
* Supervisor dapat menyetujui absensi.
* Supervisor dapat menolak absensi dengan alasan.
* Supervisor dapat mengoreksi absensi dengan alasan.

### 13.3 Admin/HR

* Admin/HR dapat mengelola user.
* Admin/HR dapat mengelola pegawai.
* Admin/HR dapat mengelola lokasi kerja.
* Admin/HR dapat mengelola jadwal atau surat tugas.
* Admin/HR dapat melihat seluruh absensi.
* Admin/HR dapat melakukan koreksi absensi.
* Admin/HR dapat melihat laporan absensi.
* Admin/HR dapat mengelola konfigurasi integrasi.

### 13.4 Manajemen

* Manajemen dapat melihat ringkasan pegawai aktif.
* Manajemen dapat melihat ringkasan hadir, terlambat, tidak hadir, izin, sakit, dan perlu verifikasi.
* Manajemen dapat melihat laporan berdasarkan periode.
* Manajemen dapat melihat laporan berdasarkan lokasi kerja.

### 13.5 Integrasi

* Sistem berhasil menggunakan minimal satu service pihak ketiga.
* Jika menggunakan Cloudinary, foto absensi tersimpan dan URL tersimpan di database.
* Jika integrasi gagal, sistem mencatat error pada integration log.

---

## 14. Test Scenarios

### 14.1 Authentication

| **ID** | **Scenario** | **Expected Result** |
| :--- | :--- | :--- |
| T-UM-01 | Login dengan akun aktif | User masuk ke dashboard sesuai role. |
| T-UM-02 | Login dengan akun nonaktif | Sistem menolak login. |
| T-UM-03 | Pegawai membuka halaman admin | Akses ditolak. |
| T-UM-04 | Supervisor membuka data bukan bawahan | Akses ditolak. |

### 14.2 Absensi Masuk

| **ID** | **Scenario** | **Expected Result** |
| :--- | :--- | :--- |
| T-AT-01 | Pegawai absen di dalam radius | Absensi tersimpan dengan status valid. |
| T-AT-02 | Pegawai absen di luar radius | Absensi tersimpan dengan status di luar lokasi. |
| T-AT-03 | Pegawai menolak izin lokasi | Absensi dapat diajukan dengan status perlu verifikasi. |
| T-AT-04 | Pegawai tidak upload foto masuk | Submit ditolak. |
| T-AT-05 | Pegawai absen dua kali pada assignment sama | Submit kedua ditolak. |

### 14.3 Absensi Pulang

| **ID** | **Scenario** | **Expected Result** |
| :--- | :--- | :--- |
| T-AO-01 | Pegawai pulang setelah check-in | Check-out tersimpan. |
| T-AO-02 | Pegawai pulang tanpa check-in | Submit ditolak. |
| T-AO-03 | Pegawai pulang di luar lokasi | Status validasi perlu verifikasi atau di luar lokasi. |
| T-AO-04 | Pegawai submit check-out dua kali | Submit kedua ditolak. |

### 14.4 Validasi dan Koreksi

| **ID** | **Scenario** | **Expected Result** |
| :--- | :--- | :--- |
| T-AV-01 | Supervisor approve absensi bawahan | Status menjadi valid/disetujui dan riwayat tersimpan. |
| T-AV-02 | Supervisor reject absensi tanpa alasan | Sistem menolak action. |
| T-AV-03 | Supervisor reject absensi dengan alasan | Status menjadi ditolak dan alasan tersimpan. |
| T-AC-01 | Admin koreksi waktu check-in | Nilai lama dan baru tersimpan di correction log. |
| T-AC-02 | Pegawai mencoba koreksi sendiri | Akses ditolak. |

### 14.5 Laporan

| **ID** | **Scenario** | **Expected Result** |
| :--- | :--- | :--- |
| T-RP-01 | Admin filter laporan per tanggal | Data sesuai periode tampil. |
| T-RP-02 | Admin filter laporan per lokasi | Data sesuai lokasi tampil. |
| T-RP-03 | Supervisor lihat rekap bawahan | Hanya data bawahan tampil. |
| T-RP-04 | Manajemen lihat dashboard | Ringkasan tampil tanpa action koreksi. |

---

## 15. Milestone Implementasi

### 15.1 Sprint 1 - Foundation

Output:

* Setup Laravel, Filament, database, auth.
* Role dan permission awal.
* UserResource.
* EmployeeResource.
* WorkLocationResource.

### 15.2 Sprint 2 - Assignment dan Dashboard Dasar

Output:

* AssignmentResource.
* Relasi pegawai, supervisor, lokasi.
* Dashboard pegawai menampilkan jadwal hari ini.
* Dashboard supervisor menampilkan bawahan.

### 15.3 Sprint 3 - Absensi Masuk dan Pulang

Output:

* Form absen masuk.
* Form absen pulang.
* Geolocation API.
* Upload foto.
* Perhitungan radius.
* Status kehadiran dan validasi awal.

### 15.4 Sprint 4 - Validasi dan Koreksi

Output:

* Dashboard absensi perlu verifikasi.
* Approve/reject absensi.
* Koreksi absensi.
* Attendance approval log.
* Attendance correction log.

### 15.5 Sprint 5 - Laporan dan Integrasi

Output:

* Laporan harian, pegawai, lokasi, periode.
* Integrasi Cloudinary.
* Integration log.
* Dashboard admin dan manajemen.

### 15.6 Sprint 6 - QA dan Stabilization

Output:

* Testing role dan permission.
* Testing absensi mobile.
* Testing upload foto.
* Testing laporan.
* Perbaikan bug.
* Dokumentasi deployment dasar.

---

## 16. Risiko Produk dan Mitigasi

| **Risiko** | **Mitigasi** |
| :--- | :--- |
| Pegawai tidak memberi izin lokasi | Absensi tetap dapat diajukan dengan status perlu verifikasi. |
| GPS tidak akurat | Supervisor/admin dapat memverifikasi dan mengoreksi. |
| Pegawai lupa absen | Koreksi manual dengan alasan. |
| Foto tidak sesuai | Supervisor/admin dapat menolak absensi. |
| Upload foto gagal | Tampilkan error dan catat log integrasi jika memakai provider. |
| Supervisor terlalu banyak verifikasi | Prioritaskan absensi bermasalah di dashboard. |
| Scope melebar menjadi HRIS lengkap | Out of scope ditegaskan dan backlog fase lanjutan dipisahkan. |
| Integrasi pihak ketiga gagal | Data utama absensi tetap disimpan jika memungkinkan, error dicatat. |
| Data sensitif terekspos | Terapkan policy, role, private storage, dan pembatasan akses foto. |

---

## 17. Future Enhancements

Fitur lanjutan yang dapat dipertimbangkan setelah MVP:

* Export Excel/PDF.
* Notifikasi Telegram otomatis.
* Pengajuan izin dan sakit oleh pegawai.
* Dokumen pendukung izin/sakit.
* Approval bertingkat.
* Shift kerja berulang.
* Kalender absensi.
* Deteksi fake GPS dasar.
* Face recognition.
* Mode offline.
* Integrasi payroll sebagai pembaca data absensi, bukan bagian inti MVP.
* Merit/reward/training sebagai modul lanjutan jika dibutuhkan.

---

## 18. Ringkasan Keputusan PRD v4

PRD v4 menetapkan bahwa sistem tetap merupakan bagian dari domain SDM, tetapi MVP difokuskan pada modul absensi jarak jauh pegawai lapangan. Produk tidak dikembangkan sebagai HRIS lengkap pada tahap awal.

Prioritas utama MVP:

1. Master data user, pegawai, supervisor, dan lokasi kerja.
2. Jadwal kerja atau surat tugas.
3. Absensi masuk dan pulang berbasis waktu, lokasi, dan foto.
4. Validasi dan koreksi absensi oleh supervisor/admin.
5. Dashboard per role.
6. Laporan absensi dasar.
7. Minimal satu integrasi service pihak ketiga.

Dokumen ini menjadi acuan teknis untuk implementasi, pengujian, dan pengendalian scope pengembangan versi 4.0.
