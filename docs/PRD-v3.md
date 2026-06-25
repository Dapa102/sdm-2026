# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## SISTEM SUMBER DAYA MANUSIA TERINTEGRASI
### Versi 3.0 - Implementasi Modular 8 Layanan SDM

---

| **Dokumen** | **Keterangan** |
| :--- | :--- |
| **Nama Dokumen** | Product Requirements Document - Sistem SDM Terintegrasi |
| **Versi** | 3.0 |
| **Tanggal** | 18 Juni 2026 |
| **Acuan** | BRD v3.0 |
| **Audience** | Engineering, QA, UI/UX, Product Owner, HR Stakeholder |
| **Platform Target** | Web Responsive |
| **Base Stack** | Laravel 12 + Filament 5 + Livewire + PostgreSQL/MySQL |
| **Status** | Draft Teknis untuk Pengembangan Bertahap |

---

## 1. PENDAHULUAN

PRD ini menerjemahkan BRD v3.0 menjadi spesifikasi produk dan teknis untuk membangun Sistem SDM Terintegrasi berbasis 8 layanan domain. Sistem tetap direkomendasikan sebagai **modular monolith** di Laravel, dengan pemisahan domain melalui service class, model, resource Filament, policy, event, job, dan konfigurasi.

Tujuan PRD v3.0 adalah memastikan sistem dapat:

- Mendukung MVP yang sudah ada: Surat Tugas, absensi dinas luar, merit, reward, training, dan approval.
- Berkembang ke modul SDM yang lebih lengkap: attendance reguler, leave, payroll, benefit, reporting.
- Fleksibel untuk kebutuhan perusahaan yang berbeda-beda melalui konfigurasi, custom fields, workflow, dan provider/plugin.
- Menghindari hardcode aturan perusahaan tertentu di dalam business logic inti.

---

## 2. PRINSIP PRODUK

| **Prinsip** | **Implikasi Implementasi** |
| :--- | :--- |
| Core domain harus stabil | Entitas utama seperti User, Employee, Attendance, Payroll, Merit, Reward, Training harus terstruktur jelas. |
| Rule harus configurable | Point, approval, leave type, payroll component, radius absensi, dan expiry point tidak boleh terkunci di kode. |
| Integrasi harus berbasis provider | Storage, map, notification, payroll export, dan SSO disediakan lewat contract/interface. |
| Data sensitif harus dilindungi | Payroll, dokumen karyawan, dan slip gaji harus memakai permission ketat dan audit log. |
| MVP tetap sederhana | Fitur yang belum kritis masuk Fase 2/Fase 3 agar rilis awal tetap realistis. |

---

## 3. RUANG LINGKUP PRODUK

### 3.1 MVP

| **Layanan** | **Fitur MVP** |
| :--- | :--- |
| Employee & Organization | User, role, permission, manager, data dasar karyawan |
| Travel Duty | Surat Tugas, check-in/out dinas luar, GPS, foto, approval |
| Merit & Performance | Point otomatis, histori point, expiry sederhana |
| Rewards & Benefits | Katalog reward, pengajuan reward, approval, deduct point |
| Learning & Career | Katalog training, rekomendasi sederhana, enrollment, completion point |
| Workflow, Notification & Reporting | Approval dasar, audit trail, dashboard per role |

### 3.2 Fase 2

| **Layanan** | **Fitur Fase 2** |
| :--- | :--- |
| Attendance & Leave | Absensi kantor, cuti, izin, sakit, lembur |
| Payroll & Compensation | Master komponen gaji, data kompensasi karyawan, periode payroll draft |
| Rewards & Benefits | Benefit karyawan, fulfillment reward |
| Workflow, Notification & Reporting | Email/push notification, export Excel/PDF, laporan lintas modul |

### 3.3 Fase 3

| **Layanan** | **Fitur Fase 3** |
| :--- | :--- |
| Payroll & Compensation | Perhitungan payroll penuh, slip gaji, payroll approval, export bank |
| Merit & Performance | Indikator performa lanjutan |
| Learning & Career | Jalur karir dan kompetensi |
| Workflow, Notification & Reporting | Report builder sederhana, dashboard manajemen |

---

## 4. ROLE & AKSES

