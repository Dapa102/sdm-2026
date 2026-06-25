# BUSINESS REQUIREMENT DOCUMENT (BRD)

# SISTEM ABSENSI JARAK JAUH PEGAWAI LAPANGAN BERBASIS WEB

## Sistem Monitoring Kehadiran Pegawai Lapangan Berbasis Lokasi, Foto, dan Validasi Supervisor

---

## 1. Ringkasan Eksekutif

Dokumen Business Requirement Document (BRD) ini menjelaskan kebutuhan bisnis dan kebutuhan sistem untuk pengembangan aplikasi **Sistem Absensi Jarak Jauh Pegawai Lapangan Berbasis Web**. Sistem ini dirancang untuk membantu perusahaan atau organisasi yang memiliki pegawai lapangan dalam mencatat, memantau, dan memvalidasi kehadiran pegawai yang bekerja di luar kantor utama.

Sistem ini berfokus pada layanan absensi jarak jauh sebagai fitur utama. Pegawai lapangan dapat melakukan absensi masuk dan absensi pulang melalui sistem berbasis web dengan mencatat waktu, lokasi, dan foto sebagai bukti pendukung. Supervisor atau admin dapat memantau absensi pegawai, memverifikasi absensi bermasalah, serta melihat rekap kehadiran berdasarkan periode tertentu.

Sistem ini tidak dirancang sebagai HRIS lengkap, melainkan sebagai sistem monitoring kehadiran pegawai lapangan. Fitur seperti merit system, reward, training, payroll, dan pembinaan karir tidak menjadi ruang lingkup utama pada tahap MVP. Fitur tersebut dapat dikembangkan sebagai pengembangan lanjutan apabila perusahaan membutuhkan evaluasi performa berbasis data absensi.

Dengan adanya sistem ini, perusahaan dapat mengurangi ketergantungan pada pencatatan manual, chat WhatsApp, atau file Excel yang terpisah. Sistem diharapkan dapat membantu perusahaan dalam meningkatkan akurasi pencatatan kehadiran, mempermudah pengawasan pegawai lapangan, serta menyediakan data absensi yang lebih terstruktur untuk kebutuhan laporan dan evaluasi.

---

## 2. Latar Belakang dan Justifikasi Bisnis

### 2.1 Konteks

Banyak perusahaan atau organisasi memiliki pegawai yang bekerja di luar kantor utama. Pegawai tersebut dapat berupa tenaga cleaning service, security, teknisi lapangan, sales lapangan, surveyor, kurir internal, petugas maintenance, pegawai cabang, atau pegawai yang mendapatkan surat tugas ke lokasi tertentu.

Karakteristik pegawai lapangan adalah mereka tidak selalu berada di kantor pusat dan tidak selalu diawasi langsung oleh supervisor. Dalam kondisi tersebut, pencatatan absensi manual dapat menimbulkan masalah seperti data kehadiran tidak rapi, sulit diverifikasi, dan sulit direkap.

Jika absensi masih dilakukan melalui WhatsApp, foto manual, telepon, atau Excel, maka perusahaan akan kesulitan memastikan apakah pegawai benar-benar hadir di lokasi kerja, datang sesuai jadwal, dan menyelesaikan jam kerja sesuai ketentuan. Oleh karena itu, dibutuhkan sistem absensi jarak jauh yang dapat mencatat waktu, lokasi, dan bukti foto saat pegawai melakukan absensi.

### 2.2 Permasalahan

Permasalahan yang umum terjadi dalam pengelolaan absensi pegawai lapangan adalah sebagai berikut:

* Pegawai bekerja di luar kantor utama sehingga sulit dipantau secara langsung.
* Absensi manual melalui WhatsApp, telepon, atau Excel sulit diverifikasi.
* Perusahaan sulit memastikan apakah pegawai benar-benar berada di lokasi kerja saat melakukan absensi.
* Data absensi dapat tersebar di banyak media dan sulit direkap.
* Supervisor tidak selalu hadir di lokasi kerja untuk memantau kehadiran pegawai.
* Pegawai dapat lupa melakukan absen masuk atau absen pulang.
* Lokasi GPS dapat gagal terbaca atau tidak akurat pada kondisi tertentu.
* Perusahaan membutuhkan laporan kehadiran pegawai berdasarkan tanggal, lokasi, dan periode tertentu.
* Admin atau supervisor membutuhkan mekanisme validasi untuk absensi yang bermasalah.
* Perusahaan membutuhkan sistem yang mudah digunakan oleh pegawai lapangan, termasuk pegawai yang belum terbiasa dengan aplikasi kompleks.

### 2.3 Solusi yang Diusulkan

Solusi yang diusulkan adalah membangun sistem absensi jarak jauh berbasis web untuk pegawai lapangan. Sistem ini memungkinkan pegawai melakukan absensi masuk dan pulang dari lokasi kerja menggunakan perangkat smartphone atau browser.

Fitur utama yang diusulkan meliputi:

