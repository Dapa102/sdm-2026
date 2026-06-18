# Testing Checklist - MVP Sistem SDM Terintegrasi

**Generated:** 2026-06-18  
**Fase:** 11 - Testing & UAT Preparation  
**Status:** Ready for Testing

---

## Test Environment Setup

- [ ] Database seeded dengan user demo untuk semua role
- [ ] Storage public configured dan accessible
- [ ] Application running di environment HTTPS (untuk GPS/Camera)
- [ ] Browser permissions granted untuk Location dan Camera

---

## 1. Role-Based Access Control

### TC-01: Karyawan Login dan Isolasi Data
**Objective:** Karyawan hanya melihat data sendiri

**Steps:**
1. Login sebagai user dengan role `karyawan`
2. Akses dashboard
3. Cek menu yang tersedia
4. Akses resource Absensi
5. Akses resource Reward Request
6. Akses resource Training Enrollment

**Expected:**
- Dashboard menampilkan widget: Point Balance, Active Assignment, Available Trainings
- Menu: hanya Absen Dinas Luar, Absensi (own), Merit Transactions (own), Reward Request (own), Training Enrollment (own)
- Tidak bisa akses data karyawan lain
- Tidak bisa akses menu admin (Surat Tugas create, User management, dll)

**Verification:**
- [ ] Dashboard widgets sesuai role
- [ ] Menu terbatas sesuai permission
- [ ] Query scope filter by user_id
- [ ] Tidak ada data leak

**Code Reference:**
- `AttendanceLogResource::getEloquentQuery()` line 54
- `RewardRequestResource::getEloquentQuery()` line 53
- `TrainingEnrollmentResource::getEloquentQuery()` line 53

---

### TC-02: Manajer Access Control
**Objective:** Manajer hanya melihat data bawahan

**Steps:**
1. Login sebagai user dengan role `manajer`
2. Akses Absensi resource
3. Akses Reward Request resource
4. Cek filter data

**Expected:**
- Dashboard menampilkan: Pending Attendance, Pending Rewards, Team Members
- Hanya melihat absensi bawahan (manager_id = current_user)
- Hanya melihat reward request bawahan
- Bisa approve/reject absensi dan reward

**Verification:**
- [ ] Query scope filter by manager_id
- [ ] Approval actions visible
- [ ] Bisa create ST untuk bawahan

**Code Reference:**
- `AttendanceLogResource::getEloquentQuery()` line 51
- `RewardRequestResource::getEloquentQuery()` line 50

---

### TC-03: Admin HR Full Access
**Objective:** Admin HR akses semua data

**Steps:**
1. Login sebagai user dengan role `admin_hr`
2. Akses semua resource
3. Cek dashboard widgets

**Expected:**
- Dashboard: HR Overview (total karyawan, ST aktif, points), Top 5 Employees
- Bisa CRUD semua resource
- Bisa approve absensi, reward, mark training complete

**Verification:**
- [ ] Akses tidak terbatas
- [ ] Dashboard widgets HR muncul
- [ ] Semua action tersedia

---

### TC-04: Super Admin Full Access
**Objective:** Super Admin akses penuh

**Steps:**
1. Login sebagai `super_admin`
2. Akses semua resource dan action

**Expected:**
- Akses penuh tanpa batasan
- Bisa bypass semua policy

**Verification:**
- [ ] Full access confirmed

---

## 2. Surat Tugas Module

### TC-05: Admin HR Buat ST dengan PDF Wajib
**Objective:** Dokumen PDF wajib untuk ST

**Steps:**
1. Login sebagai `admin_hr`
2. Buka Surat Tugas → Create
3. Isi form tanpa upload PDF
4. Submit
5. Isi form dengan upload PDF
6. Submit

**Expected:**
- Submit gagal jika PDF tidak diupload
- Validasi file type = PDF only
- Max size 5MB
- Submit berhasil dengan PDF

**Verification:**
- [ ] Required validation bekerja
- [ ] File type validation (PDF only)
- [ ] File size validation (5MB max)
- [ ] File tersimpan di storage/surat-tugas/

**Code Reference:**
- `SuratTugasResource` FileUpload component
- `acceptedFileTypes(['application/pdf'])`
- `maxSize(5120)`

---

### TC-06: ST Aktif Muncul di Dashboard Karyawan
**Objective:** Widget menampilkan ST aktif hari ini

**Steps:**
1. Buat ST untuk karyawan A, periode hari ini
2. Login sebagai karyawan A
3. Cek dashboard