| **Role** | **Deskripsi** | **Akses Utama** |
| :--- | :--- | :--- |
| Super Admin | Role teknis tertinggi | Semua resource, konfigurasi, role, permission, audit |
| Admin HR | Pengelola operasional HR | Employee, ST, training, reward, benefit, laporan HR |
| HR Payroll | Pengelola payroll | Komponen gaji, periode payroll, slip gaji, export payroll |
| Manajer | Atasan langsung | Tim sendiri, Surat Tugas bawahan, approval bawahan |
| Karyawan | Pengguna umum | Data pribadi, dinas aktif, absensi, point, reward, training, slip gaji pribadi |

Catatan implementasi:

- Role awal dibuat melalui seeder.
- Permission dibuat granular per resource/action.
- Relasi bawahan menggunakan `users.manager_id`.
- Semua policy harus mempertimbangkan ownership, manager relationship, dan role.

---

## 5. ARSITEKTUR APLIKASI

### 5.1 Modular Monolith

```txt
app/
├── Contracts/
│   ├── MapProvider.php
│   ├── NotificationProvider.php
│   ├── PayrollExporter.php
│   └── StorageProvider.php
├── Services/
│   ├── EmployeeService.php
│   ├── AttendanceService.php
│   ├── TravelDutyService.php
│   ├── PayrollService.php
│   ├── MeritService.php
│   ├── RewardService.php
│   ├── LearningCareerService.php
│   └── WorkflowService.php
├── Integrations/
│   ├── Maps/
│   ├── Notifications/
│   ├── Payroll/
│   └── Storage/
├── Filament/
│   └── Admin/
│       ├── Resources/
│       ├── Pages/
│       └── Widgets/
├── Models/
├── Policies/
├── Jobs/
├── Events/
└── Listeners/
```

### 5.2 Provider / Plugin Layer

Domain service tidak boleh langsung bergantung ke vendor. Semua vendor atau layanan eksternal harus lewat contract.

```php
interface NotificationProvider
{
    public function send(string $recipient, string $subject, string $message): void;
}
```

Contoh implementasi:

```txt
LogNotificationProvider
BrevoNotificationProvider
FirebaseNotificationProvider
WhatsappNotificationProvider
```

Provider awal yang direkomendasikan untuk MVP:

| **Kebutuhan** | **Provider Default** | **Provider Alternatif** |
| :--- | :--- | :--- |
| GPS | Browser Geolocation API | Google Maps Geolocation |
| Peta | Leaflet + OpenStreetMap | Google Maps |
| Storage | Local disk | Cloudflare R2 / MinIO / S3 |
| Email | Log mail / SMTP | Brevo / Mailgun / SES |
| Push | Disabled | Firebase Cloud Messaging |
| Payroll export | CSV | Bank template / payout API |

---

## 6. KONFIGURASI FLEKSIBEL

Sistem harus mendukung kebutuhan perusahaan yang berbeda-beda melalui konfigurasi database.

| **Area** | **Konfigurasi** | **Contoh** |
| :--- | :--- | :--- |
| Company | `company_settings` | Nama perusahaan, zona waktu, logo, mata uang |
| Organization | `departments`, `positions`, `employee_grades` | Divisi IT, HR, Finance; grade Staff/Lead/Manager |
| Attendance | `attendance_policies` | Radius kantor, jam kerja, toleransi terlambat |
| Leave | `leave_types` | Cuti tahunan, sakit, izin, unpaid leave |
| Approval | `approval_workflows`, `approval_steps` | Manager -> HR -> Finance |
| Merit | `merit_rules` | +10 point dinas, +25 point training |
| Payroll | `payroll_components`, `payroll_component_rules` | Gaji pokok, tunjangan, potongan, lembur |
| Custom Data | `custom_field_definitions` | NPWP, BPJS, nomor rekening, ukuran seragam |
| Integration | `integration_settings` | Provider aktif dan credential reference |

Aturan:

- Konfigurasi sensitif seperti API key tidak disimpan plaintext.
- Nilai konfigurasi harus punya default yang aman.
- Perubahan konfigurasi penting wajib masuk audit log.

---

## 7. SPESIFIKASI FUNGSIONAL PER LAYANAN

### 7.1 Employee & Organization Service

**Tujuan:** Menjadi master data karyawan dan struktur organisasi.