* Login pengguna berdasarkan role.
* Manajemen data pegawai.
* Manajemen lokasi kerja.
* Manajemen jadwal atau surat tugas.
* Absensi masuk pegawai.
* Absensi pulang pegawai.
* Pencatatan waktu absensi.
* Pencatatan lokasi absensi.
* Upload foto sebagai bukti absensi.
* Validasi absensi oleh supervisor atau admin.
* Koreksi manual absensi dengan catatan alasan.
* Rekap absensi pegawai.
* Laporan kehadiran berdasarkan periode.
* Integrasi service pihak ketiga untuk mendukung lokasi, foto, atau notifikasi.

Manfaat yang diharapkan dari sistem ini adalah:

* Mempermudah pencatatan absensi pegawai lapangan.
* Membantu perusahaan memantau kehadiran pegawai di luar kantor.
* Mengurangi risiko data absensi tercecer atau tidak terdokumentasi.
* Membantu supervisor memverifikasi absensi bermasalah.
* Menyediakan laporan absensi yang lebih rapi.
* Meningkatkan efisiensi pengawasan pegawai lapangan.
* Memberikan bukti pendukung berupa lokasi dan foto saat absensi.

---

## 3. Tujuan Bisnis

Tujuan bisnis dari pengembangan sistem ini adalah:

* Menyediakan sistem absensi jarak jauh yang mudah digunakan oleh pegawai lapangan.
* Membantu perusahaan mencatat kehadiran pegawai lapangan secara lebih terstruktur.
* Membantu supervisor dan admin memantau absensi pegawai tanpa harus selalu berada di lokasi kerja.
* Menyediakan data absensi yang dilengkapi waktu, lokasi, dan foto sebagai bukti pendukung.
* Mempermudah proses validasi absensi yang bermasalah.
* Mengurangi penggunaan pencatatan manual melalui WhatsApp, kertas, atau Excel.
* Menyediakan laporan absensi berdasarkan pegawai, lokasi kerja, jadwal, dan periode tertentu.
* Mendukung perusahaan dalam meningkatkan kedisiplinan dan transparansi kehadiran pegawai lapangan.

---

## 4. Ruang Lingkup

### 4.1 Dashboard Pegawai Lapangan

Dashboard pegawai digunakan oleh pegawai lapangan untuk melihat jadwal atau surat tugas dan melakukan absensi.

Ruang lingkup dashboard pegawai meliputi:

* Login dan logout.
* Melihat jadwal kerja atau surat tugas hari ini.
* Melihat detail lokasi kerja.
* Melakukan absensi masuk.
* Melakukan absensi pulang.
* Mengunggah foto absensi.
* Menambahkan catatan apabila lokasi gagal terbaca atau ada kendala.
* Melihat status absensi pribadi.
* Melihat riwayat absensi pribadi.

### 4.2 Dashboard Supervisor

Dashboard supervisor digunakan untuk memantau dan memverifikasi absensi pegawai yang berada di bawah tanggung jawabnya.

Ruang lingkup dashboard supervisor meliputi:

* Login dan logout.
* Melihat daftar pegawai yang diawasi.
* Melihat absensi masuk dan pulang pegawai.
* Melihat lokasi dan foto absensi pegawai.
* Memverifikasi absensi yang perlu validasi.
* Menyetujui atau menolak absensi bermasalah.
* Melakukan koreksi absensi dengan catatan alasan.
* Melihat rekap absensi pegawai.
* Melihat laporan kehadiran berdasarkan periode.

### 4.3 Dashboard Admin/HR

Dashboard Admin/HR digunakan untuk mengelola data utama sistem dan melihat laporan absensi.

Ruang lingkup dashboard Admin/HR meliputi:

* Manajemen data pengguna.
* Manajemen data pegawai.
* Manajemen data supervisor.
* Manajemen lokasi kerja.
* Manajemen jadwal kerja atau surat tugas.
* Monitoring data absensi.
* Monitoring absensi yang perlu verifikasi.
* Koreksi data absensi jika diperlukan.
* Pengelolaan laporan absensi.
* Pengaturan integrasi service pihak ketiga.

### 4.4 Dashboard Manajemen/Pemilik

Dashboard manajemen digunakan untuk melihat ringkasan absensi pegawai lapangan.

Ruang lingkup dashboard manajemen meliputi:

* Monitoring jumlah pegawai aktif.
* Monitoring jumlah pegawai hadir.
* Monitoring jumlah pegawai terlambat.
* Monitoring jumlah pegawai tidak hadir.
* Monitoring absensi yang perlu verifikasi.
* Melihat laporan absensi berdasarkan periode.
* Melihat laporan absensi berdasarkan lokasi kerja.

### 4.5 Pengelolaan Pegawai

Ruang lingkup pengelolaan pegawai meliputi:

* Menambahkan data pegawai.
* Mengubah data pegawai.
* Menonaktifkan data pegawai.
* Menentukan supervisor pegawai.
* Menentukan status aktif pegawai.
* Menghubungkan pegawai dengan akun pengguna.

### 4.6 Pengelolaan Lokasi Kerja

Ruang lingkup pengelolaan lokasi kerja meliputi:

* Menambahkan lokasi kerja.
* Mengubah lokasi kerja.
* Menentukan alamat lokasi kerja.
* Menentukan titik koordinat lokasi kerja.
* Menentukan radius toleransi absensi.
* Mengaktifkan atau menonaktifkan lokasi kerja.