**Expected:**
- Widget "Surat Tugas Aktif" menampilkan count > 0
- Link ke halaman Absen Dinas Luar

**Verification:**
- [ ] ActiveAssignmentWidget muncul
- [ ] Count ST aktif benar
- [ ] Link berfungsi

**Code Reference:**
- `ActiveAssignmentWidget.php`

---

## 3. Attendance Module

### TC-07: Check-In Dalam Radius
**Objective:** Check-in berhasil jika dalam radius

**Steps:**
1. Buat ST dengan target lokasi dan radius 300m
2. Login sebagai karyawan
3. Buka Absen Dinas Luar
4. Simulasi GPS dalam radius
5. Ambil foto
6. Check-in

**Expected:**
- Check-in berhasil
- `location_status` = VALID
- `approval_status` = PENDING
- GPS dan foto tersimpan
- Notification success

**Verification:**
- [ ] AttendanceLog created
- [ ] location_status = VALID
- [ ] Distance calculation correct (Haversine)
- [ ] Photo stored (max 5MB, jpeg/jpg/png/webp)
- [ ] Notification muncul

**Code Reference:**
- `AttendanceService::checkIn()` line 16-52
- `AttendanceService::distanceInMeters()` line 104-117
- `AttendanceService::storePhoto()` line 141-163

---

### TC-08: Check-In Luar Radius
**Objective:** Check-in luar radius tetap tersimpan sebagai OUT_OF_RANGE

**Steps:**
1. Buat ST dengan target lokasi
2. Simulasi GPS di luar radius (> 300m)
3. Check-in

**Expected:**
- Check-in berhasil tersimpan
- `location_status` = OUT_OF_RANGE
- `approval_status` = PENDING
- Menunggu verifikasi Manajer

**Verification:**
- [ ] AttendanceLog created dengan status OUT_OF_RANGE
- [ ] Bisa di-approve/reject oleh Manajer
- [ ] Distance calculation > radius

---

### TC-09: Check-In Tanpa ST Aktif
**Objective:** Check-in gagal jika tidak ada ST aktif

**Steps:**
1. Login karyawan tanpa ST aktif hari ini
2. Buka Absen Dinas Luar
3. Attempt check-in

**Expected:**
- Tidak ada ST yang muncul untuk check-in
- Error message: "Tidak ada surat tugas aktif"

**Verification:**
- [ ] Page menampilkan "Tidak ada ST aktif"
- [ ] Check-in button disabled/tidak muncul

**Code Reference:**
- `AttendancePage::getActiveSuratTugas()` line 41-53

---

### TC-10: Check-In Duplikat
**Objective:** Tidak bisa check-in dua kali pada ST dan tanggal yang sama

**Steps:**
1. Check-in pada ST X hari ini
2. Attempt check-in lagi pada ST X hari yang sama

**Expected:**
- Error: "Check-in untuk surat tugas ini pada tanggal tersebut sudah ada"
- Check-in ditolak

**Verification:**
- [ ] Validation error muncul
- [ ] Duplicate check bekerja

**Code Reference:**
- `AttendanceService::checkIn()` line 28-32

---

### TC-11: Check-Out Sebelum 7 Jam
**Objective:** Check-out gagal jika belum 7 jam

**Steps:**
1. Check-in pada 08:00
2. Attempt check-out pada 14:00 (6 jam)

**Expected:**
- Error: "Check-out hanya bisa dilakukan minimal 7 jam setelah check-in"
- Check-out ditolak

**Verification:**
- [ ] Validation error muncul
- [ ] Time calculation benar

**Code Reference:**
- `AttendanceService::checkOut()` line 88-92

---

### TC-12: Check-Out Setelah 7 Jam
**Objective:** Check-out berhasil setelah 7 jam

**Steps:**
1. Check-in pada 08:00
2. Check-out pada 15:30 (7.5 jam)

**Expected:**
- Check-out berhasil
- GPS check-out dan foto tersimpan
- Notification success

**Verification:**
- [ ] check_out_at, check_out_lat, check_out_lng, check_out_photo_url tersimpan
- [ ] Photo validation (format + size)
- [ ] Notification muncul

**Code Reference:**
- `AttendanceService::checkOut()` line 54-102

---

## 4. Approval & Merit Module

### TC-13: Manajer Approve Absensi → Point +10
**Objective:** Point bertambah setelah approval