| **ID** | **Fitur** | **Implementasi** | **Prioritas** |
| :--- | :--- | :--- | :--- |
| EO-01 | CRUD Karyawan | `UserResource` dan/atau `EmployeeResource` | MVP |
| EO-02 | Relasi Manager | Field `manager_id` pada user | MVP |
| EO-03 | Role & Permission | Filament Shield | MVP |
| EO-04 | Department & Position | Resource `DepartmentResource`, `PositionResource` | Fase 2 |
| EO-05 | Dokumen Karyawan | Private storage + policy khusus | Fase 2 |
| EO-06 | Custom Fields | Dynamic form dari `custom_field_definitions` | Fase 2 |

Acceptance criteria:

- Admin HR dapat membuat dan mengubah data karyawan.
- Manajer hanya dapat melihat data bawahan sesuai policy.
- Karyawan hanya dapat melihat data pribadinya.
- Super Admin dapat mengatur role dan permission.

---

### 7.2 Travel Duty Service

**Tujuan:** Mengelola Surat Tugas dan absensi dinas luar.

| **ID** | **Fitur** | **Implementasi** | **Prioritas** |
| :--- | :--- | :--- | :--- |
| TD-01 | CRUD Surat Tugas | `SuratTugasResource` | MVP |
| TD-02 | Upload Dokumen ST | Filament FileUpload ke storage | MVP |
| TD-03 | Check-in Dinas | Custom Page/Livewire + `TravelDutyService` | MVP |
| TD-04 | Check-out Dinas | Custom Page/Livewire + `TravelDutyService` | MVP |
| TD-05 | Validasi Radius | Haversine calculation | MVP |
| TD-06 | Approval Dinas | Action approve/reject pada `AttendanceLogResource` | MVP |
| TD-07 | Rekap Dinas | Table filter + export | Fase 2 |

Business rules:

- Karyawan tidak bisa check-in tanpa ST aktif.
- ST aktif jika `status = ACTIVE` dan tanggal hari ini berada di antara `start_date` dan `end_date`.
- Satu ST hanya boleh memiliki satu attendance log per tanggal.
- Jika lokasi di luar radius, check-in tetap disimpan dengan status `OUT_OF_RANGE` dan perlu verifikasi manager.
- Check-out hanya boleh dilakukan setelah check-in dan minimal 7 jam dari check-in.
- Approval manager pada absensi dinas akan memicu point merit.

Acceptance criteria:

- Karyawan bisa melihat ST aktif miliknya.
- Karyawan bisa check-in menggunakan GPS dan foto.
- Karyawan bisa check-out di hari yang sama.
- Manager bisa approve/reject absensi bawahan.
- Approved attendance menghasilkan transaksi point.

---

### 7.3 Merit & Performance Service

**Tujuan:** Mengelola point merit dan histori performa sederhana.

| **ID** | **Fitur** | **Implementasi** | **Prioritas** |
| :--- | :--- | :--- | :--- |
| MP-01 | Tambah Point Dinas | `MeritService::addPoints()` dari event approval | MVP |
| MP-02 | Tambah Point Training | Event training completed | MVP |
| MP-03 | Histori Point | `MeritTransactionResource` view-only untuk karyawan | MVP |
| MP-04 | Saldo Point | Query agregat transaksi aktif | MVP |
| MP-05 | Expiry Point | Scheduler bulanan | MVP |
| MP-06 | Merit Rules | `merit_rules` configurable | Fase 2 |
| MP-07 | Adjustment Point | Action Admin HR dengan alasan | Fase 2 |

Business rules:

- Point positif memiliki `expiry_date`.
- Point reward disimpan sebagai transaksi negatif.
- Transaksi point tidak boleh diedit setelah dibuat.
- Jika sumber transaksi sama, sistem harus mencegah duplikasi point.

Acceptance criteria:

- Approval dinas memberi point sesuai rule aktif.
- Training selesai memberi point sesuai rule aktif.
- Karyawan dapat melihat saldo dan histori point.
- Point expired tidak dihitung dalam saldo aktif.

---

### 7.4 Rewards & Benefits Service

**Tujuan:** Mengelola reward berbasis point dan benefit karyawan.

| **ID** | **Fitur** | **Implementasi** | **Prioritas** |
| :--- | :--- | :--- | :--- |
| RB-01 | CRUD Reward Catalog | `RewardCatalogResource` | MVP |
| RB-02 | Pengajuan Reward | `RewardRequestResource` | MVP |
| RB-03 | Validasi Saldo | `RewardService::validateBalance()` | MVP |
| RB-04 | Approval Reward | Action approve/reject | MVP |
| RB-05 | Deduct Point | `MeritService::deductPoints()` | MVP |
| RB-06 | Benefit Karyawan | `BenefitResource` | Fase 2 |
| RB-07 | Fulfillment Reward | Status processing/completed | Fase 2 |

