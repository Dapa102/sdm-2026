# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## SISTEM SUMBER DAYA MANUSIA TERINTEGRASI (MVP)
### Versi 2.0 – Fokus Pengembangan Web dengan Laravel Filament

---

| **Dokumen** | **Keterangan** |
| :--- | :--- |
| **Nama Dokumen** | Product Requirements Document (PRD) - SDM Terintegrasi MVP |
| **Versi** | 2.0 |
| **Tanggal** | 19 Juni 2026 |
| **Acuan** | BRD v2.0 (tanggal 18 Juni 2026) |
| **Audience** | Tim Engineering (Backend, Frontend, QA), UI/UX Designer, Tech Lead |
| **Platform Target** | **Web Only** (Desktop + Mobile Responsive) |
| **Base Stack** | **Laravel 12 + Filament 5 (Fullstack)** dengan boilerplate [FilaStarter](https://github.com/raugadh/fila-starter) |
| **Status** | Ready for Development |

---

## 1. PENDAHULUAN & TUJUAN

PRD ini adalah turunan teknis dari BRD yang telah disusun sebelumnya. Berbeda dengan PRD versi sebelumnya, dokumen ini **dikhususkan untuk pengembangan berbasis website** dengan memanfaatkan **Fullstack Laravel + Filament** menggunakan boilerplate **FilaStarter**.

### 1.1. Mengapa Web First?

| **Pertimbangan** | **Penjelasan** |
| :--- | :--- |
| **GPS & Kamera** | Web modern sudah mendukung GPS (HTTPS + Geolocation API) dan akses kamera via `getUserMedia()`, sehingga tidak ada hambatan teknis untuk absensi berbasis lokasi dan foto. |
| **Responsive Design** | Filament secara native mendukung tampilan responsive untuk desktop, tablet, dan mobile. |
| **Satu Kode Base** | Dengan pendekatan web, tidak perlu mengembangkan dan memelihara dua aplikasi terpisah (Android & iOS). |
| **Kecepatan Development** | Filament menyediakan komponen admin panel yang siap pakai (Resources, Forms, Tables, Widgets), sehingga development bisa lebih cepat. |

### 1.2. Teknologi yang Digunakan

| **Lapisan** | **Teknologi** | **Keterangan** |
| :--- | :--- | :--- |
| **Framework** | Laravel 12 | Backend MVC, Eloquent ORM, Routing, Authentication, Queue, Scheduler |
| **Admin Panel** | Filament 5 | Panel Admin dengan Theme Shadcn, Resource Management, Form Builder, Table Builder, Widgets |
| **Frontend UI** | Livewire 4 + Blade | Komponen reaktif tanpa JavaScript kompleks, integrasi native dengan Laravel |
| **Database** | PostgreSQL / MySQL | Sesuai kebutuhan (Eloquent siap pakai) |
| **Authentication** | Filament Breezy | Login, Registrasi, Reset Password, Profile Management |
| **Authorization** | Filament Shield | Role-Based Access Control (RBAC) berbasis Policy dan Permission |
| **Activity Log** | Filament Logger | Audit trail otomatis untuk semua aktivitas pengguna |
| **Asset Build** | Vite + NPM | Untuk kompilasi CSS/JS (termasuk tema Shadcn) |
| **Development** | Laravel Debugbar | Debugging dan profiling |

---

## 2. USER ROLES & PERMISSIONS (BERBASIS FILAMENT SHIELD)

FilaStarter telah menyediakan **Filament Shield** untuk manajemen roles dan permissions secara visual di dalam panel admin. Berikut adalah roles yang wajib dibuat di awal pengembangan:

| **Role** | **Deskripsi** | **Akses Panel Filament** |
| :--- | :--- | :--- |
| **Super Admin** | Hak akses penuh ke seluruh sistem | Semua Resource, semua Page, manajemen User & Role via Shield |
| **Admin HR** | Mengelola data karyawan, Surat Tugas, Pelatihan, dan melihat semua laporan | Resource: Users (view only untuk karyawan), SuratTugasResource (full), TrainingResource (full), RewardCatalogResource (full). Page: Dashboard HR |
| **Manajer** | Membuat Surat Tugas untuk bawahan, menyetujui absensi dan pengajuan reward bawahan | Resource: SuratTugasResource (create for subordinates), AttendanceApprovalResource (approve/reject), RewardApprovalResource (approve/reject). Page: Dashboard Manajer |
| **Karyawan** | Melakukan check-in/out, melihat point, mengajukan reward, daftar pelatihan | Resource: AttendanceResource (create check-in/out only), MeritResource (view only), RewardRequestResource (create only), TrainingEnrollmentResource (create only). Page: Dashboard Karyawan |

> **Catatan Implementasi:** Shield memungkinkan pembuatan permission granular (misal: `view_any_surat_tugas`, `create_surat_tugas`, `update_surat_tugas`). Gunakan fitur ini untuk mengontrol akses sesuai tabel di atas.

---

## 3. ARSITEKTUR APLIKASI

### 3.1. Struktur Direktori Utama (Berbasis FilaStarter)

```
app/
├── Filament/
│   ├── Admin/                          # Panel Admin (default)
│   │   ├── Resources/                  # Filament Resources
│   │   │   ├── UserResource.php
│   │   │   ├── SuratTugasResource.php
│   │   │   ├── AttendanceLogResource.php
│   │   │   ├── MeritTransactionResource.php
│   │   │   ├── RewardCatalogResource.php
│   │   │   ├── RewardRequestResource.php
│   │   │   ├── TrainingResource.php
│   │   │   └── TrainingEnrollmentResource.php
│   │   ├── Pages/                      # Custom Pages (Dashboard)
│   │   │   ├── Dashboard.php           # Default dashboard
│   │   │   ├── HrDashboard.php         # Dashboard khusus HR
│   │   │   └── ManagerDashboard.php    # Dashboard khusus Manajer
│   │   └── Widgets/                    # Dashboard Widgets
│   │       ├── PendingApprovalWidget.php
│   │       ├── TodayAttendanceWidget.php
│   │       └── TopPerformerWidget.php
│   └── Auth/                           # Auth Breezy (sudah tersedia)
├── Models/                             # Eloquent Models
│   ├── User.php
│   ├── SuratTugas.php
│   ├── AttendanceLog.php
│   ├── MeritTransaction.php
│   ├── RewardCatalog.php
│   ├── RewardRequest.php
│   ├── Training.php
│   └── TrainingEnrollment.php
├── Http/
│   ├── Controllers/                    # Controllers untuk halaman non-Filament
│   │   └── AttendanceController.php    # Untuk API check-in/out via JS
│   └── Livewire/                       # Livewire Components (opsional)
│       └── CheckInComponent.php
└── Policies/                           # Shield Policies
    ├── SuratTugasPolicy.php
    ├── AttendanceLogPolicy.php
    └── ...
```

### 3.2. Alur Data (High-Level)

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER BROWSER                            │
│              (Desktop / Tablet / Mobile - Responsive)           │
└───────────────────────────┬─────────────────────────────────────┘
                            │ HTTPS
┌───────────────────────────▼─────────────────────────────────────┐
│                    LARAVEL 12 + FILAMENT 5                      │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │               FILAMENT PANEL (Admin)                    │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐  │   │
│  │  │ Resources │ │  Pages   │ │ Widgets  │ │  Shield  │  │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘  │   │
│  └─────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              LIVEWIRE COMPONENTS                        │   │
│  │  (Check-in Form, Real-time Notifications, dll)         │   │
│  └─────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              ELOQUENT ORM + BUSINESS LOGIC              │   │
│  └─────────────────────────────────────────────────────────┘   │
└───────────────────────────┬─────────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────────┐
│              POSTGRESQL / MYSQL DATABASE                        │
│  (Users, SuratTugas, AttendanceLogs, MeritTransactions, dll)   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4. SPESIFIKASI FUNGSIONAL DETAIL (PER MODUL)

> **Catatan untuk Developer:** Semua modul di bawah ini akan diimplementasikan sebagai **Filament Resources** kecuali disebutkan sebagai **Livewire Component** atau **Custom Page**.

---

### 4.1. Modul Manajemen Surat Tugas (Filament Resource)

| **ID** | **Fitur** | **Implementasi di Filament** |
| :--- | :--- | :--- |
| ST-01 | **Buat Surat Tugas** | **Resource:** `SuratTugasResource` dengan Form Schema: <br> - `select('user_id')` → opsi karyawan (filter by role EMPLOYEE) <br> - `date('start_date')` → minimal hari ini <br> - `date('end_date')` → harus >= start_date <br> - `text('location_name')` → nama lokasi tujuan <br> - `text('target_lat')` → input koordinat latitude <br> - `text('target_lng')` → input koordinat longitude <br> - `number('radius_meters')` → default 300 <br> - `file('document_url')` → upload PDF (wajib) <br> **Action:** Simpan dengan status `ACTIVE` |
| ST-02 | **List Surat Tugas** | **Table Schema:** <br> - Kolom: Nama Karyawan, Lokasi, Tanggal Mulai-Selesai, Status <br> - Filter: Status (ACTIVE/EXPIRED/CANCELLED), Tanggal <br> - Sorting: berdasarkan created_at |
| ST-03 | **Edit/Hapus** | Edit dan Delete hanya diizinkan jika status `ACTIVE` dan belum ada AttendanceLog terkait. Gunakan `canUpdate()` / `canDelete()` di Policy. |
| ST-04 | **Validasi Tanggal** | Di Form, tambahkan Rule: `start_date` tidak boleh kurang dari hari ini (kecuali Super Admin). Gunakan `rule()` di Form Schema. |

**Policy Rules:**
```php
// SuratTugasPolicy.php
public function create(User $user) {
    return $user->hasRole(['admin_hr', 'manager']);
}
public function view(User $user, SuratTugas $suratTugas) {
    // Karyawan hanya bisa melihat ST miliknya sendiri
    if ($user->hasRole('employee')) {
        return $user->id === $suratTugas->user_id;
    }
    return true;
}
```

---

### 4.2. Modul Absen Dinas Luar (Livewire + Custom Page)

> **Catatan:** Karena absensi membutuhkan akses GPS dan Kamera, fitur ini TIDAK bisa menggunakan Filament Resource murni. Kita akan buat **Livewire Component** yang di-render di dalam **Custom Page** Filament.

| **ID** | **Fitur** | **Implementasi Teknis** |
| :--- | :--- | :--- |
| AB-01 | **Dashboard "Dinas Aktif"** | **Custom Page:** `app/Filament/Admin/Pages/DashboardKaryawan.php` <br> **Livewire Component:** Tampilkan daftar Surat Tugas dengan status `ACTIVE` dan `start_date <= today <= end_date`. Jika ada, tampilkan tombol "Check-in" / "Check-out". |
| AB-02 | **Proses Check-in** | **Livewire Component Method:** `checkIn($suratTugasId)` <br> 1. Ambil data ST dari database <br> 2. Panggil JavaScript untuk mengakses **Geolocation API** (`navigator.geolocation.getCurrentPosition`) <br> 3. Kirim koordinat ke backend via AJAX (Livewire native) <br> 4. **Validasi Radius:** Hitung jarak dengan Haversine antara GPS aktual vs target ST <br> 5. Buka Kamera via `navigator.mediaDevices.getUserMedia()` → upload foto ke server (store di storage/app/public/attendance) <br> 6. Simpan ke `AttendanceLog` dengan status `PENDING` <br> 7. Tampilkan notifikasi sukses/gagal |
| AB-03 | **Proses Check-out** | Sama seperti Check-in, tetapi memanggil method `checkOut($attendanceLogId)`. Wajib sudah ada check-in di hari yang sama, minimal 7 jam setelah check-in, serta wajib upload foto bukti check-out. |
| AB-04 | **Cegah Duplikasi** | Di method `checkIn()`, cek apakah sudah ada `AttendanceLog` untuk ST dan tanggal hari ini. Jika sudah ada, tolak dengan pesan error. |
| AB-05 | **Riwayat Absensi** | **Resource:** `AttendanceLogResource` (view only untuk karyawan, full untuk HR/Manager). <br> **Table Schema:** Tampilkan Tanggal, Status Approval (Pending/Approved/Rejected), Status Lokasi (`VALID`/`OUT_OF_RANGE`). Status `OUT_OF_RANGE` merepresentasikan kondisi bisnis "Perlu Verifikasi". |

**JavaScript Snippet untuk Check-in (di Blade/Livewire):**
```javascript
// Geolocation
navigator.geolocation.getCurrentPosition(
    (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        // Kirim ke Livewire
        @this.set('checkInLat', lat);
        @this.set('checkInLng', lng);
    },
    (error) => {
        alert('Akses lokasi ditolak. Silakan aktifkan GPS.');
    }
);

// Kamera
navigator.mediaDevices.getUserMedia({ video: true })
    .then((stream) => {
        // Tampilkan preview dan capture foto
    });
```

---

### 4.3. Modul Approval Absensi (Filament Resource + Custom Action)

| **ID** | **Fitur** | **Implementasi di Filament** |
| :--- | :--- | :--- |
| AP-01 | **Dashboard Approval** | **Custom Page:** `ManagerDashboard.php` dengan **Widget** `PendingApprovalWidget` yang menampilkan daftar AttendanceLog bawahan dengan status `PENDING`. |
| AP-02 | **Aksi Approve** | Di `AttendanceLogResource`, tambahkan **Bulk Action** dan **Action** di Table: <br> - Tombol "Setujui" dengan icon hijau <br> - **Action Logic:** <br>   1. Ubah status `approval_status` menjadi `APPROVED` <br>   2. Panggil `MeritService::addPoints($user, 10, 'ATTENDANCE_BONUS', $attendanceLog->id)` <br>   3. Simpan `approved_by` = user_id yang login |
| AP-03 | **Aksi Reject** | Tombol "Tolak" dengan icon merah → muncul **Modal** dengan Textarea "Alasan Penolakan" (wajib diisi). Simpan `rejection_reason`. |
| AP-04 | **Bulk Approve** | Gunakan **Bulk Action** di Table untuk approve beberapa record sekaligus. |

**MeritService (Business Logic):**
```php
// app/Services/MeritService.php
class MeritService {
    public static function addPoints(User $user, int $amount, string $source, ?string $referenceId = null) {
        $transaction = new MeritTransaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->source_type = $source;
        $transaction->reference_id = $referenceId;
        $transaction->expiry_date = now()->addMonths(12);
        $transaction->save();
        
        // Log activity via Filament Logger
        activity()
            ->performedOn($user)
            ->event('merit_added')
            ->log("Added {$amount} points from {$source}");
    }
}
```

---

### 4.4. Modul Sistem Merit & Reward (Filament Resource)

| **ID** | **Fitur** | **Implementasi di Filament** |
| :--- | :--- | :--- |
| MR-01 | **Dashboard Point** | **Custom Page:** Di halaman dashboard karyawan, tampilkan card "Total Point" dan chart breakdown (dari dinas, dari pelatihan, digunakan). Gunakan **Filament Widget** dengan `View` atau `Chart` widget. |
| MR-02 | **Katalog Reward** | **Resource:** `RewardCatalogResource` (CRUD untuk Admin HR). <br> **Fields:** Nama Reward, Deskripsi, Harga Point, Status (Active/Inactive). <br> Seed 5 reward awal: <br> - Voucher Belanja (50pt) <br> - Bonus Tunai (100pt) <br> - Cuti Tambahan 1 Hari (75pt) <br> - Merchandise (25pt) <br> - Training Premium (200pt) |
| MR-03 | **Ajukan Reward** | **Resource:** `RewardRequestResource` (create only untuk karyawan). <br> **Form:** Pilih Reward dari dropdown (hanya yang active) dan isi alasan pengajuan. <br> **Validation:** Cek saldo point cukup (`user->balance >= reward->points_cost`). <br> **Action:** Simpan dengan status `PENDING`. Point **belum** dikurangi. |
| MR-04 | **Approval Reward** | Di `RewardRequestResource`, tambahkan Action "Setujui" dan "Tolak" (sama seperti approval absensi). <br> **Logic Setujui:** <br> 1. Status → `APPROVED` <br> 2. Kurangi point user: `MeritService::deductPoints($user, $reward->points_cost, 'REWARD_REDEMPTION', $request->id)` <br> **Logic Tolak:** Status → `REJECTED`, point tetap utuh. |
| MR-05 | **Expired Point** | **Scheduler:** Buat Command `php artisan merit:expire` yang berjalan setiap bulan. Cari transaksi dengan `expiry_date < now()` dan belum di-expire. Tandai sebagai expired (bisa dengan field `is_expired` atau log khusus). Di MVP, cukup tampilkan peringatan di dashboard jika ada point mendekati kadaluarsa. |

---

### 4.5. Modul Pembinaan Karir (Filament Resource)

| **ID** | **Fitur** | **Implementasi di Filament** |
| :--- | :--- | :--- |
| CA-01 | **Trigger Rekomendasi** | **Scheduler / Event:** Setiap kali point bertambah, jalankan `TrainingRecommendationService::checkEligibility($user)`. Jika total point dalam 3 bulan terakhir >= 100, masukkan user ke daftar eligible. |
| CA-02 | **Tampilan Rekomendasi** | Di dashboard karyawan, tampilkan section "Rekomendasi Pelatihan" dengan daftar training yang eligible. Gunakan **Widget** atau **View** di Page. |
| CA-03 | **Katalog & Daftar** | **Resource:** `TrainingResource` (CRUD untuk Admin HR). <br> **Fields:** Judul, Deskripsi, Minimal Point (untuk eligible), Durasi (jam). <br> **Enrollment:** `TrainingEnrollmentResource` dengan form pilih training. Cek apakah user memenuhi `min_points_required`. |
| CA-04 | **Admin Tandai Selesai** | Di `TrainingEnrollmentResource`, tambahkan Action "Tandai Selesai". <br> **Logic:** <br> 1. Status → `COMPLETED` <br> 2. `completed_at` = now() <br> 3. Panggil `MeritService::addPoints($user, 25, 'TRAINING_COMPLETION', $enrollment->id)` |

---

## 5. DATABASE SCHEMA (ELOQUENT MODELS)

Berikut adalah struktur tabel dan relasi yang wajib dibuat. Semua model menggunakan **UUID** sebagai primary key (FilaStarter sudah mengkonfigurasi UUID secara default).

```php
// 1. Users (extends Laravel default + roles via Shield)
Schema::create('users', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->foreignUuid('manager_id')->nullable()->constrained('users')->nullOnDelete();
    $table->rememberToken();
    $table->timestamps();
});

// 2. Surat Tugas
Schema::create('surat_tugas', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignUuid('created_by')->constrained('users');
    $table->string('location_name');
    $table->decimal('target_lat', 10, 8);
    $table->decimal('target_lng', 11, 8);
    $table->integer('radius_meters')->default(300);
    $table->date('start_date');
    $table->date('end_date');
    $table->string('document_url');
    $table->enum('status', ['ACTIVE', 'EXPIRED', 'CANCELLED'])->default('ACTIVE');
    $table->timestamps();
    
    // Index untuk performa query
    $table->index(['user_id', 'status']);
});

// 3. Attendance Logs
Schema::create('attendance_logs', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('surat_tugas_id')->constrained('surat_tugas')->cascadeOnDelete();
    $table->foreignUuid('user_id')->constrained('users');
    $table->date('attendance_date');
    
    // Check-in
    $table->timestamp('check_in_time')->nullable();
    $table->decimal('check_in_lat', 10, 8)->nullable();
    $table->decimal('check_in_lng', 11, 8)->nullable();
    $table->string('check_in_photo_url')->nullable();
    $table->enum('location_status', ['VALID', 'OUT_OF_RANGE'])->default('VALID');
    
    // Check-out
    $table->timestamp('check_out_time')->nullable();
    $table->decimal('check_out_lat', 10, 8)->nullable();
    $table->decimal('check_out_lng', 11, 8)->nullable();
    $table->string('check_out_photo_url')->nullable();
    
    // Approval
    $table->enum('approval_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
    $table->foreignUuid('approved_by')->nullable()->constrained('users');
    $table->text('rejection_reason')->nullable();
    
    $table->timestamps();
    
    // Unique constraint: 1 hari hanya 1 record per ST
    $table->unique(['surat_tugas_id', 'attendance_date']);
});

// 4. Merit Transactions
Schema::create('merit_transactions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
    $table->integer('amount'); // positif = penambahan, negatif = pengurangan
    $table->enum('source_type', ['ATTENDANCE_BONUS', 'TRAINING_COMPLETION', 'REWARD_REDEMPTION', 'ADJUSTMENT']);
    $table->uuid('reference_id')->nullable(); // Polymorphic reference
    $table->date('expiry_date')->nullable();
    $table->text('description')->nullable();
    $table->boolean('is_expired')->default(false);
    $table->timestamps();
    
    $table->index(['user_id', 'created_at']);
});

// 5. Rewards Catalog
Schema::create('rewards_catalog', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->text('description')->nullable();
    $table->integer('points_cost');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// 6. Reward Requests
Schema::create('reward_requests', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignUuid('reward_id')->constrained('rewards_catalog');
    $table->integer('points_spent');
    $table->text('reason');
    $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
    $table->foreignUuid('approved_by')->nullable()->constrained('users');
    $table->text('rejection_reason')->nullable();
    $table->timestamps();
});

// 7. Trainings
Schema::create('trainings', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('title');
    $table->text('description')->nullable();
    $table->integer('min_points_required')->default(0);
    $table->integer('duration_hours')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// 8. Training Enrollments
Schema::create('training_enrollments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignUuid('training_id')->constrained('trainings');
    $table->enum('status', ['REGISTERED', 'COMPLETED', 'DROPPED'])->default('REGISTERED');
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    
    $table->unique(['user_id', 'training_id']);
});
```

---

## 6. BUSINESS LOGIC ENGINE (WAJIB DIIMPLEMENTASIKAN)

### 6.1. Haversine Formula (Validasi Radius)

```php
// app/Helpers/GeoHelper.php
class GeoHelper {
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // meter
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}

// Di Livewire Component
$distance = GeoHelper::calculateDistance(
    $checkInLat, $checkInLng,
    $suratTugas->target_lat, $suratTugas->target_lng
);
$locationStatus = $distance <= $suratTugas->radius_meters ? 'VALID' : 'OUT_OF_RANGE';
```

### 6.2. Logic Perhitungan Point (Saat Approval)

```php
// app/Services/MeritService.php
class MeritService {
    public static function processAttendanceApproval(AttendanceLog $log, User $approver, string $action, ?string $reason = null) {
        if ($action === 'APPROVED') {
            $log->approval_status = 'APPROVED';
            $log->approved_by = $approver->id;
            $log->save();
            
            // Tambah 10 point
            self::addPoints(
                $log->user, 
                10, 
                'ATTENDANCE_BONUS', 
                $log->id,
                "Bonus absensi dinas {$log->attendance_date} - {$log->suratTugas->location_name}"
            );
        } else {
            $log->approval_status = 'REJECTED';
            $log->rejection_reason = $reason;
            $log->save();
        }
    }
    
    public static function addPoints(User $user, int $amount, string $source, ?string $referenceId = null, ?string $description = null) {
        $transaction = new MeritTransaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->source_type = $source;
        $transaction->reference_id = $referenceId;
        $transaction->expiry_date = now()->addMonths(12);
        $transaction->description = $description;
        $transaction->save();
        
        // Log via Filament Logger
        activity()
            ->performedOn($user)
            ->event('merit_added')
            ->withProperties(['amount' => $amount, 'source' => $source])
            ->log("Added {$amount} points from {$source}");
    }
}
```

### 6.3. Logic Rekomendasi Pelatihan

```php
// app/Services/TrainingRecommendationService.php
class TrainingRecommendationService {
    public static function checkEligibility(User $user) {
        $threeMonthsAgo = now()->subMonths(3);
        $totalPoints = MeritTransaction::where('user_id', $user->id)
            ->where('created_at', '>=', $threeMonthsAgo)
            ->where('amount', '>', 0)
            ->sum('amount');
        
        if ($totalPoints >= 100) {
            // Cari training yang eligible
            $trainings = Training::where('min_points_required', '<=', $totalPoints)
                ->where('is_active', true)
                ->get();
            
            // Simpan ke session atau tampilkan di dashboard
            session()->put('recommended_trainings', $trainings);
        }
    }
}
```

---

## 7. UI/UX SPESIFIKASI (FILAMENT-BASED)

### 7.1. Tema dan Styling

FilaStarter menggunakan **Filament Shadcn Theme** yang memberikan tampilan modern dengan komponen UI yang konsisten. Semua halaman akan mengikuti tema ini secara otomatis.

### 7.2. Halaman Dashboard per Role

| **Role** | **Dashboard Content** |
| :--- | :--- |
| **Karyawan** | - Card "Total Point Saya" <br> - Card "Dinas Aktif Hari Ini" dengan tombol Check-in/out <br> - Widget "Rekomendasi Pelatihan" <br> - Widget "Riwayat Point Terbaru" |
| **Manajer** | - Card "Pending Approval Absensi" (jumlah) <br> - Widget daftar approval absensi bawahan <br> - Widget "Tim Saya" (daftar karyawan dan status dinas hari ini) <br> - Tombol cepat "Buat Surat Tugas" |
| **Admin HR** | - Card "Total Karyawan", "Total ST Aktif", "Total Point Digunakan" <br> - Chart "Tren Point per Bulan" <br> - Widget "Top 5 Karyawan dengan Point Tertinggi" |

### 7.3. Custom Page untuk Check-in/out (Karyawan)

Karena Filament Resource tidak mendukung GPS dan Kamera secara native, kita akan membuat **Custom Page** dengan Livewire:

```php
// app/Filament/Admin/Pages/AttendancePage.php
class AttendancePage extends Page {
    protected static string $view = 'filament.pages.attendance';
    
    // Livewire properties
    public $selectedSuratTugasId;
    public $checkInLat;
    public $checkInLng;
    public $checkInPhoto;
    // ...
}
```

**Blade View (`resources/views/filament/pages/attendance.blade.php`):**
```blade
<x-filament-panels::page>
    @if($activeSuratTugas->isEmpty())
        <x-filament::card>
            <p class="text-center text-gray-500">Tidak ada dinas aktif hari ini.</p>
        </x-filament::card>
    @else
        <x-filament::card>
            <div class="space-y-4">
                <h2 class="text-xl font-bold">Dinas Aktif: {{ $activeSuratTugas->first()->location_name }}</h2>
                
                @if($isCheckedIn)
                    <div class="bg-green-100 p-4 rounded">
                        <p>✅ Anda sudah check-in pada {{ $checkInTime }}</p>
                        <button wire:click="checkOut" class="btn btn-primary">
                            Check-out Sekarang
                        </button>
                    </div>
                @else
                    <button wire:click="startCheckIn" class="btn btn-primary">
                        📍 Check-in Sekarang
                    </button>
                @endif
            </div>
        </x-filament::card>
    @endif
</x-filament-panels::page>
```

### 7.4. Notifikasi Real-time

Gunakan **Filament Notifications** untuk memberi feedback ke user:

```php
// Setelah check-in berhasil
Notification::make()
    ->title('Check-in Berhasil!')
    ->body('Absensi Anda telah tercatat dan menunggu approval Manajer.')
    ->success()
    ->send();

// Setelah approval
Notification::make()
    ->title('Absensi Disetujui!')
    ->body('Anda mendapatkan +10 point untuk dinas tanggal ' . $date)
    ->success()
    ->sendTo($user);
```

---

## 8. API ENDPOINTS (Opsional - untuk Integrasi)

Meskipun aplikasi berbasis web, beberapa endpoint REST API mungkin diperlukan untuk integrasi dengan sistem lain (misal: export data ke sistem penggajian).

| **Method** | **Endpoint** | **Deskripsi** | **Auth** |
| :--- | :--- | :--- | :--- |
| GET | `/api/v1/merit/balance/{user_id}` | Cek saldo point karyawan | API Token |
| GET | `/api/v1/attendance/{user_id}?month=2026-06` | Export rekap absensi bulanan (JSON) | API Token |
| POST | `/api/v1/webhook/attendance-approved` | Webhook untuk sistem eksternal (jika ada) | API Key |

---

## 9. KEBUTUHAN NON-FUNGSIONAL (TEKNIS)

| **Kategori** | **Spesifikasi** |
| :--- | :--- |
| **PHP Version** | PHP 8.2 atau lebih tinggi (Laravel 12 requirement) |
| **Database** | PostgreSQL 15+ atau MySQL 8.0+ |
| **Web Server** | Nginx / Apache dengan mod_rewrite |
| **Storage** | Local storage untuk foto (symlink ke `storage/app/public`). Untuk production, gunakan AWS S3 atau Cloud Storage. |
| **Queue Driver** | Redis / Database (untuk scheduler dan notifikasi) |
| **Cache** | Redis / Memcached (opsional, untuk performa) |
| **GPS Access** | HTTPS wajib (Geolocation API hanya bekerja di HTTPS) |
| **Browser Support** | Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ |
| **Responsive** | Mobile-first, semua halaman Filament sudah responsive secara default |
| **Security** | - CSRF Protection (Laravel default) <br> - XSS Protection (Blade escaping) <br> - SQL Injection Protection (Eloquent) <br> - Filament Shield untuk RBAC |
| **Logging** | Filament Logger otomatis mencatat semua aktivitas CRUD |
| **Performance** | - Gunakan `with()` untuk eager loading relasi di Resources <br> - Pagination di semua Table (default 10-25 per halaman) <br> - Cache query yang sering dipanggil |

---

## 10. INSTALASI & SETUP (BERDASARKAN FILASTARTER)

Berikut adalah langkah-langkah untuk memulai pengembangan:

```bash
# 1. Clone / Create project dari FilaStarter
composer create-project --prefer-dist raugadh/fila-starter nama-project

# 2. Setup environment
cp .env.example .env
# Edit .env: DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Install dependencies
composer install
npm install

# 4. Generate key
php artisan key:generate

# 5. Link storage
php artisan storage:link

# 6. Initialize project (migrate + seed + permissions)
php artisan project:init

# 7. Build assets
npm run build

# 8. Jalankan development server
php artisan serve
```

> **Catatan:** FilaStarter memiliki command `project:init` dan `project:update` yang akan membuat permission dan roles dasar. Gunakan ini untuk inisialisasi awal.

---

## 11. SKENARIO UJI (TEST CASES - WAJIB PASS)

| **ID** | **Skenario** | **Ekspektasi** |
| :--- | :--- | :--- |
| TC-01 | Karyawan login dengan role EMPLOYEE | Redirect ke Dashboard Karyawan (hanya melihat data miliknya sendiri) |
| TC-02 | Karyawan check-in di lokasi tepat (dalam radius) | Status `VALID`, foto terupload, log masuk ke database dengan status `PENDING` |
| TC-03 | Karyawan check-in di lokasi jauh (>300m) | Status lokasi `OUT_OF_RANGE`, warning muncul, approval status tetap `PENDING` untuk diverifikasi Manajer |
| TC-04 | Karyawan mencoba check-in tanpa Surat Tugas aktif | Tombol check-in tidak muncul / muncul pesan "Tidak ada dinas aktif hari ini" |
| TC-05 | Manajer Approve absensi via Resource Action | Status menjadi `APPROVED`, Point +10 langsung bertambah di saldo karyawan |
| TC-06 | Manajer Reject absensi dengan alasan | Status `REJECTED`, alasan tersimpan, point tidak bertambah |
| TC-07 | Karyawan ajukan reward dengan point cukup dan alasan terisi | Request status `PENDING`, alasan tersimpan, point **belum** berkurang |
| TC-08 | Manajer Approve reward request | Status `APPROVED`, point berkurang sesuai harga reward |
| TC-09 | Admin HR tandai pelatihan selesai | Status `COMPLETED`, point +25 otomatis bertambah |
| TC-10 | Admin HR buat Surat Tugas untuk karyawan dengan dokumen PDF | ST tersimpan dengan status `ACTIVE`, dokumen tersimpan, muncul di dashboard karyawan |
| TC-11 | Karyawan check-out kurang dari 7 jam setelah check-in | Check-out ditolak dengan pesan validasi minimal durasi |
| TC-12 | Karyawan check-out setelah minimal 7 jam dengan foto bukti | Check-out berhasil, GPS dan foto check-out tersimpan |

---

## 12. GLOSARIUM TEKNIS

| **Istilah** | **Keterangan** |
| :--- | :--- |
| **Filament** | Admin panel framework untuk Laravel, menyediakan Resources, Forms, Tables, Widgets |
| **Filament Shield** | Package untuk RBAC (Role-Based Access Control) di Filament |
| **Filament Breezy** | Package untuk Authentication (Login, Register, Reset Password, Profile) |
| **Filament Logger** | Package untuk mencatat semua aktivitas CRUD secara otomatis |
| **Livewire** | Full-stack framework untuk Laravel yang memungkinkan UI reaktif tanpa JavaScript |
| **Resource** | Konsep di Filament untuk CRUD operation terhadap satu Model |
| **Widget** | Komponen kecil di Filament untuk ditampilkan di Dashboard |
| **Policy** | Laravel Authorization class untuk mengontrol akses per Model |
| **Haversine** | Rumus matematika untuk menghitung jarak antara dua titik koordinat GPS |