**Steps:**
1. Karyawan check-in dan check-out
2. Login sebagai Manajer
3. Buka Absensi resource
4. Approve attendance log
5. Cek saldo point karyawan

**Expected:**
- Status berubah menjadi APPROVED
- Point +10 ditambahkan via MeritTransaction
- Notification: "Point +10 telah ditambahkan"
- Saldo point karyawan bertambah

**Verification:**
- [ ] approval_status = APPROVED
- [ ] MeritTransaction created (type=CREDIT, points=10, source=attendance_approval)
- [ ] No double point (idempotent)
- [ ] Notification muncul

**Code Reference:**
- `AttendanceLogResource` approve action line 223-244
- `MeritService::processAttendanceApproval()` line 73-95

---

### TC-14: Manajer Reject Absensi → Point Tidak Bertambah
**Objective:** Point tidak bertambah jika rejected

**Steps:**
1. Manajer reject attendance log
2. Isi alasan penolakan
3. Submit
4. Cek saldo point karyawan

**Expected:**
- Status = REJECTED
- rejection_reason tersimpan
- Point tidak bertambah
- Notification: "Absensi ditolak"

**Verification:**
- [ ] approval_status = REJECTED
- [ ] rejection_reason filled
- [ ] No MeritTransaction created
- [ ] Point balance unchanged

**Code Reference:**
- `AttendanceLogResource` reject action line 245-268

---

### TC-15: Point Kadaluarsa Tidak Dihitung
**Objective:** Point expired tidak masuk saldo aktif

**Steps:**
1. Buat MeritTransaction dengan expiry_date < now()
2. Set is_expired = true
3. Cek saldo karyawan via getBalance()

**Expected:**
- Point expired tidak dihitung dalam saldo
- Saldo hanya dari point aktif (is_expired=false, expiry_date >= now())

**Verification:**
- [ ] getBalance() filter is_expired=false
- [ ] getBalance() filter expiry_date >= now()
- [ ] Widget menampilkan point akan kadaluarsa (30 hari)

**Code Reference:**
- `MeritService::getBalance()` line 62-71
- `EmployeePointBalanceWidget` line 34-39

---

## 5. Reward Module

### TC-16: Karyawan Ajukan Reward dengan Alasan
**Objective:** Reward request wajib isi alasan

**Steps:**
1. Login karyawan
2. Buka Reward Request → Create
3. Pilih reward
4. Submit tanpa isi alasan
5. Isi alasan dan submit

**Expected:**
- Submit gagal jika alasan kosong
- Validation: required
- Submit berhasil dengan alasan
- Status = PENDING
- Point belum dikurangi

**Verification:**
- [ ] Reason field required
- [ ] RewardRequest created dengan status PENDING
- [ ] Point balance unchanged
- [ ] Saldo cukup divalidasi

**Code Reference:**
- `RewardRequestResource` form line 123-130

---

### TC-17: Manajer Approve Reward → Point Berkurang
**Objective:** Point dikurangi saat approval

**Steps:**
1. Karyawan ajukan reward (50 pt)
2. Saldo karyawan 100 pt
3. Manajer approve
4. Cek saldo karyawan

**Expected:**
- Status = APPROVED
- Point -50 via MeritTransaction
- Saldo menjadi 50 pt
- Notification: "Point 50 telah dikurangi"

**Verification:**
- [ ] status = APPROVED
- [ ] MeritTransaction created (type=DEBIT, points=-50)
- [ ] Balance check saat approval (gagal jika tidak cukup)
- [ ] No double deduction
- [ ] Notification muncul

**Code Reference:**
- `RewardRequestResource` approve action line 238-281
- `MeritService::processRewardApproval()` line 121-143

---

### TC-18: Manajer Reject Reward → Point Tetap
**Objective:** Point tidak berkurang jika rejected

**Steps:**
1. Manajer reject reward request
2. Isi alasan penolakan
3. Cek saldo karyawan

**Expected:**
- Status = REJECTED
- rejection_reason tersimpan
- Point tidak berkurang
- Notification: "Permintaan reward ditolak"

**Verification:**
- [ ] status = REJECTED
- [ ] rejection_reason filled
- [ ] No MeritTransaction created
- [ ] Point balance unchanged

**Code Reference:**
- `RewardRequestResource` reject action line 282-306

---

## 6. Training Module

### TC-19: Admin HR Mark Training Complete → Point +25
**Objective:** Point bertambah setelah training completed

**Steps:**
1. Karyawan enroll training
2. Admin HR mark as completed
3. Cek saldo point karyawan