Business rules:

- Reward hanya bisa diajukan jika status reward aktif.
- Point belum dikurangi saat request masih `PENDING`.
- Point hanya dikurangi ketika request `APPROVED`.
- Request yang sudah approved/rejected tidak boleh diproses ulang.

Acceptance criteria:

- Karyawan bisa melihat reward aktif.
- Karyawan hanya bisa submit jika saldo cukup.
- Manager bisa approve/reject reward bawahan.
- Approval reward membuat transaksi point negatif.

---

### 7.5 Learning & Career Service

**Tujuan:** Mengelola training, enrollment, rekomendasi, dan point completion.

| **ID** | **Fitur** | **Implementasi** | **Prioritas** |
| :--- | :--- | :--- | :--- |
| LC-01 | CRUD Training | `TrainingResource` | MVP |
| LC-02 | Rekomendasi Training | `LearningCareerService::recommendTrainings()` | MVP |
| LC-03 | Enrollment Training | `TrainingEnrollmentResource` | MVP |
| LC-04 | Completion Training | Action "Tandai Selesai" | MVP |
| LC-05 | Completion Point | Event training completed -> Merit | MVP |
| LC-06 | Riwayat Pembelajaran | View enrollment per user | Fase 2 |
| LC-07 | Career Path | Resource kompetensi/jalur karir | Fase 3 |

Business rules:

- Karyawan hanya bisa daftar training aktif.
- Jika training punya `min_points_required`, saldo point aktif harus memenuhi syarat.
- Kombinasi `user_id` dan `training_id` harus unik untuk enrollment aktif.
- Completion hanya bisa dilakukan oleh Admin HR atau role yang diberi permission.

Acceptance criteria:

- Admin HR dapat membuat training.
- Karyawan melihat training yang eligible.
- Karyawan dapat daftar training.
- Training completion memberi point sesuai rule.

---

### 7.6 Attendance & Leave Service

**Tujuan:** Mengelola absensi reguler, cuti, izin, sakit, dan lembur.

| **ID** | **Fitur** | **Implementasi** | **Prioritas** |
| :--- | :--- | :--- | :--- |
| AL-01 | Absensi Kantor | `AttendanceRecordResource` + custom check-in page | Fase 2 |
| AL-02 | Attendance Policy | `AttendancePolicyResource` | Fase 2 |
| AL-03 | Leave Type | `LeaveTypeResource` | Fase 2 |
| AL-04 | Pengajuan Cuti/Izin/Sakit | `LeaveRequestResource` | Fase 2 |
| AL-05 | Approval Cuti | Workflow approval | Fase 2 |
| AL-06 | Lembur | `OvertimeRequestResource` | Fase 2 |
| AL-07 | Rekap Attendance | Export/report | Fase 2 |

Business rules:

- Jenis cuti dan kuota cuti harus configurable.
- Approval cuti mengikuti workflow aktif perusahaan.
- Data absensi dan lembur dapat menjadi input payroll.
- Attendance reguler tidak boleh bercampur dengan attendance dinas luar tanpa tipe yang jelas.

Acceptance criteria:

- Admin HR dapat mengatur tipe cuti.
- Karyawan dapat mengajukan cuti.
- Manager dapat approve/reject cuti bawahan.
- Rekap attendance dapat difilter per periode dan karyawan.

---

### 7.7 Payroll & Compensation Service

**Tujuan:** Mengelola komponen gaji, periode payroll, perhitungan, approval, slip gaji, dan export.

| **ID** | **Fitur** | **Implementasi** | **Prioritas** |
| :--- | :--- | :--- | :--- |
| PC-01 | Master Payroll Component | `PayrollComponentResource` | Fase 2 |
| PC-02 | Employee Compensation | `EmployeeCompensationResource` | Fase 2 |
| PC-03 | Payroll Period | `PayrollPeriodResource` | Fase 2 |
| PC-04 | Generate Payroll Draft | `PayrollService::generateDraft()` | Fase 2/Fase 3 |
| PC-05 | Payroll Review | Table detail per karyawan | Fase 3 |
| PC-06 | Payroll Approval | Workflow HR Payroll -> Finance/Management | Fase 3 |
| PC-07 | Slip Gaji | Private PDF/view | Fase 3 |
| PC-08 | Export Payroll | `PayrollExporter` provider | Fase 3 |