### 4.7 Pengelolaan Jadwal atau Surat Tugas

Ruang lingkup pengelolaan jadwal atau surat tugas meliputi:

* Membuat jadwal kerja pegawai.
* Membuat surat tugas pegawai lapangan.
* Menentukan tanggal tugas.
* Menentukan jam mulai dan jam selesai.
* Menentukan lokasi kerja.
* Menentukan pegawai yang ditugaskan.
* Menentukan supervisor penanggung jawab.
* Mengubah status jadwal atau surat tugas.

### 4.8 Pengelolaan Absensi

Ruang lingkup pengelolaan absensi meliputi:

* Absensi masuk.
* Absensi pulang.
* Pencatatan waktu absensi.
* Pencatatan lokasi absensi.
* Upload foto absensi.
* Status kehadiran.
* Status validasi absensi.
* Validasi absensi oleh supervisor atau admin.
* Koreksi manual absensi dengan catatan.
* Rekap absensi berdasarkan periode.

Status kehadiran dipisahkan dari status validasi agar proses bisnis lebih jelas.

Status kehadiran meliputi:

* Hadir
* Terlambat
* Izin
* Sakit
* Tidak hadir

Status validasi absensi meliputi:

* Valid
* Perlu verifikasi
* Di luar lokasi
* Ditolak
* Dikoreksi manual

### 4.9 Integrasi Service Pihak Ketiga

Karena sistem dikembangkan untuk kebutuhan mata kuliah Arsitektur Berbasis Layanan, sistem dapat menggunakan service pihak ketiga untuk mendukung fitur absensi jarak jauh.

Integrasi yang dapat digunakan:

* Google Maps Platform untuk menampilkan lokasi kerja dan lokasi absensi.
* Cloudinary untuk menyimpan foto absensi pegawai.
* Telegram Bot API untuk mengirim notifikasi absensi bermasalah kepada supervisor atau admin.

Untuk MVP, minimal satu service pihak ketiga digunakan. Integrasi utama yang direkomendasikan adalah Google Maps Platform atau Cloudinary karena paling relevan dengan fitur absensi jarak jauh.

### 4.10 Pelaporan Dasar

Ruang lingkup pelaporan dasar meliputi:

* Laporan absensi harian.
* Laporan absensi berdasarkan pegawai.
* Laporan absensi berdasarkan lokasi kerja.
* Laporan keterlambatan.
* Laporan izin dan sakit.
* Laporan tidak hadir.
* Laporan absensi yang perlu verifikasi.
* Laporan absensi berdasarkan periode.

### 4.11 Ruang Lingkup yang Tidak Termasuk

Ruang lingkup yang tidak termasuk dalam sistem ini adalah:

* Sistem payroll atau perhitungan gaji.
* Sistem rekrutmen pegawai.
* Sistem kontrak kerja.
* Sistem akuntansi perusahaan.
* Sistem merit point sebagai fitur utama.
* Sistem reward sebagai fitur utama.
* Sistem training sebagai fitur utama.
* Sistem pembinaan karir sebagai fitur utama.
* Aplikasi mobile native Android atau iOS.
* Validasi GPS anti-fake location secara penuh.
* Integrasi fingerprint.
* Integrasi CCTV.
* Keputusan otomatis terhadap sanksi pegawai.
* Monitoring produktivitas kerja secara detail di luar absensi.

---

## 5. Stakeholders dan Pengguna

| Stakeholder       | Peran dan Tanggung Jawab                                                                                                                          |
| ----------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| Pegawai Lapangan  | Melihat jadwal atau surat tugas, melakukan absensi masuk, melakukan absensi pulang, mengunggah foto absensi, dan melihat riwayat absensi pribadi. |
| Supervisor        | Memantau absensi pegawai, memverifikasi absensi bermasalah, menyetujui atau menolak absensi, dan melihat rekap kehadiran pegawai.                 |
| Admin/HR          | Mengelola data pegawai, lokasi kerja, jadwal atau surat tugas, akun pengguna, serta laporan absensi.                                              |
| Manajemen/Pemilik | Melihat ringkasan kehadiran, laporan absensi, dan data absensi berdasarkan periode.                                                               |
| Tim Pengembang    | Mengembangkan, menguji, dan memelihara sistem sesuai kebutuhan bisnis.                                                                            |

---

## 6. Persyaratan Fungsional

### 6.1 Website Pegawai

Sistem harus menyediakan fitur untuk pegawai lapangan sebagai berikut:

* Pegawai dapat login ke sistem.
* Pegawai dapat melihat jadwal atau surat tugas hari ini.
* Pegawai dapat melihat detail lokasi kerja.
* Pegawai dapat melakukan absensi masuk.
* Pegawai dapat melakukan absensi pulang.
* Pegawai dapat mengunggah foto saat absensi.
* Pegawai dapat mengisi catatan apabila terjadi kendala absensi.
* Pegawai dapat melihat status absensi.
* Pegawai dapat melihat riwayat absensi pribadi.

### 6.2 Manajemen Pengguna dan Role

Sistem harus menyediakan pengelolaan pengguna berdasarkan role.

Role utama sistem meliputi:

