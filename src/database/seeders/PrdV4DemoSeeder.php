<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AttendanceApproval;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\IntegrationLog;
use App\Models\SuratTugas;
use App\Models\User;
use App\Models\WorkLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PrdV4DemoSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->seedUsers();
        $employees = $this->seedEmployees($users);
        $locations = $this->seedWorkLocations();
        $assignments = $this->seedAssignments($employees, $locations, $users['supervisor']);

        $this->seedAttendanceLogs($assignments, $users);
        $this->seedIntegrationLogs();
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $password = Hash::make('password');

        $users = [
            'super_admin' => User::updateOrCreate(
                ['email' => 'admin@admin.com'],
                ['name' => 'Super Admin', 'password' => $password, 'manager_id' => null],
            ),
            'admin_hr' => User::updateOrCreate(
                ['email' => 'admin.hr@sdm.test'],
                ['name' => 'Admin HR', 'password' => $password, 'manager_id' => null],
            ),
            'supervisor' => User::updateOrCreate(
                ['email' => 'supervisor@sdm.test'],
                ['name' => 'Supervisor Lapangan', 'password' => $password, 'manager_id' => null],
            ),
            'management' => User::updateOrCreate(
                ['email' => 'management@sdm.test'],
                ['name' => 'Manajemen', 'password' => $password, 'manager_id' => null],
            ),
        ];

        $users['budi'] = User::updateOrCreate(
            ['email' => 'budi@sdm.test'],
            ['name' => 'Budi Santoso', 'password' => $password, 'manager_id' => $users['supervisor']->id],
        );
        $users['sari'] = User::updateOrCreate(
            ['email' => 'sari@sdm.test'],
            ['name' => 'Sari Wulandari', 'password' => $password, 'manager_id' => $users['supervisor']->id],
        );
        $users['raka'] = User::updateOrCreate(
            ['email' => 'raka@sdm.test'],
            ['name' => 'Raka Pratama', 'password' => $password, 'manager_id' => $users['supervisor']->id],
        );
        $users['dina'] = User::updateOrCreate(
            ['email' => 'dina@sdm.test'],
            ['name' => 'Dina Lestari', 'password' => $password, 'manager_id' => $users['supervisor']->id],
        );

        $users['super_admin']->syncRoles(['super_admin']);
        $users['admin_hr']->syncRoles(['admin_hr']);
        $users['supervisor']->syncRoles(['supervisor', 'manajer']);
        $users['management']->syncRoles(['management']);

        collect(['budi', 'sari', 'raka', 'dina'])
            ->each(fn (string $key) => $users[$key]->syncRoles(['employee', 'karyawan']));

        return $users;
    }

    /**
     * @param  array<string, User>  $users
     * @return array<string, Employee>
     */
    private function seedEmployees(array $users): array
    {
        return [
            'budi' => Employee::updateOrCreate(
                ['employee_code' => 'EMP-001'],
                [
                    'user_id' => $users['budi']->id,
                    'position' => 'Petugas Maintenance',
                    'status' => 'active',
                    'join_date' => now()->subMonths(14)->toDateString(),
                    'supervisor_user_id' => $users['supervisor']->id,
                ],
            ),
            'sari' => Employee::updateOrCreate(
                ['employee_code' => 'EMP-002'],
                [
                    'user_id' => $users['sari']->id,
                    'position' => 'Cleaning Service',
                    'status' => 'active',
                    'join_date' => now()->subMonths(9)->toDateString(),
                    'supervisor_user_id' => $users['supervisor']->id,
                ],
            ),
            'raka' => Employee::updateOrCreate(
                ['employee_code' => 'EMP-003'],
                [
                    'user_id' => $users['raka']->id,
                    'position' => 'Security',
                    'status' => 'active',
                    'join_date' => now()->subMonths(6)->toDateString(),
                    'supervisor_user_id' => $users['supervisor']->id,
                ],
            ),
            'dina' => Employee::updateOrCreate(
                ['employee_code' => 'EMP-004'],
                [
                    'user_id' => $users['dina']->id,
                    'position' => 'Surveyor Lapangan',
                    'status' => 'active',
                    'join_date' => now()->subMonths(4)->toDateString(),
                    'supervisor_user_id' => $users['supervisor']->id,
                ],
            ),
        ];
    }

    /**
     * @return array<string, WorkLocation>
     */
    private function seedWorkLocations(): array
    {
        return [
            'jakarta' => WorkLocation::updateOrCreate(
                ['location_name' => 'Kantor Cabang Jakarta Pusat'],
                [
                    'client_name' => 'PT Nusantara Properti',
                    'address' => 'Jl. MH Thamrin No. 10, Jakarta Pusat',
                    'latitude' => -6.2000000,
                    'longitude' => 106.8166660,
                    'radius_tolerance' => 300,
                    'status' => 'active',
                ],
            ),
            'bekasi' => WorkLocation::updateOrCreate(
                ['location_name' => 'Gudang Bekasi'],
                [
                    'client_name' => 'PT Logistik Timur',
                    'address' => 'Jl. Ahmad Yani, Bekasi',
                    'latitude' => -6.2382700,
                    'longitude' => 106.9755730,
                    'radius_tolerance' => 250,
                    'status' => 'active',
                ],
            ),
            'bandung' => WorkLocation::updateOrCreate(
                ['location_name' => 'Kantor Cabang Bandung'],
                [
                    'client_name' => 'PT Bandung Sejahtera',
                    'address' => 'Jl. Asia Afrika, Bandung',
                    'latitude' => -6.9174640,
                    'longitude' => 107.6191230,
                    'radius_tolerance' => 350,
                    'status' => 'active',
                ],
            ),
        ];
    }

    /**
     * @param  array<string, Employee>  $employees
     * @param  array<string, WorkLocation>  $locations
     * @return array<string, Assignment>
     */
    private function seedAssignments(array $employees, array $locations, User $supervisor): array
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        return [
            'budi_today' => $this->assignment(
                $employees['budi'],
                $locations['jakarta'],
                $supervisor,
                $today,
                '08:00',
                '17:00',
                'Maintenance AC Kantor Jakarta',
                'berjalan',
            ),
            'sari_today' => $this->assignment(
                $employees['sari'],
                $locations['jakarta'],
                $supervisor,
                $today,
                '07:00',
                '15:00',
                'Cleaning Area Lobby',
                'berjalan',
            ),
            'raka_today' => $this->assignment(
                $employees['raka'],
                $locations['bekasi'],
                $supervisor,
                $today,
                '09:00',
                '18:00',
                'Patroli Gudang Bekasi',
                'berjalan',
            ),
            'dina_today' => $this->assignment(
                $employees['dina'],
                $locations['bandung'],
                $supervisor,
                $today,
                '08:30',
                '16:30',
                'Survey Lokasi Bandung',
                'berjalan',
            ),
            'budi_yesterday' => $this->assignment(
                $employees['budi'],
                $locations['bekasi'],
                $supervisor,
                $yesterday,
                '08:00',
                '17:00',
                'Pengecekan Panel Gudang',
                'selesai',
            ),
            'sari_tomorrow' => $this->assignment(
                $employees['sari'],
                $locations['bandung'],
                $supervisor,
                $tomorrow,
                '07:30',
                '15:30',
                'Cleaning Area Meeting Room',
                'terjadwal',
            ),
        ];
    }

    private function assignment(
        Employee $employee,
        WorkLocation $location,
        User $supervisor,
        string $date,
        string $startTime,
        string $endTime,
        string $title,
        string $status,
    ): Assignment {
        return Assignment::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'assignment_date' => $date,
                'title' => $title,
            ],
            [
                'work_location_id' => $location->id,
                'supervisor_user_id' => $supervisor->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'description' => 'Data demo PRD v4 untuk monitoring absensi lapangan.',
                'assignment_status' => $status,
            ],
        );
    }

    /**
     * @param  array<string, Assignment>  $assignments
     * @param  array<string, User>  $users
     */
    private function seedAttendanceLogs(array $assignments, array $users): void
    {
        $photoPath = $this->demoPhotoPath();

        $this->attendance(
            $assignments['budi_today'],
            $users['admin_hr'],
            [
                'check_in_at' => now()->setTime(7, 55),
                'check_out_at' => now()->setTime(17, 5),
                'check_in_lat' => -6.2000000,
                'check_in_lng' => 106.8166660,
                'check_out_lat' => -6.2001200,
                'check_out_lng' => 106.8167000,
                'check_in_distance_meters' => 0,
                'check_out_distance_meters' => 14,
                'location_status' => 'VALID',
                'attendance_status' => 'hadir',
                'verification_status' => 'valid',
                'approval_status' => 'APPROVED',
                'approved_by' => $users['admin_hr']->id,
                'notes' => 'Check-in normal, lokasi sesuai radius.',
                'photo_path' => $photoPath,
            ],
        );

        $this->attendance(
            $assignments['sari_today'],
            $users['supervisor'],
            [
                'check_in_at' => now()->setTime(7, 18),
                'check_out_at' => null,
                'check_in_lat' => -6.2146200,
                'check_in_lng' => 106.8451300,
                'check_out_lat' => null,
                'check_out_lng' => null,
                'check_in_distance_meters' => 3520,
                'check_out_distance_meters' => null,
                'location_status' => 'OUT_OF_RANGE',
                'attendance_status' => 'terlambat',
                'verification_status' => 'di_luar_lokasi',
                'approval_status' => 'PENDING',
                'approved_by' => null,
                'notes' => 'Terjebak macet dan check-in dari area parkir luar.',
                'photo_path' => $photoPath,
            ],
        );

        $this->attendance(
            $assignments['raka_today'],
            null,
            [
                'check_in_at' => now()->setTime(9, 3),
                'check_out_at' => null,
                'check_in_lat' => null,
                'check_in_lng' => null,
                'check_out_lat' => null,
                'check_out_lng' => null,
                'check_in_distance_meters' => null,
                'check_out_distance_meters' => null,
                'location_status' => 'UNKNOWN',
                'attendance_status' => 'terlambat',
                'verification_status' => 'perlu_verifikasi',
                'approval_status' => 'PENDING',
                'approved_by' => null,
                'notes' => 'GPS tidak terbaca di pos security.',
                'photo_path' => $photoPath,
            ],
        );

        $this->attendance(
            $assignments['dina_today'],
            $users['admin_hr'],
            [
                'check_in_at' => null,
                'check_out_at' => null,
                'check_in_lat' => null,
                'check_in_lng' => null,
                'check_out_lat' => null,
                'check_out_lng' => null,
                'check_in_distance_meters' => null,
                'check_out_distance_meters' => null,
                'location_status' => 'UNKNOWN',
                'attendance_status' => 'sakit',
                'verification_status' => 'valid',
                'approval_status' => 'APPROVED',
                'approved_by' => $users['admin_hr']->id,
                'notes' => 'Input sakit oleh admin HR.',
                'photo_path' => null,
            ],
        );

        $correctedLog = $this->attendance(
            $assignments['budi_yesterday'],
            $users['supervisor'],
            [
                'check_in_at' => now()->subDay()->setTime(8, 15),
                'check_out_at' => now()->subDay()->setTime(17, 0),
                'check_in_lat' => -6.2382700,
                'check_in_lng' => 106.9755730,
                'check_out_lat' => -6.2383000,
                'check_out_lng' => 106.9755900,
                'check_in_distance_meters' => 0,
                'check_out_distance_meters' => 4,
                'location_status' => 'VALID',
                'attendance_status' => 'hadir',
                'verification_status' => 'dikoreksi_manual',
                'approval_status' => 'APPROVED',
                'approved_by' => $users['supervisor']->id,
                'notes' => 'Waktu masuk dikoreksi dari 08:15 menjadi hadir karena briefing lapangan.',
                'photo_path' => $photoPath,
            ],
        );

        AttendanceCorrection::firstOrCreate(
            [
                'attendance_log_id' => $correctedLog->id,
                'correction_type' => 'manual',
                'correction_reason' => 'Briefing lapangan dimulai sebelum check-in.',
            ],
            [
                'corrected_by' => $users['supervisor']->id,
                'old_value' => ['attendance_status' => 'terlambat'],
                'new_value' => ['attendance_status' => 'hadir', 'verification_status' => 'dikoreksi_manual'],
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function attendance(Assignment $assignment, ?User $approver, array $data): AttendanceLog
    {
        $suratTugas = $this->suratTugasForAssignment($assignment);
        $employee = $assignment->employee;
        $user = $employee->user;
        $attendanceDate = $assignment->assignment_date->toDateString();

        $log = AttendanceLog::updateOrCreate(
            [
                'surat_tugas_id' => $suratTugas->id,
                'attendance_date' => $attendanceDate,
            ],
            [
                'user_id' => $user?->id,
                'employee_id' => $employee->id,
                'assignment_id' => $assignment->id,
                'check_in_at' => $data['check_in_at'],
                'check_in_lat' => $data['check_in_lat'],
                'check_in_lng' => $data['check_in_lng'],
                'check_in_distance_meters' => $data['check_in_distance_meters'],
                'check_in_photo_url' => $data['photo_path'],
                'check_out_at' => $data['check_out_at'],
                'check_out_lat' => $data['check_out_lat'],
                'check_out_lng' => $data['check_out_lng'],
                'check_out_distance_meters' => $data['check_out_distance_meters'],
                'check_out_photo_url' => $data['check_out_at'] ? $data['photo_path'] : null,
                'location_status' => $data['location_status'],
                'attendance_status' => $data['attendance_status'],
                'verification_status' => $data['verification_status'],
                'approval_status' => $data['approval_status'],
                'approved_by' => $data['approved_by'],
                'notes' => $data['notes'],
            ],
        );

        if ($approver) {
            AttendanceApproval::firstOrCreate(
                [
                    'attendance_log_id' => $log->id,
                    'approval_status' => strtolower((string) $data['approval_status']),
                ],
                [
                    'approved_by' => $approver->id,
                    'approval_note' => $data['approval_status'] === 'APPROVED'
                        ? 'Demo approval absensi.'
                        : 'Demo penolakan absensi.',
                    'approved_at' => Carbon::parse($data['check_in_at'] ?? now())->addHour(),
                ],
            );
        }

        return $log;
    }

    private function suratTugasForAssignment(Assignment $assignment): SuratTugas
    {
        $employee = $assignment->employee;
        $location = $assignment->workLocation;

        $documentPath = 'surat-tugas/demo-prd-v4.pdf';

        Storage::disk('public')->put(
            $documentPath,
            "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF",
        );

        return SuratTugas::updateOrCreate(
            [
                'user_id' => $employee->user_id,
                'start_date' => $assignment->assignment_date->toDateString(),
                'location_name' => $location->location_name,
            ],
            [
                'created_by' => $assignment->supervisor_user_id,
                'end_date' => $assignment->assignment_date->toDateString(),
                'target_lat' => $location->latitude,
                'target_lng' => $location->longitude,
                'radius_meters' => $location->radius_tolerance,
                'document_url' => $documentPath,
                'status' => match ($assignment->assignment_status) {
                    'dibatalkan' => 'CANCELLED',
                    'selesai' => 'COMPLETED',
                    default => 'ACTIVE',
                },
            ],
        );
    }

    private function demoPhotoPath(): string
    {
        $path = 'attendance/demo/prd-v4-demo.jpg';

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, base64_decode(
                '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAH/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oACAEBAAEFAqf/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oACAEDAQE/ASf/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oACAECAQE/ASf/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oACAEBAAY/Al//xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oACAEBAAE/IV//2gAMAwEAAgADAAAAEP/EABQRAQAAAAAAAAAAAAAAAAAAABD/2gAIAQMBAT8QH//EABQRAQAAAAAAAAAAAAAAAAAAABD/2gAIAQIBAT8QH//EABQQAQAAAAAAAAAAAAAAAAAAABD/2gAIAQEAAT8QH//Z',
                true,
            ));
        }

        return $path;
    }

    private function seedIntegrationLogs(): void
    {
        collect([
            [
                'provider_name' => 'local_storage',
                'action' => 'upload_attendance_photo',
                'request_status' => 'success',
                'response_message' => 'Foto demo absensi disimpan ke storage public.',
            ],
            [
                'provider_name' => 'google_maps',
                'action' => 'render_attendance_coordinate',
                'request_status' => 'failed',
                'response_message' => 'API key belum dikonfigurasi, koordinat tetap disimpan.',
            ],
        ])->each(fn (array $log) => IntegrationLog::firstOrCreate($log));
    }
}