Business rules:

- Payroll hanya membaca snapshot dari attendance, travel duty, benefit, dan compensation.
- Payroll tidak boleh mengubah data sumber.
- Periode payroll memiliki status: `DRAFT`, `REVIEW`, `APPROVED`, `PAID`, `LOCKED`.
- Periode `LOCKED` tidak boleh diedit langsung.
- Koreksi payroll dilakukan melalui adjustment di periode berikutnya.
- Akses payroll hanya untuk HR Payroll, Super Admin, dan karyawan pemilik slip.

Acceptance criteria:

- HR Payroll dapat membuat periode payroll.
- HR Payroll dapat mengatur komponen gaji.
- Draft payroll dapat dibuat dari data kompensasi karyawan.
- Payroll locked tidak dapat diubah tanpa adjustment.

---

### 7.8 Workflow, Notification & Reporting Service

**Tujuan:** Menyediakan approval workflow, notifikasi, audit trail, dashboard, dan laporan.

| **ID** | **Fitur** | **Implementasi** | **Prioritas** |
| :--- | :--- | :--- | :--- |
| WNR-01 | Approval Workflow Dasar | `WorkflowService` + resource action | MVP |
| WNR-02 | Approval Config | `ApprovalWorkflowResource` | Fase 2 |
| WNR-03 | Audit Trail | Filament Logger / activity log | MVP |
| WNR-04 | Dashboard Karyawan | Filament Page/Widget | MVP |
| WNR-05 | Dashboard Manager | Pending approval, tim, dinas aktif | MVP |
| WNR-06 | Dashboard HR | Ringkasan karyawan, ST, point, training | MVP |
| WNR-07 | Notification Provider | Email/log/push via provider | Fase 2 |
| WNR-08 | Export Laporan | Excel/PDF | Fase 2 |
| WNR-09 | Report Builder | Query/report builder sederhana | Fase 3 |

Business rules:

- Approval harus mencatat approver, waktu, status, dan alasan rejection.
- Notifikasi gagal tidak boleh membatalkan transaksi utama.
- Semua event penting harus masuk audit log.
- Dashboard hanya menampilkan data sesuai role dan policy.

Acceptance criteria:

- Manager melihat daftar approval pending bawahan.
- Karyawan melihat status pengajuan sendiri.
- Admin HR melihat ringkasan lintas modul.
- Semua approve/reject tercatat di audit.

---

## 8. DATA MODEL AWAL

### 8.1 MVP Tables

```txt
users
roles
permissions
model_has_roles
model_has_permissions
departments
positions
surat_tugas
attendance_logs
merit_transactions
reward_catalogs
reward_requests
trainings
training_enrollments
approval_records
company_settings
integration_settings
activity_log
```

### 8.2 Fase 2/Fase 3 Tables

```txt
attendance_policies
attendance_records
leave_types
leave_balances
leave_requests
overtime_requests
benefits
employee_benefits
payroll_components
payroll_component_rules
employee_compensations
payroll_periods
payroll_runs
payroll_run_items
payroll_adjustments
payroll_slips
approval_workflows
approval_steps
custom_field_definitions
custom_field_values
```

### 8.3 Status Enum Rekomendasi

```txt
surat_tugas.status:
- ACTIVE
- EXPIRED
- CANCELLED

attendance_logs.location_status:
- VALID
- OUT_OF_RANGE

approval_status:
- PENDING
- APPROVED
- REJECTED

reward_requests.status:
- PENDING
- APPROVED
- REJECTED
- FULFILLED

training_enrollments.status:
- REGISTERED
- COMPLETED
- DROPPED

payroll_periods.status:
- DRAFT
- REVIEW
- APPROVED
- PAID
- LOCKED
```

Catatan: enum yang berpotensi berbeda per perusahaan sebaiknya menjadi tabel konfigurasi, bukan enum database permanen.

---

## 9. EVENT & INTEGRASI INTERNAL