* Admin/HR
* Supervisor
* Pegawai
* Manajemen

Fitur manajemen pengguna meliputi:

* Admin dapat membuat akun pengguna.
* Admin dapat mengubah data akun pengguna.
* Admin dapat mengatur role pengguna.
* Admin dapat menonaktifkan akun pengguna.
* Sistem membatasi akses fitur berdasarkan role pengguna.

### 6.3 Manajemen Pegawai

Sistem harus menyediakan fitur manajemen data pegawai.

Fitur manajemen pegawai meliputi:

* Admin dapat menambahkan data pegawai.
* Admin dapat mengubah data pegawai.
* Admin dapat menonaktifkan data pegawai.
* Admin dapat menghubungkan data pegawai dengan akun pengguna.
* Admin dapat menentukan supervisor pegawai.
* Supervisor hanya dapat melihat pegawai yang menjadi tanggung jawabnya.

### 6.4 Manajemen Lokasi Kerja

Sistem harus menyediakan fitur manajemen lokasi kerja.

Fitur manajemen lokasi kerja meliputi:

* Admin dapat menambahkan lokasi kerja.
* Admin dapat mengubah data lokasi kerja.
* Admin dapat menentukan alamat lokasi kerja.
* Admin dapat menentukan latitude dan longitude lokasi kerja.
* Admin dapat menentukan radius toleransi absensi.
* Admin dapat mengaktifkan atau menonaktifkan lokasi kerja.

### 6.5 Manajemen Jadwal atau Surat Tugas

Sistem harus menyediakan fitur jadwal kerja atau surat tugas sebagai dasar absensi pegawai.

Fitur jadwal atau surat tugas meliputi:

* Admin atau supervisor dapat membuat jadwal kerja atau surat tugas.
* Jadwal atau surat tugas berisi pegawai, tanggal, jam mulai, jam selesai, dan lokasi kerja.
* Pegawai hanya dapat melakukan absensi jika memiliki jadwal atau surat tugas aktif.
* Admin atau supervisor dapat mengubah status jadwal atau surat tugas.
* Sistem dapat menampilkan jadwal atau surat tugas hari ini pada dashboard pegawai.

Status jadwal atau surat tugas meliputi:

* Terjadwal
* Berjalan
* Selesai
* Dibatalkan

### 6.6 Absensi Masuk

Sistem harus menyediakan fitur absensi masuk.

Alur absensi masuk:

* Pegawai login ke sistem.
* Pegawai membuka jadwal atau surat tugas hari ini.
* Pegawai menekan tombol Absen Masuk.
* Sistem mencatat waktu absensi masuk.
* Sistem mengambil lokasi pegawai jika tersedia.
* Pegawai mengunggah foto absensi.
* Sistem membandingkan lokasi pegawai dengan lokasi kerja.
* Jika lokasi sesuai radius, absensi berstatus valid.
* Jika lokasi di luar radius atau gagal terbaca, absensi berstatus perlu verifikasi.
* Data absensi masuk disimpan ke database.

### 6.7 Absensi Pulang

Sistem harus menyediakan fitur absensi pulang.

Alur absensi pulang:

* Pegawai membuka jadwal atau surat tugas yang sedang berjalan.
* Pegawai menekan tombol Absen Pulang.
* Sistem mencatat waktu absensi pulang.
* Sistem mengambil lokasi pegawai jika tersedia.
* Pegawai mengunggah foto absensi pulang jika diperlukan.
* Sistem menyimpan data absensi pulang.
* Jika absensi pulang bermasalah, sistem menandai data sebagai perlu verifikasi.

### 6.8 Status Kehadiran dan Validasi Absensi

Sistem harus memisahkan status kehadiran dan status validasi absensi.

Status kehadiran digunakan untuk menunjukkan kondisi kehadiran pegawai.

Status kehadiran meliputi:

* Hadir
* Terlambat
* Izin
* Sakit
* Tidak hadir

Status validasi digunakan untuk menunjukkan validitas data absensi.

Status validasi meliputi:

* Valid
* Perlu verifikasi
* Di luar lokasi
* Ditolak
* Dikoreksi manual

Contoh kondisi:

* Pegawai hadir tepat waktu dan berada di lokasi kerja: status kehadiran = hadir, status validasi = valid.
* Pegawai hadir tetapi lokasi berada di luar radius: status kehadiran = hadir, status validasi = di luar lokasi.
* Pegawai tidak melakukan absen dan tidak memiliki izin: status kehadiran = tidak hadir, status validasi = perlu verifikasi.
* Pegawai lupa absen, lalu dikoreksi oleh admin: status kehadiran = hadir, status validasi = dikoreksi manual.

### 6.9 Validasi Absensi oleh Supervisor atau Admin

Sistem harus menyediakan fitur validasi absensi.

Fitur validasi absensi meliputi:

* Supervisor dapat melihat absensi pegawai yang perlu verifikasi.
* Admin dapat melihat seluruh absensi yang perlu verifikasi.
* Supervisor atau admin dapat menyetujui absensi.
* Supervisor atau admin dapat menolak absensi.
* Supervisor atau admin dapat mengoreksi absensi dengan catatan alasan.
* Sistem menyimpan riwayat validasi absensi.

