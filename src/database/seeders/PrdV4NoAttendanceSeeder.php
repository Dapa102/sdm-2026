<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\SuratTugas;
use App\Models\User;
use App\Models\WorkLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PrdV4NoAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $supervisor = $this->supervisor();
        $location = $this->location();
        $employees = $this->employees($supervisor);

        foreach ($employees as $index => $employee) {
            $assignment = $this->assignment($employee, $location, $supervisor, $index);
            $suratTugas = $this->suratTugas($assignment);

            AttendanceLog::query()
                ->where('assignment_id', $assignment->id)
                ->orWhere('surat_tugas_id', $suratTugas->id)
                ->delete();
        }
    }

    private function supervisor(): User
    {
        $supervisor = User::updateOrCreate(
            ['email' => 'supervisor.belum-absen@sdm.test'],
            [
                'name' => 'Supervisor Belum Absen',
                'password' => Hash::make('password'),
            ],
        );

        $supervisor->syncRoles(['supervisor', 'manajer']);

        return $supervisor;
    }

    private function location(): WorkLocation
    {
        return WorkLocation::updateOrCreate(
            ['location_name' => 'Mall Operasional Surabaya'],
            [
                'client_name' => 'PT Retail Surabaya',
                'address' => 'Jl. Pemuda No. 1, Surabaya',
                'latitude' => -7.2574720,
                'longitude' => 112.7520880,
                'radius_tolerance' => 300,
                'status' => 'active',
            ],
        );
    }

    /**
     * @return array<int, Employee>
     */
    private function employees(User $supervisor): array
    {
        return collect([
            ['code' => 'NOABS-001', 'name' => 'Andi Belum Absen', 'email' => 'andi.belum-absen@sdm.test', 'position' => 'Teknisi Lapangan'],
            ['code' => 'NOABS-002', 'name' => 'Maya Belum Absen', 'email' => 'maya.belum-absen@sdm.test', 'position' => 'Cleaning Service'],
            ['code' => 'NOABS-003', 'name' => 'Tono Belum Absen', 'email' => 'tono.belum-absen@sdm.test', 'position' => 'Security'],
        ])->map(function (array $data) use ($supervisor): Employee {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'manager_id' => $supervisor->id,
                ],
            );

            $user->syncRoles(['employee', 'karyawan']);

            return Employee::updateOrCreate(
                ['employee_code' => $data['code']],
                [
                    'user_id' => $user->id,
                    'position' => $data['position'],
                    'status' => 'active',
                    'join_date' => now()->subMonths(2)->toDateString(),
                    'supervisor_user_id' => $supervisor->id,
                ],
            );
        })->all();
    }

    private function assignment(Employee $employee, WorkLocation $location, User $supervisor, int $index): Assignment
    {
        return Assignment::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'assignment_date' => now()->toDateString(),
                'title' => 'Shift Belum Absen ' . ($index + 1),
            ],
            [
                'work_location_id' => $location->id,
                'supervisor_user_id' => $supervisor->id,
                'start_time' => now()->subHour()->format('H:i'),
                'end_time' => now()->addHours(7)->format('H:i'),
                'description' => 'Skenario demo: pegawai sudah punya assignment aktif tetapi belum melakukan check-in.',
                'assignment_status' => 'berjalan',
            ],
        );
    }

    private function suratTugas(Assignment $assignment): SuratTugas
    {
        $location = $assignment->workLocation;
        $documentPath = 'surat-tugas/demo-belum-absen.pdf';

        Storage::disk('public')->put(
            $documentPath,
            "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF",
        );

        return SuratTugas::updateOrCreate(
            [
                'user_id' => $assignment->employee->user_id,
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
                'status' => 'ACTIVE',
            ],
        );
    }
}