| **Event** | **Trigger** | **Listener / Efek** |
| :--- | :--- | :--- |
| `AttendanceApproved` | Manager approve absensi dinas | Tambah point dinas, kirim notifikasi |
| `AttendanceRejected` | Manager reject absensi dinas | Simpan alasan, kirim notifikasi |
| `RewardApproved` | Manager approve reward | Deduct point, update status |
| `RewardRejected` | Manager reject reward | Kirim notifikasi |
| `TrainingCompleted` | HR menandai training selesai | Tambah point training |
| `PayrollPeriodLocked` | Payroll dikunci | Generate slip final, audit |
| `ApprovalRequested` | Pengajuan dibuat | Buat approval record, kirim notifikasi |

Aturan teknis:

- Event yang mengubah data finansial atau point harus idempotent.
- Listener yang memanggil provider eksternal sebaiknya berjalan sebagai queued job.
- Kegagalan notifikasi tidak boleh rollback transaksi utama.

---

## 10. UI/UX REQUIREMENTS

### 10.1 Prinsip UI

- Gunakan Filament Resource untuk CRUD dan workflow administratif.
- Gunakan Custom Page/Livewire untuk flow khusus seperti check-in/out GPS dan kamera.
- Dashboard role-based harus menampilkan action utama sesuai kebutuhan user.
- Mobile responsive wajib untuk karyawan yang melakukan check-in/out dari lapangan.

### 10.2 Dashboard Per Role

| **Role** | **Widget Utama** |
| :--- | :--- |
| Karyawan | Dinas aktif, tombol check-in/out, saldo point, reward request, training recommendation |
| Manajer | Pending approval absensi, pending reward, tim saya, shortcut buat ST |
| Admin HR | Total karyawan, ST aktif, top point, training enrollment, laporan HR |
| HR Payroll | Payroll period, payroll draft, pending review, payroll locked |
| Super Admin | Audit activity, user/role summary, system settings |

### 10.3 Mobile Flow Check-in/out

1. Karyawan login.
2. Karyawan membuka halaman Dinas Aktif.
3. Sistem menampilkan ST aktif milik user.
4. User menekan Check-in.
5. Browser meminta permission lokasi.
6. Browser meminta permission kamera.
7. Sistem menampilkan preview foto.
8. User submit.
9. Sistem menyimpan log dengan status approval `PENDING`.

---

## 11. NON-FUNCTIONAL REQUIREMENTS

| **Kategori** | **Requirement** |
| :--- | :--- |
| Performance | Operasi utama kurang dari 3 detik, kecuali upload foto/file. |
| Security | Semua halaman admin wajib authentication. |
| Authorization | Semua resource wajib policy. |
| Audit | Semua approval, payroll, point, dan perubahan konfigurasi wajib tercatat. |
| Privacy | Dokumen karyawan dan slip gaji disimpan private. |
| Storage | File public dan private dipisahkan. |
| Reliability | Provider eksternal harus punya fallback/logging. |
| Browser | Check-in/out membutuhkan HTTPS, Geolocation API, dan camera access. |
| Data Integrity | Transaksi point dan payroll harus idempotent dan tidak boleh double process. |
| Backup | Database dan storage production wajib punya strategi backup. |

---

## 12. ACCEPTANCE CRITERIA MVP

| **No** | **Kriteria** | **Target** |
| :--- | :--- | :--- |
| 1 | Admin HR dapat mengelola data karyawan dasar | Berhasil create/update/list user |
| 2 | Admin HR/Manager dapat membuat Surat Tugas | ST aktif muncul pada dashboard karyawan |
| 3 | Karyawan dapat check-in dinas luar | GPS, foto, tanggal, dan status tersimpan |
| 4 | Karyawan dapat check-out dinas luar | Check-out minimal 7 jam setelah check-in |
| 5 | Manager dapat approve/reject absensi | Status berubah dan alasan reject tersimpan |
| 6 | Approval absensi menambah point | Merit transaction dibuat satu kali |
| 7 | Karyawan dapat mengajukan reward | Request tersimpan sebagai pending |
| 8 | Approval reward mengurangi point | Merit transaction negatif dibuat satu kali |
| 9 | Admin HR dapat membuat training | Training muncul untuk karyawan eligible |
| 10 | Training selesai menambah point | Merit transaction training dibuat satu kali |
| 11 | Dashboard berbeda per role | Data yang tampil sesuai permission |
| 12 | Audit trail aktif | Event penting tercatat |

---

## 13. TESTING REQUIREMENTS

### 13.1 Feature Tests