### 6.10 Pengelolaan Izin dan Sakit

Untuk MVP, pengelolaan izin dan sakit dibuat sederhana.

Fitur izin dan sakit meliputi:

* Admin atau supervisor dapat menginput status izin atau sakit.
* Status izin atau sakit dihubungkan dengan jadwal pegawai.
* Pegawai dengan status izin atau sakit tidak wajib melakukan absensi.
* Data izin dan sakit masuk ke laporan absensi.
* Admin atau supervisor dapat mengubah status jika terjadi kesalahan pencatatan.

### 6.11 Koreksi Absensi

Sistem harus menyediakan fitur koreksi absensi.

Koreksi absensi digunakan untuk kondisi:

* Pegawai lupa absen masuk.
* Pegawai lupa absen pulang.
* GPS gagal terbaca.
* Lokasi terbaca tidak akurat.
* Pegawai memiliki alasan yang diterima supervisor.
* Data absensi perlu diperbaiki oleh admin.

Aturan koreksi absensi:

* Koreksi hanya dapat dilakukan oleh supervisor atau admin.
* Koreksi harus memiliki catatan alasan.
* Koreksi tersimpan dalam riwayat sistem.
* Absensi yang dikoreksi memiliki status validasi dikoreksi manual.

### 6.12 Integrasi Service Pihak Ketiga

Sistem harus menggunakan minimal satu service pihak ketiga.

Pilihan service pihak ketiga yang relevan:

1. Google Maps Platform

   * Menampilkan lokasi kerja.
   * Menampilkan lokasi absensi pegawai.
   * Membantu supervisor melihat apakah absensi dilakukan dekat lokasi kerja.

2. Cloudinary

   * Menyimpan foto absensi masuk dan pulang.
   * Menyimpan URL foto ke database aplikasi.
   * Membantu mengurangi penyimpanan file pada server aplikasi.

3. Telegram Bot API

   * Mengirim notifikasi kepada supervisor jika ada absensi perlu verifikasi.
   * Mengirim notifikasi kepada admin jika ada absensi di luar lokasi.
   * Mengirim notifikasi ringkasan absensi tertentu.

Untuk MVP, integrasi utama yang direkomendasikan adalah:

* Google Maps Platform untuk dukungan lokasi absensi.
* Cloudinary untuk penyimpanan foto absensi.

### 6.13 Dashboard Supervisor

Sistem harus menyediakan dashboard supervisor.

Fitur dashboard supervisor meliputi:

* Melihat daftar pegawai yang diawasi.
* Melihat absensi hari ini.
* Melihat absensi yang perlu verifikasi.
* Melihat foto dan lokasi absensi.
* Menyetujui atau menolak absensi.
* Melakukan koreksi absensi dengan catatan.
* Melihat rekap absensi pegawai.

### 6.14 Dashboard Admin/HR

Sistem harus menyediakan dashboard Admin/HR.

Fitur dashboard Admin/HR meliputi:

* Mengelola akun pengguna.
* Mengelola data pegawai.
* Mengelola data lokasi kerja.
* Mengelola jadwal atau surat tugas.
* Melihat seluruh data absensi.
* Melihat absensi yang perlu verifikasi.
* Melakukan koreksi absensi.
* Mengakses laporan absensi.
* Mengelola konfigurasi service pihak ketiga.

### 6.15 Dashboard Manajemen

Sistem harus menyediakan dashboard manajemen secara sederhana.

Fitur dashboard manajemen meliputi:

* Melihat jumlah pegawai aktif.
* Melihat jumlah pegawai hadir hari ini.
* Melihat jumlah pegawai terlambat.
* Melihat jumlah pegawai tidak hadir.
* Melihat jumlah absensi yang perlu verifikasi.
* Melihat laporan absensi berdasarkan periode.
* Melihat laporan absensi berdasarkan lokasi kerja.

### 6.16 Pelaporan

Sistem harus menyediakan fitur pelaporan dasar.

Laporan yang tersedia meliputi:

* Riwayat absensi pegawai.
* Rekap absensi harian.
* Rekap absensi per pegawai.
* Rekap absensi per lokasi.
* Rekap keterlambatan.
* Rekap izin dan sakit.
* Rekap tidak hadir.
* Rekap absensi perlu verifikasi.
* Rekap absensi berdasarkan periode tertentu.

---

## 7. Persyaratan Non-Fungsional

Persyaratan non-fungsional sistem meliputi:

* **Keamanan Data:** data pegawai, lokasi, foto absensi, dan riwayat absensi hanya dapat diakses oleh pengguna yang memiliki hak akses.
* **Reliabilitas:** sistem dapat menyimpan data absensi dengan baik agar dapat ditampilkan kembali saat dibutuhkan.
* **Kemudahan Penggunaan:** tampilan pegawai dibuat sederhana agar mudah digunakan oleh pegawai lapangan.
* **Kinerja Sistem:** sistem dapat menampilkan data absensi, lokasi, dan laporan dengan waktu akses yang wajar.
* **Kompatibilitas:** sistem dapat diakses melalui browser modern pada komputer maupun smartphone.
* **Aksesibilitas:** tombol absensi dan form catatan dibuat jelas dan mudah digunakan pada layar smartphone.
* **Auditabilitas Dasar:** perubahan atau koreksi absensi memiliki catatan alasan dan riwayat perubahan.
* **Pemeliharaan:** data pegawai, lokasi kerja, jadwal, dan konfigurasi sistem dapat diperbarui oleh admin tanpa mengubah kode program.
* **Integrasi:** sistem dapat terhubung dengan service pihak ketiga untuk lokasi, penyimpanan foto, atau notifikasi.
* **Privasi Data:** foto dan lokasi absensi digunakan hanya untuk kebutuhan validasi kehadiran dan monitoring operasional.