**Expected:**
- Status = COMPLETED
- completed_at filled
- Point +25 via MeritTransaction
- Notification: "Point +25 telah ditambahkan"

**Verification:**
- [ ] status = COMPLETED
- [ ] completed_at = now()
- [ ] MeritTransaction created (type=CREDIT, points=25, source=training_completion)
- [ ] No double point
- [ ] Notification muncul

**Code Reference:**
- `TrainingEnrollmentResource` complete action line 207-247
- `MeritService::processTrainingCompletion()` line 97-119

---

### TC-20: Rekomendasi Training Muncul Berdasarkan Point
**Objective:** Widget rekomendasi jika point 3 bulan >= 100

**Steps:**
1. Karyawan A punya 50 pt dalam 3 bulan terakhir
2. Check dashboard → tidak ada rekomendasi
3. Tambahkan 60 pt lagi (total 110 pt)
4. Check dashboard

**Expected:**
- Widget header: "Kumpulkan 100 pt..." (jika < 100)
- Widget header: "✨ Rekomendasi Pelatihan..." (jika >= 100)
- Training filtered by minimum_points <= current_balance

**Verification:**
- [ ] TrainingRecommendationService::isEligibleForRecommendations() benar
- [ ] Widget menampilkan training sesuai eligibility
- [ ] Filter by minimum_points

**Code Reference:**
- `AvailableTrainingsWidget` line 19-35
- `TrainingRecommendationService`

---

## 7. General System

### TC-21: Notifications
**Objective:** Semua notification muncul

**Expected:**
- [ ] Check-in success/fail notification
- [ ] Check-out success/fail notification
- [ ] Attendance approved notification
- [ ] Attendance rejected notification
- [ ] Reward approved notification
- [ ] Reward rejected notification
- [ ] Training completed notification

---

### TC-22: Activity Log
**Objective:** CRUD logged untuk audit

**Steps:**
1. Buat/edit/delete ST
2. Check-in/out
3. Approve/reject absensi
4. Approve/reject reward
5. Complete training
6. Check Activity Log resource

**Expected:**
- Semua aktivitas tercatat
- Model: SuratTugas, AttendanceLog, MeritTransaction, RewardRequest, TrainingEnrollment

**Verification:**
- [ ] Activity log resource accessible (Admin panel → Activity)
- [ ] Events logged dengan user, timestamp, changes
- [ ] Config: filament-logger models registered

**Code Reference:**
- `config/filament-logger.php` line 43-45

---

### TC-23: File Upload Validation
**Objective:** Validasi file berfungsi

**Expected:**
- [ ] PDF upload untuk ST (5MB max, PDF only)
- [ ] Photo upload untuk attendance (5MB max, jpeg/jpg/png/webp)
- [ ] Validation error jika ukuran/format salah

**Code Reference:**
- `SuratTugasResource` FileUpload component
- `AttendanceService::storePhoto()` line 141-171

---

### TC-24: Eager Loading & Performance
**Objective:** No N+1 queries

**Steps:**
1. Enable query log
2. Load resource list pages
3. Check query count

**Expected:**
- Resources use eager loading (with())
- Table pagination enabled
- Query count reasonable (< 10 per page load)

**Verification:**
- [ ] AttendanceLogResource: with(['user', 'suratTugas', 'approver'])
- [ ] RewardRequestResource: with(['user', 'rewardCatalog', 'approver'])
- [ ] SuratTugasResource: with(['user', 'creator'])
- [ ] MeritTransactionResource: with(['user', 'reference'])
- [ ] TrainingEnrollmentResource: with(['user', 'training'])

---

## Definition of Done

MVP dianggap **LULUS** jika:

- [ ] Semua 24 test case PASS
- [ ] Tidak ada data leak antar user
- [ ] Semua role berfungsi sesuai permission
- [ ] Point transaction immutable dan akurat
- [ ] Notification berfungsi
- [ ] Activity log tercatat
- [ ] File validation berfungsi
- [ ] Performance acceptable (no N+1)

---

## Notes & Issues

| Test Case | Status | Issue | Resolution |
|-----------|--------|-------|------------|
| TC-XX | FAIL/PASS | Description | Action taken |

---

## UAT Sign-Off

- [ ] Karyawan Representative: ________________ Date: ________
- [ ] Manajer Representative: ________________ Date: ________
- [ ] Admin HR Representative: ________________ Date: ________
- [ ] System Owner: ________________ Date: ________

---

**End of Checklist**