| **Area** | **Test** |
| :--- | :--- |
| RBAC | User hanya dapat mengakses resource sesuai role |
| Travel Duty | Check-in tanpa ST aktif ditolak |
| Travel Duty | Check-in duplikat tanggal yang sama ditolak |
| Travel Duty | Check-out kurang dari 7 jam ditolak |
| Merit | Approval attendance menambah point satu kali |
| Reward | Reward request saldo kurang ditolak |
| Reward | Approval reward deduct point satu kali |
| Training | Completion training menambah point satu kali |
| Payroll | Locked period tidak bisa diedit |
| Workflow | Reject wajib menyimpan alasan |

### 13.2 Manual QA

- Test check-in/out di mobile browser.
- Test permission kamera dan GPS ditolak.
- Test upload foto besar.
- Test user manager melihat hanya bawahan.
- Test dashboard role Karyawan, Manajer, Admin HR, HR Payroll, Super Admin.

---

## 14. IMPLEMENTATION ROADMAP

### Sprint Group 1 - Foundation

- Role, permission, seeders.
- Employee profile dasar.
- Manager relationship.
- Company settings dasar.
- Activity log.

### Sprint Group 2 - Travel Duty MVP

- Surat Tugas resource.
- Attendance log model/resource.
- Check-in/out page.
- GPS/foto validation.
- Manager approval.

### Sprint Group 3 - Merit, Reward, Training

- Merit service dan transaction.
- Reward catalog dan request.
- Training catalog dan enrollment.
- Event integration antar domain.

### Sprint Group 4 - Dashboard & Reporting MVP

- Dashboard per role.
- Pending approval widget.
- Point summary widget.
- Basic export/filter.

### Sprint Group 5 - Fase 2 Foundation

- Attendance policy.
- Leave type/request.
- Payroll component.
- Benefit.
- Configurable workflow.

### Sprint Group 6 - Payroll & Advanced Reporting

- Payroll period.
- Payroll draft.
- Payroll approval.
- Slip gaji.
- Export payroll.
- Report builder.

---

## 15. RISIKO & MITIGASI

| **Risiko** | **Dampak** | **Mitigasi** |
| :--- | :--- | :--- |
| Terlalu banyak scope di awal | MVP terlambat | Tetapkan MVP sesuai fase dan tunda payroll penuh ke Fase 3 |
| Rule terlalu hardcoded | Sulit dipakai perusahaan lain | Simpan rule di tabel konfigurasi |
| Payroll salah hitung | Risiko finansial tinggi | Snapshot input, approval, locked period, audit, test ketat |
| Provider eksternal gagal | Notifikasi/storage terganggu | Queue, retry, fallback provider |
| Data payroll bocor | Risiko privacy tinggi | Policy ketat, private storage, audit |
| GPS tidak akurat | Approval terganggu | Simpan status `OUT_OF_RANGE`, tetap izinkan check-in untuk verifikasi |

---

## 16. OPEN QUESTIONS

| **Area** | **Pertanyaan** |
| :--- | :--- |
| Organization | Apakah perusahaan membutuhkan cabang/lokasi kerja sejak MVP? |
| Attendance | Apakah absensi kantor wajib di Fase 2 atau bisa setelah payroll? |
| Payroll | Apakah pajak/BPJS dihitung sistem atau hanya input manual pada awalnya? |
| Approval | Apakah approval cukup manager langsung pada MVP? |
| Notification | Apakah email cukup untuk MVP, atau perlu push/WhatsApp? |
| Storage | Apakah storage production memakai local, R2, MinIO, atau S3? |
| Training | Apakah cukup katalog internal atau perlu integrasi LMS seperti Moodle? |

---

## 17. KESIMPULAN

PRD v3.0 mengarahkan Sistem SDM Terintegrasi menjadi platform modular yang tetap sederhana untuk MVP, tetapi siap berkembang menjadi sistem HR yang fleksibel. Core domain dibangun di Laravel + Filament, aturan bisnis dibuat configurable, dan integrasi eksternal dibungkus provider/plugin agar dapat diganti tanpa mengubah business logic utama.

Prioritas implementasi tetap dimulai dari fondasi data karyawan, Travel Duty, Merit, Reward, Training, Workflow, dan Dashboard. Attendance reguler, leave, payroll, benefit, notification lanjutan, dan reporting lanjutan masuk fase berikutnya.