---

## 8. Arsitektur Tingkat Tinggi

Arsitektur sistem menggunakan pendekatan **modular monolith**. Sistem dibangun dalam satu aplikasi Laravel, tetapi proses bisnis dipisahkan berdasarkan modul agar lebih mudah dikembangkan, diuji, dan dipelihara.

Modul utama sistem meliputi:

* **User Management Module:** mengelola akun, role, dan hak akses pengguna.
* **Employee Module:** mengelola data pegawai dan relasi supervisor.
* **Location Module:** mengelola lokasi kerja dan radius toleransi absensi.
* **Assignment/Schedule Module:** mengelola jadwal kerja atau surat tugas pegawai lapangan.
* **Attendance Module:** mengelola absensi masuk, absensi pulang, status kehadiran, dan status validasi.
* **Attendance Approval Module:** mengelola validasi, penolakan, dan koreksi absensi.
* **Report Module:** mengelola rekap dan laporan absensi.
* **Third Party Integration Module:** mengelola integrasi Google Maps, Cloudinary, atau Telegram Bot.

Teknologi tingkat tinggi:

* Back-end: Laravel
* Panel/Admin UI: Filament
* Database: MariaDB / MySQL
* Front-end: Blade, HTML, CSS, JavaScript
* Web Server: Nginx
* Containerization: Docker
* Version Control: Git dan GitHub
* Development Environment: Visual Studio Code
* Storage: Cloudinary atau storage aplikasi
* Integrasi Pihak Ketiga: Google Maps Platform, Cloudinary, atau Telegram Bot API

---

## 9. Model Data Ringkas

### 9.1 Data Pengguna dan Akses

* users: id, name, email, password, phone_number, role, status, created_at, updated_at

Role pengguna:

* admin
* supervisor
* employee
* management

### 9.2 Data Pegawai

* employees: id, user_id, employee_code, position, status, join_date, supervisor_user_id, created_at, updated_at

Catatan:

* Supervisor adalah user dengan role supervisor.
* Relasi pegawai dengan supervisor disimpan melalui supervisor_user_id.

### 9.3 Data Lokasi Kerja

* work_locations: id, location_name, client_name, address, latitude, longitude, radius_tolerance, status, created_at, updated_at

### 9.4 Data Jadwal atau Surat Tugas

* assignments: id, employee_id, work_location_id, supervisor_user_id, assignment_date, start_time, end_time, title, description, assignment_status, created_at, updated_at

Status assignment:

* terjadwal
* berjalan
* selesai
* dibatalkan

### 9.5 Data Absensi

* attendance_records: id, employee_id, assignment_id, check_in_time, check_out_time, check_in_latitude, check_in_longitude, check_out_latitude, check_out_longitude, check_in_photo_url, check_out_photo_url, attendance_status, verification_status, notes, created_at, updated_at

attendance_status:

* hadir
* terlambat
* izin
* sakit
* tidak_hadir

verification_status:

* valid
* perlu_verifikasi
* di_luar_lokasi
* ditolak
* dikoreksi_manual

### 9.6 Data Validasi Absensi

* attendance_approvals: id, attendance_record_id, approved_by, approval_status, approval_note, approved_at, created_at, updated_at

approval_status:

* disetujui
* ditolak
* perlu_koreksi

### 9.7 Data Koreksi Absensi

* attendance_corrections: id, attendance_record_id, corrected_by, correction_type, old_value, new_value, correction_reason, created_at, updated_at

### 9.8 Data Integrasi Pihak Ketiga

* integration_logs: id, provider_name, action, request_status, response_message, created_at, updated_at

Provider yang dapat digunakan:

* google_maps
* cloudinary
* telegram_bot

---

## 10. Alur Proses Bisnis Ringkas

### 10.1 Alur Persiapan Data

* Admin membuat akun pengguna.
* Admin membuat data pegawai.
* Admin menentukan supervisor pegawai.
* Admin membuat data lokasi kerja.
* Admin menentukan titik koordinat dan radius lokasi kerja.
* Admin atau supervisor membuat jadwal atau surat tugas pegawai.
* Sistem menampilkan jadwal atau surat tugas pada dashboard pegawai.

### 10.2 Alur Absensi Masuk

* Pegawai login ke sistem.
* Pegawai membuka jadwal atau surat tugas hari ini.
* Pegawai menekan tombol Absen Masuk.
* Sistem mencatat waktu absen masuk.
* Sistem mengambil lokasi pegawai.
* Pegawai mengunggah foto absensi.
* Sistem membandingkan lokasi pegawai dengan lokasi kerja.
* Jika lokasi sesuai radius, absensi disimpan dengan status valid.
* Jika lokasi di luar radius atau gagal terbaca, absensi disimpan dengan status perlu verifikasi.
* Supervisor dapat melihat absensi tersebut pada dashboard.

### 10.3 Alur Absensi Pulang

* Pegawai membuka jadwal atau surat tugas yang sedang berjalan.
* Pegawai menekan tombol Absen Pulang.
* Sistem mencatat waktu absen pulang.
* Sistem mengambil lokasi pegawai.
* Pegawai mengunggah foto absen pulang jika diperlukan.
* Sistem menyimpan data absen pulang.
* Jika terjadi masalah lokasi atau data tidak lengkap, absensi ditandai perlu verifikasi.

### 10.4 Alur Validasi Absensi

* Supervisor login ke sistem.
* Supervisor melihat daftar absensi yang perlu verifikasi.
* Supervisor melihat detail waktu, lokasi, foto, dan catatan pegawai.
* Supervisor menyetujui, menolak, atau meminta koreksi.
* Jika disetujui, status validasi menjadi valid.
* Jika ditolak, status validasi menjadi ditolak.
* Jika dikoreksi, status validasi menjadi dikoreksi manual.
* Sistem menyimpan riwayat validasi.

### 10.5 Alur Izin, Sakit, dan Tidak Hadir

* Pegawai menyampaikan izin atau sakit kepada supervisor sesuai prosedur perusahaan.
* Supervisor atau admin menginput status izin atau sakit pada jadwal pegawai.
* Pegawai dengan status izin atau sakit tidak perlu melakukan absensi.
* Jika pegawai tidak melakukan absen dan tidak memiliki izin atau sakit, sistem menandai status sebagai tidak hadir.
* Admin atau supervisor dapat melakukan koreksi jika terjadi kesalahan.

### 10.6 Alur Laporan Absensi

* Admin atau manajemen memilih periode laporan.
* Sistem menampilkan rekap absensi pegawai.
* Sistem menampilkan jumlah hadir, terlambat, izin, sakit, tidak hadir, dan perlu verifikasi.
* Admin dapat memfilter laporan berdasarkan pegawai, lokasi, atau periode.
* Laporan digunakan untuk evaluasi kehadiran pegawai lapangan.

### 10.7 Alur Integrasi Pihak Ketiga

* Saat pegawai melakukan absensi, sistem menggunakan data lokasi untuk mencatat posisi pegawai.
* Jika menggunakan Google Maps, sistem dapat menampilkan titik lokasi absensi dan lokasi kerja pada peta.
* Jika menggunakan Cloudinary, sistem mengunggah foto absensi ke Cloudinary dan menyimpan URL foto ke database.
* Jika menggunakan Telegram Bot, sistem mengirim notifikasi kepada supervisor ketika ada absensi yang perlu verifikasi.

---

## 11. Teknologi

Teknologi yang digunakan dalam pengembangan sistem adalah:

* Back-end: Laravel
* Panel/Admin UI: Filament
* Database: MariaDB / MySQL
* Front-end: Blade, HTML, CSS, JavaScript
* Web Server: Nginx
* Containerization: Docker
* Version Control: Git dan GitHub
* Development Environment: Visual Studio Code
* Local Development URL: https://remote-attendance.test
* Storage: Cloudinary atau storage aplikasi
* Third Party Service:

  * Google Maps Platform untuk dukungan lokasi
  * Cloudinary untuk penyimpanan foto absensi
  * Telegram Bot API untuk notifikasi opsional
* Browser Support: Google Chrome, Microsoft Edge, Mozilla Firefox, dan browser modern lainnya

---

## 12. Asumsi

Asumsi dalam pengembangan sistem ini adalah:

* Pegawai memiliki akses ke smartphone atau perangkat yang dapat membuka browser.
* Pegawai memiliki koneksi internet saat melakukan absensi.
* Pegawai memberikan izin akses lokasi pada browser saat melakukan absensi.
* Pegawai dapat mengunggah foto sebagai bukti absensi.
* Admin memiliki data pegawai dan lokasi kerja.
* Supervisor bertanggung jawab memverifikasi absensi pegawai yang bermasalah.
* Data lokasi digunakan sebagai pendukung validasi, bukan jaminan anti-kecurangan sepenuhnya.
* Jika lokasi tidak terbaca, absensi tetap dapat diajukan dengan status perlu verifikasi.
* Sistem digunakan untuk monitoring kehadiran, bukan untuk menghitung gaji secara otomatis.
* Integrasi pihak ketiga digunakan untuk mendukung proses absensi, bukan sebagai satu-satunya sumber keputusan.
* Keputusan final atas absensi bermasalah tetap berada pada supervisor atau admin.

---

## 13. Risiko dan Mitigasi

| Risiko                                             | Mitigasi                                                                                                                     |
| -------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------- |
| Pegawai tidak terbiasa menggunakan sistem digital. | Tampilan pegawai dibuat sederhana dengan tombol absen masuk dan absen pulang yang jelas.                                     |
| Pegawai tidak memberikan izin lokasi pada browser. | Sistem menampilkan peringatan dan absensi disimpan sebagai perlu verifikasi.                                                 |
| GPS tidak akurat atau lokasi gagal terbaca.        | Sistem menyediakan status perlu verifikasi dan supervisor dapat melakukan validasi manual.                                   |
| Pegawai melakukan absensi di luar lokasi kerja.    | Sistem menandai absensi sebagai di luar lokasi atau perlu verifikasi.                                                        |
| Pegawai lupa melakukan absen masuk atau pulang.    | Supervisor atau admin dapat melakukan koreksi manual dengan catatan alasan.                                                  |
| Pegawai mengunggah foto yang tidak sesuai.         | Supervisor dapat menolak absensi atau meminta koreksi.                                                                       |
| Foto absensi tersimpan tidak aman.                 | Foto dapat disimpan melalui service pihak ketiga seperti Cloudinary atau storage private.                                    |
| Koneksi internet pegawai bermasalah.               | Pegawai dapat menghubungi supervisor, lalu supervisor/admin melakukan koreksi manual jika alasan diterima.                   |
| Supervisor terlalu banyak memverifikasi absensi.   | Sistem hanya menampilkan absensi bermasalah sebagai prioritas verifikasi. Absensi valid tidak perlu diperiksa satu per satu. |
| Data absensi hilang.                               | Data disimpan dalam database dan dapat dilakukan backup berkala.                                                             |
| Integrasi pihak ketiga gagal.                      | Sistem tetap menyimpan data utama absensi dan mencatat kegagalan integrasi pada integration log.                             |
| Scope sistem melebar menjadi HRIS lengkap.         | Sistem dibatasi pada absensi jarak jauh, validasi, dan laporan absensi.                                                      |

---

## 14. Kriteria Penerimaan

Kriteria penerimaan sistem adalah sebagai berikut:

* Pegawai dapat login ke sistem menggunakan akun yang valid.
* Pegawai dapat melihat jadwal atau surat tugas hari ini.
* Pegawai dapat melakukan absensi masuk pada jadwal atau surat tugas aktif.
* Sistem dapat mencatat waktu absensi masuk.
* Sistem dapat mencatat lokasi absensi masuk jika izin lokasi diberikan.
* Pegawai dapat mengunggah foto absensi masuk.
* Jika pegawai berada dalam radius lokasi kerja, sistem menyimpan status validasi sebagai valid.
* Jika pegawai berada di luar radius lokasi kerja, sistem menyimpan status validasi sebagai perlu verifikasi atau di luar lokasi.
* Pegawai dapat melakukan absensi pulang.
* Sistem dapat mencatat waktu absensi pulang.
* Sistem dapat menyimpan foto absensi pulang jika diperlukan.
* Supervisor dapat melihat daftar absensi yang perlu verifikasi.
* Supervisor dapat menyetujui absensi yang perlu verifikasi.
* Supervisor dapat menolak absensi yang tidak sesuai.
* Supervisor atau admin dapat melakukan koreksi absensi dengan catatan alasan.
* Admin dapat mengelola data pegawai.
* Admin dapat mengelola data lokasi kerja.
* Admin dapat mengelola jadwal atau surat tugas pegawai.
* Admin dapat melihat seluruh data absensi.
* Manajemen dapat melihat laporan absensi berdasarkan periode.
* Sistem dapat menampilkan rekap hadir, terlambat, izin, sakit, tidak hadir, dan perlu verifikasi.
* Sistem dapat membatasi akses berdasarkan role pengguna.
* Sistem dapat terhubung dengan minimal satu service pihak ketiga.
* Data absensi yang tersimpan dapat ditampilkan kembali saat dibutuhkan.

---

## 15. Diagram Use Case

Aktor utama dalam sistem:

* Pegawai Lapangan
* Supervisor
* Admin/HR
* Manajemen/Pemilik

### Use Case Pegawai Lapangan

* Login ke sistem.
* Melihat jadwal atau surat tugas.
* Melakukan absensi masuk.
* Mengunggah foto absensi masuk.
* Melakukan absensi pulang.
* Mengunggah foto absensi pulang.
* Mengisi catatan kendala absensi.
* Melihat riwayat absensi pribadi.

### Use Case Supervisor

* Login ke sistem.
* Melihat daftar pegawai.
* Melihat absensi pegawai.
* Melihat absensi yang perlu verifikasi.
* Melihat lokasi dan foto absensi.
* Menyetujui absensi.
* Menolak absensi.
* Melakukan koreksi absensi.
* Melihat rekap absensi pegawai.

### Use Case Admin/HR

* Login ke sistem.
* Mengelola data pengguna.
* Mengelola data pegawai.
* Mengelola data lokasi kerja.
* Mengelola jadwal atau surat tugas.
* Mengelola konfigurasi service pihak ketiga.
* Melihat seluruh data absensi.
* Melakukan koreksi absensi.
* Melihat laporan absensi.

### Use Case Manajemen/Pemilik

* Login ke sistem.
* Melihat ringkasan kehadiran.
* Melihat laporan absensi.
* Melihat absensi berdasarkan lokasi.
* Melihat absensi berdasarkan periode.
